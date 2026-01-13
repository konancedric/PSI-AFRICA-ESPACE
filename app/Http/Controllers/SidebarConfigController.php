<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;

/**
 * ✅ SIDEBAR CONTROLLER - CORRECTION COMPLÈTE 404
 * 
 * Toutes les URLs correspondent maintenant aux routes définies dans web.php
 * Toutes les permissions sont vérifiées avant d'afficher les éléments
 */
class SidebarConfigController extends Controller
{
    /**
     * ✅ MÉTHODE PRINCIPALE - Configuration des sidebars par type d'utilisateur
     */
    public static function getSidebarConfig($user)
    {
        try {
            $config = [
                'user_type' => $user->type_user ?? 'public',
                'roles' => $user->getRoleNames()->toArray(),
                'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
                'sections' => []
            ];

            // Configuration par rôle avec vérification des permissions
            if ($user->hasRole('Super Admin')) {
                $config['sections'] = self::getSuperAdminSidebar($user);
            } elseif ($user->hasRole('Admin')) {
                $config['sections'] = self::getAdminSidebar($user);
            } elseif ($user->hasRole('Commercial') || $user->type_user === 'commercial') {
                $config['sections'] = self::getCommercialSidebar($user);
            } elseif ($user->hasRole('Agent Comptoir') || $user->type_user === 'agent_comptoir') {
                $config['sections'] = self::getComptoirSidebar($user);
            } elseif ($user->type_user === 'public') {
                $config['sections'] = self::getPublicSidebar($user);
            } else {
                $config['sections'] = self::getDefaultSidebar($user);
            }

            Log::info('Sidebar configurée pour: ' . $user->name, [
                'user_type' => $config['user_type'],
                'roles' => $config['roles'],
                'sections_count' => count($config['sections'])
            ]);

            return $config;

        } catch (\Exception $e) {
            Log::error('Erreur getSidebarConfig: ' . $e->getMessage());
            return [
                'user_type' => 'public',
                'roles' => [],
                'permissions' => [],
                'sections' => self::getDefaultSidebar($user)
            ];
        }
    }

