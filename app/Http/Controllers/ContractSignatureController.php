<?php

namespace App\Http\Controllers;

use App\Models\CRMContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ContractSignatureController extends Controller
{
    /**
     * Afficher la page de signature publique
     * Cette route est accessible sans authentification
     */
    public function showSignaturePage($token)
    {
        try {
            // Rechercher le contrat par son token
            $contract = CRMContract::findByToken($token);

            if (!$contract) {
                Log::warning('Token de signature invalide ou contrat introuvable', [
                    'token' => $token
                ]);

                return view('contracts.signature-error', [
                    'error' => 'Lien de signature invalide',
                    'message' => 'Ce lien de signature n\'existe pas ou a expiré.'
                ]);
            }

            // Vérifier si le token est valide
            if (!$contract->isTokenValid()) {
                $reason = $this->getInvalidTokenReason($contract);

                Log::warning('Token de signature invalide', [
                    'contract_id' => $contract->id,
                    'token' => $token,
                    'reason' => $reason
                ]);

                return view('contracts.signature-error', [
                    'error' => 'Lien de signature invalide',
                    'message' => $reason
                ]);
            }

            // Token valide, afficher la page de signature
            Log::info('Page de signature affichée', [
                'contract_id' => $contract->id,
                'numero_contrat' => $contract->numero_contrat,
                'client' => $contract->nom_complet
            ]);

            return view('contracts.signature-page', [
                'contract' => $contract,
                'token' => $token
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage de la page de signature', [
                'token' => $token,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('contracts.signature-error', [
                'error' => 'Erreur',
                'message' => 'Une erreur est survenue. Veuillez réessayer plus tard.'
            ]);
        }
    }

    /**
     * Traiter la signature du contrat
     */
    public function processSignature(Request $request, $token)
    {
        try {
            // Log des données reçues pour débogage
            Log::info('🔍 Tentative de signature de contrat', [
                'token' => $token,
                'has_signature' => !empty($request->signature),
                'nom_signataire' => $request->nom_signataire,
                'acceptation' => $request->acceptation
            ]);

            // Validation des données
            $validator = Validator::make($request->all(), [
                'signature' => 'required|string',
                'nom_signataire' => 'required|string|max:255',
                'acceptation' => 'required|accepted'
            ], [
                'signature.required' => 'La signature est obligatoire',
                'nom_signataire.required' => 'Votre nom complet est obligatoire',
                'acceptation.accepted' => 'Vous devez accepter les termes du contrat'
            ]);

            if ($validator->fails()) {
                Log::warning('⚠️ Erreur de validation', [
                    'token' => $token,
                    'errors' => $validator->errors()->toArray()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Rechercher le contrat
            $contract = CRMContract::findByToken($token);

            if (!$contract) {
                Log::warning('⚠️ Contrat introuvable', [
                    'token' => $token
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Lien de signature invalide'
                ], 404);
            }

            // Vérifier si le token est toujours valide
            if (!$contract->isTokenValid()) {
                $reason = $this->getInvalidTokenReason($contract);

                Log::warning('⚠️ Token invalide', [
                    'contract_id' => $contract->id,
                    'token' => $token,
                    'reason' => $reason
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $reason
                ], 400);
            }

            // Signer le contrat
            $contract->signer(
                $request->signature,
                $request->nom_signataire
            );

            // Recharger le contrat pour avoir les données à jour
            $contract->refresh();

            Log::info('✅ Contrat signé avec succès', [
                'contract_id' => $contract->id,
                'numero_contrat' => $contract->numero_contrat,
                'signataire' => $request->nom_signataire,
                'date_signature' => $contract->date_signature,
                'token' => $token
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contrat signé avec succès',
                'contract' => [
                    'numero_contrat' => $contract->numero_contrat,
                    'date_signature' => $contract->date_signature->format('d/m/Y H:i')
                ]
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('❌ Erreur de base de données lors de la signature', [
                'token' => $token,
                'error' => $e->getMessage(),
                'sql' => $e->getSql() ?? 'N/A',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur de base de données. Veuillez contacter le support.'
            ], 500);

        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de la signature du contrat', [
                'token' => $token,
                'error_type' => get_class($e),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la signature. Veuillez réessayer ou contacter le support.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Obtenir la raison pour laquelle un token est invalide
     */
    private function getInvalidTokenReason($contract)
    {
        if ($contract->statut === 'Signé') {
            return 'Ce contrat a déjà été signé. Le lien ne peut plus être utilisé.';
        }

        if ($contract->token_used_at) {
            return 'Ce lien de signature a déjà été utilisé et ne peut plus être réutilisé.';
        }

        if ($contract->token_expires_at && $contract->token_expires_at->isPast()) {
            return 'Ce lien de signature a expiré le ' . $contract->token_expires_at->format('d/m/Y à H:i') . '. Veuillez contacter notre service pour obtenir un nouveau lien.';
        }

        return 'Ce lien de signature n\'est plus valide. Veuillez contacter notre service.';
    }

    /**
     * Vérifier le statut d'un token (pour AJAX)
     */
    public function checkTokenStatus($token)
    {
        try {
            $contract = CRMContract::findByToken($token);

            if (!$contract) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Token invalide'
                ]);
            }

            $isValid = $contract->isTokenValid();

            return response()->json([
                'valid' => $isValid,
                'message' => $isValid ? 'Token valide' : $this->getInvalidTokenReason($contract),
                'contract' => $isValid ? [
                    'numero_contrat' => $contract->numero_contrat,
                    'client' => $contract->nom_complet,
                    'expires_at' => $contract->token_expires_at ? $contract->token_expires_at->format('d/m/Y H:i') : null
                ] : null
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification du token', [
                'token' => $token,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'valid' => false,
                'message' => 'Erreur lors de la vérification'
            ], 500);
        }
    }
}
