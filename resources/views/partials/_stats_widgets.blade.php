{{-- 
    Partial: Widgets de Statistiques PSI Africa
    Contient les cartes de statistiques principales du dashboard
--}}

<!-- Cartes de Statistiques Principales -->
<div class="row mb-4">
    <!-- Utilisateurs Total -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card border-0 shadow-sm fade-in-up">
            <div class="card-body">
                <div class="stats-icon bg-psi-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number" data-counter="total_users">{{ number_format($totalUsers) }}</h3>
                    <p class="stats-label">Utilisateurs Total</p>
                    <div class="stats-trend">
                        <span class="trend-indicator {{ $newUsersToday > 0 ? 'positive' : 'negative' }}">
                            <i class="fas fa-arrow-{{ $newUsersToday > 0 ? 'up' : 'down' }}"></i> 
                            {{ $newUsersToday }}
                        </span>
                        <span class="trend-text">aujourd'hui</span>
                    </div>
                </div>
            </div>
            <!-- Indicateur de progression -->
            <div class="card-footer bg-transparent border-0 p-0">
                <div class="progress" style="height: 3px;">
                    <div class="progress-bar bg-psi-primary" 
                         role="progressbar" 
                         style="width: {{ $totalUsers > 0 ? min(($newUsersThisMonth / max($totalUsers, 1)) * 100, 100) : 0 }}%">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profils Visa Total -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card border-0 shadow-sm fade-in-up">
            <div class="card-body">
                <div class="stats-icon bg-psi-success">
                    <i class="fas fa-passport"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number" data-counter="total_profil_visa">{{ number_format($totalProfilVisa) }}</h3>
                    <p class="stats-label">Profils Visa</p>
                    <div class="stats-trend">
                        <span class="trend-indicator {{ $newProfilVisaToday > 0 ? 'positive' : 'negative' }}">
                            <i class="fas fa-arrow-{{ $newProfilVisaToday > 0 ? 'up' : 'down' }}"></i> 
                            {{ $newProfilVisaToday }}
                        </span>
                        <span class="trend-text">aujourd'hui</span>
                    </div>
                </div>
            </div>
            <!-- Indicateur de progression -->
            <div class="card-footer bg-transparent border-0 p-0">
                <div class="progress" style="height: 3px;">
                    <div class="progress-bar bg-psi-success" 
                         role="progressbar" 
                         style="width: {{ $totalProfilVisa > 0 ? min(($newProfilVisaThisMonth / max($totalProfilVisa, 1)) * 100, 100) : 0 }}%">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Taux de Réussite -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card border-0 shadow-sm fade-in-up">
            <div class="card-body">
                <div class="stats-icon bg-psi-info">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number">{{ $successRate }}%</h3>
                    <p class="stats-label">Taux de Réussite</p>
                    <div class="stats-trend">
                        <span class="trend-indicator {{ $successRate >= 70 ? 'positive' : 'negative' }}">
                            <i class="fas fa-{{ $successRate >= 70 ? 'thumbs-up' : 'thumbs-down' }}"></i>
                        </span>
                        <span class="trend-text">
                            {{ $successRate >= 90 ? 'Excellent' : ($successRate >= 70 ? 'Bon' : 'À améliorer') }}
                        </span>
                    </div>
                </div>
            </div>
            <!-- Indicateur circulaire de progression -->
            <div class="card-footer bg-transparent border-0 p-0">
                <div class="progress" style="height: 3px;">
                    <div class="progress-bar bg-psi-info" 
                         role="progressbar" 
                         style="width: {{ $successRate }}%">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Temps Moyen de Traitement -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card border-0 shadow-sm fade-in-up">
            <div class="card-body">
                <div class="stats-icon bg-psi-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number">{{ round($avgProcessingTime, 1) }}</h3>
                    <p class="stats-label">Jours Moyen</p>
                    <div class="stats-trend">
                        <span class="trend-indicator {{ $avgProcessingTime <= 7 ? 'positive' : 'negative' }}">
                            <i class="fas fa-{{ $avgProcessingTime <= 7 ? 'clock' : 'exclamation-triangle' }}"></i>
                        </span>
                        <span class="trend-text">
                            {{ $avgProcessingTime <= 5 ? 'Rapide' : ($avgProcessingTime <= 10 ? 'Normal' : 'Lent') }}
                        </span>
                    </div>
                </div>
            </div>
            <!-- Indicateur de temps -->
            <div class="card-footer bg-transparent border-0 p-0">
                <div class="progress" style="height: 3px;">
                    <div class="progress-bar {{ $avgProcessingTime <= 7 ? 'bg-success' : ($avgProcessingTime <= 14 ? 'bg-warning' : 'bg-danger') }}" 
                         role="progressbar" 
                         style="width: {{ min($avgProcessingTime * 5, 100) }}%">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques Complémentaires -->
