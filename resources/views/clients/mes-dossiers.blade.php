@extends('layouts.main')
@section('title', 'J\'envoie mes dossiers')
@section('content')

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="fas fa-folder-open bg-blue"></i>
                    <div class="d-inline">
                        <h5>{{ __('J\'envoie mes dossiers')}}</h5>
                        <span>{{ __('Upload et gestion de vos documents')}}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <nav class="breadcrumb-container" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{url('dashboard')}}"><i class="ik ik-home"></i></a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Mes Dossiers')}}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    {{-- Messages d'alerte --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        {{-- Formulaire d'upload --}}
        <div class="col-md-12 mb-4">
            <div class="card profil-visa-main-card">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-upload"></i> Envoyer de nouveaux documents
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('mes-dossiers.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="fichiers" class="form-label">
                                        <i class="fas fa-paperclip"></i> Sélectionner les fichiers
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="custom-file">
                                        <input type="file"
                                               class="custom-file-input @error('fichiers') is-invalid @enderror"
                                               id="fichiers"
                                               name="fichiers[]"
                                               multiple
                                               required
                                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip,.rar">
                                        <label class="custom-file-label" for="fichiers">Choisir les fichiers...</label>
                                    </div>
                                    @error('fichiers')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        Formats acceptés: PDF, Word (DOC, DOCX), Images (JPG, PNG), Archives (ZIP, RAR).
                                        Taille maximum: 10 MB par fichier.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description" class="form-label">
                                        <i class="fas fa-comment"></i> Description (optionnel)
                                    </label>
                                    <textarea class="form-control"
                                              id="description"
                                              name="description"
                                              rows="3"
                                              placeholder="Ajoutez une description pour vos documents..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-success profil-visa-action-btn">
                                    <i class="fas fa-cloud-upload-alt"></i> Envoyer les fichiers
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Liste des fichiers Envoyés --}}
        <div class="col-md-12">
            <div class="card profil-visa-main-card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-archive"></i> Mes documents Envoyés ({{ count($files) }})
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($files) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-file"></i> Nom du fichier</th>
                                        <th><i class="fas fa-weight"></i> Taille</th>
                                        <th><i class="fas fa-calendar"></i> Date d'upload</th>
                                        <th class="text-center"><i class="fas fa-tag"></i> Statut</th>
                                        <th class="text-center"><i class="fas fa-cogs"></i> Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($files as $file)
                                        <tr>
                                            <td>
                                                <i class="fas fa-file-{{ $file['file_icon'] }} text-primary"></i>
                                                {{ $file['name'] }}
                                                @if($file['is_from_admin'])
                                                    <span class="badge badge-info ml-2">
                                                        <i class="fas fa-user-shield"></i> De l'admin
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $file['formatted_size'] }}</td>
                                            <td>{{ $file['date'] }}</td>
                                            <td class="text-center">
                                                {!! $file['status_badge'] !!}
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('mes-dossiers.download', $file['id']) }}"
                                                   class="btn btn-sm btn-primary profil-visa-action-btn"
                                                   title="Télécharger">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @if($file['is_from_client'])
                                                    <form action="{{ route('mes-dossiers.delete', $file['id']) }}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce fichier ?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-sm btn-danger profil-visa-action-btn"
                                                                title="Supprimer">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-sm btn-secondary profil-visa-action-btn"
                                                            title="Document admin - Non supprimable"
                                                            disabled>
                                                        <i class="fas fa-lock"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="profil-visa-empty-state">
                            <i class="fas fa-folder-open"></i>
                            <h5>Aucun document Envoyés</h5>
                            <p>Vous n'avez pas encore Envoyés de documents. Utilisez le formulaire ci-dessus pour envoyer vos fichiers.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
$(document).ready(function() {
    // Afficher les noms des fichiers sélectionnés
    $('#fichiers').on('change', function() {
        var files = $(this)[0].files;
        var fileNames = [];

        for (var i = 0; i < files.length; i++) {
            fileNames.push(files[i].name);
        }

        if (fileNames.length > 0) {
            $(this).next('.custom-file-label').html(fileNames.length + ' fichier(s) sélectionné(s)');
        } else {
            $(this).next('.custom-file-label').html('Choisir les fichiers...');
        }
    });

    // Auto-hide alerts après 5 secondes
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endpush

@endsection
