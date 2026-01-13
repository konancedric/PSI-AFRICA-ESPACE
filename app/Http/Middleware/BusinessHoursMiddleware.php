<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BusinessHoursMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $now = Carbon::now();
        $currentHour = $now->hour;
        $currentDay = $now->dayOfWeek; // 0 = Dimanche, 6 = Samedi
        
        // Heures d'ouverture : 8h à 18h, du lundi au vendredi
        $isBusinessHour = ($currentDay >= 1 && $currentDay <= 5) && 
                         ($currentHour >= 8 && $currentHour < 18);
        
        // Les admins peuvent accéder 24/7
        $user = auth()->user();
        if ($user && $user->hasAnyRole(['Super Admin', 'Admin'])) {
            return $next($request);
        }
        
        if (!$isBusinessHour) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Service disponible uniquement pendant les heures d\'ouverture (8h-18h, Lundi-Vendredi)'
                ], 423);
            }
            
            return redirect('/dashboard')->with('warning', 'Cette fonctionnalité n\'est disponible que pendant les heures d\'ouverture (8h-18h, Lundi-Vendredi).');
        }

        return $next($request);
    }
}