<?php
/**
 * @Author: MARS
 * @Date:   2025-06-30 14:00:00
 * @Last Modified by:   MARS
 * @Last Modified time: 2025-06-30 14:00:00
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AddMessageProfilVisa extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'add_message_profil_visa';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message',
        'objet',
        'id_profil_visa',
        'user1d',
        'photo',
        'etat'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'etat' => 'boolean'
    ];

    /**
     * Relation avec le profil visa
     */
    public function profilVisa(): BelongsTo
    {
        return $this->belongsTo(ProfilVisa::class, 'id_profil_visa');
    }

    /**
     * Relation avec l'utilisateur qui a créé le message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1d');
    }

    /**
     * Scope pour les messages actifs
     */
    public function scopeActive($query)
    {
        return $query->where('etat', 1);
    }

    /**
     * Scope pour les messages d'un profil visa
     */
    public function scopeForProfil($query, $profilId)
    {
        return $query->where('id_profil_visa', $profilId);
    }

    /**
     * Vérifier si le message a une pièce jointe
     */
    public function getHasAttachmentAttribute(): bool
    {
        return $this->photo && $this->photo !== 'NO' && $this->photo !== 'NULL';
    }

    /**
     * Obtenir l'URL de la pièce jointe
     */
    public function getAttachmentUrlAttribute(): string|null
    {
        if (!$this->has_attachment) {
            return null;
        }

        return asset('upload/profil-visa/' . $this->id_profil_visa . '/' . $this->photo);
    }

    /**
     * Obtenir l'extension du fichier
     */
    public function getFileExtensionAttribute(): string|null
    {
        if (!$this->has_attachment) {
            return null;
        }

        return pathinfo($this->photo, PATHINFO_EXTENSION);
    }

    /**
     * Vérifier si le fichier est une image
     */
    public function getIsImageAttribute(): bool
    {
        if (!$this->has_attachment) {
            return false;
        }

        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        return in_array(strtolower($this->file_extension), $imageExtensions);
    }

    /**
     * Vérifier si le fichier est un PDF
     */
    public function getIsPdfAttribute(): bool
    {
        if (!$this->has_attachment) {
            return false;
        }

        return strtolower($this->file_extension) === 'pdf';
    }

    /**
     * Obtenir la taille readable du fichier
     */
    public function getFileSizeAttribute(): string|null
    {
        if (!$this->has_attachment) {
            return null;
        }

        $filePath = public_path('upload/profil-visa/' . $this->id_profil_visa . '/' . $this->photo);
        
        if (!file_exists($filePath)) {
            return null;
        }

        $bytes = filesize($filePath);
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Formater la date de création
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('d/m/Y à H:i');
    }

    /**
     * Obtenir un aperçu du message (tronqué)
     */
    public function getPreviewAttribute(): string
    {
        return str_limit($this->message, 100);
    }
}