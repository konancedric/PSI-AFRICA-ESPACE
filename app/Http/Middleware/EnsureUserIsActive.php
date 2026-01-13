<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if ($user && $user->etat != 1) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Votre compte a été désactivé. Contactez l\'administrateur.');
        }

        return $next($request);
    }
}
