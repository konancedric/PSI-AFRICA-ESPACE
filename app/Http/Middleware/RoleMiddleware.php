<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $role
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        $user = Auth::user();
        
        // Vérifier si l'utilisateur est actif
        if ($user->etat != 1 || $user->statut_emploi != 'actif') {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Votre compte n\'est pas actif.');
        }

        // Vérifier si l'utilisateur a le rôle requis
        if (!$user->hasRole($role)) {
            // Rediriger selon le rôle de l'utilisateur
            return $this->redirectToUserDashboard($user);
        }

        return $next($request);
    }

    /**
     * Rediriger l'utilisateur vers son tableau de bord approprié
     */
    private function redirectToUserDashboard($user)
    {
        if ($user->hasRole('Admin')) {
            return redirect('/dashboard')->with('warning', 'Accès non autorisé à cette section.');
        } elseif ($user->hasRole('Agent Comptoir')) {
            return redirect('/comptoir/dashboard')->with('warning', 'Accès non autorisé à cette section.');
        } elseif ($user->hasRole('Commercial')) {
            return redirect('/commercial/dashboard')->with('warning', 'Accès non autorisé à cette section.');
        } else {
            return redirect('/')->with('error', 'Accès non autorisé.');
        }
    }
}
