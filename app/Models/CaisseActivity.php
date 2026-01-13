<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaisseActivity extends Model
{
    protected $table = 'caisse_activities';

    protected $fillable = [
        'action',
        'details',
        'user_name',
        'user_id',
        'entity_type',
        'entity_id'
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Enregistrer une nouvelle activité
     */
    public static function log($action, $details, $entityType = null, $entityId = null)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                \Log::warning('Tentative d\'enregistrement d\'activité sans utilisateur authentifié');
                return null;
            }

            return self::create([
                'action' => $action,
                'details' => $details,
                'user_name' => $user->name ?? $user->username ?? 'Utilisateur inconnu',
                'user_id' => $user->id,
                'entity_type' => $entityType,
                'entity_id' => $entityId
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'enregistrement de l\'activité caisse:', [
                'error' => $e->getMessage(),
                'action' => $action
            ]);
            return null;
        }
    }
}
