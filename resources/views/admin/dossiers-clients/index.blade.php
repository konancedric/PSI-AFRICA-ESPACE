@extends('layouts.main')
@section('title', 'Gestion des Dossiers Clients')
@section('content')

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="fas fa-folder-open bg-blue"></i>
                    <div class="d-inline">
                        <h5>{{ __('Gestion des Dossiers Clients')}}</h5>
                        <span>{{ __('Visualisation et gestion des documents clients')}}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <nav class="breadcrumb-container" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{url('dashboard')}}"><i class="ik ik-home"></i></a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Dossiers Clients')}}</li>
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

    {{-- Statistiques --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white profil-visa-stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['total_dossiers'] }}</h4>
                    <p class="mb-0">Total Dossiers</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white profil-visa-stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['dossiers_clients'] }}</h4>
                    <p class="mb-0">Envoyés par clients</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white profil-visa-stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['dossiers_admin'] }}</h4>
                    <p class="mb-0">Envoyés par admin</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white profil-visa-stat-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['dossiers_pending'] }}</h4>
                    <p class="mb-0">En attente</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-filter"></i> Filtres de recherche</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.dossiers.index') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="client_id">Client</label>
                                    <select name="client_id" id="client_id" class="form-control">
                                        <option value="">Tous les clients</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }} ({{ $client->email ?? $client->contact }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="type">Type</label>
                                    <select name="type" id="type" class="form-control">
                                        <option value="">Tous les types</option>
                                        <option value="client_to_admin" {{ request('type') == 'client_to_admin' ? 'selected' : '' }}>
                                            Envoyés par client
                                        </option>
                                        <option value="admin_to_client" {{ request('type') == 'admin_to_client' ? 'selected' : '' }}>
                                            Envoyés par admin
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Statut</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Tous les statuts</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                        <option value="viewed" {{ request('status') == 'viewed' ? 'selected' : '' }}>Vu</option>
                                        <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Traité</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i> Filtrer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Liste des dossiers --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card profil-visa-main-card">
                <div class="card-header bg-dark text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>LISTE DES DOSSIERS ({{ $dossiers->total() }})</span>
                        <div>
                            <button class="btn btn-light btn-sm profil-visa-action-btn" onclick="window.location.reload()">
                                <i class="fa fa-refresh"></i> Actualiser
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($dossiers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-user"></i> Client</th>
                                        <th><i class="fas fa-file"></i> Fichier</th>
                                        <th><i class="fas fa-weight"></i> Taille</th>
                                        <th><i class="fas fa-arrow-right"></i> Type</th>
                                        <th><i class="fas fa-calendar"></i> Date</th>
                                        <th class="text-center"><i class="fas fa-tag"></i> Statut</th>
                                        <th class="text-center"><i class="fas fa-cogs"></i> Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dossiers as $dossier)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.dossiers.client', $dossier->user_id) }}">
                                                    {{ $dossier->user->name }}
                                                </a>
                                                <br>
                                                <small class="text-muted">{{ $dossier->user->email ?? $dossier->user->contact }}</small>
                                            </td>
                                            <td>
                                                <i class="fas fa-file-{{ $dossier->file_icon }} text-primary"></i>
                                                {{ $dossier->original_name }}
                                                @if($dossier->description)
                                                    <br><small class="text-muted">{{ Str::limit($dossier->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $dossier->formatted_size }}</td>
                                            <td>
                                                @if($dossier->isFromClient())
                                                    <span class="badge badge-info">
                                                        <i class="fas fa-arrow-up"></i> Client
                                                    </span>
                                                @else
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-arrow-down"></i> Admin
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $dossier->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="text-center">{!! $dossier->status_badge !!}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.dossiers.download', $dossier->id) }}"
                                                   class="btn btn-sm btn-primary profil-visa-action-btn"
                                                   title="Télécharger">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @if($dossier->isFromClient() && $dossier->status !== 'processed')
                                                    <form action="{{ route('admin.dossiers.mark-processed', $dossier->id) }}"
                                                          method="POST"
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit"
                                                                class="btn btn-sm btn-success profil-visa-action-btn"
                                                                title="Marquer comme traité">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($dossier->isFromAdmin())
                                                    <form action="{{ route('admin.dossiers.delete', $dossier->id) }}"
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
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-center mt-3">
                            {{ $dossiers->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="profil-visa-empty-state">
                            <i class="fas fa-folder-open"></i>
                            <h5>Aucun dossier</h5>
                            <p>Aucun dossier ne correspond aux critères de recherche.</p>
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
    // Auto-hide alerts après 5 secondes
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endpush

@endsection