    /**
     * ✅ SUPER ADMIN SIDEBAR - Accès complet avec toutes les routes corrigées
     */
    private static function getSuperAdminSidebar($user)
    {
        return [
            [
                'type' => 'single',
                'title' => 'Dashboard Principal',
                'icon' => 'ik ik-bar-chart-2',
                'url' => '/dashboard',
                'active_routes' => ['dashboard']
            ],
            [
                'type' => 'single',
                'title' => 'Dashboard Admin',
                'icon' => 'fas fa-crown',
                'url' => '/admin/dashboard',
                'active_routes' => ['admin']
            ],
            [
                'type' => 'group',
                'title' => 'Administration Système',
                'icon' => 'ik ik-settings',
                'active_routes' => ['roles', 'permission', 'users', 'agents', 'configuration'],
                'items' => [
                    [
                        'title' => 'Gestion des Rôles',
                        'icon' => 'fas fa-user-shield',
                        'url' => '/roles',
                        'permission' => 'manage_role'
                    ],
                    [
                        'title' => 'Gestion des Permissions',
                        'icon' => 'fas fa-key',
                        'url' => '/permissions',
                        'permission' => 'manage_permission'
                    ],
                    [
                        'title' => 'Gestion des Agents',
                        'icon' => 'fas fa-users-cog',
                        'url' => '/users',
                        'permission' => 'manage_user'
                    ],
                    [
                        'title' => 'Ajouter un Agent',
                        'icon' => 'fas fa-user-plus',
                        'url' => '/users/create',
                        'permission' => 'create_user'
                    ],
                    [
                        'title' => 'Agents Internes',
                        'icon' => 'fas fa-user-tie',
                        'url' => '/agents',
                        'permission' => 'manage_user'
                    ],
                    [
                        'title' => 'Configuration Système',
                        'icon' => 'fas fa-cogs',
                        'url' => '/configuration',
                        'permission' => 'manage_system_config'
                    ]
                ]
            ],
            [
                'type' => 'group',
                'title' => 'Gestion Clients',
                'icon' => 'fas fa-users',
                'active_routes' => ['clients', 'public-users', 'list-clients'],
                'items' => [
                    [
                        'title' => 'Clients Publics',
                        'icon' => 'fas fa-users',
                        'url' => '/public-users',
                        'permission' => 'view_clients'
                    ],
                    [
                        'title' => 'Liste Clients',
                        'icon' => 'fas fa-list',
                        'url' => '/list-clients',
                        'permission' => 'view_clients'
                    ]
                ]
            ],
            [
                'type' => 'group',
                'title' => 'Modules Métier',
                'icon' => 'fas fa-th-large',
                'active_routes' => ['services', 'forfaits', 'profil-visa', 'rendez-vous'],
                'items' => [
                    [
                        'title' => 'Profils Visa',
                        'icon' => 'fas fa-passport',
                        'url' => '/profil-visa',
                        'permission' => 'view_profil_visa'
                    ],
                    [
                        'title' => 'Gestion Services',
                        'icon' => 'fas fa-cogs',
                        'url' => '/services',
                        'permission' => 'view_services'
                    ],
                    [
                        'title' => 'Gestion Forfaits',
                        'icon' => 'fas fa-tags',
                        'url' => '/forfaits',
                        'permission' => 'view_forfaits'
                    ],
                    [
                        'title' => 'Rendez-vous',
                        'icon' => 'fas fa-calendar',
                        'url' => '/rendez-vous',
                        'permission' => 'view_rendez_vous'
                    ]
                ]
            ],
            [
                'type' => 'group',
                'title' => 'Marketing & Partenariats',
                'icon' => 'fas fa-handshake',
                'active_routes' => ['partenaires', 'temoignages'],
                'items' => [
                    [
                        'title' => 'Gestion Partenaires',
                        'icon' => 'fas fa-handshake',
                        'url' => '/partenaires',
                        'permission' => 'view_partenaires'
                    ],
                    [
                        'title' => 'Témoignages',
                        'icon' => 'fas fa-star',
                        'url' => '/temoignages',
                        'permission' => 'view_temoignages'
                    ]
                ]
            ],
            [
                'type' => 'group',
                'title' => 'Système & Logs',
                'icon' => 'fas fa-server',
                'active_routes' => ['logs', 'debug'],
                'items' => [
                    [
                        'title' => 'Logs Système',
                        'icon' => 'fas fa-file-alt',
                        'url' => '/log-stat',
                        'permission' => 'view_logs'
                    ],
                    [
                        'title' => 'Tests Debug',
                        'icon' => 'fas fa-bug',
                        'url' => '/debug/test-permissions',
                        'permission' => 'manage_system_config'
                    ]
                ]
            ]
        ];
    }

