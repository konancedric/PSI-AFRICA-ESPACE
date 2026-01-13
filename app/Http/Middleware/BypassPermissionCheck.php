<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BypassPermissionCheck
{
    /**
     * Handle an incoming request - Bypass pour certains cas spéciaux
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Ce middleware peut être utilisé pour contourner certaines vérifications
        // dans des cas spéciaux ou pour des utilisateurs privilégiés
        
        if (Auth::check()) {
            $user = Auth::user();
            
            // Super administrateurs ont accès à tout
            if ($user->hasRole('Super Admin') || $user->email === 'admin@psiafrica.ci') {
                Log::info('Bypass permission check pour super admin', [
                    'user_id' => $user->id,
                    'url' => $request->url()
                ]);
                return $next($request);
            }
        }

        return $next($request);
    }
}