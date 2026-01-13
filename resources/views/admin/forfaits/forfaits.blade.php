@extends('layouts.main')
@section('title', 'Forfait')
@section('content')
    <!-- push external head elements to head -->
    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-ervicems-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="fa fa-comments bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Forfaits')}}</h5>
                            <span>{{ __('Gestion des Forfaits')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-ervicem">
                                <a href="{{url('dashboard')}}"><i class="fa fa-comments"></i></a>
                            </li>
                            <li class="breadcrumb-ervicem">
                                <a href="#">{{ __('Forfaits')}}</a>
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
            <!-- only those have manage_forfaits forfaits will get access -->
            @can('manage_forfaits')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"> @include('admin.forfaits.form-new-forfaits')</div>
                    <div class="card-header bg-dark text-white">LISTE DES FORFAITS</div>
                    <div class="card-body">
                       @include('admin.forfaits.list-forfaits')
                    </div>
                </div>
            </div>
            @endcan
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('js/forfaits.js') }}"></script>
    @endpush
@endsection
