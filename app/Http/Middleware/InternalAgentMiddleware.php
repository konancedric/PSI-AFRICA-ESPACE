<?php

namespace App\Http\Middleware;

class InternalAgentMiddleware
{
    /**
     * Middleware pour s'assurer que seuls les agents internes accèdent aux fonctionnalités internes
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Types d'utilisateurs autorisés (agents internes)
        $internalTypes = ['admin', 'agent_comptoir', 'commercial'];
        
        if (!in_array($user->type_user, $internalTypes)) {
            abort(403, 'Accès refusé : Réservé aux agents internes.');
        }

        // Vérifier que l'utilisateur est actif
        if ($user->etat != 1 || $user->statut_emploi != 'actif') {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Votre compte n\'est pas actif.');
        }

        return $next($request);
    }
}