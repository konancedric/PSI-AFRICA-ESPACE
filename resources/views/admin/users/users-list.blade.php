@extends('layouts.main')
@section('title', 'Site')
@section('content')
    <!-- push external head elements to head -->
    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="fa fa-user-cog bg-orange"></i>
                        <div class="d-inline">
                            <h5>{{ __('Personnes')}}</h5>
                            <span>{{ __('Gestion des Personnes')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{url('dashboard')}}"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">{{ __('Personnes')}}</a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <!-- start message area-->
            @include('include.message')
            <!-- end message area-->
            <!-- only those have manage_sites users will get access -->
            @can('manage_user')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">  @include('admin.users.form-new-users')</div>
                    <div class="card-header bg-dark text-white">LISTE DES PERSONNES</div>
                    <div class="card-body">
                       @include('admin.users.list-users')
                    </div>
                </div>
            </div>
            @endcan
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('js/users.js') }}"></script>
    @endpush
@endsection
