<?php

namespace App\Http\Middleware;

class AutoRedirectMiddleware
{
    /**
     * Redirection automatique selon le type d'utilisateur après connexion
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Redirection automatique selon le rôle après connexion
            if ($request->is('login') || $request->is('/')) {
                switch ($user->type_user) {
                    case 'admin':
                        return redirect('/dashboard');
                    case 'agent_comptoir':
                        return redirect('/comptoir/dashboard');
                    case 'commercial':
                        return redirect('/commercial/dashboard');
                    case 'public':
                        return redirect('/mes-demandes');
                }
            }
        }

        return $next($request);
    }
}