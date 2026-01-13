<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class CRMInvoice extends Model
{
    use SoftDeletes;

    protected $table = 'crm_invoices';

    protected $fillable = [
        'number', 'client_id', 'client_name', 'service', 'amount',
        'paid_amount', 'status', 'issue_date', 'due_date', 'agent',
        'notes', 'reminders_count', 'last_reminder_at', 'user_id', 'view_token',
        'client_validated_at', 'client_signature_data',
        'receipt_signed_at', 'receipt_signature_data'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'issue_date' => 'date',
        'due_date' => 'date',
        'last_reminder_at' => 'datetime',
        'client_validated_at' => 'datetime',
        'receipt_signed_at' => 'datetime',
    ];

    protected $appends = ['public_url', 'remaining', 'days_overdue'];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($invoice) {
            if (empty($invoice->number)) {
                $invoice->number = self::generateInvoiceNumber();
            }
            if (empty($invoice->issue_date)) {
                $invoice->issue_date = now();
            }
        });

        static::saving(function ($invoice) {
            $invoice->updateStatus();
        });
    }

    public static function generateInvoiceNumber()
    {
        return \DB::transaction(function () {
            $year = date('Y');

            // Utiliser withTrashed() pour inclure les factures supprimées et lockForUpdate pour éviter les conditions de course
            $lastInvoice = self::withTrashed()
                ->whereYear('created_at', $year)
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

            $number = $lastInvoice ? intval(substr($lastInvoice->number, -4)) + 1 : 1;

            // Vérifier si le numéro existe déjà (incluant les supprimés) et incrémenter si nécessaire
            $invoiceNumber = 'PSI-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);

            while (self::withTrashed()->where('number', $invoiceNumber)->exists()) {
                $number++;
                $invoiceNumber = 'PSI-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
            }

            return $invoiceNumber;
        });
    }

    public function updateStatus()
    {
        if ($this->paid_amount >= $this->amount) {
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partial';
        } elseif (Carbon::parse($this->due_date)->isPast()) {
            $this->status = 'overdue';
        } else {
            $this->status = 'pending';
        }
    }

    public function getRemainingAttribute()
    {
        return $this->amount - $this->paid_amount;
    }

    public function getDaysOverdueAttribute()
    {
        if ($this->status === 'paid') {
            return 0;
        }
        
        $dueDate = Carbon::parse($this->due_date);
        return max(0, now()->diffInDays($dueDate, false) * -1);
    }

    // Relations
    public function client()
    {
        return $this->belongsTo(CRMClient::class, 'client_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(CRMPayment::class, 'invoice_id');
    }

    public function reminderLogs()
    {
        return $this->hasMany(CRMReminderLog::class, 'invoice_id');
    }

    /**
     * Generate a unique view token for the invoice
     */
    public function generateViewToken()
    {
        do {
            $token = \Illuminate\Support\Str::random(64);
        } while (self::where('view_token', $token)->exists());

        $this->view_token = $token;
        $this->save();

        return $token;
    }

    /**
     * Get the public URL for viewing the invoice
     */
    public function getPublicUrlAttribute()
    {
        if (!$this->view_token) {
            $this->generateViewToken();
        }
        return url('/facturation/' . $this->view_token);
    }

    /**
     * Mark the invoice as validated by client
     */
    public function markAsValidatedByClient()
    {
        $this->client_validated_at = now();
        $this->save();
    }

    /**
     * Check if invoice is validated by client
     */
    public function isValidatedByClient()
    {
        return !is_null($this->client_validated_at);
    }

    /**
     * Mark the receipt as signed by client
     */
    public function markReceiptAsSigned($signatureData = null)
    {
        $this->receipt_signed_at = now();
        if ($signatureData) {
            $this->receipt_signature_data = $signatureData;
        }
        $this->save();
    }

    /**
     * Check if receipt is signed by client
     */
    public function isReceiptSigned()
    {
        return !is_null($this->receipt_signed_at);
    }
}