@extends('layouts.main')
@section('title', 'Réservations achat')
@section('content')
    <!-- push external head elements to head -->
    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-ervicems-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="fa fa-file bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Réservations achat')}}</h5>
                            <span>{{ __('Gestion des Réservations achat')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-ervicem">
                                <a href="{{url('dashboard')}}"><i class="fa fa-file"></i></a>
                            </li>
                            <li class="breadcrumb-ervicem">
                                <a href="#">{{ __('Réservations achat')}}</a>
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
            <!-- only those have manage_reservation-achat reservation-achat will get access -->
            @can('manage_reservation_achat')
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header"><?php /* @include('admin.reservation-achat.form-new-reservation-achat') */ ?></div>
                        <div class="card-header bg-dark text-white">LISTE DES RÉSERVATIONS ACHAT</div>
                        <div class="card-body">
                           @include('admin.reservation-achat.list-reservation-achat')
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('js/reservation-achat.js') }}"></script>
    @endpush
@endsection
