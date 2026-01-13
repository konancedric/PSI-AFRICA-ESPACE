@extends('layouts.main')
@section('title', 'Nouvelle Demande de Profil Visa')
@section('content')

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="fa fa-plus-circle bg-blue"></i>
                    <div class="d-inline">
                        <h5>{{ __('Nouvelle Demande de Profil Visa')}}</h5>
                        <span>{{ __('Remplissez le formulaire ci-dessous pour soumettre votre demande')}}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <nav class="breadcrumb-container" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{route('dashboard')}}"><i class="fa fa-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{route('profil.visa.index')}}">{{ __('Profil Visa')}}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('Nouvelle Demande')}}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <!-- Messages d'alerte -->
        @include('include.message')

        <!-- Message de bienvenue -->
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fa fa-info-circle"></i>
                <strong>Bienvenue {{ Auth::user()->name }}!</strong>
                Vous êtes sur le point de créer une nouvelle demande de profil visa.
                Veuillez sélectionner le type de visa et indiquer le motif de votre voyage.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>

        <!-- Formulaire de création -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>{{ __('Informations de la demande')}}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('profil.visa.store') }}" method="POST" id="form-new-profil-visa">
                        @csrf

                        <div class="row">
                            <!-- Type de profil visa -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type_profil_visa" class="control-label">
                                        {{ __('Type de Visa')}} <span class="text-danger">*</span>
                                    </label>
                                    <select name="type_profil_visa" id="type_profil_visa" class="form-control" required>
                                        <option value="">-- Sélectionnez le type de visa --</option>
                                        @foreach($typesProfilVisa as $key => $type)
                                            <option value="{{ $key }}" {{ old('type_profil_visa') == $key ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type_profil_visa')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Motif du voyage -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="motif_voyage" class="control-label">
                                        {{ __('Motif du voyage')}} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="motif_voyage"
                                           id="motif_voyage"
                                           class="form-control"
                                           placeholder="Ex: Vacances en famille, Réunion d'affaires, etc."
                                           value="{{ old('motif_voyage') }}"
                                           required>
                                    @error('motif_voyage')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description" class="control-label">
                                        {{ __('Description / Informations complémentaires')}}
                                    </label>
                                    <textarea name="description"
                                              id="description"
                                              class="form-control"
                                              rows="4"
                                              placeholder="Ajoutez des informations supplémentaires sur votre voyage (optionnel)">{{ old('description') }}</textarea>
                                    <small class="form-text text-muted">
                                        Cette information nous aidera à mieux traiter votre demande
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="form-group text-right">
                                    <a href="{{ route('profil.visa.index') }}" class="btn btn-secondary">
                                        <i class="fa fa-times"></i> {{ __('Annuler')}}
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="btn-submit">
                                        <i class="fa fa-save"></i> {{ __('Soumettre ma demande')}}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Carte d'aide -->
        <div class="col-md-12 mt-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h5><i class="fa fa-question-circle"></i> {{ __('Besoin d\'aide ?')}}</h5>
                </div>
                <div class="card-body">
                    <p><strong>Types de visa disponibles :</strong></p>
                    <ul>
                        <li><strong>Tourisme :</strong> Pour les voyages de loisirs et vacances</li>
                        <li><strong>Affaires :</strong> Pour les déplacements professionnels et réunions d'affaires</li>
                        <li><strong>Transit :</strong> Pour les escales et transits par le pays</li>
                        <li><strong>Étudiant :</strong> Pour les études et formations académiques</li>
                        <li><strong>Travail :</strong> Pour les emplois et missions professionnelles</li>
                        <li><strong>Famille :</strong> Pour les visites familiales et regroupement familial</li>
                        <li><strong>Autre :</strong> Pour tout autre type de visa non mentionné ci-dessus</li>
                    </ul>
                    <p class="mb-0">
                        <i class="fa fa-phone"></i> Pour toute question, n'hésitez pas à nous contacter.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Validation du formulaire
        $('#form-new-profil-visa').on('submit', function(e) {
            const typeVisa = $('#type_profil_visa').val();
            const motif = $('#motif_voyage').val();

            if (!typeVisa || !motif) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Champs requis',
                    text: 'Veuillez remplir tous les champs obligatoires',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            // Désactiver le bouton pour éviter les doubles soumissions
            $('#btn-submit').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Envoi en cours...');
        });

        // Afficher un message d'aide selon le type de visa sélectionné
        $('#type_profil_visa').on('change', function() {
            const selectedType = $(this).find('option:selected').text();
            if (selectedType && selectedType !== '-- Sélectionnez le type de visa --') {
                toastr.info('Vous avez sélectionné : ' + selectedType);
            }
        });
    });
</script>
@endsection
