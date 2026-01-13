<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DossierClient extends Model
{
    use HasFactory;

    protected $table = 'dossiers_clients';

    protected $fillable = [
        'user_id',
        'uploader_id',
        'file_name',
        'file_path',
        'original_name',
        'file_size',
        'file_type',
        'type',
        'status',
        'description'
    ];

    /**
     * Relation avec l'utilisateur (client) qui possède le document
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation avec l'utilisateur qui a uploadé le document
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    /**
     * Vérifier si le document a été envoyé par le client
     */
    public function isFromClient()
    {
        return $this->type === 'client_to_admin';
    }

    /**
     * Vérifier si le document a été envoyé par l'admin
     */
    public function isFromAdmin()
    {
        return $this->type === 'admin_to_client';
    }

    /**
     * Marquer le document comme vu
     */
    public function markAsViewed()
    {
        $this->status = 'viewed';
        $this->save();
    }

    /**
     * Marquer le document comme traité
     */
    public function markAsProcessed()
    {
        $this->status = 'processed';
        $this->save();
    }

    /**
     * Obtenir la taille formatée du fichier
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Obtenir l'icône en fonction du type de fichier
     */
    public function getFileIconAttribute()
    {
        $icons = [
            'pdf' => 'pdf',
            'doc' => 'word',
            'docx' => 'word',
            'jpg' => 'image',
            'jpeg' => 'image',
            'png' => 'image',
            'zip' => 'archive',
            'rar' => 'archive',
        ];
        return $icons[$this->file_type] ?? 'alt';
    }

    /**
     * Obtenir le badge de statut avec la classe CSS appropriée
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge badge-warning"><i class="fas fa-clock"></i> En attente</span>',
            'viewed' => '<span class="badge badge-info"><i class="fas fa-eye"></i> Vu</span>',
            'processed' => '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Traité</span>',
        ];
        return $badges[$this->status] ?? $badges['pending'];
    }

    /**
     * Scope pour les documents envoyés par le client
     */
    public function scopeFromClient($query)
    {
        return $query->where('type', 'client_to_admin');
    }

    /**
     * Scope pour les documents envoyés par l'admin
     */
    public function scopeFromAdmin($query)
    {
        return $query->where('type', 'admin_to_client');
    }

    /**
     * Scope pour les documents en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope pour un client spécifique
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
