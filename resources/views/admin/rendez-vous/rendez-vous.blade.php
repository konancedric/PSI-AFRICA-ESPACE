@extends('layouts.main')
@section('title', 'Rendez vous')
@section('content')
    <!-- push external head elements to head -->
    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-ervicems-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="fa fa-calendar bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Rendez vous')}}</h5>
                            <span>{{ __('Gestion des Rendez vous')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-ervicem">
                                <a href="{{url('dashboard')}}"><i class="fa fa-calendar"></i></a>
                            </li>
                            <li class="breadcrumb-ervicem">
                                <a href="#">{{ __('Rendez vous')}}</a>
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
            <!-- only those have manage_rendez-vous rendez-vous will get access -->
            @can('manage_rendez_vous')
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header"><?php /* @include('admin.rendez-vous.form-new-rendez-vous') */ ?></div>
                        <div class="card-header bg-dark text-white">LISTE DES RENDEZ VOUS</div>
                        <div class="card-body">
                           @include('admin.rendez-vous.list-rendez-vous')
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('js/rendez-vous.js') }}"></script>
    @endpush
@endsection
