@extends('layouts.main')

@section('title', 'Dashboard Commercial - Modules PSI Africa')

@section('content')
<div class="container-fluid">
    
    <!-- En-tête Dashboard Modules avec Filtres -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="modules-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2.5rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3); position: relative; overflow: hidden;">
                <!-- Motifs décoratifs -->
                <div style="position: absolute; top: -50%; right: -10%; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%; transform: rotate(45deg);"></div>
                <div style="position: absolute; bottom: -30%; left: -5%; width: 150px; height: 150px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                
                <div class="d-flex justify-content-between align-items-center position-relative">
                    <div>
                        <h1 class="mb-3" style="font-weight: 800; font-size: 2.5rem; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <i class="fas fa-th-large me-3" style="color: #ffd700;"></i>
                            Dashboard Commercial - Modules
                        </h1>
                        <p class="mb-3" style="font-size: 1.2rem; opacity: 0.95; font-weight: 500;">
                            Gérez vos 5 modules principaux : VISA, FORFAITS, DOCUMENTS, RDV, RÉSERVATIONS
                        </p>
                        <div class="d-flex align-items-center flex-wrap">
                            <span class="badge bg-white text-primary px-3 py-2 me-3 mb-2" style="border-radius: 25px; font-weight: 600;">
                                <i class="fas fa-user me-1"></i> {{ Auth::user()->name }}
                            </span>
                            <span class="badge" style="background: rgba(255,255,255,0.2); color: white; border-radius: 25px; padding: 0.5rem 1rem; margin-bottom: 0.5rem;">
                                <i class="fas fa-calendar me-1"></i> {{ $filterData['period_label'] ?? 'Ce mois' }}
                            </span>
                        </div>
                    </div>
                    <div class="modules-icons d-none d-md-flex">
                        <div class="d-flex flex-column align-items-center">
                            <div style="display: flex; gap: 15px; margin-bottom: 10px;">
                                <div style="width: 45px; height: 45px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-passport" style="color: #ffd700; font-size: 1.2rem;"></i>
                                </div>
                                <div style="width: 45px; height: 45px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-tags" style="color: #ffd700; font-size: 1.2rem;"></i>
                                </div>
                                <div style="width: 45px; height: 45px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-file-text" style="color: #ffd700; font-size: 1.2rem;"></i>
                                </div>
                            </div>
                            <div style="display: flex; gap: 15px;">
                                <div style="width: 45px; height: 45px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-calendar-check" style="color: #ffd700; font-size: 1.2rem;"></i>
                                </div>
                                <div style="width: 45px; height: 45px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-plane" style="color: #ffd700; font-size: 1.2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- NOUVEAU : Panneau de Filtres par Période -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="filter-panel" style="background: white; border-radius: 20px; padding: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid rgba(102, 126, 234, 0.1);">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 style="color: #2d3436; font-weight: 700; font-size: 1.1rem; margin: 0;">
                        <i class="fas fa-filter me-2" style="color: #667eea;"></i>
                        Filtrer les statistiques par période
                    </h5>
                    <div class="filter-actions">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetFilters()" style="border-radius: 12px;">
                            <i class="fas fa-undo me-1"></i>Réinitialiser
                        </button>
                    </div>
                </div>
                
                <form id="periodFilterForm" class="row g-3">
                    <!-- Filtres prédéfinis -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Périodes prédéfinies</label>
                        <div class="period-buttons">
                            <div class="btn-group-wrap d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-period btn-sm" data-period="today">Aujourd'hui</button>
                                <button type="button" class="btn btn-period btn-sm" data-period="yesterday">Hier</button>
                                <button type="button" class="btn btn-period btn-sm" data-period="this_week">Cette semaine</button>
                                <button type="button" class="btn btn-period btn-sm" data-period="last_week">Semaine dernière</button>
                                <button type="button" class="btn btn-period btn-sm active" data-period="this_month">Ce mois</button>
                                <button type="button" class="btn btn-period btn-sm" data-period="last_month">Mois dernier</button>
                                <button type="button" class="btn btn-period btn-sm" data-period="this_quarter">Ce trimestre</button>
                                <button type="button" class="btn btn-period btn-sm" data-period="last_quarter">Trimestre dernier</button>
                                <button type="button" class="btn btn-period btn-sm" data-period="this_year">Cette année</button>
                                <button type="button" class="btn btn-period btn-sm" data-period="last_year">Année dernière</button>
                                <button type="button" class="btn btn-period btn-sm" data-period="last_30_days">30 derniers jours</button>
                                <button type="button" class="btn btn-period btn-sm" data-period="last_90_days">90 derniers jours</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Période personnalisée -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">Période personnalisée</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="date" class="form-control form-control-sm" id="customStartDate" name="start_date" 
                                       value="{{ $filterData['start_date'] ?? '' }}" style="border-radius: 12px;">
                                <small class="text-muted">Date de début</small>
                            </div>
                            <div class="col-6">
                                <input type="date" class="form-control form-control-sm" id="customEndDate" name="end_date" 
                                       value="{{ $filterData['end_date'] ?? '' }}" style="border-radius: 12px;">
                                <small class="text-muted">Date de fin</small>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-primary btn-sm" onclick="applyCustomPeriod()" style="border-radius: 12px;">
                                <i class="fas fa-calendar-alt me-1"></i>Appliquer
                            </button>
                        </div>
                    </div>
                </form>
                
                <!-- Indicateur de chargement -->
                <div id="filterLoadingIndicator" class="text-center mt-3" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <small class="text-muted ms-2">Filtrage en cours...</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Métriques des 5 Modules -->
    <div class="row mb-4">
        <!-- Module PROFIL VISA -->
        <div class="col-xl-2 col-lg-4 col-md-6 mb-4">
            <div class="module-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; padding: 1.8rem; color: white; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3); transition: all 0.3s ease; position: relative; overflow: hidden; height: 220px;">
                <i class="fas fa-passport" style="position: absolute; right: 1rem; top: 1rem; font-size: 2.5rem; opacity: 0.2;"></i>
                
                <div class="position-relative">
                    <div class="module-icon mb-2">
                        <div style="width: 45px; height: 45px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-passport fa-lg"></i>
                        </div>
                    </div>
                    <h4 id="profilVisaTotal" style="font-weight: 800; font-size: 1.8rem; margin-bottom: 0.5rem;">{{ number_format($profilVisaStats['periode'] ?? $profilVisaStats['total']) }}</h4>
                    <p style="opacity: 0.9; font-weight: 600; margin-bottom: 1rem; font-size: 0.95rem;">PROFIL VISA</p>
                    <div style="background: rgba(255,255,255,0.1); border-radius: 10px; padding: 0.8rem;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small style="opacity: 0.9;">Période</small>
                            <strong id="profilVisaPeriode">{{ $profilVisaStats['periode'] ?? $profilVisaStats['ce_mois'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small style="opacity: 0.9;">Taux réussite</small>
                            <strong id="profilVisaTaux">{{ $profilVisaStats['taux_reussite'] }}%</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Module SOUSCRIPTION FORFAITS -->
        <div class="col-xl-2 col-lg-4 col-md-6 mb-4">
            <div class="module-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border-radius: 20px; padding: 1.8rem; color: white; box-shadow: 0 8px 25px rgba(17, 153, 142, 0.3); transition: all 0.3s ease; position: relative; overflow: hidden; height: 220px;">
                <i class="fas fa-tags" style="position: absolute; right: 1rem; top: 1rem; font-size: 2.5rem; opacity: 0.2;"></i>
                
                <div class="position-relative">
                    <div class="module-icon mb-2">
                        <div style="width: 45px; height: 45px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-tags fa-lg"></i>
                        </div>
                    </div>
                    <h4 id="souscriptionTotal" style="font-weight: 800; font-size: 1.8rem; margin-bottom: 0.5rem;">{{ number_format($souscriptionStats['periode'] ?? $souscriptionStats['total']) }}</h4>
                    <p style="opacity: 0.9; font-weight: 600; margin-bottom: 1rem; font-size: 0.95rem;">SOUSCRIPTIONS</p>
                    <div style="background: rgba(255,255,255,0.1); border-radius: 10px; padding: 0.8rem;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small style="opacity: 0.9;">Période</small>
                            <strong id="souscriptionPeriode">{{ $souscriptionStats['periode'] ?? $souscriptionStats['ce_mois'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small style="opacity: 0.9;">CA période</small>
                            <strong id="souscriptionCA">{{ number_format(($souscriptionStats['chiffre_affaires_periode'] ?? $souscriptionStats['chiffre_affaires_mois'])/1000) }}K</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Module DOCUMENTS VOYAGE -->
        <div class="col-xl-2 col-lg-4 col-md-6 mb-4">
            <div class="module-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 20px; padding: 1.8rem; color: white; box-shadow: 0 8px 25px rgba(240, 147, 251, 0.3); transition: all 0.3s ease; position: relative; overflow: hidden; height: 220px;">
                <i class="fas fa-file-text" style="position: absolute; right: 1rem; top: 1rem; font-size: 2.5rem; opacity: 0.2;"></i>
                
                <div class="position-relative">
                    <div class="module-icon mb-2">
                        <div style="width: 45px; height: 45px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-file-text fa-lg"></i>
                        </div>
                    </div>
                    <h4 id="documentsTotal" style="font-weight: 800; font-size: 1.8rem; margin-bottom: 0.5rem;">{{ number_format($documentsVoyageStats['periode'] ?? $documentsVoyageStats['total']) }}</h4>
                    <p style="opacity: 0.9; font-weight: 600; margin-bottom: 1rem; font-size: 0.95rem;">DOCUMENTS</p>
                    <div style="background: rgba(255,255,255,0.1); border-radius: 10px; padding: 0.8rem;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small style="opacity: 0.9;">Période</small>
                            <strong id="documentsPeriode">{{ $documentsVoyageStats['periode'] ?? $documentsVoyageStats['ce_mois'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small style="opacity: 0.9;">Traités</small>
                            <strong id="documentsTraites">{{ $documentsVoyageStats['traites'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Module RENDEZ-VOUS -->
        <div class="col-xl-2 col-lg-4 col-md-6 mb-4">
            <div class="module-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 20px; padding: 1.8rem; color: white; box-shadow: 0 8px 25px rgba(79, 172, 254, 0.3); transition: all 0.3s ease; position: relative; overflow: hidden; height: 220px;">
                <i class="fas fa-calendar-check" style="position: absolute; right: 1rem; top: 1rem; font-size: 2.5rem; opacity: 0.2;"></i>
                
                <div class="position-relative">
                    <div class="module-icon mb-2">
                        <div style="width: 45px; height: 45px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-calendar-check fa-lg"></i>
                        </div>
                    </div>
                    <h4 id="rdvTotal" style="font-weight: 800; font-size: 1.8rem; margin-bottom: 0.5rem;">{{ number_format($rendezVousStats['periode'] ?? $rendezVousStats['total']) }}</h4>
                    <p style="opacity: 0.9; font-weight: 600; margin-bottom: 1rem; font-size: 0.95rem;">RENDEZ-VOUS</p>
                    <div style="background: rgba(255,255,255,0.1); border-radius: 10px; padding: 0.8rem;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small style="opacity: 0.9;">Période</small>
                            <strong id="rdvPeriode">{{ $rendezVousStats['periode'] ?? $rendezVousStats['ce_mois'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small style="opacity: 0.9;">À venir</small>
                            <strong id="rdvAvenir">{{ $rendezVousStats['a_venir'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Module RÉSERVATION ACHAT -->
        <div class="col-xl-2 col-lg-4 col-md-6 mb-4">
            <div class="module-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); border-radius: 20px; padding: 1.8rem; color: white; box-shadow: 0 8px 25px rgba(250, 112, 154, 0.3); transition: all 0.3s ease; position: relative; overflow: hidden; height: 220px;">
                <i class="fas fa-plane" style="position: absolute; right: 1rem; top: 1rem; font-size: 2.5rem; opacity: 0.2;"></i>
                
                <div class="position-relative">
                    <div class="module-icon mb-2">
                        <div style="width: 45px; height: 45px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-plane fa-lg"></i>
                        </div>
                    </div>
                    <h4 id="reservationTotal" style="font-weight: 800; font-size: 1.8rem; margin-bottom: 0.5rem;">{{ number_format($reservationStats['periode'] ?? $reservationStats['total']) }}</h4>
                    <p style="opacity: 0.9; font-weight: 600; margin-bottom: 1rem; font-size: 0.95rem;">RÉSERVATIONS</p>
                    <div style="background: rgba(255,255,255,0.1); border-radius: 10px; padding: 0.8rem;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small style="opacity: 0.9;">Période</small>
                            <strong id="reservationPeriode">{{ $reservationStats['periode'] ?? $reservationStats['ce_mois'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small style="opacity: 0.9;">Type pop.</small>
                            <strong id="reservationType" style="font-size: 0.7rem;">{{ Str::limit($reservationStats['type_populaire'], 8) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bouton Quick Stats -->
        <div class="col-xl-2 col-lg-4 col-md-6 mb-4">
            <div style="background: white; border: 2px dashed #e9ecef; border-radius: 20px; padding: 1.8rem; text-align: center; height: 220px; display: flex; flex-direction: column; justify-content: center; transition: all 0.3s ease; cursor: pointer;" onclick="refreshAllStats()">
                <i class="fas fa-sync-alt fa-2x mb-3" style="color: #6c757d;"></i>
                <h6 style="color: #6c757d; font-weight: 600; margin-bottom: 1rem;">Actualiser Stats</h6>
                <small style="color: #adb5bd;">Temps réel</small>
                <div class="mt-2">
                    <div class="spinner-border spinner-border-sm d-none" id="refreshSpinner" style="color: #667eea;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et Analyses -->
    <div class="row mb-4">
        <!-- Évolution des Modules -->
        <div class="col-xl-8 mb-4">
            <div class="chart-panel" style="background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid rgba(102, 126, 234, 0.1);">
                <div class="panel-header d-flex justify-content-between align-items-center mb-4">
                    <h5 style="color: #2d3436; font-weight: 700; font-size: 1.3rem; margin: 0;">
                        <i class="fas fa-chart-line me-2" style="color: #667eea;"></i>
                        <span id="chartTitle">Évolution des 5 Modules - {{ $filterData['period_label'] ?? 'Ce mois' }}</span>
                    </h5>
                    <div class="chart-controls">
                        <div class="btn-group">
                            <button class="btn btn-outline-primary btn-sm active" onclick="toggleChart('line')" style="border-radius: 12px 0 0 12px;">
                                <i class="fas fa-chart-line me-1"></i>Ligne
                            </button>
                            <button class="btn btn-outline-primary btn-sm" onclick="toggleChart('bar')" style="border-radius: 0 12px 12px 0;">
                                <i class="fas fa-chart-bar me-1"></i>Barres
                            </button>
                        </div>
                    </div>
                </div>
                <div style="position: relative; height: 400px;">
                    <canvas id="modulesEvolutionChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Objectifs & Performance -->
        <div class="col-xl-4 mb-4">
            <div class="objectives-panel" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; padding: 2rem; color: white; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3); height: 100%;">
                <h6 class="mb-4" style="font-weight: 700; font-size: 1.2rem;">
                    <i class="fas fa-bullseye me-2"></i>
                    Objectifs pour la Période
                </h6>
                
                <!-- Objectif Profils Visa -->
                <div class="objective-item mb-3" style="background: rgba(255,255,255,0.1); border-radius: 12px; padding: 1.2rem;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span style="font-weight: 600; font-size: 0.9rem;">
                            <i class="fas fa-passport me-2"></i>Profils Visa
                        </span>
                        <span class="badge bg-white text-primary px-2 py-1" style="border-radius: 8px; font-weight: 600; font-size: 0.75rem;">
                            <span id="objectifProfilsCount">{{ $profilVisaStats['periode'] ?? $profilVisaStats['ce_mois'] }}</span>/<span id="objectifProfilsTarget">{{ $objectifs['profil_visa_periode'] ?? $objectifs['profil_visa_mois'] }}</span>
                        </span>
                    </div>
                    <div class="progress mb-1" style="height: 6px; border-radius: 10px; background: rgba(255,255,255,0.2);">
                        <div class="progress-bar bg-white" id="progressProfils" style="width: {{ $objectifs['progression_profils'] }}%; border-radius: 10px;"></div>
                    </div>
                    <small style="opacity: 0.8; font-size: 0.75rem;"><span id="progressProfilsPercent">{{ round($objectifs['progression_profils']) }}</span>% réalisé</small>
                </div>
                
                <!-- Objectif Souscriptions -->
                <div class="objective-item mb-3" style="background: rgba(255,255,255,0.1); border-radius: 12px; padding: 1.2rem;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span style="font-weight: 600; font-size: 0.9rem;">
                            <i class="fas fa-tags me-2"></i>Souscriptions
                        </span>
                        <span class="badge bg-white text-success px-2 py-1" style="border-radius: 8px; font-weight: 600; font-size: 0.75rem;">
                            <span id="objectifSouscriptionsCount">{{ $souscriptionStats['periode'] ?? $souscriptionStats['ce_mois'] }}</span>/<span id="objectifSouscriptionsTarget">{{ $objectifs['souscriptions_periode'] ?? $objectifs['souscriptions_mois'] }}</span>
                        </span>
                    </div>
                    <div class="progress mb-1" style="height: 6px; border-radius: 10px; background: rgba(255,255,255,0.2);">
                        <div class="progress-bar bg-white" id="progressSouscriptions" style="width: {{ $objectifs['progression_souscriptions'] }}%; border-radius: 10px;"></div>
                    </div>
                    <small style="opacity: 0.8; font-size: 0.75rem;"><span id="progressSouscriptionsPercent">{{ round($objectifs['progression_souscriptions']) }}</span>% réalisé</small>
                </div>
                
                <!-- Objectif RDV -->
                <div class="objective-item mb-4" style="background: rgba(255,255,255,0.1); border-radius: 12px; padding: 1.2rem;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span style="font-weight: 600; font-size: 0.9rem;">
                            <i class="fas fa-calendar-check me-2"></i>Rendez-vous
                        </span>
                        <span class="badge bg-white text-info px-2 py-1" style="border-radius: 8px; font-weight: 600; font-size: 0.75rem;">
                            <span id="objectifRdvCount">{{ $rendezVousStats['periode'] ?? $rendezVousStats['ce_mois'] }}</span>/<span id="objectifRdvTarget">{{ $objectifs['rdv_periode'] ?? $objectifs['rdv_mois'] }}</span>
                        </span>
                    </div>
                    <div class="progress mb-1" style="height: 6px; border-radius: 10px; background: rgba(255,255,255,0.2);">
                        <div class="progress-bar bg-white" id="progressRdv" style="width: {{ $objectifs['progression_rdv'] }}%; border-radius: 10px;"></div>
                    </div>
                    <small style="opacity: 0.8; font-size: 0.75rem;"><span id="progressRdvPercent">{{ round($objectifs['progression_rdv']) }}</span>% réalisé</small>
                </div>
                
                <!-- Performance Globale -->
                <div class="text-center" style="background: rgba(255,255,255,0.1); border-radius: 12px; padding: 1.5rem;">
                    <div id="performanceGlobale" style="font-size: 1.8rem; font-weight: 800; margin-bottom: 0.5rem;">
                        {{ round(($objectifs['progression_profils'] + $objectifs['progression_souscriptions'] + $objectifs['progression_rdv']) / 3) }}%
                    </div>
                    <div style="font-weight: 600; margin-bottom: 0.5rem;">Performance Globale</div>
                    <small style="opacity: 0.9;">Moyenne des 3 objectifs principaux</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Destinations et Activités -->
    <div class="row mb-4">
        <!-- Top Destinations -->
        <div class="col-xl-6 mb-4">
            <div class="destinations-panel" style="background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid rgba(17, 153, 142, 0.1);">
                <h5 class="mb-4" style="color: #2d3436; font-weight: 700; font-size: 1.3rem;">
                    <i class="fas fa-globe-americas me-2" style="color: #11998e;"></i>
                    Top Destinations - {{ $filterData['period_label'] ?? 'Période sélectionnée' }}
                </h5>
                
                <div class="destinations-list" id="destinationsList">
                    @forelse($topDestinations as $index => $destination)
                    <div class="destination-item" style="background: #f8f9fa; border-radius: 12px; padding: 1.2rem; margin-bottom: 0.8rem; border-left: 4px solid #11998e; position: relative;">
                        <div style="position: absolute; top: -6px; left: 12px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.7rem;">
                            {{ $index + 1 }}
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center" style="padding-left: 1rem;">
                            <div>
                                <h6 style="color: #2d3436; font-weight: 700; margin-bottom: 0.25rem;">
                                    <i class="fas fa-map-marker-alt me-2" style="color: #11998e;"></i>
                                    {{ $destination->pays_destination }}
                                </h6>
                                <small style="color: #6c757d;">Destination préférée</small>
                            </div>
                            <div class="text-end">
                                <div style="color: #11998e; font-weight: 800; font-size: 1.1rem;">
                                    {{ $destination->total }}
                                </div>
                                <small style="color: #6c757d;">réservations</small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4" style="color: #6c757d;">
                        <i class="fas fa-plane fa-3x mb-3" style="opacity: 0.3;"></i>
                        <h6 style="font-weight: 600;">Aucune destination</h6>
                        <p>Les destinations populaires pour cette période apparaîtront ici</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        
        <!-- Activités Récentes Multi-Modules -->
        <div class="col-xl-6 mb-4">
            <div class="activities-panel" style="background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid rgba(240, 147, 251, 0.1);">
                <h5 class="mb-4" style="color: #2d3436; font-weight: 700; font-size: 1.3rem;">
                    <i class="fas fa-history me-2" style="color: #f093fb;"></i>
                    Activités Récentes - {{ $filterData['period_label'] ?? 'Période sélectionnée' }}
                </h5>
                
                <div style="max-height: 350px; overflow-y: auto;" id="activitiesList">
                    @forelse($activitesRecentes as $activite)
                    <div class="activity-item" style="border-left: 3px solid {{ $activite['color'] === 'primary' ? '#667eea' : ($activite['color'] === 'success' ? '#11998e' : ($activite['color'] === 'warning' ? '#ffc107' : '#4facfe')) }}; padding-left: 1rem; margin-left: 0.5rem; margin-bottom: 1.2rem; position: relative;">
                        <!-- Point d'activité -->
                        <div style="position: absolute; left: -6px; top: 0.5rem; width: 12px; height: 12px; border-radius: 50%; background-color: {{ $activite['color'] === 'primary' ? '#667eea' : ($activite['color'] === 'success' ? '#11998e' : ($activite['color'] === 'warning' ? '#ffc107' : '#4facfe')) }};"></div>
                        
                        <div class="d-flex align-items-start">
                            <i class="{{ $activite['icon'] }} me-2 mt-1" style="color: {{ $activite['color'] === 'primary' ? '#667eea' : ($activite['color'] === 'success' ? '#11998e' : ($activite['color'] === 'warning' ? '#ffc107' : '#4facfe')) }}; font-size: 0.9rem;"></i>
                            <div class="flex-grow-1">
                                <p style="margin-bottom: 0.3rem; color: #2d3436; font-weight: 500; font-size: 0.9rem;">{{ $activite['message'] }}</p>
                                <small style="color: #6c757d; font-size: 0.75rem;">
                                    <i class="fas fa-clock me-1"></i>{{ $activite['date']->diffForHumans() }}
                                </small>
                                <span class="badge ms-2" style="background: {{ $activite['color'] === 'primary' ? '#667eea' : ($activite['color'] === 'success' ? '#11998e' : ($activite['color'] === 'warning' ? '#ffc107' : '#4facfe')) }}; color: white; font-size: 0.6rem; padding: 0.2rem 0.5rem;">
                                    {{ strtoupper(str_replace('_', ' ', $activite['type'])) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4" style="color: #6c757d;">
                        <i class="fas fa-clock fa-3x mb-3" style="opacity: 0.3;"></i>
                        <h6 style="font-weight: 600;">Aucune activité récente</h6>
                        <p>Les activités pour cette période apparaîtront ici</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Rapides pour Modules -->
    <div class="floating-modules-actions" style="position: fixed; bottom: 30px; right: 30px; z-index: 1000;">
        <div class="modules-fab-menu">
            <div class="modules-fab-submenu" id="modulesFabSubmenu" style="position: absolute; bottom: 70px; right: 0; display: none;">
                <a href="/profil-visa" class="modules-fab-item" style="display: block; margin-bottom: 8px; padding: 10px 16px; background: white; color: #667eea; text-decoration: none; border-radius: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s ease; white-space: nowrap; font-weight: 500; font-size: 0.85rem;">
                    <i class="fas fa-passport me-2"></i>Profil Visa
                </a>
                <a href="/souscrire-forfaits" class="modules-fab-item" style="display: block; margin-bottom: 8px; padding: 10px 16px; background: white; color: #11998e; text-decoration: none; border-radius: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s ease; white-space: nowrap; font-weight: 500; font-size: 0.85rem;">
                    <i class="fas fa-tags me-2"></i>Souscriptions
                </a>
                <a href="/documents-voyage" class="modules-fab-item" style="display: block; margin-bottom: 8px; padding: 10px 16px; background: white; color: #f093fb; text-decoration: none; border-radius: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s ease; white-space: nowrap; font-weight: 500; font-size: 0.85rem;">
                    <i class="fas fa-file-text me-2"></i>Documents
                </a>
                <a href="/rendez-vous" class="modules-fab-item" style="display: block; margin-bottom: 8px; padding: 10px 16px; background: white; color: #4facfe; text-decoration: none; border-radius: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s ease; white-space: nowrap; font-weight: 500; font-size: 0.85rem;">
                    <i class="fas fa-calendar-check me-2"></i>Rendez-vous
                </a>
                <a href="/reservation-achat" class="modules-fab-item" style="display: block; margin-bottom: 8px; padding: 10px 16px; background: white; color: #fa709a; text-decoration: none; border-radius: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s ease; white-space: nowrap; font-weight: 500; font-size: 0.85rem;">
                    <i class="fas fa-plane me-2"></i>Réservations
                </a>
            </div>
            <button class="modules-fab-button" onclick="toggleModulesFabMenu()" style="width: 55px; height: 55px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white; font-size: 1.3rem; box-shadow: 0 6px 18px rgba(102, 126, 234, 0.4); transition: all 0.3s ease; cursor: pointer;">
                <i class="fas fa-th-large" id="modulesFabIcon"></i>
            </button>
        </div>
    </div>

</div>

<!-- CSS Intégré -->
<style>
/* Animations et effets pour modules */
.module-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 15px 35px rgba(0,0,0,0.2) !important;
}

.destination-item:hover {
    transform: translateX(5px);
    background: #e9ecef !important;
    border-left-color: #38ef7d !important;
}

.modules-fab-item:hover {
    background: #f8f9fa !important;
    transform: translateX(-3px);
}

.modules-fab-button:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6) !important;
}

/* Styles pour les filtres par période */
.btn-period {
    background: white;
    border: 1px solid #dee2e6;
    color: #495057;
    font-weight: 500;
    border-radius: 12px;
    padding: 0.375rem 0.75rem;
    transition: all 0.3s ease;
    margin: 0.125rem;
}

.btn-period:hover {
    background: #f8f9fa;
    border-color: #667eea;
    color: #667eea;
    transform: translateY(-1px);
}

.btn-period.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.filter-panel {
    border-left: 4px solid #667eea;
}

.btn-group-wrap {
    max-width: 100%;
}

/* Animations d'entrée */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.module-card, .chart-panel, .objectives-panel, .destinations-panel, .activities-panel, .filter-panel {
    animation: slideInUp 0.6s ease-out;
}

/* Animations de mise à jour */
@keyframes updatePulse {
    0% { 
        background-color: rgba(102, 126, 234, 0.1);
        transform: scale(1);
    }
    50% { 
        background-color: rgba(102, 126, 234, 0.2);
        transform: scale(1.02);
    }
    100% { 
        background-color: transparent;
        transform: scale(1);
    }
}

.stats-updating {
    animation: updatePulse 0.6s ease-in-out;
}

/* Responsive pour modules et filtres */
@media (max-width: 768px) {
    .modules-header {
        padding: 1.5rem !important;
    }
    
    .modules-header h1 {
        font-size: 1.8rem !important;
    }
    
    .module-card {
        height: 180px !important;
        padding: 1.2rem !important;
    }
    
    .module-card h4 {
        font-size: 1.5rem !important;
    }
    
    .floating-modules-actions {
        bottom: 20px !important;
        right: 20px !important;
    }
    
    .modules-fab-button {
        width: 45px !important;
        height: 45px !important;
        font-size: 1.1rem !important;
    }
    
    .modules-icons {
        display: none !important;
    }
    
    .btn-group-wrap {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .btn-period {
        width: 100%;
        margin-bottom: 0.25rem;
        text-align: left;
    }
    
    .filter-panel .row {
        flex-direction: column;
    }
    
    .filter-panel .col-md-6 {
        width: 100%;
        margin-bottom: 1rem;
    }
}

/* Scrollbar personnalisée */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

/* Animation de chargement */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.spinner-border {
    animation: spin 1s linear infinite;
}

/* Toast notifications pour les filtres */
.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
}

.toast {
    border-radius: 12px;
    border: none;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.toast-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.toast-error {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}
</style>

<!-- Scripts Chart.js et interactions avec filtres -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration des couleurs des modules
    const moduleColors = {
        profil_visa: '#667eea',
        souscriptions: '#11998e',
        documents_voyage: '#f093fb',
        rendez_vous: '#4facfe',
        reservations: '#fa709a'
    };

    // Données initiales pour le graphique d'évolution
    let evolutionData = @json($evolutionModules ?? []);
    let currentPeriod = '{{ $filterData["current_period"] ?? "this_month" }}';

    // Graphique d'évolution des modules
    const ctxEvolution = document.getElementById('modulesEvolutionChart');
    let currentChart = null;
    
    function createChart(type = 'line') {
        if (currentChart) {
            currentChart.destroy();
        }
        
        currentChart = new Chart(ctxEvolution, {
            type: type,
            data: {
                labels: evolutionData.map(item => item.label || 'N/A'),
                datasets: [
                    {
                        label: 'Profils Visa',
                        data: evolutionData.map(item => item.profil_visa || 0),
                        borderColor: moduleColors.profil_visa,
                        backgroundColor: moduleColors.profil_visa + '20',
                        fill: type === 'line',
                        tension: 0.4
                    },
                    {
                        label: 'Souscriptions',
                        data: evolutionData.map(item => item.souscriptions || 0),
                        borderColor: moduleColors.souscriptions,
                        backgroundColor: moduleColors.souscriptions + '20',
                        fill: type === 'line',
                        tension: 0.4
                    },
                    {
                        label: 'Documents',
                        data: evolutionData.map(item => item.documents_voyage || 0),
                        borderColor: moduleColors.documents_voyage,
                        backgroundColor: moduleColors.documents_voyage + '20',
                        fill: type === 'line',
                        tension: 0.4
                    },
                    {
                        label: 'Rendez-vous',
                        data: evolutionData.map(item => item.rendez_vous || 0),
                        borderColor: moduleColors.rendez_vous,
                        backgroundColor: moduleColors.rendez_vous + '20',
                        fill: type === 'line',
                        tension: 0.4
                    },
                    {
                        label: 'Réservations',
                        data: evolutionData.map(item => item.reservations || 0),
                        borderColor: moduleColors.reservations,
                        backgroundColor: moduleColors.reservations + '20',
                        fill: type === 'line',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: { weight: '600', size: 11 }
                        }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { font: { weight: '500' } }
                    },
                    x: { 
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { font: { weight: '500' } }
                    }
                }
            }
        });
    }

    createChart('line');

    // ==================== GESTION DES FILTRES PAR PÉRIODE ====================

    // Gestionnaire pour les boutons de période prédéfinie
    document.querySelectorAll('.btn-period').forEach(button => {
        button.addEventListener('click', function() {
            const period = this.getAttribute('data-period');
            applyPeriodFilter(period);
        });
    });

    // Fonction pour appliquer un filtre de période
    function applyPeriodFilter(period, startDate = null, endDate = null) {
        // Mettre à jour l'état visuel des boutons
        document.querySelectorAll('.btn-period').forEach(btn => {
            btn.classList.remove('active');
        });
        
        if (period !== 'custom') {
            document.querySelector(`[data-period="${period}"]`).classList.add('active');
        }

        // Afficher l'indicateur de chargement
        showLoadingIndicator();

        // Préparer les données de la requête
        const filterData = {
            period: period,
            _token: '{{ csrf_token() }}'
        };

        if (period === 'custom' && startDate && endDate) {
            filterData.start_date = startDate;
            filterData.end_date = endDate;
        }

        // Appel AJAX pour récupérer les données filtrées
        fetch('{{ route("commercial.dashboard") }}/filter', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(filterData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                showToast('Erreur lors du filtrage: ' + data.error, 'error');
                return;
            }

            // Mettre à jour toutes les statistiques
            updateDashboardStats(data);
            
            // Mettre à jour le graphique
            evolutionData = data.evolution || [];
            createChart('line');
            
            // Mettre à jour le titre du graphique
            document.getElementById('chartTitle').textContent = 
                `Évolution des 5 Modules - ${data.filter_info.label}`;

            // Mettre à jour les destinations
            updateDestinations(data.top_destinations || []);
            
            // Mettre à jour les activités
            updateActivities(data.activites_recentes || []);

            currentPeriod = period;
            hideLoadingIndicator();
            showToast('Statistiques mises à jour avec succès!', 'success');
        })
        .catch(error => {
            console.error('Erreur:', error);
            hideLoadingIndicator();
            showToast('Erreur lors du filtrage des données', 'error');
        });
    }

    // Fonction pour mettre à jour les statistiques du dashboard
    function updateDashboardStats(data) {
        // Ajouter animation de mise à jour
        document.querySelectorAll('.module-card').forEach(card => {
            card.classList.add('stats-updating');
            setTimeout(() => card.classList.remove('stats-updating'), 600);
        });

        // Profils Visa
        if (data.profil_visa) {
            document.getElementById('profilVisaTotal').textContent = 
                new Intl.NumberFormat().format(data.profil_visa.total || 0);
            document.getElementById('profilVisaPeriode').textContent = 
                data.profil_visa.total || 0;
            // Calcul du taux de réussite
            const tauxReussite = data.profil_visa.total > 0 ? 
                Math.round((data.profil_visa.approuves / data.profil_visa.total) * 100) : 0;
            document.getElementById('profilVisaTaux').textContent = tauxReussite + '%';
        }

        // Souscriptions
        if (data.souscriptions) {
            document.getElementById('souscriptionTotal').textContent = 
                new Intl.NumberFormat().format(data.souscriptions.total || 0);
            document.getElementById('souscriptionPeriode').textContent = 
                data.souscriptions.total || 0;
            document.getElementById('souscriptionCA').textContent = 
                Math.floor((data.souscriptions.chiffre_affaires || 0) / 1000) + 'K';
        }

        // Documents Voyage
        if (data.documents_voyage) {
            document.getElementById('documentsTotal').textContent = 
                new Intl.NumberFormat().format(data.documents_voyage.total || 0);
            document.getElementById('documentsPeriode').textContent = 
                data.documents_voyage.total || 0;
            document.getElementById('documentsTraites').textContent = 
                data.documents_voyage.traites || 0;
        }

        // Rendez-vous
        if (data.rendez_vous) {
            document.getElementById('rdvTotal').textContent = 
                new Intl.NumberFormat().format(data.rendez_vous.total || 0);
            document.getElementById('rdvPeriode').textContent = 
                data.rendez_vous.total || 0;
            document.getElementById('rdvAvenir').textContent = 
                data.rendez_vous.confirmes || 0;
        }

        // Réservations
        if (data.reservations) {
            document.getElementById('reservationTotal').textContent = 
                new Intl.NumberFormat().format(data.reservations.total || 0);
            document.getElementById('reservationPeriode').textContent = 
                data.reservations.total || 0;
        }

        // Mettre à jour les objectifs (si disponibles)
        updateObjectives(data);
    }

    // Fonction pour mettre à jour les objectifs
    function updateObjectives(data) {
        if (data.objectifs) {
            // Objectifs Profils
            if (data.profil_visa) {
                const progressProfils = data.profil_visa.total > 0 ? 
                    Math.min(100, (data.profil_visa.total / (data.objectifs?.profil_visa_periode || 100)) * 100) : 0;
                document.getElementById('objectifProfilsCount').textContent = data.profil_visa.total || 0;
                document.getElementById('progressProfils').style.width = progressProfils + '%';
                document.getElementById('progressProfilsPercent').textContent = Math.round(progressProfils);
            }

            // Objectifs Souscriptions
            if (data.souscriptions) {
                const progressSouscriptions = data.souscriptions.total > 0 ? 
                    Math.min(100, (data.souscriptions.total / (data.objectifs?.souscriptions_periode || 60)) * 100) : 0;
                document.getElementById('objectifSouscriptionsCount').textContent = data.souscriptions.total || 0;
                document.getElementById('progressSouscriptions').style.width = progressSouscriptions + '%';
                document.getElementById('progressSouscriptionsPercent').textContent = Math.round(progressSouscriptions);
            }

            // Objectifs RDV
            if (data.rendez_vous) {
                const progressRdv = data.rendez_vous.total > 0 ? 
                    Math.min(100, (data.rendez_vous.total / (data.objectifs?.rdv_periode || 80)) * 100) : 0;
                document.getElementById('objectifRdvCount').textContent = data.rendez_vous.total || 0;
                document.getElementById('progressRdv').style.width = progressRdv + '%';
                document.getElementById('progressRdvPercent').textContent = Math.round(progressRdv);
            }

            // Performance globale
            const avgProgress = ((data.profil_visa?.progression || 0) + 
                               (data.souscriptions?.progression || 0) + 
                               (data.rendez_vous?.progression || 0)) / 3;
            document.getElementById('performanceGlobale').textContent = Math.round(avgProgress) + '%';
        }
    }

    // Fonction pour mettre à jour les destinations
    function updateDestinations(destinations) {
        const destinationsList = document.getElementById('destinationsList');
        if (!destinations || destinations.length === 0) {
            destinationsList.innerHTML = `
                <div class="text-center py-4" style="color: #6c757d;">
                    <i class="fas fa-plane fa-3x mb-3" style="opacity: 0.3;"></i>
                    <h6 style="font-weight: 600;">Aucune destination</h6>
                    <p>Les destinations populaires pour cette période apparaîtront ici</p>
                </div>
            `;
            return;
        }

        let html = '';
        destinations.forEach((destination, index) => {
            html += `
                <div class="destination-item" style="background: #f8f9fa; border-radius: 12px; padding: 1.2rem; margin-bottom: 0.8rem; border-left: 4px solid #11998e; position: relative;">
                    <div style="position: absolute; top: -6px; left: 12px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.7rem;">
                        ${index + 1}
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center" style="padding-left: 1rem;">
                        <div>
                            <h6 style="color: #2d3436; font-weight: 700; margin-bottom: 0.25rem;">
                                <i class="fas fa-map-marker-alt me-2" style="color: #11998e;"></i>
                                ${destination.pays_destination}
                            </h6>
                            <small style="color: #6c757d;">Destination préférée</small>
                        </div>
                        <div class="text-end">
                            <div style="color: #11998e; font-weight: 800; font-size: 1.1rem;">
                                ${destination.total}
                            </div>
                            <small style="color: #6c757d;">réservations</small>
                        </div>
                    </div>
                </div>
            `;
        });
        destinationsList.innerHTML = html;
    }

    // Fonction pour mettre à jour les activités
    function updateActivities(activities) {
        const activitiesList = document.getElementById('activitiesList');
        if (!activities || activities.length === 0) {
            activitiesList.innerHTML = `
                <div class="text-center py-4" style="color: #6c757d;">
                    <i class="fas fa-clock fa-3x mb-3" style="opacity: 0.3;"></i>
                    <h6 style="font-weight: 600;">Aucune activité récente</h6>
                    <p>Les activités pour cette période apparaîtront ici</p>
                </div>
            `;
            return;
        }

        let html = '';
        activities.forEach(activite => {
            const colors = {
                'primary': '#667eea',
                'success': '#11998e',
                'warning': '#ffc107',
                'info': '#4facfe'
            };
            const color = colors[activite.color] || '#667eea';

            html += `
                <div class="activity-item" style="border-left: 3px solid ${color}; padding-left: 1rem; margin-left: 0.5rem; margin-bottom: 1.2rem; position: relative;">
                    <div style="position: absolute; left: -6px; top: 0.5rem; width: 12px; height: 12px; border-radius: 50%; background-color: ${color};"></div>
                    
                    <div class="d-flex align-items-start">
                        <i class="${activite.icon} me-2 mt-1" style="color: ${color}; font-size: 0.9rem;"></i>
                        <div class="flex-grow-1">
                            <p style="margin-bottom: 0.3rem; color: #2d3436; font-weight: 500; font-size: 0.9rem;">${activite.message}</p>
                            <small style="color: #6c757d; font-size: 0.75rem;">
                                <i class="fas fa-clock me-1"></i>${activite.date_formatted || 'Récent'}
                            </small>
                            <span class="badge ms-2" style="background: ${color}; color: white; font-size: 0.6rem; padding: 0.2rem 0.5rem;">
                                ${activite.type.toUpperCase().replace('_', ' ')}
                            </span>
                        </div>
                    </div>
                </div>
            `;
        });
        activitiesList.innerHTML = html;
    }

    // ==================== FONCTIONS UTILITAIRES ====================

    // Fonction pour appliquer une période personnalisée
    window.applyCustomPeriod = function() {
        const startDate = document.getElementById('customStartDate').value;
        const endDate = document.getElementById('customEndDate').value;

        if (!startDate || !endDate) {
            showToast('Veuillez sélectionner une date de début et de fin', 'error');
            return;
        }

        if (new Date(startDate) > new Date(endDate)) {
            showToast('La date de début doit être antérieure à la date de fin', 'error');
            return;
        }

        applyPeriodFilter('custom', startDate, endDate);
    };

    // Fonction pour réinitialiser les filtres
    window.resetFilters = function() {
        applyPeriodFilter('this_month');
        document.getElementById('customStartDate').value = '';
        document.getElementById('customEndDate').value = '';
    };

    // Fonction pour changer le type de graphique
    window.toggleChart = function(type) {
        createChart(type);
        
        // Mise à jour des boutons
        document.querySelectorAll('.chart-controls .btn').forEach(btn => {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-primary');
        });
        
        event.target.classList.remove('btn-outline-primary');
        event.target.classList.add('btn-primary');
    };

    // Fonction pour afficher l'indicateur de chargement
    function showLoadingIndicator() {
        document.getElementById('filterLoadingIndicator').style.display = 'block';
    }

    // Fonction pour masquer l'indicateur de chargement
    function hideLoadingIndicator() {
        document.getElementById('filterLoadingIndicator').style.display = 'none';
    }

    // Fonction pour afficher les notifications toast
    function showToast(message, type = 'success') {
        // Créer le conteneur de toast s'il n'existe pas
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container';
            document.body.appendChild(toastContainer);
        }

        // Créer le toast
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.style.display = 'block';
        toast.innerHTML = `
            <div class="toast-body d-flex align-items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} me-2"></i>
                ${message}
            </div>
        `;

        toastContainer.appendChild(toast);

        // Animation d'entrée
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(0)';
        }, 100);

        // Suppression automatique après 3 secondes
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }

    // Animation des cartes au chargement
    const cards = document.querySelectorAll('.module-card, .chart-panel, .objectives-panel, .destinations-panel, .activities-panel, .filter-panel');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Actualisation temps réel toutes les 5 minutes
    setInterval(function() {
        fetch('/commercial/dashboard/realtime-stats')
            .then(response => response.json())
            .then(data => {
                console.log('📊 Stats modules mises à jour:', data);
                // Mettre à jour les statistiques temps réel sans recharger
            })
            .catch(error => console.log('Erreur actualisation modules:', error));
    }, 300000);
});

// Fonctions du menu FAB modules
function toggleModulesFabMenu() {
    const submenu = document.getElementById('modulesFabSubmenu');
    const icon = document.getElementById('modulesFabIcon');
    
    submenu.style.display = submenu.style.display === 'none' ? 'block' : 'none';
    icon.classList.toggle('fa-th-large');
    icon.classList.toggle('fa-times');
}

// Fermer le menu quand on clique ailleurs
document.addEventListener('click', function(event) {
    const fabMenu = document.querySelector('.modules-fab-menu');
    if (!fabMenu.contains(event.target)) {
        document.getElementById('modulesFabSubmenu').style.display = 'none';
        document.getElementById('modulesFabIcon').className = 'fas fa-th-large';
    }
});

// Fonction d'actualisation des stats
function refreshAllStats() {
    const spinner = document.getElementById('refreshSpinner');
    const icon = document.querySelector('[onclick="refreshAllStats()"] i');
    
    spinner.classList.remove('d-none');
    icon.style.display = 'none';
    
    fetch('/commercial/dashboard/realtime-stats')
        .then(response => response.json())
        .then(data => {
            setTimeout(() => {
                spinner.classList.add('d-none');
                icon.style.display = 'inline-block';
                
                // Animation de succès
                icon.style.color = '#28a745';
                setTimeout(() => {
                    icon.style.color = '#6c757d';
                }, 2000);
            }, 1500);
        })
        .catch(error => {
            spinner.classList.add('d-none');
            icon.style.display = 'inline-block';
            icon.style.color = '#dc3545';
            setTimeout(() => {
                icon.style.color = '#6c757d';
            }, 2000);
        });
}
</script>

@endsection