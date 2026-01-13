/**
 * PSI AFRICA - DASHBOARD JAVASCRIPT
 * Gestion des graphiques et interactions
 */

class PSIDashboard {
    constructor() {
        this.charts = {};
        this.refreshInterval = null;
        this.init();
    }

    /**
     * Initialisation du dashboard
     */
    init() {
        console.log('🚀 Initialisation du Dashboard PSI Africa');
        
        // Configuration Chart.js
        this.configureChartJS();
        
        // Initialiser les graphiques
        this.initializeCharts();
        
        // Configurer l'actualisation automatique
        this.setupAutoRefresh();
        
        // Configurer les interactions
        this.setupInteractions();
        
        // Animations d'entrée
        this.setupAnimations();
        
        console.log('✅ Dashboard initialisé avec succès');
    }

    /**
     * Configuration globale de Chart.js
     */
    configureChartJS() {
        Chart.defaults.font.family = "'Segoe UI', system-ui, sans-serif";
        Chart.defaults.font.size = 12;
        Chart.defaults.color = '#6c757d';
        Chart.defaults.elements.point.radius = 4;
        Chart.defaults.elements.point.hoverRadius = 6;
        Chart.defaults.elements.line.tension = 0.4;
        Chart.defaults.plugins.legend.display = true;
        Chart.defaults.plugins.legend.position = 'top';
        Chart.defaults.responsive = true;
        Chart.defaults.maintainAspectRatio = false;
    }

    /**
     * Initialiser tous les graphiques
     */
    initializeCharts() {
        // Vérifier que les données sont disponibles
        if (!window.dashboardData) {
            console.error('❌ window.dashboardData non disponible');
            return;
        }

        console.log('📊 Initialisation des graphiques avec les données:', window.dashboardData);
        
        this.initMonthlyChart();
        this.initStatusChart();
        this.initDailyChart();
        this.initTypeChart();
    }

