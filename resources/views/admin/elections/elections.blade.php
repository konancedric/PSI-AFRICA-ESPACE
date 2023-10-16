@extends('layouts.main')
@section('title', 'Election')
@section('content')
    <!-- push external head elements to head -->
    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="fas fa-vote-yea bg-orange"></i>
                        <div class="d-inline">
                            <h5>{{ __('Elections')}}</h5>
                            <span>{{ __('Gestion des Elections')}}</span>
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
                                <a href="#">{{ __('Elections')}}</a>
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
            <!-- only those have manage_elections elections will get access -->
            @can('manage_elections')
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header"> @include('admin.elections.form-new-elections')</div>
                        <div class="card-header bg-dark text-white">LISTE DES DIFFERENTES ELECTIONS</div>
                        <div class="card-body">
                            @include('admin.elections.list-elections')
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('js/elections.js') }}"></script>
    @endpush
@endsection
