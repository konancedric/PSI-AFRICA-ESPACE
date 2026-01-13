<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicUserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user || $user->type_user !== 'public') {
            return redirect('/dashboard')->with('error', 'Cette section est réservée aux clients.');
        }

        return $next($request);
    }
}