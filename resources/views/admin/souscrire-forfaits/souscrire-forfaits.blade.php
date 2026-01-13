@extends('layouts.main')
@section('title', 'Souscriptions Forfaits')
@section('content')
    <!-- push external head elements to head -->
    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-ervicems-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="fa fa-calendar bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Souscriptions Forfaits')}}</h5>
                            <span>{{ __('Gestion des Souscriptions Forfaits')}}</span>
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
                                <a href="#">{{ __('Souscriptions Forfaits')}}</a>
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
            <!-- only those have manage_souscrire-forfaits souscrire-forfaits will get access -->
            @can('manage_souscrire_forfaits')
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header"><?php /* @include('admin.souscrire-forfaits.form-new-souscrire-forfaits') */ ?></div>
                        <div class="card-header bg-dark text-white">LISTE DES SOUSCRIPTIONS FORFAITS</div>
                        <div class="card-body">
                           @include('admin.souscrire-forfaits.list-souscrire-forfaits')
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('js/souscrire-forfaits.js') }}"></script>
    @endpush
@endsection
