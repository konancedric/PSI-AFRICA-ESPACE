<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckCrmPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = auth()->user();
        
        // Super Admin a tous les accès
        if ($user->hasRole('Super Admin')) {
            return $next($request);
        }
        
        // Récupérer les permissions CRM
        $permissions = $user->crm_permissions;
        
        Log::info('CheckCrmPermission', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'required_permission' => $permission,
            'user_permissions' => $permissions
        ]);
        
        // Si pas de permissions définies
        if (!$permissions || !is_array($permissions)) {
            Log::warning('Aucune permission CRM définie', ['user_id' => $user->id]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'error' => 'Accès refusé'], 403);
            }
            return redirect()->back()->with('error', 'Accès refusé');
        }
        
        // Vérifier si l'utilisateur a la permission
        if (!in_array($permission, $permissions)) {
            Log::warning('Permission CRM refusée', [
                'user_id' => $user->id,
                'required' => $permission,
                'has' => $permissions
            ]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'error' => 'Accès refusé'], 403);
            }
            return redirect()->back()->with('error', 'Accès refusé à ce module');
        }
        
        return $next($request);
    }
}