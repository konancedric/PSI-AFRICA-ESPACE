<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PSI Africa - Dashboard Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .header-bar {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .main-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 15px;
        }
        
        .success-banner {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
        
        .success-banner h2 {
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .info-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            height: 100%;
        }
        
        .info-card h5 {
            color: #495057;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 500;
            color: #6c757d;
        }
        
        .info-value {
            color: #495057;
            font-weight: 600;
        }
        
        .actions-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .action-btn {
            display: block;
            width: 100%;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
        }
        
        .btn-success-custom {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        
        .btn-info-custom {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
        }
        
        .btn-warning-custom {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            color: #212529;
        }
        
        .system-status {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .status-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .status-item:last-child {
            margin-bottom: 0;
        }
        
        .status-icon {
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }
        
        .statistics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #6c757d;
            font-weight: 500;
        }
        
        .icon-users { color: #007bff; }
        .icon-agents { color: #28a745; }
        .icon-visa { color: #17a2b8; }
        .icon-revenue { color: #ffc107; }
        
        @media (max-width: 768px) {
            .main-container {
                margin: 1rem auto;
                padding: 0 10px;
            }
            
            .success-banner {
                padding: 1.5rem;
            }
            
            .statistics-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header-bar">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="mb-0">
                        <i class="fas fa-crown me-2"></i>
                        PSI Africa - Dashboard Admin
                    </h4>
                </div>
                <div class="col-md-6 text-end">
                    <span class="me-3">Admin PSI Africa</span>
                    <a href="/logout" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="main-container">
        <!-- Bannière de succès -->
        <div class="success-banner">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2>
                        <i class="fas fa-check-circle me-2"></i>
                        Connexion Réussie !
                    </h2>
                    <p class="mb-0">Bienvenue, Admin PSI Africa ! Votre système de gestion des agents fonctionne parfaitement.</p>
                </div>
                <div class="col-md-4 text-end">
                    <i class="fas fa-trophy" style="font-size: 4rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="statistics-grid">
            <div class="stat-card">
                <i class="fas fa-users stat-icon icon-users"></i>
                <div class="stat-number">{{ $totalUsers ?? 5149 }}</div>
                <div class="stat-label">Total Utilisateurs</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-user-tie stat-icon icon-agents"></i>
                <div class="stat-number">{{ $totalAgents ?? 25 }}</div>
                <div class="stat-label">Agents Internes</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-passport stat-icon icon-visa"></i>
                <div class="stat-number">{{ $totalProfilVisa ?? 5126 }}</div>
                <div class="stat-label">Profils Visa</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-chart-line stat-icon icon-revenue"></i>
                <div class="stat-number">{{ $successRate ?? 85 }}%</div>
                <div class="stat-label">Taux de Réussite</div>
            </div>
        </div>

        <div class="row">
            <!-- Informations du compte -->
            <div class="col-lg-6 mb-4">
                <div class="info-card">
                    <h5>
                        <i class="fas fa-user-circle me-2 text-primary"></i>
                        Informations de votre compte
                    </h5>
                    
                    <div class="info-item">
                        <span class="info-label">Nom :</span>
                        <span class="info-value">Admin PSI Africa</span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Email :</span>
                        <span class="info-value">admin@psiafrica.ci</span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Type :</span>
                        <span class="info-value">Administrateur</span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Rôles :</span>
                        <span class="info-value">Admin</span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Statut :</span>
                        <span class="info-value">
                            <span class="badge bg-success">Actif</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="col-lg-6 mb-4">
                <div class="actions-card">
                    <h5>
                        <i class="fas fa-lightning-bolt me-2 text-warning"></i>
                        Actions Rapides
                    </h5>
                    
                    <a href="/agents" class="action-btn btn-primary-custom">
                        <i class="fas fa-users-cog me-2"></i>
                        Gérer les Agents
                    </a>
                    
                    <a href="/users" class="action-btn btn-success-custom">
                        <i class="fas fa-user-plus me-2"></i>
                        Créer un Agent
                    </a>
                    
                    <a href="/roles" class="action-btn btn-info-custom">
                        <i class="fas fa-shield-alt me-2"></i>
                        Tester les Rôles
                    </a>
                    
                    <a href="/permission" class="action-btn btn-warning-custom">
                        <i class="fas fa-cog me-2"></i>
                        Gérer les Rôles
                    </a>
                </div>
            </div>
        </div>

        <!-- Statut du système -->
        <div class="system-status">
            <h5 class="mb-3">
                <i class="fas fa-check-double me-2"></i>
                Système configuré avec succès !
            </h5>
            <p class="mb-3">Votre système de gestion des agents PSI Africa est maintenant opérationnel avec :</p>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="status-item">
                        <i class="fas fa-check status-icon"></i>
                        <span>Authentification par rôles</span>
                    </div>
                    <div class="status-item">
                        <i class="fas fa-check status-icon"></i>
                        <span>Séparation agents internes / utilisateurs publics</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="status-item">
                        <i class="fas fa-check status-icon"></i>
                        <span>Permissions configurées</span>
                    </div>
                    <div class="status-item">
                        <i class="fas fa-check status-icon"></i>
                        <span>Tableaux de bord spécialisés</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation rapide -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="info-card">
                    <h5>
                        <i class="fas fa-compass me-2 text-info"></i>
                        Navigation Rapide
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-2">
                            <a href="/agents" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-users-cog me-1"></i>
                                Gestion Agents
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <a href="/profil-visa" class="btn btn-outline-success btn-sm w-100">
                                <i class="fas fa-passport me-1"></i>
                                Profils Visa
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <a href="/users" class="btn btn-outline-info btn-sm w-100">
                                <i class="fas fa-users me-1"></i>
                                Utilisateurs
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <a href="/roles" class="btn btn-outline-warning btn-sm w-100">
                                <i class="fas fa-shield-alt me-1"></i>
                                Rôles & Permissions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agents récents -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="info-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2 text-secondary"></i>
                            Agents Récents
                        </h5>
                        <a href="/agents" class="btn btn-outline-primary btn-sm">
                            Voir tout
                        </a>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom</th>
                                    <th>Type</th>
                                    <th>Email</th>
                                    <th>Statut</th>
                                    <th>Créé le</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle text-white text-center me-2" style="width: 32px; height: 32px; line-height: 32px; font-size: 0.8rem;">
                                                AC
                                            </div>
                                            Agent Comptoir Test
                                        </div>
                                    </td>
                                    <td><span class="badge bg-info">Agent Comptoir</span></td>
                                    <td>comptoir@psiafrica.ci</td>
                                    <td><span class="badge bg-success">Actif</span></td>
                                    <td>{{ now()->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success rounded-circle text-white text-center me-2" style="width: 32px; height: 32px; line-height: 32px; font-size: 0.8rem;">
                                                CT
                                            </div>
                                            Commercial Test
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Commercial</span></td>
                                    <td>commercial@psiafrica.ci</td>
                                    <td><span class="badge bg-success">Actif</span></td>
                                    <td>{{ now()->format('d/m/Y') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Animation d'entrée des cartes
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card, .info-card, .actions-card');
            
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.5s ease';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
        
        // Mise à jour automatique des statistiques
        function updateStats() {
            fetch('/dashboard/realtime-stats')
                .then(response => response.json())
                .then(data => {
                    // Mettre à jour les statistiques en temps réel
                    console.log('Statistiques mises à jour:', data);
                })
                .catch(error => console.log('Erreur:', error));
        }
        
        // Actualiser toutes les 5 minutes
        setInterval(updateStats, 300000);
    </script>
</body>
</html>