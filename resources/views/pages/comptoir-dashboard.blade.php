@extends('layouts.main')

@section('title', 'Dashboard Agent Comptoir - Modules PSI Africa')

@section('content')
<div class="container-fluid">
    
    <!-- En-tête Dashboard Agent Comptoir avec 5 Modules -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="dashboard-header" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 2.5rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(79, 172, 254, 0.3); position: relative; overflow: hidden;">
                <!-- Motifs décoratifs -->
                <div style="position: absolute; top: -50%; right: -10%; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%; transform: rotate(45deg);"></div>
                <div style="position: absolute; bottom: -30%; left: -5%; width: 150px; height: 150px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                
                <div class="d-flex justify-content-between align-items-center position-relative">
                    <div>
                        <h1 class="mb-3" style="font-weight: 800; font-size: 2.5rem; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <i class="fas fa-desktop me-3" style="color: #ffd700;"></i>
                            Dashboard Agent Comptoir - Modules
                        </h1>
                        <p class="mb-3" style="font-size: 1.2rem; opacity: 0.95; font-weight: 500;">
                            Gérez vos 5 modules : PROFIL VISA, SOUSCRIPTIONS, DOCUMENTS, RDV, RÉSERVATIONS
                        </p>
                        <div class="d-flex align-items-center flex-wrap">
                            <span class="badge bg-white text-primary px-3 py-2 me-3 mb-2" style="border-radius: 25px; font-weight: 600;">
                                <i class="fas fa-user me-1"></i> {{ Auth::user()->name }}
                            </span>
                            <span class="badge" style="background: rgba(255,255,255,0.2); color: white; border-radius: 25px; padding: 0.5rem 1rem; margin-bottom: 0.5rem;">
                                <i class="fas fa-calendar me-1"></i> {{ $filterData['period_label'] ?? 'Ce mois' }}
                            </span>
                            <span class="badge" style="background: rgba(255,255,255,0.15); color: white; border-radius: 25px; padding: 0.5rem 1rem; margin-bottom: 0.5rem;">
                                <i class="fas fa-chart-line me-1"></i> {{ $classementAgent['position'] ?? 1 }}{{ ($classementAgent['position'] ?? 1) == 1 ? 'er' : 'ème' }} sur {{ $classementAgent['total_agents'] ?? 1 }}
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

    <!-- Panneau de Filtres par Période -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="filter-panel" style="background: white; border-radius: 20px; padding: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid rgba(79, 172, 254, 0.1);">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 style="color: #2d3436; font-weight: 700; font-size: 1.1rem; margin: 0;">
                        <i class="fas fa-filter me-2" style="color: #4facfe;"></i>
                        Filtrer les statistiques par période
                    </h5>
                    <div class="filter-actions">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetFilters()" style="border-radius: 12px;">
                            <i class="fas fa-undo me-1"></i>Réinitialiser
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm ms-2" onclick="refreshAllStats()" style="border-radius: 12px;">
                            <i class="fas fa-sync-alt me-1"></i>Actualiser
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

    <!-- Métriques des 5 Modules - Perspective Agent Comptoir -->
    <div class="row mb-4">
        <!-- Module PROFIL VISA - Statistiques Globales + Agent -->
        <div class="col-xl-2 col-lg-4 col-md-6 mb-4">
            <div class="module-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; padding: 1.8rem; color: white; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3); transition: all 0.3s ease; position: relative; overflow: hidden; height: 220px;">
                <i class="fas fa-passport" style="position: absolute; right: 1rem; top: 1rem; font-size: 2.5rem; opacity: 0.2;"></i>
                
                <div class="position-relative">
                    <div class="module-icon mb-2">
                        <div style="width: 45px; height: 45px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-passport fa-lg"></i>
                        </div>
                    </div>
                    <h4 id="profilVisaTotal" style="font-weight: 800; font-size: 1.8rem; margin-bottom: 0.5rem;">{{ number_format($profilVisaStats['total_global'] ?? 0) }}</h4>
                    <p style="opacity: 0.9; font-weight: 600; margin-bottom: 1rem; font-size: 0.95rem;">PROFIL VISA</p>
                    <div style="background: rgba(255,255,255,0.1); border-radius: 10px; padding: 0.8rem;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small style="opacity: 0.9;">Global période</small>
                            <strong id="profilVisaGlobalPeriode">{{ $profilVisaStats['periode_global'] ?? 0 }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small style="opacity: 0.9;">Mes traités</small>
                            <strong id="profilVisaAgentTraites">{{ $profilVisaStats['traites_periode_agent'] ?? 0 }}</strong>
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
                    <h4 id="souscriptionTotal" style="font-weight: 800; font-size: 1.8rem; margin-bottom: 0.5rem;">{{ number_format($souscriptionStats['periode'] ?? 0) }}</h4>
                    <p style="opacity: 0.9; font-weight: 600; margin-bottom: 1rem; font-size: 0.95rem;">SOUSCRIPTIONS</p>
                    <div style="background: rgba(255,255,255,0.1); border-radius: 10px; padding: 0.8rem;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small style="opacity: 0.9;">Aujourd'hui</small>
                            <strong id="souscriptionAujourdhui">{{ $souscriptionStats['aujourd_hui'] ?? 0 }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small style="opacity: 0.9;">CA période</small>
                            <strong id="souscriptionCA">{{ number_format(($souscriptionStats['chiffre_affaires_periode'] ?? 0)/1000) }}K</strong>
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
                    <h4 id="documentsTotal" style="font-weight: 800; font-size: 1.8rem; margin-bottom: 0.5rem;">{{ number_format($documentsVoyageStats['periode'] ?? 0) }}</h4>
                    <p style="opacity: 0.9; font-weight: 600; margin-bottom: 1rem; font-size: 0.95rem;">DOCUMENTS</p>
                    <div style="background: rgba(255,255,255,0.1); border-radius: 10px; padding: 0.8rem;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small style="opacity: 0.9;">Traités</small>
                            <strong id="documentsTraites">{{ $documentsVoyageStats['traites'] ?? 0 }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small style="opacity: 0.9;">En cours</small>
                            <strong id="documentsEnCours">{{ $documentsVoyageStats['en_cours'] ?? 0 }}</strong>
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
                    <h4 id="rdvTotal" style="font-weight: 800; font-size: 1.8rem; margin-bottom: 0.5rem;">{{ number_format($rendezVousStats['periode'] ?? 0) }}</h4>
                    <p style="opacity: 0.9; font-weight: 600; margin-bottom: 1rem; font-size: 0.95rem;">RENDEZ-VOUS</p>
                    <div style="background: rgba(255,255,255,0.1); border-radius: 10px; padding: 0.8rem;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small style="opacity: 0.9;">Confirmés</small>
                            <strong id="rdvConfirmes">{{ $rendezVousStats['confirmes'] ?? 0 }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small style="opacity: 0.9;">À venir</small>
                            <strong id="rdvAvenir">{{ $rendezVousStats['a_venir'] ?? 0 }}</strong>
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
                    <h4 id="reservationTotal" style="font-weight: 800; font-size: 1.8rem; margin-bottom: 0.5rem;">{{ number_format($reservationStats['periode'] ?? 0) }}</h4>
                    <p style="opacity: 0.9; font-weight: 600; margin-bottom: 1rem; font-size: 0.95rem;">RÉSERVATIONS</p>
                    <div style="background: rgba(255,255,255,0.1); border-radius: 10px; padding: 0.8rem;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small style="opacity: 0.9;">Confirmées</small>
                            <strong id="reservationConfirmees">{{ $reservationStats['confirmees'] ?? 0 }}</strong>
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
                    <div class="spinner-border spinner-border-sm d-none" id="refreshSpinner" style="color: #4facfe;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et Analyses -->
    <div class="row mb-4">
        <!-- Évolution des 5 Modules -->
        <div class="col-xl-8 mb-4">
            <div class="chart-panel" style="background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid rgba(79, 172, 254, 0.1);">
                <div class="panel-header d-flex justify-content-between align-items-center mb-4">
                    <h5 style="color: #2d3436; font-weight: 700; font-size: 1.3rem; margin: 0;">
                        <i class="fas fa-chart-line me-2" style="color: #4facfe;"></i>
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
        
        <!-- Performance Agent & Objectifs -->
        <div class="col-xl-4 mb-4">
            <div class="objectives-panel" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 20px; padding: 2rem; color: white; box-shadow: 0 8px 25px rgba(79, 172, 254, 0.3); height: 100%;">
                <h6 class="mb-4" style="font-weight: 700; font-size: 1.2rem;">
                    <i class="fas fa-trophy me-2"></i>
                    Performance & Objectifs
                </h6>
                
                <!-- Classement Agent -->
                <div class="text-center mb-4" style="background: rgba(255,255,255,0.1); border-radius: 12px; padding: 1.5rem;">
                    <div style="font-size: 2.5rem; font-weight: 800; margin-bottom: 0.5rem;">
                        {{ $classementAgent['position'] ?? 1 }}{{ ($classementAgent['position'] ?? 1) == 1 ? 'er' : 'ème' }}
                    </div>
                    <div style="font-weight: 600; margin-bottom: 0.5rem;">Position / {{ $classementAgent['total_agents'] ?? 1 }} agents</div>
                    <small style="opacity: 0.9;">{{ $classementAgent['percentile'] ?? 100 }}e percentile</small>
                </div>

                <!-- Objectif Profils Visa Agent -->
                <div class="objective-item mb-3" style="background: rgba(255,255,255,0.1); border-radius: 12px; padding: 1.2rem;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span style="font-weight: 600; font-size: 0.9rem;">
                            <i class="fas fa-passport me-2"></i>Mes Profils Traités
                        </span>
                        <span class="badge bg-white text-primary px-2 py-1" style="border-radius: 8px; font-weight: 600; font-size: 0.75rem;">
                            <span id="objectifProfilsCount">{{ $profilVisaStats['traites_periode_agent'] ?? 0 }}</span>/<span id="objectifProfilsTarget">{{ $objectifs['profil_visa_periode'] ?? 700 }}</span>
                        </span>
                    </div>
                    <div class="progress mb-1" style="height: 6px; border-radius: 10px; background: rgba(255,255,255,0.2);">
                        <div class="progress-bar bg-white" id="progressProfils" style="width: {{ $objectifs['progression_profils'] ?? 0 }}%; border-radius: 10px;"></div>
                    </div>
                    <small style="opacity: 0.8; font-size: 0.75rem;"><span id="progressProfilsPercent">{{ round($objectifs['progression_profils'] ?? 0) }}</span>% réalisé</small>
                </div>
                
                <!-- Performance Metrics -->
                <div class="row text-center mb-3">
                    <div class="col-6">
                        <div class="performance-metric" style="background: rgba(255,255,255,0.1); border-radius: 12px; padding: 1rem;">
                            <h4 class="mb-1" style="font-weight: bold; color: white;">{{ $tempsReponse ?? 24 }}h</h4>
                            <small style="opacity: 0.9; font-size: 0.8rem;">Temps Réponse</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="performance-metric" style="background: rgba(255,255,255,0.1); border-radius: 12px; padding: 1rem;">
                            <h4 class="mb-1" style="font-weight: bold; color: white;">{{ $tauxResolution ?? 85 }}%</h4>
                            <small style="opacity: 0.9; font-size: 0.8rem;">Taux Résolution</small>
                        </div>
                    </div>
                </div>

                <!-- Satisfaction -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small fw-bold" style="opacity: 0.9;">Satisfaction Client</span>
                        <span class="small fw-bold">{{ $tauxSatisfaction ?? 88 }}%</span>
                    </div>
                    <div class="progress mb-1" style="height: 8px; border-radius: 10px; background: rgba(255,255,255,0.2);">
                        <div class="progress-bar bg-white" style="width: {{ $tauxSatisfaction ?? 88 }}%; border-radius: 10px; transition: width 0.6s ease;"></div>
                    </div>
                    <small style="opacity: 0.8; font-size: 0.75rem;">Basé sur performance et rapidité</small>
                </div>
                
                <!-- Productivité -->
                <div class="text-center" style="background: rgba(255,255,255,0.1); border-radius: 12px; padding: 1rem;">
                    <div class="d-flex justify-content-between mb-2">
                        <small style="opacity: 0.9;">Moyenne/jour:</small>
                        <strong>{{ $productiviteStats['moyenne_quotidienne'] ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <small style="opacity: 0.9;">Score Efficacité:</small>
                        <strong>{{ $productiviteStats['efficacite_score'] ?? 0 }}%</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small style="opacity: 0.9;">Pic Productivité:</small>
                        <strong>{{ $productiviteStats['pic_productivite'] ?? '10:00' }}</strong>
                    </div>
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
                    <div class="activity-item" style="border-left: 3px solid {{ $activite['color'] === 'primary' ? '#4facfe' : ($activite['color'] === 'success' ? '#11998e' : ($activite['color'] === 'info' ? '#17a2b8' : '#f093fb')) }}; padding-left: 1rem; margin-left: 0.5rem; margin-bottom: 1.2rem; position: relative;">
                        <!-- Point d'activité -->
                        <div style="position: absolute; left: -6px; top: 0.5rem; width: 12px; height: 12px; border-radius: 50%; background-color: {{ $activite['color'] === 'primary' ? '#4facfe' : ($activite['color'] === 'success' ? '#11998e' : ($activite['color'] === 'info' ? '#17a2b8' : '#f093fb')) }};"></div>
                        
                        <div class="d-flex align-items-start">
                            <i class="{{ $activite['icon'] }} me-2 mt-1" style="color: {{ $activite['color'] === 'primary' ? '#4facfe' : ($activite['color'] === 'success' ? '#11998e' : ($activite['color'] === 'info' ? '#17a2b8' : '#f093fb')) }}; font-size: 0.9rem;"></i>
                            <div class="flex-grow-1">
                                <p style="margin-bottom: 0.3rem; color: #2d3436; font-weight: 500; font-size: 0.9rem;">{{ $activite['message'] }}</p>
                                <small style="color: #6c757d; font-size: 0.75rem;">
                                    <i class="fas fa-clock me-1"></i>{{ $activite['date_formatted'] ?? 'Récent' }}
                                </small>
                                <span class="badge ms-2" style="background: {{ $activite['color'] === 'primary' ? '#4facfe' : ($activite['color'] === 'success' ? '#11998e' : ($activite['color'] === 'info' ? '#17a2b8' : '#f093fb')) }}; color: white; font-size: 0.6rem; padding: 0.2rem 0.5rem;">
                                    {{ strtoupper(str_replace('_', ' ', $activite['type'])) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4" style="color: #6c757d;">
                        <i class="fas fa-clock fa-3x mb-3" style="opacity: 0.3;"></i>
                        <h6 style="font-weight: 600;">Aucune activité récente</h6>
                        <p>Vos activités pour cette période apparaîtront ici</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Nouveaux clients et Répartitions -->
    <div class="row mb-4">
        <!-- Nouveaux clients à traiter -->
        <div class="col-lg-8">
            <div class="card" style="border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                <div class="card-header bg-white d-flex justify-content-between align-items-center" style="border-radius: 15px 15px 0 0; border-bottom: 1px solid #dee2e6;">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users me-2 text-success"></i>
                        Nouveaux Clients - Profils à Traiter
                        <span class="badge bg-success ms-2">{{ ($nouveauxClients ?? collect())->count() }}</span>
                    </h5>
                    <div>
                        <button class="btn btn-outline-success btn-sm me-2" onclick="refreshClientsList()">
                            <i class="fas fa-refresh me-1"></i>Actualiser
                        </button>
                        <button class="btn btn-success btn-sm" onclick="voirTousProfilsVisa()">
                            <i class="fas fa-list me-1"></i>Voir Tous
                        </button>
                    </div>
                </div>
                <div class="card-body" style="max-height: 450px; overflow-y: auto;">
                    @if(isset($nouveauxClients) && $nouveauxClients->count() > 0)
                        @foreach($nouveauxClients as $client)
                        <div class="client-item {{ \Carbon\Carbon::parse($client->created_at)->diffInHours(now()) <= 24 ? 'client-nouveau' : '' }}" style="border-left: 4px solid #11998e; background-color: #f8fff8; padding: 1rem; margin-bottom: 0.75rem; border-radius: 0 12px 12px 0; transition: all 0.3s ease;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="client-avatar" style="width: 45px; height: 45px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; margin-right: 15px; font-size: 1.1rem;">
                                        {{ strtoupper(substr($client->client_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-success">{{ $client->client_name }}</h6>
                                        <small class="text-muted">{{ $client->numero_profil_visa ?? 'PSI-VIS-' . str_pad($client->id, 6, '0', STR_PAD_LEFT) }}</small>
                                        <div class="client-info mt-1">
                                            <small class="text-muted d-block">
                                                <i class="fas fa-calendar-plus me-1 text-success"></i>
                                                Créé {{ \Carbon\Carbon::parse($client->created_at)->diffForHumans() }}
                                            </small>
                                            @if($client->client_contact)
                                                <small class="text-info d-block">
                                                    <i class="fas fa-phone me-1"></i>{{ $client->client_contact }}
                                                </small>
                                            @endif
                                            @if(\Carbon\Carbon::parse($client->created_at)->diffInHours(now()) <= 2)
                                                <span class="badge bg-gradient bg-success text-white px-2 py-1 mt-1" style="font-size: 0.7rem;">
                                                    <i class="fas fa-star me-1"></i>Tout nouveau !
                                                </span>
                                            @elseif(\Carbon\Carbon::parse($client->created_at)->diffInHours(now()) <= 24)
                                                <span class="badge bg-gradient bg-info text-white px-2 py-1 mt-1" style="font-size: 0.7rem;">
                                                    <i class="fas fa-clock me-1"></i>Récent
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    @if($client->status_name)
                                        <span class="badge" style="background-color: #{{ $client->status_color ?? '11998e' }}; color: white; font-size: 0.75rem; padding: 0.5rem 0.75rem; border-radius: 20px;">
                                            {{ $client->status_name }}
                                        </span>
                                    @else
                                        <span class="badge" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; font-size: 0.75rem; padding: 0.5rem 0.75rem; border-radius: 20px;">
                                            <i class="fas fa-hourglass-start me-1"></i>Nouveau
                                        </span>
                                    @endif
                                    <br>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-success me-1" onclick="accueillerClient({{ $client->id }})" title="Accueillir le client">
                                            <i class="fas fa-handshake"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary me-1" onclick="traiterProfil({{ $client->id }})" title="Traiter le profil">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" onclick="voirDetails({{ $client->id }})" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-4x text-muted mb-3" style="opacity: 0.3;"></i>
                            <h5 class="text-muted">Aucun nouveau client</h5>
                            <p class="text-muted">Les nouveaux profils visa créés apparaîtront ici automatiquement.</p>
                            <button class="btn btn-outline-success btn-sm mt-2" onclick="refreshClientsList()">
                                <i class="fas fa-sync-alt me-1"></i>Vérifier les nouveaux clients
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Répartitions -->
        <div class="col-lg-4">
            <div class="row">
                <!-- Répartition par statut -->
                <div class="col-12 mb-4">
                    <div class="card" style="border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                        <div class="card-header bg-white" style="border-radius: 15px 15px 0 0; border-bottom: 1px solid #dee2e6;">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-chart-pie me-2 text-info"></i>
                                Répartition par Statut
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart-container" style="position: relative; height: 220px;">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Répartition par type -->
                <div class="col-12">
                    <div class="card" style="border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                        <div class="card-header bg-white" style="border-radius: 15px 15px 0 0; border-bottom: 1px solid #dee2e6;">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-tags me-2 text-warning"></i>
                                Types de Visa
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="types-list">
                                @if(isset($visaParType) && $visaParType->count() > 0)
                                    @foreach($visaParType->take(5) as $type)
                                    <div class="d-flex justify-content-between align-items-center mb-2 p-2" style="background: #f8f9fa; border-radius: 8px;">
                                        <div>
                                            <span class="fw-bold" style="color: #2d3436; font-size: 0.9rem;">{{ $type->type_name }}</span>
                                        </div>
                                        <div>
                                            <span class="badge bg-primary">{{ $type->total }}</span>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-3">
                                        <i class="fas fa-tags fa-2x text-muted mb-2" style="opacity: 0.3;"></i>
                                        <small class="text-muted">Aucune donnée disponible</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides flottantes pour modules -->
    <div class="floating-modules-actions" style="position: fixed; bottom: 30px; right: 30px; z-index: 1000;">
        <div class="modules-fab-menu">
            <div class="modules-fab-submenu" id="modulesFabSubmenu" style="position: absolute; bottom: 70px; right: 0; display: none;">
                <a href="/profil-visa" class="modules-fab-item" style="display: block; margin-bottom: 8px; padding: 10px 16px; background: white; color: #4facfe; text-decoration: none; border-radius: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s ease; white-space: nowrap; font-weight: 500; font-size: 0.85rem;">
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
            <button class="modules-fab-button" onclick="toggleModulesFabMenu()" style="width: 55px; height: 55px; border-radius: 50%; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border: none; color: white; font-size: 1.3rem; box-shadow: 0 6px 18px rgba(79, 172, 254, 0.4); transition: all 0.3s ease; cursor: pointer;">
                <i class="fas fa-th-large" id="modulesFabIcon"></i>
            </button>
        </div>
    </div>

</div>

<!-- CSS Intégré - Similaire au commercial mais couleur comptoir -->
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
    box-shadow: 0 8px 25px rgba(79, 172, 254, 0.6) !important;
}

.client-item:hover {
    background-color: #e8f5e8 !important;
    transform: translateX(5px);
    border-left-color: #38ef7d !important;
}

.client-nouveau {
    animation: pulseGlow 2s infinite;
}

@keyframes pulseGlow {
    0%, 100% {
        box-shadow: 0 0 5px rgba(17, 153, 142, 0.3);
    }
    50% {
        box-shadow: 0 0 20px rgba(17, 153, 142, 0.6);
    }
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
    border-color: #4facfe;
    color: #4facfe;
    transform: translateY(-1px);
}

.btn-period.active {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    border-color: #4facfe;
    color: white;
    box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);
}

.filter-panel {
    border-left: 4px solid #4facfe;
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
        background-color: rgba(79, 172, 254, 0.1);
        transform: scale(1);
    }
    50% { 
        background-color: rgba(79, 172, 254, 0.2);
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
    .dashboard-header {
        padding: 1.5rem !important;
    }
    
    .dashboard-header h1 {
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
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
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

.performance-metric:hover {
    transform: scale(1.05);
    background: rgba(255,255,255,0.2) !important;
}
</style>

<!-- Scripts Chart.js et interactions avec filtres -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration des couleurs des modules - thème comptoir
    const moduleColors = {
        profil_visa: '#4facfe',
        profil_visa_agent: '#667eea', 
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
                        label: 'Profils Visa (Global)',
                        data: evolutionData.map(item => item.profil_visa || 0),
                        borderColor: moduleColors.profil_visa,
                        backgroundColor: moduleColors.profil_visa + '20',
                        fill: type === 'line',
                        tension: 0.4
                    },
                    {
                        label: 'Mes Profils Traités',
                        data: evolutionData.map(item => item.profil_visa_agent || 0),
                        borderColor: moduleColors.profil_visa_agent,
                        backgroundColor: moduleColors.profil_visa_agent + '20',
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
        fetch('{{ route("comptoir.dashboard.filter") }}', {
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
                new Intl.NumberFormat().format(data.profil_visa.total_global || 0);
            document.getElementById('profilVisaGlobalPeriode').textContent = 
                data.profil_visa.periode_global || 0;
            document.getElementById('profilVisaAgentTraites').textContent = 
                data.profil_visa.traites_periode_agent || 0;
        }

        // Souscriptions
        if (data.souscriptions) {
            document.getElementById('souscriptionTotal').textContent = 
                new Intl.NumberFormat().format(data.souscriptions.periode || 0);
            document.getElementById('souscriptionAujourdhui').textContent = 
                data.souscriptions.aujourd_hui || 0;
            document.getElementById('souscriptionCA').textContent = 
                Math.floor((data.souscriptions.chiffre_affaires_periode || 0) / 1000) + 'K';
        }

        // Documents Voyage
        if (data.documents_voyage) {
            document.getElementById('documentsTotal').textContent = 
                new Intl.NumberFormat().format(data.documents_voyage.periode || 0);
            document.getElementById('documentsTraites').textContent = 
                data.documents_voyage.traites || 0;
            document.getElementById('documentsEnCours').textContent = 
                data.documents_voyage.en_cours || 0;
        }

        // Rendez-vous
        if (data.rendez_vous) {
            document.getElementById('rdvTotal').textContent = 
                new Intl.NumberFormat().format(data.rendez_vous.periode || 0);
            document.getElementById('rdvConfirmes').textContent = 
                data.rendez_vous.confirmes || 0;
            document.getElementById('rdvAvenir').textContent = 
                data.rendez_vous.a_venir || 0;
        }

        // Réservations
        if (data.reservations) {
            document.getElementById('reservationTotal').textContent = 
                new Intl.NumberFormat().format(data.reservations.periode || 0);
            document.getElementById('reservationConfirmees').textContent = 
                data.reservations.confirmees || 0;
        }
    }

    // Configuration du graphique de répartition par statut
    const ctxStatus = document.getElementById('statusChart');
    const statusData = @json($visaParStatut ?? []);
    
    if (ctxStatus && statusData.length > 0) {
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: statusData.map(item => item.status_name || 'Non défini'),
                datasets: [{
                    data: statusData.map(item => item.total || 0),
                    backgroundColor: statusData.map(item => {
                        const color = item.status_color || '4facfe';
                        return '#' + color;
                    }),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 10,
                            font: { size: 10, weight: '600' },
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        titleColor: '#2d3436',
                        bodyColor: '#636e72',
                        borderColor: '#ddd',
                        borderWidth: 1,
                        cornerRadius: 8
                    }
                },
                cutout: '60%'
            }
        });
    } else if (ctxStatus) {
        // Graphique par défaut si pas de données
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['En attente', 'Approuvé', 'Refusé'],
                datasets: [{
                    data: [45, 35, 20],
                    backgroundColor: ['#4facfe', '#11998e', '#f093fb'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 10,
                            font: { size: 10, weight: '600' },
                            usePointStyle: true
                        }
                    }
                },
                cutout: '60%'
            }
        });
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
        fetch('{{ route("comptoir.dashboard.realtime") }}')
            .then(response => response.json())
            .then(data => {
                console.log('📊 Stats comptoir mises à jour:', data);
                // Mettre à jour les statistiques temps réel sans recharger
            })
            .catch(error => console.log('Erreur actualisation comptoir:', error));
    }, 300000);

    function updateDestinations(destinations) {
        // Logique pour mettre à jour les destinations
        console.log('Mise à jour destinations:', destinations);
    }

    function updateActivities(activities) {
        // Logique pour mettre à jour les activités
        console.log('Mise à jour activités:', activities);
    }
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
    
    fetch('{{ route("comptoir.dashboard.realtime") }}')
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

// Fonctions d'actions
function refreshClientsList() {
    fetch('{{ route("comptoir.dashboard.profils") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Nouveaux clients:', data.profils);
                // Logique pour mettre à jour la liste des clients
            }
        })
        .catch(error => {
            console.error('Erreur actualisation clients:', error);
        });
}

function voirTousProfilsVisa() {
    window.location.href = '/profil-visa';
}

function accueillerClient(clientId) {
    // Logique pour accueillir un client
    console.log('Accueillir client:', clientId);
}

function traiterProfil(profilId) {
    window.location.href = `/profil-visa/edit/${profilId}`;
}

function voirDetails(profilId) {
    window.location.href = `/profil-visa/view/${profilId}`;
}
</script>

@endsection