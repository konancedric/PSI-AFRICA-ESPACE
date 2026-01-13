<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserTypeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $requiredType
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $requiredType)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        $user = Auth::user();
        
        // Vérifier si l'utilisateur est actif
        if ($user->etat != 1) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Votre compte n\'est pas actif.');
        }

        // CORRECTION PRINCIPALE : Vérifier SOIT le type_user SOIT le rôle correspondant
        $hasAccess = false;
        
        // Mapping des types vers les rôles équivalents
        $typeToRoleMapping = [
            'admin' => ['Admin', 'Super Admin'],
            'agent_comptoir' => ['Agent Comptoir'],
            'commercial' => ['Commercial'],
            'public' => ['Public'] // pour les utilisateurs publics
        ];

        try {
            // Vérifier d'abord par type_user
            if ($user->type_user === $requiredType) {
                $hasAccess = true;
                Log::info("Accès autorisé par type_user", [
                    'user_id' => $user->id,
                    'user_type' => $user->type_user,
                    'required_type' => $requiredType
                ]);
            }
            
            // Si pas d'accès par type_user, vérifier par rôle
            if (!$hasAccess && isset($typeToRoleMapping[$requiredType])) {
                $allowedRoles = $typeToRoleMapping[$requiredType];
                
                foreach ($allowedRoles as $roleName) {
                    if ($user->hasRole($roleName)) {
                        $hasAccess = true;
                        Log::info("Accès autorisé par rôle", [
                            'user_id' => $user->id,
                            'user_type' => $user->type_user,
                            'role_name' => $roleName,
                            'required_type' => $requiredType
                        ]);
                        break;
                    }
                }
            }
            
            // Cas spécial pour les admins : ils peuvent accéder à tout
            if (!$hasAccess && $user->hasAnyRole(['Super Admin', 'Admin'])) {
                $hasAccess = true;
                Log::info("Accès autorisé par privilège admin", [
                    'user_id' => $user->id,
                    'required_type' => $requiredType
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification des rôles:', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'required_type' => $requiredType
            ]);
            
            // En cas d'erreur, fallback sur type_user
            $hasAccess = ($user->type_user === $requiredType);
        }

        // Si l'utilisateur n'a pas accès, le rediriger vers son dashboard approprié
        if (!$hasAccess) {
            Log::warning('Accès refusé', [
                'user_id' => $user->id,
                'user_type' => $user->type_user,
                'user_roles' => $user->getRoleNames()->toArray(),
                'required_type' => $requiredType,
                'request_url' => $request->url()
            ]);
            
            return $this->redirectToUserDashboard($user);
        }

        return $next($request);
    }

    /**
     * Rediriger l'utilisateur vers son tableau de bord approprié
     */
    private function redirectToUserDashboard($user)
    {
        try {
            // Redirection selon les rôles d'abord, puis par type_user
            if ($user->hasRole('Super Admin') || $user->hasRole('Admin')) {
                return redirect('/admin/dashboard')->with('warning', 'Accès non autorisé à cette section.');
            } elseif ($user->hasRole('Agent Comptoir') || $user->type_user === 'agent_comptoir') {
                return redirect('/comptoir/dashboard')->with('warning', 'Accès non autorisé à cette section.');
            } elseif ($user->hasRole('Commercial') || $user->type_user === 'commercial') {
                return redirect('/commercial/dashboard')->with('warning', 'Accès non autorisé à cette section.');
            } else {
                // Utilisateur public ou sans rôle spécifique
                return redirect('/mes-demandes')->with('warning', 'Accès non autorisé à cette section.');
            }
        } catch (\Exception $e) {
            Log::error('Erreur redirection dashboard:', ['error' => $e->getMessage()]);
            return redirect('/dashboard')->with('error', 'Erreur lors de la redirection.');
        }
    }
}