<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleOrPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roleOrPermission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roleOrPermission = null)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Si aucun rôle/permission spécifié, laisser passer
        if (!$roleOrPermission) {
            return $next($request);
        }
        
        // Séparer les rôles/permissions (format: "Admin|Super Admin|manage_users")
        $rolesAndPermissions = explode('|', $roleOrPermission);
        
        try {
            // Si Spatie est disponible, utiliser la logique normale
            if (method_exists($user, 'hasAnyRole') && method_exists($user, 'can')) {
                // Vérifier les rôles
                if ($user->hasAnyRole($rolesAndPermissions)) {
                    return $next($request);
                }
                
                // Vérifier les permissions
                foreach ($rolesAndPermissions as $permission) {
                    if ($user->can($permission)) {
                        return $next($request);
                    }
                }
            } else {
                // Fallback sur type_user
                $allowedTypes = ['admin', 'agent_comptoir', 'commercial'];
                if (in_array($user->type_user ?? 'public', $allowedTypes)) {
                    return $next($request);
                }
                
                // Vérifier si l'un des rôles correspond au type_user
                $userType = $user->type_user ?? 'public';
                $typeMapping = [
                    'admin' => ['Admin', 'Super Admin'],
                    'agent_comptoir' => ['Agent Comptoir'],
                    'commercial' => ['Commercial']
                ];
                
                foreach ($rolesAndPermissions as $roleOrPerm) {
                    foreach ($typeMapping as $type => $roles) {
                        if ($userType === $type && in_array($roleOrPerm, $roles)) {
                            return $next($request);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // En cas d'erreur, utiliser le fallback type_user
            $allowedTypes = ['admin', 'agent_comptoir', 'commercial'];
            if (in_array($user->type_user ?? 'public', $allowedTypes)) {
                return $next($request);
            }
        }
        
        // Accès refusé
        return redirect()->back()->with('error', 'Accès non autorisé');
    }
}