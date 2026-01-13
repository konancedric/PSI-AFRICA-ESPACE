<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return response()->json([
                'error' => 'Non authentifié',
                'message' => 'Vous devez être connecté pour accéder à cette API'
            ], 401);
        }

        $user = Auth::user();
        
        if ($user->etat != 1) {
            return response()->json([
                'error' => 'Compte inactif',
                'message' => 'Votre compte est désactivé'
            ], 403);
        }

        return $next($request);
    }
}
