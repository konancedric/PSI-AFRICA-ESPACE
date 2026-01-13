<!doctype html>
<html class="no-js" lang="fr">
    <head>
        <title>Créer un compte | {{ config('app.name') }}</title>
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
                            <p>Créer votre compte - {{ config('app.name') }}</p>

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
                                    Créez un compte pour soumettre votre demande de profil visa.
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('register.post') }}">
                                @csrf

                                <div class="form-group">
                                    <input id="prenom"
                                           type="text"
                                           placeholder="Prénom"
                                           class="form-control @error('prenom') is-invalid @enderror"
                                           name="prenom"
                                           value="{{ old('prenom') }}"
                                           required
                                           autofocus>
                                    <i class="ik ik-user"></i>
                                    @error('prenom')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <input id="nom"
                                           type="text"
                                           placeholder="Nom"
                                           class="form-control @error('nom') is-invalid @enderror"
                                           name="nom"
                                           value="{{ old('nom') }}"
                                           required>
                                    <i class="ik ik-user"></i>
                                    @error('nom')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="contact" class="form-label">Numéro de téléphone</label>
                                    <input id="contact"
                                           type="tel"
                                           class="form-control @error('contact') is-invalid @enderror"
                                           name="contact"
                                           value="{{ old('contact') }}">
                                    <!-- Champs cachés pour le numéro complet au format E.164 -->
                                    <input type="hidden" id="contact_full" name="contact_full">
                                    <input type="hidden" id="contact_country" name="contact_country">

                                    @error('contact')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="ik ik-info"></i> Sélectionnez votre pays et entrez votre numéro
                                    </small>
                                </div>

                                <div class="form-group">
                                    <input id="email"
                                           type="email"
                                           placeholder="Adresse email (optionnel)"
                                           class="form-control @error('email') is-invalid @enderror"
                                           name="email"
                                           value="{{ old('email') }}">
                                    <i class="ik ik-mail"></i>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <input id="password"
                                           type="password"
                                           placeholder="Mot de passe"
                                           class="form-control @error('password') is-invalid @enderror"
                                           name="password"
                                           required
                                           autocomplete="new-password">
                                    <i class="ik ik-lock"></i>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Le mot de passe doit contenir au moins 8 caractères
                                    </small>
                                </div>

                                <div class="form-group">
                                    <input id="password-confirm"
                                           type="password"
                                           placeholder="Confirmer le mot de passe"
                                           class="form-control"
                                           name="password_confirmation"
                                           required
                                           autocomplete="new-password">
                                    <i class="ik ik-lock"></i>
                                </div>

                                <div class="form-group">
                                    <label class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="terms" name="terms" required>
                                        <span class="custom-control-label">
                                            &nbsp;J'accepte les <a href="#" target="_blank">conditions d'utilisation</a>
                                        </span>
                                    </label>
                                </div>

                                <div class="sign-btn text-center">
                                    <button type="submit" class="btn btn-custom bg-warning">
                                        <i class="fas fa-user-plus"></i> Créer mon compte
                                    </button>
                                </div>
                            </form>

                            <div class="register">
                                <p>{{ __('Vous avez déjà un compte ?')}}
                                    <a href="{{route('login')}}">{{ __('Se connecter')}}</a>
                                </p>
                                <p class="mt-2">
                                    <small>{{ __('Vous êtes une entreprise ?')}}
                                        <a href="{{url('register/pro')}}">{{ __('Inscription professionnelle')}}</a>
                                    </small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.layouts-js')

        <script>
            $(document).ready(function() {
                // Initialisation IntlTelInput
                const phoneInput = document.querySelector("#contact");
                if (phoneInput) {
                    const iti = window.intlTelInput(phoneInput, {
                        // Pays par défaut (Côte d'Ivoire)
                        initialCountry: "ci",
                        // Pays préférés en haut de la liste
                        preferredCountries: ["ci", "fr", "sn", "ml", "bf", "bj", "tg", "ne"],
                        // Séparer les pays préférés
                        separateDialCode: true,
                        // Format national
                        nationalMode: true,
                        // Auto-placeholder
                        autoPlaceholder: "aggressive",
                        // Utiliser le formatage complet
                        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.0/build/js/utils.js"
                    });

                    // Validation en temps réel
                    phoneInput.addEventListener('blur', function() {
                        if (phoneInput.value.trim()) {
                            if (iti.isValidNumber()) {
                                phoneInput.classList.remove('is-invalid');
                                phoneInput.classList.add('is-valid');

                                // Stocker le numéro complet au format E.164
                                document.getElementById('contact_full').value = iti.getNumber();
                                document.getElementById('contact_country').value = iti.getSelectedCountryData().iso2;
                            } else {
                                phoneInput.classList.remove('is-valid');
                                phoneInput.classList.add('is-invalid');
                            }
                        }
                    });

                    // Soumettre le numéro complet avec le formulaire
                    $('form').on('submit', function() {
                        if (phoneInput.value.trim()) {
                            // Remplacer le champ contact par le numéro au format E.164
                            phoneInput.value = iti.getNumber();
                        }
                    });
                }

                // Validation du formulaire (existante)
                $('form').on('submit', function(e) {
                    var password = $('#password').val();
                    var confirmPassword = $('#password-confirm').val();

                    if (password !== confirmPassword) {
                        e.preventDefault();
                        alert('Les mots de passe ne correspondent pas');
                        return false;
                    }

                    if (password.length < 8) {
                        e.preventDefault();
                        alert('Le mot de passe doit contenir au moins 8 caractères');
                        return false;
                    }

                    if (!$('#terms').is(':checked')) {
                        e.preventDefault();
                        alert('Vous devez accepter les conditions d\'utilisation');
                        return false;
                    }
                });
            });
        </script>
    </body>
</html>
