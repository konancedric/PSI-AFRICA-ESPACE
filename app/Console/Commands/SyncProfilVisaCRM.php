<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CRMClient;
use App\Models\InformationsPersonnelles;
use App\Models\ProfilVisa;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SyncProfilVisaCRM extends Command
{
    protected $signature = 'crm:sync-profil-visa';
    protected $description = 'Synchronise les profils visa de février 2026 (etape > 1) vers le CRM';

    public function handle()
    {
        try {
            // Supprimer les clients CRM dont le profil_visa est à etape 1
            $etape1Ids = ProfilVisa::where('etape', 1)->pluck('id')->toArray();
            if (!empty($etape1Ids)) {
                $deleted = DB::table('crm_clients')
                    ->whereIn('profil_visa_id', $etape1Ids)
                    ->delete();
                if ($deleted > 0) {
                    Log::info("CRM Sync: {$deleted} client(s) etape 1 supprimé(s)");
                }
            }

            // Importer les nouveaux profils visa de février 2026 (etape > 1)
            $existingPvIds = CRMClient::whereNotNull('profil_visa_id')
                ->pluck('profil_visa_id')->toArray();

            $newProfils = ProfilVisa::with('user')
                ->where('ent1d', 1)
                ->whereYear('created_at', 2026)
                ->whereMonth('created_at', 2)
                ->whereNotNull('type_profil_visa')
                ->where('type_profil_visa', '!=', '')
                ->where('etape', '>', 1)
                ->whereNotIn('id', $existingPvIds)
                ->orderBy('etape', 'desc')
                ->get();

            $imported = 0;
            foreach ($newProfils as $profil) {
                $profilUser = $profil->user;
                if (!$profilUser) continue;

                // Utiliser InformationsPersonnelles si disponible (données réelles du questionnaire)
                $infos = InformationsPersonnelles::where('id_profil_visa', $profil->id)->first();
                if ($infos && !empty(trim($infos->nom ?? '')) && !empty(trim($infos->prenom ?? ''))) {
                    $nomCrm    = strtoupper(trim($infos->nom));
                    $prenomsCrm = trim($infos->prenom);
                } else {
                    // Fallback sur users.name : format "Prenom NOM" → on inverse pour avoir NOM Prenom
                    $nameParts  = explode(' ', trim($profilUser->name));
                    $nomCrm     = strtoupper(array_pop($nameParts) ?? $profilUser->name);
                    $prenomsCrm = implode(' ', $nameParts);
                }

                // Ignorer les profils clairement de test
                if (stripos($nomCrm, 'test') !== false || stripos($prenomsCrm, 'test') !== false) {
                    Log::info("CRM Sync: profil ignoré (nom test) - profil#{$profil->id}");
                    continue;
                }

                $contact = !empty($profilUser->contact) ? $profilUser->contact : '';
                if (empty($contact) && !empty($profilUser->email) && strpos($profilUser->email, '+') !== false) {
                    $parts = explode('+', $profilUser->email);
                    $phone = end($parts);
                    if (is_numeric(str_replace([' ', '-'], '', $phone))) $contact = '+' . $phone;
                }

                // Chercher doublon par profil_visa_id, email ou contact
                $existing = CRMClient::where('profil_visa_id', $profil->id)->first();
                if (!$existing && $profilUser->email) {
                    $existing = CRMClient::where('email', $profilUser->email)->first();
                }
                if (!$existing && $contact) {
                    $existing = CRMClient::where('contact', $contact)->first();
                }

                if ($existing) {
                    if (empty($existing->profil_visa_id)) {
                        $existing->profil_visa_id = $profil->id;
                        $existing->source = 'Profil Visa';
                        $existing->save();
                        $imported++;
                    }
                    continue;
                }

                CRMClient::create([
                    'nom'            => $nomCrm,
                    'prenoms'        => $prenomsCrm,
                    'email'          => $profilUser->email,
                    'contact'        => $contact,
                    'media'          => 'Profil Visa',
                    'prestation'     => $profil->type_profil_visa ?? 'Visa',
                    'statut'         => 'Profil Visa',
                    'agent'          => 'Auto - Sync Profil Visa',
                    'source'         => 'Profil Visa',
                    'profil_visa_id' => $profil->id,
                    'user_id'        => 1,
                    'commentaire'    => 'Synchronisé depuis Profil Visa N° ' . ($profil->numero_profil_visa ?? $profil->id),
                ]);
                $imported++;
            }

            if ($imported > 0) {
                Log::info("CRM Sync: {$imported} nouveau(x) profil(s) visa importé(s)");
                $this->info("{$imported} profil(s) importé(s).");
            } else {
                $this->info('Aucun nouveau profil à importer.');
            }

        } catch (\Exception $e) {
            Log::error('CRM Sync Profil Visa: ' . $e->getMessage());
            $this->error('Erreur: ' . $e->getMessage());
        }

        return 0;
    }
}
