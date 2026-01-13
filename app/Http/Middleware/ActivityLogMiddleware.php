<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityLogMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if ($user) {
            Log::info('Activité utilisateur', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_type' => $user->type_user,
                'action' => $request->method(),
                'url' => $request->url(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()
            ]);
        }

        return $next($request);
    }
}
