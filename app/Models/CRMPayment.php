<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CRMPayment extends Model
{
    protected $table = 'crm_payments';
    
    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_date',
        'payment_method',
        'notes',
        'user_id',
        'receipt_signed_at',
        'receipt_signature',
        'client_ip',
        'receipt_number'
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'receipt_signed_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function invoice()
    {
        return $this->belongsTo(CRMInvoice::class, 'invoice_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * Vérifie si le reçu de ce paiement est signé
     */
    public function isReceiptSigned()
    {
        return !is_null($this->receipt_signed_at);
    }

    /**
     * Vérifie si ce paiement peut être signé
     */
    public function canBeSigned()
    {
        // Le paiement peut être signé si la facture est validée et que le reçu n'est pas encore signé
        return $this->invoice &&
               $this->invoice->isValidatedByClient() &&
               !$this->isReceiptSigned() &&
               $this->amount > 0;
    }

    /**
     * Génère le numéro de reçu pour ce paiement
     */
    public function generateReceiptNumber()
    {
        if ($this->receipt_number) {
            return $this->receipt_number;
        }

        // Format: BC-YYYY-XXX-YY
        // BC = Bon de Caisse
        // YYYY = Année
        // XXX = ID de la facture (3 chiffres)
        // YY = Index du paiement (2 chiffres)
        $invoice = $this->invoice;
        if (!$invoice) {
            return null;
        }

        // Trouver l'index de ce paiement parmi tous les paiements de la facture
        $payments = $invoice->payments()->orderBy('id')->get();
        $index = $payments->search(function($payment) {
            return $payment->id === $this->id;
        });

        $year = date('Y');
        $invoiceId = str_pad($invoice->id, 3, '0', STR_PAD_LEFT);
        $paymentIndex = str_pad($index + 1, 2, '0', STR_PAD_LEFT);

        return "BC-{$year}-{$invoiceId}-{$paymentIndex}";
    }
}