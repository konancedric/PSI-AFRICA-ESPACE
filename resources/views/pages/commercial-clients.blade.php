@extends('layouts.main')

@section('title', 'Gestion des Clients - Commercial')

@section('content')
<div class="container-fluid">
    
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-users text-success me-2"></i>
                        Gestion des Clients
                    </h1>
                    <p class="text-muted mb-0">Gérez et suivez vos clients</p>
                </div>
                <div>
                    <a href="{{ url('/commercial/dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Retour au Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary">{{ $clients->total() ?? 0 }}</h3>
                    <p class="text-muted mb-0">Total Clients</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success">{{ $clients->where('created_at', '>=', now()->subDays(30))->count() ?? 0 }}</h3>
                    <p class="text-muted mb-0">Nouveaux ce mois</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-info">{{ $clients->whereNotNull('email_verified_at')->count() ?? 0 }}</h3>
                    <p class="text-muted mb-0">Clients vérifiés</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning">{{ $clients->where('etat', 1)->count() ?? 0 }}</h3>
                    <p class="text-muted mb-0">Clients actifs</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des clients -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>
                        Liste des Clients
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($clients) && $clients->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Contact</th>
                                        <th>Demandes</th>
                                        <th>Date inscription</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($clients as $client)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                    {{ substr($client->name, 0, 1) }}
                                                </div>
                                                <strong>{{ $client->name }}</strong>
                                            </div>
                                        </td>
                                        <td>{{ $client->email }}</td>
                                        <td>{{ $client->contact ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $client->profilsVisa->count() ?? 0 }} demande(s)
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $client->created_at->format('d/m/Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($client->etat == 1)
                                                <span class="badge bg-success">Actif</span>
                                            @else
                                                <span class="badge bg-danger">Inactif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-info" title="Voir détails">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-primary" title="Contacter">
                                                    <i class="fas fa-envelope"></i>
                                                </button>
                                                @if($client->profilsVisa->count() > 0)
                                                    <a href="{{ url('/profil-visa?user_id=' . $client->id) }}" 
                                                       class="btn btn-outline-success" title="Voir demandes">
                                                        <i class="fas fa-file-alt"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if(method_exists($clients, 'links'))
                            <div class="d-flex justify-content-center mt-3">
                                {{ $clients->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun client trouvé</h5>
                            <p class="text-muted">Les clients s'inscriront via le site web.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 0.875rem;
    font-weight: 600;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-radius: 0.5rem;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>
@endsection