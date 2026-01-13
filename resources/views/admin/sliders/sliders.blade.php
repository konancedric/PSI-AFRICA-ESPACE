@extends('layouts.main')
@section('title', 'Slider')
@section('content')
    <!-- push external head elements to head -->
    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="fas fa-user-cog bg-orange"></i>
                        <div class="d-inline">
                            <h5>{{ __('Sliders Actualités')}}</h5>
                            <span>{{ __('Gestion des Sliders Actualités')}}</span>
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
                                <a href="#">{{ __('Sliders')}}</a>
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
            <!-- only those have manage_sliders sliders will get access -->
            @can('manage_sliders')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h3>Ajout d'un Slider  </h3></div>
                    <div class="card-body">
                        @include('admin.sliders.form-new-sliders')
                    </div>
                </div>
            </div>
            @endcan
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card p-3">
                    <div class="card-body">
                        @include('admin.sliders.list-sliders')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('js/sliders.js') }}"></script>
    @endpush
@endsection