    /**
     * ✅ ADMIN SIDEBAR - Accès administratif avec routes corrigées
     */
    private static function getAdminSidebar($user)
    {
        $sections = [
            [
                'type' => 'single',
                'title' => 'Dashboard Principal',
                'icon' => 'ik ik-bar-chart-2',
                'url' => '/dashboard',
                'active_routes' => ['dashboard']
            ]
        ];

        // Dashboard Admin
        if ($user->can('view_dashboard_admin')) {
            $sections[] = [
                'type' => 'single',
                'title' => 'Dashboard Admin',
                'icon' => 'fas fa-tachometer-alt',
                'url' => '/admin/dashboard',
                'active_routes' => ['admin']
            ];
        }

        // Gestion Utilisateurs
        if ($user->can('view_user') || $user->can('manage_user')) {
            $userItems = [];
            
            if ($user->can('view_user')) {
                $userItems[] = [
                    'title' => 'Agents Internes',
                    'icon' => 'fas fa-user-tie',
                    'url' => '/users'
                ];
                
                $userItems[] = [
                    'title' => 'Liste Agents',
                    'icon' => 'fas fa-users-cog',
                    'url' => '/agents'
                ];
            }
            
            if ($user->can('create_user')) {
                $userItems[] = [
                    'title' => 'Ajouter Agent',
                    'icon' => 'fas fa-user-plus',
                    'url' => '/users/create'
                ];
            }
            
            if ($user->can('view_clients')) {
                $userItems[] = [
                    'title' => 'Clients Publics',
                    'icon' => 'fas fa-users',
                    'url' => '/public-users'
                ];
            }

            if (!empty($userItems)) {
                $sections[] = [
                    'type' => 'group',
                    'title' => 'Gestion Utilisateurs',
                    'icon' => 'fas fa-users',
                    'active_routes' => ['users', 'agents', 'public-users'],
                    'items' => $userItems
                ];
            }
        }

        // Gestion Système
        if ($user->can('manage_role') || $user->can('manage_permission') || $user->can('manage_system_config')) {
            $systemItems = [];
            
            if ($user->can('manage_role')) {
                $systemItems[] = [
                    'title' => 'Gestion des Rôles',
                    'icon' => 'fas fa-user-shield',
                    'url' => '/roles'
                ];
            }
            
            if ($user->can('manage_permission')) {
                $systemItems[] = [
                    'title' => 'Permissions',
                    'icon' => 'fas fa-key',
                    'url' => '/permissions'
                ];
            }
            
            if ($user->can('manage_system_config')) {
                $systemItems[] = [
                    'title' => 'Configuration',
                    'icon' => 'fas fa-cogs',
                    'url' => '/configuration'
                ];
            }

            if (!empty($systemItems)) {
                $sections[] = [
                    'type' => 'group',
                    'title' => 'Administration',
                    'icon' => 'fas fa-cogs',
                    'active_routes' => ['roles', 'permissions', 'configuration'],
                    'items' => $systemItems
                ];
            }
        }

        // Modules Métier
        if ($user->can('view_profil_visa') || $user->can('view_services') || $user->can('view_forfaits')) {
            $businessItems = [];
            
            if ($user->can('view_profil_visa')) {
                $businessItems[] = [
                    'title' => 'Profils Visa',
                    'icon' => 'fas fa-passport',
                    'url' => '/profil-visa'
                ];
            }
            
            if ($user->can('view_services')) {
                $businessItems[] = [
                    'title' => 'Services',
                    'icon' => 'fas fa-cogs',
                    'url' => '/services'
                ];
            }
            
            if ($user->can('view_forfaits')) {
                $businessItems[] = [
                    'title' => 'Forfaits',
                    'icon' => 'fas fa-tags',
                    'url' => '/forfaits'
                ];
            }

            if (!empty($businessItems)) {
                $sections[] = [
                    'type' => 'group',
                    'title' => 'Modules Métier',
                    'icon' => 'fas fa-briefcase',
                    'active_routes' => ['profil-visa', 'services', 'forfaits'],
                    'items' => $businessItems
                ];
            }
        }

        return $sections;
    }

