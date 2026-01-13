<div class="app-sidebar colored">
    <div class="sidebar-header">
        <a class="header-brand" href="{{url('dashboard')}}">
            <div class="logo-img">
               <img height="30" src="{{ asset('img/logo.png')}}" class="header-brand-img" title="{{ config('app.name') }}"> 
            </div>
        </a>
        <div class="sidebar-action"><i class="ik ik-arrow-left-circle"></i></div>
        <button id="sidebarClose" class="nav-close"><i class="ik ik-x"></i></button>
    </div>

    @php
        $segment1 = request()->segment(1);
        $segment2 = request()->segment(2);
        $user = Auth::user();
        
        // ✅ FONCTION PURE : Vérifier SEULEMENT les permissions assignées par l'admin
        function hasAssignedPermission($user, $permission) {
            try {
                // Recharger les permissions en temps réel
                $user->load(['roles.permissions', 'permissions']);
                
                // Méthode 1 : Spatie Permissions (méthode principale)
                if (method_exists($user, 'can') && $user->can($permission)) {
                    return true;
                }
                
                // Méthode 2 : Vérification directe permissions assignées à l'utilisateur
                if (Schema::hasTable('model_has_permissions') && Schema::hasTable('permissions')) {
                    $hasDirectPermission = DB::table('model_has_permissions')
                        ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
                        ->where('model_has_permissions.model_id', $user->id)
                        ->where('model_has_permissions.model_type', 'App\\Models\\User')
                        ->where('permissions.name', $permission)
                        ->exists();
                    
                    if ($hasDirectPermission) {
                        return true;
                    }
                }
                
                // Méthode 3 : Vérification permissions via rôles assignés
                if (Schema::hasTable('role_has_permissions') && Schema::hasTable('model_has_roles')) {
                    $hasRolePermission = DB::table('model_has_roles')
                        ->join('role_has_permissions', 'model_has_roles.role_id', '=', 'role_has_permissions.role_id')
                        ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                        ->where('model_has_roles.model_id', $user->id)
                        ->where('model_has_roles.model_type', 'App\\Models\\User')
                        ->where('permissions.name', $permission)
                        ->exists();
                    
                    if ($hasRolePermission) {
                        return true;
                    }
                }
                
                // AUCUN FALLBACK - Si pas assigné explicitement, retourner false
                return false;
                
            } catch (\Exception $e) {
                \Log::warning("Erreur vérification permission assignée {$permission}: " . $e->getMessage());
                return false;
            }
        }
        
        // ✅ NOUVELLE FONCTION : Vérifier les permissions CRM
        function hasCrmPermission($user, $permission) {
            try {
                // Super Admin a toutes les permissions CRM
                if ($user->hasRole('Super Admin')) {
                    return true;
                }
                
                // Récupérer les permissions CRM de l'utilisateur
                $crmPermissions = $user->getCrmPermissions();
                
                // Vérifier si la permission est dans le tableau
                return in_array($permission, $crmPermissions);
                
            } catch (\Exception $e) {
                \Log::warning("Erreur vérification permission CRM {$permission}: " . $e->getMessage());
                return false;
            }
        }
        
        // ✅ NOUVELLE FONCTION : Vérifier si l'utilisateur a AU MOINS UNE permission CRM
        function hasAnyCrmPermission($user) {
            try {
                // Super Admin a toutes les permissions
                if ($user->hasRole('Super Admin')) {
                    return true;
                }
                
                $crmPermissions = $user->getCrmPermissions();
                
                // Permissions CRM valides
                $validCrmPermissions = ['dashboard', 'clients', 'invoicing', 'recovery', 'performance', 'analytics', 'admin'];
                
                // Vérifier s'il y a une intersection
                return count(array_intersect($crmPermissions, $validCrmPermissions)) > 0;
                
            } catch (\Exception $e) {
                \Log::error("Erreur vérification permissions CRM: " . $e->getMessage());
                return false;
            }
        }
        
        // ✅ RÉCUPÉRER TOUTES LES PERMISSIONS ASSIGNÉES À L'UTILISATEUR
        function getUserAssignedPermissions($user) {
            try {
                $permissions = [];
                
                // Permissions directes
                if (Schema::hasTable('model_has_permissions')) {
                    $directPermissions = DB::table('model_has_permissions')
                        ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
                        ->where('model_has_permissions.model_id', $user->id)
                        ->where('model_has_permissions.model_type', 'App\\Models\\User')
                        ->pluck('permissions.name')
                        ->toArray();
                    
                    $permissions = array_merge($permissions, $directPermissions);
                }
                
                // Permissions via rôles
                if (Schema::hasTable('role_has_permissions') && Schema::hasTable('model_has_roles')) {
                    $rolePermissions = DB::table('model_has_roles')
                        ->join('role_has_permissions', 'model_has_roles.role_id', '=', 'role_has_permissions.role_id')
                        ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                        ->where('model_has_roles.model_id', $user->id)
                        ->where('model_has_roles.model_type', 'App\\Models\\User')
                        ->pluck('permissions.name')
                        ->toArray();
                    
                    $permissions = array_merge($permissions, $rolePermissions);
                }
                
                return array_unique($permissions);
                
            } catch (\Exception $e) {
                \Log::error("Erreur récupération permissions assignées: " . $e->getMessage());
                return [];
            }
        }
        
        // ✅ RÉCUPÉRER LES RÔLES ASSIGNÉS PAR L'ADMIN
        function getUserAssignedRoles($user) {
            try {
                if (Schema::hasTable('model_has_roles') && Schema::hasTable('roles')) {
                    return DB::table('model_has_roles')
                        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                        ->where('model_has_roles.model_id', $user->id)
                        ->where('model_has_roles.model_type', 'App\\Models\\User')
                        ->pluck('roles.name')
                        ->toArray();
                }
                return [];
            } catch (\Exception $e) {
                return [];
            }
        }
        
        // Récupérer les permissions et rôles assignés
        $assignedPermissions = getUserAssignedPermissions($user);
        $assignedRoles = getUserAssignedRoles($user);
        
        // Détermination du type d'utilisateur BASÉE SUR LES RÔLES ASSIGNÉS
        $isSuperAdmin = in_array('Super Admin', $assignedRoles) || ($user->email === 'admin@psiafrica.ci');
        $isAdmin = in_array('Admin', $assignedRoles) && !$isSuperAdmin;
        $isCommercial = in_array('Commercial', $assignedRoles);
        $isAgentComptoir = in_array('Agent Comptoir', $assignedRoles);
        $isPublic = empty($assignedRoles) && !$isSuperAdmin;
        
        // ✅ Vérifier l'accès au CRM
        $hasAnyCrmAccess = hasAnyCrmPermission($user);
        
        \Log::info("SIDEBAR - Permissions assignées pour {$user->name}:", [
            'assignedRoles' => $assignedRoles,
            'assignedPermissions' => $assignedPermissions,
            'permissionsCount' => count($assignedPermissions),
            'isSuperAdmin' => $isSuperAdmin,
            'isAdmin' => $isAdmin,
            'isCommercial' => $isCommercial,
            'isAgentComptoir' => $isAgentComptoir,
            'isPublic' => $isPublic,
            'hasAnyCrmAccess' => $hasAnyCrmAccess
        ]);
    @endphp
    
    <div class="sidebar-content">
        <div class="nav-container">
            <nav id="main-menu-navigation" class="navigation-main">
                
                {{-- DASHBOARD PRINCIPAL - Pour tous les utilisateurs connectés --}}
                <div class="nav-item {{ ($segment1 == 'dashboard') ? 'active' : '' }}">
                    <a href="{{url('dashboard')}}">
                        <i class="ik ik-bar-chart-2"></i>
                        <span>{{ __('Tableau de Bord')}}</span>
                    </a>
                </div>

                {{-- ==================== SIDEBAR SUPER ADMIN ==================== --}}
                @if($isSuperAdmin)
                    
                    {{-- Dashboard Admin --}}
                    <div class="nav-item {{ ($segment1 == 'admin') ? 'active' : '' }}">
                        <a href="{{url('admin/dashboard')}}">
                            <i class="fas fa-crown"></i>
                            <span>{{ __('Dashboard Super Admin')}}</span>
                        </a>
                    </div>
                    
                    {{-- ✅ MODULE CRM - Pour Super Admin --}}
                    <div class="nav-item {{ ($segment1 == 'crm') ? 'active' : '' }}">
                        <a href="{{url('crm')}}">
                            <i class="fas fa-chart-line"></i>
                            <span>{{ __('CRM Business')}}</span>
                            <span class="badge bg-success ms-auto">Pro</span>
                        </a>
                    </div>

                    {{-- MODULE CAISSE - Pour Super Admin --}}
                    <div class="nav-item {{ ($segment1 == 'caisse') ? 'active' : '' }}">
                        <a href="{{url('caisse')}}">
                            <i class="fas fa-cash-register"></i>
                            <span>{{ __('Gestion de Caisse')}}</span>
                            <span class="badge bg-warning ms-auto">Finance</span>
                        </a>
                    </div>

                    {{-- MODULE CALENDRIER - Pour Super Admin --}}
                    <div class="nav-item {{ ($segment1 == 'crm' && $segment2 == 'calendrier') ? 'active' : '' }}">
                        <a href="{{url('crm/calendrier')}}">
                            <i class="fas fa-calendar-alt"></i>
                            <span>{{ __('Calendrier')}}</span>
                        </a>
                    </div>

                    {{-- MODULE MESSAGERIE - Pour Super Admin --}}
                    <div class="nav-item {{ ($segment1 == 'messagerie') ? 'active' : '' }}">
                        <a href="{{url('messagerie')}}">
                            <i class="fas fa-comments"></i>
                            <span>{{ __('Messagerie Interne')}}</span>
                        </a>
                    </div>

                    {{-- GESTION DES DOSSIERS CLIENTS - Pour Super Admin --}}
                    <div class="nav-item {{ ($segment1 == 'admin' && $segment2 == 'dossiers-clients') ? 'active' : '' }}">
                        <a href="{{url('admin/dossiers-clients')}}">
                            <i class="fas fa-folder-open"></i>
                            <span>{{ __('Dossiers Clients')}}</span>
                        </a>
                    </div>

                    {{-- Administration Système --}}
                    <div class="nav-item {{ (in_array($segment1, ['roles', 'permission', 'permissions', 'users', 'user', 'configuration', 'agents'])) ? 'active open' : '' }} has-sub">
                        <a href="#"><i class="ik ik-settings"></i><span>{{ __('Administration Système')}}</span></a>
                        <div class="submenu-content">
                            <a href="{{url('roles')}}" class="menu-item {{ ($segment1 == 'roles') ? 'active' : '' }}">
                                <i class="fas fa-user-shield"></i> {{ __('Gestion des Rôles')}}
                            </a>
                            <a href="{{url('permissions')}}" class="menu-item {{ (in_array($segment1, ['permission', 'permissions'])) ? 'active' : '' }}">
                                <i class="fas fa-key"></i> {{ __('Gestion des Permissions')}}
                            </a>
                            <a href="{{url('users')}}" class="menu-item {{ ($segment1 == 'users') ? 'active' : '' }}">
                                <i class="fas fa-users-cog"></i> {{ __('Gestion des Agents')}}
                            </a>
                            <a href="{{url('users/create')}}" class="menu-item {{ ($segment1 == 'user' && $segment2 == 'create') ? 'active' : '' }}">
                                <i class="fas fa-user-plus"></i> {{ __('Ajouter un Agent')}}
                            </a>
                            <a href="{{url('configuration')}}" class="menu-item {{ ($segment1 == 'configuration') ? 'active' : '' }}">
                                <i class="fas fa-cogs"></i> {{ __('Configuration Système')}}
                            </a>
                        </div>
                    </div>

                    {{-- Modules Métier Super Admin --}}
                    <div class="nav-item {{ (in_array($segment1, ['services', 'forfaits', 'partenaires', 'temoignages', 'profil-visa', 'rendez-vous', 'statuts-etat', 'villes', 'souscrire-forfaits', 'documents-voyage', 'reservation-achat', 'parrainages'])) ? 'active open' : '' }} has-sub">
                        <a href="#"><i class="fas fa-briefcase"></i><span>{{ __('Modules Métier')}}</span></a>
                        <div class="submenu-content">
                            <a href="{{url('profil-visa')}}" class="menu-item {{ ($segment1 == 'profil-visa') ? 'active' : '' }}">
                                <i class="fas fa-passport"></i> {{ __('Profils Visa')}}
                            </a>
                            <a href="{{url('documents-voyage')}}" class="menu-item {{ ($segment1 == 'documents-voyage') ? 'active' : '' }}">
                                <i class="fas fa-file-alt"></i> {{ __('Documents Voyage')}}
                            </a>
                            <a href="{{url('services')}}" class="menu-item {{ ($segment1 == 'services') ? 'active' : '' }}">
                                <i class="fas fa-cogs"></i> {{ __('Services')}}
                            </a>
                            <a href="{{url('forfaits')}}" class="menu-item {{ ($segment1 == 'forfaits') ? 'active' : '' }}">
                                <i class="fas fa-tags"></i> {{ __('Forfaits')}}
                            </a>
                            <a href="{{url('souscrire-forfaits')}}" class="menu-item {{ ($segment1 == 'souscrire-forfaits') ? 'active' : '' }}">
                                <i class="fas fa-file-invoice"></i> {{ __('Souscriptions')}}
                            </a>
                            <a href="{{url('reservation-achat')}}" class="menu-item {{ ($segment1 == 'reservation-achat') ? 'active' : '' }}">
                                <i class="fas fa-shopping-bag"></i> {{ __('Réservations & Achats')}}
                            </a>
                            <a href="{{url('rendez-vous')}}" class="menu-item {{ ($segment1 == 'rendez-vous') ? 'active' : '' }}">
                                <i class="fas fa-calendar"></i> {{ __('Rendez-vous')}}
                            </a>
                            <a href="{{url('partenaires')}}" class="menu-item {{ ($segment1 == 'partenaires') ? 'active' : '' }}">
                                <i class="fas fa-handshake"></i> {{ __('Partenaires')}}
                            </a>
                            <a href="{{url('temoignages')}}" class="menu-item {{ ($segment1 == 'temoignages') ? 'active' : '' }}">
                                <i class="fas fa-star"></i> {{ __('Témoignages')}}
                            </a>
                            <a href="{{url('parrainages')}}" class="menu-item {{ ($segment1 == 'parrainages') ? 'active' : '' }}">
                                <i class="fas fa-users-cog"></i> {{ __('Parrainages')}}
                            </a>
                            <a href="{{url('villes')}}" class="menu-item {{ ($segment1 == 'villes') ? 'active' : '' }}">
                                <i class="fas fa-map-marker-alt"></i> {{ __('Villes')}}
                            </a>
                            <a href="{{url('statuts-etat')}}" class="menu-item {{ ($segment1 == 'statuts-etat') ? 'active' : '' }}">
                                <i class="fas fa-tags"></i> {{ __('Statuts État')}}
                            </a>
                        </div>
                    </div>

                    {{-- Contenu & Communication Super Admin --}}
                    <div class="nav-item {{ (in_array($segment1, ['actualites', 'faqs', 'sliders', 'categories', 'galerie-video', 'galerie-image', 'categories-images'])) ? 'active open' : '' }} has-sub">
                        <a href="#"><i class="fas fa-newspaper"></i><span>{{ __('Contenu & Communication')}}</span></a>
                        <div class="submenu-content">
                            <a href="{{url('actualites')}}" class="menu-item {{ ($segment1 == 'actualites') ? 'active' : '' }}">
                                <i class="fas fa-newspaper"></i> {{ __('Actualités')}}
                            </a>
                            <a href="{{url('faqs')}}" class="menu-item {{ ($segment1 == 'faqs') ? 'active' : '' }}">
                                <i class="fas fa-question-circle"></i> {{ __('FAQs')}}
                            </a>
                            <a href="{{url('sliders')}}" class="menu-item {{ ($segment1 == 'sliders') ? 'active' : '' }}">
                                <i class="fas fa-images"></i> {{ __('Sliders')}}
                            </a>
                            <a href="{{url('categories')}}" class="menu-item {{ ($segment1 == 'categories') ? 'active' : '' }}">
                                <i class="fas fa-folder"></i> {{ __('Catégories')}}
                            </a>
                            <a href="{{url('galerie-video')}}" class="menu-item {{ ($segment1 == 'galerie-video') ? 'active' : '' }}">
                                <i class="fas fa-video"></i> {{ __('Galerie Vidéo')}}
                            </a>
                            <a href="{{url('galerie-image')}}" class="menu-item {{ ($segment1 == 'galerie-image') ? 'active' : '' }}">
                                <i class="fas fa-images"></i> {{ __('Galerie Images')}}
                            </a>
                            <a href="{{url('categories-images')}}" class="menu-item {{ ($segment1 == 'categories-images') ? 'active' : '' }}">
                                <i class="fas fa-folder-open"></i> {{ __('Catégories Images')}}
                            </a>
                        </div>
                    </div>

                {{-- ==================== SIDEBAR BASÉ UNIQUEMENT SUR LES PERMISSIONS ASSIGNÉES ==================== --}}
                @else
                    
                    {{-- Dashboard Admin si permission assignée --}}
                    @if(hasAssignedPermission($user, 'view_dashboard_admin'))
                        <div class="nav-item {{ ($segment1 == 'admin') ? 'active' : '' }}">
                            <a href="{{url('admin/dashboard')}}">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>{{ __('Dashboard Admin')}}</span>
                            </a>
                        </div>
                    @endif
                    
                    {{-- Dashboard Commercial si permission assignée --}}
                    @if(hasAssignedPermission($user, 'view_dashboard_commercial'))
                        <div class="nav-item {{ ($segment1 == 'commercial') ? 'active' : '' }}">
                            <a href="{{url('commercial/dashboard')}}">
                                <i class="fas fa-chart-line"></i>
                                <span>{{ __('Dashboard Commercial')}}</span>
                            </a>
                        </div>
                    @endif
                    
                    {{-- Dashboard Comptoir si permission assignée --}}
                    @if(hasAssignedPermission($user, 'view_dashboard_comptoir'))
                        <div class="nav-item {{ ($segment1 == 'comptoir') ? 'active' : '' }}">
                            <a href="{{url('comptoir/dashboard')}}">
                                <i class="fas fa-desktop"></i>
                                <span>{{ __('Dashboard Comptoir')}}</span>
                            </a>
                        </div>
                    @endif
                    
                    {{-- ✅ MODULE CRM - Si l'utilisateur a AU MOINS UNE permission CRM --}}
                    @if($hasAnyCrmAccess)
                        <div class="nav-item {{ ($segment1 == 'crm') ? 'active' : '' }}">
                            <a href="{{url('crm')}}">
                                <i class="fas fa-chart-line"></i>
                                <span>{{ __('CRM Business')}}</span>
                                @if(hasCrmPermission($user, 'admin'))
                                    <span class="badge bg-danger ms-auto">Admin</span>
                                @elseif(hasCrmPermission($user, 'performance') || hasCrmPermission($user, 'analytics'))
                                    <span class="badge bg-info ms-auto">Manager</span>
                                @else
                                    <span class="badge bg-success ms-auto">Agent</span>
                                @endif
                            </a>
                        </div>
                    @endif

                    {{-- MODULE CAISSE - Si l'utilisateur a la permission d'accès --}}
                    @if(hasAssignedPermission($user, 'access_caisse') || $user->can_access_caisse)
                        <div class="nav-item {{ ($segment1 == 'caisse') ? 'active' : '' }}">
                            <a href="{{url('caisse')}}">
                                <i class="fas fa-cash-register"></i>
                                <span>{{ __('Gestion de Caisse')}}</span>
                                <span class="badge bg-warning ms-auto">Finance</span>
                            </a>
                        </div>
                    @endif

                    {{-- MODULE CALENDRIER - Si l'utilisateur a accès au CRM --}}
                    @if($hasAnyCrmAccess)
                        <div class="nav-item {{ ($segment1 == 'crm' && $segment2 == 'calendrier') ? 'active' : '' }}">
                            <a href="{{url('crm/calendrier')}}">
                                <i class="fas fa-calendar-alt"></i>
                                <span>{{ __('Calendrier')}}</span>
                            </a>
                        </div>
                    @endif

                    {{-- GESTION DES UTILISATEURS - Construction dynamique --}}
                    @php
                        $userManagementItems = [];
                        if(hasAssignedPermission($user, 'view_users') || hasAssignedPermission($user, 'manage_users')) {
                            $userManagementItems[] = ['url' => 'users', 'title' => 'Agents Internes', 'icon' => 'fas fa-user-tie', 'segment' => 'users'];
                            $userManagementItems[] = ['url' => 'agents', 'title' => 'Liste Agents', 'icon' => 'fas fa-users-cog', 'segment' => 'agents'];
                        }
                        if(hasAssignedPermission($user, 'create_user')) {
                            $userManagementItems[] = ['url' => 'users/create', 'title' => 'Ajouter un Agent', 'icon' => 'fas fa-user-plus', 'segment' => 'user', 'segment2' => 'create'];
                        }
                        if(hasAssignedPermission($user, 'view_clients') || hasAssignedPermission($user, 'manage_clients')) {
                            $userManagementItems[] = ['url' => 'public-users', 'title' => 'Clients Publics', 'icon' => 'fas fa-users', 'segment' => 'public-users'];
                            $userManagementItems[] = ['url' => 'list-clients', 'title' => 'Liste Clients', 'icon' => 'fas fa-list', 'segment' => 'list-clients'];
                        }
                    @endphp
                    @if(!empty($userManagementItems))
                        <div class="nav-item {{ (in_array($segment1, array_column($userManagementItems, 'segment'))) ? 'active open' : '' }} has-sub">
                            <a href="#"><i class="fas fa-users"></i><span>{{ __('Gestion Utilisateurs')}}</span></a>
                            <div class="submenu-content">
                                @foreach($userManagementItems as $item)
                                    <a href="{{url($item['url'])}}" class="menu-item {{ ($segment1 == $item['segment'] && (!isset($item['segment2']) || $segment2 == $item['segment2'])) ? 'active' : '' }}">
                                        <i class="{{$item['icon']}}"></i> {{ __($item['title'])}}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    {{-- PROFILS VISA - Élément principal pour les agents --}}
                    @if(hasAssignedPermission($user, 'view_profil_visa') || hasAssignedPermission($user, 'manage_profil_visa'))
                        <div class="nav-item {{ ($segment1 == 'profil-visa') ? 'active' : '' }}">
                            <a href="{{url('profil-visa')}}">
                                <i class="fas fa-passport"></i>
                                <span>{{ __('Profils Visa')}}</span>
                                @if(hasAssignedPermission($user, 'manage_profil_visa'))
                                    <span class="badge bg-primary ms-auto">Gestion</span>
                                @endif
                            </a>
                        </div>
                    @endif
                    
                    {{-- MODULES MÉTIER - Construction dynamique --}}
                    @php
                        $businessItems = [];
                        if(hasAssignedPermission($user, 'view_services') || hasAssignedPermission($user, 'manage_services')) {
                            $businessItems[] = ['url' => 'services', 'title' => 'Services', 'icon' => 'fas fa-cogs', 'segment' => 'services'];
                        }
                        if(hasAssignedPermission($user, 'view_forfaits') || hasAssignedPermission($user, 'manage_forfaits')) {
                            $businessItems[] = ['url' => 'forfaits', 'title' => 'Forfaits', 'icon' => 'fas fa-tags', 'segment' => 'forfaits'];
                        }
                        if(hasAssignedPermission($user, 'view_souscrire_forfaits') || hasAssignedPermission($user, 'manage_souscrire_forfaits')) {
                            $businessItems[] = ['url' => 'souscrire-forfaits', 'title' => 'Souscriptions', 'icon' => 'fas fa-file-invoice', 'segment' => 'souscrire-forfaits'];
                        }
                        if(hasAssignedPermission($user, 'view_rendez_vous') || hasAssignedPermission($user, 'manage_rendez_vous')) {
                            $businessItems[] = ['url' => 'rendez-vous', 'title' => 'Rendez-vous', 'icon' => 'fas fa-calendar', 'segment' => 'rendez-vous'];
                        }
                        if(hasAssignedPermission($user, 'view_documents_voyage') || hasAssignedPermission($user, 'manage_documents_voyage')) {
                            $businessItems[] = ['url' => 'documents-voyage', 'title' => 'Documents Voyage', 'icon' => 'fas fa-file-alt', 'segment' => 'documents-voyage'];
                        }
                        if(hasAssignedPermission($user, 'view_reservation_achat') || hasAssignedPermission($user, 'manage_reservation_achat')) {
                            $businessItems[] = ['url' => 'reservation-achat', 'title' => 'Réservations & Achats', 'icon' => 'fas fa-shopping-bag', 'segment' => 'reservation-achat'];
                        }
                        if(hasAssignedPermission($user, 'view_partenaires') || hasAssignedPermission($user, 'manage_partenaires')) {
                            $businessItems[] = ['url' => 'partenaires', 'title' => 'Partenaires', 'icon' => 'fas fa-handshake', 'segment' => 'partenaires'];
                        }
                        if(hasAssignedPermission($user, 'view_temoignages') || hasAssignedPermission($user, 'manage_temoignages')) {
                            $businessItems[] = ['url' => 'temoignages', 'title' => 'Témoignages', 'icon' => 'fas fa-star', 'segment' => 'temoignages'];
                        }
                    @endphp
                    @if(!empty($businessItems))
                        <div class="nav-item {{ (in_array($segment1, array_column($businessItems, 'segment'))) ? 'active open' : '' }} has-sub">
                            <a href="#"><i class="fas fa-briefcase"></i><span>{{ __('Modules Métier')}}</span></a>
                            <div class="submenu-content">
                                @foreach($businessItems as $item)
                                    <a href="{{url($item['url'])}}" class="menu-item {{ ($segment1 == $item['segment']) ? 'active' : '' }}">
                                        <i class="{{$item['icon']}}"></i> {{ __($item['title'])}}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    {{-- CONTENU & COMMUNICATION - Construction dynamique --}}
                    @php
                        $contentItems = [];
                        if(hasAssignedPermission($user, 'view_actualites') || hasAssignedPermission($user, 'manage_actualites')) {
                            $contentItems[] = ['url' => 'actualites', 'title' => 'Actualités', 'icon' => 'fas fa-newspaper', 'segment' => 'actualites'];
                        }
                        if(hasAssignedPermission($user, 'view_faqs') || hasAssignedPermission($user, 'manage_faqs')) {
                            $contentItems[] = ['url' => 'faqs', 'title' => 'FAQs', 'icon' => 'fas fa-question-circle', 'segment' => 'faqs'];
                        }
                        if(hasAssignedPermission($user, 'view_sliders') || hasAssignedPermission($user, 'manage_sliders')) {
                            $contentItems[] = ['url' => 'sliders', 'title' => 'Sliders', 'icon' => 'fas fa-images', 'segment' => 'sliders'];
                        }
                        if(hasAssignedPermission($user, 'view_categories') || hasAssignedPermission($user, 'manage_categories')) {
                            $contentItems[] = ['url' => 'categories', 'title' => 'Catégories', 'icon' => 'fas fa-folder', 'segment' => 'categories'];
                        }
                        if(hasAssignedPermission($user, 'view_galerie_video') || hasAssignedPermission($user, 'manage_galerie_video')) {
                            $contentItems[] = ['url' => 'galerie-video', 'title' => 'Galerie Vidéo', 'icon' => 'fas fa-video', 'segment' => 'galerie-video'];
                        }
                        if(hasAssignedPermission($user, 'view_galerie_images') || hasAssignedPermission($user, 'manage_galerie_images')) {
                            $contentItems[] = ['url' => 'galerie-image', 'title' => 'Galerie Images', 'icon' => 'fas fa-images', 'segment' => 'galerie-image'];
                        }
                        if(hasAssignedPermission($user, 'view_categories_images') || hasAssignedPermission($user, 'manage_categories_images')) {
                            $contentItems[] = ['url' => 'categories-images', 'title' => 'Catégories Images', 'icon' => 'fas fa-folder-open', 'segment' => 'categories-images'];
                        }
                    @endphp
                    @if(!empty($contentItems))
                        <div class="nav-item {{ (in_array($segment1, array_column($contentItems, 'segment'))) ? 'active open' : '' }} has-sub">
                            <a href="#"><i class="fas fa-newspaper"></i><span>{{ __('Contenu & Communication')}}</span></a>
                            <div class="submenu-content">
                                @foreach($contentItems as $item)
                                    <a href="{{url($item['url'])}}" class="menu-item {{ ($segment1 == $item['segment']) ? 'active' : '' }}">
                                        <i class="{{$item['icon']}}"></i> {{ __($item['title'])}}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    {{-- CONFIGURATION & ADMINISTRATION - Construction dynamique --}}
                    @php
                        $configItems = [];
                        if(hasAssignedPermission($user, 'view_roles') || hasAssignedPermission($user, 'manage_roles')) {
                            $configItems[] = ['url' => 'roles', 'title' => 'Gestion des Rôles', 'icon' => 'fas fa-user-shield', 'segment' => 'roles'];
                        }
                        if(hasAssignedPermission($user, 'view_permissions') || hasAssignedPermission($user, 'manage_permissions')) {
                            $configItems[] = ['url' => 'permissions', 'title' => 'Gestion des Permissions', 'icon' => 'fas fa-key', 'segment' => 'permissions'];
                        }
                        if(hasAssignedPermission($user, 'view_statuts_etat') || hasAssignedPermission($user, 'manage_statuts_etat')) {
                            $configItems[] = ['url' => 'statuts-etat', 'title' => 'Statuts État', 'icon' => 'fas fa-tags', 'segment' => 'statuts-etat'];
                        }
                        if(hasAssignedPermission($user, 'view_villes') || hasAssignedPermission($user, 'manage_villes')) {
                            $configItems[] = ['url' => 'villes', 'title' => 'Villes', 'icon' => 'fas fa-map-marker-alt', 'segment' => 'villes'];
                        }
                        if(hasAssignedPermission($user, 'view_parrainages') || hasAssignedPermission($user, 'manage_parrainages')) {
                            $configItems[] = ['url' => 'parrainages', 'title' => 'Parrainages', 'icon' => 'fas fa-users-cog', 'segment' => 'parrainages'];
                        }
                        if(hasAssignedPermission($user, 'manage_system_config')) {
                            $configItems[] = ['url' => 'configuration', 'title' => 'Configuration Système', 'icon' => 'fas fa-cogs', 'segment' => 'configuration'];
                        }
                        if(hasAssignedPermission($user, 'view_logs')) {
                            $configItems[] = ['url' => 'log-stat', 'title' => 'Logs Système', 'icon' => 'fas fa-file-alt', 'segment' => 'log-stat'];
                        }
                    @endphp
                    @if(!empty($configItems))
                        <div class="nav-item {{ (in_array($segment1, array_column($configItems, 'segment')) || in_array($segment1, ['permission'])) ? 'active open' : '' }} has-sub">
                            <a href="#"><i class="fas fa-cogs"></i><span>{{ __('Configuration & Administration')}}</span></a>
                            <div class="submenu-content">
                                @foreach($configItems as $item)
                                    <a href="{{url($item['url'])}}" class="menu-item {{ ($segment1 == $item['segment'] || (in_array($segment1, ['permission', 'permissions']) && $item['segment'] == 'permissions')) ? 'active' : '' }}">
                                        <i class="{{$item['icon']}}"></i> {{ __($item['title'])}}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    {{-- UTILISATEURS PUBLICS - Affichage pour les utilisateurs sans rôles --}}
                    @if($isPublic)
                        <div class="nav-item {{ ($segment1 == 'mes-demandes' || $segment1 == 'profil-visa') ? 'active' : '' }}">
                            <a href="{{url('profil-visa')}}">
                                <i class="fas fa-passport"></i>
                                <span>{{ __('Mes Profils Visa')}}</span>
                            </a>
                        </div>

                        <div class="nav-item {{ ($segment1 == 'mes-dossiers') ? 'active' : '' }}">
                            <a href="{{url('mes-dossiers')}}">
                                <i class="fas fa-folder-open"></i>
                                <span>{{ __('J\'envoie mes dossiers')}}</span>
                            </a>
                        </div>

                        <div class="nav-item {{ ($segment1 == 'mes-factures') ? 'active' : '' }}">
                            <a href="{{url('mes-factures')}}">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <span>{{ __('Mes factures et paiement')}}</span>
                            </a>
                        </div>
                    @endif

                @endif

                {{-- ==================== SECTIONS COMMUNES ==================== --}}
                
                {{-- Mon Profil --}}
                <div class="nav-item {{ ($segment1 == 'profile') ? 'active' : '' }}">
                     <a href="{{url('profile')}}">
                         <i class="fa fa-user"></i>
                         <span>{{ Auth::user()->name}}</span>
                     </a>
                </div>
                
                {{-- Déconnexion --}}
                <div class="nav-item {{ ($segment1 == 'logout') ? 'active' : '' }}">
                     <a href="{{url('logout')}}">
                         <i class="ik ik-power dropdown-icon"></i>
                         <span>Se déconnecter</span>
                     </a>
                </div>

            </nav>   
        </div>
    </div>
</div>


<style>
.nav-item.has-sub .submenu-content {
    max-height: 0;
    overflow: hidden;
    transition: all 0.3s ease;
}

.nav-item.has-sub.open .submenu-content {
    max-height: 800px;
}

.menu-item {
    display: block;
    padding: 8px 20px 8px 45px;
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.menu-item:hover {
    color: #fff;
    background-color: rgba(255,255,255,0.1);
    text-decoration: none;
}

.menu-item.active {
    color: #fff;
    background-color: rgba(255,255,255,0.2);
    border-left: 3px solid #fff;
}

.menu-item i {
    margin-right: 8px;
    width: 16px;
    text-align: center;
}

.badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 3px;
}

.ms-auto {
    margin-left: auto;
}

.bg-success {
    background-color: #16a34a !important;
    color: white !important;
}

.bg-info {
    background-color: #06b6d4 !important;
    color: white !important;
}

.bg-danger {
    background-color: #ef4444 !important;
    color: white !important;
}

.bg-warning {
    background-color: #f59e0b !important;
    color: white !important;
}

.bg-primary {
    background-color: #3b82f6 !important;
    color: white !important;
}
</style>