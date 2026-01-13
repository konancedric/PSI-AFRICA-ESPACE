@extends('layouts.main')
@section('title', 'SousCategorie')
@section('content')
    <!-- push external head elements to head -->
    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="fas fa-tags bg-orange"></i>
                        <div class="d-inline">
                            <h5>{{ __('Sous Categories')}}</h5>
                            <span>{{ __('Gestion des Sous Categories')}}</span>
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
                                <a href="#">{{ __('Sous Categories')}}</a>
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
            <!-- only those have manage_souscategories souscategories will get access -->
            @can('manage_souscategories')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h3>Ajout d'une Sous Categorie  </h3></div>
                    <div class="card-body">
                        @include('admin.souscategories.form-new-souscategories')
                    </div>
                </div>
            </div>
            @endcan
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card p-3">
                    <div class="card-body">
                        @include('admin.souscategories.list-souscategories')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('js/souscategories.js') }}"></script>
    @endpush
@endsection
