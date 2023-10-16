@extends('layouts.main')
@section('title', 'Grade')
@section('content')
    <!-- push external head elements to head -->
    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="fas fa-award bg-orange"></i>
                        <div class="d-inline">
                            <h5>{{ __('Grades')}}</h5>
                            <span>{{ __('Gestion des Grades')}}</span>
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
                                <a href="#">{{ __('Grades')}}</a>
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
            <!-- only those have manage_grades grades will get access -->
            @can('manage_grades')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h3>Ajout d'un Grade  </h3></div>
                    <div class="card-body">
                        @include('admin.grades.form-new-grades')
                    </div>
                </div>
            </div>
            @endcan
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card p-3">
                    <div class="card-body">
                        @include('admin.grades.list-grades')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('js/grades.js') }}"></script>
    @endpush
@endsection
