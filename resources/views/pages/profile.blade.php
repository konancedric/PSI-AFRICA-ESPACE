@extends('layouts.main') 
@section('title', 'Profile')
@section('content')
    <!-- push external head elements to head -->
    @push('head')

        <link rel="stylesheet" href="{{ asset('plugins/weather-icons/css/weather-icons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.theme.default.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/chartist/dist/chartist.min.css') }}">
    @endpush
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-4 col-md-5">
            <div class="card">
                <div class="card-body">
                    <div class="text-center"> 
                        <img src="../assets/img/avatars/1.png" class="rounded-circle" width="150" />
                        <h4 class="card-title mt-10">{{ Auth::user()->name}}</h4>
                        <p class="card-subtitle">{{Auth::user()->email}}</p>
                    </div>
                </div>
                <hr class="mb-0"> 
            </div>
        </div>
        <div class="col-lg-8 col-md-7">
            <div class="card">
                <!-- start message area-->
                @include('include.message')
                <!-- end message area-->
                <ul class="nav nav-pills custom-pills" id="pills-tab" role="tablist">
                    
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-setting-tab" data-toggle="pill" href="#previous-month" role="tab" aria-controls="pills-setting" aria-selected="false">{{ __('Mettre à jour le mot de passe')}}</a>
                    </li>
                </ul>
                <div class="tab-content" id="pills-tabContent">
                    
                    <div class="tab-pane fade show active" id="previous-month" role="tabpanel" aria-labelledby="pills-setting-tab">
                        <div class="card-body">
                            <form class="form-horizontal" method="post" action="{{url('user/update-mdp')}}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" value="{{ Auth::user()->id}}" />
                                <div class="form-group">
                                    <input type="password" class="form-control" placeholder="Mot de passe" name="password" required>
                                    <i class="ik ik-lock"></i>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" placeholder="Confirmer le mot de passe" name="password_confirmation" required>
                                    <i class="ik ik-eye-off"></i>
                                    @error('password_confirmation')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <button class="btn btn-primary" type="submit"><i class="fa fa-check-circle"></i> Mettre à jour</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div> 
  <!-- push external js -->
    @push('script')
        <script src="{{ asset('plugins/owl.carousel/dist/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('plugins/chartist/dist/chartist.min.js') }}"></script>
        <script src="{{ asset('plugins/flot-charts/jquery.flot.js') }}"></script>
        <!-- <script src="{{ asset('plugins/flot-charts/jquery.flot.categories.js') }}"></script> -->
        <script src="{{ asset('plugins/flot-charts/curvedLines.js') }}"></script>
        <script src="{{ asset('plugins/flot-charts/jquery.flot.tooltip.min.js') }}"></script>

        <script src="{{ asset('plugins/amcharts/amcharts.js') }}"></script>
        <script src="{{ asset('plugins/amcharts/serial.js') }}"></script>
        <script src="{{ asset('plugins/amcharts/themes/light.js') }}"></script>
       
        
        <script src="{{ asset('js/widget-statistic.js') }}"></script>
        <script src="{{ asset('js/widget-data.js') }}"></script>
        <script src="{{ asset('js/dashboard-charts.js') }}"></script>
        
    @endpush
@endsection