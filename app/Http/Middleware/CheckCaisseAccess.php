<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckCaisseAccess
{
    /**
     * Handle an incoming request.
     * Vérifie si l'utilisateur connecté a accès à la caisse
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non authentifié'
                ], 401);
            }
            return redirect('/login')->with('error', 'Vous devez être connecté pour accéder à la caisse');
        }

        // Vérifier si l'utilisateur est bloqué
        if ($user->caisse_blocked) {
            Log::warning('Tentative d\'accès à la caisse par un utilisateur bloqué', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'ip' => $request->ip(),
                'url' => $request->fullUrl()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès à la caisse bloqué. Contactez l\'administrateur.'
                ], 403);
            }

            return redirect('/home')->with('error', 'Votre accès à la caisse a été bloqué. Contactez l\'administrateur.');
        }

        // Vérifier si l'utilisateur a le droit d'accéder à la caisse (agents internes)
        if (!in_array($user->type_user, ['admin', 'commercial', 'agent_comptoir'])) {
            Log::warning('Tentative d\'accès à la caisse par un utilisateur non autorisé', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'type_user' => $user->type_user,
                'url' => $request->fullUrl()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'avez pas accès à la caisse.'
                ], 403);
            }

            return redirect('/home')->with('error', 'Vous n\'avez pas accès à la caisse.');
        }

        return $next($request);
    }
}
