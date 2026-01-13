<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PSI AFRICA - Gestion des Contrats CRM</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <!-- PAS DE BOOTSTRAP CDN - Évite les conflits avec le CRM -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <style>
        /* Variables CSS alignées avec le CRM principal */
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #06b6d4;
            --success: #16a34a;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #06b6d4;
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-tertiary: #f1f5f9;
            --text-primary: #1e293b;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --shadow: rgba(0, 0, 0, 0.1);

            /* Alias PSI pour compatibilité */
            --psi-blue: #2563eb;
            --psi-gold: #f59e0b;
            --psi-light: #f8fafc;
            --psi-dark: #1e293b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #f8fafc 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.6;
        }

        /* Système de grille (remplace Bootstrap grid) */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-left: -0.75rem;
            margin-right: -0.75rem;
        }

        .row > * {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .col-md-3 { flex: 0 0 25%; max-width: 25%; }
        .col-md-4 { flex: 0 0 33.333%; max-width: 33.333%; }
        .col-md-5 { flex: 0 0 41.666%; max-width: 41.666%; }
        .col-md-6 { flex: 0 0 50%; max-width: 50%; }
        .col-md-7 { flex: 0 0 58.333%; max-width: 58.333%; }
        .col-md-8 { flex: 0 0 66.666%; max-width: 66.666%; }
        .col-md-9 { flex: 0 0 75%; max-width: 75%; }
        .col-md-12 { flex: 0 0 100%; max-width: 100%; }

        @media (max-width: 768px) {
            .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }

        /* Marges et espacements (remplace Bootstrap utilities) */
        .m-0 { margin: 0; }
        .m-2 { margin: 0.5rem; }
        .m-3 { margin: 1rem; }
        .m-4 { margin: 1.5rem; }
        .mb-0 { margin-bottom: 0; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 1rem; }
        .mb-4 { margin-bottom: 1.5rem; }
        .mb-5 { margin-bottom: 3rem; }
        .mt-1 { margin-top: 0.25rem; }
        .mt-2 { margin-top: 0.5rem; }
        .mt-3 { margin-top: 1rem; }
        .mt-4 { margin-top: 1.5rem; }
        .my-4 { margin-top: 1.5rem; margin-bottom: 1.5rem; }
        .me-2 { margin-right: 0.5rem; }
        .ms-2 { margin-left: 0.5rem; }
        .p-0 { padding: 0; }
        .p-2 { padding: 0.5rem; }
        .p-3 { padding: 1rem; }
        .p-4 { padding: 1.5rem; }
        .px-0 { padding-left: 0; padding-right: 0; }
        .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }

        /* Display utilities */
        .d-none { display: none; }
        .d-block { display: block; }
        .d-flex { display: flex; }
        .d-inline-block { display: inline-block; }

        /* Flexbox utilities */
        .justify-content-start { justify-content: flex-start; }
        .justify-content-end { justify-content: flex-end; }
        .justify-content-center { justify-content: center; }
        .justify-content-between { justify-content: space-between; }
        .justify-content-around { justify-content: space-around; }

        .align-items-start { align-items: flex-start; }
        .align-items-end { align-items: flex-end; }
        .align-items-center { align-items: center; }
        .align-items-baseline { align-items: baseline; }
        .align-items-stretch { align-items: stretch; }

        .flex-wrap { flex-wrap: wrap; }
        .flex-column { flex-direction: column; }

        /* Width utilities */
        .w-100 { width: 100%; }
        .w-75 { width: 75%; }
        .w-50 { width: 50%; }
        .w-25 { width: 25%; }

        /* Height utilities */
        .h-100 { height: 100%; }

        /* Texte */
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-start { text-align: left; }
        .text-muted { color: var(--text-muted); }
        .text-primary { color: var(--primary); }
        .text-success { color: var(--success); }
        .text-danger { color: var(--danger); }
        .text-warning { color: var(--warning); }
        .fw-bold { font-weight: 700; }
        .fw-semibold { font-weight: 600; }
        .fw-normal { font-weight: 400; }
        .fst-italic { font-style: italic; }

        /* Boutons (remplace Bootstrap buttons) */
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            line-height: 1.5;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }

        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.8125rem;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Cards (remplace Bootstrap cards) */
        .card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px var(--shadow);
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        /* Forms (remplace Bootstrap forms) */
        .form-control {
            display: block;
            width: 100%;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.5;
            color: var(--text-primary);
            background-color: white;
            border: 1px solid var(--border);
            border-radius: 0.375rem;
            transition: border-color 0.15s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-primary);
        }

        .form-select {
            display: block;
            width: 100%;
            padding: 0.5rem 2rem 0.5rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.5;
            color: var(--text-primary);
            background-color: white;
            border: 1px solid var(--border);
            border-radius: 0.375rem;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
        }

        .form-check {
            display: block;
            margin-bottom: 0.5rem;
        }

        .form-check-input {
            width: 1.25rem;
            height: 1.25rem;
            margin-right: 0.5rem;
            vertical-align: middle;
        }

        .form-check-label {
            vertical-align: middle;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1;
            border-radius: 0.25rem;
        }

        .badge-success {
            background: var(--success);
            color: white;
        }

        .badge-warning {
            background: var(--warning);
            color: white;
        }

        .badge-danger {
            background: var(--danger);
            color: white;
        }

        .badge-info {
            background: var(--info);
            color: white;
        }

        /* Modals */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1050;
        }

        .modal.show {
            display: flex;
        }

        .modal-dialog {
            position: relative;
            width: 90%;
            max-width: 500px;
            margin: 1.75rem auto;
        }

        .modal-dialog-lg {
            max-width: 800px;
        }

        .modal-content {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }

        .modal-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0;
            width: 1.5rem;
            height: 1.5rem;
            opacity: 0.5;
        }

        .btn-close:hover {
            opacity: 1;
        }

        /* Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            border-bottom: 1px solid var(--border);
        }

        .table thead th {
            background: var(--bg-tertiary);
            font-weight: 600;
            text-align: left;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background: var(--bg-secondary);
        }

        /* List groups */
        .list-group {
            display: flex;
            flex-direction: column;
            padding-left: 0;
            margin-bottom: 0;
        }

        .list-group-item {
            position: relative;
            display: block;
            padding: 0.75rem 1.25rem;
            background-color: white;
            border: 1px solid var(--border);
        }

        .list-group-item:first-child {
            border-top-left-radius: 0.375rem;
            border-top-right-radius: 0.375rem;
        }

        .list-group-item:last-child {
            border-bottom-left-radius: 0.375rem;
            border-bottom-right-radius: 0.375rem;
        }

        .list-group-item + .list-group-item {
            border-top-width: 0;
        }

        .list-group-flush .list-group-item {
            border-right: 0;
            border-left: 0;
            border-radius: 0;
        }

        .list-group-flush .list-group-item:first-child {
            border-top: 0;
        }

        /* Alerts */
        .alert {
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.375rem;
        }

        .alert-success {
            background-color: #d1fae5;
            border-color: #6ee7b7;
            color: #065f46;
        }

        .alert-danger {
            background-color: #fee2e2;
            border-color: #fca5a5;
            color: #991b1b;
        }

        .alert-warning {
            background-color: #fed7aa;
            border-color: #fdba74;
            color: #92400e;
        }

        .alert-info {
            background-color: #cffafe;
            border-color: #67e8f9;
            color: #164e63;
        }

        /* Styles de boutons spécifiques */
        .btn-primary,
        .btn-psi-primary {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .btn-primary:hover,
        .btn-psi-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-secondary,
        .btn-psi-secondary {
            background: var(--warning);
            border-color: var(--warning);
            color: white;
        }

        .btn-secondary:hover,
        .btn-psi-secondary:hover {
            background: #d97706;
            border-color: #d97706;
        }

        .btn-success {
            background: var(--success);
            border-color: var(--success);
            color: white;
        }

        .btn-success:hover {
            background: #15803d;
            border-color: #15803d;
        }

        .btn-danger {
            background: var(--danger);
            border-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
            border-color: #dc2626;
        }

        .btn-info {
            background: var(--info);
            border-color: var(--info);
            color: white;
        }

        .btn-info:hover {
            background: #0891b2;
            border-color: #0891b2;
        }

        .btn-outline-primary {
            background: transparent;
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .btn-outline-secondary {
            background: transparent;
            border-color: var(--text-muted);
            color: var(--text-secondary);
        }

        .btn-outline-secondary:hover {
            background: var(--bg-tertiary);
            border-color: var(--text-muted);
            color: var(--text-primary);
        }

        .btn-outline-danger {
            background: transparent;
            border-color: var(--danger);
            color: var(--danger);
        }

        .btn-outline-danger:hover {
            background: var(--danger);
            border-color: var(--danger);
            color: white;
        }

        .navbar-psi {
            background: var(--psi-blue) !important;
            border-bottom: 3px solid var(--psi-gold);
        }
        
        .logo-container {
            background: white;
            padding: 10px 20px;
            border-radius: 8px;
            margin-right: 15px;
        }
        
        .card-psi {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(10, 36, 99, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card-psi:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.15);
        }

        /* btn-psi-primary et btn-psi-secondary sont définis plus haut avec les couleurs CRM */

        .signature-pad {
            border: 2px dashed #dee2e6;
            background: #f8f9fa;
            cursor: crosshair;
            border-radius: 8px;
            width: 100%;
        }
        
        .contrat-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border-left: 4px solid var(--psi-gold);
        }
        
        .section {
            display: none;
        }
        
        .active-section {
            display: block;
            animation: fadeIn 0.5s ease-in;
        }
        
        .stat-card {
            text-align: center;
            padding: 20px;
            border-radius: 12px;
            color: white;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0;
        }
        
        .badge-psi {
            background: var(--psi-gold);
            color: var(--psi-dark);
            font-weight: 600;
        }
        
        .audit-log {
            border-left: 3px solid var(--psi-blue);
            background: #f8fbff;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 0 8px 8px 0;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideOutRight {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }

        /* Hover effect pour les lignes du tableau */
        .table tbody tr:hover {
            background: var(--bg-secondary);
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        /* .form-control:focus est défini plus haut avec les couleurs CRM */

        .progress-bar-psi {
            background: var(--psi-blue);
        }
        
        .montant-lettres {
            font-style: italic;
            color: var(--psi-blue);
            font-weight: 500;
        }
        
        .pdf-preview {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            min-height: 500px;
            background: white;
            padding: 20px;
            overflow: auto;
        }
        
        .fiche-field {
            border-bottom: 1px dashed #ccc;
            min-height: 25px;
            margin-bottom: 5px;
            padding: 2px 5px;
        }
        
        .mode-paiement {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .echeance-date {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }
        
        .contrat-content {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        
        .contrat-article {
            margin-bottom: 20px;
        }
        
        .contrat-article h4 {
            color: #0A2463;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        /* Alignement avec le CRM principal - même largeur de container */
        .container {
            max-width: 1400px !important;
            margin: 0 auto;
            padding: 1.5rem;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .container {
                max-width: 100% !important;
                padding: 1rem;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container my-4">
        <!-- Tableau de Bord Agent -->
        <div id="dashboard" class="section active-section">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h2 class="fw-bold" style="color: var(--psi-blue);">
                        <i class="bi bi-speedometer2"></i> Tableau de Bord Agent
                    </h2>
                    <p class="lead">Gestion des contrats et fiches d'inscription</p>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-psi-primary me-2" onclick="showSection('nouveau-contrat')">
                        <i class="bi bi-plus-circle"></i> Nouveau Contrat
                    </button>
                    <button class="btn btn-psi-secondary" onclick="showSection('nouvelle-fiche')">
                        <i class="bi bi-person-plus"></i> Nouvelle Fiche
                    </button>
                </div>
            </div>

            <!-- Cartes Statistiques -->
            <div class="row mb-5">
                <div class="col-md-3 mb-3">
                    <div class="stat-card" style="background: linear-gradient(135deg, var(--psi-blue), #1a3a8a);">
                        <div class="stat-number" id="stat-total">0</div>
                        <div>Contrats Créés</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card" style="background: linear-gradient(135deg, #28a745, #20c997);">
                        <div class="stat-number" id="stat-signes">0</div>
                        <div>Contrats Signés</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card" style="background: linear-gradient(135deg, #ffc107, #fd7e14);">
                        <div class="stat-number" id="stat-fiches">0</div>
                        <div>Fiches Créées</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card" style="background: linear-gradient(135deg, #6f42c1, #e83e8c);">
                        <div class="stat-number" id="stat-expires">0</div>
                        <div>Expirés</div>
                    </div>
                </div>
            </div>

            <!-- Actions Rapides -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-psi mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-lightning-charge"></i> Actions Rapides
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-psi-primary btn-lg" onclick="showSection('nouveau-contrat')">
                                            <i class="bi bi-file-earmark-plus"></i><br>
                                            <small>Créer un Contrat</small>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-primary btn-lg" onclick="showSection('liste-contrats')">
                                            <i class="bi bi-list-check"></i><br>
                                            <small>Gérer les Contrats</small>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-psi-secondary btn-lg" onclick="showSection('nouvelle-fiche')">
                                            <i class="bi bi-person-plus"></i><br>
                                            <small>Nouvelle Fiche</small>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Mini Stats -->
                            <div class="row mt-3 text-center">
                                <div class="col-md-4">
                                    <div class="p-2 bg-light rounded">
                                        <h4 class="mb-0 text-primary" id="quick-stat-total">0</h4>
                                        <small class="text-muted">Total Contrats</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-2 bg-light rounded">
                                        <h4 class="mb-0 text-success" id="quick-stat-signes">0</h4>
                                        <small class="text-muted">Signés</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-2 bg-light rounded">
                                        <h4 class="mb-0 text-warning" id="quick-stat-attente">0</h4>
                                        <small class="text-muted">En Attente</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dernières Activités -->
                    <div class="card card-psi">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-clock-history"></i> Dernières Activités
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="audit-logs">
                                <div class="text-center text-muted py-3">
                                    <i class="bi bi-inbox"></i><br>
                                    Aucune activité récente
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Guide Rapide -->
                    <div class="card card-psi mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-info-circle"></i> Guide du Processus
                            </h5>
                        </div>
                        <div class="card-body">
                            <ol class="list-group list-group-flush">
                                <li class="list-group-item px-0">
                                    <strong>1. Création</strong><br>
                                    Contrat ou fiche d'inscription
                                </li>
                                <li class="list-group-item px-0">
                                    <strong>2. Validation</strong><br>
                                    Contrôle et génération du PDF
                                </li>
                                <li class="list-group-item px-0">
                                    <strong>3. Envoi</strong><br>
                                    Lien unique envoyé au candidat
                                </li>
                                <li class="list-group-item px-0">
                                    <strong>4. Signature</strong><br>
                                    Le candidat signe électroniquement
                                </li>
                                <li class="list-group-item px-0">
                                    <strong>5. Archivage</strong><br>
                                    PDF signé verrouillé et sauvegardé
                                </li>
                            </ol>
                        </div>
                    </div>

                    <!-- Statistiques de Performance -->
                    <div class="card card-psi">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-graph-up"></i> Performance
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Taux de signature</span>
                                    <span id="taux-signature">0%</span>
                                </div>
                                <div class="progress mt-1">
                                    <div class="progress-bar progress-bar-psi" id="progress-signature" style="width: 0%"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Fiches complétées</span>
                                    <span id="taux-fiches">0%</span>
                                </div>
                                <div class="progress mt-1">
                                    <div class="progress-bar bg-success" id="progress-fiches" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulaire Nouveau Contrat -->
        <div id="nouveau-contrat" class="section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold" style="color: var(--psi-blue);">
                    <i class="bi bi-file-earmark-plus"></i> Nouveau Contrat d'Assistance Visa
                </h2>
                <button class="btn btn-outline-secondary" onclick="showSection('dashboard')">
                    <i class="bi bi-arrow-left"></i> Retour
                </button>
            </div>

            <form id="form-contrat" onsubmit="creerContrat(event)">
                <div class="card card-psi">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Informations du Candidat</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nom du Candidat *</label>
                                    <input type="text" class="form-control" name="nom" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Prénom du Candidat *</label>
                                    <input type="text" class="form-control" name="prenom" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Date de Naissance *</label>
                                    <input type="date" class="form-control" name="date_naissance" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Lieu de Naissance *</label>
                                    <input type="text" class="form-control" name="lieu_naissance" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Profession *</label>
                                    <input type="text" class="form-control" name="profession" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Téléphone *</label>
                                    <input type="tel" class="form-control" name="telephone" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" placeholder="email@exemple.com (optionnel)">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">N° CNI/Passeport *</label>
                                    <input type="text" class="form-control" name="numero_cni" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Adresse Complète *</label>
                                    <textarea class="form-control" name="adresse" rows="2" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-psi mt-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Détails du Visa et Conditions Financières</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Destination Visa *</label>
                                    <select class="form-select" name="destination_visa" required>
                                        <option value="">-- Sélectionnez un pays --</option>
                                        <optgroup label="🌍 AFRIQUE">
                                            <option value="Afrique du Sud">🇿🇦 Afrique du Sud</option>
                                            <option value="Algérie">🇩🇿 Algérie</option>
                                            <option value="Angola">🇦🇴 Angola</option>
                                            <option value="Bénin">🇧🇯 Bénin</option>
                                            <option value="Botswana">🇧🇼 Botswana</option>
                                            <option value="Burkina Faso">🇧🇫 Burkina Faso</option>
                                            <option value="Burundi">🇧🇮 Burundi</option>
                                            <option value="Cameroun">🇨🇲 Cameroun</option>
                                            <option value="Cap-Vert">🇨🇻 Cap-Vert</option>
                                            <option value="Comores">🇰🇲 Comores</option>
                                            <option value="Congo">🇨🇬 Congo</option>
                                            <option value="Côte d'Ivoire">🇨🇮 Côte d'Ivoire</option>
                                            <option value="Djibouti">🇩🇯 Djibouti</option>
                                            <option value="Égypte">🇪🇬 Égypte</option>
                                            <option value="Éthiopie">🇪🇹 Éthiopie</option>
                                            <option value="Gabon">🇬🇦 Gabon</option>
                                            <option value="Gambie">🇬🇲 Gambie</option>
                                            <option value="Ghana">🇬🇭 Ghana</option>
                                            <option value="Guinée">🇬🇳 Guinée</option>
                                            <option value="Kenya">🇰🇪 Kenya</option>
                                            <option value="Libéria">🇱🇷 Libéria</option>
                                            <option value="Libye">🇱🇾 Libye</option>
                                            <option value="Madagascar">🇲🇬 Madagascar</option>
                                            <option value="Malawi">🇲🇼 Malawi</option>
                                            <option value="Mali">🇲🇱 Mali</option>
                                            <option value="Maroc">🇲🇦 Maroc</option>
                                            <option value="Maurice">🇲🇺 Maurice</option>
                                            <option value="Mauritanie">🇲🇷 Mauritanie</option>
                                            <option value="Mozambique">🇲🇿 Mozambique</option>
                                            <option value="Namibie">🇳🇦 Namibie</option>
                                            <option value="Niger">🇳🇪 Niger</option>
                                            <option value="Nigeria">🇳🇬 Nigeria</option>
                                            <option value="Ouganda">🇺🇬 Ouganda</option>
                                            <option value="RD Congo">🇨🇩 RD Congo</option>
                                            <option value="Rwanda">🇷🇼 Rwanda</option>
                                            <option value="Sénégal">🇸🇳 Sénégal</option>
                                            <option value="Seychelles">🇸🇨 Seychelles</option>
                                            <option value="Somalie">🇸🇴 Somalie</option>
                                            <option value="Soudan">🇸🇩 Soudan</option>
                                            <option value="Tanzanie">🇹🇿 Tanzanie</option>
                                            <option value="Tchad">🇹🇩 Tchad</option>
                                            <option value="Togo">🇹🇬 Togo</option>
                                            <option value="Tunisie">🇹🇳 Tunisie</option>
                                            <option value="Zambie">🇿🇲 Zambie</option>
                                            <option value="Zimbabwe">🇿🇼 Zimbabwe</option>
                                        </optgroup>
                                        <optgroup label="🌎 AMÉRIQUE DU NORD">
                                            <option value="Canada">🇨🇦 Canada</option>
                                            <option value="États-Unis">🇺🇸 États-Unis</option>
                                            <option value="Mexique">🇲🇽 Mexique</option>
                                        </optgroup>
                                        <optgroup label="🌴 AMÉRIQUE CENTRALE & CARAÏBES">
                                            <option value="Bahamas">🇧🇸 Bahamas</option>
                                            <option value="Barbade">🇧🇧 Barbade</option>
                                            <option value="Belize">🇧🇿 Belize</option>
                                            <option value="Costa Rica">🇨🇷 Costa Rica</option>
                                            <option value="Cuba">🇨🇺 Cuba</option>
                                            <option value="République Dominicaine">🇩🇴 République Dominicaine</option>
                                            <option value="El Salvador">🇸🇻 El Salvador</option>
                                            <option value="Guatemala">🇬🇹 Guatemala</option>
                                            <option value="Haïti">🇭🇹 Haïti</option>
                                            <option value="Honduras">🇭🇳 Honduras</option>
                                            <option value="Jamaïque">🇯🇲 Jamaïque</option>
                                            <option value="Nicaragua">🇳🇮 Nicaragua</option>
                                            <option value="Panama">🇵🇦 Panama</option>
                                            <option value="Trinité-et-Tobago">🇹🇹 Trinité-et-Tobago</option>
                                        </optgroup>
                                        <optgroup label="🌎 AMÉRIQUE DU SUD">
                                            <option value="Argentine">🇦🇷 Argentine</option>
                                            <option value="Bolivie">🇧🇴 Bolivie</option>
                                            <option value="Brésil">🇧🇷 Brésil</option>
                                            <option value="Chili">🇨🇱 Chili</option>
                                            <option value="Colombie">🇨🇴 Colombie</option>
                                            <option value="Équateur">🇪🇨 Équateur</option>
                                            <option value="Guyana">🇬🇾 Guyana</option>
                                            <option value="Paraguay">🇵🇾 Paraguay</option>
                                            <option value="Pérou">🇵🇪 Pérou</option>
                                            <option value="Suriname">🇸🇷 Suriname</option>
                                            <option value="Uruguay">🇺🇾 Uruguay</option>
                                            <option value="Venezuela">🇻🇪 Venezuela</option>
                                        </optgroup>
                                        <optgroup label="🌏 ASIE">
                                            <option value="Afghanistan">🇦🇫 Afghanistan</option>
                                            <option value="Bangladesh">🇧🇩 Bangladesh</option>
                                            <option value="Bhoutan">🇧🇹 Bhoutan</option>
                                            <option value="Birmanie">🇲🇲 Birmanie</option>
                                            <option value="Brunei">🇧🇳 Brunei</option>
                                            <option value="Cambodge">🇰🇭 Cambodge</option>
                                            <option value="Chine">🇨🇳 Chine</option>
                                            <option value="Corée du Nord">🇰🇵 Corée du Nord</option>
                                            <option value="Corée du Sud">🇰🇷 Corée du Sud</option>
                                            <option value="Inde">🇮🇳 Inde</option>
                                            <option value="Indonésie">🇮🇩 Indonésie</option>
                                            <option value="Japon">🇯🇵 Japon</option>
                                            <option value="Kazakhstan">🇰🇿 Kazakhstan</option>
                                            <option value="Laos">🇱🇦 Laos</option>
                                            <option value="Malaisie">🇲🇾 Malaisie</option>
                                            <option value="Maldives">🇲🇻 Maldives</option>
                                            <option value="Mongolie">🇲🇳 Mongolie</option>
                                            <option value="Népal">🇳🇵 Népal</option>
                                            <option value="Pakistan">🇵🇰 Pakistan</option>
                                            <option value="Philippines">🇵🇭 Philippines</option>
                                            <option value="Singapour">🇸🇬 Singapour</option>
                                            <option value="Sri Lanka">🇱🇰 Sri Lanka</option>
                                            <option value="Taïwan">🇹🇼 Taïwan</option>
                                            <option value="Thaïlande">🇹🇭 Thaïlande</option>
                                            <option value="Vietnam">🇻🇳 Vietnam</option>
                                        </optgroup>
                                        <optgroup label="🇪🇺 EUROPE">
                                            <option value="Albanie">🇦🇱 Albanie</option>
                                            <option value="Allemagne">🇩🇪 Allemagne</option>
                                            <option value="Andorre">🇦🇩 Andorre</option>
                                            <option value="Autriche">🇦🇹 Autriche</option>
                                            <option value="Belgique">🇧🇪 Belgique</option>
                                            <option value="Bulgarie">🇧🇬 Bulgarie</option>
                                            <option value="Chypre">🇨🇾 Chypre</option>
                                            <option value="Croatie">🇭🇷 Croatie</option>
                                            <option value="Danemark">🇩🇰 Danemark</option>
                                            <option value="Espagne">🇪🇸 Espagne</option>
                                            <option value="Estonie">🇪🇪 Estonie</option>
                                            <option value="Finlande">🇫🇮 Finlande</option>
                                            <option value="France">🇫🇷 France</option>
                                            <option value="Grèce">🇬🇷 Grèce</option>
                                            <option value="Hongrie">🇭🇺 Hongrie</option>
                                            <option value="Irlande">🇮🇪 Irlande</option>
                                            <option value="Islande">🇮🇸 Islande</option>
                                            <option value="Italie">🇮🇹 Italie</option>
                                            <option value="Lettonie">🇱🇻 Lettonie</option>
                                            <option value="Lituanie">🇱🇹 Lituanie</option>
                                            <option value="Luxembourg">🇱🇺 Luxembourg</option>
                                            <option value="Malte">🇲🇹 Malte</option>
                                            <option value="Monaco">🇲🇨 Monaco</option>
                                            <option value="Norvège">🇳🇴 Norvège</option>
                                            <option value="Pays-Bas">🇳🇱 Pays-Bas</option>
                                            <option value="Pologne">🇵🇱 Pologne</option>
                                            <option value="Portugal">🇵🇹 Portugal</option>
                                            <option value="République Tchèque">🇨🇿 République Tchèque</option>
                                            <option value="Roumanie">🇷🇴 Roumanie</option>
                                            <option value="Royaume-Uni">🇬🇧 Royaume-Uni</option>
                                            <option value="Russie">🇷🇺 Russie</option>
                                            <option value="Serbie">🇷🇸 Serbie</option>
                                            <option value="Slovaquie">🇸🇰 Slovaquie</option>
                                            <option value="Suède">🇸🇪 Suède</option>
                                            <option value="Suisse">🇨🇭 Suisse</option>
                                            <option value="Ukraine">🇺🇦 Ukraine</option>
                                        </optgroup>
                                        <optgroup label="🕌 MOYEN-ORIENT">
                                            <option value="Arabie Saoudite">🇸🇦 Arabie Saoudite</option>
                                            <option value="Bahreïn">🇧🇭 Bahreïn</option>
                                            <option value="Émirats Arabes Unis">🇦🇪 Émirats Arabes Unis</option>
                                            <option value="Iran">🇮🇷 Iran</option>
                                            <option value="Irak">🇮🇶 Irak</option>
                                            <option value="Israël">🇮🇱 Israël</option>
                                            <option value="Jordanie">🇯🇴 Jordanie</option>
                                            <option value="Koweït">🇰🇼 Koweït</option>
                                            <option value="Liban">🇱🇧 Liban</option>
                                            <option value="Oman">🇴🇲 Oman</option>
                                            <option value="Qatar">🇶🇦 Qatar</option>
                                            <option value="Syrie">🇸🇾 Syrie</option>
                                            <option value="Turquie">🇹🇷 Turquie</option>
                                            <option value="Yémen">🇾🇪 Yémen</option>
                                        </optgroup>
                                        <optgroup label="🌊 OCÉANIE">
                                            <option value="Australie">🇦🇺 Australie</option>
                                            <option value="Fidji">🇫🇯 Fidji</option>
                                            <option value="Nouvelle-Zélande">🇳🇿 Nouvelle-Zélande</option>
                                            <option value="Papouasie-Nouvelle-Guinée">🇵🇬 Papouasie-Nouvelle-Guinée</option>
                                            <option value="Samoa">🇼🇸 Samoa</option>
                                            <option value="Vanuatu">🇻🇺 Vanuatu</option>
                                        </optgroup>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Type de Visa *</label>
                                    <select class="form-select" name="type_visa" required>
                                        <option value="Etudiant">Étudiant</option>
                                        <option value="Tourisme">Tourisme</option>
                                        <option value="Affaires">Affaires</option>
                                        <option value="Immigration">Immigration</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Montant Total (FCFA) *</label>
                                    <input type="number" class="form-control" name="montant_total" id="montant-total" required onchange="calculerMontants()">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Mode de Paiement *</label>
                                    <select class="form-select" name="mode_paiement" id="mode-paiement-select" required onchange="toggleEchelonnage()">
                                        <option value="avance-solde">50% à la signature + 50% sous 45 jours</option>
                                        <option value="totalite">Paiement en totalité</option>
                                        <option value="echelonnage">Échelonnage (45 jours après 1er versement)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div id="section-avance-solde">
                                    <div class="mb-3">
                                        <label class="form-label">Avance Versée *</label>
                                        <input type="number" class="form-control" name="avance" id="avance-input" placeholder="Montant versé par le client" onchange="calculerMontants()">
                                        <small class="text-muted">Le montant que le client a déjà versé</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Reste à Payer (50%)</label>
                                        <input type="text" class="form-control" id="reste-payer" readonly>
                                        <div class="echeance-date" id="date-echeance-solde"></div>
                                    </div>
                                </div>

                                <div id="section-echelonnage" style="display: none;">
                                    <div class="mode-paiement">
                                        <h6>Échéancier de paiement :</h6>
                                        <div class="mb-2">
                                            <label>1er versement (50%) :</label>
                                            <input type="text" class="form-control" id="versement1" readonly>
                                            <div class="echeance-date">À la signature du contrat</div>
                                        </div>
                                        <div class="mb-2">
                                            <label>2ème versement (50%) :</label>
                                            <input type="text" class="form-control" id="versement2" readonly>
                                            <div class="echeance-date" id="date-echeance-echelonnage"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Montant en Lettres</label>
                                    <input type="text" class="form-control montant-lettres" id="montant-lettres" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nom du Conseiller *</label>
                            <input type="text" class="form-control" name="conseiller" value="{{ Auth::user()->name }}" required readonly>
                            <small class="text-muted">Conseiller automatiquement défini</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lieu du Contrat *</label>
                            <input type="text" class="form-control" name="lieu_contrat" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date du Contrat *</label>
                            <input type="date" class="form-control" name="date_contrat" id="date-contrat" required onchange="calculerEcheances()">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observations</label>
                            <textarea class="form-control" name="observations" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-primary" onclick="afficherApercuContrat()">
                        <i class="bi bi-eye"></i> Aperçu du Contrat
                    </button>
                    <div>
                        <button type="button" class="btn btn-secondary me-2" onclick="showSection('dashboard')">Annuler</button>
                        <button type="submit" class="btn btn-psi-primary">
                            <i class="bi bi-send-check"></i> Valider et Envoyer le Lien de Signature
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Aperçu du Contrat -->
        <div id="apercu-contrat" class="section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold" style="color: var(--psi-blue);">
                    <i class="bi bi-eye"></i> Aperçu du Contrat
                </h2>
                <div>
                    <button class="btn btn-outline-secondary me-2" onclick="showSection('nouveau-contrat')">
                        <i class="bi bi-arrow-left"></i> Retour
                    </button>
                    <button class="btn btn-psi-primary" onclick="window.print()">
                        <i class="bi bi-printer"></i> Imprimer
                    </button>
                </div>
            </div>

            <div class="card card-psi">
                <div class="card-body pdf-preview" id="apercu-contenu">
                    <!-- Contenu généré dynamiquement -->
                </div>
            </div>
        </div>

        <!-- Lien de Signature Généré -->
        <div id="lien-signature" class="section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold" style="color: var(--psi-blue);">
                    <i class="bi bi-link-45deg"></i> Lien de Signature Généré
                </h2>
                <button class="btn btn-outline-secondary" onclick="showSection('dashboard')">
                    <i class="bi bi-arrow-left"></i> Tableau de Bord
                </button>
            </div>

            <div class="card card-psi">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h3 class="text-success mb-3">✅ Contrat créé avec succès !</h3>
                    <p class="lead mb-4">Le lien de signature a été généré. Vous pouvez maintenant l'envoyer au candidat.</p>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Lien de signature unique et temporaire :</label>
                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control" id="lien-signature-input" readonly>
                            <button class="btn btn-psi-primary" type="button" onclick="copierLien()">
                                <i class="bi bi-clipboard"></i> Copier
                            </button>
                        </div>
                        <div class="form-text">
                            <i class="bi bi-clock"></i> Ce lien expirera dans 1 heure ou après signature
                        </div>
                    </div>
                    
                    <div class="alert alert-info text-start">
                        <h6><i class="bi bi-chat-dots"></i> Message à envoyer au candidat :</h6>
                        <p id="message-sms" class="mb-0"></p>
                    </div>
                    
                    <div class="mt-4">
                        <button class="btn btn-outline-primary btn-lg" onclick="showSection('dashboard')">
                            <i class="bi bi-house"></i> Retour au Tableau de Bord
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page de Signature Candidat -->
        <div id="page-signature" class="section">
            <div class="card card-psi">
                <div class="card-header bg-white text-center py-4">
                    <h2 class="mb-2" style="color: var(--psi-blue);">
                        <i class="bi bi-pen"></i> Signature du Contrat
                    </h2>
                    <p class="text-muted mb-0">Veuillez lire attentivement puis signer votre contrat</p>
                </div>
                <div class="card-body">
                    <div class="contrat-container p-4 mb-4">
                        <div id="contrat-a-signer">
                            <!-- Contenu généré dynamiquement -->
                        </div>
                    </div>

                    <div class="border-top pt-4">
                        <h5 class="mb-4" style="color: var(--psi-blue);">
                            <i class="bi bi-check-square"></i> Validation et Signature
                        </h5>
                        
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="acceptation">
                                <label class="form-check-label fw-bold">
                                    Je reconnais avoir lu et accepté l'intégralité des termes de ce contrat
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Nom complet du signataire *</label>
                            <input type="text" class="form-control" id="nom-signataire" placeholder="Votre nom complet">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Signature électronique *</label>
                            <div class="text-center">
                                <canvas id="signature-pad" class="signature-pad" width="600" height="200"></canvas>
                            </div>
                            <div class="text-center mt-2">
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="effacerSignature()">
                                    <i class="bi bi-eraser"></i> Effacer la signature
                                </button>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <strong><i class="bi bi-exclamation-triangle"></i> Important :</strong> 
                            Cette signature a valeur légale. Le contrat sera verrouillé après signature et ne pourra plus être modifié.
                        </div>

                        <div class="text-center">
                            <button class="btn btn-psi-primary btn-lg px-5" onclick="signerContrat()" id="btn-signer">
                                <i class="bi bi-check-lg"></i> Signer et Valider le Contrat
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmation Signature -->
        <div id="confirmation-signature" class="section">
            <div class="card card-psi text-center py-5">
                <div class="card-body">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="text-success mb-3">🎉 Félicitations !</h2>
                    <h4 id="confirmation-nom" class="mb-4">Votre contrat a été signé avec succès.</h4>
                    
                    <div class="alert alert-success d-inline-block text-start">
                        <h6><i class="bi bi-file-earmark-pdf"></i> Votre contrat signé est prêt :</h6>
                        <p class="mb-2" id="lien-pdf-text"></p>
                        <button class="btn btn-psi-secondary mt-2" onclick="telechargerPDF()">
                            <i class="bi bi-download"></i> Télécharger le PDF Signé
                        </button>
                    </div>
                    
                    <div class="mt-5">
                        <p class="text-muted">
                            <i class="bi bi-shield-check"></i> 
                            Document horodaté et verrouillé - PSI AFRICA © 2024
                        </p>
                        <button class="btn btn-outline-primary" onclick="showSection('dashboard')">
                            <i class="bi bi-house"></i> Retour à l'accueil
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des Contrats -->
        <div id="liste-contrats" class="section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold" style="color: var(--psi-blue);">
                    <i class="bi bi-list-ul"></i> Gestion des Contrats
                </h2>
                <div>
                    <button class="btn btn-outline-secondary me-2" onclick="showSection('dashboard')">
                        <i class="bi bi-arrow-left"></i> Retour
                    </button>
                    <button class="btn btn-psi-primary" onclick="showSection('nouveau-contrat')">
                        <i class="bi bi-plus-circle"></i> Nouveau Contrat
                    </button>
                </div>
            </div>

            <!-- Filtres et Recherche -->
            <div class="card card-psi mb-3">
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-search"></i> Rechercher</label>
                            <input type="text" class="form-control" id="search-contrats" placeholder="Numéro, nom, prénom, destination..." onkeyup="filterContrats()">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><i class="bi bi-filter"></i> Statut</label>
                            <select class="form-select" id="filter-statut" onchange="filterContrats()">
                                <option value="tous">Tous</option>
                                <option value="Signé">Signés</option>
                                <option value="En attente">En attente</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                                <i class="bi bi-arrow-clockwise"></i> Réinitialiser
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-psi">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); color: white;">
                                <tr>
                                    <th style="border: none;"><i class="bi bi-hash"></i> N° Contrat</th>
                                    <th style="border: none;"><i class="bi bi-person"></i> Candidat</th>
                                    <th style="border: none;"><i class="bi bi-geo-alt"></i> Destination</th>
                                    <th style="border: none;"><i class="bi bi-currency-exchange"></i> Montant</th>
                                    <th style="border: none;"><i class="bi bi-check-circle"></i> Statut</th>
                                    <th style="border: none;"><i class="bi bi-calendar"></i> Date Création</th>
                                    <th class="text-center" style="width: 350px; border: none;"><i class="bi bi-tools"></i> Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table-contrats">
                                <!-- Rempli dynamiquement -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination si nécessaire -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <small class="text-muted">Total: <strong id="total-contrats-count">0</strong> contrat(s)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulaire Nouvelle Fiche d'Inscription -->
        <div id="nouvelle-fiche" class="section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold" style="color: var(--psi-blue);">
                    <i class="bi bi-person-plus"></i> Nouvelle Fiche d'Inscription
                </h2>
                <button class="btn btn-outline-secondary" onclick="showSection('dashboard')">
                    <i class="bi bi-arrow-left"></i> Retour
                </button>
            </div>

            <form id="form-fiche" onsubmit="creerFicheInscription(event)">
                <div class="card card-psi">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Informations Personnelles</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Type de visa souhaité *</label>
                                    <select class="form-select" name="type_visa" required>
                                        <option value="Touriste">Touriste</option>
                                        <option value="Étudiant">Étudiant</option>
                                        <option value="Affaires">Affaires</option>
                                        <option value="Immigration">Immigration</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Pays de destination *</label>
                                    <select class="form-select" name="pays_destination" required>
                                        <option value="">-- Sélectionnez un pays --</option>
                                        <optgroup label="🌍 AFRIQUE">
                                            <option value="Afrique du Sud">🇿🇦 Afrique du Sud</option>
                                            <option value="Algérie">🇩🇿 Algérie</option>
                                            <option value="Angola">🇦🇴 Angola</option>
                                            <option value="Bénin">🇧🇯 Bénin</option>
                                            <option value="Botswana">🇧🇼 Botswana</option>
                                            <option value="Burkina Faso">🇧🇫 Burkina Faso</option>
                                            <option value="Burundi">🇧🇮 Burundi</option>
                                            <option value="Cameroun">🇨🇲 Cameroun</option>
                                            <option value="Cap-Vert">🇨🇻 Cap-Vert</option>
                                            <option value="Comores">🇰🇲 Comores</option>
                                            <option value="Congo">🇨🇬 Congo</option>
                                            <option value="Côte d'Ivoire">🇨🇮 Côte d'Ivoire</option>
                                            <option value="Djibouti">🇩🇯 Djibouti</option>
                                            <option value="Égypte">🇪🇬 Égypte</option>
                                            <option value="Éthiopie">🇪🇹 Éthiopie</option>
                                            <option value="Gabon">🇬🇦 Gabon</option>
                                            <option value="Gambie">🇬🇲 Gambie</option>
                                            <option value="Ghana">🇬🇭 Ghana</option>
                                            <option value="Guinée">🇬🇳 Guinée</option>
                                            <option value="Kenya">🇰🇪 Kenya</option>
                                            <option value="Libéria">🇱🇷 Libéria</option>
                                            <option value="Libye">🇱🇾 Libye</option>
                                            <option value="Madagascar">🇲🇬 Madagascar</option>
                                            <option value="Malawi">🇲🇼 Malawi</option>
                                            <option value="Mali">🇲🇱 Mali</option>
                                            <option value="Maroc">🇲🇦 Maroc</option>
                                            <option value="Maurice">🇲🇺 Maurice</option>
                                            <option value="Mauritanie">🇲🇷 Mauritanie</option>
                                            <option value="Mozambique">🇲🇿 Mozambique</option>
                                            <option value="Namibie">🇳🇦 Namibie</option>
                                            <option value="Niger">🇳🇪 Niger</option>
                                            <option value="Nigeria">🇳🇬 Nigeria</option>
                                            <option value="Ouganda">🇺🇬 Ouganda</option>
                                            <option value="RD Congo">🇨🇩 RD Congo</option>
                                            <option value="Rwanda">🇷🇼 Rwanda</option>
                                            <option value="Sénégal">🇸🇳 Sénégal</option>
                                            <option value="Seychelles">🇸🇨 Seychelles</option>
                                            <option value="Somalie">🇸🇴 Somalie</option>
                                            <option value="Soudan">🇸🇩 Soudan</option>
                                            <option value="Tanzanie">🇹🇿 Tanzanie</option>
                                            <option value="Tchad">🇹🇩 Tchad</option>
                                            <option value="Togo">🇹🇬 Togo</option>
                                            <option value="Tunisie">🇹🇳 Tunisie</option>
                                            <option value="Zambie">🇿🇲 Zambie</option>
                                            <option value="Zimbabwe">🇿🇼 Zimbabwe</option>
                                        </optgroup>
                                        <optgroup label="🌎 AMÉRIQUE DU NORD">
                                            <option value="Canada">🇨🇦 Canada</option>
                                            <option value="États-Unis">🇺🇸 États-Unis</option>
                                            <option value="Mexique">🇲🇽 Mexique</option>
                                        </optgroup>
                                        <optgroup label="🌴 AMÉRIQUE CENTRALE & CARAÏBES">
                                            <option value="Bahamas">🇧🇸 Bahamas</option>
                                            <option value="Barbade">🇧🇧 Barbade</option>
                                            <option value="Belize">🇧🇿 Belize</option>
                                            <option value="Costa Rica">🇨🇷 Costa Rica</option>
                                            <option value="Cuba">🇨🇺 Cuba</option>
                                            <option value="République Dominicaine">🇩🇴 République Dominicaine</option>
                                            <option value="El Salvador">🇸🇻 El Salvador</option>
                                            <option value="Guatemala">🇬🇹 Guatemala</option>
                                            <option value="Haïti">🇭🇹 Haïti</option>
                                            <option value="Honduras">🇭🇳 Honduras</option>
                                            <option value="Jamaïque">🇯🇲 Jamaïque</option>
                                            <option value="Nicaragua">🇳🇮 Nicaragua</option>
                                            <option value="Panama">🇵🇦 Panama</option>
                                            <option value="Trinité-et-Tobago">🇹🇹 Trinité-et-Tobago</option>
                                        </optgroup>
                                        <optgroup label="🌎 AMÉRIQUE DU SUD">
                                            <option value="Argentine">🇦🇷 Argentine</option>
                                            <option value="Bolivie">🇧🇴 Bolivie</option>
                                            <option value="Brésil">🇧🇷 Brésil</option>
                                            <option value="Chili">🇨🇱 Chili</option>
                                            <option value="Colombie">🇨🇴 Colombie</option>
                                            <option value="Équateur">🇪🇨 Équateur</option>
                                            <option value="Guyana">🇬🇾 Guyana</option>
                                            <option value="Paraguay">🇵🇾 Paraguay</option>
                                            <option value="Pérou">🇵🇪 Pérou</option>
                                            <option value="Suriname">🇸🇷 Suriname</option>
                                            <option value="Uruguay">🇺🇾 Uruguay</option>
                                            <option value="Venezuela">🇻🇪 Venezuela</option>
                                        </optgroup>
                                        <optgroup label="🌏 ASIE">
                                            <option value="Afghanistan">🇦🇫 Afghanistan</option>
                                            <option value="Bangladesh">🇧🇩 Bangladesh</option>
                                            <option value="Bhoutan">🇧🇹 Bhoutan</option>
                                            <option value="Birmanie">🇲🇲 Birmanie</option>
                                            <option value="Brunei">🇧🇳 Brunei</option>
                                            <option value="Cambodge">🇰🇭 Cambodge</option>
                                            <option value="Chine">🇨🇳 Chine</option>
                                            <option value="Corée du Nord">🇰🇵 Corée du Nord</option>
                                            <option value="Corée du Sud">🇰🇷 Corée du Sud</option>
                                            <option value="Inde">🇮🇳 Inde</option>
                                            <option value="Indonésie">🇮🇩 Indonésie</option>
                                            <option value="Japon">🇯🇵 Japon</option>
                                            <option value="Kazakhstan">🇰🇿 Kazakhstan</option>
                                            <option value="Laos">🇱🇦 Laos</option>
                                            <option value="Malaisie">🇲🇾 Malaisie</option>
                                            <option value="Maldives">🇲🇻 Maldives</option>
                                            <option value="Mongolie">🇲🇳 Mongolie</option>
                                            <option value="Népal">🇳🇵 Népal</option>
                                            <option value="Pakistan">🇵🇰 Pakistan</option>
                                            <option value="Philippines">🇵🇭 Philippines</option>
                                            <option value="Singapour">🇸🇬 Singapour</option>
                                            <option value="Sri Lanka">🇱🇰 Sri Lanka</option>
                                            <option value="Taïwan">🇹🇼 Taïwan</option>
                                            <option value="Thaïlande">🇹🇭 Thaïlande</option>
                                            <option value="Vietnam">🇻🇳 Vietnam</option>
                                        </optgroup>
                                        <optgroup label="🇪🇺 EUROPE">
                                            <option value="Albanie">🇦🇱 Albanie</option>
                                            <option value="Allemagne">🇩🇪 Allemagne</option>
                                            <option value="Andorre">🇦🇩 Andorre</option>
                                            <option value="Autriche">🇦🇹 Autriche</option>
                                            <option value="Belgique">🇧🇪 Belgique</option>
                                            <option value="Bulgarie">🇧🇬 Bulgarie</option>
                                            <option value="Chypre">🇨🇾 Chypre</option>
                                            <option value="Croatie">🇭🇷 Croatie</option>
                                            <option value="Danemark">🇩🇰 Danemark</option>
                                            <option value="Espagne">🇪🇸 Espagne</option>
                                            <option value="Estonie">🇪🇪 Estonie</option>
                                            <option value="Finlande">🇫🇮 Finlande</option>
                                            <option value="France">🇫🇷 France</option>
                                            <option value="Grèce">🇬🇷 Grèce</option>
                                            <option value="Hongrie">🇭🇺 Hongrie</option>
                                            <option value="Irlande">🇮🇪 Irlande</option>
                                            <option value="Islande">🇮🇸 Islande</option>
                                            <option value="Italie">🇮🇹 Italie</option>
                                            <option value="Lettonie">🇱🇻 Lettonie</option>
                                            <option value="Lituanie">🇱🇹 Lituanie</option>
                                            <option value="Luxembourg">🇱🇺 Luxembourg</option>
                                            <option value="Malte">🇲🇹 Malte</option>
                                            <option value="Monaco">🇲🇨 Monaco</option>
                                            <option value="Norvège">🇳🇴 Norvège</option>
                                            <option value="Pays-Bas">🇳🇱 Pays-Bas</option>
                                            <option value="Pologne">🇵🇱 Pologne</option>
                                            <option value="Portugal">🇵🇹 Portugal</option>
                                            <option value="République Tchèque">🇨🇿 République Tchèque</option>
                                            <option value="Roumanie">🇷🇴 Roumanie</option>
                                            <option value="Royaume-Uni">🇬🇧 Royaume-Uni</option>
                                            <option value="Russie">🇷🇺 Russie</option>
                                            <option value="Serbie">🇷🇸 Serbie</option>
                                            <option value="Slovaquie">🇸🇰 Slovaquie</option>
                                            <option value="Suède">🇸🇪 Suède</option>
                                            <option value="Suisse">🇨🇭 Suisse</option>
                                            <option value="Ukraine">🇺🇦 Ukraine</option>
                                        </optgroup>
                                        <optgroup label="🕌 MOYEN-ORIENT">
                                            <option value="Arabie Saoudite">🇸🇦 Arabie Saoudite</option>
                                            <option value="Bahreïn">🇧🇭 Bahreïn</option>
                                            <option value="Émirats Arabes Unis">🇦🇪 Émirats Arabes Unis</option>
                                            <option value="Iran">🇮🇷 Iran</option>
                                            <option value="Irak">🇮🇶 Irak</option>
                                            <option value="Israël">🇮🇱 Israël</option>
                                            <option value="Jordanie">🇯🇴 Jordanie</option>
                                            <option value="Koweït">🇰🇼 Koweït</option>
                                            <option value="Liban">🇱🇧 Liban</option>
                                            <option value="Oman">🇴🇲 Oman</option>
                                            <option value="Qatar">🇶🇦 Qatar</option>
                                            <option value="Syrie">🇸🇾 Syrie</option>
                                            <option value="Turquie">🇹🇷 Turquie</option>
                                            <option value="Yémen">🇾🇪 Yémen</option>
                                        </optgroup>
                                        <optgroup label="🌊 OCÉANIE">
                                            <option value="Australie">🇦🇺 Australie</option>
                                            <option value="Fidji">🇫🇯 Fidji</option>
                                            <option value="Nouvelle-Zélande">🇳🇿 Nouvelle-Zélande</option>
                                            <option value="Papouasie-Nouvelle-Guinée">🇵🇬 Papouasie-Nouvelle-Guinée</option>
                                            <option value="Samoa">🇼🇸 Samoa</option>
                                            <option value="Vanuatu">🇻🇺 Vanuatu</option>
                                        </optgroup>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Nom *</label>
                                    <input type="text" class="form-control" name="nom" value="KOUAME" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Prénoms *</label>
                                    <input type="text" class="form-control" name="prenom" value="Jean" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Date de Naissance *</label>
                                    <input type="date" class="form-control" name="date_naissance" value="1990-05-15" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nationalité *</label>
                                    <input type="text" class="form-control" name="nationalite" value="Ivoirienne" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Sexe *</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="sexe" id="sexe_m" value="Masculin" checked>
                                            <label class="form-check-label" for="sexe_m">Masculin</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="sexe" id="sexe_f" value="Féminin">
                                            <label class="form-check-label" for="sexe_f">Féminin</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">État civil *</label>
                                    <select class="form-select" name="etat_civil" required>
                                        <option value="Célibataire">Célibataire</option>
                                        <option value="Marié(e)">Marié(e)</option>
                                        <option value="Divorcé(e)">Divorcé(e)</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Profession actuelle *</label>
                                    <input type="text" class="form-control" name="profession" value="Commercial" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Employeur / Entreprise</label>
                                    <input type="text" class="form-control" name="employeur" value="SARL Excellence">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-psi mt-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Coordonnées</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Adresse complète *</label>
                                    <textarea class="form-control" name="adresse" rows="2" required>Plateau, Abidjan, Côte d'Ivoire</textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Ville *</label>
                                    <input type="text" class="form-control" name="ville" value="Abidjan" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Téléphone mobile *</label>
                                    <input type="tel" class="form-control" name="telephone_mobile" value="+2250700000001" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" value="jean.kouame@test.com" placeholder="email@exemple.com (optionnel)">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-primary" onclick="afficherApercuFiche()">
                        <i class="bi bi-eye"></i> Aperçu de la Fiche
                    </button>
                    <div>
                        <button type="button" class="btn btn-secondary me-2" onclick="showSection('dashboard')">Annuler</button>
                        <button type="submit" class="btn btn-psi-primary">
                            <i class="bi bi-save"></i> Enregistrer la Fiche
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Aperçu Fiche d'Inscription -->
        <div id="apercu-fiche" class="section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold" style="color: var(--psi-blue);">
                    <i class="bi bi-eye"></i> Aperçu de la Fiche d'Inscription
                </h2>
                <div>
                    <button class="btn btn-outline-secondary me-2" onclick="showSection('nouvelle-fiche')">
                        <i class="bi bi-arrow-left"></i> Retour
                    </button>
                    <button class="btn btn-psi-primary" onclick="window.print()">
                        <i class="bi bi-printer"></i> Imprimer
                    </button>
                </div>
            </div>

            <div class="card card-psi">
                <div class="card-body pdf-preview" id="apercu-fiche-contenu">
                    <!-- Contenu généré dynamiquement -->
                </div>
            </div>
        </div>

        <!-- Liste des Fiches d'Inscription -->
        <div id="liste-fiches" class="section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold" style="color: var(--psi-blue);">
                    <i class="bi bi-people"></i> Gestion des Fiches d'Inscription
                </h2>
                <div>
                    <button class="btn btn-outline-secondary me-2" onclick="showSection('dashboard')">
                        <i class="bi bi-arrow-left"></i> Retour
                    </button>
                    <button class="btn btn-psi-secondary" onclick="showSection('nouvelle-fiche')">
                        <i class="bi bi-person-plus"></i> Nouvelle Fiche
                    </button>
                </div>
            </div>

            <div class="card card-psi">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>N° Fiche</th>
                                    <th>Candidat</th>
                                    <th>Destination</th>
                                    <th>Type Visa</th>
                                    <th>Date Création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table-fiches">
                                <!-- Rempli dynamiquement -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        // ==========================================
        // VARIABLES GLOBALES ET INITIALISATION
        // ==========================================
        let contrats = [];
        let fiches = [];
        let currentContrat = null;
        let currentFiche = null;
        let signaturePad = null;

        // Configuration CSRF Token pour Laravel
        const csrfToken = '{{ csrf_token() }}';

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            console.log("🚀 PSI AFRICA - Système initialisé");
            loadContratsFromDB();
            calculerMontants();
            calculerEcheances();
        });

        // ==========================================
        // FONCTIONS DE CHARGEMENT DEPUIS LA BDD
        // ==========================================
        async function loadContratsFromDB() {
            try {
                const response = await fetch('/crm/contrats', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Erreur lors du chargement des contrats');
                }

                const data = await response.json();

                if (data.success) {
                    // Adapter les données du backend au format attendu par le frontend
                    contrats = data.contracts.map(contract => ({
                        id: contract.id,
                        numero: contract.numero_contrat,
                        nom: contract.nom,
                        prenom: contract.prenom,
                        date_naissance: contract.date_naissance,
                        lieu_naissance: contract.lieu_naissance,
                        nationalite: contract.nationalite,
                        sexe: contract.sexe,
                        etat_civil: contract.etat_civil,
                        profession: contract.profession,
                        employeur: contract.employeur,
                        telephone: contract.telephone_mobile,
                        telephone_mobile: contract.telephone_mobile,
                        telephone_fixe: contract.telephone_fixe,
                        email: contract.email,
                        adresse: contract.adresse,
                        ville: contract.ville,
                        numero_cni: contract.numero_cni,
                        pays_destination: contract.pays_destination,
                        destination_visa: contract.pays_destination,
                        type_visa: contract.type_visa,
                        montant_total: parseFloat(contract.montant_contrat),
                        montant_contrat: parseFloat(contract.montant_contrat),
                        avance: parseFloat(contract.avance || 0),
                        reste_payer: parseFloat(contract.reste_payer || 0),
                        montant_lettres: contract.montant_lettres,
                        mode_paiement: contract.mode_paiement,
                        date_echeance: contract.date_echeance,
                        conseiller: contract.conseiller,
                        lieu_contrat: contract.lieu_contrat,
                        date_contrat: contract.date_contrat,
                        statut: contract.statut, // Garder le statut original: "Signé" ou "En attente"
                        signature: contract.signature,
                        signature_data: contract.signature,
                        nom_signataire: contract.nom_signataire,
                        date_signature: contract.date_signature,
                        signature_token: contract.signature_token,
                        signature_url: contract.signature_token ? `${window.location.origin}/signature/${contract.signature_token}` : null,
                        token_expires_at: contract.token_expires_at,
                        token_used_at: contract.token_used_at,
                        date_creation: contract.created_at,
                        observations: contract.observations
                    }));

                    console.log(`✅ ${contrats.length} contrats chargés depuis la base de données`);
                    updateStats();
                    updateAuditLogs();
                    updateListeContrats();
                } else {
                    console.error('Erreur:', data.message);
                }
            } catch (error) {
                console.error('Erreur lors du chargement des contrats:', error);
                alert('Erreur lors du chargement des contrats. Veuillez rafraîchir la page.');
            }
        }

        // ==========================================
        // FONCTIONS DE NAVIGATION
        // ==========================================
        function showSection(sectionId) {
            console.log("Navigation vers:", sectionId);
            document.querySelectorAll('.section').forEach(section => {
                section.classList.remove('active-section');
            });
            document.getElementById(sectionId).classList.add('active-section');

            // Recharger les données quand on va sur certaines sections
            if (sectionId === 'liste-contrats' || sectionId === 'dashboard') {
                loadContratsFromDB(); // Recharger pour voir les dernières modifications
            }

            if (sectionId === 'page-signature' && !signaturePad) {
                setTimeout(() => {
                    const canvas = document.getElementById('signature-pad');
                    if (canvas) {
                        signaturePad = new SignaturePad(canvas);
                        console.log("SignaturePad initialisé");
                    }
                }, 100);
            }
        }

        // ==========================================
        // GESTION DES CONTRATS
        // ==========================================
        async function creerContrat(event) {
            event.preventDefault();
            console.log("Création contrat...");

            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData);
            const modePaiement = document.getElementById('mode-paiement-select').value;

            // Calculs financiers
            const montantTotal = parseInt(data.montant_total);
            let avance, reste_payer, echeances = [];

            if (modePaiement === 'totalite') {
                avance = montantTotal;
                reste_payer = 0;
                echeances = [{ montant: avance, date: data.date_contrat || new Date().toISOString().split('T')[0], type: 'Paiement total' }];
            } else if (modePaiement === 'avance-solde') {
                // Utiliser la valeur saisie dans le champ avance
                avance = parseInt(data.avance) || Math.round(montantTotal * 0.5);
                reste_payer = montantTotal - avance;
                const dateEcheance = new Date(data.date_contrat || new Date());
                dateEcheance.setDate(dateEcheance.getDate() + 45);

                echeances = [
                    { montant: avance, date: data.date_contrat || new Date().toISOString().split('T')[0], type: '1er versement', statut: 'payé' },
                    { montant: reste_payer, date: dateEcheance.toISOString().split('T')[0], type: '2ème versement - Solde' }
                ];
            } else {
                // Autre mode de paiement
                avance = parseInt(data.avance) || 0;
                reste_payer = montantTotal - avance;
                echeances = [];
            }

            // Préparer les données pour la BDD (format attendu par le backend)
            const contractData = {
                nom: data.nom,
                prenom: data.prenom,
                date_naissance: data.date_naissance,
                lieu_naissance: data.lieu_naissance || '',
                nationalite: 'Ivoirienne', // Valeur par défaut
                sexe: 'Masculin', // Valeur par défaut - À améliorer avec un champ dans le formulaire
                etat_civil: 'Célibataire', // Valeur par défaut - À améliorer avec un champ dans le formulaire
                profession: data.profession,
                employeur: data.employeur || '',
                adresse: data.adresse,
                ville: 'Abidjan', // Valeur par défaut - À améliorer avec un champ dans le formulaire
                telephone_mobile: data.telephone,
                telephone_fixe: '',
                email: data.email,
                type_visa: data.type_visa,
                pays_destination: data.destination_visa,
                montant_contrat: montantTotal,
                avance: avance,
                reste_payer: reste_payer,
                montant_lettres: nombreEnLettres(montantTotal),
                date_echeance: echeances.length > 1 ? echeances[1].date : null,
                mode_paiement: modePaiement,
                conseiller: data.conseiller,
                lieu_contrat: data.lieu_contrat,
                date_contrat: data.date_contrat || new Date().toISOString().split('T')[0],
                statut: 'En attente'
            };

            try {
                console.log('📤 Envoi des données du contrat:', contractData);

                // Enregistrer dans la BDD
                const response = await fetch('/crm/contrats', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(contractData)
                });

                console.log('📥 Réponse du serveur - Status:', response.status);

                const result = await response.json();
                console.log('📥 Réponse du serveur - Données:', result);

                if (!response.ok) {
                    // Afficher les erreurs de validation si disponibles
                    if (result.errors) {
                        const errorsText = Object.entries(result.errors)
                            .map(([field, messages]) => `${field}: ${messages.join(', ')}`)
                            .join('\n');
                        throw new Error('Erreur de validation:\n' + errorsText);
                    }
                    throw new Error(result.message || 'Erreur lors de la création du contrat');
                }

                if (result.success) {
                    console.log("✅ Contrat créé avec succès:", result.contract);
                    console.log("🔗 URL de signature:", result.signature_url);
                    console.log("⏰ Token expire le:", result.token_expires_at);

                    // Créer l'objet contrat pour l'interface
                    const nouveauContrat = {
                        id: result.contract.id,
                        numero: result.contract.numero_contrat,
                        ...data,
                        montant_total: montantTotal,
                        montant_contrat: montantTotal,
                        avance: avance,
                        reste_payer: reste_payer,
                        mode_paiement: modePaiement,
                        echeances: echeances,
                        statut: result.contract.statut || 'En attente', // Utiliser le statut du backend
                        signature_token: result.contract.signature_token,
                        token_signature: result.contract.signature_token,
                        signature_url: result.signature_url,
                        token_expires_at: result.token_expires_at,
                        date_creation: result.contract.created_at,
                        date_expiration: result.token_expires_at,
                        signature: null,
                        signature_data: null,
                        date_signature: null,
                        nom_signataire: null
                    };

                    contrats.push(nouveauContrat);
                    currentContrat = nouveauContrat;

                    addAuditLog('creation_contrat', `Contrat ${nouveauContrat.numero} créé`);
                    showLienSignature(nouveauContrat, result.signature_url, result.token_expires_at);
                    updateStats();
                    updateListeContrats();

                    // Ne pas afficher d'alert ici, l'utilisateur verra directement le lien
                } else {
                    console.error('❌ Erreur:', result.message);
                    alert('Erreur lors de la création du contrat: ' + (result.message || 'Erreur inconnue'));
                }
            } catch (error) {
                console.error('❌ Erreur lors de la création du contrat:', error);
                alert('Erreur lors de la création du contrat:\n' + error.message);
            }
        }

        // Fonction pour convertir un nombre en lettres (simplifiée)
        function nombreEnLettres(nombre) {
            // Version simplifiée - à améliorer si nécessaire
            return nombre.toLocaleString('fr-FR') + ' francs CFA';
        }

        function showLienSignature(contrat, signatureUrl, expiresAt) {
            // Utiliser l'URL de signature fournie par le serveur
            const lienSignature = signatureUrl || contrat.signature_url;
            const expiration = expiresAt || contrat.date_expiration;

            document.getElementById('lien-signature-input').value = lienSignature;
            document.getElementById('message-sms').textContent =
                `Bonjour ${contrat.prenom}, cliquez pour signer votre contrat PSI AFRICA : ${lienSignature}\n\n⏰ Lien valable jusqu'au ${expiration}\n\nCordialement,\nL'équipe PSI AFRICA`;

            showSection('lien-signature');
        }

        function afficherApercuContrat() {
            console.log("Affichage aperçu contrat");
            const formData = new FormData(document.getElementById('form-contrat'));
            const data = Object.fromEntries(formData);
            const apercuHTML = genererContratHTML(data);
            document.getElementById('apercu-contenu').innerHTML = apercuHTML;
            showSection('apercu-contrat');
        }

        function testSignature() {
            if (currentContrat) {
                document.getElementById('contrat-a-signer').innerHTML = genererContratHTML(currentContrat);
                document.getElementById('nom-signataire').value = currentContrat.prenom + ' ' + currentContrat.nom;
                showSection('page-signature');
            } else {
                alert("Veuillez d'abord créer un contrat");
                showSection('nouveau-contrat');
            }
        }

        async function signerContrat() {
            if (!document.getElementById('acceptation').checked) {
                alert("Veuillez accepter les termes du contrat");
                return;
            }

            const nomSignataire = document.getElementById('nom-signataire').value;
            if (!nomSignataire.trim()) {
                alert("Veuillez saisir votre nom complet");
                return;
            }

            if (!signaturePad || signaturePad.isEmpty()) {
                alert("Veuillez apposer votre signature");
                return;
            }

            try {
                // Préparer les données de signature
                const signatureData = {
                    signature: signaturePad.toDataURL(),
                    nom_signataire: nomSignataire
                };

                // Envoyer à la BDD
                const response = await fetch(`/crm/contrats/${currentContrat.id}/sign`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(signatureData)
                });

                if (!response.ok) {
                    throw new Error('Erreur lors de la signature du contrat');
                }

                const result = await response.json();

                if (result.success) {
                    console.log("✅ Contrat signé avec succès:", result.contract);

                    // Mise à jour du contrat local
                    const contratIndex = contrats.findIndex(c => c.id === currentContrat.id);
                    if (contratIndex !== -1) {
                        contrats[contratIndex].statut = 'signe';
                        contrats[contratIndex].date_signature = new Date().toISOString();
                        contrats[contratIndex].signature_data = signaturePad.toDataURL();
                        contrats[contratIndex].nom_signataire = nomSignataire;
                    }

                    addAuditLog('signature_contrat', `Contrat ${currentContrat.numero} signé`);

                    document.getElementById('confirmation-nom').textContent =
                        `Félicitations ${currentContrat.prenom}, votre contrat a été signé avec succès.`;
                    document.getElementById('lien-pdf-text').textContent =
                        `CONTRAT_${currentContrat.numero}_SIGNE.pdf`;

                    showSection('confirmation-signature');
                    updateStats();
                    updateListeContrats();

                    alert('✅ Contrat signé avec succès et enregistré dans la base de données!');
                } else {
                    console.error('Erreur:', result.message);
                    alert('Erreur lors de la signature du contrat: ' + (result.message || 'Erreur inconnue'));
                }
            } catch (error) {
                console.error('Erreur lors de la signature du contrat:', error);
                alert('Erreur lors de la signature du contrat. Veuillez réessayer.');
            }
        }

        function telechargerPDF() {
            alert("📄 PDF signé téléchargé (simulation)");
        }

        // ==========================================
        // GESTION DES FICHES D'INSCRIPTION
        // ==========================================
        function creerFicheInscription(event) {
            event.preventDefault();
            console.log("Création fiche...");
            
            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData);
            
            const nouvelleFiche = {
                id: Date.now(),
                numero: 'FICHE-' + new Date().getFullYear() + '-' + (fiches.length + 1).toString().padStart(4, '0'),
                ...data,
                date_creation: new Date().toISOString(),
                statut: 'complet'
            };
            
            fiches.push(nouvelleFiche);
            currentFiche = nouvelleFiche;

            addAuditLog('creation_fiche', `Fiche ${nouvelleFiche.numero} créée`);
            alert("✅ Fiche d'inscription créée avec succès !");
            showSection('dashboard');
            updateStats();
            updateListeFiches();
        }

        function afficherApercuFiche() {
            console.log("Affichage aperçu fiche");
            const formData = new FormData(document.getElementById('form-fiche'));
            const data = Object.fromEntries(formData);
            const apercuHTML = genererFicheHTML(data);
            document.getElementById('apercu-fiche-contenu').innerHTML = apercuHTML;
            showSection('apercu-fiche');
        }

        // ==========================================
        // GESTION FINANCIÈRE
        // ==========================================
        function toggleEchelonnage() {
            const mode = document.getElementById('mode-paiement-select').value;
            const sectionAvance = document.getElementById('section-avance-solde');
            const sectionEchelonnage = document.getElementById('section-echelonnage');
            
            if (mode === 'echelonnage') {
                sectionAvance.style.display = 'none';
                sectionEchelonnage.style.display = 'block';
            } else {
                sectionAvance.style.display = 'block';
                sectionEchelonnage.style.display = 'none';
            }
            calculerMontants();
        }

        function calculerMontants() {
            const montantTotal = parseInt(document.getElementById('montant-total').value) || 0;
            const mode = document.getElementById('mode-paiement-select').value;

            if (mode === 'totalite') {
                document.getElementById('avance-input').value = montantTotal;
                document.getElementById('reste-payer').value = '0 FCFA';
                document.getElementById('versement1').value = montantTotal.toLocaleString() + ' FCFA';
                document.getElementById('versement2').value = '0 FCFA';
            } else if (mode === 'avance-solde') {
                // Calculer le reste à payer en fonction de l'avance saisie
                const avanceInput = document.getElementById('avance-input');
                let avance = parseInt(avanceInput.value) || 0;

                // Si l'avance n'est pas définie, proposer 50% par défaut
                if (!avanceInput.value || avance === 0) {
                    avance = Math.round(montantTotal * 0.5);
                    avanceInput.value = avance;
                }

                const reste = montantTotal - avance;
                document.getElementById('reste-payer').value = reste.toLocaleString() + ' FCFA';
                document.getElementById('versement1').value = avance.toLocaleString() + ' FCFA';
                document.getElementById('versement2').value = reste.toLocaleString() + ' FCFA';
            } else {
                const moitie = Math.round(montantTotal * 0.5);
                document.getElementById('avance-input').value = moitie;
                document.getElementById('reste-payer').value = moitie.toLocaleString() + ' FCFA';
                document.getElementById('versement1').value = moitie.toLocaleString() + ' FCFA';
                document.getElementById('versement2').value = moitie.toLocaleString() + ' FCFA';
            }

            document.getElementById('montant-lettres').value = convertirMontantLettres(montantTotal);
            calculerEcheances();
        }

        function calculerEcheances() {
            const dateContrat = document.getElementById('date-contrat').value;
            if (!dateContrat) return;

            const date = new Date(dateContrat);
            const dateEcheance = new Date(date);
            dateEcheance.setDate(date.getDate() + 45);
            
            const dateStr = dateEcheance.toLocaleDateString('fr-FR');
            document.getElementById('date-echeance-solde').textContent = `Échéance : ${dateStr}`;
            document.getElementById('date-echeance-echelonnage').textContent = `Échéance : ${dateStr} (45 jours après signature)`;
        }

        function convertirMontantLettres(montant) {
            if (montant === 500000) return "Cinq cent mille francs CFA";
            if (montant === 250000) return "Deux cent cinquante mille francs CFA";
            return montant.toLocaleString() + " francs CFA";
        }

        // ==========================================
        // FONCTIONS UTILITAIRES
        // ==========================================
        function copierLien() {
            const input = document.getElementById('lien-signature-input');
            input.select();
            navigator.clipboard.writeText(input.value);
            alert("✅ Lien copié !");
        }

        function effacerSignature() {
            if (signaturePad) signaturePad.clear();
        }

        function updateStats() {
            const totalContrats = contrats.length;
            const signes = contrats.filter(c => c.statut === 'Signé').length;
            const enAttente = contrats.filter(c => c.statut === 'En attente').length;
            const totalFiches = fiches.length;

            // Stats principales
            document.getElementById('stat-total').textContent = totalContrats;
            document.getElementById('stat-signes').textContent = signes;
            document.getElementById('stat-fiches').textContent = totalFiches;

            // Mini stats des Actions Rapides
            if (document.getElementById('quick-stat-total')) {
                document.getElementById('quick-stat-total').textContent = totalContrats;
                document.getElementById('quick-stat-signes').textContent = signes;
                document.getElementById('quick-stat-attente').textContent = enAttente;
            }

            const tauxSignature = totalContrats > 0 ? Math.round((signes / totalContrats) * 100) : 0;
            document.getElementById('taux-signature').textContent = tauxSignature + '%';
            document.getElementById('progress-signature').style.width = tauxSignature + '%';
        }

        function updateListeContrats() {
            const tbody = document.getElementById('table-contrats');
            const searchTerm = document.getElementById('search-contrats')?.value.toLowerCase() || '';
            const filterStatut = document.getElementById('filter-statut')?.value || 'tous';

            // Filtrer les contrats
            let contratsFiltered = contrats.filter(contrat => {
                const matchSearch = searchTerm === '' ||
                    contrat.numero.toLowerCase().includes(searchTerm) ||
                    contrat.nom.toLowerCase().includes(searchTerm) ||
                    contrat.prenom.toLowerCase().includes(searchTerm) ||
                    contrat.destination_visa.toLowerCase().includes(searchTerm);

                const matchStatut = filterStatut === 'tous' || contrat.statut === filterStatut;

                return matchSearch && matchStatut;
            });

            if (contratsFiltered.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4"><i class="bi bi-inbox text-muted"></i><br>Aucun contrat trouvé</td></tr>';
                document.getElementById('total-contrats-count').textContent = '0';
                return;
            }

            tbody.innerHTML = contratsFiltered.map(contrat => {
                const isExpired = contrat.token_expires_at && new Date(contrat.token_expires_at) < new Date();
                const isSigned = contrat.statut === 'Signé';

                return `
                <tr style="transition: all 0.3s;">
                    <td><strong style="color: var(--primary);">${contrat.numero}</strong></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 35px; height: 35px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                ${contrat.prenom.charAt(0)}${contrat.nom.charAt(0)}
                            </div>
                            <div>
                                <div style="font-weight: 600;">${contrat.prenom} ${contrat.nom}</div>
                                <small style="color: var(--text-muted);">${contrat.email || 'Pas d\'email'}</small>
                            </div>
                        </div>
                    </td>
                    <td><i class="bi bi-geo-alt-fill" style="color: var(--danger);"></i> ${contrat.destination_visa}</td>
                    <td><strong style="color: var(--success);">${contrat.montant_total.toLocaleString()} FCFA</strong></td>
                    <td>
                        ${isSigned ?
                            '<span class="badge badge-success" style="padding: 0.5rem 0.75rem;"><i class="bi bi-check-circle-fill"></i> Signé</span>' :
                            isExpired ?
                                '<span class="badge badge-danger" style="padding: 0.5rem 0.75rem;"><i class="bi bi-clock-history"></i> Lien expiré</span>' :
                                '<span class="badge badge-warning" style="padding: 0.5rem 0.75rem;"><i class="bi bi-hourglass-split"></i> En attente</span>'
                        }
                    </td>
                    <td><small style="color: var(--text-muted);">${new Date(contrat.date_creation).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' })}</small></td>
                    <td class="text-center">
                        <div style="display: flex; gap: 0.25rem; justify-content: center; flex-wrap: wrap;">
                            <!-- Voir -->
                            <button class="btn btn-sm btn-outline-primary" onclick="voirContrat(${contrat.id})" title="Voir le contrat" style="min-width: 36px;">
                                <i class="bi bi-eye"></i>
                            </button>

                            <!-- Envoyer le contrat (lien vers PDF) -->
                            <button class="btn btn-sm btn-info" onclick="envoyerLienContrat(${contrat.id})" title="Envoyer le contrat au client" style="min-width: 36px;">
                                <i class="bi bi-send-fill"></i>
                            </button>

                            <!-- Copier lien signature (si non signé et non expiré) -->
                            ${!isSigned && !isExpired ? `
                                <button class="btn btn-sm btn-outline-success" onclick="afficherLienSignature(${contrat.id})" title="Afficher le lien de signature" style="min-width: 36px;">
                                    <i class="bi bi-link-45deg"></i>
                                </button>
                            ` : ''}

                            <!-- Régénérer lien si expiré -->
                            ${!isSigned && isExpired ? `
                                <button class="btn btn-sm btn-outline-warning" onclick="regenererLienSignature(${contrat.id})" title="Régénérer le lien" style="min-width: 36px;">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            ` : ''}

                            <!-- Imprimer -->
                            <button class="btn btn-sm btn-outline-secondary" onclick="imprimerContrat(${contrat.id})" title="Imprimer" style="min-width: 36px;">
                                <i class="bi bi-printer"></i>
                            </button>

                            <!-- Modifier (seulement si non signé) -->
                            ${!isSigned ? `
                                <button class="btn btn-sm btn-primary" onclick="modifierContrat(${contrat.id})" title="Modifier" style="min-width: 36px;">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            ` : ''}

                            <!-- Supprimer -->
                            <button class="btn btn-sm btn-outline-danger" onclick="supprimerContrat(${contrat.id})" title="Supprimer" style="min-width: 36px;">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `}).join('');

            document.getElementById('total-contrats-count').textContent = contratsFiltered.length;
        }

        function updateListeFiches() {
            const tbody = document.getElementById('table-fiches');
            if (fiches.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">Aucune fiche</td></tr>';
                return;
            }
            
            tbody.innerHTML = fiches.map(fiche => `
                <tr>
                    <td>${fiche.numero}</td>
                    <td>${fiche.prenom} ${fiche.nom}</td>
                    <td>${fiche.pays_destination}</td>
                    <td>${fiche.type_visa}</td>
                    <td>${new Date(fiche.date_creation).toLocaleDateString('fr-FR')}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="voirFiche(${fiche.id})">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function voirContrat(id) {
            const contrat = contrats.find(c => c.id === id);
            if (contrat) {
                document.getElementById('apercu-contenu').innerHTML = genererContratHTML(contrat);
                showSection('apercu-contrat');
            }
        }

        function voirFiche(id) {
            const fiche = fiches.find(f => f.id === id);
            if (fiche) {
                document.getElementById('apercu-fiche-contenu').innerHTML = genererFicheHTML(fiche);
                showSection('apercu-fiche');
            }
        }

        // ==========================================
        // NOUVELLES ACTIONS DE GESTION DES CONTRATS
        // ==========================================

        /**
         * Filtrer les contrats (intégré dans updateListeContrats)
         */
        function filterContrats() {
            updateListeContrats();
        }

        /**
         * Réinitialiser les filtres
         */
        function resetFilters() {
            document.getElementById('search-contrats').value = '';
            document.getElementById('filter-statut').value = 'tous';
            updateListeContrats();
        }

        /**
         * Afficher le modal du lien de signature avec message professionnel
         */
        function afficherLienSignature(id) {
            const contrat = contrats.find(c => c.id === id);
            if (!contrat) {
                alert('❌ Contrat introuvable');
                return;
            }

            // Créer le modal
            const modal = document.createElement('div');
            modal.id = 'signatureLinkModal';
            modal.innerHTML = `
                <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);
                            z-index: 9999; display: flex; align-items: center; justify-content: center;">
                    <div style="background: white; padding: 2rem; border-radius: 1rem; max-width: 600px; width: 90%;
                                box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-height: 90vh; overflow-y: auto;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                            <h3 style="margin: 0; color: var(--primary); display: flex; align-items: center; gap: 0.5rem;">
                                <i class="bi bi-pen"></i>
                                Lien de Signature du Contrat
                            </h3>
                            <button onclick="closeSignatureLinkModal()" style="background: none; border: none; font-size: 1.5rem;
                                            cursor: pointer; color: var(--text-muted);">×</button>
                        </div>

                        <div style="background: var(--bg-light); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                            <p style="margin: 0 0 0.5rem 0; font-size: 0.875rem; color: var(--text-muted);">
                                <strong>Contrat:</strong> ${contrat.numero_contrat}
                            </p>
                            <p style="margin: 0; font-size: 0.875rem; color: var(--text-muted);">
                                <strong>Client:</strong> ${contrat.prenom} ${contrat.nom}
                            </p>
                        </div>

                        <!-- Message professionnel -->
                        <div style="margin-bottom: 1.5rem; padding: 1.5rem; background: #f8f9fa; border-radius: 0.75rem; border: 1px solid #e9ecef;">
                            <p style="margin: 0 0 1rem 0; font-weight: 500; color: #1e3c72;">Cher(e) Candidat(e),</p>
                            <p style="margin: 0 0 1rem 0; line-height: 1.6;">Pour finaliser l'activation officielle de votre dossier, veuillez suivre attentivement les étapes suivantes :</p>
                            <ol style="margin: 0 0 1rem 1.5rem; line-height: 1.8; color: #495057;">
                                <li>Accéder au lien du contrat ci-dessous</li>
                                <li>Lire attentivement le contrat et cocher les cases de validation</li>
                                <li>Cliquer sur "Valider"</li>
                                <li>Signer électroniquement le document</li>
                            </ol>
                            <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 4px;">
                                <strong>⚠️ Important :</strong> La validation et la signature du contrat sont obligatoires pour activer officiellement votre dossier.
                            </div>
                            <p style="margin: 0; font-style: italic; color: #1e3c72; font-weight: 500;">PSI AFRICA – Votre projet, notre engagement.</p>
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-dark);">
                                Lien de signature:
                            </label>
                            <div style="display: flex; gap: 0.5rem;">
                                <input type="text" id="signatureLink" value="${contrat.signature_url}" readonly
                                       style="flex: 1; padding: 0.75rem; border: 1px solid var(--border-color);
                                              border-radius: 0.5rem; font-family: monospace; font-size: 0.875rem;
                                              background: white;">
                                <button onclick="copySignatureLink()" id="copySignatureBtn"
                                        style="padding: 0.75rem 1.5rem; background: var(--success); color: white;
                                               border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 600;
                                               transition: all 0.3s ease; display: flex; align-items: center; gap: 0.5rem;
                                               white-space: nowrap;">
                                    <i class="bi bi-clipboard"></i>
                                    Copier le message et le lien
                                </button>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                            <button onclick="closeSignatureLinkModal()"
                                    style="padding: 0.75rem 1.5rem; background: var(--bg-light); color: var(--text-dark);
                                           border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 600;">
                                Fermer
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            // Sélectionner automatiquement le lien
            document.getElementById('signatureLink').select();

            showNotification('✅ Modal du lien de signature affichée', 'success');
        }

        /**
         * Copier le message professionnel complet avec le lien de signature
         */
        function copySignatureLink() {
            const linkInput = document.getElementById('signatureLink');
            const signatureUrl = linkInput.value;

            // Créer le message complet à copier
            const fullMessage = `Cher(e) Candidat(e),

Pour finaliser l'activation officielle de votre dossier, veuillez suivre attentivement les étapes suivantes :

1. Accéder au lien du contrat ci-dessous
2. Lire attentivement le contrat et cocher les cases de validation
3. Cliquer sur "Valider"
4. Signer électroniquement le document

⚠️ Important : La validation et la signature du contrat sont obligatoires pour activer officiellement votre dossier.

Lien de signature du contrat :
${signatureUrl}

PSI AFRICA – Votre projet, notre engagement.`;

            try {
                // Utiliser l'API moderne de clipboard si disponible
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(fullMessage).then(() => {
                        const copyBtn = document.getElementById('copySignatureBtn');
                        const originalHTML = copyBtn.innerHTML;

                        // Changer temporairement le bouton
                        copyBtn.innerHTML = '<i class="bi bi-check-lg"></i> Copié !';
                        copyBtn.style.background = '#28a745';

                        // Restaurer après 2 secondes
                        setTimeout(() => {
                            copyBtn.innerHTML = originalHTML;
                            copyBtn.style.background = 'var(--success)';
                        }, 2000);

                        showNotification('📋 Message et lien de signature copiés dans le presse-papiers', 'success');
                    }).catch(err => {
                        console.error('Erreur clipboard API:', err);
                        fallbackCopySignatureLink(fullMessage);
                    });
                } else {
                    // Fallback pour les navigateurs plus anciens
                    fallbackCopySignatureLink(fullMessage);
                }
            } catch (err) {
                console.error('Erreur lors de la copie:', err);
                alert('Erreur lors de la copie. Veuillez copier manuellement le message.');
            }
        }

        /**
         * Méthode de secours pour copier le lien de signature (navigateurs anciens)
         */
        function fallbackCopySignatureLink(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                document.execCommand('copy');
                const copyBtn = document.getElementById('copySignatureBtn');
                const originalHTML = copyBtn.innerHTML;

                copyBtn.innerHTML = '<i class="bi bi-check-lg"></i> Copié !';
                copyBtn.style.background = '#28a745';

                setTimeout(() => {
                    copyBtn.innerHTML = originalHTML;
                    copyBtn.style.background = 'var(--success)';
                }, 2000);

                showNotification('📋 Message et lien de signature copiés dans le presse-papiers', 'success');
            } catch (err) {
                console.error('Erreur execCommand:', err);
                alert('Erreur lors de la copie. Veuillez copier manuellement le message.');
            }

            document.body.removeChild(textArea);
        }

        /**
         * Fermer le modal du lien de signature
         */
        function closeSignatureLinkModal() {
            const modal = document.getElementById('signatureLinkModal');
            if (modal) {
                modal.remove();
            }
        }

        /**
         * Régénérer le lien de signature (si expiré)
         */
        async function regenererLienSignature(contractId) {
            if (!confirm('Voulez-vous régénérer un nouveau lien de signature pour ce contrat ?')) {
                return;
            }

            try {
                const response = await fetch(`/crm/contrats/${contractId}/regenerate-token`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    // Mettre à jour le contrat dans le tableau local
                    const contrat = contrats.find(c => c.id === contractId);
                    if (contrat) {
                        contrat.signature_url = result.signature_url;
                        contrat.token_expires_at = result.token_expires_at;
                    }

                    updateListeContrats();

                    alert(`✅ Nouveau lien généré avec succès!\n\nLien valable jusqu'au ${result.token_expires_at}`);

                    // Afficher le lien
                    copierLienSignature(result.signature_url);
                } else {
                    alert('❌ Erreur: ' + result.message);
                }
            } catch (error) {
                console.error('Erreur régénération token:', error);
                alert('❌ Erreur lors de la régénération du lien.');
            }
        }

        /**
         * Imprimer un contrat
         */
        function imprimerContrat(id) {
            const contrat = contrats.find(c => c.id === id);
            if (!contrat) return;

            // Générer le HTML du contrat
            const contratHTML = genererContratHTML(contrat);

            // Créer une nouvelle fenêtre pour l'impression
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Contrat ${contrat.numero}</title>
                    <style>
                        * { margin: 0; padding: 0; box-sizing: border-box; }
                        body {
                            font-family: Arial, sans-serif;
                            padding: 20px;
                            line-height: 1.6;
                            color: #333;
                        }
                        h1, h2, h3, h4, h5, h6 {
                            margin-top: 1rem;
                            margin-bottom: 0.5rem;
                        }
                        p { margin-bottom: 0.5rem; }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin: 1rem 0;
                        }
                        table th, table td {
                            padding: 0.5rem;
                            border: 1px solid #ddd;
                            text-align: left;
                        }
                        table th {
                            background: #f5f5f5;
                            font-weight: bold;
                        }
                        @media print {
                            body { margin: 0; padding: 20px; }
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>
                    ${contratHTML}
                    <script>
                        window.onload = function() {
                            window.print();
                        }
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }

        /**
         * Générer et copier le lien du contrat
         * Affiche une modal avec le lien que le client peut copier
         */
        async function envoyerLienContrat(id) {
            const contrat = contrats.find(c => c.id === id);
            if (!contrat) {
                alert('❌ Contrat introuvable');
                return;
            }

            try {
                // Afficher un message de chargement
                const loadingMsg = document.createElement('div');
                loadingMsg.innerHTML = `
                    <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
                                background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                                z-index: 9999; text-align: center;">
                        <div style="font-size: 2rem; margin-bottom: 1rem;">
                            <i class="bi bi-link-45deg" style="color: var(--info);"></i>
                        </div>
                        <div style="font-weight: 600; margin-bottom: 0.5rem;">Génération du lien...</div>
                        <div style="color: var(--text-muted); font-size: 0.875rem;">Veuillez patienter</div>
                    </div>
                `;
                document.body.appendChild(loadingMsg);

                // Appeler l'API pour générer le lien du contrat
                const response = await fetch(`/crm/contrats/${id}/send-link`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                document.body.removeChild(loadingMsg);

                if (response.ok) {
                    const data = await response.json();

                    // Afficher une modal avec le lien
                    const modal = document.createElement('div');
                    modal.id = 'linkModal';
                    modal.innerHTML = `
                        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);
                                    z-index: 9999; display: flex; align-items: center; justify-content: center;">
                            <div style="background: white; padding: 2rem; border-radius: 1rem; max-width: 600px; width: 90%;
                                        box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                                    <h3 style="margin: 0; color: var(--primary); display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="bi bi-link-45deg"></i>
                                        Lien du contrat
                                    </h3>
                                    <button onclick="closeLinkModal()" style="background: none; border: none; font-size: 1.5rem;
                                                    cursor: pointer; color: var(--text-muted);">×</button>
                                </div>

                                <div style="background: var(--bg-light); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                                    <p style="margin: 0 0 0.5rem 0; font-size: 0.875rem; color: var(--text-muted);">
                                        <strong>Contrat:</strong> ${data.contract.numero_contrat}
                                    </p>
                                    <p style="margin: 0; font-size: 0.875rem; color: var(--text-muted);">
                                        <strong>Client:</strong> ${data.contract.nom_complet}
                                    </p>
                                </div>

                                <!-- Message professionnel -->
                                <div style="margin-bottom: 1.5rem; padding: 1.5rem; background: #f8f9fa; border-radius: 0.75rem; border: 1px solid #e9ecef;">
                                    <p style="margin: 0 0 1rem 0; font-weight: 500; color: #1e3c72;">Cher(e) Candidat(e),</p>
                                    <p style="margin: 0 0 1rem 0; line-height: 1.6;">Pour finaliser l'activation officielle de votre dossier, veuillez suivre attentivement les étapes suivantes :</p>
                                    <ol style="margin: 0 0 1rem 1.5rem; line-height: 1.8; color: #495057;">
                                        <li>Accéder au lien du contrat ci-dessous</li>
                                        <li>Lire attentivement le contrat et cocher les cases de validation</li>
                                        <li>Cliquer sur "Valider"</li>
                                        <li>Signer électroniquement le document</li>
                                    </ol>
                                    <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 4px;">
                                        <strong>⚠️ Important :</strong> La validation et la signature du contrat sont obligatoires pour activer officiellement votre dossier.
                                    </div>
                                    <p style="margin: 0; font-style: italic; color: #1e3c72; font-weight: 500;">PSI AFRICA – Votre projet, notre engagement.</p>
                                </div>

                                <div style="margin-bottom: 1.5rem;">
                                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-dark);">
                                        Lien du contrat:
                                    </label>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <input type="text" id="contractLink" value="${data.view_link}" readonly
                                               style="flex: 1; padding: 0.75rem; border: 1px solid var(--border-color);
                                                      border-radius: 0.5rem; font-family: monospace; font-size: 0.875rem;
                                                      background: white;">
                                        <button onclick="copyContractLink()" id="copyBtn"
                                                style="padding: 0.75rem 1.5rem; background: var(--primary); color: white;
                                                       border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 600;
                                                       transition: all 0.3s ease; display: flex; align-items: center; gap: 0.5rem;
                                                       white-space: nowrap;">
                                            <i class="bi bi-clipboard"></i>
                                            Copier le message et le lien
                                        </button>
                                    </div>
                                </div>

                                <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                                    <button onclick="closeLinkModal()"
                                            style="padding: 0.75rem 1.5rem; background: var(--bg-light); color: var(--text-dark);
                                                   border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 600;">
                                        Fermer
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modal);

                    // Sélectionner automatiquement le lien
                    document.getElementById('contractLink').select();

                    showNotification('✅ Lien généré avec succès', 'success');
                } else {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Erreur lors de la génération du lien');
                }
            } catch (error) {
                console.error('Erreur génération lien:', error);
                alert(`❌ ${error.message}`);
            }
        }

        /**
         * Copier le message professionnel complet avec le lien du contrat
         */
        function copyContractLink() {
            const linkInput = document.getElementById('contractLink');
            const contractUrl = linkInput.value;

            // Créer le message complet à copier
            const fullMessage = `Cher(e) Candidat(e),

Pour finaliser l'activation officielle de votre dossier, veuillez suivre attentivement les étapes suivantes :

1. Accéder au lien du contrat ci-dessous
2. Lire attentivement le contrat et cocher les cases de validation
3. Cliquer sur "Valider"
4. Signer électroniquement le document

⚠️ Important : La validation et la signature du contrat sont obligatoires pour activer officiellement votre dossier.

Lien du contrat :
${contractUrl}

PSI AFRICA – Votre projet, notre engagement.`;

            try {
                // Utiliser l'API moderne de clipboard si disponible
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(fullMessage).then(() => {
                        const copyBtn = document.getElementById('copyBtn');
                        const originalHTML = copyBtn.innerHTML;

                        // Changer temporairement le bouton
                        copyBtn.innerHTML = '<i class="bi bi-check-lg"></i> Copié !';
                        copyBtn.style.background = 'var(--success)';

                        // Restaurer après 2 secondes
                        setTimeout(() => {
                            copyBtn.innerHTML = originalHTML;
                            copyBtn.style.background = 'var(--primary)';
                        }, 2000);

                        showNotification('📋 Message et lien copiés dans le presse-papiers', 'success');
                    }).catch(err => {
                        console.error('Erreur clipboard API:', err);
                        fallbackCopyTextToClipboard(fullMessage);
                    });
                } else {
                    // Fallback pour les navigateurs plus anciens
                    fallbackCopyTextToClipboard(fullMessage);
                }
            } catch (err) {
                console.error('Erreur lors de la copie:', err);
                alert('Erreur lors de la copie. Veuillez copier manuellement le message.');
            }
        }

        /**
         * Méthode de secours pour copier du texte (navigateurs anciens)
         */
        function fallbackCopyTextToClipboard(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                document.execCommand('copy');
                const copyBtn = document.getElementById('copyBtn');
                const originalHTML = copyBtn.innerHTML;

                copyBtn.innerHTML = '<i class="bi bi-check-lg"></i> Copié !';
                copyBtn.style.background = 'var(--success)';

                setTimeout(() => {
                    copyBtn.innerHTML = originalHTML;
                    copyBtn.style.background = 'var(--primary)';
                }, 2000);

                showNotification('📋 Message et lien copiés dans le presse-papiers', 'success');
            } catch (err) {
                console.error('Erreur execCommand:', err);
                alert('Erreur lors de la copie. Veuillez copier manuellement le message.');
            }

            document.body.removeChild(textArea);
        }

        /**
         * Fermer la modal du lien
         */
        function closeLinkModal() {
            const modal = document.getElementById('linkModal');
            if (modal) {
                modal.remove();
            }
        }

        /**
         * Afficher une notification temporaire
         */
        function showNotification(message, type = 'info') {
            const colors = {
                success: 'var(--success)',
                error: 'var(--danger)',
                warning: 'var(--warning)',
                info: 'var(--info)'
            };

            const notification = document.createElement('div');
            notification.innerHTML = `
                <div style="position: fixed; top: 20px; right: 20px; background: ${colors[type]}; color: white;
                            padding: 1rem 1.5rem; border-radius: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                            z-index: 9999; animation: slideInRight 0.3s ease-out;">
                    ${message}
                </div>
            `;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease-out';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        /**
         * Modifier un contrat
         */
        function modifierContrat(id) {
            const contrat = contrats.find(c => c.id === id);
            if (!contrat) return;

            if (contrat.statut === 'Signé') {
                alert('⚠️ Ce contrat est déjà signé et ne peut plus être modifié.');
                return;
            }

            // Remplir le formulaire avec les données existantes
            document.querySelector('[name="nom"]').value = contrat.nom;
            document.querySelector('[name="prenom"]').value = contrat.prenom;
            document.querySelector('[name="date_naissance"]').value = contrat.date_naissance;
            document.querySelector('[name="lieu_naissance"]').value = contrat.lieu_naissance;
            document.querySelector('[name="profession"]').value = contrat.profession;
            document.querySelector('[name="telephone"]').value = contrat.telephone_mobile;
            document.querySelector('[name="email"]').value = contrat.email;
            document.querySelector('[name="numero_cni"]').value = contrat.numero_cni || '';
            document.querySelector('[name="adresse"]').value = contrat.adresse;
            document.querySelector('[name="destination_visa"]').value = contrat.pays_destination;
            document.querySelector('[name="type_visa"]').value = contrat.type_visa;
            document.querySelector('[name="montant_total"]').value = contrat.montant_total;
            document.querySelector('[name="mode_paiement"]').value = contrat.mode_paiement;
            document.querySelector('[name="conseiller"]').value = contrat.conseiller;
            document.querySelector('[name="lieu_contrat"]').value = contrat.lieu_contrat;
            document.querySelector('[name="date_contrat"]').value = contrat.date_contrat;
            document.querySelector('[name="observations"]').value = contrat.observations || '';

            // Stocker l'ID pour la mise à jour
            currentContrat = contrat;

            // Afficher le formulaire
            showSection('nouveau-contrat');

            alert('ℹ️ Vous pouvez maintenant modifier le contrat. Cliquez sur "Valider" pour enregistrer les modifications.');
        }

        /**
         * Supprimer un contrat
         */
        async function supprimerContrat(id) {
            const contrat = contrats.find(c => c.id === id);
            if (!contrat) return;

            if (!confirm(`⚠️ Êtes-vous sûr de vouloir supprimer le contrat ${contrat.numero} ?\n\nCette action est irréversible.`)) {
                return;
            }

            try {
                const response = await fetch(`/crm/contrats/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    // Retirer du tableau local
                    contrats = contrats.filter(c => c.id !== id);

                    updateStats();
                    updateListeContrats();

                    addAuditLog('Contrat supprimé', `Contrat ${contrat.numero} supprimé`);
                    alert('✅ Contrat supprimé avec succès.');
                } else {
                    alert('❌ Erreur: ' + result.message);
                }
            } catch (error) {
                console.error('Erreur suppression contrat:', error);
                alert('❌ Erreur lors de la suppression du contrat.');
            }
        }

        // ==========================================
        // GESTION DES LOGS D'AUDIT
        // ==========================================
        let auditLogs = []; // Stockage en mémoire des logs de session

        function updateAuditLogs() {
            const container = document.getElementById('audit-logs');

            if (auditLogs.length === 0) {
                container.innerHTML = '<div class="text-center text-muted py-3"><i class="bi bi-inbox"></i><br>Aucune activité récente</div>';
                return;
            }

            container.innerHTML = auditLogs.slice(-5).reverse().map(log => `
                <div class="audit-log">
                    <div class="d-flex justify-content-between">
                        <strong>${log.action}</strong>
                        <small>${new Date(log.timestamp).toLocaleString()}</small>
                    </div>
                    <div class="text-muted">${log.details}</div>
                </div>
            `).join('');
        }

        function addAuditLog(action, details) {
            auditLogs.push({ action, details, timestamp: new Date().toISOString() });
            updateAuditLogs();
        }

        function resetSystem() {
            if (confirm("Voulez-vous vraiment recharger les données depuis la base de données ?")) {
                contrats = [];
                fiches = [];
                auditLogs = [];
                loadContratsFromDB(); // Recharge depuis la BDD
                alert("✅ Données rechargées depuis la base de données");
            }
        }

        // ==========================================
        // GÉNÉRATION HTML AVEC VRAIS MODÈLES
        // ==========================================
        function genererContratHTML(data) {
            // Article 3 : Toujours afficher les modalités de paiement
            const montantTotal = parseInt(data.montant_total || data.montant_contrat || 0);
            const avance = parseInt(data.avance || 0);
            const restePayer = parseInt(data.reste_payer || (montantTotal - avance));

            let conditionsPaiement = `
                <div class="contrat-article">
                    <h4>Article 3 : Modalités financières</h4>
                    <p>Les frais dus à <strong>CCE</strong> comprennent deux catégories :</p>
                    <ul>
                        <li><strong>Frais de cabinet</strong> : couvrant le conseil, le suivi et la gestion du dossier ;</li>
                        <li><strong>Frais d'assistance et d'inscription</strong>, le cas échéant.</li>
                    </ul>

                    <h5>3.1 Montant et conditions de paiement</h5>
                    <p><strong>Montant total des frais :</strong> ${montantTotal.toLocaleString()} FCFA</p>
                    ${data.montant_lettres ? `<p><em>(${data.montant_lettres})</em></p>` : ''}

                    ${avance > 0 ? `
                        <p><strong>✅ Avance versée :</strong> <span style="color: #28a745; font-weight: bold;">${avance.toLocaleString()} FCFA</span></p>
                    ` : ''}

                    ${restePayer > 0 ? `
                        <p><strong>⏳ Reste à payer :</strong> <span style="color: #dc3545; font-weight: bold;">${restePayer.toLocaleString()} FCFA</span></p>
                    ` : ''}

                    ${data.mode_paiement ? `
                        <p><strong>Mode de paiement :</strong> ${data.mode_paiement}</p>
                    ` : ''}

                    ${data.echeances && data.echeances.length > 0 ? `
                        <h5 style="margin-top: 15px;">3.2 Échéancier de paiement</h5>
                        <table style="width: 100%; border-collapse: collapse; margin: 10px 0;">
                            <thead>
                                <tr style="background: #f8f9fa;">
                                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Description</th>
                                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Montant</th>
                                    <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Date</th>
                                    <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.echeances.map((echeance, index) => `
                                    <tr>
                                        <td style="border: 1px solid #ddd; padding: 8px;">${echeance.type || ('Versement ' + (index + 1))}</td>
                                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${parseInt(echeance.montant).toLocaleString()} FCFA</td>
                                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${new Date(echeance.date).toLocaleDateString('fr-FR')}</td>
                                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                                            ${echeance.statut === 'payé' || index === 0 ? '✅ Versé' : '⏳ À venir'}
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    ` : ''}

                    <p style="margin-top: 15px;"><strong>Clause importante :</strong> Tout retard de paiement supérieur à 10 jours entraîne une <strong>suspension du suivi du dossier</strong> jusqu'à régularisation complète.</p>
                </div>
            `;

            return `
                <div class="contrat-content">
                    <div style="text-align: center; border-bottom: 2px solid #0A2463; padding-bottom: 20px; margin-bottom: 30px;">
                        <h1 style="color: #0A2463; margin-bottom: 5px;">CABINET CONSEILLER EXPERT (CCE)</h1>
                        <h2 style="color: #D4AF37; margin-bottom: 10px;">CONTRAT D'ASSISTANCE ET DE CONSEIL EN PROCÉDURE DE VISA</h2>
                        <p style="color: #666;">Cabinet de conseil spécialisé en immigration légale</p>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <p><strong>Entre les soussignés :</strong></p>
                        <p><strong>CABINET CONSEILLER EXPERT (CCE)</strong>, Cabinet de conseil spécialisé en immigration légale, immatriculé en Côte d'Ivoire,</p>
                        <p>ci-après désigné <em>« le Cabinet »</em> ou <em>« CCE »</em>,</p>
                        
                        <p><strong>Et :</strong></p>
                        <p><strong>M./Mme : ${data.prenom} ${data.nom}</strong></p>
                        <p>Né(e) le : ${new Date(data.date_naissance).toLocaleDateString('fr-FR')}</p>
                        <p>Profession : ${data.profession || 'Non renseigné'}</p>
                        <p>Téléphone : ${data.telephone}</p>
                        <p>Adresse e-mail : ${data.email}</p>
                        <p>ci-après désigné <em>« le Client »</em>.</p>
                        
                        <p>Les deux parties, ci-après désignées collectivement <em>« les Parties »</em>, ont convenu de ce qui suit :</p>
                    </div>

                    <div class="contrat-article">
                        <h4>Article 1 : Objet du contrat</h4>
                        <p>Le présent contrat a pour objet de définir les conditions dans lesquelles <strong>CCE</strong> assure au Client :</p>
                        <ul>
                            <li>Une <strong>assistance administrative, documentaire et logistique</strong> dans le cadre de sa <strong>demande de visa</strong> ;</li>
                            <li>Un <strong>accompagnement personnalisé</strong> pour la constitution du dossier, la prise de rendez-vous, la préparation à l'entretien et le suivi de la procédure jusqu'à décision finale.</li>
                        </ul>
                        <p><strong>CCE</strong> agit uniquement comme <strong>cabinet de conseil et d'assistance</strong> et <strong>n'intervient pas dans la décision d'obtention du visa</strong>, celle-ci relevant exclusivement des autorités consulaires.</p>
                    </div>

                    <div class="contrat-article">
                        <h4>Article 2 : Nature des prestations</h4>
                        <p>Les prestations fournies par le Cabinet comprennent notamment :</p>
                        <ol>
                            <li>L'étude du profil du Client et la définition du type de visa approprié ;</li>
                            <li>L'assistance pour la constitution du dossier (formulaires, pièces, justificatifs, traduction) ;</li>
                            <li>La préparation à l'entretien consulaire (simulation, coaching, conseils personnalisés) ;</li>
                            <li>La prise de rendez-vous et le suivi administratif de la demande ;</li>
                            <li>Le conseil général sur les démarches liées au voyage (hébergement, assurance, billet d'avion, etc.).</li>
                        </ol>
                        <p>Toute prestation non mentionnée dans la liste ci-dessus fera l'objet d'un <strong>avenant</strong> ou d'un <strong>devis séparé</strong>.</p>
                    </div>

                    ${conditionsPaiement}

                    <div class="contrat-article">
                        <h4>Article 4 : Clause de non-remboursement</h4>
                        <p>Les frais versés à <strong>CCE</strong> sont <strong>strictement non remboursables</strong>, pour les raisons suivantes :</p>
                        <ul>
                            <li>Les prestations de conseil et d'assistance sont considérées comme <strong>démarrées dès l'ouverture du dossier</strong> ;</li>
                            <li>Le Cabinet engage des ressources humaines, matérielles et administratives dès la signature du contrat ;</li>
                            <li>Le résultat final (délivrance du visa) dépend <strong>exclusivement de la décision consulaire</strong>, indépendante du Cabinet.</li>
                        </ul>
                        <p>Aucun remboursement ne sera accordé en cas :</p>
                        <ul>
                            <li>De refus de visa par les autorités consulaires ;</li>
                            <li>De retrait du dossier par le Client ;</li>
                            <li>De fourniture tardive, incomplète ou erronée des documents par le Client ;</li>
                            <li>De non-respect du calendrier ou des consignes données par le Cabinet.</li>
                        </ul>
                    </div>

                    <div class="contrat-article">
                        <h4>Article 5 : Obligations du Cabinet</h4>
                        <p><strong>CCE</strong> s'engage à :</p>
                        <ul>
                            <li>Fournir des conseils exacts, actualisés et conformes aux procédures consulaires en vigueur ;</li>
                            <li>Assister le Client de manière professionnelle, transparente et confidentielle ;</li>
                            <li>Respecter les délais convenus dans la mesure où le Client fournit les éléments demandés en temps utile.</li>
                        </ul>
                        <p>Le Cabinet ne saurait être tenu responsable :</p>
                        <ul>
                            <li>Des décisions des ambassades, consulats ou services d'immigration ;</li>
                            <li>Des retards administratifs ou modifications de procédures ;</li>
                            <li>Des pertes ou rejets de documents imputables au Client ou aux autorités.</li>
                        </ul>
                    </div>

                    <div class="contrat-article">
                        <h4>Article 6 : Obligations du Client</h4>
                        <p>Le Client s'engage à :</p>
                        <ul>
                            <li>Fournir des informations exactes et véridiques ;</li>
                            <li>Respecter les délais de remise des documents demandés ;</li>
                            <li>Régler les frais dus dans les conditions prévues à l'article 3 ;</li>
                            <li>Suivre les conseils du Cabinet et ne pas engager de démarches parallèles sans en informer <strong>CCE</strong>.</li>
                        </ul>
                        <p>Toute fausse déclaration, omission ou comportement frauduleux entraînera la <strong>résiliation automatique</strong> du contrat sans remboursement possible.</p>
                    </div>

                    <div class="contrat-article">
                        <h4>Article 7 : Confidentialité et protection des données</h4>
                        <p><strong>CCE</strong> s'engage à protéger la confidentialité de toutes les informations fournies par le Client et à ne les utiliser que dans le cadre strict de la prestation d'assistance.</p>
                        <p>Aucune donnée ne sera transmise à un tiers sans l'accord écrit du Client.</p>
                    </div>

                    <div class="contrat-article">
                        <h4>Article 8 : Durée du contrat</h4>
                        <p>Le présent contrat prend effet à la date de signature et reste valable <strong>jusqu'à la fin de la procédure engagée</strong>.</p>
                        <p>Il prend automatiquement fin :</p>
                        <ul>
                            <li>À la délivrance du visa ;</li>
                            <li>Ou à la réception d'une décision de refus ;</li>
                            <li>Ou en cas de résiliation motivée par une faute grave du Client.</li>
                        </ul>
                    </div>

                    <div class="contrat-article">
                        <h4>Article 9 : Résiliation</h4>
                        <p>En cas de manquement grave par l'une des Parties à ses obligations contractuelles, le contrat pourra être résilié de plein droit après <strong>mise en demeure restée sans effet pendant 7 jours</strong>.</p>
                        <p>Aucune somme déjà versée ne sera remboursée.</p>
                    </div>

                    <div class="contrat-article">
                        <h4>Article 10 : Clause de non-garantie de résultat</h4>
                        <p>Le Cabinet s'engage à <strong>mettre en œuvre tous les moyens possibles</strong> pour l'aboutissement du dossier, sans pour autant garantir l'obtention du visa.</p>
                        <p>Le Client reconnaît avoir compris que <strong>le résultat dépend de la décision souveraine des autorités consulaires.</strong></p>
                    </div>

                    <div class="contrat-article">
                        <h4>Article 11 : Relance ou réclamation en cas de refus de visa</h4>
                        <p>En cas de refus de visa, le candidat conserve le droit de solliciter une relance de procédure ou une réclamation auprès de l'ambassade, avec l'accompagnement du cabinet CCE, selon les conditions suivantes :</p>

                        <h5>11.1 – Analyse du refus et accompagnement</h5>
                        <ul>
                            <li>Dès réception du refus, CCE procède à une analyse complète et détaillée des motifs communiqués par l'ambassade.</li>
                            <li>Un rapport d'analyse est établi afin d'identifier les causes probables du refus (documents manquants, incohérences, profil, etc.) et de proposer les actions correctives nécessaires.</li>
                            <li>Le candidat est ensuite orienté vers une relance ou une réclamation argumentée, selon la pertinence du dossier.</li>
                        </ul>

                        <h5>11.2 – Relance de la même demande (même type de visa et même destination)</h5>
                        <p>Si le candidat choisit de relancer une nouvelle demande de visa pour le même type de visa et la même destination, <strong>aucun nouvel honoraire de cabinet ne sera exigé</strong>.</p>
                        <p>Le candidat devra uniquement s'acquitter des frais suivants :</p>
                        <ul>
                            <li>Les frais de documents de voyage (assurance, réservations, etc.) ;</li>
                            <li>Les frais de rendez-vous ou de visa à l'ambassade, fixés par les autorités consulaires.</li>
                        </ul>
                        <p>Le candidat a le droit de relancer sa demande <strong>autant de fois qu'il le souhaite, sans limite</strong>, jusqu'à l'obtention effective de son visa, à condition que le dossier reste conforme et valide.</p>

                        <h5>11.3 – Changement de type de visa ou de destination</h5>
                        <p>Si le candidat décide de changer de type de visa (ex. passer d'un visa touristique à un visa études ou travail) ou de changer de pays de destination, il devra s'acquitter à nouveau des frais suivants :</p>
                        <ul>
                            <li>Les frais d'étude de profil visa relatifs au nouveau projet ;</li>
                            <li>Les documents de voyage exigés (assurance, réservations, etc.) ;</li>
                            <li>Les frais consulaires ou de rendez-vous applicables à la nouvelle destination.</li>
                        </ul>
                        <p>Les honoraires du cabinet pourront être ajustés en fonction de la nature de la nouvelle procédure.</p>

                        <h5>11.4 – Non-remboursement</h5>
                        <p>Conformément à la politique de non-remboursement du cabinet CCE, les sommes versées ne sont pas remboursables, car elles couvrent les prestations déjà effectuées (étude, montage, coaching, suivi administratif, etc.).</p>
                        <p>Cependant, CCE s'engage à accompagner le candidat jusqu'à la réussite finale de son projet, dans le respect des conditions ci-dessus.</p>
                    </div>

                    <div class="contrat-article">
                        <h4>Article 12 : Juridiction compétente</h4>
                        <p>En cas de litige, les Parties s'engagent d'abord à rechercher une solution amiable.</p>
                        <p>À défaut, compétence expresse est attribuée aux tribunaux du ressort du <strong>siège social du Cabinet Conseiller Expert (CCE)</strong>.</p>
                    </div>

                    <div class="contrat-article">
                        <h4>Article 13 : Acceptation</h4>
                        <p>Le Client déclare avoir lu, compris et accepté sans réserve les termes du présent contrat, ainsi que les <strong>conditions générales d'assistance du Cabinet Conseiller Expert (CCE)</strong> annexées au document.</p>
                    </div>

                    <div style="margin-top: 50px; border-top: 1px solid #000; padding-top: 20px;">
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 50%; vertical-align: top; text-align: center;">
                                    <strong>Le Client</strong><br><br>
                                    ${data.prenom} ${data.nom}<br><br>
                                    ${data.signature ? `
                                        <div style="margin: 10px 0;">
                                            <img src="${data.signature}" alt="Signature" style="max-width: 500px; border: 0px solid #ddd; padding: 40px; background: white;">
                                        </div>
                                        <p style="margin-top: 5px; font-size: 0.9em; color: #666;">
                                            Signé le ${data.date_signature ? new Date(data.date_signature).toLocaleString('fr-FR') : ''}
                                            ${data.nom_signataire ? '<br>Par: ' + data.nom_signataire : ''}
                                        </p>
                                    ` : `
                                        _________________________<br>
                                        <em>Signature précédée de la mention « Lu et approuvé »</em>
                                    `}
                                </td>
                                <td style="width: 50%; vertical-align: top; text-align: center;">
                                    <strong>CABINET CONSEILLER EXPERT (CCE)</strong><br><br>
                                    ${data.conseiller || 'Le Responsable'}<br><br>
                                    <div style="margin: 10px 0;">
                                        <img src="/img/cachet-cce.jpg" alt="Cachet CCE" style="max-width: 180px; height: auto;">
                                    </div>
                                    <em style="font-size: 0.85em; color: #666;">Cachet et signature du Cabinet</em>
                                </td>
                            </tr>
                        </table>

                        <div style="text-align: center; margin-top: 30px;">
                            <p><strong>Fait à ${data.lieu_contrat || 'Abidjan'}, le ${data.date_contrat}</strong></p>
                            <p>En deux exemplaires originaux.</p>
                        </div>
                    </div>

                    <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px; font-size: 0.9em;">
                        <p><strong>Mention légale :</strong> Document officiel généré électroniquement - Toute modification rend ce document nul.</p>
                        <p>Contrat généré le ${new Date().toLocaleDateString('fr-FR')} à ${new Date().toLocaleTimeString('fr-FR')}</p>
                    </div>
                </div>
            `;
        }

        function genererFicheHTML(data) {
            return `
                <div class="contrat-content">
                    <div style="text-align: center; border-bottom: 2px solid #0A2463; padding-bottom: 20px; margin-bottom: 30px;">
                        <h1 style="color: #0A2463; margin-bottom: 5px;">CABINET CONSEILLER EXPERT (CCE)</h1>
                        <h2 style="color: #D4AF37; margin-bottom: 10px;">BULLETIN INDIVIDUEL D'INSCRIPTION</h2>
                        <p style="color: #666;">(À remplir en lettres majuscules -- Une case par lettre ou chiffre)</p>
                        <p><strong>N° Fiche: ${data.numero || 'FICHE-' + new Date().getFullYear() + '-001'}</strong></p>
                    </div>

                    <div class="contrat-article">
                        <h4>🧾 1. Informations personnelles</h4>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px; width: 30%;"><strong>Type de visa souhaité</strong></td>
                                <td style="border: 1px solid #ddd; padding: 8px;"><div class="fiche-field">${data.type_visa}</div></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;"><strong>Pays de destination</strong></td>
                                <td style="border: 1px solid #ddd; padding: 8px;"><div class="fiche-field">${data.pays_destination}</div></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;"><strong>Nom</strong></td>
                                <td style="border: 1px solid #ddd; padding: 8px;"><div class="fiche-field">${data.nom}</div></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;"><strong>Prénoms</strong></td>
                                <td style="border: 1px solid #ddd; padding: 8px;"><div class="fiche-field">${data.prenom}</div></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;"><strong>Date et lieu de naissance</strong></td>
                                <td style="border: 1px solid #ddd; padding: 8px;"><div class="fiche-field">${new Date(data.date_naissance).toLocaleDateString('fr-FR')} à ${data.lieu_naissance || ''}</div></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;"><strong>Nationalité</strong></td>
                                <td style="border: 1px solid #ddd; padding: 8px;"><div class="fiche-field">${data.nationalite}</div></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;"><strong>Sexe</strong></td>
                                <td style="border: 1px solid #ddd; padding: 8px;"><div class="fiche-field">${data.sexe}</div></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;"><strong>État civil</strong></td>
                                <td style="border: 1px solid #ddd; padding: 8px;"><div class="fiche-field">${data.etat_civil}</div></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;"><strong>Profession actuelle</strong></td>
                                <td style="border: 1px solid #ddd; padding: 8px;"><div class="fiche-field">${data.profession}</div></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;"><strong>Employeur / Entreprise</strong></td>
                                <td style="border: 1px solid #ddd; padding: 8px;"><div class="fiche-field">${data.employeur || ''}</div></td>
                            </tr>
                        </table>
                    </div>

                    <div class="contrat-article">
                        <h4>🏠 2. Coordonnées</h4>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px; width: 30%;"><strong>Adresse complète</strong></td>
                                <td style="border: 1px solid #ddd; padding: 8px;"><div class="fiche-field">${data.adresse}</div></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;"><strong>Ville / Commune</strong></td>
                                <td style="border: 1px solid #ddd; padding: 8px;"><div class="fiche-field">${data.ville}</div></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;"><strong>Téléphone mobile</strong></td>
                                <td style="border: 1px solid #ddd; padding: 8px;"><div class="fiche-field">${data.telephone_mobile}</div></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;"><strong>Adresse e-mail</strong></td>
                                <td style="border: 1px solid #ddd; padding: 8px;"><div class="fiche-field">${data.email}</div></td>
                            </tr>
                        </table>
                    </div>

                    <div class="contrat-article">
                        <h4>✍️ 7. Engagement du candidat</h4>
                        <p>Je soussigné(e) <strong>${data.prenom} ${data.nom}</strong>,</p>
                        <p>certifie sur l'honneur que les informations fournies sont exactes et sincères.</p>
                        <p>Je déclare avoir pris connaissance des <strong>conditions générales et financières</strong> du <strong>Cabinet Conseiller Expert (CCE)</strong> et les accepter sans réserve.</p>
                        
                        <div style="margin-top: 50px;">
                            <div style="float: left; width: 45%;">
                                <p>Fait à : <strong>${data.ville}</strong></p>
                                <p>Le : <strong>${new Date().toLocaleDateString('fr-FR')}</strong></p>
                            </div>
                            <div style="float: right; width: 45%; text-align: center;">
                                <p>Signature du candidat :</p>
                                <div style="margin-top: 60px; border-top: 1px solid #000; width: 80%; margin-left: auto; margin-right: auto;"></div>
                                <p><em>${data.prenom} ${data.nom}</em></p>
                            </div>
                            <div style="clear: both;"></div>
                        </div>
                    </div>

                    <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px; font-size: 0.9em;">
                        <p><strong>CABINET CONSEILLER EXPERT (CCE) - Cabinet de conseil spécialisé en immigration légale</strong></p>
                        <p>Abidjan, Plateau | Tél: +225 27 20 00 00 00 | Email: contact@cce.com</p>
                        <p>Fiche générée le ${new Date().toLocaleDateString('fr-FR')} à ${new Date().toLocaleTimeString('fr-FR')}</p>
                    </div>
                </div>
            `;
        }

        // ==================== JAVASCRIPT POUR CONTRATS AVEC BASE DE DONNÉES ====================
// Remplacer les fonctions localStorage dans contrats.blade.php par ces nouvelles fonctions

// Configuration AJAX avec CSRF token
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// ========== FONCTIONS DE GESTION DES CONTRATS ==========

/**
 * Charger tous les contrats depuis la base de données
 */
async function chargerContrats() {
    try {
        const response = await fetch('/crm/contrats/', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            return data.contracts || [];
        } else {
            console.error('Erreur:', data.message);
            return [];
        }
    } catch (error) {
        console.error('Erreur chargement contrats:', error);
        showToast('Erreur lors du chargement des contrats', 'error');
        return [];
    }
}

/**
 * Sauvegarder un nouveau contrat
 */
async function sauvegarderContrat(contratData) {
    try {
        const response = await fetch('/crm/contrats/', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(contratData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Contrat créé avec succès', 'success');
            return data.contract;
        } else {
            showToast(data.message || 'Erreur lors de la création du contrat', 'error');
            if (data.errors) {
                console.error('Erreurs de validation:', data.errors);
            }
            return null;
        }
    } catch (error) {
        console.error('Erreur sauvegarde contrat:', error);
        showToast('Erreur lors de la sauvegarde du contrat', 'error');
        return null;
    }
}

/**
 * Signer un contrat
 */
async function signerContratDB(contractId, signature, nomSignataire) {
    try {
        const response = await fetch(`/crm/contrats/${contractId}/sign`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                signature: signature,
                nom_signataire: nomSignataire
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Contrat signé avec succès', 'success');
            return data.contract;
        } else {
            showToast(data.message || 'Erreur lors de la signature', 'error');
            return null;
        }
    } catch (error) {
        console.error('Erreur signature contrat:', error);
        showToast('Erreur lors de la signature du contrat', 'error');
        return null;
    }
}

/**
 * Charger les statistiques
 */
async function chargerStatistiques() {
    try {
        const response = await fetch('/crm/contrats/stats/dashboard', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            return data.stats;
        } else {
            console.error('Erreur:', data.message);
            return null;
        }
    } catch (error) {
        console.error('Erreur chargement statistiques:', error);
        return null;
    }
}

/**
 * Charger le dashboard des contrats
 */
async function chargerDashboard() {
    const stats = await chargerStatistiques();
    const contrats = await chargerContrats();
    
    if (!stats) {
        document.getElementById('dashboard-stats').innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i>
                Erreur lors du chargement des statistiques
            </div>
        `;
        return;
    }
    
    // Afficher les statistiques
    document.getElementById('dashboard-stats').innerHTML = `
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card-contrat" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h3 class="stat-number-contrat">${stats.total}</h3>
                    <p class="mb-0">Total Contrats</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card-contrat" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <h3 class="stat-number-contrat">${stats.signed}</h3>
                    <p class="mb-0">Contrats Signés</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card-contrat" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <h3 class="stat-number-contrat">${stats.pending}</h3>
                    <p class="mb-0">En Attente</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card-contrat" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <h3 class="stat-number-contrat">${stats.signature_rate}%</h3>
                    <p class="mb-0">Taux de Signature</p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">
                    <strong><i class="bi bi-info-circle"></i> Statistiques :</strong>
                    ${stats.this_month} contrats ce mois | 
                    ${stats.this_year} contrats cette année | 
                    Montant total : ${new Intl.NumberFormat('fr-FR').format(stats.total_amount)} FCFA
                </div>
            </div>
        </div>
    `;
    
    // Afficher les contrats récents
    afficherContratsRecents(contrats);
}

/**
 * Afficher les contrats récents
 */
function afficherContratsRecents(contrats) {
    const container = document.getElementById('contrats-list');
    
    if (!contrats || contrats.length === 0) {
        container.innerHTML = `
            <div class="alert alert-warning">
                <i class="bi bi-inbox"></i>
                Aucun contrat pour le moment
            </div>
        `;
        return;
    }
    
    let html = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>N° Contrat</th>
                        <th>Client</th>
                        <th>Type Visa</th>
                        <th>Pays</th>
                        <th>Montant</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    contrats.forEach(contrat => {
        const statusClass = contrat.statut === 'Signé' ? 'success' : 
                           contrat.statut === 'En attente' ? 'warning' : 'danger';
        const statusIcon = contrat.statut === 'Signé' ? '✅' : 
                          contrat.statut === 'En attente' ? '⏳' : '❌';
        
        html += `
            <tr>
                <td><strong>${contrat.numero_contrat}</strong></td>
                <td>${contrat.prenom} ${contrat.nom}</td>
                <td>${contrat.type_visa}</td>
                <td>${contrat.pays_destination}</td>
                <td>${new Intl.NumberFormat('fr-FR').format(contrat.montant_contrat)} F</td>
                <td>${new Date(contrat.date_contrat).toLocaleDateString('fr-FR')}</td>
                <td><span class="badge bg-${statusClass}">${statusIcon} ${contrat.statut}</span></td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="voirContrat(${contrat.id})">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-primary" onclick="telechargerPDFContrat(${contrat.id})">
                        <i class="bi bi-download"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="supprimerContrat(${contrat.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = html;
}

/**
 * Fonction Toast pour les notifications
 */
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show`;
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    toastContainer.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 5000);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999;';
    document.body.appendChild(container);
    return container;
}

// ========== REMPLACER LA FONCTION genererContrat ORIGINALE ==========
async function genererContrat() {
    const form = document.getElementById('form-contrat');
    const formData = new FormData(form);
    
    // Convertir FormData en objet
    const contratData = {
        nom: formData.get('nom'),
        prenom: formData.get('prenom'),
        date_naissance: formData.get('date_naissance'),
        lieu_naissance: formData.get('lieu_naissance') || '',
        nationalite: formData.get('nationalite'),
        sexe: formData.get('sexe'),
        etat_civil: formData.get('etat_civil'),
        profession: formData.get('profession'),
        employeur: formData.get('employeur') || '',
        adresse: formData.get('adresse'),
        ville: formData.get('ville'),
        telephone_mobile: formData.get('telephone_mobile'),
        telephone_fixe: formData.get('telephone_fixe') || '',
        email: formData.get('email'),
        type_visa: formData.get('type_visa'),
        pays_destination: formData.get('pays_destination'),
        montant_contrat: parseFloat(formData.get('montant_contrat')),
        montant_lettres: formData.get('montant_lettres'),
        date_echeance: formData.get('date_echeance') || null,
        mode_paiement: formData.get('mode_paiement') || '',
        conseiller: formData.get('conseiller') || '',
        lieu_contrat: formData.get('lieu_contrat') || 'Abidjan',
        date_contrat: formData.get('date_contrat'),
        statut: 'En attente'
    };
    
    // Sauvegarder dans la base de données
    const savedContract = await sauvegarderContrat(contratData);
    
    if (savedContract) {
        // Stocker l'ID du contrat pour la signature
        window.currentContractId = savedContract.id;
        
        // Afficher la prévisualisation
        afficherContrat(savedContract);
    }
}

// ========== REMPLACER LA FONCTION signerContrat ORIGINALE ==========
async function signerContrat() {
    const acceptation = document.getElementById('acceptation');
    const nomSignataire = document.getElementById('nom-signataire').value;
    
    if (!acceptation.checked) {
        alert('Vous devez accepter les termes du contrat');
        return;
    }
    
    if (!nomSignataire) {
        alert('Veuillez saisir votre nom complet');
        return;
    }
    
    if (signaturePad.isEmpty()) {
        alert('Veuillez signer le document');
        return;
    }
    
    const signature = signaturePad.toDataURL();
    
    // Signer le contrat dans la base de données
    const signedContract = await signerContratDB(window.currentContractId, signature, nomSignataire);
    
    if (signedContract) {
        alert('✅ Contrat signé avec succès !');
        showSection('dashboard');
        await chargerDashboard();
    }
}

// ========== INITIALISATION AU CHARGEMENT ==========
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('contrats-panel')) {
        chargerDashboard();
    }
});
    </script>
</body>
</html>