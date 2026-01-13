<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user || !$user->hasRole('Super Admin')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Accès refusé',
                    'message' => 'Seuls les Super Admins peuvent accéder à cette ressource'
                ], 403);
            }
            
            abort(403, 'Accès refusé - Réservé aux Super Admins');
        }

        return $next($request);
    }
}
