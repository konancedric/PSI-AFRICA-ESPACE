<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ComptoirAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
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

        // CORRECTION PRINCIPALE : Vérification simplifiée et robuste pour les agents comptoir
        $hasComptoirAccess = false;
        
        try {
            // Méthode 1 : Vérifier par rôle (priorité)
            if ($user->hasRole('Agent Comptoir')) {
                $hasComptoirAccess = true;
                Log::info('Accès comptoir autorisé par rôle Agent Comptoir', [
                    'user_id' => $user->id,
                    'user_name' => $user->name
                ]);
            }
            // Méthode 2 : Vérifier par type_user (fallback)
            elseif ($user->type_user === 'agent_comptoir') {
                $hasComptoirAccess = true;
                Log::info('Accès comptoir autorisé par type_user', [
                    'user_id' => $user->id,
                    'user_name' => $user->name
                ]);
            }
            // Méthode 3 : Accès admin (Super Admin et Admin peuvent tout voir)
            elseif ($user->hasAnyRole(['Super Admin', 'Admin'])) {
                $hasComptoirAccess = true;
                Log::info('Accès comptoir autorisé par privilège admin', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'roles' => $user->getRoleNames()->toArray()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification des rôles comptoir:', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            // En cas d'erreur avec Spatie, fallback sur type_user uniquement
            $hasComptoirAccess = ($user->type_user === 'agent_comptoir');
        }

        // Si l'utilisateur n'a pas accès, le rediriger vers son dashboard approprié
        if (!$hasComptoirAccess) {
            Log::warning('Tentative d\'accès non autorisé au dashboard comptoir', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_type' => $user->type_user,
                'user_roles' => $user->getRoleNames()->toArray(),
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
            if ($user->hasRole('Super Admin') || $user->hasRole('Admin') || $user->type_user === 'admin') {
                return redirect('/admin/dashboard')->with('warning', 'Vous avez été redirigé vers votre dashboard administrateur.');
            } elseif ($user->hasRole('Commercial') || $user->type_user === 'commercial') {
                return redirect('/commercial/dashboard')->with('warning', 'Vous avez été redirigé vers votre dashboard commercial.');
            } else {
                // Utilisateur public ou sans rôle spécifique
                return redirect('/mes-demandes')->with('warning', 'Accès non autorisé à cette section.');
            }
        } catch (\Exception $e) {
            Log::error('Erreur redirection dashboard:', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return redirect('/dashboard')->with('error', 'Erreur lors de la redirection.');
        }
    }
}