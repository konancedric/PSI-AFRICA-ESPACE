<!doctype html>
<html class="no-js" lang="fr">
    <head>
        <title> Prendre un RDV dans une entreprise ou structure | {{ config('app.name') }}</title>
        <meta name="description" content="">
        <meta name="keywords" content="">
        @include('layouts.layouts-css')
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        <div class="auth-wrapper">
            <div class="container-fluid h-100">
                <div class="row flex-row h-100 bg-white">
                    <div class="col-md-4 p-0 d-md-block d-lg-block d-sm-none d-none">
                        <div class="lavalite-bg">
                            <div class="lavalite-overlay"></div>
                        </div>
                    </div>
                    <div class="col-md-8 my-auto p-0">
                        <div class="authentication-form mx-auto" style="width:100%;">
                            <!-- start message area-->
                            @include('include.message-register')
                            <!-- end message area-->
                            <div class="logo-centered">
                                <a href="/"><img width="150" src="{{ asset('img/logo.png') }}" alt=""></a>
                            </div>
                            <p class="text-center text-white bg-primary">CHOISIR UNE STRUCTURE POUR PRENDRE UN RDV SUR - {{ config('app.name') }}</p>
                            <div class="col-md-12 row">
                                @foreach($dataEntreprises as $dataEntrepriseSearch)
                                    <div class="col-md-4 card">
                                        <div class="card-header bg-{{ $dataEntrepriseSearch->bg_color}} text-white text-center">
                                            <div class="text-center"> 
                                            <img src="/upload/entreprise/{{$dataEntrepriseSearch->logo_ent}}" class="rounded-circle" style="max-height: 100px; min-height: 100px; width:70%; text-align: center;" align="center"/>
                                                <h4 class="card-title text-center">{{ $dataEntrepriseSearch->denomination}}</h4>
                                                <p class="card-subtitle text-center text-white">@<?=$dataEntrepriseSearch->username?></p>
                                            </div>
                                        </div>
                                        <hr class="mb-0 mt-0"> 
                                        <div class="card-body"> 
                                            <small class="text-muted d-block">{{ __('Email')}} </small>
                                            <h6>{{ $dataEntrepriseSearch->emailent}}</h6> 
                                            <small class="text-muted d-block pt-10">{{ __('Contact')}}</small>
                                            <h6>{{ $dataEntrepriseSearch->contact}}</h6> 
                                            <small class="text-muted d-block pt-10">{{ __('Adresse')}}</small>
                                            <h6>{{ $dataEntrepriseSearch->adresse}}</h6>
                                            <small class="text-muted d-block pt-30">{{ __('Réseaux sociaux')}}</small>
                                            <br/>
                                            <a class="btn btn-icon btn-primary text-white" target="_blank" href="{{ $dataEntrepriseSearch->link_siteweb}}"><i class="fa fa-globe"></i></a>
                                            <a class="btn btn-icon btn-facebook text-white" target="_blank" href="{{ $dataEntrepriseSearch->link_facebook}}"><i class="fab fa-facebook-f"></i></a>
                                            <a class="btn btn-icon btn-twitter text-white" target="_blank" href="{{ $dataEntrepriseSearch->link_twitter}}"><i class="fab fa-twitter"></i></a>
                                            <a class="btn btn-icon btn-linkedin text-white" target="_blank" href="{{ $dataEntrepriseSearch->link_linkedin}}"><i class="fab fa-linkedin"></i></a>
                                        </div>
                                        <hr class="mb-0 mt-0"> 
                                        <div class="card-footer text-white text-center">
                                            <div class="text-center"> 
                                                <a href="compagny/{{ $dataEntrepriseSearch->username}}" class="btn  bg-{{ $dataEntrepriseSearch->bg_color}} text-white"><i class="fa fa-check-circle"></i> Prendre un rdv à @<?=$dataEntrepriseSearch->username?></a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="register">
                                <p>{{ __('Vous avez un compte ?')}} <a href="{{url('login')}}">{{ __('Se Connecter !')}}</a> | Vous n'avez pas de compte ? <a href="{{url('register')}}">S'inscrire</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.layouts-js')
    </body>
</html>