<div class="row mb-4">
    <!-- Demandes en Attente -->
    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="stats-icon-small bg-warning mx-auto mb-2">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <h4 class="mb-1 text-warning">{{ $pendingVisas }}</h4>
                <small class="text-muted">En Attente</small>
            </div>
        </div>
    </div>

    <!-- Demandes Urgentes -->
    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="stats-icon-small bg-danger mx-auto mb-2">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h4 class="mb-1 text-danger">{{ $urgentVisas }}</h4>
                <small class="text-muted">Urgentes</small>
            </div>
        </div>
    </div>

    <!-- Cette Semaine - Utilisateurs -->
    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="stats-icon-small bg-primary mx-auto mb-2">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h4 class="mb-1 text-primary">{{ $newUsersThisWeek }}</h4>
                <small class="text-muted">Users/Semaine</small>
            </div>
        </div>
    </div>

    <!-- Cette Semaine - Profils -->
    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="stats-icon-small bg-success mx-auto mb-2">
                    <i class="fas fa-file-plus"></i>
                </div>
                <h4 class="mb-1 text-success">{{ $newProfilVisaThisWeek }}</h4>
                <small class="text-muted">Profils/Semaine</small>
            </div>
        </div>
    </div>

    <!-- Ce Mois - Utilisateurs -->
    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="stats-icon-small bg-info mx-auto mb-2">
                    <i class="fas fa-users"></i>
                </div>
                <h4 class="mb-1 text-info">{{ $newUsersThisMonth }}</h4>
                <small class="text-muted">Users/Mois</small>
            </div>
        </div>
    </div>

    <!-- Ce Mois - Profils -->
    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="stats-icon-small bg-secondary mx-auto mb-2">
                    <i class="fas fa-passport"></i>
                </div>
                <h4 class="mb-1 text-secondary">{{ $newProfilVisaThisMonth }}</h4>
                <small class="text-muted">Profils/Mois</small>
            </div>
        </div>
    </div>
</div>

<!-- Styles pour les petites icônes -->
<style>
.stats-icon-small {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.stats-card {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--bs-primary), var(--bs-info));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.stats-card:hover::before {
    opacity: 1;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
}

.progress {
    border-radius: 0;
}

.progress-bar {
    transition: width 1s ease-in-out;
}

/* Animation pour les compteurs */
.stats-number {
    counter-reset: num var(--num);
    animation: counter 2s ease-out;
}

@keyframes counter {
    from {
        --num: 0;
    }
    to {
        --num: var(--target);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .stats-card .card-body {
        flex-direction: column;
        text-align: center;
    }
    
    .stats-icon {
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .stats-number {
        font-size: 2rem;
    }
}
</style>

<!-- Script pour l'animation des compteurs -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation des compteurs au chargement
    const counters = document.querySelectorAll('[data-counter]');
    
    const animateCounter = (element, target, duration = 2000) => {
        let start = 0;
        const increment = target / (duration / 16);
        
        const timer = setInterval(() => {
            start += increment;
            element.textContent = Math.floor(start).toLocaleString('fr-FR');
            
            if (start >= target) {
                element.textContent = target.toLocaleString('fr-FR');
                clearInterval(timer);
            }
        }, 16);
    };
    
    // Observer pour déclencher l'animation quand visible
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.dataset.animated) {
                const target = parseInt(entry.target.textContent.replace(/\D/g, ''));
                entry.target.dataset.animated = 'true';
                entry.target.textContent = '0';
                setTimeout(() => animateCounter(entry.target, target), 200);
            }
        });
    });
    
    counters.forEach(counter => observer.observe(counter));
});
</script>