<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class PermissionMiddleware
{
    /**
     * ✅ MIDDLEWARE PERSONNALISÉ POUR VÉRIFICATION GRANULAIRE DES PERMISSIONS
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $permission
     * @param  string|null  $guard
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $permission = null, $guard = null)
    {
        try {
            $authGuard = Auth::guard($guard);
            
            if (!$authGuard->check()) {
                Log::warning('❌ Tentative d\'accès non authentifié', [
                    'url' => $request->fullUrl(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                
                return $this->handleUnauthorized($request, 'Vous devez être connecté pour accéder à cette ressource.');
            }

            $user = $authGuard->user();
            
            // Si aucune permission spécifique n'est requise, autoriser l'accès
            if (empty($permission)) {
                return $next($request);
            }

            // Vérifier les permissions multiples (séparées par |)
            $permissions = explode('|', $permission);
            
            if ($this->checkPermissions($user, $permissions, $request)) {
                // Log succès d'accès pour audit
                $this->logSuccessfulAccess($user, $request, $permission);
                return $next($request);
            }

            // Accès refusé
            Log::warning('❌ Accès refusé - Permission insuffisante', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_type' => $user->type_user ?? 'unknown',
                'required_permissions' => $permissions,
                'user_permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
                'user_roles' => $user->getRoleNames()->toArray(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'route_name' => $request->route()->getName(),
                'ip' => $request->ip()
            ]);

            return $this->handleForbidden($request, $user, $permissions);

        } catch (\Exception $e) {
            Log::error('❌ Erreur dans PermissionMiddleware', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'url' => $request->fullUrl(),
                'user_id' => Auth::id()
            ]);

            return $this->handleError($request, 'Erreur lors de la vérification des permissions.');
        }
    }

    /**
     * ✅ VÉRIFIER LES PERMISSIONS AVEC LOGIQUE AVANCÉE
     */
    private function checkPermissions($user, array $permissions, Request $request): bool
    {
        try {
            // Super Admin : accès à tout
            if ($user->hasRole('Super Admin')) {
                Log::info('✅ Accès autorisé - Super Admin', [
                    'user_id' => $user->id,
                    'user_name' => $user->name
                ]);
                return true;
            }

            // Vérifier chaque permission (OR logic)
            foreach ($permissions as $permission) {
                $permission = trim($permission);
                
                if (empty($permission)) {
                    continue;
                }

                // Vérification directe de la permission
                if ($user->can($permission)) {
                    Log::info('✅ Accès autorisé - Permission directe', [
                        'user_id' => $user->id,
                        'permission' => $permission
                    ]);
                    return true;
                }

                // Vérifications contextuelles spéciales
                if ($this->checkContextualPermissions($user, $permission, $request)) {
                    return true;
                }
            }

            return false;

        } catch (\Exception $e) {
            Log::error('❌ Erreur vérification permissions', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            return false;
        }
    }

    /**
     * ✅ VÉRIFICATIONS CONTEXTUELLES AVANCÉES
     */
    private function checkContextualPermissions($user, string $permission, Request $request): bool
    {
        try {
            $routeName = $request->route()->getName();
            $routeParameters = $request->route()->parameters();

            // 1. VÉRIFICATIONS POUR PROFILS VISA
            if (strpos($permission, 'profil_visa') !== false) {
                return $this->checkProfilVisaPermissions($user, $permission, $request, $routeParameters);
            }

            // 2. VÉRIFICATIONS POUR CATÉGORIES IMAGES
            if (strpos($permission, 'categories_images') !== false) {
                return $this->checkCategoriesImagesPermissions($user, $permission, $request, $routeParameters);
            }

            // 3. VÉRIFICATIONS POUR GALERIE VIDÉO
            if (strpos($permission, 'galerie_video') !== false) {
                return $this->checkGalerieVideoPermissions($user, $permission, $request, $routeParameters);
            }

            // 4. VÉRIFICATIONS POUR UTILISATEURS
            if (strpos($permission, 'user') !== false || strpos($permission, 'client') !== false) {
                return $this->checkUserPermissions($user, $permission, $request, $routeParameters);
            }

            // 5. PERMISSIONS PROPRIÉTAIRES (own_)
            if (strpos($permission, 'own_') !== false) {
                return $this->checkOwnerPermissions($user, $permission, $request, $routeParameters);
            }

            return false;

        } catch (\Exception $e) {
            Log::error('❌ Erreur vérifications contextuelles', [
                'error' => $e->getMessage(),
                'permission' => $permission,
                'user_id' => $user->id
            ]);
            return false;
        }
    }

    /**
     * ✅ VÉRIFICATIONS SPÉCIFIQUES PROFILS VISA
     */
    private function checkProfilVisaPermissions($user, string $permission, Request $request, array $routeParameters): bool
    {
        try {
            // Si l'utilisateur est agent comptoir, il peut gérer les profils visa
            if ($user->hasRole('Agent Comptoir') && strpos($permission, 'manage_profil_visa') !== false) {
                return true;
            }

            // Vérification d'accès propriétaire pour utilisateurs publics
            if ($permission === 'view_own_profil_visa' || $permission === 'edit_own_profil_visa') {
                if ($user->type_user === 'public' || empty($user->type_user)) {
                    // L'utilisateur peut voir/modifier ses propres profils
                    return true;
                }
            }

            // Si un ID de profil est dans la route, vérifier l'ownership
            if (isset($routeParameters['id']) && is_numeric($routeParameters['id'])) {
                return $this->checkProfilVisaOwnership($user, $routeParameters['id'], $permission);
            }

            return false;

        } catch (\Exception $e) {
            Log::error('❌ Erreur vérification profils visa', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'permission' => $permission
            ]);
            return false;
        }
    }

    /**
     * ✅ VÉRIFIER L'OWNERSHIP D'UN PROFIL VISA
     */
    private function checkProfilVisaOwnership($user, int $profilId, string $permission): bool
    {
        try {
            // Récupérer le profil visa
            $profil = \App\Models\ProfilVisa::find($profilId);
            
            if (!$profil) {
                return false;
            }

            // L'utilisateur est propriétaire
            if ($profil->user1d == $user->id) {
                Log::info('✅ Accès autorisé - Propriétaire du profil visa', [
                    'user_id' => $user->id,
                    'profil_id' => $profilId
                ]);
                return true;
            }

            // L'agent est assigné à ce profil
            if ($profil->assigned_agent_id == $user->id) {
                Log::info('✅ Accès autorisé - Agent assigné au profil visa', [
                    'user_id' => $user->id,
                    'profil_id' => $profilId
                ]);
                return true;
            }

            // Agent avec permissions générales
            if ($user->hasAnyRole(['Admin', 'Agent Comptoir']) && $user->can('manage_profil_visa')) {
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('❌ Erreur vérification ownership profil visa', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'profil_id' => $profilId
            ]);
            return false;
        }
    }

    /**
     * ✅ VÉRIFICATIONS CATÉGORIES IMAGES
     */
    private function checkCategoriesImagesPermissions($user, string $permission, Request $request, array $routeParameters): bool
    {
        try {
            // Commercial peut gérer les catégories images
            if ($user->hasRole('Commercial') && in_array($permission, [
                'view_categories_images', 'create_categories_images', 'edit_categories_images'
            ])) {
                return true;
            }

            // Modérateur peut modérer le contenu
            if ($user->hasRole('Modérateur') && strpos($permission, 'categories_images') !== false) {
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('❌ Erreur vérification catégories images', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            return false;
        }
    }

    /**
     * ✅ VÉRIFICATIONS GALERIE VIDÉO
     */
    private function checkGalerieVideoPermissions($user, string $permission, Request $request, array $routeParameters): bool
    {
        try {
            // Modérateur peut tout gérer dans la galerie vidéo
            if ($user->hasRole('Modérateur')) {
                return true;
            }

            // Commercial peut créer et modifier des vidéos
            if ($user->hasRole('Commercial') && in_array($permission, [
                'view_galerie_video', 'create_galerie_video', 'edit_galerie_video'
            ])) {
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('❌ Erreur vérification galerie vidéo', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            return false;
        }
    }

    /**
     * ✅ VÉRIFICATIONS UTILISATEURS
     */
    private function checkUserPermissions($user, string $permission, Request $request, array $routeParameters): bool
    {
        try {
            // Admin peut gérer tous les utilisateurs
            if ($user->hasAnyRole(['Admin', 'Super Admin'])) {
                return true;
            }

            // Commercial peut voir les clients
            if ($user->hasRole('Commercial') && in_array($permission, [
                'view_clients', 'view_user'
            ])) {
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('❌ Erreur vérification utilisateurs', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            return false;
        }
    }

    /**
     * ✅ VÉRIFICATIONS PERMISSIONS PROPRIÉTAIRES
     */
    private function checkOwnerPermissions($user, string $permission, Request $request, array $routeParameters): bool
    {
        try {
            // Permissions "own_" sont généralement accordées aux propriétaires
            if (strpos($permission, 'own_') === 0) {
                // L'utilisateur peut toujours gérer ses propres ressources
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('❌ Erreur vérification permissions propriétaires', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            return false;
        }
    }

    /**
     * ✅ LOG ACCÈS RÉUSSI POUR AUDIT
     */
    private function logSuccessfulAccess($user, Request $request, string $permission): void
    {
        try {
            Log::info('✅ Accès autorisé', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_type' => $user->type_user ?? 'unknown',
                'permission' => $permission,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'route_name' => $request->route()->getName(),
                'ip' => $request->ip(),
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            // Ne pas faire échouer la requête pour un problème de log
            Log::error('❌ Erreur log accès réussi', ['error' => $e->getMessage()]);
        }
    }

    /**
     * ✅ GÉRER L'ACCÈS NON AUTHENTIFIÉ
     */
    private function handleUnauthorized(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'error_code' => 'UNAUTHORIZED',
                'redirect_url' => route('login')
            ], 401);
        }

        return redirect()->route('login')->with('error', $message);
    }

    /**
     * ✅ GÉRER L'ACCÈS INTERDIT
     */
    private function handleForbidden(Request $request, $user, array $permissions)
    {
        $message = $this->getForbiddenMessage($user, $permissions);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'error_code' => 'FORBIDDEN',
                'required_permissions' => $permissions,
                'user_permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
                'suggestions' => $this->getAccessSuggestions($user, $permissions)
            ], 403);
        }

        // Redirection intelligente selon le type d'utilisateur
        $redirectUrl = $this->getRedirectUrl($user);
        
        return redirect($redirectUrl)->with('error', $message);
    }

    /**
     * ✅ GÉRER LES ERREURS
     */
    private function handleError(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'error_code' => 'PERMISSION_CHECK_ERROR'
            ], 500);
        }

        return redirect('/dashboard')->with('error', $message);
    }

    /**
     * ✅ GÉNÉRER MESSAGE D'ERREUR PERSONNALISÉ
     */
    private function getForbiddenMessage($user, array $permissions): string
    {
        $permissionNames = implode(', ', $permissions);
        
        return "Accès refusé. Vous n'avez pas les permissions requises ({$permissionNames}) pour accéder à cette ressource.";
    }

    /**
     * ✅ OBTENIR L'URL DE REDIRECTION SELON LE TYPE D'UTILISATEUR
     */
    private function getRedirectUrl($user): string
    {
        try {
            if ($user->hasRole('Super Admin') || $user->hasRole('Admin')) {
                return '/admin/dashboard';
            } elseif ($user->hasRole('Commercial')) {
                return '/commercial/dashboard';
            } elseif ($user->hasRole('Agent Comptoir')) {
                return '/comptoir/dashboard';
            } elseif ($user->type_user === 'public' || empty($user->type_user)) {
                return '/profil-visa';
            }

            return '/dashboard';

        } catch (\Exception $e) {
            Log::error('❌ Erreur détermination URL redirection', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            return '/dashboard';
        }
    }

    /**
     * ✅ OBTENIR DES SUGGESTIONS D'ACCÈS
     */
    private function getAccessSuggestions($user, array $permissions): array
    {
        $suggestions = [];

        try {
            foreach ($permissions as $permission) {
                if (strpos($permission, 'view_') === 0) {
                    $suggestions[] = 'Demandez l\'autorisation de consultation à votre administrateur';
                } elseif (strpos($permission, 'manage_') === 0) {
                    $suggestions[] = 'Seuls les administrateurs peuvent gérer cette ressource';
                } elseif (strpos($permission, 'create_') === 0) {
                    $suggestions[] = 'Permission de création requise';
                } elseif (strpos($permission, 'edit_') === 0) {
                    $suggestions[] = 'Permission de modification requise';
                } elseif (strpos($permission, 'delete_') === 0) {
                    $suggestions[] = 'Permission de suppression requise - accès restreint';
                }
            }

            // Suggestions générales
            if (empty($suggestions)) {
                $suggestions[] = 'Contactez votre administrateur pour obtenir les permissions nécessaires';
            }

            return array_unique($suggestions);

        } catch (\Exception $e) {
            Log::error('❌ Erreur génération suggestions', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            return ['Contactez votre administrateur'];
        }
    }

    /**
     * ✅ MÉTHODE STATIQUE POUR VÉRIFICATION RAPIDE
     */
    public static function checkQuick($permission, $user = null): bool
    {
        try {
            $user = $user ?? Auth::user();
            
            if (!$user) {
                return false;
            }

            if ($user->hasRole('Super Admin')) {
                return true;
            }

            return $user->can($permission);

        } catch (\Exception $e) {
            Log::error('❌ Erreur vérification rapide', [
                'error' => $e->getMessage(),
                'permission' => $permission
            ]);
            return false;
        }
    }
}