    /**
     * Graphique d'évolution mensuelle
     */
    initMonthlyChart() {
        const ctx = document.getElementById('monthlyChart');
        if (!ctx) {
            console.warn('⚠️ monthlyChart canvas non trouvé');
            return;
        }

        const data = window.dashboardData?.monthlyStats || [];
        console.log('📈 Données mensuelles:', data);
        
        this.charts.monthly = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(item => item.month),
                datasets: [{
                    label: 'Utilisateurs',
                    data: data.map(item => item.users || 0),
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }, {
                    label: 'Profils Visa',
                    data: data.map(item => item.profil_visa || 0),
                    borderColor: '#2ecc71',
                    backgroundColor: 'rgba(46, 204, 113, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#2ecc71',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    title: {
                        display: false
                    },
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#667eea',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.parsed.y}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)',
                            lineWidth: 1
                        },
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                elements: {
                    point: {
                        radius: 3,
                        hoverRadius: 8
                    }
                }
            }
        });
    }

    /**
     * Graphique de répartition par statut (Donut)
     */
    initStatusChart() {
        const ctx = document.getElementById('statusChart');
        if (!ctx) {
            console.warn('⚠️ statusChart canvas non trouvé');
            return;
        }

        const data = window.dashboardData?.profilVisaByStatus || [];
        console.log('🍩 Données statuts:', data);
        
        const colors = [
            '#667eea', '#2ecc71', '#f39c12', '#e74c3c', 
            '#9b59b6', '#1abc9c', '#34495e', '#95a5a6'
        ];

        this.charts.status = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.map(item => item.status_name || 'Non défini'),
                datasets: [{
                    data: data.map(item => item.total || 0),
                    backgroundColor: colors,
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#667eea',
                        borderWidth: 1,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return `${context.label}: ${context.parsed} (${percentage}%)`;
                            }
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });
    }

    /**
     * Graphique d'évolution journalière (Barres)
     */
    initDailyChart() {
        const ctx = document.getElementById('dailyChart');
        if (!ctx) {
            console.warn('⚠️ dailyChart canvas non trouvé');
            return;
        }

        const data = window.dashboardData?.dailyStats || [];
        console.log('📊 Données quotidiennes:', data);

        this.charts.daily = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(item => item.date),
                datasets: [{
                    label: 'Utilisateurs',
                    data: data.map(item => item.users || 0),
                    backgroundColor: 'rgba(102, 126, 234, 0.8)',
                    borderColor: '#667eea',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                }, {
                    label: 'Profils Visa',
                    data: data.map(item => item.profil_visa || 0),
                    backgroundColor: 'rgba(46, 204, 113, 0.8)',
                    borderColor: '#2ecc71',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#667eea',
                        borderWidth: 1,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        },
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    /**
     * Graphique des types de profils (Barres horizontales)
     */
    initTypeChart() {
        const ctx = document.getElementById('typeChart');
        if (!ctx) {
            console.warn('⚠️ typeChart canvas non trouvé');
            return;
        }

        const data = window.dashboardData?.profilVisaByType || [];
        console.log('📋 Données types:', data);
        
        const colors = [
            '#667eea', '#2ecc71', '#f39c12', '#e74c3c', 
            '#9b59b6', '#1abc9c', '#34495e', '#95a5a6'
        ];

        this.charts.type = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(item => this.getTypeLabel(item.type_profil_visa)),
                datasets: [{
                    label: 'Nombre',
                    data: data.map(item => item.total || 0),
                    backgroundColor: colors,
                    borderColor: colors,
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#667eea',
                        borderWidth: 1,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        },
                        ticks: {
                            precision: 0
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    /**
     * Configuration de l'actualisation automatique
     */
    setupAutoRefresh() {
        // Actualiser les statistiques toutes les 5 minutes
        this.refreshInterval = setInterval(() => {
            this.refreshRealtimeStats();
        }, 300000);

        // Première actualisation après 30 secondes
        setTimeout(() => {
            this.refreshRealtimeStats();
        }, 30000);
    }

    /**
     * Actualiser les statistiques en temps réel
     */
    async refreshRealtimeStats() {
        try {
            const response = await fetch('/dashboard/realtime-stats');
            const data = await response.json();
            
            // Mettre à jour les compteurs
            this.updateCounters(data);
            
            console.log('📊 Statistiques actualisées:', data);
        } catch (error) {
            console.error('❌ Erreur lors de l\'actualisation:', error);
        }
    }

    /**
     * Mettre à jour les compteurs visuels
     */
    updateCounters(data) {
        const counters = {
            'total_users': data.total_users,
            'total_profil_visa': data.total_profil_visa,
            'new_users_today': data.new_users_today,
            'new_profil_visa_today': data.new_profil_visa_today
        };

        Object.entries(counters).forEach(([key, value]) => {
            const element = document.querySelector(`[data-counter="${key}"]`);
            if (element) {
                this.animateCounter(element, parseInt(element.textContent.replace(/\D/g, '')), value);
            }
        });
    }

    /**
     * Animation des compteurs
     */
    animateCounter(element, start, end, duration = 1000) {
        const startTime = performance.now();
        const difference = end - start;

        const step = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const current = Math.floor(start + (difference * this.easeOutQuart(progress)));
            element.textContent = this.formatNumber(current);

            if (progress < 1) {
                requestAnimationFrame(step);
            }
        };

        requestAnimationFrame(step);
    }

    /**
     * Fonction d'easing pour l'animation
     */
    easeOutQuart(t) {
        return 1 - Math.pow(1 - t, 4);
    }

    /**
     * Formater les nombres
     */
    formatNumber(num) {
        return new Intl.NumberFormat('fr-FR').format(num);
    }

    /**
     * Configuration des interactions
     */
    setupInteractions() {
        // Bouton d'actualisation
        const refreshBtn = document.querySelector('[data-action="refresh"]');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                this.refreshDashboard();
            });
        }

        // Bouton d'export
        const exportBtn = document.querySelector('[data-action="export"]');
        if (exportBtn) {
            exportBtn.addEventListener('click', () => {
                this.exportDashboard();
            });
        }

        // Tooltips pour les graphiques
        this.setupTooltips();
    }

    /**
     * Configuration des tooltips
     */
    setupTooltips() {
        // Initialiser les tooltips Bootstrap si disponible
        if (typeof bootstrap !== 'undefined') {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    }

    /**
     * Animations d'entrée
     */
    setupAnimations() {
        // Observer pour les animations à l'scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                }
            });
        }, { threshold: 0.1 });

        // Observer tous les éléments avec animation
        document.querySelectorAll('.stats-card, .chart-card, .activity-item').forEach(el => {
            observer.observe(el);
        });
    }

    /**
     * Actualiser le dashboard complet
     */
    refreshDashboard() {
        const btn = document.querySelector('[data-action="refresh"]');
        if (btn) {
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualisation...';
            btn.disabled = true;
        }

        setTimeout(() => {
            location.reload();
        }, 1000);
    }

    /**
     * Export du dashboard
     */
    exportDashboard() {
        const btn = document.querySelector('[data-action="export"]');
        if (btn) {
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Export...';
            btn.disabled = true;
        }

        // Simuler l'export - à remplacer par la vraie logique
        setTimeout(() => {
            alert('Fonction d\'export en cours de développement');
            if (btn) {
                btn.innerHTML = '<i class="fas fa-download"></i> Exporter';
                btn.disabled = false;
            }
        }, 2000);
    }

    /**
     * Obtenir le libellé du type de profil
     */
    getTypeLabel(type) {
        const types = {
            1: 'Tourisme',
            2: 'Affaires',
            3: 'Transit',
            4: 'Étudiant',
            5: 'Travail',
            6: 'Famille',
            7: 'Autre'
        };
        return types[type] || 'Non défini';
    }

    /**
     * Détruire le dashboard (nettoyage)
     */
    destroy() {
        // Nettoyer les intervalles
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }

        // Détruire les graphiques
        Object.values(this.charts).forEach(chart => {
            if (chart) {
                chart.destroy();
            }
        });

        console.log('🧹 Dashboard nettoyé');
    }
}

