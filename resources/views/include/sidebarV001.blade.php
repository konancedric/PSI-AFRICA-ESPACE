<div class="app-sidebar colored">
    <div class="sidebar-header">
        <a class="header-brand" href="{{url('dashboard')}}">
            <div class="logo-img">
               <img height="30" src="{{ asset('img/logo_white.png')}}" class="header-brand-img" title="{{ config('app.name') }}"> 
            </div>
        </a>
        <div class="sidebar-action"><i class="ik ik-arrow-left-circle"></i></div>
        <button id="sidebarClose" class="nav-close"><i class="ik ik-x"></i></button>
    </div>

    @php
        $segment1 = request()->segment(1);
        $segment2 = request()->segment(2);
    @endphp
    
    <div class="sidebar-content">
        <div class="nav-container">
            <nav id="main-menu-navigation" class="navigation-main">
                <div class="nav-item {{ ($segment1 == 'dashboard') ? 'active' : '' }}">
                    <a href="{{url('dashboard')}}"><i class="ik ik-bar-chart-2"></i><span>{{ __('Tableau de Bord')}}</span></a>
                </div>
                @can('manage_config')
                    <div class="nav-item {{ ($segment1 == 'roles'||$segment1 == 'permission' ||$segment1 == 'user') ? 'active open' : '' }} has-sub">
                        <a href="#"><i class="ik ik-settings"></i><span>{{ __('Administration')}}</span></a>
                        <div class="submenu-content">
                            <!-- only those have manage_role permission will get access -->
                            @can('manage_roles')
                            <a href="{{url('roles')}}" class="menu-item {{ ($segment1 == 'roles') ? 'active' : '' }}">{{ __('Roles')}}</a>
                            @endcan
                            <!-- only those have manage_permission permission will get access -->
                            @can('manage_permission')
                            <a href="{{url('permission')}}" class="menu-item {{ ($segment1 == 'permission') ? 'active' : '' }}">{{ __('Permission')}}</a>
                            @endcan
                            @can('manage_user')
                                <a href="{{url('users')}}" class="menu-item {{ ($segment1 == 'users') ? 'active' : '' }}">{{ __('Admin')}}</a>
                                <a href="{{url('user/create')}}" class="menu-item {{ ($segment1 == 'user' && $segment2 == 'create') ? 'active' : '' }}">{{ __('Add Admin')}}</a>
                            @endcan
                        </div>
                    </div>
                 @endcan
                @can('manage_categories')
                   <div class="nav-item {{ ($segment1 == 'categories') ? 'active' : '' }}">
                        <a href="{{url('categories')}}"><i class="fas fa-tags"></i><span>{{ __('Categories Actualités')}}</span> </a>
                    </div>
                @endcan
                @can('manage_forfaits')
                   <div class="nav-item {{ ($segment1 == 'forfaits') ? 'active' : '' }}">
                        <a href="{{url('forfaits')}}"><i class="fas fa-tags"></i><span>{{ __('Forfait')}}</span> </a>
                    </div>
                @endcan
                @can('manage_statuts')
                   <div class="nav-item {{ ($segment1 == 'statuts') ? 'active' : '' }}">
                        <a href="{{url('statuts')}}"><i class="fas fa-magic"></i><span>{{ __('Statuts')}}</span> </a>
                    </div>
                @endcan
                @can('manage_sliders')
                   <div class="nav-item {{ ($segment1 == 'sliders') ? 'active' : '' }}">
                        <a href="{{url('sliders')}}"><i class="fas fa-file"></i><span>{{ __('Gestion des Sliders')}}</span> </a>
                    </div>
                @endcan
                @can('manage_services')
                    <div class="nav-item {{ ($segment1 == 'services') ? 'active' : '' }}">
                        <a href="{{url('services')}}"  class="menu-item {{ ($segment1 == 'services') ? 'active' : '' }}"><i class="fas fa-tags"></i><span>{{ __('Services')}}</span></a>
                    </div>
                @endcan
                @can('manage_temoignages')
                    <div class="nav-item {{ ($segment1 == 'temoignages') ? 'active' : '' }}">
                        <a href="{{url('temoignages')}}"  class="menu-item {{ ($segment1 == 'temoignages') ? 'active' : '' }}"><i class="fas fa-users"></i><span>{{ __('Temoignages')}}</span></a>
                    </div>
                @endcan
                @can('manage_faqs')
                    <div class="nav-item {{ ($segment1 == 'faqs') ? 'active' : '' }}">
                        <a href="{{url('faqs')}}"  class="menu-item {{ ($segment1 == 'faqs') ? 'active' : '' }}"><i class="fa fa-info-circle"></i><span>{{ __('Faqs')}}</span></a>
                    </div>
                @endcan
                @can('manage_actualites')
                    <div class="nav-item {{ ($segment1 == 'actualites') ? 'active' : '' }}">
                        <a href="{{url('actualites')}}"  class="menu-item {{ ($segment1 == 'actualites') ? 'active' : '' }}"><i class="fas fa-comments"></i><span>{{ __('Actualités')}}</span></a>
                    </div>
                @endcan
                @can('manage_partenaires')
                    <div class="nav-item {{ ($segment1 == 'partenaires') ? 'active' : '' }}">
                        <a href="{{url('partenaires')}}"  class="menu-item {{ ($segment1 == 'partenaires') ? 'active' : '' }}"><i class="fas fa-user-cog"></i><span>{{ __('Partenaires')}}</span></a>
                    </div>
                @endcan
                @can('manage_galerie_images')
                   <div class="nav-item {{ ($segment1 == 'categories') ? 'active' : '' }}">
                        <a href="{{url('categories-images')}}"><i class="fas fa-file"></i><span>{{ __('Categories Images')}}</span> </a>
                    </div>
                @endcan
                @can('manage_galerie_images')
                   <div class="nav-item {{ ($segment1 == 'categories') ? 'active' : '' }}">
                        <a href="{{url('galerie-video')}}"><i class="fas fa-file"></i><span>{{ __('Galerie Video')}}</span> </a>
                    </div>
                @endcan
                @can('manage_userLOL')
                   <div class="nav-item {{ ($segment1 == 'users') ? 'active' : '' }}">
                        <a href="{{url('list-users')}}"><i class="fas fa-user"></i><span>{{ __('Gestion des Utilisateurs')}}</span> </a>
                    </div>
                @endcan
                @can('manage_rendez_vous')
                   <div class="nav-item {{ ($segment1 == 'rendez-vous') ? 'active' : '' }}">
                        <a href="{{url('rendez-vous')}}"><i class="fas fa-calendar"></i><span>{{ __('Gestion des Rendez-vous')}}</span> </a>
                    </div>
                @endcan
                @can('manage_parrainages')
                   <div class="nav-item {{ ($segment1 == 'parrainages') ? 'active' : '' }}">
                        <a href="{{url('parrainages')}}"><i class="fas fa-users"></i><span>{{ __('Gestion des Parrainages')}}</span> </a>
                    </div>
                @endcan
                @can('manage_documentsvoyage')
                   <div class="nav-item {{ ($segment1 == 'documents-voyage') ? 'active' : '' }}">
                        <a href="{{url('documents-voyage')}}"><i class="fas fa-folder"></i><span>{{ __('Gestion des documents voyage ')}}</span> </a>
                    </div>
                @endcan
                @can('manage_reservation_achat')
                   <div class="nav-item {{ ($segment1 == 'reservation-achat') ? 'active' : '' }}">
                        <a href="{{url('reservation-achat')}}"><i class="fa fa-file"></i><span>{{ __('Gestion des réservations achats ')}}</span> </a>
                    </div>
                @endcan
                @can('manage_profil_visa')
                   <div class="nav-item {{ ($segment1 == 'profil-visa') ? 'active' : '' }}">
                        <a href="{{url('profil-visa')}}"><i class="fas fa-users"></i><span>{{ __('Gestion des demandes de visa')}}</span> </a>
                    </div>
                @endcan

            </nav>   
                
        </div>
    </div>
</div>