    /**
     * ✅ COMMERCIAL SIDEBAR - Routes corrigées pour les commerciaux
     */
    private static function getCommercialSidebar($user)
    {
        $sections = [
            [
                'type' => 'single',
                'title' => 'Dashboard Principal',
                'icon' => 'ik ik-bar-chart-2',
                'url' => '/dashboard',
                'active_routes' => ['dashboard']
            ]
        ];

        // Dashboard Commercial
        if ($user->can('view_dashboard_commercial')) {
            $sections[] = [
                'type' => 'single',
                'title' => 'Dashboard Commercial',
                'icon' => 'fas fa-chart-line',
                'url' => '/commercial/dashboard',
                'active_routes' => ['commercial']
            ];
        }

        // Gestion Clients
        if ($user->can('manage_clients') || $user->can('view_clients')) {
            $clientItems = [];
            
            if ($user->can('view_dashboard_commercial')) {
                $clientItems[] = [
                    'title' => 'Tableau de Bord Clients',
                    'icon' => 'fas fa-chart-pie',
                    'url' => '/commercial/clients'
                ];
            }
            
            if ($user->can('view_clients')) {
                $clientItems[] = [
                    'title' => 'Liste Clients',
                    'icon' => 'fas fa-list',
                    'url' => '/list-clients'
                ];
                
                $clientItems[] = [
                    'title' => 'Clients Publics',
                    'icon' => 'fas fa-users',
                    'url' => '/public-users'
                ];
            }

            if (!empty($clientItems)) {
                $sections[] = [
                    'type' => 'group',
                    'title' => 'Gestion Clients',
                    'icon' => 'fas fa-users',
                    'active_routes' => ['clients', 'list-clients', 'public-users'],
                    'items' => $clientItems
                ];
            }
        }

        // Produits & Ventes
        if ($user->can('manage_forfaits') || $user->can('manage_souscrire_forfaits') || $user->can('manage_rendez_vous')) {
            $venteItems = [];
            
            if ($user->can('view_forfaits')) {
                $venteItems[] = [
                    'title' => 'Gestion Forfaits',
                    'icon' => 'fas fa-tags',
                    'url' => '/forfaits'
                ];
            }
            
            if ($user->can('view_souscrire_forfaits')) {
                $venteItems[] = [
                    'title' => 'Souscriptions',
                    'icon' => 'fas fa-file-invoice',
                    'url' => '/souscrire-forfaits'
                ];
            }
            
            if ($user->can('view_rendez_vous')) {
                $venteItems[] = [
                    'title' => 'Rendez-vous',
                    'icon' => 'fas fa-calendar',
                    'url' => '/rendez-vous'
                ];
            }

            if (!empty($venteItems)) {
                $sections[] = [
                    'type' => 'group',
                    'title' => 'Produits & Ventes',
                    'icon' => 'fas fa-shopping-cart',
                    'active_routes' => ['forfaits', 'souscrire-forfaits', 'rendez-vous'],
                    'items' => $venteItems
                ];
            }
        }

        // Marketing & Partenariats
        if ($user->can('view_partenaires') || $user->can('view_temoignages')) {
            $marketingItems = [];
            
            if ($user->can('view_partenaires')) {
                $marketingItems[] = [
                    'title' => 'Partenaires',
                    'icon' => 'fas fa-handshake',
                    'url' => '/partenaires'
                ];
            }
            
            if ($user->can('view_temoignages')) {
                $marketingItems[] = [
                    'title' => 'Témoignages',
                    'icon' => 'fas fa-star',
                    'url' => '/temoignages'
                ];
            }

            if (!empty($marketingItems)) {
                $sections[] = [
                    'type' => 'group',
                    'title' => 'Marketing',
                    'icon' => 'fas fa-bullhorn',
                    'active_routes' => ['partenaires', 'temoignages'],
                    'items' => $marketingItems
                ];
            }
        }

        // Statistiques & Rapports
        if ($user->can('view_dashboard_commercial')) {
            $rapportItems = [];
            
            $rapportItems[] = [
                'title' => 'Statistiques',
                'icon' => 'fas fa-chart-pie',
                'url' => '/commercial/statistiques'
            ];
            
            $rapportItems[] = [
                'title' => 'Export Données',
                'icon' => 'fas fa-download',
                'url' => '/commercial/exports'
            ];

            $sections[] = [
                'type' => 'group',
                'title' => 'Rapports',
                'icon' => 'fas fa-chart-bar',
                'active_routes' => ['commercial/statistiques', 'commercial/exports'],
                'items' => $rapportItems
            ];
        }

        return $sections;
    }

