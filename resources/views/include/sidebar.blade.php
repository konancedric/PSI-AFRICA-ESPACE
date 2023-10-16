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
                @can('manage_grades')
                   <div class="nav-item {{ ($segment1 == 'grades') ? 'active' : '' }}">
                        <a href="{{url('grades')}}"><i class="fas fa-award"></i><span>{{ __('Gestion des grades')}}</span> </a>
                    </div>
                @endcan
                @can('manage_categories')
                   <div class="nav-item {{ ($segment1 == 'categories') ? 'active' : '' }}">
                        <a href="{{url('categories')}}"><i class="fas fa-user-cog"></i><span>{{ __('Gestion des categories')}}</span> </a>
                    </div>
                @endcan
                @can('manage_user')
                   <div class="nav-item {{ ($segment1 == 'users') ? 'active' : '' }}">
                        <a href="{{url('list-users')}}"><i class="fas fa-users"></i><span>{{ __('Gestion des Utilisateurs')}}</span> </a>
                    </div>
                @endcan
                @can('manage_elections')
                   <div class="nav-item {{ ($segment1 == 'elections') ? 'active' : '' }}">
                        <a href="{{url('elections')}}"><i class="fas fa-vote-yea"></i><span>{{ __('Gestion des Elections')}}</span> </a>
                    </div>
                @endcan


                <!-- Include demo pages inside sidebar start-->
                <?php /*@include('pages.sidebar-menu')*/?>
                <!-- Include demo pages inside sidebar end-->

            </nav>   
                
        </div>
    </div>
</div>