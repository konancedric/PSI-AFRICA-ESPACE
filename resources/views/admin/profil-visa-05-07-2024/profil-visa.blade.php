@extends('layouts.main')
@section('title', 'Profil Visa')
@section('content')
    <!-- push external head elements to head -->
    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-ervicems-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="fa fa-folder bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Profil Visa')}}</h5>
                            <span>{{ __('Gestion des Profil Visa')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-ervicem">
                                <a href="{{url('dashboard')}}"><i class="fa fa-folder"></i></a>
                            </li>
                            <li class="breadcrumb-ervicem">
                                <a href="#">{{ __('Profil Visa')}}</a>
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
            <!-- only those have manage_profil-visa profil-visa will get access -->
            @can('manage_profil_visa')
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header"><?php /* @include('admin.profil-visa.form-new-profil-visa') */ ?></div>
                        <div class="card-header bg-dark text-white">LISTE DES PROFILS VISA</div>
                        <div class="card-body">
                           @include('admin.profil-visa.list-profil-visa')
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('js/profil-visa.js') }}"></script>
    @endpush
@endsection