    /**
     * ✅ COMPTOIR SIDEBAR - Routes corrigées pour agents comptoir
     */
    private static function getComptoirSidebar($user)
    {
        $sections = [
            [
                'type' => 'single',
                'title' => 'Dashboard Principal',
                'icon' => 'ik ik-bar-chart-2',
                'url' => '/dashboard',
                'active_routes' => ['dashboard']
            ]
        ];

        // Dashboard Comptoir
        if ($user->can('view_dashboard_comptoir')) {
            $sections[] = [
                'type' => 'single',
                'title' => 'Dashboard Comptoir',
                'icon' => 'fas fa-desktop',
                'url' => '/comptoir/dashboard',
                'active_routes' => ['comptoir']
            ];
        }

        // Profils Visa (Principal pour agents comptoir)
        if ($user->can('view_profil_visa')) {
            $sections[] = [
                'type' => 'single',
                'title' => 'Profils Visa',
                'icon' => 'fas fa-passport',
                'url' => '/profil-visa',
                'active_routes' => ['profil-visa'],
                'badge' => 'Principal'
            ];
        }

        // Services Comptoir
        $comptoirItems = [];
        
        if ($user->can('view_rendez_vous')) {
            $comptoirItems[] = [
                'title' => 'Rendez-vous',
                'icon' => 'fas fa-calendar',
                'url' => '/rendez-vous'
            ];
        }

        if ($user->can('view_documentsvoyage')) {
            $comptoirItems[] = [
                'title' => 'Documents Voyage',
                'icon' => 'fas fa-file-alt',
                'url' => '/documents-voyage'
            ];
        }

        if ($user->can('view_reservation_achat')) {
            $comptoirItems[] = [
                'title' => 'Réservations',
                'icon' => 'fas fa-shopping-bag',
                'url' => '/reservation-achat'
            ];
        }

        if ($user->can('view_services')) {
            $comptoirItems[] = [
                'title' => 'Services',
                'icon' => 'fas fa-cogs',
                'url' => '/services'
            ];
        }

        if (!empty($comptoirItems)) {
            $sections[] = [
                'type' => 'group',
                'title' => 'Services Comptoir',
                'icon' => 'fas fa-concierge-bell',
                'active_routes' => ['rendez-vous', 'documents-voyage', 'reservation-achat', 'services'],
                'items' => $comptoirItems
            ];
        }

        // Gestion des Statuts
        if ($user->can('view_statuts_etat') || $user->can('edit_profil_visa_status')) {
            $statutsItems = [];
            
            if ($user->can('view_statuts_etat')) {
                $statutsItems[] = [
                    'title' => 'Statuts État',
                    'icon' => 'fas fa-tags',
                    'url' => '/statuts-etat'
                ];
            }

            if (!empty($statutsItems)) {
                $sections[] = [
                    'type' => 'group',
                    'title' => 'Configuration',
                    'icon' => 'fas fa-cog',
                    'active_routes' => ['statuts-etat'],
                    'items' => $statutsItems
                ];
            }
        }

        return $sections;
    }

    /**
     * ✅ PUBLIC USER SIDEBAR - Routes simplifiées pour utilisateurs publics
     */
    private static function getPublicSidebar($user)
    {
        return [
            [
                'type' => 'single',
                'title' => 'Mon Dashboard',
                'icon' => 'ik ik-bar-chart-2',
                'url' => '/dashboard',
                'active_routes' => ['dashboard']
            ],
            [
                'type' => 'single',
                'title' => 'Mes Profils Visa',
                'icon' => 'fas fa-passport',
                'url' => '/profil-visa',
                'active_routes' => ['profil-visa', 'mes-demandes'],
                'badge' => 'Principal'
            ],
            [
                'type' => 'single',
                'title' => 'Mes Demandes',
                'icon' => 'fas fa-file-alt',
                'url' => '/mes-demandes',
                'active_routes' => ['mes-demandes']
            ],
            [
                'type' => 'single',
                'title' => 'Mon Profil',
                'icon' => 'fas fa-user',
                'url' => '/profile',
                'active_routes' => ['profile']
            ],
            [
                'type' => 'group',
                'title' => 'Services',
                'icon' => 'fas fa-concierge-bell',
                'active_routes' => ['about', 'contact', 'faq'],
                'items' => [
                    [
                        'title' => 'À propos',
                        'icon' => 'fas fa-info-circle',
                        'url' => '/about'
                    ],
                    [
                        'title' => 'Contact',
                        'icon' => 'fas fa-envelope',
                        'url' => '/contact'
                    ],
                    [
                        'title' => 'FAQ',
                        'icon' => 'fas fa-question-circle',
                        'url' => '/faq'
                    ]
                ]
            ]
        ];
    }

    /**
     * ✅ DEFAULT SIDEBAR - Sidebar par défaut avec routes de base
     */
    private static function getDefaultSidebar($user)
    {
        return [
            [
                'type' => 'single',
                'title' => 'Dashboard',
                'icon' => 'ik ik-bar-chart-2',
                'url' => '/dashboard',
                'active_routes' => ['dashboard']
            ],
            [
                'type' => 'single',
                'title' => 'Mon Profil',
                'icon' => 'fas fa-user',
                'url' => '/profile',
                'active_routes' => ['profile']
            ]
        ];
    }

