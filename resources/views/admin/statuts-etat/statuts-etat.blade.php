@extends('layouts.main')
@section('title', 'StatutsEtat')
@section('content')
    <!-- push external head elements to head -->
    @push('include.layout-css')
    
    @endpush
    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <div class="d-inline">
                            <h5><i class="fa fa-tags bg-blue"></i> {{ __('StatutsEtat')}}</h5>
                            <span>{{ __('Gestion des StatutsEtat')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{url('dashboard')}}"><i class="fa fa-tags"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">{{ __('StatutsEtat')}}</a>
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
            <!-- only those have manage_statuts_etat statuts will get access -->
            @can('manage_statuts_etat')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h3>Ajout d'une StatutsEtat  </h3></div>
                    <div class="card-body">
                        @include('admin.statuts-etat.form-new-statuts-etat')
                    </div>
                </div>
            </div>
            @endcan
        </div>
        <div class="row">
            <div class="col-md-12 mt-2">
                <div class="card p-3">
                    <div class="card-body">
                        @include('admin.statuts-etat.list-statuts-etat')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        
    @endpush
@endsection
