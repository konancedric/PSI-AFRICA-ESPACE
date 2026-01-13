@extends('layouts.main')
@section('title', 'Ville')
@section('content')
    @foreach ($dataVilles as $tabVilles)
        <!-- push external head elements to head -->
        <div class="container-fluid">
        	<div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="fas fa-map bg-orange"></i>
                            <div class="d-inline">
                                <h5>{{ __('Villes')}}</h5>
                                <span>{{ __('Gestion de la ville de ')}} {{ $tabVilles->libelle }}</span>
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
                                    <a href="#">{{ __('Ville de ')}} {{ $tabVilles->libelle }}</a>
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="row clearfix">
                <!-- start message area-->
                @include('include.message')
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card p-3">
                        <div class="card-body">
                           @include('admin.villes.form-historique-villes')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('js/villes.js') }}"></script>
    @endpush
@endsection
