@extends('layouts.main')
@section('title', 'Mes Factures et Paiements')
@section('content')

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="fas fa-file-invoice-dollar bg-blue"></i>
                    <div class="d-inline">
                        <h5>{{ __('Mes Factures et Paiements')}}</h5>
                        <span>{{ __('Suivi de vos factures et historique des paiements')}}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <nav class="breadcrumb-container" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{url('dashboard')}}"><i class="ik ik-home"></i></a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Mes Factures')}}</li>
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
                    <h4>{{ $statistiques['total_factures'] }}</h4>
                    <p class="mb-0">Total Factures</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white profil-visa-stat-card" style="background: linear-gradient(135deg, #28a745, #1e7e34);">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['factures_payees'] }}</h4>
                    <p class="mb-0">Factures Payées</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white profil-visa-stat-card" style="background: linear-gradient(135deg, #fd7e14, #e8590c);">
                <div class="card-body text-center">
                    <h4>{{ $statistiques['factures_impayees'] }}</h4>
                    <p class="mb-0">Factures Impayées</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white profil-visa-stat-card" style="background: linear-gradient(135deg, #17a2b8, #138496);">
                <div class="card-body text-center">
                    <h4>{{ number_format($statistiques['total_montant'], 0, ',', ' ') }} FCFA</h4>
                    <p class="mb-0">Montant Total</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Liste des factures --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card profil-visa-main-card">
                <div class="card-header bg-dark text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>MES FACTURES ({{ $factures->count() }})</span>
                        <div>
                            <button class="btn btn-light btn-sm profil-visa-action-btn" onclick="window.location.reload()">
                                <i class="fa fa-refresh"></i> Actualiser
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($factures->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-hashtag"></i> N° Facture</th>
                                        <th><i class="fas fa-calendar"></i> Date</th>
                                        <th><i class="fas fa-info-circle"></i> Description</th>
                                        <th><i class="fas fa-money-bill-wave"></i> Montant</th>
                                        <th><i class="fas fa-tag"></i> Statut</th>
                                        <th class="text-center"><i class="fas fa-cogs"></i> Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($factures as $facture)
                                        <tr>
                                            <td><strong>{{ $facture->numero_facture }}</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($facture->date_facture)->format('d/m/Y') }}</td>
                                            <td>{{ $facture->description ?? 'Prestation de service' }}</td>
                                            <td>
                                                <strong>{{ number_format($facture->montant_total, 0, ',', ' ') }} FCFA</strong>
                                            </td>
                                            <td>
                                                @if($facture->statut_paiement === 'payé')
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle"></i> Payé
                                                    </span>
                                                @elseif($facture->statut_paiement === 'partiel')
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-clock"></i> Partiel
                                                    </span>
                                                @else
                                                    <span class="badge badge-danger">
                                                        <i class="fas fa-times-circle"></i> Impayé
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('mes-factures.show', $facture->id) }}"
                                                   class="btn btn-sm btn-info profil-visa-action-btn"
                                                   title="Voir les détails">
                                                    <i class="fas fa-eye"></i> Voir
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="profil-visa-empty-state">
                            <i class="fas fa-file-invoice"></i>
                            <h5>Aucune facture</h5>
                            <p>Vous n'avez aucune facture pour le moment. Les factures de vos prestations apparaîtront ici.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Historique des paiements --}}
    @if($paiements->count() > 0)
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card profil-visa-main-card">
                    <div class="card-header" style="background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-history"></i> Historique des paiements ({{ $paiements->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-calendar"></i> Date</th>
                                        <th><i class="fas fa-hashtag"></i> N° Facture</th>
                                        <th><i class="fas fa-money-bill-wave"></i> Montant</th>
                                        <th><i class="fas fa-credit-card"></i> Méthode</th>
                                        <th><i class="fas fa-receipt"></i> Référence</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($paiements as $paiement)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if($paiement->invoice)
                                                    <a href="{{ route('mes-factures.show', $paiement->invoice_id) }}">
                                                        {{ $paiement->invoice->numero_facture }}
                                                    </a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <strong class="text-success">
                                                    {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA
                                                </strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-primary">
                                                    {{ strtoupper($paiement->methode_paiement) }}
                                                </span>
                                            </td>
                                            <td>{{ $paiement->reference_paiement ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
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