    /**
     * ✅ RENDER SIDEBAR - Génération du HTML avec vérification des permissions
     */
    public static function renderSidebar($config, $currentSegment1, $currentSegment2 = null)
    {
        try {
            $html = '';
            $user = Auth::user();
            
            foreach ($config['sections'] as $section) {
                // Vérifier si l'utilisateur a accès à cette section
                if (!self::hasAccessToSection($user, $section)) {
                    continue;
                }

                if ($section['type'] === 'single') {
                    $html .= self::renderSingleItem($section, $currentSegment1);
                } elseif ($section['type'] === 'group') {
                    $html .= self::renderGroupItem($section, $currentSegment1, $currentSegment2, $user);
                }
            }
            
            return $html;

        } catch (\Exception $e) {
            Log::error('Erreur renderSidebar: ' . $e->getMessage());
            return '<div class="nav-item"><span class="text-danger">Erreur chargement sidebar</span></div>';
        }
    }

    /**
     * ✅ VÉRIFICATION ACCÈS SECTION
     */
    private static function hasAccessToSection($user, $section)
    {
        try {
            // Vérifier les permissions sur les items si c'est un groupe
            if ($section['type'] === 'group' && isset($section['items'])) {
                $hasAccessToAtLeastOneItem = false;
                foreach ($section['items'] as $item) {
                    if (!isset($item['permission']) || $user->can($item['permission'])) {
                        $hasAccessToAtLeastOneItem = true;
                        break;
                    }
                }
                return $hasAccessToAtLeastOneItem;
            }

            // Pour les items simples, vérifier la permission si elle existe
            if (isset($section['permission'])) {
                return $user->can($section['permission']);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Erreur hasAccessToSection: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ✅ RENDER SINGLE ITEM
     */
    private static function renderSingleItem($section, $currentSegment1)
    {
        $isActive = in_array($currentSegment1, $section['active_routes']) ? 'active' : '';
        $badge = isset($section['badge']) ? '<span class="badge badge-info ml-2">' . $section['badge'] . '</span>' : '';
        
        $html = '<div class="nav-item ' . $isActive . '">';
        $html .= '<a href="' . url($section['url']) . '" class="nav-link">';
        $html .= '<i class="' . $section['icon'] . ' nav-icon"></i>';
        $html .= '<span class="nav-text">' . __($section['title']) . '</span>';
        $html .= $badge;
        $html .= '</a>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * ✅ RENDER GROUP ITEM
     */
    private static function renderGroupItem($section, $currentSegment1, $currentSegment2, $user)
    {
        $isActive = array_intersect([$currentSegment1], $section['active_routes']) ? 'active open' : '';
        
        $html = '<div class="nav-item ' . $isActive . ' has-sub">';
        $html .= '<a href="#" class="nav-link">';
        $html .= '<i class="' . $section['icon'] . ' nav-icon"></i>';
        $html .= '<span class="nav-text">' . __($section['title']) . '</span>';
        $html .= '<i class="fas fa-chevron-right submenu-arrow"></i>';
        $html .= '</a>';
        $html .= '<div class="submenu-content">';
        
        foreach ($section['items'] as $item) {
            // Vérifier les permissions pour chaque item
            if (isset($item['permission']) && !$user->can($item['permission'])) {
                continue;
            }

            $itemActive = request()->url() === url($item['url']) ? 'active' : '';
            
            $html .= '<a href="' . url($item['url']) . '" class="submenu-item ' . $itemActive . '">';
            $html .= '<i class="' . $item['icon'] . ' submenu-icon"></i>';
            $html .= '<span>' . __($item['title']) . '</span>';
            $html .= '</a>';
        }
        
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * ✅ MÉTHODE DE DEBUG - Vérifier les permissions utilisateur
     */
    public static function debugUserPermissions($user)
    {
        try {
            return [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_type' => $user->type_user,
                'roles' => $user->getRoleNames()->toArray(),
                'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
                'direct_permissions' => $user->getDirectPermissions()->pluck('name')->toArray(),
                'role_permissions' => $user->getPermissionsViaRoles()->pluck('name')->toArray(),
                'sidebar_config_generated' => true
            ];
        } catch (\Exception $e) {
            Log::error('Erreur debugUserPermissions: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}