<!doctype html>
<html class="no-js" lang="fr">
    <head>
        <title>Se Connecter | {{ config('app.name') }}</title>
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
                        <div class="authentication-form mx-auto">
                            <div class="logo-centered">
                                <a href="/"><img width="150" src="{{ asset('img/logo.png') }}" alt=""></a>
                            </div>
                            <p>Se Connecter à son espace - {{ config('app.name') }}</p>

                            @if(session('info'))
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    <i class="fa fa-info-circle"></i> {{ session('info') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if(request()->has('redirect') && request('redirect') == 'profil-visa/create')
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    <i class="fa fa-passport"></i>
                                    <strong>Demande de profil visa</strong><br>
                                    Connectez-vous pour soumettre votre demande de profil visa.
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="login" class="form-label">Email ou Téléphone</label>
                                    <input id="login"
                                           type="text"
                                           placeholder="Email ou Numéro de téléphone"
                                           class="form-control @error('email') is-invalid @enderror @error('contact') is-invalid @enderror"
                                           name="login"
                                           required
                                           autocomplete="username"
                                           autofocus>
                                    <i class="ik ik-user"></i>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    @error('contact')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="ik ik-info"></i> Utilisez votre email ou votre numéro de téléphone (avec indicatif)
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="password" class="form-label">Mot de passe</label>
                                    <input id="password" type="password" placeholder="Mot de passe" class="form-control @error('password') is-invalid @enderror" name="password" value="1234" required>
                                    <i class="ik ik-lock"></i>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="row">
                                    <div class="col text-left">
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="item_checkbox" name="item_checkbox" value="option1">
                                            <span class="custom-control-label">&nbsp;Memoriser ma session</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="sign-btn text-center">
                                    <button class="btn btn-custom bg-warning"><i class="fas fa-sign-out-alt"></i> Se connecter</button>
                                </div>
                            </form>
                            <div class="register">
                                <p>{{ __('Pas de compte ?')}}
                                    <a href="{{route('register')}}">{{ __('Créer un compte')}}</a>
                                    {{ __('ou')}}
                                    <a href="{{url('register/pro')}}">{{ __('S\'inscrire en tant que professionnel')}}</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.layouts-js')
    </body>
</html>
