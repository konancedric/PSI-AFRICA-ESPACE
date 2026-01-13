@extends('layouts.main')
@section('title', 'Service')
@section('content')
    <!-- push external head elements to head -->
    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-ervicems-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="fa fa-tags bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Services')}}</h5>
                            <span>{{ __('Gestion des Services')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-ervicem">
                                <a href="{{url('dashboard')}}"><i class="ik ik-tag"></i></a>
                            </li>
                            <li class="breadcrumb-ervicem">
                                <a href="#">{{ __('Services')}}</a>
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
            <!-- only those have manage_services services will get access -->
            @can('manage_services')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"> @include('admin.services.form-new-services')</div>
                    <div class="card-header bg-dark text-white">LISTE DES SERVICES</div>
                    <div class="card-body">
                       @include('admin.services.list-services')
                    </div>
                </div>
            </div>
            @endcan
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('js/services.js') }}"></script>
    @endpush
@endsection
