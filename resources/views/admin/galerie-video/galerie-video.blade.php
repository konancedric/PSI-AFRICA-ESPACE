@extends('layouts.main')
@section('title', 'Galerie Video')
@section('content')
    <!-- push external head elements to head -->
    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="fas fa-tags bg-orange"></i>
                        <div class="d-inline">
                            <h5>{{ __('Galerie Video')}}</h5>
                            <span>{{ __('Gestion des Galerie Video')}}</span>
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
                                <a href="#">{{ __('Galerie Video')}}</a>
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
            <!-- only those have manage_galerie_images galerie will get access -->
            @can('manage_galerie_images')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h3>Ajout d'une Galerie Video  </h3></div>
                    <div class="card-body">
                        @include('admin.galerie-video.form-new-galerie-video')
                    </div>
                </div>
            </div>
            @endcan
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card p-3">
                    <div class="card-body">
                        @include('admin.galerie-video.list-galerie-video')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('js/galerie-video.js') }}"></script>
    @endpush
@endsection