// Fonctions utilitaires globales
window.PSIDashboardUtils = {
    /**
     * Calculer le total des réservations
     */
    calculateTotalReservations() {
        if (window.dashboard && window.dashboard.charts.daily) {
            const chart = window.dashboard.charts.daily;
            let total = 0;
            let count = 0;
            
            chart.data.datasets.forEach(dataset => {
                dataset.data.forEach(value => {
                    if (value && !isNaN(parseFloat(value))) {
                        total += parseFloat(value);
                        count++;
                    }
                });
            });
            
            console.log('Total calculé:', total, 'Nombre d\'éléments:', count);
            return { total, count };
        }
        return { total: 0, count: 0 };
    },

    /**
     * Filtrer par statut
     */
    filterByStatus(status) {
        console.log('Filtrage par statut:', status);
        // Logique de filtrage à implémenter
    },

    /**
     * Exporter les données en CSV
     */
    exportToCSV(data, filename = 'dashboard_data.csv') {
        const csv = this.convertToCSV(data);
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.setAttribute('hidden', '');
        a.setAttribute('href', url);
        a.setAttribute('download', filename);
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    },

    /**
     * Convertir les données en CSV
     */
    convertToCSV(data) {
        if (!data || !data.length) return '';
        
        const headers = Object.keys(data[0]);
        const csvContent = [
            headers.join(','),
            ...data.map(row => headers.map(header => `"${row[header]}"`).join(','))
        ].join('\n');
        
        return csvContent;
    }
};

// Initialisation automatique quand le DOM est prêt
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 DOM ready, initialisation du dashboard...');
    
    // Diagnostic initial
    console.log('=== DIAGNOSTIC DASHBOARD ===');
    console.log('Chart.js disponible:', typeof Chart !== 'undefined');
    console.log('Window.dashboardData:', !!window.dashboardData);
    console.log('Canvas monthlyChart:', !!document.getElementById('monthlyChart'));
    console.log('Canvas statusChart:', !!document.getElementById('statusChart'));
    console.log('Canvas dailyChart:', !!document.getElementById('dailyChart'));
    console.log('Canvas typeChart:', !!document.getElementById('typeChart'));
    
    if (window.dashboardData) {
        console.log('Données reçues:');
        console.log('- monthlyStats:', window.dashboardData.monthlyStats?.length || 0, 'éléments');
        console.log('- profilVisaByStatus:', window.dashboardData.profilVisaByStatus?.length || 0, 'éléments');
        console.log('- dailyStats:', window.dashboardData.dailyStats?.length || 0, 'éléments');
        console.log('- profilVisaByType:', window.dashboardData.profilVisaByType?.length || 0, 'éléments');
    }
    
    // Vérifier si nous sommes sur la page dashboard
    if (document.getElementById('monthlyChart') || document.querySelector('.dashboard-container')) {
        if (typeof Chart !== 'undefined') {
            console.log('✅ Initialisation du dashboard PSI Africa');
            window.dashboard = new PSIDashboard();
            
            // Diagnostic après initialisation
            setTimeout(() => {
                console.log('=== POST-INITIALISATION ===');
                console.log('Charts créés:', Object.keys(window.dashboard.charts));
                console.log('Actualisation automatique active:', !!window.dashboard.refreshInterval);
            }, 1000);
        } else {
            console.error('❌ Chart.js non disponible, impossible d\'initialiser les graphiques');
            // Afficher un message d'erreur à l'utilisateur
            const container = document.querySelector('.dashboard-container') || document.body;
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-danger mt-3';
            errorDiv.innerHTML = `
                <h5><i class="fas fa-exclamation-triangle"></i> Erreur de Chargement</h5>
                <p>Les graphiques ne peuvent pas s'afficher car Chart.js n'est pas chargé. 
                Veuillez vérifier votre connexion internet et rafraîchir la page.</p>
            `;
            container.insertBefore(errorDiv, container.firstChild);
        }
    }
});

// Nettoyage avant le déchargement de la page
window.addEventListener('beforeunload', function() {
    if (window.dashboard) {
        window.dashboard.destroy();
    }
});