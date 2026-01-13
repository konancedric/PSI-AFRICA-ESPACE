<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Système de gestion de caisse PSI - Version 3.0">
    <title>Gestion de Caisse PSI v3.0</title>
    <link rel="icon" href="{{ asset('favicon.png') }}"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --primary-light: #3b82f6;
            --secondary: #7c3aed;
            --success: #10b981;
            --success-light: #34d399;
            --danger: #ef4444;
            --danger-light: #f87171;
            --warning: #f59e0b;
            --warning-light: #fbbf24;
            --info: #06b6d4;
            --info-light: #22d3ee;
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --bg-hover: #f1f5f9;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border: #e2e8f0;
            --border-light: #f1f5f9;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        [data-theme="dark"] {
            --bg-main: #0f172a;
            --bg-card: #1e293b;
            --bg-hover: #334155;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --border: #334155;
            --border-light: #1e293b;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            background: var(--bg-main);
            color: var(--text-primary);
            line-height: 1.6;
            transition: background 0.3s, color 0.3s;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            font-size: 15px;
        }

        .toast-container { 
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            max-width: 350px;
        }
        
        .toast {
            background: var(--bg-card);
            padding: 16px 20px;
            margin-bottom: 12px;
            border-radius: 10px;
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideInRight 0.3s ease;
            border-left: 4px solid var(--primary);
            backdrop-filter: blur(10px);
        }
        
        .toast.success { border-left-color: var(--success); }
        .toast.error { border-left-color: var(--danger); }
        .toast.warning { border-left-color: var(--warning); }
        
        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .toast-close {
            margin-left: auto;
            cursor: pointer;
            opacity: 0.5;
            font-size: 20px;
            border: none;
            background: none;
            color: var(--text-primary);
        }

        .app { 
            display: none;
        }
        
        .app.active { 
            display: block;
        }
        
        .container { 
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 28px 32px;
            margin-bottom: 32px;
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-left h1 {
            font-size: 2em;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.025em;
        }

        .header-left p {
            opacity: 0.95;
            font-size: 0.95em;
            font-weight: 400;
            letter-spacing: -0.01em;
        }
        
        .header-right { 
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .theme-toggle {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }

        .theme-toggle:hover {
            background: rgba(255,255,255,0.3);
        }

        .tabs { 
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .tab-btn {
            padding: 14px 28px;
            background: var(--bg-card);
            border: 2px solid var(--border);
            border-radius: 10px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            color: var(--text-primary);
            letter-spacing: -0.01em;
        }

        .tab-btn:hover:not(:disabled) {
            background: var(--bg-hover);
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .tab-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: var(--shadow-md);
        }
        
        .tab-btn:disabled { 
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .tab-content { 
            display: none;
        }
        
        .tab-content.active { 
            display: block;
            animation: fadeIn 0.3s;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            background: var(--bg-card);
            padding: 28px;
            border-radius: 16px;
            box-shadow: var(--shadow);
            margin-bottom: 28px;
            border: 1px solid var(--border);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card:hover {
            box-shadow: var(--shadow-md);
            border-color: var(--border-light);
        }

        .card h2 {
            color: var(--text-primary);
            margin-bottom: 24px;
            font-size: 1.5em;
            font-weight: 700;
            letter-spacing: -0.025em;
        }

        .kpi-grid { 
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .kpi-card {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 24px;
            border-radius: 14px;
            box-shadow: var(--shadow-md);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .kpi-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg);
        }
        
        .kpi-card.success {
            background: linear-gradient(135deg, #059669 0%, var(--success) 100%);
        }

        .kpi-card.warning {
            background: linear-gradient(135deg, var(--danger) 0%, var(--danger-light) 100%);
        }

        .kpi-card.info {
            background: linear-gradient(135deg, #0891b2 0%, var(--info) 100%);
        }

        .kpi-card.dime {
            background: linear-gradient(135deg, #d946ef 0%, #f97316 100%);
        }

        .kpi-card.cabinet {
            background: linear-gradient(135deg, #06b6d4 0%, #8b5cf6 100%);
        }
        
        .kpi-label {
            font-size: 0.875em;
            opacity: 0.95;
            margin-bottom: 8px;
            font-weight: 500;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .kpi-value {
            font-size: 2em;
            font-weight: 800;
            letter-spacing: -0.025em;
        }

        .kpi-sub {
            font-size: 0.8em;
            opacity: 0.85;
            margin-top: 8px;
            font-weight: 400;
        }

        .form-grid { 
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .form-group { 
            display: flex;
            flex-direction: column;
        }
        
        label {
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-primary);
            font-size: 14px;
            letter-spacing: -0.01em;
        }

        input, select, textarea {
            padding: 12px 14px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            background: var(--bg-card);
            color: var(--text-primary);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
        }

        input:hover, select:hover, textarea:hover {
            border-color: var(--text-muted);
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            background: var(--bg-card);
        }

        .input-error {
            border-color: var(--danger) !important;
        }

        .error-message {
            color: var(--danger);
            font-size: 12px;
            margin-top: 4px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            letter-spacing: -0.01em;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: var(--shadow);
        }

        .btn-primary:hover:not(:disabled) {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.25);
        }
        
        .btn-success {
            background: var(--success);
            color: white;
            box-shadow: var(--shadow);
        }

        .btn-success:hover:not(:disabled) {
            background: var(--success-light);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.25);
        }

        .btn-danger {
            background: var(--danger);
            color: white;
            box-shadow: var(--shadow);
        }

        .btn-danger:hover:not(:disabled) {
            background: var(--danger-light);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(239, 68, 68, 0.25);
        }

        .btn-secondary {
            background: var(--text-secondary);
            color: white;
            box-shadow: var(--shadow);
        }

        .btn-secondary:hover:not(:disabled) {
            background: var(--text-primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-warning {
            background: var(--warning);
            color: white;
            box-shadow: var(--shadow);
        }

        .btn-warning:hover:not(:disabled) {
            background: var(--warning-light);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(245, 158, 11, 0.25);
        }
        
        .btn-group { 
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn-logout { 
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border);
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        th {
            background: var(--bg-hover);
            font-weight: 700;
            color: var(--text-primary);
            font-size: 14px;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        tr:hover {
            background: var(--bg-hover);
            transition: background 0.2s ease;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin-top: 24px;
        }

        .pagination button {
            padding: 10px 16px;
            border: 2px solid var(--border);
            background: var(--bg-card);
            border-radius: 8px;
            cursor: pointer;
            color: var(--text-primary);
            font-weight: 600;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .pagination button:hover:not(:disabled) {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .pagination button:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }
        
        .pagination span { 
            font-weight: 600;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(8px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--bg-card);
            padding: 32px;
            border-radius: 20px;
            max-width: 550px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--border);
        }

        .modal-header h3 {
            color: var(--text-primary);
            font-weight: 700;
            font-size: 1.5em;
            letter-spacing: -0.025em;
        }

        .close-modal {
            background: var(--bg-hover);
            border: none;
            font-size: 22px;
            cursor: pointer;
            color: var(--text-primary);
            width: 36px;
            height: 36px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .close-modal:hover {
            background: var(--danger);
            color: white;
            transform: rotate(90deg);
        }

        .filters {
            background: var(--bg-card);
            padding: 24px;
            border-radius: 14px;
            margin-bottom: 24px;
            border: 2px solid var(--border);
            box-shadow: var(--shadow-sm);
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .chart-canvas {
            width: 100%;
            height: 300px;
            border: 2px solid var(--border);
            border-radius: 14px;
            background: var(--bg-card);
            padding: 16px;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .badge-doc {
            background: var(--primary);
            color: white;
        }

        .badge-cabinet {
            background: var(--success);
            color: white;
        }

        .badge-autre {
            background: var(--text-secondary);
            color: white;
        }

        .badge-admin {
            background: var(--danger);
            color: white;
        }
        
        .badge-agent { 
            background: var(--info);
            color: white;
        }
        
        .badge-active { 
            background: var(--success);
            color: white;
        }
        
        .badge-blocked { 
            background: var(--danger);
            color: white;
        }
        
        .badge-info {
            background: var(--info);
            color: white;
        }

        .spinner {
            border: 3px solid var(--border);
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Scrollbar personnalisé */
        ::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-main);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--text-muted);
            border-radius: 10px;
            border: 2px solid var(--bg-main);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-secondary);
        }

        /* Animation de chargement */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* États des inputs */
        input:read-only, select:disabled {
            background: var(--bg-hover);
            cursor: not-allowed;
            opacity: 0.7;
        }

        /* Focus visible pour l'accessibilité */
        button:focus-visible, a:focus-visible {
            outline: 3px solid var(--primary);
            outline-offset: 2px;
        }

        @media (max-width: 768px) {
            .form-grid, .kpi-grid {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 13px;
            }

            th, td {
                padding: 10px;
            }

            .header-right {
                width: 100%;
                justify-content: space-between;
            }

            header {
                padding: 20px;
            }

            .header-left h1 {
                font-size: 1.5em;
            }

            .card {
                padding: 20px;
            }

            .btn {
                font-size: 14px;
                padding: 10px 20px;
            }

            .modal-content {
                padding: 24px;
                width: 95%;
            }

            .tab-btn {
                padding: 10px 20px;
                font-size: 14px;
            }
        }

        @media print {
            body { 
                background: white;
            }
            
            .tabs, .btn, .filters, .no-print, 
            header .header-right, .action-btns, .pagination { 
                display: none !important;
            }
            
            .card { 
                box-shadow: none;
                page-break-inside: avoid;
                border: 1px solid #ddd;
            }
            
            .app { 
                display: block !important;
            }
        }
    </style>
</head>
<body>
    <div class="toast-container" id="toastContainer"></div>

    <div class="app active" id="mainApp">
        <div class="container">
            <header>
                <div class="header-left">
                    <h1>💰 Gestion de Caisse</h1>
                    <p>Système PSI v3.0 - Amélioré</p>
                </div>
                <div class="header-right">
                    <button class="theme-toggle" onclick="toggleTheme()">🌙 Thème</button>
                    <div class="user-info">
                        <div class="username" id="currentUsername">-</div>
                        <div class="role" id="currentUserRole">-</div>
                    </div>
                    <button class="btn btn-logout" onclick="logout()">🚪 Déconnexion</button>
                </div>
            </header>

            <div class="tabs" role="tablist">
                <button class="tab-btn active" id="tabDashboard" onclick="switchTab('dashboard')">
                    📊 Dashboard
                </button>
                <button class="tab-btn" id="tabEntrees" onclick="switchTab('entrees')">
                    ➕ Entrées
                </button>
                <button class="tab-btn" id="tabSorties" onclick="switchTab('sorties')">
                    ➖ Sorties
                </button>
                <button class="tab-btn" id="tabDepenses" onclick="switchTab('depenses')" style="display: none;">
                    💳 Dépenses
                </button>
                <button class="tab-btn" id="tabSettings" onclick="switchTab('settings')">
                    ⚙️ Paramètres
                </button>
            </div>

            <div id="dashboard" class="tab-content active">
                <div class="card no-print">
                    <h2>Filtres et Recherche</h2>
                    <div class="filters">
                        <div class="filters-grid">
                            <div class="form-group">
                                <label for="filterMois">📅 Mois/Année</label>
                                <select id="filterMois" onchange="chargerDonneesMois()" style="font-weight: 600; color: var(--primary);">
                                    <option value="">Chargement...</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="filterDateDebut">Du</label>
                                <input type="date" id="filterDateDebut">
                            </div>
                            <div class="form-group">
                                <label for="filterDateFin">Au</label>
                                <input type="date" id="filterDateFin">
                            </div>
                            <div class="form-group">
                                <label for="filterNature">Nature</label>
                                <select id="filterNature">
                                    <option value="">Toutes</option>
                                    <option value="Frais de Cabinet">Frais de Cabinet</option>
                                    <option value="Documents de Voyage">Documents de Voyage</option>
                                    <option value="Autres Documents">Autres Documents</option>
                                    <option value="Autre Entrée">Autre Entrée</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="filterMode">Mode de paiement</label>
                                <select id="filterMode">
                                    <option value="">Tous</option>
                                    <option value="Espèces">Espèces</option>
                                    <option value="Mobile Money">Mobile Money</option>
                                    <option value="Virement">Virement</option>
                                    <option value="Chèque">Chèque</option>
                                    <option value="Carte">Carte</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="filterSearch">Recherche</label>
                                <input 
                                    type="text" 
                                    id="filterSearch" 
                                    placeholder="Nom, prénom, réf...">
                            </div>
                        </div>
                        <div class="btn-group" style="margin-top: 15px;">
                            <button class="btn btn-primary" onclick="applyFilters()">
                                Appliquer
                            </button>
                            <button class="btn btn-secondary" onclick="resetFilters()">
                                Réinitialiser
                            </button>
                        </div>
                    </div>
                </div>

                <div class="kpi-grid" id="kpiGrid"></div>

                <div class="card">
                    <h2>Graphique : Encaissements / Décaissements / Solde</h2>
                    <canvas id="chartCashFlow" class="chart-canvas"></canvas>
                </div>

                <div class="card">
                    <h2>Répartition par Nature</h2>
                    <canvas id="chartNature" class="chart-canvas"></canvas>
                </div>

                <div class="card">
                    <h2>Évolution Marge & Dîme</h2>
                    <canvas id="chartMargeDime" class="chart-canvas"></canvas>
                </div>

                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h2 style="margin: 0;">🎯 Activités Récentes</h2>
                        <a href="{{ route('caisse.activities.view') }}" class="btn btn-primary btn-sm">
                            📋 Voir tout l'historique
                        </a>
                    </div>
                    <div id="recentActivities">
                        <div style="text-align: center; padding: 20px; color: #999;">
                            Chargement des activités...
                        </div>
                    </div>
                </div>

                <div class="card no-print">
                    <h2>Actions</h2>
                    <div class="btn-group">
                        <button class="btn btn-success" onclick="exportCSV('entrees')">
                            📥 Exporter Entrées
                        </button>
                        <button class="btn btn-success" onclick="exportCSV('sorties')">
                            📥 Exporter Sorties
                        </button>
                        <button class="btn btn-primary" onclick="window.print()">
                            🖨️ Imprimer
                        </button>
                        <button class="btn btn-warning" onclick="backupData()">
                            💾 Sauvegarder
                        </button>
                        <button class="btn btn-secondary" onclick="restoreData()">
                            📂 Restaurer
                        </button>
                        <button class="btn btn-danger" onclick="resetDemo()" id="btnResetDemo">
                            🔄 Réinitialiser
                        </button>
                    </div>
                </div>
            </div>

            <div id="entrees" class="tab-content">
                <div class="card">
                    <h2>Ajouter/Modifier une Entrée</h2>
                    <form id="formEntree">
                        <div class="form-grid">
                            <!-- === ÉTAPE 1: TYPE DE PAIEMENT === -->
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="entreeTypePaiement" style="font-size: 1.1em; font-weight: bold; color: var(--primary);">
                                    ➊ Type de paiement *
                                </label>
                                <select id="entreeTypePaiement" required onchange="toggleClientCRM()" style="font-size: 1em; padding: 12px;">
                                    <option value="">Sélectionner le type de paiement...</option>
                                    <option value="client_crm">💳 Paiement Client CRM (avec facture)</option>
                                    <option value="autre">📝 Autre paiement</option>
                                </select>
                            </div>

                            <!-- === ÉTAPE 2: SÉLECTION CLIENT ET FACTURE (CRM uniquement) === -->
                            <div id="sectionCRM" style="display: none; grid-column: 1 / -1; background: #f0f8ff; padding: 20px; border-radius: 10px; border: 2px solid var(--info); margin: 15px 0;">
                                <h3 style="color: var(--primary); margin-bottom: 15px;">💼 Informations Client CRM</h3>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="crmClientSearch">Rechercher un client</label>
                                        <input type="text" id="crmClientSearch" placeholder="🔍 Rechercher par nom, contact, email..."
                                               oninput="filterCRMClients()" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                                    </div>
                                    <div class="form-group">
                                        <label for="entreeClientCRM">Client *</label>
                                        <select id="entreeClientCRM" onchange="selectClientCRM(this.value)" style="width: 100%;">
                                            <option value="">Sélectionner un client...</option>
                                        </select>
                                    </div>

                                    <div class="form-group" id="factureGroup" style="display: none;">
                                        <label for="entreeFacture">Facture à payer *</label>
                                        <select id="entreeFacture" onchange="selectFacture()" style="width: 100%;">
                                            <option value="">Sélectionner une facture...</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Info Facture Sélectionnée -->
                                <div id="infoFacture" style="display: none; background: white; padding: 15px; border-radius: 8px; border-left: 4px solid #28a745; margin-top: 15px;">
                                    <h4 style="color: #28a745; margin-bottom: 10px;">📋 Détails de la Facture</h4>
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px;">
                                        <div>
                                            <small style="color: #666;">Numéro:</small><br>
                                            <strong id="infoFactureNum" style="color: var(--primary);"></strong>
                                        </div>
                                        <div>
                                            <small style="color: #666;">Service:</small><br>
                                            <strong id="infoFactureService"></strong>
                                        </div>
                                        <div>
                                            <small style="color: #666;">Montant Total:</small><br>
                                            <strong id="infoFactureTotal"></strong> FCFA
                                        </div>
                                        <div>
                                            <small style="color: #666;">Déjà Payé:</small><br>
                                            <strong style="color: #28a745;" id="infoFacturePaye"></strong> FCFA
                                        </div>
                                        <div>
                                            <small style="color: #666;">Montant à Payer:</small><br>
                                            <strong style="color: #d32f2f; font-size: 1.2em;" id="infoFactureRestant"></strong> FCFA
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- === ÉTAPE 3: DÉTAILS DU PAIEMENT === -->
                            <div style="grid-column: 1 / -1; margin-top: 10px;">
                                <h3 style="color: var(--primary); margin-bottom: 10px;">➋ Détails du Paiement</h3>
                            </div>

                            <div class="form-group">
                                <label for="entreeNom">Nom *</label>
                                <input type="text" id="entreeNom" required>
                            </div>
                            <div class="form-group">
                                <label for="entreePrenoms">Prénoms *</label>
                                <input type="text" id="entreePrenoms" required>
                            </div>
                            <div class="form-group">
                                <label for="entreeDate">Date *</label>
                                <input type="date" id="entreeDate" required>
                            </div>
                            <div class="form-group">
                                <label for="entreeRef">
                                    Référence
                                    <small style="color: var(--success); display: block; margin-top: 2px;">
                                        ✅ Générée automatiquement
                                    </small>
                                </label>
                                <div style="display: flex; gap: 5px;">
                                    <input type="text" id="entreeRef" style="flex: 1;" placeholder="Auto-générée..." readonly>
                                    <button type="button" class="btn btn-secondary" onclick="generateRef('entree')" title="Régénérer">
                                        🔄
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="entreeCategorie">Catégorie *</label>
                                <select id="entreeCategorie" required onchange="updateNatureOptions()">
                                    <option value="">Sélectionner une catégorie...</option>
                                    <option value="Frais de Cabinet">Frais de Cabinet</option>
                                    <option value="Documents de Voyage">Documents de Voyage</option>
                                    <option value="Autre">Autre</option>
                                </select>
                            </div>
                            <div class="form-group" id="autreNatureGroup" style="display: none;">
                                <label for="entreeAutreNature">Préciser la nature *</label>
                                <input type="text" id="entreeAutreNature" placeholder="Ex: Remboursement, Donation...">
                            </div>
                            <div class="form-group">
                                <label for="entreeMontant">Montant Total (FCFA) *</label>
                                <input type="number" id="entreeMontant" min="1" step="1" required>
                            </div>
                            <div class="form-group" id="detailPrestationsGroup" style="display: none;">
                                <label style="font-weight: bold; margin-bottom: 10px; display: block;">
                                    📋 Détail des prestations
                                    <small style="display: block; color: var(--text-secondary); font-weight: normal;">
                                        Décomposez le montant par prestation (optionnel)
                                    </small>
                                </label>
                                <div id="prestationsContainer" style="display: grid; gap: 10px;"></div>
                            </div>
                            <div class="form-group">
                                <label for="entreeMode">Mode de paiement *</label>
                                <select id="entreeMode" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="Espèces">Espèces</option>
                                    <option value="Mobile Money">Mobile Money</option>
                                    <option value="Virement">Virement</option>
                                    <option value="Chèque">Chèque</option>
                                    <option value="Carte">Carte</option>
                                </select>
                            </div>

                            <!-- === INFORMATION DU PAYEUR === -->
                            <div style="grid-column: 1 / -1; margin-top: 20px;">
                                <h3 style="color: var(--primary); margin-bottom: 10px;">➌ Information du Payeur</h3>
                            </div>

                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="entreeTypePayeur">Qui effectue le paiement ? *</label>
                                <select id="entreeTypePayeur" required onchange="togglePayeurFields()">
                                    <option value="lui_meme" selected>Lui-même</option>
                                    <option value="autre_personne">Autre personne</option>
                                </select>
                            </div>

                            <!-- Champs affichés uniquement si "Autre personne" est sélectionné -->
                            <div id="autrePayeurFields" style="display: none; grid-column: 1 / -1; background: #fff3cd; padding: 20px; border-radius: 10px; border: 2px solid #ffc107;">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="payeurNomPrenom">Nom et Prénom du payeur *</label>
                                        <input type="text" id="payeurNomPrenom" placeholder="Ex: KOUASSI Jean">
                                    </div>
                                    <div class="form-group">
                                        <label for="payeurTelephone">Téléphone *</label>
                                        <input type="tel" id="payeurTelephone" placeholder="Ex: 0707070707">
                                    </div>
                                    <div class="form-group">
                                        <label for="payeurRelation">Relation avec le client *</label>
                                        <input type="text" id="payeurRelation" placeholder="Ex: Parent, Ami, Employeur...">
                                    </div>
                                    <div class="form-group">
                                        <label for="payeurReferenceDossier">
                                            Référence du dossier
                                            <small style="color: var(--success); display: block; margin-top: 2px;">
                                                ✅ Auto-rempli avec la référence du reçu
                                            </small>
                                        </label>
                                        <input type="text" id="payeurReferenceDossier" placeholder="Auto-rempli après enregistrement..." readonly style="background-color: #f0f0f0; cursor: not-allowed;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="entreeId">
                        <input type="hidden" id="entreeTiersNom">
                        <input type="hidden" id="entreeMontantVerseTiers" value="0">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary" id="btnSaveEntree">
                                Ajouter
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="cancelEntree()">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <h2>Liste des Entrées</h2>
                    <div id="entreesTable"></div>
                    <div class="pagination" id="entreesPagination"></div>
                </div>
            </div>

            <div id="sorties" class="tab-content">
                <div class="card">
                    <h2>Ajouter/Modifier une Sortie</h2>
                    <form id="formSortie">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="sortieDate">Date *</label>
                                <input type="date" id="sortieDate" required>
                            </div>
                            <div class="form-group">
                                <label for="sortieRef">Référence</label>
                                <div style="display: flex; gap: 5px;">
                                    <input type="text" id="sortieRef" style="flex: 1;">
                                    <button type="button" class="btn btn-secondary" onclick="generateRef('sortie')">
                                        Générer
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="sortieNature">Nature *</label>
                                <input type="text" id="sortieNature" required placeholder="Ex: Fournitures...">
                            </div>
                            <div class="form-group">
                                <label for="sortieFournisseur">Fournisseur *</label>
                                <input type="text" id="sortieFournisseur" required>
                            </div>
                            <div class="form-group">
                                <label for="sortieMontant">Montant (FCFA) *</label>
                                <input type="number" id="sortieMontant" min="1" step="1" required>
                            </div>
                        </div>
                        <input type="hidden" id="sortieId">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary" id="btnSaveSortie">
                                Ajouter
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="cancelSortie()">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <h2>Liste des Sorties</h2>
                    <div id="sortiesTable"></div>
                    <div class="pagination" id="sortiesPagination"></div>
                </div>
            </div>

            <div id="depenses" class="tab-content">
                <div class="card">
                    <h2>📅 Historique des Clôtures Mensuelles</h2>
                    <div id="histoireClotures"></div>
                </div>

                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h2 style="margin: 0;">📊 Trésorerie - <span id="depensesMoisActuel"></span></h2>
                        <div>
                            <span id="statutMois" class="badge badge-active" style="font-size: 14px; padding: 8px 16px;">OUVERT</span>
                        </div>
                    </div>
                    <div style="background: var(--bg-main); padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                        <div id="reportMoisPrecedent" style="padding: 15px; background: var(--bg-card); border-radius: 8px; border-left: 4px solid var(--info); margin-bottom: 20px; display: none;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <div style="font-size: 0.85em; color: var(--text-secondary);">📋 Report du mois précédent</div>
                                    <div style="font-size: 1.3em; font-weight: bold; color: var(--info); margin-top: 5px;" id="montantReport">0 FCFA</div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 0.85em; color: var(--text-secondary);">Clôture de</div>
                                    <div style="font-size: 1em; font-weight: bold;" id="periodeReport">-</div>
                                </div>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 20px;">
                            <div style="text-align: center; padding: 20px; background: var(--bg-card); border-radius: 8px; border: 2px solid #38ef7d;">
                                <div style="font-size: 0.85em; color: var(--text-secondary); margin-bottom: 5px;">💰 CAISSE (Date du jour)</div>
                                <div style="font-size: 1.6em; font-weight: bold; color: #38ef7d;" id="tresorerieCaisse">0 FCFA</div>
                                <div style="font-size: 0.75em; color: var(--text-secondary); margin-top: 5px;">Solde net du dashboard</div>
                            </div>
                            <div style="text-align: center; padding: 20px; background: var(--bg-card); border-radius: 8px; border: 2px solid #4facfe;">
                                <div style="font-size: 0.85em; color: var(--text-secondary); margin-bottom: 5px;">🏦 BANQUE</div>
                                <div style="font-size: 1.6em; font-weight: bold; color: #4facfe;" id="tresorerieBanque">0 FCFA</div>
                                <div style="font-size: 0.75em; color: var(--text-secondary); margin-top: 5px;">Montant en banque</div>
                            </div>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); border-radius: 8px; margin-bottom: 20px; color: white;">
                            <div>
                                <div style="font-size: 0.9em; opacity: 0.9;">TOTAL TRÉSORERIE</div>
                                <div style="font-size: 2em; font-weight: bold;" id="tresorerieTotal">0 FCFA</div>
                                <div style="font-size: 0.8em; opacity: 0.8; margin-top: 5px;">Caisse + Banque <span id="avecReport"></span></div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 0.9em; opacity: 0.9;">DÉPENSES DU MOIS</div>
                                <div style="font-size: 1.6em; font-weight: bold;" id="tresorerieDepenses">0 FCFA</div>
                                <div style="font-size: 0.8em; opacity: 0.8; margin-top: 5px;">Charges exécutées</div>
                            </div>
                        </div>
                        
                        <div style="padding: 25px; background: var(--bg-card); border-radius: 8px; border: 3px solid var(--success); text-align: center;">
                            <div style="font-size: 1em; color: var(--text-secondary); margin-bottom: 10px;">SOLDE DE TRÉSORERIE APRÈS CHARGES</div>
                            <div style="font-size: 2.2em; font-weight: bold;" id="tresorerieSolde">0 FCFA</div>
                            <div style="font-size: 0.85em; color: var(--text-secondary); margin-top: 8px;">Total Trésorerie - Dépenses</div>
                        </div>

                        <div id="actionCloture" style="margin-top: 20px; padding: 20px; background: var(--bg-card); border-radius: 8px; border: 2px solid var(--warning);">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <div style="font-size: 1.1em; font-weight: bold; color: var(--text-primary); margin-bottom: 5px;">
                                        🔒 Clôture du mois
                                    </div>
                                    <div style="font-size: 0.85em; color: var(--text-secondary);">
                                        Le solde actuel sera reporté sur le mois suivant
                                    </div>
                                </div>
                                <button class="btn btn-warning" onclick="cloturerMois()" id="btnCloture">
                                    Clôturer ce mois
                                </button>
                            </div>
                        </div>

                        <div id="moisCloture" style="margin-top: 20px; padding: 20px; background: var(--danger); color: white; border-radius: 8px; display: none;">
                            <div style="text-align: center;">
                                <div style="font-size: 1.2em; font-weight: bold; margin-bottom: 5px;">
                                    🔒 Ce mois a été clôturé
                                </div>
                                <div style="font-size: 0.9em; opacity: 0.9;">
                                    Les modifications ne sont plus possibles. Le solde a été reporté.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h2>💰 Configuration de la Trésorerie Mensuelle</h2>
                    <form id="formBudget">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="budgetMois">Mois/Année *</label>
                                <input type="month" id="budgetMois" required>
                            </div>
                            <div class="form-group">
                                <label for="budgetBanqueInput">Montant en Banque (FCFA) *</label>
                                <input type="number" id="budgetBanqueInput" min="0" step="1" value="0" required oninput="updateBudgetTotal()">
                                <small style="color: var(--text-secondary); margin-top: 5px; display: block;">
                                    La caisse sera calculée automatiquement depuis le solde net
                                </small>
                            </div>
                        </div>
                        <div style="background: var(--info); opacity: 0.1; padding: 20px; border-radius: 5px; margin: 15px 0;">
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
                                <div>
                                    <div style="font-size: 0.85em; color: var(--text-secondary);">Caisse (auto)</div>
                                    <div style="font-size: 1.3em; font-weight: bold;" id="budgetCaissePreview">0 FCFA</div>
                                </div>
                                <div>
                                    <div style="font-size: 0.85em; color: var(--text-secondary);">Banque</div>
                                    <div style="font-size: 1.3em; font-weight: bold;" id="budgetBanquePreview">0 FCFA</div>
                                </div>
                                <div>
                                    <div style="font-size: 0.85em; color: var(--text-secondary);">Total Trésorerie</div>
                                    <div style="font-size: 1.3em; font-weight: bold; color: var(--primary);" id="budgetTotalPreview">0 FCFA</div>
                                </div>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                💾 Enregistrer la Trésorerie
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="loadCurrentMonthBudget()">
                                🔄 Charger le Mois Actuel
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <h2>Ajouter/Modifier une Dépense</h2>
                    <form id="formDepense">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="depenseDate">Date *</label>
                                <input type="date" id="depenseDate" required>
                            </div>
                            <div class="form-group">
                                <label for="depenseNature">Nature *</label>
                                <select id="depenseNature" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="Salaire">Salaire</option>
                                    <option value="Prime">Prime</option>
                                    <option value="Loyer">Loyer</option>
                                    <option value="Cie">Cie (Électricité)</option>
                                    <option value="Internet">Internet</option>
                                    <option value="Impôt">Impôt</option>
                                    <option value="Eau">Eau</option>
                                    <option value="Entretien Machine">Entretien Machine</option>
                                    <option value="Consultation">Consultation</option>
                                    <option value="Autre">Autre</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="depenseMontant">Montant (FCFA) *</label>
                                <input type="number" id="depenseMontant" min="1" step="1" required>
                            </div>
                            <div class="form-group">
                                <label for="depenseBeneficiaire">Bénéficiaire *</label>
                                <input type="text" id="depenseBeneficiaire" required placeholder="Nom du bénéficiaire">
                            </div>
                            <div class="form-group">
                                <label for="depenseDatePaiement">Date de Paiement</label>
                                <input type="date" id="depenseDatePaiement">
                            </div>
                            <div class="form-group">
                                <label for="depenseModePaiement">Mode de Paiement *</label>
                                <select id="depenseModePaiement" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="Espèces">Espèces</option>
                                    <option value="Chèque">Chèque</option>
                                    <option value="Virement">Virement</option>
                                    <option value="Mobile Money">Mobile Money</option>
                                    <option value="Carte">Carte</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="depenseStatut">Statut *</label>
                                <select id="depenseStatut" required>
                                    <option value="NON EXÉCUTÉ">NON EXÉCUTÉ</option>
                                    <option value="EXÉCUTÉ">EXÉCUTÉ</option>
                                </select>
                            </div>
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="depenseObservations">Observations</label>
                                <textarea id="depenseObservations" rows="3" placeholder="Remarques ou observations..."></textarea>
                            </div>
                        </div>
                        <input type="hidden" id="depenseId">
                        <input type="hidden" id="depensePeriode">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary" id="btnSaveDepense">
                                Ajouter
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="cancelDepense()">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2 style="margin: 0;">📋 Liste des Dépenses - <span id="depensesListMois"></span></h2>
                        <button class="btn btn-success" onclick="exportCSV('depenses')">
                            📥 Exporter CSV
                        </button>
                    </div>
                    <div class="filters" style="margin-bottom: 20px;">
                        <div style="display: flex; gap: 15px; align-items: end; flex-wrap: wrap;">
                            <div class="form-group" style="margin: 0;">
                                <label for="filterDepenseMois">Mois/Année</label>
                                <input type="month" id="filterDepenseMois" onchange="filterDepenses()">
                            </div>
                            <div class="form-group" style="margin: 0;">
                                <label for="filterDepenseNature">Nature</label>
                                <select id="filterDepenseNature" onchange="filterDepenses()">
                                    <option value="">Toutes</option>
                                    <option value="Salaire">Salaire</option>
                                    <option value="Prime">Prime</option>
                                    <option value="Loyer">Loyer</option>
                                    <option value="Cie">Cie</option>
                                    <option value="Internet">Internet</option>
                                    <option value="Impôt">Impôt</option>
                                    <option value="Eau">Eau</option>
                                    <option value="Autre">Autre</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin: 0;">
                                <label for="filterDepenseStatut">Statut</label>
                                <select id="filterDepenseStatut" onchange="filterDepenses()">
                                    <option value="">Tous</option>
                                    <option value="EXÉCUTÉ">Exécuté</option>
                                    <option value="NON EXÉCUTÉ">Non exécuté</option>
                                </select>
                            </div>
                            <button class="btn btn-secondary" onclick="resetDepenseFilters()">
                                Réinitialiser
                            </button>
                        </div>
                    </div>
                    <div id="depensesTable"></div>
                    <div class="pagination" id="depensesPagination"></div>
                </div>
            </div>

            <div id="settings" class="tab-content">
                <div class="card">
                    <h2>Ajouter un Nouvel Utilisateur</h2>
                    <form id="formNewUser">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="newUserUsername">Nom d'utilisateur *</label>
                                <input type="text" id="newUserUsername" required placeholder="Ex: agent2">
                            </div>
                            <div class="form-group">
                                <label for="newUserPassword">Mot de passe *</label>
                                <input type="password" id="newUserPassword" required placeholder="Minimum 8 caractères">
                            </div>
                            <div class="form-group">
                                <label for="newUserRole">Rôle *</label>
                                <select id="newUserRole" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="admin">Administrateur</option>
                                    <option value="agent">Agent</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Permissions</label>
                                <div style="display: flex; flex-direction: column; gap: 10px;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <input type="checkbox" id="permDashboard" value="dashboard" style="width: 20px; height: 20px;">
                                        <label for="permDashboard">Dashboard</label>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <input type="checkbox" id="permEntries" value="entries" style="width: 20px; height: 20px;">
                                        <label for="permEntries">Entrées</label>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <input type="checkbox" id="permExits" value="exits" style="width: 20px; height: 20px;">
                                        <label for="permExits">Sorties</label>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <input type="checkbox" id="permSettings" value="settings" style="width: 20px; height: 20px;">
                                        <label for="permSettings">Paramètres</label>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <input type="checkbox" id="permDepenses" value="depenses" style="width: 20px; height: 20px;">
                                        <label for="permDepenses">Dépenses/Trésorerie</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                Créer l'Utilisateur
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="resetNewUserForm()">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h2 style="margin: 0;">Gestion des Utilisateurs</h2>
                        <button class="btn btn-success" onclick="syncUsersFromServer()" title="Synchroniser avec le serveur">
                            🔄 Synchroniser depuis le serveur
                        </button>
                    </div>
                    <div id="usersTable"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalDocument" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Détails du Document</h3>
                <button class="close-modal" onclick="closeDocumentModal()">&times;</button>
            </div>
            <div class="form-group">
                <label for="modalTiersNom">Nom du tiers *</label>
                <input type="text" id="modalTiersNom" required>
            </div>
            <div class="form-group">
                <label for="modalMontantVerseTiers">Montant versé au tiers (FCFA) *</label>
                <input 
                    type="number" 
                    id="modalMontantVerseTiers" 
                    min="0" 
                    step="1" 
                    required 
                    oninput="calculateMarge()">
            </div>
            <div style="background: var(--success); opacity: 0.1; padding: 15px; border-radius: 5px; margin-top: 15px;">
                <strong>Marge calculée:</strong> <span id="margeValue">0 FCFA</span>
            </div>
            <div class="btn-group" style="margin-top: 20px;">
                <button class="btn btn-primary" onclick="validateDocument()">
                    Valider
                </button>
                <button class="btn btn-secondary" onclick="closeDocumentModal()">
                    Annuler
                </button>
            </div>
        </div>
    </div>

    <div id="modalUser" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalUserTitle">Gérer Utilisateur</h3>
                <button class="close-modal" onclick="closeUserModal()">&times;</button>
            </div>
            <form id="formUser" onsubmit="saveUser(event)">
                <div class="form-group">
                    <label for="modalUserName">
                        Nom complet <span style="color: var(--danger);">*</span>
                    </label>
                    <input
                        type="text"
                        id="modalUserName"
                        placeholder="Ex: John Doe"
                        required>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label for="modalUserUsername">
                        Nom d'utilisateur / Matricule
                        <small style="color: var(--text-secondary); display: block; margin-top: 5px;">
                            Pour les agents CRM, le matricule est utilisé comme identifiant
                        </small>
                    </label>
                    <input
                        type="text"
                        id="modalUserUsername"
                        readonly
                        style="background: var(--bg-main); cursor: not-allowed;"
                        title="Le nom d'utilisateur ne peut pas être modifié">
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label for="modalUserPassword">
                        Nouveau mot de passe
                        <small style="color: var(--text-secondary); display: block; margin-top: 5px;">
                            Laisser vide pour conserver le mot de passe actuel (min. 8 caractères)
                        </small>
                    </label>
                    <input
                        type="password"
                        id="modalUserPassword"
                        placeholder="Nouveau mot de passe (optionnel)"
                        minlength="8">
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label for="modalUserPasswordConfirm">
                        Confirmer le nouveau mot de passe
                    </label>
                    <input
                        type="password"
                        id="modalUserPasswordConfirm"
                        placeholder="Confirmer le mot de passe">
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label for="modalUserRole">Rôle</label>
                    <select id="modalUserRole">
                        <option value="admin">Admin</option>
                        <option value="agent">Agent</option>
                    </select>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label>Statut</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" id="modalUserActive" style="width: 20px; height: 20px;">
                        <label for="modalUserActive">Compte actif</label>
                    </div>
                </div>
                <input type="hidden" id="modalUserId">
                <div class="btn-group" style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">
                        💾 Enregistrer les modifications
                    </button>
                    <button type="button" class="btn btn-warning" onclick="resetUserPassword()" title="Réinitialiser au mot de passe par défaut (Matricule pour agents CRM)">
                        🔄 Réinitialiser MDP
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeUserModal()">
                        ❌ Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Gestion des Permissions -->
    <div id="modalPermissions" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <h2 style="margin-bottom: 20px;">🔑 Gestion des Permissions</h2>
            <form id="formPermissions" onsubmit="saveUserPermissions(event)">
                <div style="background: var(--bg-main); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <div style="margin-bottom: 10px;">
                        <strong>Utilisateur:</strong> <span id="modalPermUserName" style="color: var(--primary);"></span>
                    </div>
                    <div>
                        <strong>Type:</strong> <span id="modalPermUserType" style="color: var(--info);"></span>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="font-weight: bold; margin-bottom: 10px; display: block;">
                        Sélectionnez les permissions:
                    </label>
                    <div id="permissionsCheckboxes" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                        <label style="display: flex; align-items: center; gap: 8px; padding: 10px; background: var(--bg-card); border-radius: 6px; cursor: pointer;">
                            <input type="checkbox" value="dashboard" style="cursor: pointer;">
                            <span>📊 Dashboard</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; padding: 10px; background: var(--bg-card); border-radius: 6px; cursor: pointer;">
                            <input type="checkbox" value="entries" style="cursor: pointer;">
                            <span>💰 Entrées</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; padding: 10px; background: var(--bg-card); border-radius: 6px; cursor: pointer;">
                            <input type="checkbox" value="exits" style="cursor: pointer;">
                            <span>💸 Sorties</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; padding: 10px; background: var(--bg-card); border-radius: 6px; cursor: pointer;">
                            <input type="checkbox" value="depenses" style="cursor: pointer;">
                            <span>📝 Dépenses</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; padding: 10px; background: var(--bg-card); border-radius: 6px; cursor: pointer;">
                            <input type="checkbox" value="settings" style="cursor: pointer;">
                            <span>⚙️ Paramètres</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; padding: 10px; background: var(--bg-card); border-radius: 6px; cursor: pointer;">
                            <input type="checkbox" value="reports" style="cursor: pointer;">
                            <span>📈 Rapports</span>
                        </label>
                    </div>
                </div>

                <div style="margin-bottom: 20px; border-top: 2px solid var(--border); padding-top: 20px;">
                    <label style="font-weight: bold; margin-bottom: 10px; display: block; color: var(--warning);">
                        🔒 Permissions Spécifiques Caisse:
                    </label>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                        <label style="display: flex; align-items: center; gap: 8px; padding: 10px; background: var(--bg-card); border-radius: 6px; cursor: pointer; border: 1px solid var(--border);">
                            <input type="checkbox" value="modifier_entrees" style="cursor: pointer;">
                            <span>✏️ Modifier Entrées</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; padding: 10px; background: var(--bg-card); border-radius: 6px; cursor: pointer; border: 1px solid var(--border);">
                            <input type="checkbox" value="supprimer_entrees" style="cursor: pointer;">
                            <span>🗑️ Supprimer Entrées</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; padding: 10px; background: var(--bg-card); border-radius: 6px; cursor: pointer; border: 1px solid var(--border);">
                            <input type="checkbox" value="modifier_sorties" style="cursor: pointer;">
                            <span>✏️ Modifier Sorties</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; padding: 10px; background: var(--bg-card); border-radius: 6px; cursor: pointer; border: 1px solid var(--border);">
                            <input type="checkbox" value="supprimer_sorties" style="cursor: pointer;">
                            <span>🗑️ Supprimer Sorties</span>
                        </label>
                    </div>
                </div>

                <input type="hidden" id="modalPermUserId">

                <div class="btn-group" style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">
                        💾 Enregistrer
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closePermissionsModal()">
                        ❌ Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Reçu Client -->
    <div id="modalReceipt" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h3>📄 Reçu de Paiement</h3>
                <button class="close-modal" onclick="closeReceiptModal()">&times;</button>
            </div>
            <div id="receiptContent" style="padding: 30px; background: white;">
                <!-- Le contenu du reçu sera généré dynamiquement -->
            </div>
            <div class="btn-group" style="margin-top: 20px;">
                <button class="btn btn-primary" onclick="printReceipt()">
                    🖨️ Imprimer
                </button>
                <button class="btn btn-secondary" onclick="closeReceiptModal()">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    <script>
        const CONFIG = {
            IDLE_TIMEOUT: 600000,
            LOCKOUT_DURATION: 120000,
            MAX_LOGIN_ATTEMPTS: 5,
            PAGE_SIZE: 10,
            DEVISE: 'XOF',
            LOCALE: 'fr-FR',
            VERSION: '3.0'
        };

        // Utilisateurs chargés depuis le serveur (agents CRM)
        const SERVER_USERS = @json($users ?? []);

        let currentUser = null;
        let sessionActive = false;
        let idleTimer = null;
        let loginAttempts = {};
        let currentPageEntrees = 1;
        let currentPageSorties = 1;
        let currentPageDepenses = 1;
        let currentEntreeMontant = 0;
        // Données brutes chargées depuis la BDD pour le Dashboard
        let dashboardData = {
            entrees: [],
            sorties: []
        };

        // Clients CRM avec factures
        let clientsCRM = [];
        let selectedClient = null;
        let selectedFacture = null;

        let filteredData = {
            entrees: [],
            sorties: [],
            totalEntrees: 0,
            totalSorties: 0,
            net: 0,
            margeCabinet: 0,
            totalCabinet: 0,
            margeDocs: 0,
            totalDocs: 0,
            verseTiers: 0,
            dime: 0
        };

        const formatCurrency = (amount) => {
            return new Intl.NumberFormat(CONFIG.LOCALE, {
                style: 'currency',
                currency: CONFIG.DEVISE
            }).format(amount);
        };

        const generateUID = () => {
            if (typeof crypto !== 'undefined' && crypto.randomUUID) {
                return crypto.randomUUID();
            }
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
                const r = Math.random() * 16 | 0;
                const v = c === 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        };

        const escapeHtml = (text) => {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        };

        const formatDate = (date) => {
            return date.toISOString().split('T')[0];
        };

        function showToast(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;

            const icon = type === 'success' ? '✔' :
                        type === 'error' ? '✗' :
                        type === 'warning' ? '⚠' : 'ℹ';

            toast.innerHTML = `
                <span style="font-size: 20px;">${icon}</span>
                <span>${escapeHtml(message)}</span>
                <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
            `;

            container.appendChild(toast);
            setTimeout(() => toast.remove(), 5000);
        }

        function showInvoiceLinkNotification(invoiceUrl, clientName) {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = 'toast success';
            toast.style.maxWidth = '500px';
            toast.style.padding = '20px';

            toast.innerHTML = `
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 24px;">✅</span>
                        <strong>Paiement enregistré avec succès!</strong>
                    </div>
                    <p style="margin: 0; font-size: 14px;">
                        Le paiement de <strong>${escapeHtml(clientName)}</strong> a été enregistré.
                    </p>
                    <p style="margin: 0; font-size: 14px; color: #1e3c72;">
                        <strong>➋ Prochaine étape :</strong> Signature du reçu
                    </p>
                    <a href="${escapeHtml(invoiceUrl)}" target="_blank"
                       style="display: inline-block; background: #1e3c72; color: white; padding: 10px 20px;
                              border-radius: 5px; text-decoration: none; text-align: center; font-weight: bold;
                              transition: background 0.3s;"
                       onmouseover="this.style.background='#2a5298'"
                       onmouseout="this.style.background='#1e3c72'">
                        📝 Ouvrir la page de signature du reçu
                    </a>
                    <small style="color: #666; font-style: italic;">
                        Le client peut maintenant signer son reçu de paiement
                    </small>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()"
                        style="position: absolute; top: 10px; right: 10px;">&times;</button>
            `;

            container.appendChild(toast);
            // Garder cette notification plus longtemps (15 secondes)
            setTimeout(() => toast.remove(), 15000);
        }

        async function hashPasswordPBKDF2(password, salt = null) {
            const encoder = new TextEncoder();
            const data = encoder.encode(password);
            
            if (!salt) {
                salt = crypto.getRandomValues(new Uint8Array(16));
            } else if (typeof salt === 'string') {
                salt = new Uint8Array(salt.match(/.{1,2}/g).map(byte => parseInt(byte, 16)));
            }
            
            const key = await crypto.subtle.importKey(
                'raw',
                data,
                { name: 'PBKDF2' },
                false,
                ['deriveBits']
            );
            
            const derivedBits = await crypto.subtle.deriveBits(
                {
                    name: 'PBKDF2',
                    salt: salt,
                    iterations: 100000,
                    hash: 'SHA-256'
                },
                key,
                256
            );
            
            const hashArray = Array.from(new Uint8Array(derivedBits));
            const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
            const saltHex = Array.from(salt).map(b => b.toString(16).padStart(2, '0')).join('');
            
            return saltHex + ':' + hashHex;
        }

        async function verifyPasswordPBKDF2(password, storedHash) {
            const [salt, hash] = storedHash.split(':');
            const computedHash = await hashPasswordPBKDF2(password, salt);
            return computedHash === storedHash;
        }

        const initStorage = () => {
            if (!localStorage.getItem('ec_entries')) localStorage.setItem('ec_entries', JSON.stringify([]));
            if (!localStorage.getItem('ec_exits')) localStorage.setItem('ec_exits', JSON.stringify([]));
            if (!localStorage.getItem('ec_seq')) localStorage.setItem('ec_seq', JSON.stringify({ entrees: 0, sorties: 0 }));
            if (!localStorage.getItem('ec_audit')) localStorage.setItem('ec_audit', JSON.stringify([]));
            if (!localStorage.getItem('ec_users')) initUsers();
            if (!localStorage.getItem('ec_theme')) localStorage.setItem('ec_theme', 'light');
            if (!localStorage.getItem('ec_budgets')) localStorage.setItem('ec_budgets', JSON.stringify([]));
            if (!localStorage.getItem('ec_depenses')) localStorage.setItem('ec_depenses', JSON.stringify([]));
            if (!localStorage.getItem('ec_clotures')) localStorage.setItem('ec_clotures', JSON.stringify([]));
        };

        async function initUsers() {
            // Charger les utilisateurs depuis le serveur
            if (SERVER_USERS && SERVER_USERS.length > 0) {
                console.log('Initialisation des utilisateurs depuis le serveur:', SERVER_USERS.length);

                // Récupérer les utilisateurs existants pour préserver les modifications locales
                const existingUsers = getUsers();

                // Transformer les utilisateurs du serveur pour la caisse
                const users = await Promise.all(SERVER_USERS.map(async (user) => {
                    // Le mot de passe par défaut est le matricule de l'utilisateur
                    const defaultPassword = user.matricule || 'PSI@2025';

                    // Chercher si cet utilisateur existe déjà localement
                    const existingUser = existingUsers.find(u => {
                        const uId = typeof u.id === 'string' ? parseInt(u.id) : u.id;
                        const userId = typeof user.id === 'string' ? parseInt(user.id) : user.id;
                        return uId === userId;
                    });

                    const finalPermissions = existingUser?.permissions || user.permissions || ['dashboard'];

                    if (existingUser && existingUser.permissions) {
                        console.log(`📌 [InitUsers] Préservation des permissions locales pour ${user.name}:`, existingUser.permissions);
                    } else {
                        console.log(`🆕 [InitUsers] Nouvelles permissions du serveur pour ${user.name}:`, user.permissions);
                    }

                    return {
                        id: user.id,
                        username: user.username,
                        name: user.name,
                        email: user.email,
                        matricule: user.matricule,
                        passwordHash: existingUser?.passwordHash || await hashPasswordPBKDF2(defaultPassword),
                        role: user.role,
                        type_user: user.type_user,
                        // Préserver les permissions locales si elles existent
                        permissions: finalPermissions,
                        active: user.active !== false,
                        photo: user.photo,
                        crm_permissions: user.crm_permissions || []
                    };
                }));

                localStorage.setItem('ec_users', JSON.stringify(users));
                console.log('Utilisateurs initialisés:', users.length);
            } else {
                // Fallback: utilisateurs par défaut si pas de données du serveur
                console.log('Utilisation des utilisateurs par défaut (fallback)');
                const users = [
                    {
                        id: generateUID(),
                        username: 'admin',
                        passwordHash: await hashPasswordPBKDF2('PSI@2025A'),
                        role: 'admin',
                        permissions: ['dashboard', 'entries', 'exits', 'settings', 'depenses'],
                        active: true
                    },
                    {
                        id: generateUID(),
                        username: 'agent1',
                        passwordHash: await hashPasswordPBKDF2('PSI@AGENT1'),
                        role: 'agent',
                        permissions: ['entries', 'exits'],
                        active: true
                    }
                ];
                localStorage.setItem('ec_users', JSON.stringify(users));
            }
        }

        // Fonction pour synchroniser les utilisateurs depuis le serveur
        async function syncUsersFromServer() {
            try {
                console.log('Synchronisation des utilisateurs depuis le serveur...');
                const response = await fetch('{{ route("caisse.api.users") }}', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Erreur lors de la récupération des utilisateurs');
                }

                const data = await response.json();

                if (data.success && data.users && data.users.length > 0) {
                    console.log('Utilisateurs récupérés du serveur:', data.users.length);

                    // Récupérer les utilisateurs existants pour préserver les modifications locales
                    const existingUsers = getUsers();

                    // Transformer les utilisateurs pour la caisse
                    const users = await Promise.all(data.users.map(async (user) => {
                        const defaultPassword = user.matricule || 'PSI@2025';

                        // Chercher si cet utilisateur existe déjà localement
                        const existingUser = existingUsers.find(u => {
                            const uId = typeof u.id === 'string' ? parseInt(u.id) : u.id;
                            const userId = typeof user.id === 'string' ? parseInt(user.id) : user.id;
                            return uId === userId;
                        });

                        const finalPermissions = existingUser?.permissions || user.permissions || ['dashboard'];

                        if (existingUser && existingUser.permissions) {
                            console.log(`📌 Préservation des permissions locales pour ${user.name}:`, existingUser.permissions);
                        } else {
                            console.log(`🆕 Nouvelles permissions du serveur pour ${user.name}:`, user.permissions);
                        }

                        return {
                            id: user.id,
                            username: user.username,
                            name: user.name,
                            email: user.email,
                            matricule: user.matricule,
                            passwordHash: existingUser?.passwordHash || await hashPasswordPBKDF2(defaultPassword),
                            role: user.role,
                            type_user: user.type_user,
                            // Préserver les permissions locales si elles existent
                            permissions: finalPermissions,
                            active: user.active !== false,
                            photo: user.photo,
                            crm_permissions: user.crm_permissions || []
                        };
                    }));

                    saveUsers(users);
                    console.log('Utilisateurs synchronisés avec succès');

                    // Mettre à jour currentUser si c'est un des utilisateurs synchronisés
                    if (currentUser) {
                        const updatedCurrentUser = users.find(u => {
                            const uId = typeof u.id === 'string' ? parseInt(u.id) : u.id;
                            const currentUserId = typeof currentUser.id === 'string' ? parseInt(currentUser.id) : currentUser.id;
                            return uId === currentUserId;
                        });

                        if (updatedCurrentUser) {
                            currentUser = updatedCurrentUser;
                            console.log('✅ CurrentUser mis à jour après synchronisation. Permissions:', currentUser.permissions);
                        }
                    }

                    // Rafraîchir la table si on est dans les paramètres
                    if (document.getElementById('usersTable')) {
                        renderUsersTable();
                    }

                    return true;
                } else {
                    console.warn('Aucun utilisateur reçu du serveur');
                    return false;
                }
            } catch (error) {
                console.error('Erreur de synchronisation des utilisateurs:', error);
                return false;
            }
        }

        const getEntrees = () => JSON.parse(localStorage.getItem('ec_entries') || '[]');
        const getSorties = () => JSON.parse(localStorage.getItem('ec_exits') || '[]');
        const getSeq = () => JSON.parse(localStorage.getItem('ec_seq') || '{"entrees":0,"sorties":0}');
        const getUsers = () => JSON.parse(localStorage.getItem('ec_users') || '[]');
        const getBudgets = () => JSON.parse(localStorage.getItem('ec_budgets') || '[]');
        const getDepenses = () => JSON.parse(localStorage.getItem('ec_depenses') || '[]');
        const getClotures = () => JSON.parse(localStorage.getItem('ec_clotures') || '[]');

        const saveEntrees = (data) => localStorage.setItem('ec_entries', JSON.stringify(data));
        const saveSorties = (data) => localStorage.setItem('ec_exits', JSON.stringify(data));
        const saveSeq = (data) => localStorage.setItem('ec_seq', JSON.stringify(data));
        const saveUsers = (data) => localStorage.setItem('ec_users', JSON.stringify(data));
        const saveBudgets = (data) => localStorage.setItem('ec_budgets', JSON.stringify(data));
        const saveDepenses = (data) => localStorage.setItem('ec_depenses', JSON.stringify(data));
        const saveClotures = (data) => localStorage.setItem('ec_clotures', JSON.stringify(data));

        function logAudit(action, details) {
            const audit = JSON.parse(localStorage.getItem('ec_audit') || '[]');
            audit.push({
                timestamp: new Date().toISOString(),
                user: currentUser?.username || 'system',
                action: action,
                details: details
            });
            if (audit.length > 1000) audit.shift();
            localStorage.setItem('ec_audit', JSON.stringify(audit));
        }

        function toggleTheme() {
            const currentTheme = localStorage.getItem('ec_theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('ec_theme', newTheme);
            showToast(`Thème ${newTheme === 'dark' ? 'sombre' : 'clair'} activé`, 'success');
        }

        function loadTheme() {
            const theme = localStorage.getItem('ec_theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        }

        async function handleLogin(event) {
            event.preventDefault();
            
            const username = document.getElementById('loginUsername').value.trim();
            const password = document.getElementById('loginPassword').value;
            
            if (loginAttempts[username]) {
                const attempt = loginAttempts[username];
                if (attempt.lockedUntil && Date.now() < attempt.lockedUntil) {
                    const remainingTime = Math.ceil((attempt.lockedUntil - Date.now()) / 1000);
                    showToast(`Compte bloqué. Réessayez dans ${remainingTime}s`, 'error');
                    return;
                }
                if (attempt.lockedUntil && Date.now() >= attempt.lockedUntil) {
                    loginAttempts[username] = { count: 0, lockedUntil: null };
                }
            }
            
            const user = getUsers().find(u => u.username === username);
            
            if (!user) {
                recordFailedAttempt(username);
                showToast('Identifiants incorrects', 'error');
                return;
            }
            
            if (!user.active) {
                showToast('Compte bloqué', 'error');
                return;
            }
            
            const isValidPassword = await verifyPasswordPBKDF2(password, user.passwordHash);
            
            if (!isValidPassword) {
                recordFailedAttempt(username);
                const remaining = CONFIG.MAX_LOGIN_ATTEMPTS - (loginAttempts[username]?.count || 0);
                showToast(`Mot de passe incorrect. ${remaining} tentative(s) restante(s)`, 'error');
                return;
            }
            
            if (loginAttempts[username]) {
                delete loginAttempts[username];
            }
            
            currentUser = user;
            sessionActive = true;

            console.log('Connexion réussie. Utilisateur:', currentUser.username, 'Permissions:', currentUser.permissions);

            // Sauvegarder la session dans sessionStorage pour la persistance
            sessionStorage.setItem('ec_session', JSON.stringify({
                userId: user.id,
                username: user.username,
                timestamp: Date.now()
            }));

            logAudit('LOGIN', `User ${username} logged in`);

            document.getElementById('loginPage').classList.remove('active');
            document.getElementById('mainApp').classList.add('active');
            document.getElementById('currentUsername').textContent = user.name || user.username;
            document.getElementById('currentUserRole').textContent =
                user.role === 'admin' ? 'Administrateur' : 'Agent';

            applyPermissions();
            resetIdleTimer();
            refreshDashboard();
            renderEntreesTable();
            renderSortiesTable();

            // Démarrer le polling des permissions
            startPermissionsPolling();

            document.getElementById('loginForm').reset();
            showToast('Connexion réussie', 'success');
        }

        function recordFailedAttempt(username) {
            if (!loginAttempts[username]) {
                loginAttempts[username] = { count: 0, lockedUntil: null };
            }
            
            loginAttempts[username].count++;
            
            if (loginAttempts[username].count >= CONFIG.MAX_LOGIN_ATTEMPTS) {
                loginAttempts[username].lockedUntil = Date.now() + CONFIG.LOCKOUT_DURATION;
            }
        }

        function applyPermissions() {
            if (!currentUser) return;
            
            const permissions = currentUser.permissions || [];
            
            document.getElementById('tabDashboard').disabled = !permissions.includes('dashboard');
            document.getElementById('tabEntrees').disabled = !permissions.includes('entries');
            document.getElementById('tabSorties').disabled = !permissions.includes('exits');
            document.getElementById('tabSettings').disabled = !permissions.includes('settings');
            
            const depensesTab = document.getElementById('tabDepenses');
            if (depensesTab) {
                depensesTab.style.display = currentUser.role === 'admin' ? 'inline-flex' : 'none';
            }
            
            const resetBtn = document.getElementById('btnResetDemo');
            if (resetBtn) {
                resetBtn.style.display = currentUser.role === 'admin' ? 'inline-flex' : 'none';
            }
        }

        function logout() {
            if (currentUser) {
                logAudit('LOGOUT', `User ${currentUser.username} logged out`);
            }

            // Clear session storage to prevent auto-restore
            sessionStorage.removeItem('ec_session');

            // Arrêter le polling des permissions
            stopPermissionsPolling();

            currentUser = null;
            sessionActive = false;
            clearTimeout(idleTimer);

            showToast('Déconnexion réussie', 'success');

            // Rediriger vers la page de connexion Laravel
            setTimeout(() => {
                window.location.href = '/login';
            }, 1000);
        }

        function resetIdleTimer() {
            clearTimeout(idleTimer);
            
            if (sessionActive) {
                idleTimer = setTimeout(() => {
                    showToast('Session expirée', 'warning');
                    logout();
                }, CONFIG.IDLE_TIMEOUT);
            }
        }

        function ensureAuth() {
            if (!sessionActive || !currentUser) {
                logout();
                return false;
            }
            return true;
        }

        function hasPermission(permission) {
            const hasIt = currentUser && currentUser.permissions && currentUser.permissions.includes(permission);
            if (!hasIt && currentUser) {
                console.log(`Permission "${permission}" refusée pour ${currentUser.username}. Permissions actuelles:`, currentUser.permissions);
            }
            return hasIt;
        }

        function hasCaissePermission(permission) {
            // Super Admin et Admin ont toutes les permissions
            if (currentUser && (currentUser.role === 'admin' || currentUser.type_user === 'admin')) {
                return true;
            }

            const hasIt = currentUser && currentUser.caisse_permissions && currentUser.caisse_permissions.includes(permission);
            if (!hasIt && currentUser) {
                console.log(`Permission caisse "${permission}" refusée pour ${currentUser.username}. Permissions caisse actuelles:`, currentUser.caisse_permissions);
            }
            return hasIt;
        }

        ['mousedown', 'keydown', 'scroll', 'touchstart'].forEach(event => {
            document.addEventListener(event, () => {
                if (sessionActive) {
                    resetIdleTimer();
                }
            });
        });

        function switchTab(tabName) {
            if (!ensureAuth()) return;
            
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            const tabContent = document.getElementById(tabName);
            if (tabContent) {
                tabContent.classList.add('active');
            }
            
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            const tabBtnId = 'tab' + tabName.charAt(0).toUpperCase() + tabName.slice(1);
            const tabBtn = document.getElementById(tabBtnId);
            if (tabBtn) {
                tabBtn.classList.add('active');
            }
            
            if (tabName === 'dashboard') {
                refreshDashboard();
            } else if (tabName === 'entrees') {
                renderEntreesTable();
                // La référence est générée automatiquement par le backend
                const entreeRefInput = document.getElementById('entreeRef');
                if (!document.getElementById('entreeId').value) {
                    // Nouvelle entrée : laisser vide pour génération automatique
                    entreeRefInput.value = '';
                    entreeRefInput.placeholder = 'Généré automatiquement';
                }
            } else if (tabName === 'sorties') {
                renderSortiesTable();
                // La référence est générée automatiquement par le backend
                const sortieRefInput = document.getElementById('sortieRef');
                if (!document.getElementById('sortieId').value) {
                    // Nouvelle sortie : laisser vide pour génération automatique
                    sortieRefInput.value = '';
                    sortieRefInput.placeholder = 'Généré automatiquement';
                }
            } else if (tabName === 'depenses') {
                if (currentUser.role === 'admin') {
                    loadCurrentMonthBudget();
                }
            } else if (tabName === 'settings') {
                renderUsersTable();
            }
        }

        function generateRef(type) {
            if (!ensureAuth()) return;

            // Les références sont maintenant générées automatiquement par le backend
            if (type === 'entree') {
                document.getElementById('entreeRef').value = '';
                document.getElementById('entreeRef').placeholder = 'Généré automatiquement';
                showToast('La référence sera générée automatiquement lors de l\'enregistrement', 'info');
            } else {
                document.getElementById('sortieRef').value = '';
                document.getElementById('sortieRef').placeholder = 'Généré automatiquement';
                showToast('La référence sera générée automatiquement lors de l\'enregistrement', 'info');
            }
        }

        function generateAutoRefSortie() {
            const date = new Date();
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const seq = getSeq();
            const numero = String(seq.sorties + 1).padStart(4, '0');

            return `SOR-${year}${month}${day}-${numero}`;
        }

        // Configuration des prestations par catégorie
        const PRESTATIONS = {
            'Frais de Cabinet': [
                'Profil visa',
                'Inscription',
                'Assistance'
            ],
            'Documents de Voyage': [
                'Réservation d\'hôtel',
                'Billet d\'avion',
                'Assurance',
                'Circuit touristique'
            ]
        };

        function updateNatureOptions() {
            const categorie = document.getElementById('entreeCategorie').value;
            const autreNatureGroup = document.getElementById('autreNatureGroup');
            const detailGroup = document.getElementById('detailPrestationsGroup');

            // Réinitialiser
            autreNatureGroup.style.display = 'none';
            detailGroup.style.display = 'none';
            document.getElementById('entreeAutreNature').required = false;

            if (categorie === 'Autre') {
                // Afficher le champ texte libre
                autreNatureGroup.style.display = 'block';
                document.getElementById('entreeAutreNature').required = true;
            } else if (categorie && PRESTATIONS[categorie]) {
                // Afficher le détail des prestations
                detailGroup.style.display = 'block';
                renderPrestationsDetail(categorie);
            }
        }

        function renderPrestationsDetail(categorie) {
            const container = document.getElementById('prestationsContainer');
            container.innerHTML = '';

            if (!PRESTATIONS[categorie]) return;

            PRESTATIONS[categorie].forEach((prestation, index) => {
                const div = document.createElement('div');
                div.style.cssText = 'display: grid; grid-template-columns: 1fr 1fr; gap: 10px; align-items: center; padding: 10px; background: var(--bg-card); border-radius: 6px; border-left: 3px solid var(--primary);';

                div.innerHTML = `
                    <label style="margin: 0; font-size: 0.9em;">${prestation}</label>
                    <input
                        type="number"
                        id="prestation_${index}"
                        class="prestation-montant"
                        data-prestation="${prestation}"
                        placeholder="Montant (optionnel)"
                        min="0"
                        step="1"
                        oninput="calculatePrestationsTotal()"
                        style="padding: 8px; border: 1px solid var(--border); border-radius: 4px;">
                `;

                container.appendChild(div);
            });
        }

        function calculatePrestationsTotal() {
            const prestationInputs = document.querySelectorAll('.prestation-montant');
            let total = 0;

            prestationInputs.forEach(input => {
                const montant = parseFloat(input.value) || 0;
                total += montant;
            });

            // Mettre à jour le champ Montant Total
            const montantTotalInput = document.getElementById('entreeMontant');
            if (montantTotalInput) {
                montantTotalInput.value = total > 0 ? total : '';
            }
        }

        function checkDocumentNature() {
            if (!ensureAuth()) return;

            const categorie = document.getElementById('entreeCategorie').value;
            const montantInput = document.getElementById('entreeMontant');
            const montant = parseFloat(montantInput.value) || 0;

            if (categorie === 'Documents de Voyage') {
                if (montant > 0) {
                    currentEntreeMontant = montant;
                    openDocumentModal(montant);
                } else {
                    showToast('Veuillez entrer un montant valide', 'warning');
                    montantInput.focus();
                }
            } else {
                document.getElementById('entreeTiersNom').value = '';
                document.getElementById('entreeMontantVerseTiers').value = '0';
                currentEntreeMontant = 0;
            }
        }

        function openDocumentModal(montant) {
            const modal = document.getElementById('modalDocument');
            modal.classList.add('active');
            
            const tiersNom = document.getElementById('entreeTiersNom').value;
            const montantVerse = parseFloat(document.getElementById('entreeMontantVerseTiers').value) || 0;
            
            document.getElementById('modalTiersNom').value = tiersNom;
            document.getElementById('modalMontantVerseTiers').value = montantVerse;
            document.getElementById('modalMontantVerseTiers').max = montant;
            
            calculateMarge();
        }

        function closeDocumentModal() {
            document.getElementById('modalDocument').classList.remove('active');
        }

        function calculateMarge() {
            const montantTotal = currentEntreeMontant;
            const montantVerseInput = document.getElementById('modalMontantVerseTiers');
            let montantVerse = parseFloat(montantVerseInput.value) || 0;
            
            if (montantVerse < 0) {
                montantVerse = 0;
                montantVerseInput.value = 0;
            }
            
            if (montantVerse > montantTotal) {
                montantVerse = montantTotal;
                montantVerseInput.value = montantTotal;
            }
            
            const marge = montantTotal - montantVerse;
            document.getElementById('margeValue').textContent = formatCurrency(marge);
        }

        function validateDocument() {
            const tiersNom = document.getElementById('modalTiersNom').value.trim();
            const montantVerse = parseFloat(document.getElementById('modalMontantVerseTiers').value) || 0;
            const montantTotal = currentEntreeMontant;
            
            if (!tiersNom) {
                showToast('Le nom du tiers est obligatoire', 'error');
                return;
            }
            
            if (montantVerse < 0 || montantVerse > montantTotal) {
                showToast('Montant versé invalide', 'error');
                return;
            }
            
            document.getElementById('entreeTiersNom').value = tiersNom;
            document.getElementById('entreeMontantVerseTiers').value = montantVerse;
            
            closeDocumentModal();
            showToast('Détails du document enregistrés', 'success');
        }

        function calculateEntreeMarge(nature, montant, montantVerseTiers) {
            if (nature === 'Frais de Cabinet') {
                return montant;
            } else if (nature === 'Documents de Voyage' || nature === 'Autres Documents') {
                return montant - montantVerseTiers;
            }
            return 0;
        }

        // === GESTION DES CLIENTS CRM ===

        async function loadClientsCRM() {
            try {
                const response = await fetch('/caisse/api/clients', {
                    headers: {
                        'Authorization': `Bearer ${currentUser.matricule}`,
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) {
                    throw new Error('Erreur de chargement des clients');
                }

                const data = await response.json();
                clientsCRM = data.clients || [];

                // Remplir le select des clients
                const select = document.getElementById('entreeClientCRM');
                select.innerHTML = '<option value="">Sélectionner un client...</option>';

                clientsCRM.forEach(client => {
                    const option = document.createElement('option');
                    option.value = client.id;
                    option.textContent = `${client.nom_complet} (${client.uid}) - ${client.nombre_factures} facture(s) - Restant: ${formatNumber(client.restant)} FCFA`;
                    select.appendChild(option);
                });

                console.log(`${clientsCRM.length} clients CRM chargés`);
            } catch (error) {
                console.error('Erreur chargement clients CRM:', error);
                showToast('Erreur de chargement des clients CRM', 'error');
            }
        }

        function toggleClientCRM() {
            const type = document.getElementById('entreeTypePaiement').value;
            const sectionCRM = document.getElementById('sectionCRM');
            const factureGroup = document.getElementById('factureGroup');
            const infoFacture = document.getElementById('infoFacture');

            if (type === 'client_crm') {
                sectionCRM.style.display = 'block';
                // Charger les clients si pas encore fait
                if (clientsCRM.length === 0) {
                    loadClientsCRM();
                }
            } else {
                sectionCRM.style.display = 'none';
                factureGroup.style.display = 'none';
                infoFacture.style.display = 'none';
                // Réinitialiser les sélections
                document.getElementById('entreeClientCRM').value = '';
                document.getElementById('entreeFacture').value = '';
                document.getElementById('entreeNom').removeAttribute('readonly');
                document.getElementById('entreePrenoms').removeAttribute('readonly');
                selectedClient = null;
                selectedFacture = null;
            }
        }

        function togglePayeurFields() {
            const typePayeur = document.getElementById('entreeTypePayeur').value;
            const autrePayeurFields = document.getElementById('autrePayeurFields');
            const payeurNomPrenom = document.getElementById('payeurNomPrenom');
            const payeurTelephone = document.getElementById('payeurTelephone');
            const payeurRelation = document.getElementById('payeurRelation');

            if (typePayeur === 'autre_personne') {
                autrePayeurFields.style.display = 'block';
                // Rendre les champs obligatoires
                payeurNomPrenom.required = true;
                payeurTelephone.required = true;
                payeurRelation.required = true;
            } else {
                autrePayeurFields.style.display = 'none';
                // Retirer l'obligation et vider les champs
                payeurNomPrenom.required = false;
                payeurTelephone.required = false;
                payeurRelation.required = false;
                payeurNomPrenom.value = '';
                payeurTelephone.value = '';
                payeurRelation.value = '';
                // payeurReferenceDossier est auto-rempli et readonly, ne pas le vider
            }
        }

        function selectClientCRM(clientId) {
            if (!clientId) {
                document.getElementById('factureGroup').style.display = 'none';
                document.getElementById('infoFacture').style.display = 'none';
                selectedClient = null;
                return;
            }

            selectedClient = clientsCRM.find(c => c.id == clientId);
            if (!selectedClient) return;

            // Afficher le select des factures
            document.getElementById('factureGroup').style.display = 'block';

            // Remplir le select des factures
            const select = document.getElementById('entreeFacture');
            select.innerHTML = '<option value="">Sélectionner une facture...</option>';

            selectedClient.invoices.forEach(invoice => {
                const remaining = invoice.remaining || (invoice.amount - invoice.paid_amount);
                if (remaining > 0) { // Seulement les factures avec montant restant
                    const option = document.createElement('option');
                    option.value = invoice.id;
                    const statusIcon = invoice.status === 'paid' ? '✅' :
                                     invoice.status === 'partial' ? '🔵' : '⏳';
                    option.textContent = `${statusIcon} ${invoice.number} - ${invoice.service} - Restant: ${formatNumber(remaining)} FCFA`;
                    select.appendChild(option);
                }
            });
        }

        function filterCRMClients() {
            const searchText = document.getElementById('crmClientSearch').value.toLowerCase().trim();
            const select = document.getElementById('entreeClientCRM');

            // Si le champ de recherche est vide, afficher tous les clients
            if (!searchText) {
                Array.from(select.options).forEach(option => {
                    if (option.value !== '') {
                        option.style.display = '';
                    }
                });
                return;
            }

            // Filtrer les options en fonction du texte de recherche
            let visibleCount = 0;
            Array.from(select.options).forEach(option => {
                if (option.value === '') {
                    // Garder l'option par défaut visible
                    option.style.display = '';
                    return;
                }

                // Récupérer le client correspondant
                const client = clientsCRM.find(c => c.id == option.value);
                if (!client) {
                    option.style.display = 'none';
                    return;
                }

                // Rechercher dans nom, prenoms, contact et email
                const searchableText = [
                    client.nom || '',
                    client.prenoms || '',
                    client.contact || '',
                    client.email || '',
                    client.uid || ''
                ].join(' ').toLowerCase();

                // Afficher/masquer en fonction de la correspondance
                if (searchableText.includes(searchText)) {
                    option.style.display = '';
                    visibleCount++;
                } else {
                    option.style.display = 'none';
                }
            });

            // Afficher un message si aucun résultat
            if (visibleCount === 0) {
                select.options[0].textContent = `❌ Aucun client trouvé pour "${searchText}"`;
            } else {
                select.options[0].textContent = 'Sélectionner un client...';
            }
        }

        function selectFacture() {
            const factureId = document.getElementById('entreeFacture').value;
            if (!factureId || !selectedClient) {
                document.getElementById('infoFacture').style.display = 'none';
                selectedFacture = null;
                return;
            }

            selectedFacture = selectedClient.invoices.find(f => f.id == factureId);
            if (!selectedFacture) return;

            const remaining = selectedFacture.remaining || (selectedFacture.amount - selectedFacture.paid_amount);

            // Afficher les infos de la facture
            document.getElementById('infoFactureNum').textContent = selectedFacture.number;
            document.getElementById('infoFactureService').textContent = selectedFacture.service;
            document.getElementById('infoFactureTotal').textContent = formatNumber(selectedFacture.amount);
            document.getElementById('infoFacturePaye').textContent = formatNumber(selectedFacture.paid_amount);
            document.getElementById('infoFactureRestant').textContent = formatNumber(remaining);
            document.getElementById('infoFacture').style.display = 'block';

            // Pré-remplir les champs du formulaire
            document.getElementById('entreeNom').value = selectedClient.nom || '';
            document.getElementById('entreePrenoms').value = selectedClient.prenoms || '';
            document.getElementById('entreeMontant').value = remaining;
            document.getElementById('entreeCategorie').value = 'Frais de Cabinet'; // Par défaut

            // Rendre les champs nom/prénom readonly
            document.getElementById('entreeNom').setAttribute('readonly', 'readonly');
            document.getElementById('entreePrenoms').setAttribute('readonly', 'readonly');
        }

        function formatNumber(num) {
            return new Intl.NumberFormat('fr-FR').format(num || 0);
        }

        async function recordCRMPayment(invoiceId, amount, paymentMethod, notes = '') {
            try {
                const response = await fetch(`/caisse/api/invoices/${invoiceId}/payment`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        amount: amount,
                        payment_method: paymentMethod,
                        notes: `Paiement caisse - Ref: ${notes}`
                    })
                });

                if (!response.ok) {
                    const data = await response.json();
                    throw new Error(data.error || 'Erreur d\'enregistrement du paiement CRM');
                }

                return await response.json();
            } catch (error) {
                console.error('Erreur recordCRMPayment:', error);
                throw error;
            }
        }

        // === FIN GESTION DES CLIENTS CRM ===

        // Fonction pour réactiver le bouton d'enregistrement
        function enableSaveButton() {
            const btnSave = document.getElementById('btnSaveEntree');
            btnSave.disabled = false;
            btnSave.style.opacity = '1';
            btnSave.style.cursor = 'pointer';
            // Le texte sera remis à "Ajouter" ou "Modifier" par cancelEntree() ou editEntree()
        }

        async function saveEntree(event) {
            event.preventDefault();

            if (!ensureAuth() || !hasPermission('entries')) return;

            // Désactiver le bouton pour éviter les double-clics
            const btnSave = document.getElementById('btnSaveEntree');
            const btnOriginalText = btnSave.textContent;
            btnSave.disabled = true;
            btnSave.textContent = '⏳ Enregistrement en cours...';
            btnSave.style.opacity = '0.6';
            btnSave.style.cursor = 'not-allowed';

            const id = document.getElementById('entreeId').value || generateUID();
            const date = document.getElementById('entreeDate').value;
            let ref = document.getElementById('entreeRef').value.trim();
            const nom = document.getElementById('entreeNom').value.trim();
            const prenoms = document.getElementById('entreePrenoms').value.trim();
            const categorie = document.getElementById('entreeCategorie').value;
            let nature = categorie; // Par défaut, la nature est la catégorie
            const montant = parseFloat(document.getElementById('entreeMontant').value);
            const modePaiement = document.getElementById('entreeMode').value;

            // Gérer la catégorie "Autre"
            if (categorie === 'Autre') {
                nature = document.getElementById('entreeAutreNature').value.trim();
                if (!nature) {
                    showToast('Veuillez préciser la nature', 'error');
                    enableSaveButton();
                    return;
                }
            }

            // Validation de base
            if (!date || !nom || !prenoms || !categorie || montant <= 0 || !modePaiement) {
                showToast('Veuillez remplir tous les champs obligatoires', 'error');
                enableSaveButton();
                return;
            }

            // La référence sera générée automatiquement par le backend si vide
            // On ne génère plus manuellement ici

            // Récupérer le détail des prestations
            const detailPrestations = {};
            const prestationInputs = document.querySelectorAll('.prestation-montant');
            prestationInputs.forEach(input => {
                const prestation = input.dataset.prestation;
                const montantPrestation = parseFloat(input.value) || 0;
                if (montantPrestation > 0) {
                    detailPrestations[prestation] = montantPrestation;
                }
            });

            // Mettre à jour la nature avec les prestations sélectionnées
            const selectedPrestations = Object.keys(detailPrestations);
            if (selectedPrestations.length > 0) {
                nature = selectedPrestations.join(', ');
            }

            let tiersNom = document.getElementById('entreeTiersNom').value.trim();
            let montantVerseTiers = parseFloat(document.getElementById('entreeMontantVerseTiers').value) || 0;

            const isDocument = categorie === 'Documents de Voyage';

            if (isDocument && (!tiersNom || montantVerseTiers < 0 || montantVerseTiers > montant)) {
                showToast('Détails du document incomplets', 'error');
                currentEntreeMontant = montant;
                openDocumentModal(montant);
                enableSaveButton();
                return;
            }

            if (!isDocument) {
                tiersNom = '';
                montantVerseTiers = 0;
            }

            const marge = categorie === 'Frais de Cabinet' ? montant : (montant - montantVerseTiers);

            const entrees = getEntrees();
            const isUpdate = entrees.some(e => e.id === id);
            const existing = entrees.find(e => e.id === id);

            // Tous les agents peuvent modifier toutes les entrées
            // (Restriction supprimée)

            // Ne vérifier la référence que si elle est fournie
            if (ref && entrees.some(e => e.ref === ref && e.id !== id)) {
                showToast('Cette référence existe déjà', 'error');
                enableSaveButton();
                return;
            }

            // Récupérer les informations du payeur
            const typePayeur = document.getElementById('entreeTypePayeur').value;
            const payeurNomPrenom = typePayeur === 'autre_personne' ? document.getElementById('payeurNomPrenom').value.trim() : null;
            const payeurTelephone = typePayeur === 'autre_personne' ? document.getElementById('payeurTelephone').value.trim() : null;
            const payeurRelation = typePayeur === 'autre_personne' ? document.getElementById('payeurRelation').value.trim() : null;
            const payeurReferenceDossier = typePayeur === 'autre_personne' ? document.getElementById('payeurReferenceDossier').value.trim() : null;

            // DEBUG: Afficher les informations du payeur
            console.log('💳 Type Payeur:', typePayeur);
            console.log('👤 Payeur Nom:', payeurNomPrenom);
            console.log('📞 Payeur Téléphone:', payeurTelephone);
            console.log('🔗 Payeur Relation:', payeurRelation);

            const entree = {
                id: id,
                date: date,
                // NE PAS envoyer ref pour nouvelle entrée (sera généré par backend)
                ref: isUpdate ? ref : '',
                nom: nom,
                prenoms: prenoms,
                categorie: categorie,
                nature: nature,
                montant: montant,
                modePaiement: modePaiement,
                isDocument: isDocument,
                tiersNom: tiersNom || null,
                montantVerseTiers: montantVerseTiers,
                marge: marge,
                detailPrestations: Object.keys(detailPrestations).length > 0 ? detailPrestations : null,
                createdBy: isUpdate ? existing.createdBy : currentUser.username,
                createdAt: isUpdate ? existing.createdAt : new Date().toISOString(),
                updatedAt: new Date().toISOString(),
                isUpdate: isUpdate,  // Ajouter ce flag pour saveEntreeToDB
                // Ajouter les infos CRM si c'est un paiement client CRM
                crm_invoice_id: selectedFacture ? selectedFacture.id : null,
                crm_client_id: selectedClient ? selectedClient.id : null,
                // Ajouter les informations du payeur
                type_payeur: typePayeur,
                payeur_nom_prenom: payeurNomPrenom,
                payeur_telephone: payeurTelephone,
                payeur_relation: payeurRelation,
                payeur_reference_dossier: payeurReferenceDossier
            };

            // ========== SAUVEGARDE DANS LA BASE DE DONNÉES ==========
            try {
                const savedEntree = await saveEntreeToDB(entree);

                // Mettre à jour localStorage pour compatibilité avec le code existant
                if (isUpdate) {
                    const index = entrees.findIndex(e => e.id === id);
                    // Mettre à jour avec les données de la BDD (uuid, ref générée)
                    entrees[index] = {
                        ...entree,
                        id: savedEntree.uuid,
                        ref: savedEntree.ref
                    };
                    logAudit('UPDATE_ENTREE', `Entrée ${savedEntree.ref} modifiée`);
                    showToast('Entrée modifiée avec succès', 'success');
                } else {
                    // Ajouter avec les données de la BDD (ref générée, uuid, etc.)
                    entrees.push({
                        ...entree,
                        id: savedEntree.uuid,
                        ref: savedEntree.ref
                    });
                    logAudit('ADD_ENTREE', `Entrée ${savedEntree.ref} ajoutée: ${categorie} - ${nature}`);
                    showToast(`Entrée ${savedEntree.ref} ajoutée avec succès`, 'success');
                }

                // Remplir automatiquement le champ "Référence du dossier" avec la référence du reçu
                document.getElementById('payeurReferenceDossier').value = savedEntree.ref;
                document.getElementById('entreeRef').value = savedEntree.ref;

                saveEntrees(entrees.sort((a, b) => new Date(b.date) - new Date(a.date)));
                renderEntreesTable();

                // Si c'est un paiement client CRM, enregistrer le paiement dans le CRM
                if (selectedFacture && !isUpdate) {
                    try {
                        const paymentResult = await recordCRMPayment(selectedFacture.id, montant, modePaiement, savedEntree.ref);
                        showToast('Paiement CRM enregistré avec succès', 'success');

                        // Afficher le lien vers la facture avec signature du reçu
                        if (paymentResult && paymentResult.invoice) {
                            const invoiceUrl = paymentResult.invoice.public_url || `/facturation/${paymentResult.invoice.view_token}`;
                            const receiptUrl = invoiceUrl + '#receipt';

                            // Afficher une notification avec le lien
                            showInvoiceLinkNotification(receiptUrl, selectedClient.nom_complet);
                        }

                        // Recharger les clients pour mettre à jour les soldes
                        await loadClientsCRM();
                    } catch (error) {
                        console.error('Erreur enregistrement paiement CRM:', error);
                        showToast('Attention: Le paiement caisse est enregistré mais l\'enregistrement CRM a échoué', 'warning');
                    }
                }

                if (hasPermission('dashboard')) {
                    refreshDashboard();
                }

                cancelEntree();
            } catch (error) {
                console.error('Erreur sauvegarde entrée:', error);
                showToast('Erreur lors de l\'enregistrement: ' + error.message, 'error');
                enableSaveButton();
            }
        }

        // Générer une référence automatique
        function generateAutoRef() {
            const date = new Date();
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const seq = getSeq();
            const numero = String(seq.entrees + 1).padStart(4, '0');

            return `ENT-${year}${month}${day}-${numero}`;
        }

        function editEntree(id) {
            if (!ensureAuth() || !hasPermission('entries')) return;

            // Vérifier la permission de modifier les entrées
            if (!hasCaissePermission('modifier_entrees')) {
                showToast('Vous n\'avez pas la permission de modifier les entrées', 'error');
                return;
            }

            const entree = getEntrees().find(e => e.id === id);

            if (entree) {
                document.getElementById('entreeId').value = entree.id;
                document.getElementById('entreeDate').value = entree.date;
                document.getElementById('entreeRef').value = entree.ref;
                document.getElementById('entreeNom').value = entree.nom;
                document.getElementById('entreePrenoms').value = entree.prenoms;

                // Remplir la catégorie
                document.getElementById('entreeCategorie').value = entree.categorie || 'Autre';

                // Afficher les champs appropriés selon la catégorie
                updateNatureOptions();

                // Si c'est "Autre", remplir le champ texte
                if (entree.categorie === 'Autre') {
                    document.getElementById('entreeAutreNature').value = entree.nature || '';
                }

                // Restaurer les montants des prestations si disponibles
                if (entree.detailPrestations) {
                    setTimeout(() => {
                        Object.entries(entree.detailPrestations).forEach(([prestation, montant]) => {
                            const input = document.querySelector(`.prestation-montant[data-prestation="${prestation}"]`);
                            if (input) {
                                input.value = montant;
                            }
                        });
                    }, 100);
                }

                document.getElementById('entreeMontant').value = entree.montant;
                document.getElementById('entreeMode').value = entree.modePaiement;
                document.getElementById('entreeTiersNom').value = entree.tiersNom || '';
                document.getElementById('entreeMontantVerseTiers').value = entree.montantVerseTiers || 0;

                // Remplir les champs du payeur
                document.getElementById('entreeTypePayeur').value = entree.type_payeur || 'lui_meme';
                togglePayeurFields(); // Afficher/masquer les champs selon le type

                if (entree.type_payeur === 'autre_personne') {
                    document.getElementById('payeurNomPrenom').value = entree.payeur_nom_prenom || '';
                    document.getElementById('payeurTelephone').value = entree.payeur_telephone || '';
                    document.getElementById('payeurRelation').value = entree.payeur_relation || '';
                }

                // Remplir la référence du dossier avec la référence du reçu
                document.getElementById('payeurReferenceDossier').value = entree.ref || '';

                document.getElementById('btnSaveEntree').textContent = 'Modifier';
            }
        }

        function cancelEntree() {
            document.getElementById('formEntree').reset();
            document.getElementById('entreeId').value = '';
            document.getElementById('entreeRef').value = '';
            document.getElementById('entreeRef').placeholder = 'Auto-générée...';
            document.getElementById('entreeTiersNom').value = '';
            document.getElementById('entreeMontantVerseTiers').value = '0';
            currentEntreeMontant = 0;
            document.getElementById('btnSaveEntree').textContent = 'Ajouter';

            // Réactiver le bouton si désactivé
            enableSaveButton();

            // Réinitialiser les champs CRM
            document.getElementById('sectionCRM').style.display = 'none';
            document.getElementById('factureGroup').style.display = 'none';
            document.getElementById('infoFacture').style.display = 'none';
            document.getElementById('entreeNom').removeAttribute('readonly');
            document.getElementById('entreePrenoms').removeAttribute('readonly');
            selectedClient = null;
            selectedFacture = null;

            // Réinitialiser les champs du payeur
            document.getElementById('entreeTypePayeur').value = 'lui_meme';
            togglePayeurFields(); // Masquer les champs "autre personne"

            closeDocumentModal();
        }

        async function deleteEntree(id) {
            if (!ensureAuth() || !hasPermission('entries')) return;

            // Vérifier la permission de supprimer les entrées
            if (!hasCaissePermission('supprimer_entrees')) {
                showToast('Vous n\'avez pas la permission de supprimer les entrées', 'error');
                return;
            }

            if (!confirm('Supprimer cette entrée ?')) return;

            let entrees = getEntrees();
            const entree = entrees.find(e => e.id === id);

            // ========== SUPPRESSION DE LA BASE DE DONNÉES ==========
            try {
                // Supprimer de la BDD
                await deleteEntreeFromDB(id);

                // Supprimer de localStorage
                entrees = entrees.filter(e => e.id !== id);
                saveEntrees(entrees);

                logAudit('DELETE_ENTREE', `Entrée ${entree.ref} supprimée`);
                showToast('Entrée supprimée avec succès', 'success');

                renderEntreesTable();
                if (hasPermission('dashboard')) {
                    refreshDashboard();
                }
            } catch (error) {
                console.error('Erreur suppression entrée:', error);
                showToast('Erreur lors de la suppression: ' + error.message, 'error');
            }
        }

        function renderEntreesTable(page = 1) {
            if (!ensureAuth() || !hasPermission('entries')) return;

            currentPageEntrees = page;

            // Utiliser les données formatées du dashboard au lieu de localStorage brut
            const entrees = dashboardData.entrees || [];

            const tableDiv = document.getElementById('entreesTable');
            const paginationDiv = document.getElementById('entreesPagination');
            
            if (entrees.length === 0) {
                tableDiv.innerHTML = '<div style="text-align:center;padding:40px;color:var(--text-secondary);">Aucune entrée</div>';
                paginationDiv.innerHTML = '';
                return;
            }
            
            const totalPages = Math.ceil(entrees.length / CONFIG.PAGE_SIZE);
            const startIndex = (page - 1) * CONFIG.PAGE_SIZE;
            const pageEntrees = entrees.slice(startIndex, startIndex + CONFIG.PAGE_SIZE);
            
            let html = `
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Réf.</th>
                            <th>Client</th>
                            <th>Nature</th>
                            <th>Montant</th>
                            <th>Mode</th>
                            <th>Agent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            pageEntrees.forEach(entree => {
                const badgeClass = entree.isDocument ? 'badge-doc' :
                                  entree.nature === 'Frais de Cabinet' ? 'badge-cabinet' : 'badge-autre';

                // Vérifier les permissions pour afficher les boutons
                const canEdit = hasCaissePermission('modifier_entrees');
                const canDelete = hasCaissePermission('supprimer_entrees');

                html += `
                    <tr>
                        <td>${entree.date}</td>
                        <td>${escapeHtml(entree.ref)}</td>
                        <td>${escapeHtml(entree.nom)} ${escapeHtml(entree.prenoms)}</td>
                        <td><span class="badge ${badgeClass}">${escapeHtml(entree.nature)}</span></td>
                        <td>${formatCurrency(entree.montant)}</td>
                        <td>${escapeHtml(entree.modePaiement)}</td>
                        <td><span class="badge badge-info">${escapeHtml(entree.createdByName || entree.createdBy || 'N/A')}</span></td>
                        <td class="action-btns">
                            <button class="btn btn-info" onclick="showReceipt('${entree.id}')" title="Voir le reçu">📄</button>
                            ${canEdit ? `<button class="btn btn-warning" onclick="editEntree('${entree.id}')" title="Modifier">✏️</button>` : ''}
                            ${canDelete ? `<button class="btn btn-danger" onclick="deleteEntree('${entree.id}')" title="Supprimer">🗑️</button>` : ''}
                        </td>
                    </tr>
                `;
            });
            
            html += '</tbody></table>';
            tableDiv.innerHTML = html;
            
            paginationDiv.innerHTML = `
                <button onclick="renderEntreesTable(${page - 1})" ${page === 1 ? 'disabled' : ''}>
                    ‹ Précédent
                </button>
                <span>Page ${page} / ${totalPages}</span>
                <button onclick="renderEntreesTable(${page + 1})" ${page === totalPages ? 'disabled' : ''}>
                    Suivant ›
                </button>
            `;
        }

        async function saveSortie(event) {
            event.preventDefault();

            if (!ensureAuth() || !hasPermission('exits')) return;

            const id = document.getElementById('sortieId').value || generateUID();
            const date = document.getElementById('sortieDate').value;
            let ref = document.getElementById('sortieRef').value.trim();
            const nature = document.getElementById('sortieNature').value.trim();
            const fournisseur = document.getElementById('sortieFournisseur').value.trim();
            const montant = parseFloat(document.getElementById('sortieMontant').value);

            if (!date || !nature || !fournisseur || montant <= 0) {
                showToast('Veuillez remplir tous les champs', 'error');
                return;
            }

            const sorties = getSorties();
            const isUpdate = sorties.some(s => s.id === id);
            const existing = sorties.find(s => s.id === id);

            // Tous les agents peuvent modifier toutes les sorties
            // (Restriction supprimée)

            // Ne vérifier la référence que si elle est fournie
            if (ref && sorties.some(s => s.ref === ref && s.id !== id)) {
                showToast('Cette référence existe déjà', 'error');
                return;
            }

            const sortie = {
                id: id,
                date: date,
                // NE PAS envoyer ref pour nouvelle sortie (sera généré par backend)
                ref: isUpdate ? ref : '',
                beneficiaire: fournisseur,  // Mapper fournisseur -> beneficiaire pour le backend
                motif: nature,              // Mapper nature -> motif pour le backend
                montant: montant,
                modePaiement: 'Espèces',    // Valeur par défaut - à améliorer si vous avez un champ dans le form
                remarques: '',              // Valeur par défaut - à améliorer si vous avez un champ dans le form
                createdBy: isUpdate ? existing.createdBy : currentUser.username,
                createdAt: isUpdate ? existing.createdAt : new Date().toISOString(),
                updatedAt: new Date().toISOString(),
                isUpdate: isUpdate  // Ajouter ce flag pour saveSortieToDB
            };

            // ========== SAUVEGARDE DANS LA BASE DE DONNÉES ==========
            try {
                const savedSortie = await saveSortieToDB(sortie);

                // Mettre à jour localStorage pour compatibilité
                const sortieForLocalStorage = {
                    id: savedSortie.uuid,
                    date: date,
                    ref: savedSortie.ref,
                    nature: nature,
                    fournisseur: fournisseur,
                    montant: montant,
                    createdBy: isUpdate ? existing.createdBy : currentUser.username,
                    createdAt: isUpdate ? existing.createdAt : new Date().toISOString(),
                    updatedAt: new Date().toISOString()
                };

                if (isUpdate) {
                    const index = sorties.findIndex(s => s.id === id);
                    sorties[index] = sortieForLocalStorage;
                    logAudit('UPDATE_SORTIE', `Sortie ${savedSortie.ref} modifiée`);
                    showToast('Sortie modifiée avec succès', 'success');
                } else {
                    sorties.push(sortieForLocalStorage);
                    logAudit('ADD_SORTIE', `Sortie ${savedSortie.ref} ajoutée`);
                    showToast(`Sortie ${savedSortie.ref} ajoutée avec succès`, 'success');
                }

                saveSorties(sorties.sort((a, b) => new Date(b.date) - new Date(a.date)));
                renderSortiesTable();

                if (hasPermission('dashboard')) {
                    refreshDashboard();
                }

                cancelSortie();
            } catch (error) {
                console.error('Erreur sauvegarde sortie:', error);
                showToast('Erreur lors de l\'enregistrement: ' + error.message, 'error');
            }
        }

        function editSortie(id) {
            if (!ensureAuth() || !hasPermission('exits')) return;

            // Vérifier la permission de modifier les sorties
            if (!hasCaissePermission('modifier_sorties')) {
                showToast('Vous n\'avez pas la permission de modifier les sorties', 'error');
                return;
            }

            const sortie = getSorties().find(s => s.id === id);

            if (sortie) {
                document.getElementById('sortieId').value = sortie.id;
                document.getElementById('sortieDate').value = sortie.date;
                document.getElementById('sortieRef').value = sortie.ref;
                document.getElementById('sortieNature').value = sortie.nature;
                document.getElementById('sortieFournisseur').value = sortie.fournisseur;
                document.getElementById('sortieMontant').value = sortie.montant;
                
                document.getElementById('btnSaveSortie').textContent = 'Modifier';
            }
        }

        function cancelSortie() {
            document.getElementById('formSortie').reset();
            document.getElementById('sortieId').value = '';
            document.getElementById('sortieRef').value = '';
            document.getElementById('sortieRef').placeholder = 'Généré automatiquement';
            document.getElementById('btnSaveSortie').textContent = 'Ajouter';
        }

        async function deleteSortie(id) {
            if (!ensureAuth() || !hasPermission('exits')) return;

            // Vérifier la permission de supprimer les sorties
            if (!hasCaissePermission('supprimer_sorties')) {
                showToast('Vous n\'avez pas la permission de supprimer les sorties', 'error');
                return;
            }

            if (!confirm('Supprimer cette sortie ?')) return;

            let sorties = getSorties();
            const sortie = sorties.find(s => s.id === id);
            // (Restriction supprimée)

            // ========== SUPPRESSION DE LA BASE DE DONNÉES ==========
            try {
                // Supprimer de la BDD
                await deleteSortieFromDB(id);

                // Supprimer de localStorage
                sorties = sorties.filter(s => s.id !== id);
                saveSorties(sorties);

                logAudit('DELETE_SORTIE', `Sortie ${sortie.ref} supprimée`);
                showToast('Sortie supprimée avec succès', 'success');

                renderSortiesTable();
                if (hasPermission('dashboard')) {
                    refreshDashboard();
                }
            } catch (error) {
                console.error('Erreur suppression sortie:', error);
                showToast('Erreur lors de la suppression: ' + error.message, 'error');
            }
        }

        function renderSortiesTable(page = 1) {
            if (!ensureAuth() || !hasPermission('exits')) return;

            currentPageSorties = page;

            // Utiliser les données formatées du dashboard au lieu de localStorage brut
            const sorties = dashboardData.sorties || [];

            const tableDiv = document.getElementById('sortiesTable');
            const paginationDiv = document.getElementById('sortiesPagination');
            
            if (sorties.length === 0) {
                tableDiv.innerHTML = '<div style="text-align:center;padding:40px;color:var(--text-secondary);">Aucune sortie</div>';
                paginationDiv.innerHTML = '';
                return;
            }
            
            const totalPages = Math.ceil(sorties.length / CONFIG.PAGE_SIZE);
            const startIndex = (page - 1) * CONFIG.PAGE_SIZE;
            const pageSorties = sorties.slice(startIndex, startIndex + CONFIG.PAGE_SIZE);
            
            let html = `
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Réf.</th>
                            <th>Nature</th>
                            <th>Fournisseur</th>
                            <th>Montant</th>
                            <th>Agent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            pageSorties.forEach(sortie => {
                // Vérifier les permissions pour afficher les boutons
                const canEdit = hasCaissePermission('modifier_sorties');
                const canDelete = hasCaissePermission('supprimer_sorties');

                html += `
                    <tr>
                        <td>${sortie.date}</td>
                        <td>${escapeHtml(sortie.ref)}</td>
                        <td>${escapeHtml(sortie.nature)}</td>
                        <td>${escapeHtml(sortie.fournisseur)}</td>
                        <td>${formatCurrency(sortie.montant)}</td>
                        <td><span class="badge badge-info">${escapeHtml(sortie.createdByName || sortie.createdBy || 'N/A')}</span></td>
                        <td class="action-btns">
                            ${canEdit ? `<button class="btn btn-warning" onclick="editSortie('${sortie.id}')" title="Modifier">✏️</button>` : ''}
                            ${canDelete ? `<button class="btn btn-danger" onclick="deleteSortie('${sortie.id}')" title="Supprimer">🗑️</button>` : ''}
                        </td>
                    </tr>
                `;
            });
            
            html += '</tbody></table>';
            tableDiv.innerHTML = html;
            
            paginationDiv.innerHTML = `
                <button onclick="renderSortiesTable(${page - 1})" ${page === 1 ? 'disabled' : ''}>
                    ‹ Précédent
                </button>
                <span>Page ${page} / ${totalPages}</span>
                <button onclick="renderSortiesTable(${page + 1})" ${page === totalPages ? 'disabled' : ''}>
                    Suivant ›
                </button>
            `;
        }

        function applyFilters() {
            if (!ensureAuth() || !hasPermission('dashboard')) return;

            const dateDebut = document.getElementById('filterDateDebut').value;
            const dateFin = document.getElementById('filterDateFin').value;
            const nature = document.getElementById('filterNature').value;
            const mode = document.getElementById('filterMode').value;
            const search = document.getElementById('filterSearch').value.toLowerCase();

            // Utiliser les données du Dashboard (chargées depuis la BDD) au lieu de localStorage
            const allEntrees = dashboardData.entrees;
            const allSorties = dashboardData.sorties;

            console.log(`🔍 Filtrage Dashboard - Total: ${allEntrees.length} entrées, ${allSorties.length} sorties`);
            console.log(`📅 Filtres: Du ${dateDebut} au ${dateFin}, Nature: ${nature}, Mode: ${mode}, Recherche: ${search}`);

            const filteredEntrees = allEntrees.filter(e => {
                const dateMatch = (!dateDebut || e.date >= dateDebut) && (!dateFin || e.date <= dateFin);
                const natureMatch = !nature || e.nature === nature;
                const modeMatch = !mode || e.modePaiement === mode;
                const searchMatch = !search ||
                    e.nom.toLowerCase().includes(search) ||
                    e.prenoms.toLowerCase().includes(search) ||
                    e.ref.toLowerCase().includes(search);

                return dateMatch && natureMatch && modeMatch && searchMatch;
            });

            const filteredSorties = allSorties.filter(s => {
                const dateMatch = (!dateDebut || s.date >= dateDebut) && (!dateFin || s.date <= dateFin);
                const searchMatch = !search ||
                    s.fournisseur.toLowerCase().includes(search) ||
                    s.ref.toLowerCase().includes(search);
                
                return dateMatch && searchMatch;
            });

            console.log(`✅ Résultats filtrés: ${filteredEntrees.length} entrées, ${filteredSorties.length} sorties`);
            if (filteredEntrees.length > 0) {
                console.log('📝 Exemple entrée filtrée:', filteredEntrees[0]);
            }
            if (filteredSorties.length > 0) {
                console.log('📝 Exemple sortie filtrée:', filteredSorties[0]);
            }

            const totalEntrees = filteredEntrees.reduce((sum, e) => sum + e.montant, 0);
            const totalSorties = filteredSorties.reduce((sum, s) => sum + s.montant, 0);
            
            const cabinetEntrees = filteredEntrees.filter(e => e.nature === 'Frais de Cabinet');
            const totalCabinet = cabinetEntrees.reduce((sum, e) => sum + e.montant, 0);
            const margeCabinet = cabinetEntrees.reduce((sum, e) => sum + e.marge, 0);
            
            const docEntrees = filteredEntrees.filter(e => e.isDocument);
            const totalDocs = docEntrees.reduce((sum, e) => sum + e.montant, 0);
            const verseTiers = docEntrees.reduce((sum, e) => sum + e.montantVerseTiers, 0);
            const margeDocs = docEntrees.reduce((sum, e) => sum + e.marge, 0);
            
            const dime = (margeCabinet + margeDocs) * 0.1;
            
            filteredData = {
                entrees: filteredEntrees,
                sorties: filteredSorties,
                totalEntrees: totalEntrees,
                totalSorties: totalSorties,
                net: totalEntrees - totalSorties,
                margeCabinet: margeCabinet,
                totalCabinet: totalCabinet,
                margeDocs: margeDocs,
                totalDocs: totalDocs,
                verseTiers: verseTiers,
                dime: dime
            };
            
            renderKPIs();
            renderDocumentDetails();
            renderChartCashFlow();
            renderChartNature();
            renderChartMargeDime();
            
            showToast('Filtres appliqués', 'success');
        }

        function resetFilters() {
            if (!ensureAuth() || !hasPermission('dashboard')) return;
            
            document.getElementById('filterDateDebut').value = '';
            document.getElementById('filterDateFin').value = '';
            document.getElementById('filterNature').value = '';
            document.getElementById('filterMode').value = '';
            document.getElementById('filterSearch').value = '';
            
            refreshDashboard();
        }

        async function refreshDashboard() {
            // Charger les données directement depuis la BDD (sans localStorage)
            try {
                console.log('🔄 Dashboard: Chargement direct depuis la BDD...');

                const [entrees, sorties] = await Promise.all([
                    getEntreesFromDB(),
                    getSortiesFromDB()
                ]);

                console.log(`Dashboard: ${entrees.length} entrées et ${sorties.length} sorties chargées depuis la BDD`);
                console.log('📦 Données brutes entrées:', entrees);
                console.log('📦 Données brutes sorties:', sorties);

                // Formater les données pour le Dashboard
                dashboardData.entrees = entrees.map(e => {
                    const montant = parseFloat(e.montant);
                    const montantVerseTiers = parseFloat(e.montant_verse_tiers || 0);
                    const isDocument = e.tiers_nom ? true : false;

                    // Calculer la marge selon la catégorie
                    let marge;
                    if (e.categorie === 'Frais de Cabinet') {
                        marge = montant;
                    } else {
                        marge = montant - montantVerseTiers;
                    }

                    // Convertir la date au format YYYY-MM-DD pour les filtres
                    const dateFormatted = typeof e.date === 'string' ? e.date.split('T')[0] : e.date;

                    return {
                        id: e.uuid,
                        date: dateFormatted,
                        ref: e.ref,
                        nom: e.nom,
                        prenoms: e.prenoms,
                        categorie: e.categorie,
                        nature: e.nature,
                        montant: montant,
                        modePaiement: e.mode_paiement,
                        isDocument: isDocument,
                        tiersNom: e.tiers_nom,
                        montantVerseTiers: montantVerseTiers,
                        marge: marge,
                        detailPrestations: e.detail_prestations,
                        createdBy: e.created_by_username,
                        createdByName: e.creator?.name || e.created_by_username || 'N/A',
                        type_payeur: e.type_payeur,
                        payeur_nom_prenom: e.payeur_nom_prenom,
                        payeur_telephone: e.payeur_telephone,
                        payeur_relation: e.payeur_relation,
                        payeur_reference_dossier: e.payeur_reference_dossier
                    };
                });

                dashboardData.sorties = sorties.map(s => {
                    // Convertir la date au format YYYY-MM-DD pour les filtres
                    const dateFormatted = typeof s.date === 'string' ? s.date.split('T')[0] : s.date;

                    return {
                        id: s.uuid,
                        date: dateFormatted,
                        ref: s.ref,
                        fournisseur: s.beneficiaire,
                        nature: s.motif,
                        montant: parseFloat(s.montant),
                        modePaiement: s.mode_paiement,
                        remarques: s.remarques,
                        createdBy: s.created_by_username,
                        createdByName: s.creator?.name || s.created_by_username || 'N/A'
                    };
                });

                console.log('✅ Dashboard: Données formatées et prêtes');
            } catch (error) {
                console.error('❌ Dashboard: Erreur chargement données:', error);
                dashboardData.entrees = [];
                dashboardData.sorties = [];
            }

            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);

            document.getElementById('filterDateDebut').value = formatDate(firstDay);
            document.getElementById('filterDateFin').value = formatDate(today);

            applyFilters();
        }

        function renderKPIs() {
            const {
                totalEntrees,
                totalSorties,
                net,
                margeCabinet,
                totalCabinet,
                margeDocs,
                totalDocs,
                verseTiers,
                dime
            } = filteredData;
            
            const kpiGrid = document.getElementById('kpiGrid');
            
            const kpis = [
                {
                    label: 'Total Entrées',
                    value: formatCurrency(totalEntrees),
                    class: 'success',
                    sub: ''
                },
                {
                    label: 'Total Sorties',
                    value: formatCurrency(totalSorties),
                    class: 'warning',
                    sub: ''
                },
                {
                    label: 'Solde Net',
                    value: formatCurrency(net),
                    class: net >= 0 ? 'info' : 'danger',
                    sub: ''
                },
                {
                    label: 'Frais Cabinet',
                    value: formatCurrency(totalCabinet),
                    class: 'cabinet',
                    sub: `Marge: ${formatCurrency(margeCabinet)}`
                },
                {
                    label: 'Documents',
                    value: formatCurrency(totalDocs),
                    class: 'info',
                    sub: `Versé Tiers: ${formatCurrency(verseTiers)} | Marge: ${formatCurrency(margeDocs)}`
                },
                {
                    label: 'Dîme (10%)',
                    value: formatCurrency(dime),
                    class: 'dime',
                    sub: `Sur marge totale: ${formatCurrency(margeCabinet + margeDocs)}`
                }
            ];
            
            kpiGrid.innerHTML = kpis.map(kpi => `
                <div class="kpi-card ${kpi.class}">
                    <div class="kpi-label">${kpi.label}</div>
                    <div class="kpi-value">${kpi.value}</div>
                    ${kpi.sub ? `<div class="kpi-sub">${kpi.sub}</div>` : ''}
                </div>
            `).join('');
        }

        function renderDocumentDetails() {
            const detailsDiv = document.getElementById('documentsDetails');
            const documents = filteredData.entrees.filter(e => e.isDocument);
            
            if (documents.length === 0) {
                detailsDiv.innerHTML = '<div style="text-align:center;padding:40px;color:var(--text-secondary);">Aucun document</div>';
                return;
            }
            
            let html = `
                <table>
                    <thead>
                        <tr>
                            <th>Réf.</th>
                            <th>Nature</th>
                            <th>Client</th>
                            <th>Tiers</th>
                            <th>Montant Total</th>
                            <th>Montant Tiers</th>
                            <th>Marge Nette</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            let totalMarge = 0;
            
            documents.forEach(doc => {
                totalMarge += doc.marge;
                html += `
                    <tr>
                        <td>${escapeHtml(doc.ref)}</td>
                        <td>${escapeHtml(doc.nature)}</td>
                        <td>${escapeHtml(doc.nom)} ${escapeHtml(doc.prenoms)}</td>
                        <td>${escapeHtml(doc.tiersNom || 'N/A')}</td>
                        <td>${formatCurrency(doc.montant)}</td>
                        <td>${formatCurrency(doc.montantVerseTiers)}</td>
                        <td><span style="font-weight: bold; color: ${doc.marge >= 0 ? '#11998e' : '#f5576c'}">${formatCurrency(doc.marge)}</span></td>
                    </tr>
                `;
            });
            
            html += `
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" style="text-align: right; font-weight: bold;">TOTAL MARGE:</td>
                            <td style="font-weight: bold; color: ${totalMarge >= 0 ? '#11998e' : '#f5576c'}">${formatCurrency(totalMarge)}</td>
                        </tr>
                    </tfoot>
                </table>
            `;
            
            detailsDiv.innerHTML = html;
        }

        function renderChartCashFlow() {
            const canvas = document.getElementById('chartCashFlow');
            const ctx = canvas.getContext('2d');
            
            canvas.width = canvas.offsetWidth;
            canvas.height = 300;
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            const dailyData = {};
            
            filteredData.entrees.forEach(e => {
                if (!dailyData[e.date]) {
                    dailyData[e.date] = { in: 0, out: 0 };
                }
                dailyData[e.date].in += e.montant;
            });
            
            filteredData.sorties.forEach(s => {
                if (!dailyData[s.date]) {
                    dailyData[s.date] = { in: 0, out: 0 };
                }
                dailyData[s.date].out += s.montant;
            });
            
            const dates = Object.keys(dailyData).sort();
            
            if (dates.length === 0) {
                ctx.font = '16px Segoe UI';
                ctx.fillStyle = '#999';
                ctx.textAlign = 'center';
                ctx.fillText('Aucune donnée pour la période', canvas.width / 2, canvas.height / 2);
                return;
            }
            
            let cumulativeBalance = 0;
            const dataPoints = dates.map(date => {
                const { in: inAmount, out: outAmount } = dailyData[date];
                cumulativeBalance += inAmount - outAmount;
                return {
                    date: date,
                    in: inAmount,
                    out: outAmount,
                    balance: cumulativeBalance
                };
            });
            
            const maxValue = Math.max(...dataPoints.map(d => Math.max(d.in, d.out, Math.abs(d.balance)))) || 1;
            const chartHeight = canvas.height - 100;
            const chartWidth = canvas.width - 100;
            const barWidth = Math.max(20, chartWidth / dates.length / 3);
            
            ctx.strokeStyle = '#ddd';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(50, 50);
            ctx.lineTo(50, canvas.height - 50);
            ctx.lineTo(canvas.width - 50, canvas.height - 50);
            ctx.stroke();
            
            dataPoints.forEach((point, index) => {
                const x = 50 + (index * chartWidth / dataPoints.length) + (chartWidth / dataPoints.length / 2);
                const inHeight = (point.in / maxValue) * chartHeight;
                const outHeight = (point.out / maxValue) * chartHeight;
                const balanceY = canvas.height - 50 - ((point.balance / maxValue) * chartHeight * 0.5);
                
                ctx.fillStyle = '#38ef7d';
                ctx.fillRect(x - barWidth * 1.5, canvas.height - 50 - inHeight, barWidth, inHeight);
                
                ctx.fillStyle = '#f5576c';
                ctx.fillRect(x - barWidth * 0.5, canvas.height - 50 - outHeight, barWidth, outHeight);
                
                ctx.fillStyle = '#4facfe';
                ctx.beginPath();
                ctx.arc(x + barWidth, balanceY, 4, 0, Math.PI * 2);
                ctx.fill();
                
                if (index > 0) {
                    const prevX = 50 + ((index - 1) * chartWidth / dataPoints.length) + (chartWidth / dataPoints.length / 2) + barWidth;
                    const prevBalance = dataPoints[index - 1].balance;
                    const prevY = canvas.height - 50 - ((prevBalance / maxValue) * chartHeight * 0.5);
                    
                    ctx.strokeStyle = '#4facfe';
                    ctx.lineWidth = 2;
                    ctx.beginPath();
                    ctx.moveTo(prevX, prevY);
                    ctx.lineTo(x + barWidth, balanceY);
                    ctx.stroke();
                }
                
                ctx.fillStyle = '#333';
                ctx.font = '10px Segoe UI';
                ctx.textAlign = 'center';
                ctx.fillText(point.date.slice(5), x, canvas.height - 30);
            });
            
            ctx.font = '12px Segoe UI';
            ctx.fillStyle = '#38ef7d';
            ctx.fillRect(canvas.width - 180, 20, 15, 15);
            ctx.fillStyle = '#333';
            ctx.fillText('Entrées', canvas.width - 100, 32);
            
            ctx.fillStyle = '#f5576c';
            ctx.fillRect(canvas.width - 180, 40, 15, 15);
            ctx.fillStyle = '#333';
            ctx.fillText('Sorties', canvas.width - 100, 52);
            
            ctx.fillStyle = '#4facfe';
            ctx.fillRect(canvas.width - 180, 60, 15, 15);
            ctx.fillStyle = '#333';
            ctx.fillText('Solde', canvas.width - 100, 72);
        }

        function renderChartNature() {
            const canvas = document.getElementById('chartNature');
            const ctx = canvas.getContext('2d');
            
            canvas.width = canvas.offsetWidth;
            canvas.height = 300;
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            const natureData = {};
            filteredData.entrees.forEach(e => {
                natureData[e.nature] = (natureData[e.nature] || 0) + e.montant;
            });
            
            if (Object.keys(natureData).length === 0) {
                ctx.font = '16px Segoe UI';
                ctx.fillStyle = '#999';
                ctx.textAlign = 'center';
                ctx.fillText('Aucune donnée', canvas.width / 2, canvas.height / 2);
                return;
            }
            
            const total = Object.values(natureData).reduce((sum, v) => sum + v, 0) || 1;
            const colors = ['#667eea', '#38ef7d', '#ffc107', '#f5576c'];
            
            let startAngle = 0;
            const centerX = canvas.width / 2 - 100;
            const centerY = canvas.height / 2;
            const radius = Math.min(centerX, centerY) - 20;
            
            Object.keys(natureData).forEach((nature, index) => {
                const value = natureData[nature];
                const sliceAngle = (value / total) * Math.PI * 2;
                
                ctx.fillStyle = colors[index % colors.length];
                ctx.beginPath();
                ctx.moveTo(centerX, centerY);
                ctx.arc(centerX, centerY, radius, startAngle, startAngle + sliceAngle);
                ctx.closePath();
                ctx.fill();
                
                ctx.fillStyle = colors[index % colors.length];
                ctx.fillRect(canvas.width - 220, 20 + (index * 30), 15, 15);
                ctx.fillStyle = '#333';
                ctx.font = '12px Segoe UI';
                ctx.textAlign = 'left';
                ctx.fillText(`${nature}: ${formatCurrency(value)} (${(value / total * 100).toFixed(1)}%)`, canvas.width - 200, 32 + (index * 30));
                
                startAngle += sliceAngle;
            });
        }

        function renderChartMargeDime() {
            const canvas = document.getElementById('chartMargeDime');
            const ctx = canvas.getContext('2d');
            
            canvas.width = canvas.offsetWidth;
            canvas.height = 300;
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            const dailyMarge = {};
            filteredData.entrees.forEach(e => {
                if (!dailyMarge[e.date]) {
                    dailyMarge[e.date] = { marge: 0 };
                }
                dailyMarge[e.date].marge += e.marge;
            });
            
            if (Object.keys(dailyMarge).sort().length === 0) {
                ctx.font = '16px Segoe UI';
                ctx.fillStyle = '#999';
                ctx.textAlign = 'center';
                ctx.fillText('Aucune donnée', canvas.width / 2, canvas.height / 2);
                return;
            }
            
            const dataPoints = Object.keys(dailyMarge).sort().map(date => ({
                date: date,
                marge: dailyMarge[date].marge,
                dime: dailyMarge[date].marge * 0.1
            }));
            
            const maxValue = Math.max(...dataPoints.map(d => d.marge)) || 1;
            const chartHeight = canvas.height - 100;
            const chartWidth = canvas.width - 100;
            
            ctx.strokeStyle = '#ddd';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(50, 50);
            ctx.lineTo(50, canvas.height - 50);
            ctx.lineTo(canvas.width - 50, canvas.height - 50);
            ctx.stroke();
            
            dataPoints.forEach((point, index) => {
                const x = 50 + (index * chartWidth / (dataPoints.length - 1 || 1));
                const margeY = canvas.height - 50 - ((point.marge / maxValue) * chartHeight);
                const dimeY = canvas.height - 50 - ((point.dime / maxValue) * chartHeight);
                
                ctx.fillStyle = '#fa709a';
                ctx.beginPath();
                ctx.arc(x, margeY, 4, 0, Math.PI * 2);
                ctx.fill();
                
                if (index > 0) {
                    const prevX = 50 + ((index - 1) * chartWidth / (dataPoints.length - 1));
                    const prevMargeY = canvas.height - 50 - ((dataPoints[index - 1].marge / maxValue) * chartHeight);
                    
                    ctx.strokeStyle = '#fa709a';
                    ctx.lineWidth = 2;
                    ctx.beginPath();
                    ctx.moveTo(prevX, prevMargeY);
                    ctx.lineTo(x, margeY);
                    ctx.stroke();
                }
                
                ctx.fillStyle = '#fee140';
                ctx.beginPath();
                ctx.arc(x, dimeY, 4, 0, Math.PI * 2);
                ctx.fill();
                
                if (index > 0) {
                    const prevX = 50 + ((index - 1) * chartWidth / (dataPoints.length - 1));
                    const prevDimeY = canvas.height - 50 - ((dataPoints[index - 1].dime / maxValue) * chartHeight);
                    
                    ctx.strokeStyle = '#fee140';
                    ctx.lineWidth = 2;
                    ctx.beginPath();
                    ctx.moveTo(prevX, prevDimeY);
                    ctx.lineTo(x, dimeY);
                    ctx.stroke();
                }
                
                ctx.fillStyle = '#333';
                ctx.font = '10px Segoe UI';
                ctx.textAlign = 'center';
                ctx.fillText(point.date.slice(5), x, canvas.height - 30);
            });
            
            ctx.font = '12px Segoe UI';
            ctx.fillStyle = '#fa709a';
            ctx.fillRect(canvas.width - 150, 20, 15, 15);
            ctx.fillStyle = '#333';
            ctx.fillText('Marge', canvas.width - 100, 32);
            
            ctx.fillStyle = '#fee140';
            ctx.fillRect(canvas.width - 150, 40, 15, 15);
            ctx.fillStyle = '#333';
            ctx.fillText('Dîme (10%)', canvas.width - 100, 52);
        }

        function backupData() {
            if (!ensureAuth()) return;
            
            const backup = {
                entries: getEntrees(),
                exits: getSorties(),
                users: getUsers(),
                seq: getSeq(),
                budgets: getBudgets(),
                depenses: getDepenses(),
                clotures: getClotures(),
                audit: JSON.parse(localStorage.getItem('ec_audit') || '[]'),
                version: CONFIG.VERSION,
                exportDate: new Date().toISOString()
            };
            
            const json = JSON.stringify(backup, null, 2);
            const blob = new Blob([json], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            
            const a = document.createElement('a');
            a.href = url;
            a.download = `backup_psi_${new Date().toISOString().split('T')[0]}.json`;
            a.click();
            
            URL.revokeObjectURL(url);
            logAudit('BACKUP', 'Data backed up');
            showToast('Sauvegarde créée', 'success');
        }

        function restoreData() {
            if (!ensureAuth() || currentUser.role !== 'admin') {
                showToast('Seul un admin peut restaurer', 'error');
                return;
            }
            
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = '.json';
            
            input.onchange = async (e) => {
                const file = e.target.files[0];
                if (!file) return;
                
                try {
                    const text = await file.text();
                    const backup = JSON.parse(text);
                    
                    if (!backup.entries || !backup.exits) {
                        throw new Error('Format invalide');
                    }
                    
                    if (confirm('Restaurer ces données ? Cela écrasera les données actuelles.')) {
                        saveEntrees(backup.entries);
                        saveSorties(backup.exits);
                        if (backup.users) saveUsers(backup.users);
                        if (backup.seq) saveSeq(backup.seq);
                        if (backup.budgets) saveBudgets(backup.budgets);
                        if (backup.depenses) saveDepenses(backup.depenses);
                        if (backup.clotures) saveClotures(backup.clotures);
                        
                        logAudit('RESTORE', 'Data restored from backup');
                        showToast('Données restaurées', 'success');
                        
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                } catch (error) {
                    showToast('Erreur de restauration: ' + error.message, 'error');
                }
            };
            
            input.click();
        }

        function exportCSV(type) {
            if (!ensureAuth() || !hasPermission('dashboard')) return;
            
            let data, headers, filename;
            
            if (type === 'entrees') {
                data = filteredData.entrees.map(e => ({
                    Date: e.date,
                    Reference: e.ref,
                    Nom: e.nom,
                    Prenoms: e.prenoms,
                    Nature: e.nature,
                    Montant: e.montant,
                    Mode: e.modePaiement,
                    Tiers: e.tiersNom || '',
                    Montant_Tiers: e.montantVerseTiers,
                    Marge: e.marge
                }));
                headers = ['Date', 'Reference', 'Nom', 'Prenoms', 'Nature', 'Montant', 'Mode', 'Tiers', 'Montant_Tiers', 'Marge'];
                filename = 'entrees.csv';
            } else if (type === 'sorties') {
                data = filteredData.sorties.map(s => ({
                    Date: s.date,
                    Reference: s.ref,
                    Nature: s.nature,
                    Fournisseur: s.fournisseur,
                    Montant: s.montant
                }));
                headers = ['Date', 'Reference', 'Nature', 'Fournisseur', 'Montant'];
                filename = 'sorties.csv';
            } else if (type === 'depenses') {
                const periode = document.getElementById('filterDepenseMois').value;
                data = getDepenses().filter(d => d.periode === periode).map(d => ({
                    Periode: d.periode,
                    Date: d.date,
                    Nature: d.nature,
                    Montant: d.montant,
                    Beneficiaire: d.beneficiaire,
                    Date_Paiement: d.datePaiement || '',
                    Mode_Paiement: d.modePaiement,
                    Statut: d.statut,
                    Observations: d.observations || ''
                }));
                headers = ['Periode', 'Date', 'Nature', 'Montant', 'Beneficiaire', 'Date_Paiement', 'Mode_Paiement', 'Statut', 'Observations'];
                filename = `depenses_${periode}.csv`;
            }
            
            if (data.length === 0) {
                showToast('Aucune donnée à exporter', 'warning');
                return;
            }
            
            let csv = headers.join(';') + '\n';
            
            data.forEach(row => {
                csv += headers.map(header => {
                    let value = row[header];
                    if (typeof value === 'string' && value.includes(';')) {
                        value = `"${value}"`;
                    }
                    return value;
                }).join(';') + '\n';
            });
            
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            
            link.setAttribute('href', url);
            link.setAttribute('download', filename);
            link.click();
            
            URL.revokeObjectURL(url);
            logAudit('EXPORT', `Exported ${type}`);
            showToast('Export terminé', 'success');
        }

        function resetDemo() {
            if (!ensureAuth() || currentUser.role !== 'admin') return;
            
            if (confirm('ATTENTION: Réinitialiser toutes les données ?')) {
                localStorage.clear();
                showToast('Données réinitialisées', 'warning');
                setTimeout(() => location.reload(), 1500);
            }
        }

        function getMoisSuivant(periode) {
            const [year, month] = periode.split('-').map(Number);
            const nextMonth = month === 12 ? 1 : month + 1;
            const nextYear = month === 12 ? year + 1 : year;
            return `${nextYear}-${String(nextMonth).padStart(2, '0')}`;
        }

        function getMoisPrecedent(periode) {
            const [year, month] = periode.split('-').map(Number);
            const prevMonth = month === 1 ? 12 : month - 1;
            const prevYear = month === 1 ? year - 1 : year;
            return `${prevYear}-${String(prevMonth).padStart(2, '0')}`;
        }

        function isMoisCloture(periode) {
            const clotures = getClotures();
            return clotures.some(c => c.periode === periode);
        }

        function getReportMoisPrecedent(periode) {
            const clotures = getClotures();
            const moisPrecedent = getMoisPrecedent(periode);
            const cloture = clotures.find(c => c.periode === moisPrecedent);
            return cloture ? cloture.soldeFinal : 0;
        }

        function updateBudgetTotal() {
            const banque = parseFloat(document.getElementById('budgetBanqueInput').value) || 0;
            const caisse = calculateCurrentCaisse();
            const total = caisse + banque;
            
            document.getElementById('budgetCaissePreview').textContent = formatCurrency(caisse);
            document.getElementById('budgetBanquePreview').textContent = formatCurrency(banque);
            document.getElementById('budgetTotalPreview').textContent = formatCurrency(total);
        }

        function calculateCurrentCaisse() {
            const entrees = getEntrees();
            const sorties = getSorties();
            const totalEntrees = entrees.reduce((sum, e) => sum + e.montant, 0);
            const totalSorties = sorties.reduce((sum, s) => sum + s.montant, 0);
            return totalEntrees - totalSorties;
        }

        function loadCurrentMonthBudget() {
            const today = new Date();
            const currentMonth = formatDate(new Date(today.getFullYear(), today.getMonth(), 1)).slice(0, 7);
            
            document.getElementById('budgetMois').value = currentMonth;
            document.getElementById('filterDepenseMois').value = currentMonth;
            
            const budgets = getBudgets();
            const budget = budgets.find(b => b.periode === currentMonth);
            
            if (budget) {
                document.getElementById('budgetBanqueInput').value = budget.banque;
            } else {
                document.getElementById('budgetBanqueInput').value = 0;
            }
            
            updateBudgetTotal();
            updateBudgetSummary();
            filterDepenses();
            renderHistoireClotures();
        }

        function saveBudgetConfig(event) {
            event.preventDefault();
            
            if (!ensureAuth() || currentUser.role !== 'admin') {
                showToast('Accès réservé aux administrateurs', 'error');
                return;
            }
            
            const periode = document.getElementById('budgetMois').value;
            const banque = parseFloat(document.getElementById('budgetBanqueInput').value) || 0;
            const caisse = calculateCurrentCaisse();
            
            if (!periode) {
                showToast('Veuillez sélectionner un mois', 'error');
                return;
            }
            
            const budgets = getBudgets();
            const existingIndex = budgets.findIndex(b => b.periode === periode);
            
            const budget = {
                periode: periode,
                caisse: caisse,
                banque: banque,
                total: caisse + banque,
                createdAt: existingIndex >= 0 ? budgets[existingIndex].createdAt : new Date().toISOString(),
                updatedAt: new Date().toISOString()
            };
            
            if (existingIndex >= 0) {
                budgets[existingIndex] = budget;
                showToast('Trésorerie mise à jour', 'success');
            } else {
                budgets.push(budget);
                showToast('Trésorerie créée', 'success');
            }
            
            saveBudgets(budgets);
            logAudit('SAVE_TRESORERIE', `Trésorerie for ${periode} saved`);
            updateBudgetSummary();
        }

        function updateBudgetSummary() {
            const periode = document.getElementById('filterDepenseMois').value;
            if (!periode) return;

            const estCloture = isMoisCloture(periode);
            
            document.getElementById('actionCloture').style.display = estCloture ? 'none' : 'block';
            document.getElementById('moisCloture').style.display = estCloture ? 'block' : 'none';
            
            const statutBadge = document.getElementById('statutMois');
            if (estCloture) {
                statutBadge.textContent = 'CLÔTURÉ';
                statutBadge.className = 'badge badge-blocked';
            } else {
                statutBadge.textContent = 'OUVERT';
                statutBadge.className = 'badge badge-active';
            }

            const formBudget = document.getElementById('formBudget');
            const formDepense = document.getElementById('formDepense');
            const inputs = [...formBudget.querySelectorAll('input, button'), ...formDepense.querySelectorAll('input, select, textarea, button')];
            inputs.forEach(input => {
                input.disabled = estCloture;
            });

            const budgets = getBudgets();
            let budget = budgets.find(b => b.periode === periode);
            
            const reportPrecedent = getReportMoisPrecedent(periode);
            
            if (reportPrecedent > 0) {
                document.getElementById('reportMoisPrecedent').style.display = 'block';
                document.getElementById('montantReport').textContent = formatCurrency(reportPrecedent);
                const moisPrec = getMoisPrecedent(periode);
                const [year, month] = moisPrec.split('-');
                const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                                   'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                document.getElementById('periodeReport').textContent = `${monthNames[parseInt(month) - 1]} ${year}`;
                document.getElementById('avecReport').textContent = '+ Report';
            } else {
                document.getElementById('reportMoisPrecedent').style.display = 'none';
                document.getElementById('avecReport').textContent = '';
            }
            
            if (!budget) {
                const caisse = calculateCurrentCaisse();
                budget = {
                    periode: periode,
                    caisse: caisse,
                    banque: 0,
                    total: caisse + reportPrecedent
                };
            } else {
                budget.caisse = calculateCurrentCaisse();
                budget.total = budget.caisse + budget.banque + reportPrecedent;
            }
            
            const depenses = getDepenses().filter(d => d.periode === periode && d.statut === 'EXÉCUTÉ');
            const totalDepenses = depenses.reduce((sum, d) => sum + d.montant, 0);
            
            const soldeTresorerie = budget.total - totalDepenses;
            
            document.getElementById('tresorerieCaisse').textContent = formatCurrency(budget.caisse);
            document.getElementById('tresorerieBanque').textContent = formatCurrency(budget.banque);
            document.getElementById('tresorerieTotal').textContent = formatCurrency(budget.total);
            document.getElementById('tresorerieDepenses').textContent = formatCurrency(totalDepenses);
            document.getElementById('tresorerieSolde').textContent = formatCurrency(soldeTresorerie);
            
            const soldeElement = document.getElementById('tresorerieSolde');
            if (soldeTresorerie >= 0) {
                soldeElement.style.color = 'var(--success)';
            } else {
                soldeElement.style.color = 'var(--danger)';
            }
            
            const [year, month] = periode.split('-');
            const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                               'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
            const monthName = monthNames[parseInt(month) - 1];
            document.getElementById('depensesMoisActuel').textContent = `${monthName} ${year}`;
            document.getElementById('depensesListMois').textContent = `${monthName} ${year}`;
        }

        function saveDepense(event) {
            event.preventDefault();
            
            if (!ensureAuth() || currentUser.role !== 'admin') {
                showToast('Accès réservé aux administrateurs', 'error');
                return;
            }

            const date = document.getElementById('depenseDate').value;
            const periode = date.slice(0, 7);

            if (isMoisCloture(periode)) {
                showToast('Impossible de modifier un mois clôturé', 'error');
                return;
            }
            
            const id = document.getElementById('depenseId').value || generateUID();
            const nature = document.getElementById('depenseNature').value;
            const montant = parseFloat(document.getElementById('depenseMontant').value);
            const beneficiaire = document.getElementById('depenseBeneficiaire').value.trim();
            const datePaiement = document.getElementById('depenseDatePaiement').value;
            const modePaiement = document.getElementById('depenseModePaiement').value;
            const statut = document.getElementById('depenseStatut').value;
            const observations = document.getElementById('depenseObservations').value.trim();
            
            if (!date || !nature || !montant || !beneficiaire || !modePaiement || !statut) {
                showToast('Veuillez remplir tous les champs obligatoires', 'error');
                return;
            }
            
            const depenses = getDepenses();
            const isUpdate = depenses.some(d => d.id === id);
            
            if (isUpdate) {
                const oldDepense = depenses.find(d => d.id === id);
                if (oldDepense.periode !== periode && isMoisCloture(periode)) {
                    showToast('Impossible de déplacer vers un mois clôturé', 'error');
                    return;
                }
            }
            
            const depense = {
                id: id,
                periode: periode,
                date: date,
                nature: nature,
                montant: montant,
                beneficiaire: beneficiaire,
                datePaiement: datePaiement || null,
                modePaiement: modePaiement,
                statut: statut,
                observations: observations,
                createdAt: isUpdate ? depenses.find(d => d.id === id).createdAt : new Date().toISOString(),
                updatedAt: new Date().toISOString()
            };
            
            if (isUpdate) {
                const index = depenses.findIndex(d => d.id === id);
                depenses[index] = depense;
                logAudit('UPDATE_DEPENSE', `Dépense ${nature} updated`);
                showToast('Dépense modifiée', 'success');
            } else {
                depenses.push(depense);
                logAudit('ADD_DEPENSE', `Dépense ${nature} added`);
                showToast('Dépense ajoutée', 'success');
            }
            
            saveDepenses(depenses);
            cancelDepense();
            filterDepenses();
            updateBudgetSummary();
        }

        function editDepense(id) {
            if (!ensureAuth() || currentUser.role !== 'admin') return;
            
            const depense = getDepenses().find(d => d.id === id);
            if (!depense) return;
            
            document.getElementById('depenseId').value = depense.id;
            document.getElementById('depenseDate').value = depense.date;
            document.getElementById('depenseNature').value = depense.nature;
            document.getElementById('depenseMontant').value = depense.montant;
            document.getElementById('depenseBeneficiaire').value = depense.beneficiaire;
            document.getElementById('depenseDatePaiement').value = depense.datePaiement || '';
            document.getElementById('depenseModePaiement').value = depense.modePaiement;
            document.getElementById('depenseStatut').value = depense.statut;
            document.getElementById('depenseObservations').value = depense.observations || '';
            
            document.getElementById('btnSaveDepense').textContent = 'Modifier';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function cancelDepense() {
            document.getElementById('formDepense').reset();
            document.getElementById('depenseId').value = '';
            document.getElementById('btnSaveDepense').textContent = 'Ajouter';
        }

        function deleteDepense(id) {
            if (!ensureAuth() || currentUser.role !== 'admin') return;
            
            let depenses = getDepenses();
            const depense = depenses.find(d => d.id === id);

            if (isMoisCloture(depense.periode)) {
                showToast('Impossible de supprimer une dépense d\'un mois clôturé', 'error');
                return;
            }
            
            if (!confirm('Supprimer cette dépense ?')) return;
            
            depenses = depenses.filter(d => d.id !== id);
            saveDepenses(depenses);
            
            logAudit('DELETE_DEPENSE', `Dépense ${depense.nature} deleted`);
            showToast('Dépense supprimée', 'success');
            
            filterDepenses();
            updateBudgetSummary();
        }

        function filterDepenses() {
            if (!ensureAuth() || currentUser.role !== 'admin') return;
            
            const periode = document.getElementById('filterDepenseMois').value;
            const nature = document.getElementById('filterDepenseNature').value;
            const statut = document.getElementById('filterDepenseStatut').value;
            
            if (!periode) {
                loadCurrentMonthBudget();
                return;
            }
            
            const allDepenses = getDepenses();
            const filtered = allDepenses.filter(d => {
                const periodeMatch = d.periode === periode;
                const natureMatch = !nature || d.nature === nature;
                const statutMatch = !statut || d.statut === statut;
                
                return periodeMatch && natureMatch && statutMatch;
            });
            
            updateBudgetSummary();
            renderDepensesTable(filtered, 1);
        }

        function resetDepenseFilters() {
            document.getElementById('filterDepenseNature').value = '';
            document.getElementById('filterDepenseStatut').value = '';
            loadCurrentMonthBudget();
        }

        function renderDepensesTable(depenses, page = 1) {
            if (!ensureAuth() || currentUser.role !== 'admin') return;
            
            currentPageDepenses = page;
            
            const tableDiv = document.getElementById('depensesTable');
            const paginationDiv = document.getElementById('depensesPagination');
            
            if (depenses.length === 0) {
                tableDiv.innerHTML = '<div style="text-align:center;padding:40px;color:var(--text-secondary);">Aucune dépense pour cette période</div>';
                paginationDiv.innerHTML = '';
                return;
            }
            
            const totalPages = Math.ceil(depenses.length / CONFIG.PAGE_SIZE);
            const startIndex = (page - 1) * CONFIG.PAGE_SIZE;
            const pageDepenses = depenses.slice(startIndex, startIndex + CONFIG.PAGE_SIZE);
            
            let html = `
                <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Période</th>
                            <th>Date</th>
                            <th>Nature</th>
                            <th>Montant</th>
                            <th>Bénéficiaire</th>
                            <th>Date Paiement</th>
                            <th>Mode</th>
                            <th>Statut</th>
                            <th>Observations</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            pageDepenses.forEach(depense => {
                const statutClass = depense.statut === 'EXÉCUTÉ' ? 'badge-active' : 'badge-blocked';
                const [year, month] = depense.periode.split('-');
                const periodeText = `${month}/${year}`;
                const estCloture = isMoisCloture(depense.periode);
                
                html += `
                    <tr style="${estCloture ? 'opacity: 0.7; background: var(--bg-main);' : ''}">
                        <td>${periodeText} ${estCloture ? '<span class="badge badge-blocked" style="font-size: 10px;">CLÔTURÉ</span>' : ''}</td>
                        <td>${depense.date}</td>
                        <td>${escapeHtml(depense.nature)}</td>
                        <td style="font-weight: bold;">${formatCurrency(depense.montant)}</td>
                        <td>${escapeHtml(depense.beneficiaire)}</td>
                        <td>${depense.datePaiement || '-'}</td>
                        <td><span class="badge badge-info">${escapeHtml(depense.modePaiement)}</span></td>
                        <td><span class="badge ${statutClass}">${escapeHtml(depense.statut)}</span></td>
                        <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${escapeHtml(depense.observations || '')}">${escapeHtml(depense.observations || '-')}</td>
                        <td class="action-btns">
                            <button class="btn btn-warning" onclick="editDepense('${depense.id}')" ${estCloture ? 'disabled' : ''}>✏️</button>
                            <button class="btn btn-danger" onclick="deleteDepense('${depense.id}')" ${estCloture ? 'disabled' : ''}>🗑️</button>
                        </td>
                    </tr>
                `;
            });
            
            html += '</tbody></table></div>';
            tableDiv.innerHTML = html;
            
            const filteredDepenses = getDepenses().filter(d => d.periode === document.getElementById('filterDepenseMois').value);
            paginationDiv.innerHTML = `
                <button onclick="renderDepensesTable(${JSON.stringify(filteredDepenses).replace(/'/g, "\\'")}, ${page - 1})" ${page === 1 ? 'disabled' : ''}>
                    ‹ Précédent
                </button>
                <span>Page ${page} / ${totalPages}</span>
                <button onclick="renderDepensesTable(${JSON.stringify(filteredDepenses).replace(/'/g, "\\'")}, ${page + 1})" ${page === totalPages ? 'disabled' : ''}>
                    Suivant ›
                </button>
            `;
        }

        function cloturerMois() {
            if (!ensureAuth() || currentUser.role !== 'admin') {
                showToast('Accès réservé aux administrateurs', 'error');
                return;
            }

            const periode = document.getElementById('filterDepenseMois').value;
            if (!periode) {
                showToast('Aucun mois sélectionné', 'error');
                return;
            }

            if (isMoisCloture(periode)) {
                showToast('Ce mois est déjà clôturé', 'warning');
                return;
            }

            const budgets = getBudgets();
            const budget = budgets.find(b => b.periode === periode);
            
            if (!budget) {
                showToast('Veuillez d\'abord configurer la trésorerie de ce mois', 'error');
                return;
            }

            const soldeFinal = parseFloat(document.getElementById('tresorerieSolde').textContent.replace(/[^0-9,-]/g, '').replace(',', '.')) || 0;
            const caisse = parseFloat(document.getElementById('tresorerieCaisse').textContent.replace(/[^0-9,-]/g, '').replace(',', '.')) || 0;
            const banque = parseFloat(document.getElementById('tresorerieBanque').textContent.replace(/[^0-9,-]/g, '').replace(',', '.')) || 0;
            const totalDepenses = parseFloat(document.getElementById('tresorerieDepenses').textContent.replace(/[^0-9,-]/g, '').replace(',', '.')) || 0;

            const confirmMessage = `Voulez-vous vraiment clôturer le mois ${periode} ?\n\nSolde final : ${formatCurrency(soldeFinal)}\n\nCe solde sera automatiquement reporté sur le mois suivant.\nLes modifications ne seront plus possibles.`;

            if (!confirm(confirmMessage)) return;

            const clotures = getClotures();
            const cloture = {
                periode: periode,
                soldeFinal: soldeFinal,
                caisse: caisse,
                banque: banque,
                totalDepenses: totalDepenses,
                reportInitial: getReportMoisPrecedent(periode),
                dateCloture: new Date().toISOString(),
                cloturePar: currentUser.username
            };

            clotures.push(cloture);
            saveClotures(clotures);

            const moisSuivant = getMoisSuivant(periode);
            const budgetsSuivant = getBudgets();
            
            if (!budgetsSuivant.some(b => b.periode === moisSuivant)) {
                const nouveauBudget = {
                    periode: moisSuivant,
                    caisse: 0,
                    banque: soldeFinal,
                    total: soldeFinal,
                    reportPrecedent: soldeFinal,
                    createdAt: new Date().toISOString(),
                    updatedAt: new Date().toISOString()
                };
                budgetsSuivant.push(nouveauBudget);
                saveBudgets(budgetsSuivant);
            }

            logAudit('CLOTURE_MOIS', `Mois ${periode} clôturé avec solde ${soldeFinal}`);
            showToast('Mois clôturé avec succès ! Le solde a été reporté.', 'success');

            document.getElementById('filterDepenseMois').value = moisSuivant;
            document.getElementById('budgetMois').value = moisSuivant;
            loadCurrentMonthBudget();
            renderHistoireClotures();
        }

        function renderHistoireClotures() {
            if (!ensureAuth() || currentUser.role !== 'admin') return;

            const clotures = getClotures().sort((a, b) => b.periode.localeCompare(a.periode));
            const container = document.getElementById('histoireClotures');

            if (clotures.length === 0) {
                container.innerHTML = '<div style="text-align:center;padding:40px;color:var(--text-secondary);">Aucune clôture enregistrée</div>';
                return;
            }

            let html = `
                <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Période</th>
                            <th>Report Initial</th>
                            <th>Caisse</th>
                            <th>Banque</th>
                            <th>Total Trésorerie</th>
                            <th>Dépenses</th>
                            <th>Solde Final</th>
                            <th>Date Clôture</th>
                            <th>Clôturé par</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            clotures.forEach(cloture => {
                const [year, month] = cloture.periode.split('-');
                const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                                   'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                const periodeText = `${monthNames[parseInt(month) - 1]} ${year}`;
                const dateCloture = new Date(cloture.dateCloture).toLocaleDateString('fr-FR');
                const totalTresorerie = cloture.caisse + cloture.banque + (cloture.reportInitial || 0);

                html += `
                    <tr>
                        <td style="font-weight: bold;">${periodeText}</td>
                        <td>${formatCurrency(cloture.reportInitial || 0)}</td>
                        <td>${formatCurrency(cloture.caisse)}</td>
                        <td>${formatCurrency(cloture.banque)}</td>
                        <td style="font-weight: bold;">${formatCurrency(totalTresorerie)}</td>
                        <td style="color: var(--danger);">${formatCurrency(cloture.totalDepenses)}</td>
                        <td style="font-weight: bold; color: ${cloture.soldeFinal >= 0 ? 'var(--success)' : 'var(--danger)'}">${formatCurrency(cloture.soldeFinal)}</td>
                        <td>${dateCloture}</td>
                        <td>${escapeHtml(cloture.cloturePar)}</td>
                    </tr>
                `;
            });

            html += '</tbody></table></div>';
            container.innerHTML = html;
        }

        function renderUsersTable() {
            if (!ensureAuth() || !hasPermission('settings')) return;

            const users = getUsers();
            const tableDiv = document.getElementById('usersTable');

            let html = `
                <table>
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Matricule</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Statut</th>
                            <th>Permissions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            users.forEach(user => {
                // Badge du rôle avec couleurs différentes
                let roleBadge = '';
                if (user.role === 'admin' || user.type_user === 'admin') {
                    roleBadge = '<span class="badge badge-admin">Administrateur</span>';
                } else if (user.type_user === 'commercial') {
                    roleBadge = '<span class="badge" style="background: var(--success);">Commercial</span>';
                } else if (user.type_user === 'agent_comptoir') {
                    roleBadge = '<span class="badge" style="background: var(--info);">Agent Comptoir</span>';
                } else {
                    roleBadge = '<span class="badge badge-agent">Agent</span>';
                }

                const statusBadge = user.active
                    ? '<span class="badge badge-active">Actif</span>'
                    : '<span class="badge badge-blocked">Bloqué</span>';

                const permsText = user.permissions ? user.permissions.join(', ') : 'Aucune';
                const isCurrentUser = user.username === currentUser.username;
                const userName = user.name || user.username;
                const matricule = user.matricule || '-';
                const email = user.email || '-';

                html += `
                    <tr>
                        <td>
                            <strong>${escapeHtml(userName)}</strong>
                            ${isCurrentUser ? '<br><span class="badge" style="background: var(--primary); color: white; margin-top: 5px;">(Vous)</span>' : ''}
                        </td>
                        <td><strong>${escapeHtml(matricule)}</strong></td>
                        <td style="font-size: 0.85em;">${escapeHtml(email)}</td>
                        <td>${roleBadge}</td>
                        <td>${statusBadge}</td>
                        <td style="font-size: 0.8em; max-width: 150px;">${escapeHtml(permsText)}</td>
                        <td class="action-btns" style="display: flex; gap: 5px; flex-wrap: wrap;">
                            <button class="btn btn-warning" onclick="editUser('${user.id}')" title="Modifier le rôle" ${isCurrentUser ? 'disabled' : ''}>
                                ✏️
                            </button>
                            <button class="btn btn-info" onclick="editUserPermissions('${user.id}')" title="Gérer les permissions" ${isCurrentUser ? 'disabled' : ''}>
                                🔑
                            </button>
                            <button class="btn ${user.active ? 'btn-danger' : 'btn-success'}" onclick="toggleUserBlock('${user.id}')" title="${user.active ? 'Bloquer' : 'Débloquer'}" ${isCurrentUser ? 'disabled' : ''}>
                                ${user.active ? '🔒' : '🔓'}
                            </button>
                            <button class="btn btn-danger" onclick="deleteUser('${user.id}')" title="Supprimer" ${isCurrentUser || (user.username === 'admin' && user.id !== currentUser.id) ? 'disabled' : ''}>
                                🗑️
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            html += '</tbody></table>';
            tableDiv.innerHTML = html;
        }

        function editUser(userId) {
            if (!ensureAuth() || !hasPermission('settings')) return;

            // Convertir l'ID en nombre pour la comparaison
            const numericUserId = typeof userId === 'string' ? parseInt(userId) : userId;
            const user = getUsers().find(u => {
                const uId = typeof u.id === 'string' ? parseInt(u.id) : u.id;
                return uId === numericUserId;
            });

            if (!user) {
                console.error('Utilisateur non trouvé:', userId);
                showToast('Utilisateur non trouvé', 'error');
                return;
            }

            console.log('Edition de l\'utilisateur:', user);

            // Remplir les champs du formulaire
            document.getElementById('modalUserId').value = user.id;
            document.getElementById('modalUserName').value = user.name || user.username;
            document.getElementById('modalUserUsername').value = user.username;
            document.getElementById('modalUserPassword').value = '';
            document.getElementById('modalUserPasswordConfirm').value = '';
            document.getElementById('modalUserRole').value = user.role;
            document.getElementById('modalUserActive').checked = user.active;
            document.getElementById('modalUserTitle').textContent = `Modifier - ${user.name || user.username}`;

            document.getElementById('modalUser').classList.add('active');
        }

        function closeUserModal() {
            document.getElementById('modalUser').classList.remove('active');
            document.getElementById('formUser').reset();
            // Réinitialiser les champs de mot de passe
            document.getElementById('modalUserPassword').value = '';
            document.getElementById('modalUserPasswordConfirm').value = '';
        }

        async function saveUser(event) {
            event.preventDefault();

            if (!ensureAuth() || !hasPermission('settings')) return;

            const userId = document.getElementById('modalUserId').value;
            const name = document.getElementById('modalUserName').value.trim();
            const newPassword = document.getElementById('modalUserPassword').value;
            const confirmPassword = document.getElementById('modalUserPasswordConfirm').value;
            const role = document.getElementById('modalUserRole').value;
            const active = document.getElementById('modalUserActive').checked;

            // Validation du nom
            if (!name) {
                showToast('Le nom complet est obligatoire', 'error');
                return;
            }

            // Validation du mot de passe si fourni
            if (newPassword || confirmPassword) {
                if (newPassword.length < 8) {
                    showToast('Le mot de passe doit contenir au moins 8 caractères', 'error');
                    return;
                }

                if (newPassword !== confirmPassword) {
                    showToast('Les mots de passe ne correspondent pas', 'error');
                    return;
                }
            }

            // Convertir l'ID pour la comparaison
            const numericUserId = typeof userId === 'string' ? parseInt(userId) : userId;

            let users = getUsers();
            const userIndex = users.findIndex(u => {
                const uId = typeof u.id === 'string' ? parseInt(u.id) : u.id;
                return uId === numericUserId;
            });

            if (userIndex === -1) {
                console.error('Utilisateur non trouvé pour la mise à jour:', userId);
                showToast('Utilisateur non trouvé', 'error');
                return;
            }

            const user = users[userIndex];

            if (user.username === currentUser.username && !active) {
                showToast('Vous ne pouvez pas désactiver votre propre compte', 'error');
                return;
            }

            console.log('Mise à jour de l\'utilisateur:', user.name, '→', name);

            // Mettre à jour les informations de base
            user.name = name;
            user.role = role;
            user.active = active;

            // Mettre à jour le mot de passe si fourni
            if (newPassword) {
                try {
                    user.passwordHash = await hashPasswordPBKDF2(newPassword);
                    console.log('Mot de passe mis à jour pour:', name);
                    logAudit('PASSWORD_CHANGED', `Password changed for user ${name}`);
                } catch (error) {
                    console.error('Erreur lors du hashage du mot de passe:', error);
                    showToast('Erreur lors de la mise à jour du mot de passe', 'error');
                    return;
                }
            }

            // Définir les permissions selon le rôle et le type d'utilisateur
            if (role === 'admin' || user.type_user === 'admin') {
                user.permissions = ['dashboard', 'entries', 'exits', 'settings', 'depenses', 'reports'];
            } else if (user.type_user === 'commercial') {
                user.permissions = ['dashboard', 'entries', 'exits', 'reports'];
            } else if (user.type_user === 'agent_comptoir') {
                user.permissions = ['dashboard', 'entries', 'exits'];
            } else {
                user.permissions = ['dashboard', 'entries', 'exits'];
            }

            users[userIndex] = user;
            saveUsers(users);

            // Mettre à jour currentUser si c'est l'utilisateur connecté
            if (user.username === currentUser.username) {
                currentUser.name = name;
                currentUser.role = role;
                if (newPassword) {
                    currentUser.passwordHash = user.passwordHash;
                }
                console.log('Profil de l\'utilisateur connecté mis à jour');
            }

            const changes = [];
            changes.push(`name: ${name}`);
            changes.push(`role: ${role}`);
            changes.push(`active: ${active}`);
            if (newPassword) changes.push('password updated');

            logAudit('UPDATE_USER', `User ${name} updated - ${changes.join(', ')}`);

            renderUsersTable();
            closeUserModal();

            let message = 'Utilisateur mis à jour avec succès';
            if (newPassword) {
                message += '\n\n✅ Nouveau mot de passe enregistré';
                if (user.username === currentUser.username) {
                    message += '\n⚠️ Vous devrez utiliser ce nouveau mot de passe lors de votre prochaine connexion';
                }
            }

            showToast(message, 'success');
        }

        async function toggleUserBlock(userId) {
            if (!ensureAuth() || !hasPermission('settings')) return;

            // Convertir l'ID pour la comparaison
            const numericUserId = typeof userId === 'string' ? parseInt(userId) : userId;

            let users = getUsers();
            const userIndex = users.findIndex(u => {
                const uId = typeof u.id === 'string' ? parseInt(u.id) : u.id;
                return uId === numericUserId;
            });

            if (userIndex === -1) {
                console.error('Utilisateur non trouvé:', userId);
                showToast('Utilisateur non trouvé', 'error');
                return;
            }

            const user = users[userIndex];

            if (user.username === currentUser.username) {
                showToast('Vous ne pouvez pas modifier votre propre statut', 'error');
                return;
            }

            const action = user.active ? 'bloquer' : 'débloquer';
            const userName = user.name || user.username;

            // Vérifier si c'est un utilisateur CRM
            const isCRMUser = user.matricule && (user.type_user === 'admin' || user.type_user === 'commercial' || user.type_user === 'agent_comptoir');
            let message = `${action.charAt(0).toUpperCase() + action.slice(1)} ${userName} ?`;

            if (isCRMUser) {
                message += `\n\nℹ️ Note: Ceci affecte uniquement l'accès à la caisse.\nL'utilisateur restera actif dans le système CRM.`;
            }

            if (!confirm(message)) return;

            try {
                // Appeler l'API serveur pour bloquer/débloquer l'utilisateur
                const response = await fetch(`/caisse/api/users/${numericUserId}/toggle-block`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Mettre à jour l'utilisateur localement
                    user.active = !data.caisse_blocked;
                    user.caisse_blocked = data.caisse_blocked;
                    users[userIndex] = user;
                    saveUsers(users);

                    console.log('Statut de l\'utilisateur modifié:', userName, 'Active:', user.active);

                    logAudit('TOGGLE_USER_BLOCK', `User ${userName} ${user.active ? 'unblocked' : 'blocked'}`);
                    showToast(data.message, 'success');
                    renderUsersTable();
                } else {
                    showToast(data.message || 'Erreur lors du blocage/déblocage', 'error');
                }
            } catch (error) {
                console.error('Erreur lors du blocage/déblocage:', error);
                showToast('Erreur de connexion au serveur', 'error');
            }
        }

        function deleteUser(userId) {
            if (!ensureAuth() || !hasPermission('settings')) return;

            // Convertir l'ID pour la comparaison
            const numericUserId = typeof userId === 'string' ? parseInt(userId) : userId;

            let users = getUsers();
            const user = users.find(u => {
                const uId = typeof u.id === 'string' ? parseInt(u.id) : u.id;
                return uId === numericUserId;
            });

            if (!user) {
                console.error('Utilisateur non trouvé:', userId);
                showToast('Utilisateur non trouvé', 'error');
                return;
            }

            if (user.username === currentUser.username) {
                showToast('Vous ne pouvez pas supprimer votre propre compte', 'error');
                return;
            }

            // Vérifier si c'est un compte admin protégé
            const currentUserId = typeof currentUser.id === 'string' ? parseInt(currentUser.id) : currentUser.id;
            const userId_numeric = typeof user.id === 'string' ? parseInt(user.id) : user.id;

            if (user.username === 'admin' && userId_numeric !== currentUserId) {
                showToast('Impossible de supprimer le compte admin initial', 'error');
                return;
            }

            const userName = user.name || user.username;

            // Vérifier si c'est un utilisateur CRM
            const isCRMUser = user.matricule && (user.type_user === 'admin' || user.type_user === 'commercial' || user.type_user === 'agent_comptoir');
            let message = `⚠️ ATTENTION ⚠️\n\nSupprimer ${userName} de la caisse ?`;

            if (isCRMUser) {
                message += `\n\nℹ️ Important:\n• Ceci supprime l'accès à la caisse uniquement\n• L'utilisateur reste actif dans le système CRM\n• Pour réactiver l'accès, utilisez "Synchroniser depuis le serveur"`;
            } else {
                message += `\n\nCette action est irréversible.`;
            }

            if (!confirm(message)) return;

            // Filtrer l'utilisateur en comparant les IDs numériques
            users = users.filter(u => {
                const uId = typeof u.id === 'string' ? parseInt(u.id) : u.id;
                return uId !== numericUserId;
            });

            saveUsers(users);

            console.log('Utilisateur supprimé:', userName);

            logAudit('DELETE_USER', `User ${userName} deleted`);
            showToast('Utilisateur supprimé avec succès', 'success');
            renderUsersTable();
        }

        async function resetUserPassword() {
            if (!ensureAuth() || !hasPermission('settings')) return;

            const userId = document.getElementById('modalUserId').value;
            if (!userId) {
                showToast('Aucun utilisateur sélectionné', 'error');
                return;
            }

            // Convertir l'ID pour la comparaison
            const numericUserId = typeof userId === 'string' ? parseInt(userId) : userId;

            let users = getUsers();
            const userIndex = users.findIndex(u => {
                const uId = typeof u.id === 'string' ? parseInt(u.id) : u.id;
                return uId === numericUserId;
            });

            if (userIndex === -1) {
                showToast('Utilisateur non trouvé', 'error');
                return;
            }

            const user = users[userIndex];
            const userName = user.name || user.username;

            // Définir le nouveau mot de passe par défaut
            let newPassword;
            if (user.matricule) {
                // Pour les utilisateurs CRM, réinitialiser au matricule
                newPassword = user.matricule;
            } else {
                // Pour les utilisateurs locaux, mot de passe par défaut
                newPassword = 'PSI@2025';
            }

            const confirmMessage = `🔄 Réinitialiser le mot de passe de ${userName} ?\n\n` +
                `Mot de passe par défaut: ${newPassword}\n\n` +
                `L'utilisateur devra utiliser ce mot de passe pour se reconnecter.\n\n` +
                `💡 Astuce: Vous pouvez aussi définir un mot de passe personnalisé\n` +
                `en utilisant les champs "Nouveau mot de passe" du formulaire.`;

            if (!confirm(confirmMessage)) return;

            try {
                // Hasher le nouveau mot de passe
                user.passwordHash = await hashPasswordPBKDF2(newPassword);
                users[userIndex] = user;
                saveUsers(users);

                // Mettre à jour currentUser si nécessaire
                if (user.username === currentUser.username) {
                    currentUser.passwordHash = user.passwordHash;
                }

                console.log('Mot de passe réinitialisé pour:', userName);

                logAudit('RESET_PASSWORD', `Password reset for user ${userName} to default`);

                // Vider les champs de mot de passe dans la modal
                document.getElementById('modalUserPassword').value = '';
                document.getElementById('modalUserPasswordConfirm').value = '';

                // Afficher le mot de passe temporaire
                alert(`✅ Mot de passe réinitialisé avec succès!\n\nUtilisateur: ${userName}\nMot de passe par défaut: ${newPassword}\n\n⚠️ Notez ce mot de passe, il ne sera plus affiché.\n\n💡 La modal reste ouverte si vous souhaitez modifier d'autres paramètres.`);

                renderUsersTable();
            } catch (error) {
                console.error('Erreur lors de la réinitialisation du mot de passe:', error);
                showToast('Erreur lors de la réinitialisation du mot de passe', 'error');
            }
        }

        function editUserPermissions(userId) {
            if (!ensureAuth() || !hasPermission('settings')) return;

            // Convertir l'ID pour la comparaison
            const numericUserId = typeof userId === 'string' ? parseInt(userId) : userId;
            const user = getUsers().find(u => {
                const uId = typeof u.id === 'string' ? parseInt(u.id) : u.id;
                return uId === numericUserId;
            });

            if (!user) {
                console.error('Utilisateur non trouvé:', userId);
                showToast('Utilisateur non trouvé', 'error');
                return;
            }

            console.log('Edition des permissions pour:', user);

            // Remplir le formulaire
            document.getElementById('modalPermUserId').value = user.id;
            document.getElementById('modalPermUserName').textContent = user.name || user.username;
            document.getElementById('modalPermUserType').textContent = user.type_user || 'N/A';

            // Cocher les permissions actuelles (générales + caisse)
            const userPermissions = user.permissions || [];
            const userCaissePermissions = user.caisse_permissions || [];
            const allUserPermissions = [...userPermissions, ...userCaissePermissions];

            document.querySelectorAll('#permissionsCheckboxes input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = allUserPermissions.includes(checkbox.value);
            });

            // Afficher la modal
            document.getElementById('modalPermissions').classList.add('active');
        }

        function closePermissionsModal() {
            document.getElementById('modalPermissions').classList.remove('active');
        }

        // ==================== GESTION DU REÇU ====================

        function showReceipt(entreeId) {
            const entree = dashboardData.entrees.find(e => e.id === entreeId);
            if (!entree) {
                showToast('Entrée non trouvée', 'error');
                return;
            }

            // Récupérer les données complètes depuis la BDD
            fetch(`/caisse/api/entrees/${entreeId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        generateReceiptHTML(data.data);
                        document.getElementById('modalReceipt').classList.add('active');
                    } else {
                        showToast('Erreur lors du chargement du reçu', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showToast('Erreur de connexion', 'error');
                });
        }

        function generateReceiptHTML(entree) {
            // DEBUG: Afficher les données reçues
            console.log('📄 Génération du reçu pour entrée:', entree);
            console.log('📄 Type payeur reçu:', entree.type_payeur);
            console.log('📄 Payeur nom prenom:', entree.payeur_nom_prenom);

            const today = new Date().toLocaleDateString('fr-FR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            const typePayeurLabel = entree.type_payeur === 'autre_personne' ? 'Autre personne' : 'Lui-même';

            // Toujours afficher la section INFORMATION DU PAYEUR
            let payeurInfoHTML = '';
            if (entree.type_payeur === 'autre_personne') {
                // Si c'est une autre personne, afficher toutes les informations
                payeurInfoHTML = `
                    <div style="background: #fff3cd; padding: 20px; border-radius: 8px; margin-top: 20px; border-left: 4px solid #ffc107;">
                        <h4 style="color: #856404; margin-bottom: 15px;">👤 INFORMATION DU PAYEUR</h4>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Type de payeur:</td>
                                <td style="padding: 8px; border-bottom: 1px solid #ddd;">${typePayeurLabel}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Nom et Prénom:</td>
                                <td style="padding: 8px; border-bottom: 1px solid #ddd;">${escapeHtml(entree.payeur_nom_prenom || '')}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Téléphone:</td>
                                <td style="padding: 8px; border-bottom: 1px solid #ddd;">${escapeHtml(entree.payeur_telephone || '')}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Relation avec le client:</td>
                                <td style="padding: 8px; border-bottom: 1px solid #ddd;">${escapeHtml(entree.payeur_relation || '')}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; font-weight: bold;">Référence du dossier:</td>
                                <td style="padding: 8px;">${escapeHtml(entree.payeur_reference_dossier || entree.ref || '')}</td>
                            </tr>
                        </table>
                    </div>
                `;
            } else {
                // Si c'est lui-même, afficher une version simplifiée
                payeurInfoHTML = `
                    <div style="background: #e8f5e9; padding: 20px; border-radius: 8px; margin-top: 20px; border-left: 4px solid #4caf50;">
                        <h4 style="color: #2e7d32; margin-bottom: 15px;">👤 INFORMATION DU PAYEUR</h4>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Type de payeur:</td>
                                <td style="padding: 8px; border-bottom: 1px solid #ddd;">${typePayeurLabel}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Nom et Prénom:</td>
                                <td style="padding: 8px; border-bottom: 1px solid #ddd;">${escapeHtml(entree.nom)} ${escapeHtml(entree.prenoms)}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; font-weight: bold;">Référence du dossier:</td>
                                <td style="padding: 8px;">${escapeHtml(entree.payeur_reference_dossier || entree.ref || '')}</td>
                            </tr>
                        </table>
                    </div>
                `;
            }

            const html = `
                <div style="max-width: 700px; margin: 0 auto; font-family: Arial, sans-serif;">
                    <div style="text-align: center; margin-bottom: 30px;">
                        <h1 style="color: var(--primary); margin-bottom: 5px;">PSI AFRICA</h1>
                        <p style="color: #666; margin: 5px 0;">Reçu de Paiement</p>
                        <p style="color: #999; font-size: 14px;">Date d'impression: ${today}</p>
                    </div>

                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <h3 style="margin: 0 0 10px 0; color: var(--primary);">Référence: ${escapeHtml(entree.ref)}</h3>
                        <p style="margin: 5px 0;">Date du paiement: ${entree.date}</p>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <h4 style="color: var(--primary); margin-bottom: 10px;">📋 DÉTAILS DU CLIENT</h4>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Nom:</td>
                                <td style="padding: 8px; border-bottom: 1px solid #ddd;">${escapeHtml(entree.nom)}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; font-weight: bold;">Prénoms:</td>
                                <td style="padding: 8px;">${escapeHtml(entree.prenoms)}</td>
                            </tr>
                        </table>
                    </div>

                    ${payeurInfoHTML}

                    <div style="margin: 20px 0;">
                        <h4 style="color: var(--primary); margin-bottom: 10px;">💰 DÉTAILS DU PAIEMENT</h4>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Catégorie:</td>
                                <td style="padding: 8px; border-bottom: 1px solid #ddd;">${escapeHtml(entree.categorie)}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Nature:</td>
                                <td style="padding: 8px; border-bottom: 1px solid #ddd;">${escapeHtml(entree.nature)}</td>
                            </tr>
                            <tr style="background: #e8f5e9;">
                                <td style="padding: 12px; font-weight: bold; font-size: 16px;">Montant payé:</td>
                                <td style="padding: 12px; font-weight: bold; font-size: 18px; color: #2e7d32;">${formatCurrency(entree.montant)}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px; font-weight: bold;">Mode de paiement:</td>
                                <td style="padding: 8px;">${escapeHtml(entree.mode_paiement)}</td>
                            </tr>
                        </table>
                    </div>

                    <div style="margin-top: 40px; padding-top: 20px; border-top: 2px dashed #ddd; text-align: center; color: #666;">
                        <p style="margin: 5px 0;">Merci pour votre confiance</p>
                        <p style="margin: 5px 0; font-size: 12px;">Ce reçu est généré automatiquement par le système de gestion PSI AFRICA</p>
                    </div>
                </div>
            `;

            document.getElementById('receiptContent').innerHTML = html;
        }

        function closeReceiptModal() {
            document.getElementById('modalReceipt').classList.remove('active');
        }

        function printReceipt() {
            const receiptContent = document.getElementById('receiptContent').innerHTML;
            const printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Reçu de Paiement</title>');
            printWindow.document.write('<style>body{font-family:Arial,sans-serif;padding:20px;}</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(receiptContent);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }

        async function saveUserPermissions(event) {
            event.preventDefault();

            if (!ensureAuth() || !hasPermission('settings')) return;

            const userId = document.getElementById('modalPermUserId').value;

            // Convertir l'ID pour la comparaison
            const numericUserId = typeof userId === 'string' ? parseInt(userId) : userId;

            let users = getUsers();
            const userIndex = users.findIndex(u => {
                const uId = typeof u.id === 'string' ? parseInt(u.id) : u.id;
                return uId === numericUserId;
            });

            if (userIndex === -1) {
                showToast('Utilisateur non trouvé', 'error');
                return;
            }

            const user = users[userIndex];

            // Récupérer les permissions cochées
            const allSelectedPermissions = [];
            document.querySelectorAll('#permissionsCheckboxes input[type="checkbox"]:checked').forEach(checkbox => {
                allSelectedPermissions.push(checkbox.value);
            });

            // Séparer les permissions générales des permissions caisse
            const caissePermissionsList = ['modifier_entrees', 'supprimer_entrees', 'modifier_sorties', 'supprimer_sorties'];
            const regularPermissions = allSelectedPermissions.filter(p => !caissePermissionsList.includes(p));
            const caissePermissions = allSelectedPermissions.filter(p => caissePermissionsList.includes(p));

            if (allSelectedPermissions.length === 0) {
                showToast('Veuillez sélectionner au moins une permission', 'error');
                return;
            }

            console.log('Nouvelles permissions générales pour', user.name, ':', regularPermissions);
            console.log('Nouvelles permissions caisse pour', user.name, ':', caissePermissions);

            // Sauvegarder dans la base de données via l'API
            try {
                const response = await fetch(`{{ url('caisse/api/users') }}/${numericUserId}/permissions`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        permissions: regularPermissions,
                        caisse_permissions: caissePermissions
                    })
                });

                const data = await response.json();

                if (data.success) {
                    console.log('✅ Permissions sauvegardées dans la base de données');
                } else {
                    console.warn('⚠️ Erreur lors de la sauvegarde en base:', data.message);
                }
            } catch (error) {
                console.error('❌ Erreur réseau lors de la sauvegarde des permissions:', error);
            }

            // Mettre à jour les permissions localement
            user.permissions = regularPermissions;
            user.caisse_permissions = caissePermissions;
            users[userIndex] = user;
            saveUsers(users);

            // Si c'est l'utilisateur actuellement connecté, mettre à jour currentUser aussi
            if (currentUser) {
                const currentUserId = typeof currentUser.id === 'string' ? parseInt(currentUser.id) : currentUser.id;
                const userId = typeof user.id === 'string' ? parseInt(user.id) : user.id;

                console.log('Comparaison IDs - currentUser:', currentUserId, 'user modifié:', userId);

                if (currentUserId === userId) {
                    currentUser.permissions = regularPermissions;
                    currentUser.caisse_permissions = caissePermissions;
                    console.log('✅ CurrentUser permissions mises à jour:', currentUser.permissions);
                    console.log('✅ CurrentUser caisse_permissions mises à jour:', currentUser.caisse_permissions);
                }
            }

            logAudit('UPDATE_PERMISSIONS', `Permissions updated for user ${user.name || user.username}: ${allSelectedPermissions.join(', ')}`);

            showToast('Permissions mises à jour avec succès', 'success');
            closePermissionsModal();
            renderUsersTable();
        }

        async function createNewUser(event) {
            event.preventDefault();
            
            if (!ensureAuth() || !hasPermission('settings')) return;
            
            const username = document.getElementById('newUserUsername').value.trim();
            const password = document.getElementById('newUserPassword').value;
            const role = document.getElementById('newUserRole').value;
            
            if (!username || !password || !role) {
                showToast('Veuillez remplir tous les champs', 'error');
                return;
            }
            
            if (password.length < 8) {
                showToast('Le mot de passe doit contenir au moins 8 caractères', 'error');
                return;
            }
            
            const users = getUsers();
            
            if (users.some(u => u.username === username)) {
                showToast('Cet utilisateur existe déjà', 'error');
                return;
            }
            
            const permissions = [];
            if (role === 'admin') {
                permissions.push('dashboard', 'entries', 'exits', 'settings', 'depenses');
            } else {
                if (document.getElementById('permDashboard').checked) permissions.push('dashboard');
                if (document.getElementById('permEntries').checked) permissions.push('entries');
                if (document.getElementById('permExits').checked) permissions.push('exits');
                if (document.getElementById('permSettings').checked) permissions.push('settings');
                if (document.getElementById('permDepenses').checked) permissions.push('depenses');
            }
            
            if (permissions.length === 0 && role === 'agent') {
                showToast('Sélectionnez au least une permission', 'error');
                return;
            }
            
            const passwordHash = await hashPasswordPBKDF2(password);
            
            const newUser = {
                id: generateUID(),
                username: username,
                passwordHash: passwordHash,
                role: role,
                permissions: permissions,
                active: true,
                createdAt: new Date().toISOString()
            };
            
            users.push(newUser);
            saveUsers(users);
            
            logAudit('CREATE_USER', `New user ${username} created with role ${role}`);
            showToast(`Utilisateur ${username} créé avec succès`, 'success');
            resetNewUserForm();
            renderUsersTable();
        }

        function resetNewUserForm() {
            document.getElementById('formNewUser').reset();
            document.getElementById('permDashboard').checked = false;
            document.getElementById('permEntries').checked = false;
            document.getElementById('permExits').checked = false;
            document.getElementById('permSettings').checked = false;
            document.getElementById('permDepenses').checked = false;
        }

        // Charger les activités récentes
        async function loadRecentActivities() {
            try {
                const response = await fetch('/caisse/activities?per_page=10');
                const data = await response.json();

                if (data.success && data.activities.length > 0) {
                    renderRecentActivities(data.activities);
                } else {
                    document.getElementById('recentActivities').innerHTML = `
                        <div style="text-align: center; padding: 20px; color: #999;">
                            <i class="fas fa-inbox"></i>
                            <p>Aucune activité récente</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Erreur lors du chargement des activités:', error);
                document.getElementById('recentActivities').innerHTML = `
                    <div style="text-align: center; padding: 20px; color: #dc3545;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Erreur lors du chargement des activités</p>
                    </div>
                `;
            }
        }

        function renderRecentActivities(activities) {
            const container = document.getElementById('recentActivities');
            let html = '<div style="max-height: 400px; overflow-y: auto;">';

            activities.forEach(activity => {
                const date = new Date(activity.created_at);
                const formattedDate = date.toLocaleDateString('fr-FR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
                const formattedTime = date.toLocaleTimeString('fr-FR', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                const actionColor = getActivityColor(activity.action);
                const userName = activity.user ? activity.user.name : activity.user_name;

                html += `
                    <div style="padding: 12px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: start;">
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                                <span style="background-color: ${actionColor}; color: white; padding: 3px 10px; border-radius: 12px; font-size: 0.85em; font-weight: 500;">
                                    ${activity.action}
                                </span>
                                <span style="font-size: 0.85em; color: #666;">
                                    ${formattedDate} à ${formattedTime}
                                </span>
                            </div>
                            <div style="font-size: 0.9em; color: #333; margin-bottom: 3px;">
                                ${activity.details}
                            </div>
                            <div style="font-size: 0.85em; color: #999;">
                                Par: ${userName}
                            </div>
                        </div>
                    </div>
                `;
            });

            html += '</div>';
            container.innerHTML = html;
        }

        function getActivityColor(action) {
            const colors = {
                'Entrée Créée': '#28a745',
                'Entrée Modifiée': '#ffc107',
                'Entrée Supprimée': '#dc3545',
                'Sortie Créée': '#17a2b8',
                'Sortie Modifiée': '#fd7e14',
                'Sortie Supprimée': '#dc3545',
                'Clôture Mensuelle': '#6f42c1'
            };
            return colors[action] || '#6c757d';
        }

        async function initApp() {
            await initStorage();
            loadTheme();

            // Charger les données depuis la base de données
            try {
                console.log('🔄 Chargement des données depuis la base de données...');
                await loadDataFromDB();
                console.log('✅ Données chargées avec succès depuis la base de données');
            } catch (error) {
                console.error('❌ Erreur lors du chargement initial depuis la BDD:', error);
                console.log('⚠️ Utilisation des données localStorage en fallback');
            }

            // Charger les activités récentes
            try {
                await loadRecentActivities();
            } catch (error) {
                console.error('❌ Erreur lors du chargement des activités:', error);
            }

            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);

            document.getElementById('filterDateDebut').value = formatDate(firstDay);
            document.getElementById('filterDateFin').value = formatDate(today);
            document.getElementById('entreeDate').value = formatDate(today);
            document.getElementById('sortieDate').value = formatDate(today);
            document.getElementById('depenseDate').value = formatDate(today);
            document.getElementById('budgetMois').value = formatDate(firstDay).slice(0, 7);
            document.getElementById('filterDepenseMois').value = formatDate(firstDay).slice(0, 7);

            // Event listeners
            document.getElementById('formEntree').addEventListener('submit', saveEntree);
            document.getElementById('formSortie').addEventListener('submit', saveSortie);
            document.getElementById('formUser').addEventListener('submit', saveUser);
            document.getElementById('formBudget').addEventListener('submit', saveBudgetConfig);
            document.getElementById('formDepense').addEventListener('submit', saveDepense);
            document.getElementById('formNewUser').addEventListener('submit', createNewUser);

            // Vérifier si l'utilisateur Laravel est connecté (accès direct)
            @auth
                const laravelUser = @json(auth()->user());
                console.log('Utilisateur Laravel connecté:', laravelUser);

                // Chercher l'utilisateur dans la caisse
                const caisseUser = getUsers().find(u =>
                    u.email === laravelUser.email ||
                    u.matricule === laravelUser.matricule ||
                    u.username === laravelUser.matricule
                );

                if (caisseUser && caisseUser.active) {
                    // Connexion automatique
                    console.log('Connexion automatique à la caisse pour:', caisseUser.name, 'Permissions:', caisseUser.permissions);
                    currentUser = caisseUser;
                    sessionActive = true;

                    // Sauvegarder la session
                    sessionStorage.setItem('ec_session', JSON.stringify({
                        userId: caisseUser.id,
                        username: caisseUser.username,
                        timestamp: Date.now()
                    }));

                    document.getElementById('currentUsername').textContent = caisseUser.name || caisseUser.username;
                    document.getElementById('currentUserRole').textContent =
                        caisseUser.role === 'admin' ? 'Administrateur' : 'Agent';

                    applyPermissions();
                    resetIdleTimer();
                    refreshDashboard();

                    // Démarrer le polling des permissions
                    startPermissionsPolling();

                    showToast('Connexion automatique réussie', 'success');
                    logAudit('AUTO_LOGIN', `User ${caisseUser.username} logged in automatically from Laravel session`);
                    return;
                } else if (caisseUser && !caisseUser.active) {
                    // Utilisateur bloqué dans la caisse
                    showToast('Accès à la caisse bloqué. Contactez l\'administrateur.', 'error');
                    setTimeout(() => {
                        window.location.href = '/home';
                    }, 2000);
                    return;
                } else {
                    // Utilisateur non trouvé dans la caisse
                    showToast('Vous n\'avez pas accès à la caisse. Contactez l\'administrateur.', 'error');
                    setTimeout(() => {
                        window.location.href = '/home';
                    }, 2000);
                    return;
                }
            @endauth

            @guest
                // Utilisateur non authentifié, rediriger vers la page de connexion
                showToast('Vous devez être connecté pour accéder à la caisse', 'error');
                setTimeout(() => {
                    window.location.href = '/login';
                }, 1500);
            @endguest
        }

        // ==================== POLLING DES PERMISSIONS ====================
        let permissionsCheckInterval = null;
        let lastPermissionsCheck = 0;

        async function checkPermissionsUpdate() {
            if (!sessionActive || !currentUser) return;

            try {
                const response = await fetch('{{ route("caisse.api.my-permissions") }}', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) return;

                const data = await response.json();

                if (data.success && data.permissions) {
                    // Vérifier si les permissions ont changé
                    const newPermissions = JSON.stringify(data.permissions.sort());
                    const currentPermissions = JSON.stringify((currentUser.permissions || []).sort());

                    if (newPermissions !== currentPermissions) {
                        console.log('🔄 Permissions mises à jour depuis le serveur');
                        console.log('Anciennes:', currentUser.permissions);
                        console.log('Nouvelles:', data.permissions);

                        // Mettre à jour currentUser
                        currentUser.permissions = data.permissions;

                        // Mettre à jour dans localStorage
                        let users = getUsers();
                        const userIndex = users.findIndex(u => {
                            const uId = typeof u.id === 'string' ? parseInt(u.id) : u.id;
                            const currentUserId = typeof currentUser.id === 'string' ? parseInt(currentUser.id) : currentUser.id;
                            return uId === currentUserId;
                        });

                        if (userIndex !== -1) {
                            users[userIndex].permissions = data.permissions;
                            saveUsers(users);
                        }

                        // Réappliquer les permissions dans l'interface
                        applyPermissions();

                        // Notifier l'utilisateur
                        showToast('Vos permissions ont été mises à jour', 'info');

                        lastPermissionsCheck = data.timestamp;
                    }
                }
            } catch (error) {
                console.error('Erreur lors de la vérification des permissions:', error);
            }
        }

        function startPermissionsPolling() {
            // Vérifier toutes les 10 secondes
            if (permissionsCheckInterval) {
                clearInterval(permissionsCheckInterval);
            }

            permissionsCheckInterval = setInterval(checkPermissionsUpdate, 10000);
            console.log('📡 Polling des permissions démarré (10 secondes)');
        }

        function stopPermissionsPolling() {
            if (permissionsCheckInterval) {
                clearInterval(permissionsCheckInterval);
                permissionsCheckInterval = null;
                console.log('📡 Polling des permissions arrêté');
            }
        }

        // ==================== SYSTÈME DE FILTRAGE PAR MOIS ====================
        let moisSelectionne = null;
        let anneeSelectionnee = null;

        // Charger les mois disponibles au chargement de la page
        async function chargerMoisDisponibles() {
            try {
                const response = await fetch('/caisse/api/mois-disponibles', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });

                if (!response.ok) throw new Error('Erreur chargement mois');

                const data = await response.json();
                const select = document.getElementById('filterMois');

                if (data.success && data.data.length > 0) {
                    select.innerHTML = '';

                    data.data.forEach(mois => {
                        const option = document.createElement('option');
                        option.value = mois.value;
                        option.textContent = mois.label;
                        option.dataset.mois = mois.mois;
                        option.dataset.annee = mois.annee;

                        if (mois.est_mois_actuel) {
                            option.selected = true;
                            moisSelectionne = mois.mois;
                            anneeSelectionnee = mois.annee;
                        }

                        select.appendChild(option);
                    });

                    console.log('✅ Mois disponibles chargés:', data.data.length);
                } else {
                    select.innerHTML = '<option value="">Aucun mois disponible</option>';
                }
            } catch (error) {
                console.error('Erreur chargement mois:', error);
                document.getElementById('filterMois').innerHTML = '<option value="">Erreur chargement</option>';
            }
        }

        // Charger les données selon le mois sélectionné
        function chargerDonneesMois() {
            const select = document.getElementById('filterMois');
            const selectedOption = select.options[select.selectedIndex];

            if (selectedOption && selectedOption.dataset.mois) {
                moisSelectionne = parseInt(selectedOption.dataset.mois);
                anneeSelectionnee = parseInt(selectedOption.dataset.annee);

                console.log(`📅 Mois sélectionné: ${moisSelectionne}/${anneeSelectionnee}`);

                // Recharger le dashboard avec le nouveau filtre
                if (typeof refreshDashboard === 'function') {
                    refreshDashboard();
                }

                // Recharger les autres données si nécessaire
                if (typeof window.syncWithDatabase === 'function') {
                    window.syncWithDatabase();
                } else if (typeof loadDashboardData === 'function') {
                    loadDashboardData();
                }

                showToast(`Affichage des données de ${selectedOption.textContent}`, 'info');
            }
        }

        // Intercepter les appels API pour ajouter les paramètres de mois
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            let [url, options] = args;

            // Ajouter les paramètres de mois si c'est un appel aux API entrées, sorties ou stats
            if (typeof url === 'string' && (url.includes('/caisse/api/entrees') || url.includes('/caisse/api/sorties') || url.includes('/caisse/api/stats'))) {
                if (moisSelectionne && anneeSelectionnee) {
                    const separator = url.includes('?') ? '&' : '?';
                    url += `${separator}mois=${moisSelectionne}&annee=${anneeSelectionnee}`;
                    console.log('🔄 URL modifiée:', url);
                }
            }

            return originalFetch(url, options);
        };

        // Charger les mois au démarrage
        setTimeout(() => {
            chargerMoisDisponibles();
        }, 1000);

        window.addEventListener('DOMContentLoaded', initApp);
    </script>

    <!-- Script de synchronisation avec la base de données -->
    <script src="{{ asset('js/caisse-api-sync.js') }}"></script>
</body>
</html>