<!doctype html>
<html class="no-js" lang="fr">
    <head>
        <title>Créer un compte Pro | {{ config('app.name') }}</title>
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
                    <div class="col-xl-8 col-lg-6 col-md-5 p-0 d-md-block d-lg-block d-sm-none d-none">
                        <div class="lavalite-bg">
                            <div class="lavalite-overlay"></div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-7 my-auto p-0">
                        <div class="authentication-form mx-auto" style="width:100%;">
                            <!-- start message area-->
                            @include('include.message-register')
                            <!-- end message area-->
                            <div class="logo-centered">
                                <a href="/"><img width="150" src="{{ asset('img/logo.png') }}" alt=""></a>
                            </div>
                            <p class="text-center text-white bg-primary">CRÉER UN COMPTE PRO - {{ config('app.name') }}</p>
                             @include('auth.form-registerpro');
                            <div class="register">
                                <p>{{ __('Vous avez un compte ?')}} <a href="{{url('login')}}">{{ __('Se Connecter !')}}</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.layouts-js')
    </body>
</html>
