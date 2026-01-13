<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PSI Africa CRM - Système de Gestion Intégré</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    
    <style>
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
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #f8fafc 100%);
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.6;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1.5rem;
        }

        /* Header */
        .app-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
            flex-wrap: wrap;
            gap: 1rem;
        }

        .app-title {
            font-size: clamp(1.5rem, 4vw, 2rem);
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .user-avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        /* Navigation */
        .nav-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            overflow-x: auto;
            padding-bottom: 0.5rem;
        }

        .nav-tab {
            background: var(--bg-secondary);
            color: var(--text-secondary);
            border: 1px solid var(--border);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            white-space: nowrap;
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-tab:hover {
            background: var(--bg-tertiary);
            color: var(--text-primary);
            transform: translateY(-1px);
        }

        .nav-tab.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary-dark);
        }

        /* Panels */
        .panel {
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            min-height: 500px;
            position: relative;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .panel.hidden {
            display: none;
        }

        /* Cards */
        .card {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .card h3 {
            color: var(--text-primary);
            margin-bottom: 1rem;
            font-size: 1.125rem;
            font-weight: 600;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1.25rem;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, var(--primary), var(--secondary));
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.875rem;
            text-transform: uppercase;
        }

        /* Forms */
        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: 6px;
            color: var(--text-primary);
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--bg-secondary);
            color: var(--text-primary);
            border: 1px solid var(--border);
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .btn-success {
            background: var(--success);
            border-color: var(--success);
            color: white;
        }

        .btn-warning {
            background: var(--warning);
            border-color: var(--warning);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            border-color: var(--danger);
            color: white;
        }

        .btn-info {
            background: var(--info);
            border-color: var(--info);
            color: white;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.8125rem;
        }

        /* Tables */
        .table-container {
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .table th {
            background: var(--bg-secondary);
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .table tbody tr:hover {
            background: rgba(37, 99, 235, 0.05);
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            font-size: 0.6875rem;
            font-weight: 600;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .badge-success { background: rgba(22, 163, 74, 0.1); color: var(--success); border: 1px solid var(--success); }
        .badge-warning { background: rgba(245, 158, 11, 0.1); color: var(--warning); border: 1px solid var(--warning); }
        .badge-danger { background: rgba(239, 68, 68, 0.1); color: var(--danger); border: 1px solid var(--danger); }
        .badge-info { background: rgba(6, 182, 212, 0.1); color: var(--info); border: 1px solid var(--info); }

        /* Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1002;
            padding: 1rem;
        }

        .modal-content {
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px -12px var(--shadow);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .close-btn {
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.25rem;
            line-height: 1;
        }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-left: 4px solid var(--primary);
            border-radius: 8px;
            padding: 1rem 1.25rem;
            color: var(--text-primary);
            box-shadow: 0 10px 40px var(--shadow);
            transform: translateX(400px);
            transition: all 0.3s ease;
            z-index: 1001;
            max-width: 400px;
        }

        .toast.show { transform: translateX(0); }
        .toast.success { border-left-color: var(--success); }
        .toast.error { border-left-color: var(--danger); }

        /* Utilities */
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .justify-center { justify-content: center; }
        .gap-1 { gap: 0.25rem; }
        .gap-2 { gap: 0.5rem; }
        .gap-3 { gap: 0.75rem; }
        .gap-4 { gap: 1rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mt-4 { margin-top: 1rem; }
        .text-center { text-align: center; }
        .text-sm { font-size: 0.875rem; }
        .text-xs { font-size: 0.75rem; }
        .font-semibold { font-weight: 600; }
        .font-bold { font-weight: 700; }
        .text-primary { color: var(--text-primary); }
        .text-secondary { color: var(--text-secondary); }
        .text-muted { color: var(--text-muted); }
        .text-success { color: var(--success); }
        .text-warning { color: var(--warning); }
        .text-danger { color: var(--danger); }
        .text-info { color: var(--info); }
        .hidden { display: none !important; }
        .w-full { width: 100%; }
        .grid { display: grid; }
        .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-cols-3 { grid-template-columns: repeat(3, 1fr); }

        /* Search */
        .search-container {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            min-width: 250px;
        }

        @media (max-width: 768px) {
            .container { padding: 1rem; }
            .stats-grid { grid-template-columns: 1fr; }
            .form-grid { grid-template-columns: 1fr; }
            .search-container { flex-direction: column; }
        }
        /* Admin Styles */
.admin-section {
    background: var(--bg-secondary);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.admin-section h4 {
    color: var(--text-primary);
    margin-bottom: 1rem;
    font-size: 1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.user-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.user-card {
    background: var(--bg-primary);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 1rem;
}

.user-card-header {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.user-card-info {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 0.25rem;
}

.permissions-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem;
    margin: 0.5rem 0;
}

.permission-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    border-radius: 4px;
    border: 1px solid var(--info);
    color: var(--info);
    text-transform: uppercase;
}

.user-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.75rem;
    flex-wrap: wrap;
}

.data-management-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
}

.data-card {
    background: var(--bg-primary);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 1rem;
}

.data-card h5 {
    font-weight: 600;
    margin-bottom: 0.5rem;
}
.badge-purple { 
    background: rgba(139, 92, 246, 0.1); 
    color: #8b5cf6; 
    border: 1px solid #8b5cf6; 
}
.badge-orange { 
    background: rgba(249, 115, 22, 0.1); 
    color: #f97316; 
    border: 1px solid #f97316; 
}

/* Styles pour les badges de relance */
.badge-success { 
    background: rgba(22, 163, 74, 0.1); 
    color: var(--success); 
    border: 1px solid var(--success); 
}
.badge-warning { 
    background: rgba(245, 158, 11, 0.1); 
    color: var(--warning); 
    border: 1px solid var(--warning); 
}
.badge-danger { 
    background: rgba(239, 68, 68, 0.1); 
    color: var(--danger); 
    border: 1px solid var(--danger); 
}
.badge-info { 
    background: rgba(6, 182, 212, 0.1); 
    color: var(--info); 
    border: 1px solid var(--info); 
}

/* Notification Badge pour Relances */
.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ef4444;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: bold;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
}

.alert-relance {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border-left: 4px solid #f59e0b;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
    from { transform: translateX(-20px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

.alert-relance-urgent {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    border-left: 4px solid #ef4444;
}
.canal-btn {
    transition: all 0.2s ease;
}

.canal-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.canal-btn.active {
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

#relanceCommentaire:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    outline: none;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

#selectedClientInfo {
    animation: slideInRight 0.3s ease-out;
}

.badge-purple { 
    background: rgba(139, 92, 246, 0.1); 
    color: #8b5cf6; 
    border: 1px solid #8b5cf6; 
}

.badge-orange { 
    background: rgba(249, 115, 22, 0.1); 
    color: #f97316; 
    border: 1px solid #f97316; 
}

/* Styles pour les badges de relance */
.badge-success { 
    background: rgba(22, 163, 74, 0.1); 
    color: var(--success); 
    border: 1px solid var(--success); 
}

.badge-warning { 
    background: rgba(245, 158, 11, 0.1); 
    color: var(--warning); 
    border: 1px solid var(--warning); 
}

.badge-danger { 
    background: rgba(239, 68, 68, 0.1); 
    color: var(--danger); 
    border: 1px solid var(--danger); 
}

.badge-info { 
    background: rgba(6, 182, 212, 0.1); 
    color: var(--info); 
    border: 1px solid var(--info); 
}
/* Couleurs de base */
.badge-orange { background: rgba(249, 115, 22, 0.15); color: #ea580c; border: 1px solid #f97316; }
.badge-cyan { background: rgba(6, 182, 212, 0.15); color: #0891b2; border: 1px solid #06b6d4; }
.badge-purple { background: rgba(139, 92, 246, 0.15); color: #7c3aed; border: 1px solid #8b5cf6; }
.badge-red { background: rgba(239, 68, 68, 0.15); color: #dc2626; border: 1px solid #ef4444; }

/* Couleurs PHASE 2 - Engagement */
.badge-green { background: rgba(34, 197, 94, 0.15); color: #16a34a; border: 1px solid #22c55e; }
.badge-emerald { background: rgba(16, 185, 129, 0.15); color: #059669; border: 1px solid #10b981; }
.badge-amber { background: rgba(251, 191, 36, 0.15); color: #d97706; border: 1px solid #fbbf24; }
.badge-lime { background: rgba(163, 230, 53, 0.15); color: #65a30d; border: 1px solid #a3e635; }
.badge-sky { background: rgba(14, 165, 233, 0.15); color: #0369a1; border: 1px solid #0ea5e9; }
.badge-red-dark { background: rgba(185, 28, 28, 0.15); color: #991b1b; border: 1px solid #b91c1c; }

/* Couleurs PHASE 3 - Visa */
.badge-indigo { background: rgba(99, 102, 241, 0.15); color: #4f46e5; border: 1px solid #6366f1; }
.badge-yellow { background: rgba(234, 179, 8, 0.15); color: #a16207; border: 1px solid #eab308; }
.badge-green-bright { background: rgba(74, 222, 128, 0.15); color: #15803d; border: 1px solid #4ade80; }
.badge-red-bright { background: rgba(248, 113, 113, 0.15); color: #b91c1c; border: 1px solid #f87171; }
.badge-teal { background: rgba(20, 184, 166, 0.15); color: #0f766e; border: 1px solid #14b8a6; }

/* Couleurs PHASE 4 - Voyage */
.badge-blue { background: rgba(59, 130, 246, 0.15); color: #1d4ed8; border: 1px solid #3b82f6; }
.badge-green-dark { background: rgba(21, 128, 61, 0.15); color: #14532d; border: 1px solid #15803d; }
.badge-gray { background: rgba(107, 114, 128, 0.15); color: #374151; border: 1px solid #6b7280; }

/* Couleurs PHASE 5 - Relance Spéciale */
.badge-orange-dark { background: rgba(194, 65, 12, 0.15); color: #7c2d12; border: 1px solid #c2410c; }
.badge-pink { background: rgba(236, 72, 153, 0.15); color: #be185d; border: 1px solid #ec4899; }
.badge-danger-flash { 
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); 
    color: #991b1b; 
    border: 2px solid #dc2626;
    font-weight: 700;
    animation: pulse-danger 2s infinite;
}
.badge-success-gold { 
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); 
    color: #15803d; 
    border: 2px solid #16a34a;
    font-weight: 700;
}

/* Badge par défaut */
.badge-default { background: rgba(148, 163, 184, 0.15); color: #475569; border: 1px solid #94a3b8; }

/* Animation danger */
@keyframes pulse-danger {
    0%, 100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.4); }
    50% { box-shadow: 0 0 0 8px rgba(220, 38, 38, 0); }
}

/* Search Container - Alignement Horizontal */
.search-container {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
    align-items: center;
}

@media (max-width: 1200px) {
    .search-container {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 768px) {
    .search-container {
        grid-template-columns: 1fr;
    }
}

.search-input {
    width: 100%;
}

/* S'assurer que les selects ont la même hauteur */
.search-container .form-control {
    height: 42px;
}

/* Badge de statut cliquable */
.status-badge-clickable {
    transition: all 0.2s ease;
    position: relative;
}

.status-badge-clickable:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    filter: brightness(1.1);
}

.status-badge-clickable:active {
    transform: translateY(0);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.status-badge-clickable::after {
    content: '✎';
    position: absolute;
    right: -12px;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0;
    font-size: 0.7rem;
    transition: all 0.2s ease;
}

.status-badge-clickable:hover::after {
    opacity: 0.6;
    right: -16px;
}

/* Animation d'ouverture du modal */
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

/* Dropdown de recherche des clients */
.client-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    margin-top: 4px;
    z-index: 1000;
    max-height: 400px;
    overflow: hidden;
}

.client-dropdown-header {
    padding: 8px 12px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 500;
}

.client-dropdown-list {
    max-height: 350px;
    overflow-y: auto;
}

.client-dropdown-item {
    padding: 10px 12px;
    cursor: pointer;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.2s ease;
}

.client-dropdown-item:hover {
    background: #f1f5f9;
}

.client-dropdown-item:last-child {
    border-bottom: none;
}

.client-dropdown-item-name {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 4px;
}

.client-dropdown-item-details {
    font-size: 0.85rem;
    color: #64748b;
    display: flex;
    gap: 12px;
}

.client-dropdown-empty {
    padding: 20px;
    text-align: center;
    color: #94a3b8;
}

/* ==================== SCROLLBARS PERSONNALISÉES ==================== */
/* Pour les sections avec scroll indépendant */
.table-container::-webkit-scrollbar,
#clientsARelancerList::-webkit-scrollbar {
    width: 10px;
    height: 10px;
}

.table-container::-webkit-scrollbar-track,
#clientsARelancerList::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.table-container::-webkit-scrollbar-thumb,
#clientsARelancerList::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 10px;
    transition: background 0.3s ease;
}

.table-container::-webkit-scrollbar-thumb:hover,
#clientsARelancerList::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, var(--primary-dark), var(--primary));
}

/* Pour Firefox */
.table-container,
#clientsARelancerList {
    scrollbar-width: thin;
    scrollbar-color: var(--primary) #f1f5f9;
}

/* Animation smooth scroll */
.table-container,
#clientsARelancerList {
    scroll-behavior: smooth;
}

    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="app-header">
            <div class="flex items-center gap-4">
                <img src="{{ asset('img/logo png png.png') }}" alt="PSI Africa" style="height: 80px; width: auto;">
                <div>
                    <h1 class="app-title">PSI Africa CRM</h1>
                    <p class="text-secondary">Système de Gestion Intégré</p>
                </div>
            </div>
            
            <div class="user-info">
                <div class="text-sm">
                    <div class="font-semibold">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-secondary">{{ Auth::user()->getRoleNames()->first() ?? 'USER' }}</div>
                </div>
                <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger">Déconnexion</button>
                </form>
            </div>
        </header>

        <!-- Navigation -->
        <nav class="nav-tabs">
        <button class="nav-tab active" data-panel="dashboard">🏠 Dashboard</button>
        <button class="nav-tab" data-panel="clients">👥 Clients</button>
        <button class="nav-tab" data-panel="invoicing">💰 Facturation</button>
        <button class="nav-tab" data-panel="recovery">📞 Recouvrement</button>
        <button class="nav-tab" data-panel="relances" style="position: relative;">
            🔔 Relances
            <span class="notification-badge hidden" id="relancesBadge">0</span>
        </button>
        <button class="nav-tab" data-panel="contrats">📄 Contrats</button>
        <button class="nav-tab" data-panel="calendrier">📅 Calendrier</button>
        <button class="nav-tab" data-panel="performance">📈 Performance</button>
        <button class="nav-tab" data-panel="analytics">📊 Analytics</button>
        <button class="nav-tab" data-panel="admin">⚙️ Administration</button>
    </nav>

        <!-- Dashboard Panel -->
        <section class="panel" id="dashboard-panel">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-bold text-xl">📊 Vue d'ensemble</h2>
                <div class="text-sm text-secondary" id="dashboardDate"></div>
            </div>

    <div class="stats-grid" id="kpiStats"></div>
    
    <!-- AJOUTER CES DEUX CARTES ICI -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
        <!-- Graphique Évolution du CA -->
        <div class="card">
            <h3>📈 Évolution du CA</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Graphique Statuts Clients -->
        <div class="card">
            <h3>👥 Statuts Clients</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="clientsStatusChart"></canvas>
            </div>
        </div>
    </div>

    @if(Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Admin'))
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0;">🎯 Activités Récentes</h3>
            <a href="{{ route('crm.activities.view') }}" class="btn btn-primary btn-sm">
                📋 Voir tout l'historique
            </a>
        </div>
        <div id="recentActivity"></div>
    </div>
    @endif
</section>

        <!-- Clients Panel -->
        <section class="panel hidden" id="clients-panel">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-bold text-xl">👥 Gestion des Clients</h2>
                <div class="flex gap-2">
                    <button class="btn btn-success" onclick="exportClients()">📊 Exporter</button>
                    <button class="btn btn-primary" onclick="showAddClientModal()">➕ Nouveau Client</button>
                </div>
            </div>

            <div class="search-container">
                <input type="text" class="form-control search-input" placeholder="🔍 Rechercher un client..." id="clientSearch">
                <select class="form-control" id="statusFilter" onchange="applyFilters()">
                    <option value="">Tous les statuts</option>
                    <option value="Lead">Lead</option>
                    <option value="Prospect">Prospect</option>
                    <option value="Opportunité">Opportunité</option>
                    <option value="Négociation">Négociation</option>
                    <option value="Converti">Converti</option>
                    <option value="Perdu">Perdu</option>
                </select>
                <select class="form-control" id="prestationFilter" onchange="applyFilters()">
                    <option value="">Toutes prestations</option>
                    <option value="Profil Visa">Profil Visa</option>
                    <option value="Inscription">Inscription</option>
                    <option value="Assistance">Assistance</option>
                    <option value="Documents de Voyage">Documents de Voyage</option>
                    <option value="Réservation d'hôtel">Réservation d'hôtel</option>
                    <option value="Billet d'avion">Billet d'avion</option>
                    <option value="Assurance">Assurance</option>
                    <option value="Circuit touristique">Circuit touristique</option>
                </select>
                </div>

            <div class="card">
                <div class="table-container" style="max-height: 600px; overflow-y: auto; overflow-x: auto;">
                    <table class="table">
                        <thead style="position: sticky; top: 0; background: white; z-index: 10; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <tr>
                                <th>UID</th>
                                <th>NOM</th>
                                <th>CONTACT</th>
                                <th>PRESTATION</th>
                                <th>MONTANT FACTURE</th>
                                <th>STATUT</th>
                                <th>AGENT</th>
                                <th>STATUT RELANCE</th> <!-- ✅ NOUVELLE COLONNE -->
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody id="clientsTableBody"></tbody>
                    </table>
                </div>

                <!-- Informations de pagination -->
                <div id="clientsPaginationInfo" style="margin-top: 10px;"></div>
            </div>
        </section>

        <!-- Invoicing Panel -->
        <section class="panel hidden" id="invoicing-panel">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-bold text-xl">💰 Gestion de la Facturation</h2>
                <button class="btn btn-primary" onclick="showCreateInvoiceModal()">➕ Nouvelle Facture</button>
            </div>

            <div class="stats-grid mb-6">
                <div class="stat-card">
                    <div class="stat-value text-primary" id="totalInvoices">0</div>
                    <div class="stat-label">Factures Total</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value text-success" id="paidInvoices">0</div>
                    <div class="stat-label">Factures Payées</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value text-warning" id="pendingInvoices">0</div>
                    <div class="stat-label">En Attente</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value text-danger" id="overdueInvoices">0</div>
                    <div class="stat-label">En Retard</div>
                </div>
            </div>

            <div class="card">
                <h3>📄 Liste des Factures</h3>
                
                <!-- ✅ AJOUT DES FILTRES DE RECHERCHE -->
                <div class="flex gap-3 mb-4 flex-wrap">
                    <input type="text" 
                           class="form-control flex-1" 
                           id="invoiceSearch" 
                           placeholder="🔍 Rechercher par N°, client, service, agent..." 
                           style="min-width: 250px;">
                    
                    <select class="form-control" id="invoiceStatusFilter" style="min-width: 150px;">
                        <option value="">Tous les statuts</option>
                        <option value="pending">En attente</option>
                        <option value="partial">Partiel</option>
                        <option value="paid">Payé</option>
                        <option value="overdue">En retard</option>
                    </select>
                    
                    <input type="date" 
                           class="form-control" 
                           id="invoiceDateFrom" 
                           placeholder="Date de début"
                           style="min-width: 150px;">
                    
                    <input type="date" 
                           class="form-control" 
                           id="invoiceDateTo" 
                           placeholder="Date de fin"
                           style="min-width: 150px;">
                    
                    <button class="btn btn-primary" onclick="searchInvoices()">🔍 Rechercher</button>
                    <button class="btn btn-secondary" onclick="resetInvoiceFilters()">🔄 Réinitialiser</button>
                </div>
                
                <div class="table-container" style="max-height: 600px; overflow-y: auto; overflow-x: auto;">
                    <table class="table">
                        <thead style="position: sticky; top: 0; background: white; z-index: 10; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <tr>
                                <th>N° FACTURE</th>
                                <th>CLIENT</th>
                                <th>SERVICE</th>
                                <th>MONTANT</th>
                                <th>PAYÉ</th>
                                <th>RESTANT</th>
                                <th>STATUT</th>
                                <th>ÉCHÉANCE</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody id="invoicesTableBody"></tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Recovery Panel -->
        <section class="panel hidden" id="recovery-panel">
            <h2 class="font-bold text-xl mb-6">📞 Processus de Recouvrement</h2>
            <div id="recoveryContent"></div>
        </section>

        <!-- Performance Panel -->
        <section class="panel hidden" id="performance-panel">
            <h2 class="font-bold text-xl mb-6">📈 Tableau de Performance</h2>
            <div id="performanceContent"></div>
        </section>

        <!-- Relances Panel -->
<section class="panel hidden" id="relances-panel">
    <div class="flex items-center justify-between mb-6">
        <h2 class="font-bold text-xl">📞 Gestion des Relances</h2>
        <button class="btn btn-primary" onclick="showAddRelanceModal()">➕ Nouvelle Relance</button>
    </div>

    <!-- Stats Relances -->
    <div class="stats-grid mb-6" id="relancesStats"></div>

    <!-- Historique des relances -->
    <div class="card mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3>📋 Historique des Relances</h3>
            <div class="flex gap-2">
                <select class="form-control" id="relanceStatusFilter" onchange="loadRelances()">
                    <option value="">Tous les statuts</option>
                    <option value="En cours">En cours</option>
                    <option value="Clôturé">Clôturé</option>
                </select>
                <input type="text" class="form-control" id="relanceSearch"
                       placeholder="🔍 Rechercher..." onkeyup="searchRelances()">
            </div>
        </div>
        <div class="table-container" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
            <table class="table">
                <thead style="position: sticky; top: 0; background: white; z-index: 10; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <tr>
                        <th>CLIENT</th>
                        <th>AGENT</th>
                        <th>DATE/HEURE</th>
                        <th>COMMENTAIRE</th>
                        <th>STATUT</th>
                        <th>PROCHAINE RELANCE</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody id="relancesTableBody"></tbody>
            </table>
        </div>
    </div>

    <!-- Clients à relancer -->
    <div class="card">
        <h3>🎯 Clients à Relancer cette Semaine</h3>
        <div id="clientsARelancerList" style="max-height: 500px; overflow-y: auto; overflow-x: auto;"></div>
    </div>
</section>

        <!-- Analytics Panel -->
        <section class="panel hidden" id="analytics-panel">
            <h2 class="font-bold text-xl mb-6">📊 Analytics Avancées</h2>
            <div id="analyticsContent"></div>
        </section>

        <!-- Admin Panel -->
        <section class="panel hidden" id="admin-panel">
            <h2 class="font-bold text-xl mb-6">⚙️ Administration Système</h2>
            <div id="adminContent"></div>
        </section>
    </div>

    <section class="panel hidden" id="contrats-panel">
       @include('crm.contrats')
   </section>

    <!-- Calendrier Panel -->
    <section class="panel hidden" id="calendrier-panel">
        <iframe src="{{ route('crm.calendrier') }}" style="width: 100%; height: 90vh; border: none; border-radius: 8px;"></iframe>
    </section>

    <!-- Modal Client -->
    <div class="modal-overlay hidden" id="clientModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Ajouter un Client</h3>
                <button class="close-btn" onclick="closeClientModal()">×</button>
            </div>
            <form id="clientForm" class="form-grid" onsubmit="saveClient(event)">
                <div class="form-group">
                    <label class="form-label">Nom *</label>
                    <input type="text" name="nom" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Prénoms</label>
                    <input type="text" name="prenoms" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Contact *</label>
                    <input type="text" name="contact" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="form-group">
                    <div class="form-group">
                    <label class="form-label">Catégorie *</label>
                    <select name="categorie" id="clientCategorie" class="form-control" required onchange="updatePrestationOptions()">
                        <option value="">Sélectionner une catégorie</option>
                        <option value="Frais du Cabinet">Frais du Cabinet</option>
                        <option value="Documents de Voyage">Documents de Voyage</option>
                        <option value="Autres">Autres</option>
                    </select>
                </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Prestation *</label>
                    <select name="prestation" id="clientPrestation" class="form-control" required disabled>
                        <option value="">Sélectionner d'abord une catégorie</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Montant Facture (FCFA)</label>
                    <input type="number" name="budget" class="form-control" value="0">
                </div>
                <div class="form-group">
    <label class="form-label">Statut *</label>
    <select name="statut" class="form-control" required>
        <optgroup label="🔵 PHASE 1 - DÉCOUVERTE">
            <option value="Lead" selected>Lead</option>
            <option value="Prospect">Prospect</option>
            <option value="À convertir">À convertir</option>
            <option value="Perdu">Perdu</option>
        </optgroup>
        
        <optgroup label="🟢 PHASE 2 - ENGAGEMENT">

        <option value="En attente de paiement des frais de profil visa et d'inscription">
                En attente frais profil visa (115.000 F)
            </option>
            <option value="Profil visa payé">Profil visa payé</option>
            
            <option value="En attente de paiement des frais de cabinet">
                En attente frais de cabinet (500.000 F)
            </option>
            <option value="Frais d'assistance payés">Frais d'assistance payés</option>
            <option value="En attente de documents">En attente de documents</option>
            <option value="Documents validés">Documents validés</option>
            <option value="Rendez-vous au bureau PSI">Rendez-vous au bureau PSI</option>
            <option value="Rendez-vous d'urgence">Rendez-vous d'urgence</option>
        </optgroup>
        
        <optgroup label="🟡 PHASE 3 - VISA">
            <option value="Prise de RDV ambassade confirmée">Prise de RDV ambassade</option>
            <option value="En attente de décision visa">En attente décision visa</option>
            <option value="Visa accepté">Visa accepté</option>
            <option value="Visa refusé">Visa refusé</option>
            <option value="Visa validé">Visa validé</option>
        </optgroup>
        
        <optgroup label="🟣 PHASE 4 - VOYAGE">
            <option value="Billet d'avion payé">Billet d'avion payé</option>
            <option value="Départ confirmé">Départ confirmé</option>
            <option value="En suivi post-départ">En suivi post-départ</option>
        </optgroup>
        
        <optgroup label="🔴 PHASE 5 - RELANCE SPÉCIALE">
            <option value="Négociation">Négociation</option>
            <option value="Message d'urgence">Message d'urgence</option>
            <option value="Opportunité">Opportunité</option>
            <option value="Converti">Converti</option>
        </optgroup>

        <optgroup label="⚪ AUTRE">
            <option value="Autre">Autre (Personnalisé)</option>
        </optgroup>
    </select>
                </div>

                <!-- Champ personnalisé pour "Autre" statut -->
                <div class="form-group" id="addAutreStatutField" style="display: none;">
                    <label class="form-label">Précisez le statut personnalisé *</label>
                    <input type="text" name="statut_autre" id="addStatutAutre" class="form-control"
                           placeholder="Entrez le statut personnalisé...">
                    <small class="text-muted">Ce champ est obligatoire si vous sélectionnez "Autre"</small>
                </div>
                <div class="form-group">
                    <label class="form-label">Média</label>
                    <select name="media" class="form-control">
                        <option>Facebook</option>
                        <option>WhatsApp</option>
                        <option>Instagram</option>
                        <option>Site PSI Africa</option>
                        <option>Réferencement</option>
                        <option>B2B</option>
                    </select>
                </div>
            </form>
            <div class="form-group">
                <label class="form-label">Commentaire</label>
                <textarea name="commentaire" rows="3" class="form-control" form="clientForm"></textarea>
            </div>
            <div class="flex gap-2 mt-4">
                <button type="submit" form="clientForm" class="btn btn-success w-full">💾 Enregistrer</button>
                <button type="button" class="btn w-full" onclick="closeClientModal()">✖ Fermer</button>
            </div>
        </div>
    </div>

    <!-- Modal Edit Client -->
<div class="modal-overlay hidden" id="editClientModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">✏️ Modifier le Client</h3>
            <button class="close-btn" onclick="closeEditClientModal()">×</button>
        </div>
        <form id="editClientForm" class="form-grid" onsubmit="updateClient(event)">
            <input type="hidden" id="editClientId" name="client_id">
            
            <div class="form-group">
                <label class="form-label">Nom *</label>
                <input type="text" id="editClientNom" name="nom" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Prénoms</label>
                <input type="text" id="editClientPrenoms" name="prenoms" class="form-control">
            </div>
            
            <div class="form-group">
                <label class="form-label">Contact *</label>
                <input type="text" id="editClientContact" name="contact" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" id="editClientEmail" name="email" class="form-control">
            </div>
            
            <div class="form-group">
                <label class="form-label">Catégorie *</label>
                <select name="categorie" id="editClientCategorie" class="form-control" required onchange="updateEditPrestationOptions()">
                    <option value="">Sélectionner une catégorie</option>
                    <option value="Frais du Cabinet">Frais du Cabinet</option>
                    <option value="Documents de Voyage">Documents de Voyage</option>
                    <option value="Autres">Autres</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Prestation *</label>
                <select name="prestation" id="editClientPrestation" class="form-control" required>
                    <option value="">Sélectionner d'abord une catégorie</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Montant Facture (FCFA)</label>
                <input type="number" id="editClientBudget" name="budget" class="form-control" value="0">
            </div>
            
            <div class="form-group">
                <label class="form-label">Statut *</label>
                <select name="statut" id="editClientStatut" class="form-control" required>
                    <optgroup label="🔵 PHASE 1 - DÉCOUVERTE">
                        <option value="Lead">Lead</option>
                        <option value="Prospect">Prospect</option>
                        <option value="À convertir">À convertir</option>
                        <option value="Perdu">Perdu</option>
                    </optgroup>
                    <optgroup label="🟢 PHASE 2 - ENGAGEMENT">
                        <option value="En attente de paiement des frais de profil visa et d'inscription">En attente frais profil visa (115.000 F)</option>
                        <option value="Profil visa payé">Profil visa payé</option>
                        <option value="En attente de paiement des frais de cabinet">En attente frais de cabinet (500.000 F)</option>
                        <option value="Frais d'assistance payés">Frais d'assistance payés</option>
                        <option value="En attente de documents">En attente de documents</option>
                        <option value="Documents validés">Documents validés</option>
                        <option value="Rendez-vous au bureau PSI">Rendez-vous au bureau PSI</option>
                        <option value="Rendez-vous d'urgence">Rendez-vous d'urgence</option>
                    </optgroup>
                    <optgroup label="🟡 PHASE 3 - VISA">
                        <option value="Prise de RDV ambassade confirmée">Prise de RDV ambassade</option>
                        <option value="En attente de décision visa">En attente décision visa</option>
                        <option value="Visa accepté">Visa accepté</option>
                        <option value="Visa refusé">Visa refusé</option>
                        <option value="Visa validé">Visa validé</option>
                    </optgroup>
                    <optgroup label="🟣 PHASE 4 - VOYAGE">
                        <option value="Billet d'avion payé">Billet d'avion payé</option>
                        <option value="Départ confirmé">Départ confirmé</option>
                        <option value="En suivi post-départ">En suivi post-départ</option>
                    </optgroup>
                    <optgroup label="🔴 PHASE 5 - RELANCE SPÉCIALE">
                        <option value="Négociation">Négociation</option>
                        <option value="Message d'urgence">Message d'urgence</option>
                        <option value="Opportunité">Opportunité</option>
                        <option value="Converti">Converti</option>
                    </optgroup>

                    <optgroup label="⚪ AUTRE">
                        <option value="Autre">Autre (Personnalisé)</option>
                    </optgroup>
                </select>
            </div>

            <!-- Champ personnalisé pour "Autre" statut dans modal édition -->
            <div class="form-group" id="editAutreStatutField" style="display: none;">
                <label class="form-label">Précisez le statut personnalisé *</label>
                <input type="text" name="statut_autre" id="editStatutAutre" class="form-control"
                       placeholder="Entrez le statut personnalisé...">
                <small class="text-muted">Ce champ est obligatoire si vous sélectionnez "Autre"</small>
            </div>

            <div class="form-group">
                <label class="form-label">Média</label>
                <select name="media" id="editClientMedia" class="form-control">
                    <option>Facebook</option>
                    <option>WhatsApp</option>
                    <option>Instagram</option>
                    <option>Site PSI Africa</option>
                    <option>Référencement</option>
                    <option>B2B</option>
                </select>
            </div>
        </form>
        
        <div class="form-group">
            <label class="form-label">Commentaire</label>
            <textarea id="editClientCommentaire" name="commentaire" rows="3" class="form-control" form="editClientForm"></textarea>
        </div>
        
        <div class="flex gap-2 mt-4">
            <button type="submit" form="editClientForm" class="btn btn-success w-full">💾 Mettre à jour</button>
            <button type="button" class="btn w-full" onclick="closeEditClientModal()">✖ Annuler</button>
        </div>
    </div>
</div>

<!-- Modal Edit Payment -->
<div class="modal-overlay hidden" id="editPaymentModal" style="z-index: 10001;">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3 class="modal-title">✏️ Modifier le Paiement</h3>
            <button class="close-btn" onclick="closeEditPaymentModal()">×</button>
        </div>
        
        <form id="editPaymentForm" onsubmit="updatePayment(event)">
            <input type="hidden" id="editPaymentId">
            
            <div class="form-group">
                <label class="form-label">Facture</label>
                <input type="text" id="editPaymentInvoiceNumber" class="form-control" readonly>
            </div>
            
            <div class="form-group">
                <label class="form-label">Montant (FCFA) *</label>
                <input type="number" id="editPaymentAmount" name="amount" class="form-control" required min="1" step="1">
            </div>
            
            <div class="form-group">
                <label class="form-label">Méthode de paiement *</label>
                <select id="editPaymentMethod" name="payment_method" class="form-control" required>
                    <option value="Espèces">Espèces</option>
                    <option value="Virement bancaire">Virement bancaire</option>
                    <option value="Mobile Money">Mobile Money</option>
                    <option value="Carte bancaire">Carte bancaire</option>
                    <option value="Chèque">Chèque</option>
                    <option value="Autres">Autres</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Date du paiement</label>
                <input type="date" id="editPaymentDate" name="payment_date" class="form-control">
            </div>
            
            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea id="editPaymentNotes" name="notes" class="form-control" rows="2"></textarea>
            </div>
            
            <div class="flex gap-2 mt-4">
                <button type="submit" class="btn btn-success w-full">💾 Mettre à jour</button>
                <button type="button" class="btn w-full" onclick="closeEditPaymentModal()">Annuler</button>
            </div>
        </form>
    </div>
</div>

   <!-- Modal Invoice Multi-Services -->
<div class="modal-overlay hidden" id="invoiceModal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h3 class="modal-title">Créer une Facture</h3>
            <button class="close-btn" onclick="closeInvoiceModal()">×</button>
        </div>
        
        <form id="invoiceForm" onsubmit="saveInvoice(event)">
            <div class="form-grid" style="grid-template-columns: 1fr 1fr;">
                <div class="form-group">
                    <label class="form-label">Client *</label>
                    <div style="position: relative;">
                        <input
                            type="text"
                            id="invoiceClientSearch"
                            class="form-control"
                            placeholder="Rechercher un client par nom, prénom, téléphone..."
                            autocomplete="off"
                            onkeyup="filterClients(this.value)"
                            onfocus="showClientDropdown()"
                        >
                        <input type="hidden" name="client_id" id="invoiceClientId" required>

                        <div id="clientDropdown" class="client-dropdown" style="display: none;">
                            <div class="client-dropdown-header">
                                <span id="clientResultsCount">0 clients trouvés</span>
                            </div>
                            <div id="clientDropdownList" class="client-dropdown-list">
                                <!-- Liste des clients sera générée ici -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Catégorie *</label>
                    <select name="categorie" id="invoiceCategorie" class="form-control" required onchange="onCategorieChange()">
                        <option value="">Sélectionner une catégorie</option>
                        <option value="Frais du Cabinet">Frais du Cabinet</option>
                        <option value="Documents de Voyage">Documents de Voyage</option>
                        <option value="Autres">Autres</option>
                    </select>
                </div>
            </div>
            
            <!-- Zone des services -->
            <div style="margin: 1.5rem 0;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h4 class="font-semibold">Services</h4>
                    <button type="button" class="btn btn-primary btn-sm" onclick="addServiceLine()">
                        ➕ Ajouter un service
                    </button>
                </div>
                
                <div id="servicesLinesContainer">
                    <div class="text-center text-secondary" style="padding: 1rem;">
                        Aucun service ajouté
                    </div>
                </div>
                
                <div style="margin-top: 1rem; padding: 1rem; background: var(--bg-secondary); border-radius: 6px; text-align: right;">
                    <strong>Montant Total: <span id="totalAmount" class="text-primary">0 FCFA</span></strong>
                </div>
            </div>
            
            <div class="form-grid" style="grid-template-columns: 1fr;">
                <div class="form-group">
                    <label class="form-label">Date d'échéance *</label>
                    <input type="date" name="due_date" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="3" class="form-control"></textarea>
                </div>
            </div>
        </form>
        
        <div class="flex gap-2 mt-4">
            <button type="submit" form="invoiceForm" class="btn btn-success w-full">💰 Créer Facture</button>
            <button type="button" class="btn w-full" onclick="closeInvoiceModal()">✖ Fermer</button>
        </div>
    </div>
</div>

    <!-- Modal Edit Permissions -->
<div class="modal-overlay hidden" id="permissionsModal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3 class="modal-title">🔐 Gérer les Permissions</h3>
            <button class="close-btn" onclick="closePermissionsModal()">×</button>
        </div>
        
        <form id="permissionsForm" onsubmit="savePermissions(event)">
            <input type="hidden" id="permissionUserId" name="user_id">
            
            <div class="form-group">
                <label class="form-label">Utilisateur</label>
                <input type="text" id="permissionUserName" class="form-control" readonly>
            </div>
            
            <div class="form-group">
                <label class="form-label">Rôle</label>
                <select name="role" id="permissionRole" class="form-control" required>
                    <option value="Admin">Admin</option>
                    <option value="Manager">Manager</option>
                    <option value="Commercial">Commercial</option>
                    <option value="Agent Comptoir">Agent Comptoir</option>
                </select>
            </div>
            
            <div class="form-group">
    <label class="form-label">Modules et actions accessibles</label>
    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
        
        <!-- MODULES PRINCIPAUX -->
        <div style="padding: 0.75rem; background: #f8fafc; border-radius: 6px;">
            <div style="font-weight: 600; margin-bottom: 0.5rem; color: #475569;">📊 Modules Principaux</div>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="permissions[]" value="dashboard" id="perm_dashboard">
                    <span>Dashboard</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="permissions[]" value="clients" id="perm_clients">
                    <span>Clients</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="permissions[]" value="invoicing" id="perm_invoicing">
                    <span>Facturation</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="permissions[]" value="recovery" id="perm_recovery">
                    <span>Recouvrement</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="permissions[]" value="performance" id="perm_performance">
                    <span>Performance</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="permissions[]" value="analytics" id="perm_analytics">
                    <span>Analytics</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="permissions[]" value="admin" id="perm_admin">
                    <span>Administration</span>
                </label>
            </div>
        </div>
        
        <!-- ACTIONS CLIENTS -->
        <div style="padding: 0.75rem; background: #fef3c7; border-radius: 6px;">
            <div style="font-weight: 600; margin-bottom: 0.5rem; color: #92400e;">👥 Actions Clients</div>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="permissions[]" value="edit_clients" id="perm_edit_clients">
                    <span>✏️ Modifier les clients</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="permissions[]" value="delete_clients" id="perm_delete_clients">
                    <span>🗑️ Supprimer les clients</span>
                </label>
            </div>
        </div>
        
        <!-- ACTIONS FACTURES -->
        <div style="padding: 0.75rem; background: #dbeafe; border-radius: 6px;">
            <div style="font-weight: 600; margin-bottom: 0.5rem; color: #1e40af;">💰 Actions Factures</div>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="permissions[]" value="edit_invoices" id="perm_edit_invoices">
                    <span>✏️ Modifier les factures</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="permissions[]" value="delete_invoices" id="perm_delete_invoices">
                    <span>🗑️ Supprimer les factures</span>
                </label>
            </div>
        </div>
        
        <!-- ACTIONS PAIEMENTS -->
        <div style="padding: 0.75rem; background: #dcfce7; border-radius: 6px;">
            <div style="font-weight: 600; margin-bottom: 0.5rem; color: #15803d;">💳 Actions Paiements</div>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="permissions[]" value="edit_payments" id="perm_edit_payments">
                    <span>✏️ Modifier les paiements</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="permissions[]" value="delete_payments" id="perm_delete_payments">
                    <span>🗑️ Supprimer les paiements (Super Admin uniquement)</span>
                </label>
            </div>
        </div>
        </div>
        </div>
        
            
            <div class="flex gap-2 mt-4">
                <button type="submit" class="btn btn-success w-full">💾 Enregistrer</button>
                <button type="button" class="btn w-full" onclick="closePermissionsModal()">Annuler</button>
            </div>
        </form>
    </div>
</div>




        <!-- Payment Reminder Modal -->
<div class="modal-overlay hidden" id="reminderModal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h3 class="modal-title">📧 Rappel de Paiement</h3>
            <button class="close-btn" onclick="closeReminderModal()">×</button>
        </div>
        
        <div style="margin-bottom: 1rem;">
            <div class="text-sm text-secondary">
                <strong>Facture:</strong> <span id="reminderInvoiceNumber"></span> |
                <strong>Client:</strong> <span id="reminderClientName"></span> |
                <strong>Montant dû:</strong> <span id="reminderAmount"></span>
            </div>
        </div>

        <!-- Reminder Settings -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="form-group">
                <label class="form-label">Ton du message</label>
                <select id="reminderTone" class="form-control" onchange="updateReminderMessage()">
                    <option value="courtois">Courtois</option>
                    <option value="ferme">Ferme</option>
                    <option value="dernier">Dernier rappel</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Scénario</label>
                <select id="reminderScenario" class="form-control" onchange="updateReminderMessage()">
                    <option value="j-7">J-7 (Avant échéance)</option>
                    <option value="j">J (Jour d'échéance)</option>
                    <option value="j+3">J+3 (3 jours de retard)</option>
                    <option value="j+10">J+10 (10 jours de retard)</option>
                    <option value="j+20">J+20 (20 jours de retard)</option>
                </select>
            </div>
        </div>

        <!-- Channel Tabs -->
        <div class="nav-tabs" style="margin-bottom: 1rem;">
            <button class="nav-tab active" onclick="switchReminderChannel('whatsapp')" id="whatsapp-tab">📱 WhatsApp</button>
            <button class="nav-tab" onclick="switchReminderChannel('email')" id="email-tab">📧 E-mail</button>
            <button class="nav-tab" onclick="switchReminderChannel('sms')" id="sms-tab">💬 SMS</button>
        </div>

        <!-- WhatsApp Channel -->
        <div id="whatsapp-channel" class="reminder-channel">
            <div class="form-group">
                <label class="form-label">Message WhatsApp</label>
                <textarea id="whatsappMessage" class="form-control" rows="6" readonly></textarea>
                <div class="flex gap-2 mt-2">
                    <button class="btn btn-success" onclick="copyReminderMessage('whatsapp')">📋 Copier</button>
                </div>
            </div>
        </div>

        <!-- Email Channel -->
        <div id="email-channel" class="reminder-channel hidden">
            <div class="form-group">
                <label class="form-label">Objet de l'e-mail</label>
                <input type="text" id="emailSubject" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label class="form-label">Corps du message</label>
                <textarea id="emailMessage" class="form-control" rows="8" readonly></textarea>
                <div class="flex gap-2 mt-2">
                    <button class="btn btn-success" onclick="copyReminderMessage('email')">📋 Copier Tout</button>
                    <button class="btn btn-info" onclick="copyEmailSubject()">📋 Copier Objet</button>
                </div>
            </div>
        </div>

        <!-- SMS Channel -->
        <div id="sms-channel" class="reminder-channel hidden">
            <div class="form-group">
                <label class="form-label">Message SMS <span id="smsLength" class="text-sm text-secondary">(0/160)</span></label>
                <textarea id="smsMessage" class="form-control" rows="4" readonly></textarea>
                <div class="flex gap-2 mt-2">
                    <button class="btn btn-success" onclick="copyReminderMessage('sms')">📋 Copier</button>
                </div>
            </div>
            <div class="text-xs text-warning">
                ⚠️ Vérifiez vos obligations légales (opt-in SMS) avant envoi
            </div>
        </div>

        <!-- Variables Info -->
        <div class="card mt-4" style="background: var(--bg-primary); font-size: 0.8rem;">
            <h5>Variables disponibles:</h5>
            <div class="grid grid-cols-3 gap-2 text-xs">
                <div>@{{client_nom}}, @{{client_prenoms}}</div>
                <div>@{{facture_num}}, @{{facture_montant}}</div>
                <div>@{{facture_echeance}},@{{jours_retard}}</div>
                <div>@{{agent_nom}}, @{{lien_paiement}}</div>
                <div>@{{tel_support}}, @{{email_support}}</div>
                <div>@{{coordonnees_societe}}</div>
            </div>
        </div>

        <!-- Disclaimer -->
        <div class="text-xs text-secondary mt-3 disclaimer">
            📋 Les messages sont fournis pour copie/colle uniquement. Vérifiez vos obligations légales (opt-in SMS/email) avant envoi.
        </div>

        <div class="flex gap-2 mt-4">
            <button class="btn w-full" onclick="closeReminderModal()">Fermer</button>
        </div>
    </div>
</div>

    <!-- Modal View Client -->
    <div class="modal-overlay hidden" id="viewClientModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Détails du Client</h3>
                <button class="close-btn" onclick="closeViewClientModal()">×</button>
            </div>
            <div id="clientDetails"></div>
            <div class="flex gap-2 mt-4">
                <button type="button" class="btn btn-primary w-full" onclick="createInvoiceForClient()">💰 Créer Facture</button>
                <button type="button" class="btn w-full" onclick="closeViewClientModal()">Fermer</button>
            </div>
        </div>
    </div>
     <!-- Modal View Invoice -->
<div class="modal-overlay hidden" id="viewInvoiceModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Détails de la Facture</h3>
            <button class="close-btn" onclick="closeViewInvoiceModal()">×</button>
        </div>
        <div id="invoiceDetails"></div>
        <div class="flex gap-2 mt-4">
            <button type="button" class="btn btn-primary" onclick="printCurrentInvoice()">📄 Imprimer</button>
            <button type="button" class="btn w-full" onclick="closeViewInvoiceModal()">Fermer</button>
        </div>
    </div>
</div>

<!-- Modal Edit Invoice -->
<div class="modal-overlay hidden" id="editInvoiceModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Modifier la Facture</h3>
            <button class="close-btn" onclick="closeEditInvoiceModal()">×</button>
        </div>
        <form id="editInvoiceForm" class="form-grid" onsubmit="updateInvoice(event)">
            <input type="hidden" id="editInvoiceId" name="invoice_id">
            <input type="hidden" id="editInvoiceClientId" name="client_id">
            <div class="form-group">
                <label class="form-label">Service *</label>
                <input type="text" name="service" id="editInvoiceService" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Montant (FCFA) *</label>
                <input type="number" name="amount" id="editInvoiceAmount" class="form-control" required min="0">
            </div>
            <div class="form-group">
                <label class="form-label">Date d'échéance *</label>
                <input type="date" name="due_date" id="editInvoiceDueDate" class="form-control" required>
            </div>
        </form>
        <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" id="editInvoiceNotes" rows="3" class="form-control" form="editInvoiceForm"></textarea>
        </div>
        <div class="flex gap-2 mt-4">
            <button type="submit" form="editInvoiceForm" class="btn btn-success w-full">💾 Mettre à jour</button>
            <button type="button" class="btn w-full" onclick="closeEditInvoiceModal()">✖ Annuler</button>
        </div>
    </div>
</div>

<!-- MODAL DE PAIEMENT DÉSACTIVÉ : Les paiements se font maintenant dans la caisse
<div class="modal-overlay hidden" id="paymentModal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3 class="modal-title">💳 Enregistrer un Paiement</h3>
            <button class="close-btn" onclick="closePaymentModal()">×</button>
        </div>

        <form id="paymentForm" onsubmit="submitPayment(event)">
            <input type="hidden" id="paymentInvoiceId">

            <div class="form-group">
                <label class="form-label">Facture</label>
                <input type="text" id="paymentInvoiceNumber" class="form-control" readonly>
            </div>

            <div class="form-group">
                <label class="form-label">Montant restant</label>
                <input type="text" id="paymentRemaining" class="form-control" readonly>
            </div>

            <div class="form-group">
                <label class="form-label">Montant du paiement (FCFA) *</label>
                <input type="number" id="paymentAmount" name="amount" class="form-control"
                       required min="1" step="1">
            </div>

            <div class="form-group">
                <label class="form-label">Méthode de paiement *</label>
                <select id="paymentMethod" name="payment_method" class="form-control" required>
                    <option value="Espèces">Espèces</option>
                    <option value="Virement bancaire">Virement bancaire</option>
                    <option value="Mobile Money">Mobile Money</option>
                    <option value="Carte bancaire">Carte bancaire</option>
                    <option value="Chèque">Chèque</option>
                    <option value="Autres">Autres</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Notes (optionnel)</label>
                <textarea id="paymentNotes" name="notes" class="form-control" rows="2"></textarea>
            </div>

            <div class="flex gap-2 mt-4">
                <button type="submit" class="btn btn-success w-full">💾 Enregistrer</button>
                <button type="button" class="btn w-full" onclick="closePaymentModal()">Annuler</button>
            </div>
        </form>
    </div>
</div>
-->

<!-- ✅ MODAL RELANCE CORRIGÉ AVEC TEMPLATES AUTOMATIQUES -->
<div class="modal-overlay hidden" id="relanceModal">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h3 class="modal-title">📞 Nouvelle Relance Client</h3>
            <button class="close-btn" onclick="closeRelanceModal()">×</button>
        </div>
        
        <form id="relanceForm" onsubmit="saveRelance(event)">
            <input type="hidden" id="relanceId" name="relance_id">
            
            <!-- ✅ CLIENT SÉLECTIONNÉ -->
            <div id="selectedClientInfo" style="display: none; padding: 0.75rem; background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%); border-left: 4px solid #667eea; border-radius: 6px; margin-bottom: 1rem;">
                <div class="text-sm" style="color: #667eea; font-weight: 600; margin-bottom: 0.25rem;">
                    👤 Client sélectionné :
                </div>
                <div class="font-semibold text-primary" id="selectedClientName" style="color: #1e293b; font-size: 1.1rem;"></div>
            </div>
            
            <!-- ✅ SÉLECTION CLIENT -->
            <div class="form-group">
                <label class="form-label">Client à relancer *</label>
                <select name="client_id" 
                        id="relanceClientSelect" 
                        class="form-control" 
                        required 
                        onchange="updateSelectedClientInfo()">
                    <option value="">Sélectionner un client</option>
                </select>
            </div>
            
            <!-- ✅ CHOIX DU CANAL DE COMMUNICATION -->
            <div class="form-group">
                <label class="form-label">
                    📢 Canal de Communication *
                </label>
                <div id="relanceCanalButtons" style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                    <!-- Boutons générés dynamiquement par JavaScript -->
                </div>
                <div style="margin-top: 0.5rem; padding: 0.5rem; background: #f0f9ff; border-radius: 6px; font-size: 0.85rem; color: #0369a1;">
                    💡 Le message sera adapté automatiquement selon le statut du client
                </div>
            </div>
            
            <!-- ✅ SUJET EMAIL (affiché uniquement si canal = email) -->
            <div class="form-group" id="emailSubjectContainer" style="display: none;">
                <label class="form-label">📧 Objet de l'email</label>
                <input type="text" 
                       id="relanceEmailSubject" 
                       class="form-control" 
                       placeholder="Objet du message email">
            </div>
            
            <!-- ✅ MESSAGE DE RELANCE -->
            <div class="form-group">
                <label class="form-label">
                    💬 Message de la relance *
                </label>
                <textarea name="commentaire" 
                          id="relanceCommentaire" 
                          rows="6" 
                          class="form-control" 
                          required 
                          placeholder="Le message sera généré automatiquement selon le statut du client..."
                          style="font-family: inherit; font-size: 0.95rem;"></textarea>
                <div style="margin-top: 0.5rem; font-size: 0.8rem; color: #64748b;">
                    ✏️ Vous pouvez personnaliser le message généré automatiquement
                </div>
            </div>
            
            <!-- ✅ STATUT DE LA RELANCE -->
            <div class="form-group">
                <label class="form-label">Statut de suivi *</label>
                <select name="statut" id="relanceStatut" class="form-control" required>
                    <option value="En cours">En cours (relance dans 7 jours)</option>
                    <option value="Clôturé">Clôturé (fin du suivi)</option>
                </select>
            </div>
            
            <!-- ✅ INFORMATION AUTOMATIQUE -->
            <div class="form-group">
                <div style="padding: 1rem; background: linear-gradient(135deg, #fef3c715 0%, #fde68a15 100%); border-left: 4px solid #f59e0b; border-radius: 6px; font-size: 0.9rem;">
                    <div style="font-weight: 600; color: #92400e; margin-bottom: 0.5rem;">
                        ⏰ Programmation automatique
                    </div>
                    <div style="color: #78350f;">
                        Si vous choisissez "En cours", une nouvelle relance sera automatiquement programmée dans 7 jours. Vous recevrez une notification pour ne pas oublier de recontacter le client.
                    </div>
                </div>
            </div>
        </form>
        
        <!-- ✅ BOUTONS D'ACTION -->
        <div class="flex gap-2 mt-4">
            <button type="submit" 
                    form="relanceForm" 
                    class="btn btn-success w-full"
                    style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <span style="font-size: 1.2rem;">💾</span> Enregistrer la Relance
            </button>
            <button type="button" 
                    class="btn w-full" 
                    onclick="closeRelanceModal()"
                    style="background: #f1f5f9; color: #475569;">
                ✖ Annuler
            </button>
        </div>
        
        <!-- ✅ AIDE RAPIDE -->
        <div style="margin-top: 1.5rem; padding: 1rem; background: #f8fafc; border-radius: 8px; font-size: 0.85rem; color: #64748b;">
            <div style="font-weight: 600; margin-bottom: 0.5rem; color: #1e293b;">
                📋 Guide rapide :
            </div>
            <ul style="margin: 0; padding-left: 1.5rem;">
                <li>Le template de message s'adapte automatiquement au <strong>statut du client</strong></li>
                <li>Vous pouvez choisir entre <strong>WhatsApp</strong>, <strong>SMS</strong> ou <strong>Email</strong></li>
                <li>Les relances "En cours" sont programmées automatiquement dans <strong>7 jours</strong></li>
                <li>Vous pouvez personnaliser le message avant de l'envoyer</li>
            </ul>
        </div>
    </div>
</div>
<!-- Modal Commentaire Client -->
<div class="modal-overlay hidden" id="commentaireModal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3 class="modal-title">💬 Ajouter un Commentaire</h3>
            <button class="close-btn" onclick="closeCommentaireModal()">×</button>
        </div>
        
        <form id="commentaireForm" onsubmit="saveCommentaire(event)">
            <input type="hidden" id="commentaireClientId">
            
            <div id="commentaireClientInfo" style="padding: 0.75rem; background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%); border-left: 4px solid #8b5cf6; border-radius: 6px; margin-bottom: 1rem;">
                <div class="text-sm" style="color: #8b5cf6; font-weight: 600; margin-bottom: 0.25rem;">
                    👤 Client :
                </div>
                <div class="font-semibold text-primary" id="commentaireClientName"></div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Commentaire *</label>
                <textarea id="commentaireText" class="form-control" rows="6" required 
                          placeholder="Notez vos observations, échanges ou informations importantes sur ce client..."></textarea>
                <div class="text-xs text-secondary mt-2">
                    💡 Ce commentaire sera visible dans l'historique du client
                </div>
            </div>
        </form>
        
        <div class="flex gap-2 mt-4">
            <button type="submit" form="commentaireForm" class="btn btn-success w-full">
                💾 Enregistrer le Commentaire
            </button>
            <button type="button" class="btn w-full" onclick="closeCommentaireModal()">
                Annuler
            </button>
        </div>
    </div>
</div>

    <div id="toastContainer"></div>

    <script>
        const API_BASE = '{{ url("/crm") }}';
        const CSRF_TOKEN = '{{ csrf_token() }}';
        let currentClients = [];
        let clientsARelancer = [];
        let currentClientForInvoice = null;
        let selectedClient = null;
        // Variable globale pour stocker les services sélectionnés
        let selectedServices = [];
        // ==================== GESTION DES PERMISSIONS GRANULAIRES ====================
        let userActionPermissions = {
            edit_clients: false,
            delete_clients: false,
            edit_invoices: false,
            delete_invoices: false,
            edit_payments: false,
            delete_payments: false
        };

        async function loadActionPermissions() {
    try {
        const response = await fetch(`${API_BASE}/user/check-permissions`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });
        
        const data = await response.json();
        
        if (data.success) {
            userActionPermissions = data.permissions;
            console.log('✅ Permissions chargées:', userActionPermissions);
        }
    } catch (error) {
        console.error('❌ Erreur chargement permissions:', error);
    }
}

        // ==================== CODES COULEURS DES STATUTS - 5 PHASES ====================
function getStatusBadgeClass(statut) {
    const statusColors = {
        // 🔵 PHASE 1 - DÉCOUVERTE
        'Lead': 'badge-warning',              // Orange
        'Prospect': 'badge-info',             // Cyan
        'À convertir': 'badge-purple',        // Violet
        'Perdu': 'badge-danger',              // Rouge
        
        // 🟢 PHASE 2 - ENGAGEMENT
        'Profil visa payé': 'badge-success',           // Vert
        'Frais d\'assistance payés': 'badge-success',  // Vert
        'En attente de documents': 'badge-warning',    // Orange
        'Documents validés': 'badge-success',          // Vert
        'Rendez-vous au bureau PSI': 'badge-info',     // Cyan
        'Rendez-vous d\'urgence': 'badge-danger',      // Rouge
        'En attente de paiement des frais de profil visa et d\'inscription': 'badge-warning',
        'En attente de paiement des frais de cabinet': 'badge-amber',
        
        // 🟡 PHASE 3 - VISA
        'Prise de RDV ambassade confirmée': 'badge-info',    // Cyan
        'En attente de décision visa': 'badge-warning',      // Orange
        'Visa accepté': 'badge-success',                     // Vert
        'Visa refusé': 'badge-danger',                       // Rouge
        'Visa validé': 'badge-success',                      // Vert
        
        // 🟣 PHASE 4 - VOYAGE
        'Billet d\'avion payé': 'badge-success',       // Vert
        'Départ confirmé': 'badge-success',            // Vert
        'En suivi post-départ': 'badge-info',          // Cyan
        
        // 🔴 PHASE 5 - RELANCE SPÉCIALE
        'Négociation': 'badge-orange',         // Orange foncé
        'Opportunité': 'badge-purple',         // Violet
        'Message d\'urgence': 'badge-danger',  // Rouge
        'Converti': 'badge-success'            // Vert
    };
    return statusColors[statut] || 'badge-info';
}


        
        // ============================================
        // AJOUT 1 : CODES COULEURS DES STATUTS
        // ============================================

        // Ajouter cette fonction au début du script (après les variables globales)
        function getStatusBadgeClass(statut) {
    const statusColors = {
        // 🔵 PHASE 1 - DÉCOUVERTE
        'Lead': 'badge-orange',              // 🟠 Orange
        'Prospect': 'badge-cyan',            // 🔵 Cyan
        'À convertir': 'badge-purple',       // 🟣 Violet
        'Perdu': 'badge-red',                // 🔴 Rouge
        
        // 🟢 PHASE 2 - ENGAGEMENT
        'Profil visa payé': 'badge-green',                    // 🟢 Vert
        'Frais d\'assistance payés': 'badge-emerald',         // 💚 Vert émeraude
        'En attente de documents': 'badge-amber',             // 🟡 Ambre
        'Documents validés': 'badge-lime',                    // 🍏 Vert citron
        'Rendez-vous au bureau PSI': 'badge-sky',             // 🔵 Bleu ciel
        'Rendez-vous d\'urgence': 'badge-red-dark',           // 🔴 Rouge foncé
        
        // ✅ AJOUT DES DEUX STATUTS MANQUANTS
        'En attente de paiement des frais de profil visa et d\'inscription': 'badge-warning',  // 🟠 Orange/Jaune
        'En attente de paiement des frais de cabinet': 'badge-amber',  // 🟡 Ambre
        
        // 🟡 PHASE 3 - VISA
        'Prise de RDV ambassade confirmée': 'badge-indigo',   // 🔵 Indigo
        'En attente de décision visa': 'badge-yellow',        // 🟡 Jaune
        'Visa accepté': 'badge-green-bright',                 // 🟢 Vert vif
        'Visa refusé': 'badge-red-bright',                    // 🔴 Rouge vif
        'Visa validé': 'badge-teal',                          // 🟢 Turquoise
        
        // 🟣 PHASE 4 - VOYAGE
        'Billet d\'avion payé': 'badge-blue',                 // 🔵 Bleu
        'Départ confirmé': 'badge-green-dark',                // 🟢 Vert foncé
        'En suivi post-départ': 'badge-gray',                 // ⚪ Gris
        
        // 🔴 PHASE 5 - RELANCE SPÉCIALE
        'Négociation': 'badge-orange-dark',   // 🟠 Orange foncé
        'Opportunité': 'badge-pink',          // 🌸 Rose
        'Message d\'urgence': 'badge-danger-flash', // ⚠️ Rouge clignotant
        'Converti': 'badge-success-gold',     // 🏆 Vert doré
    };
    
    return statusColors[statut] || 'badge-default';
}
        // === UTILITAIRES ===
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast ${type} show`;
            toast.textContent = message;
            document.getElementById('toastContainer').appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('fr-FR').format(amount || 0) + ' FCFA';
        }

        function formatDate(dateStr) {
            if (!dateStr) return '';
            return new Date(dateStr).toLocaleDateString('fr-FR');
        }

        // === NAVIGATION ===
        // ✅ Les événements de navigation sont maintenant attachés dans DOMContentLoaded après le chargement des fonctions globales
       
// Modifier la fonction sendReminder existante pour utiliser la nouvelle modal
function sendReminder(id) {
    openReminderModal(id);
}

// === SYSTÈME DE RAPPELS DE PAIEMENT COMPLET ===
       
// === SYSTÈME DE RAPPELS COMPLET ===
let currentReminderInvoice = null;
let currentReminderChannel = 'whatsapp';

@verbatim
function initializeReminderTemplates() {
    return {
        whatsapp: {
            courtois: {
                'j-7': "Bonjour {{client_prenoms}}, ici {{agent_nom}} de {{coordonnees_societe}}.  Petit rappel : la facture {{facture_num}} ({{facture_montant}} FCFA) arrive à échéance le {{facture_echeance}}.  Paiement sécurisé : {{lien_paiement}}. Merci !",
                'j': "Bonjour {{client_prenoms}}, votre facture {{facture_num}} ({{facture_montant}} FCFA) est due aujourd'hui.  Lien de paiement : {{lien_paiement}}. Merci de votre confiance ! - {{agent_nom}}",
                'j+3': "Bonjour {{client_prenoms}}, votre facture {{facture_num}} est en retard de {{jours_retard}} jours ({{facture_montant}} FCFA).  Merci de régulariser via : {{lien_paiement}}. Cordialement, {{agent_nom}}",
                'j+10': "Bonjour {{client_prenoms}}, sauf erreur, la facture {{facture_num}} reste impayée ({{facture_montant}} FCFA), échéance {{facture_echeance}} ({{jours_retard}} jours). Merci d'effectuer le règlement sous 48h : {{lien_paiement}}. Besoin d'aide ? {{tel_support}}",
                'j+20': "Bonjour {{client_prenoms}}, votre facture {{facture_num}} ({{facture_montant}} FCFA) est en retard de {{jours_retard}} jours. Merci de régulariser rapidement : {{lien_paiement}}. Contact : {{tel_support}}"
            },
            ferme: {
                'j-7': "{{client_prenoms}}, facture {{facture_num}} ({{facture_montant}} FCFA) échéance {{facture_echeance}}. Paiement : {{lien_paiement}} - {{coordonnees_societe}}",
                'j': "{{client_prenoms}}, facture {{facture_num}} due AUJOURD'HUI ({{facture_montant}} FCFA). Paiement immédiat requis : {{lien_paiement}}",
                'j+3': "{{client_prenoms}}, facture {{facture_num}} EN RETARD de {{jours_retard}} jours. Montant : {{facture_montant}} FCFA. Régularisation immédiate : {{lien_paiement}}",
                'j+10': "{{client_prenoms}}, MISE EN DEMEURE - Facture {{facture_num}} impayée depuis {{jours_retard}} jours ({{facture_montant}} FCFA). Règlement sous 48h IMPÉRATIF : {{lien_paiement}}. Support : {{tel_support}}",
                'j+20': "{{client_prenoms}}, DERNIÈRE RELANCE - {{jours_retard}} jours de retard pour facture {{facture_num}} ({{facture_montant}} FCFA). Sans règlement sous 24h, transmission au contentieux. {{lien_paiement}}"
            },
            dernier: {
                'j+20': "DERNIER RAPPEL : {{client_prenoms}}, facture {{facture_num}} en retard de {{jours_retard}} jours ({{facture_montant}} FCFA). Sans paiement sous 24h, dossier transmis pour procédure. Paiement : {{lien_paiement}}"
            }
        },
        email: {
            courtois: {
                'j-7': {
                    subject: "Rappel d'échéance – Facture {{facture_num}} ({{facture_echeance}})",
                    body: "Bonjour {{client_prenoms}} {{client_nom}},\n\nNous vous rappelons l'échéance de la facture {{facture_num}} d'un montant de {{facture_montant}} FCFA, due le {{facture_echeance}}.\n\nVous pouvez effectuer votre paiement via le lien sécurisé : {{lien_paiement}}\n\nNous restons à votre disposition pour toute question.\n\nCordialement,\n{{agent_nom}}\n{{coordonnees_societe}}\n{{tel_support}} | {{email_support}}"
                },
                'j': {
                    subject: "Échéance aujourd'hui – Facture {{facture_num}}",
                    body: "Bonjour {{client_prenoms}} {{client_nom}},\n\nVotre facture {{facture_num}} d'un montant de {{facture_montant}} FCFA arrive à échéance aujourd'hui.\n\nMerci d'effectuer votre paiement via : {{lien_paiement}}\n\nCordialement,\n{{agent_nom}}\n{{coordonnees_societe}}"
                },
                'j+3': {
                    subject: "Relance amicale – Facture {{facture_num}} ({{jours_retard}} jours)",
                    body: "Bonjour {{client_prenoms}} {{client_nom}},\n\nVotre facture {{facture_num}} ({{facture_montant}} FCFA) présente un retard de {{jours_retard}} jours depuis l'échéance du {{facture_echeance}}.\n\nMerci de bien vouloir régulariser votre situation via : {{lien_paiement}}\n\nSi vous rencontrez des difficultés, n'hésitez pas à nous contacter.\n\nCordialement,\n{{agent_nom}}\n{{tel_support}} | {{email_support}}"
                }
            },
            ferme: {
                'j+10': {
                    subject: "Relance de paiement – Facture {{facture_num}} (retard {{jours_retard}} j)",
                    body: "Madame, Monsieur {{client_nom}},\n\nNous constatons que votre facture {{facture_num}} d'un montant de {{facture_montant}} FCFA demeure impayée {{jours_retard}} jours après l'échéance du {{facture_echeance}}.\n\nNous vous demandons de procéder au règlement sous 48 heures via : {{lien_paiement}}\n\nÀ défaut, nous nous verrions contraints d'engager des procédures de recouvrement.\n\nPour toute question : {{tel_support}} | {{email_support}}\n\nCordialement,\n{{agent_nom}}\n{{coordonnees_societe}}"
                }
            }
        },
        sms: {
            courtois: {
                'j': "Rappel facture {{facture_num}} ({{facture_montant}} FCFA), échéance {{facture_echeance}}. Paiement: {{lien_paiement}} – {{coordonnees_societe}}",
                'j+3': "Facture {{facture_num}} en retard de {{jours_retard}} jours ({{facture_montant}} FCFA). Merci de régler: {{lien_paiement}}"
            },
            ferme: {
                'j+10': "Facture {{facture_num}} en retard ({{jours_retard}} j). Merci de régler sous 48h: {{lien_paiement}}. Aide: {{tel_support}}",
                'j+20': "URGENT: Facture {{facture_num}} retard {{jours_retard}} jours. Réglement immédiat: {{lien_paiement}} ou contentieux"
            }
        }
    };
}
@endverbatim

async function openReminderModal(invoiceId) {
    try {
        const response = await fetch(`${API_BASE}/invoices/${invoiceId}`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });
        const data = await response.json();
        
        if (!data.success) {
            showToast('Erreur de chargement de la facture', 'error');
            return;
        }
        
        const invoice = data.invoice;
        currentReminderInvoice = {
            id: invoice.id,
            number: invoice.number,
            clientName: invoice.client_name,
            amount: invoice.amount,
            paidAmount: invoice.paid_amount,
            dueDate: invoice.due_date,
            agent: invoice.agent,
            client: invoice.client
        };
        
        document.getElementById('reminderInvoiceNumber').textContent = invoice.number;
        document.getElementById('reminderClientName').textContent = invoice.client_name;
        document.getElementById('reminderAmount').textContent = formatCurrency(invoice.amount - invoice.paid_amount);

        const today = new Date();
        const dueDate = new Date(invoice.due_date);
        const daysDiff = Math.floor((today - dueDate) / (1000 * 60 * 60 * 24));
        
        let defaultScenario = 'j';
        if (daysDiff < -3) defaultScenario = 'j-7';
        else if (daysDiff > 0 && daysDiff <= 5) defaultScenario = 'j+3';
        else if (daysDiff > 5 && daysDiff <= 15) defaultScenario = 'j+10';
        else if (daysDiff > 15) defaultScenario = 'j+20';

        document.getElementById('reminderScenario').value = defaultScenario;
        
        let defaultTone = 'courtois';
        if (daysDiff > 15) defaultTone = 'ferme';
        if (daysDiff > 30) defaultTone = 'dernier';
        
        document.getElementById('reminderTone').value = defaultTone;

        switchReminderChannel('whatsapp');
        updateReminderMessage();

        document.getElementById('reminderModal').classList.remove('hidden');
        
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur de chargement de la facture', 'error');
    }
}

function closeReminderModal() {
    document.getElementById('reminderModal').classList.add('hidden');
    currentReminderInvoice = null;
}

function switchReminderChannel(channel) {
    currentReminderChannel = channel;
    
    document.querySelectorAll('#reminderModal .nav-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    document.getElementById(channel + '-tab').classList.add('active');

    document.querySelectorAll('.reminder-channel').forEach(ch => {
        ch.classList.add('hidden');
    });
    document.getElementById(channel + '-channel').classList.remove('hidden');

    updateReminderMessage();
}

function updateReminderMessage() {
    if (!currentReminderInvoice) return;

    const tone = document.getElementById('reminderTone').value;
    const scenario = document.getElementById('reminderScenario').value;
    const channel = currentReminderChannel;

    const templates = initializeReminderTemplates();
    const template = getReminderTemplate(templates, channel, tone, scenario);
    if (!template) return;

    const variables = buildReminderVariables(currentReminderInvoice);
    
    if (channel === 'email') {
        const subject = resolveTemplate(template.subject, variables);
        const body = resolveTemplate(template.body, variables);
        document.getElementById('emailSubject').value = subject;
        document.getElementById('emailMessage').value = body;
    } else if (channel === 'sms') {
        const message = resolveTemplate(template, variables);
        document.getElementById('smsMessage').value = message;
        document.getElementById('smsLength').textContent = '(' + message.length + '/160)';
    } else {
        const message = resolveTemplate(template, variables);
        document.getElementById('whatsappMessage').value = message;
    }
}

function getReminderTemplate(templates, channel, tone, scenario) {
    if (templates[channel] && templates[channel][tone] && templates[channel][tone][scenario]) {
        return templates[channel][tone][scenario];
    }
    
    if (templates[channel] && templates[channel]['courtois'] && templates[channel]['courtois'][scenario]) {
        return templates[channel]['courtois'][scenario];
    }

    return null;
}

function buildReminderVariables(invoice) {
    const today = new Date();
    const dueDate = new Date(invoice.dueDate);
    const jours_retard = Math.max(0, Math.floor((today - dueDate) / (1000 * 60 * 60 * 24)));

    return {
        client_nom: invoice.client?.nom || '[NOM_CLIENT]',
        client_prenoms: invoice.client?.prenoms || '[PRENOMS_CLIENT]',
        facture_num: invoice.number,
        facture_montant: new Intl.NumberFormat('fr-FR').format(invoice.amount - (invoice.paidAmount || 0)),
        facture_echeance: formatDate(invoice.dueDate),
        jours_retard: jours_retard,
        agent_nom: invoice.agent || 'PSI Africa',
        lien_paiement: 'https://www.psiafrica.ci/paiement',
        coordonnees_societe: 'PSI Africa',
        email_support: 'psintervisa@gmail.com',
        tel_support: '+225 01 04 04 04 05'
    };
}

function resolveTemplate(template, variables) {
    if (!template) return '';
    
    let resolved = template;
    Object.keys(variables).forEach(key => {
        // Échapper les accolades pour la regex
        const regex = new RegExp('\\{\\{' + key + '\\}\\}', 'g');
        resolved = resolved.replace(regex, variables[key]);
    });
    
    return resolved;
}

function copyReminderMessage(channel) {
    let textToCopy = '';
    
    if (channel === 'email') {
        const subject = document.getElementById('emailSubject').value;
        const body = document.getElementById('emailMessage').value;
        textToCopy = 'Objet: ' + subject + '\n\n' + body;
    } else if (channel === 'sms') {
        textToCopy = document.getElementById('smsMessage').value;
    } else {
        textToCopy = document.getElementById('whatsappMessage').value;
    }

    navigator.clipboard.writeText(textToCopy).then(() => {
        showToast('Message copié dans le presse-papiers', 'success');
    }).catch(() => {
        const textArea = document.createElement('textarea');
        textArea.value = textToCopy;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showToast('Message copié', 'success');
    });
}

function copyEmailSubject() {
    const subject = document.getElementById('emailSubject').value;
    navigator.clipboard.writeText(subject).then(() => {
        showToast('Objet copié dans le presse-papiers', 'success');
    }).catch(() => {
        showToast('Erreur lors de la copie', 'error');
    });
}

        // === DASHBOARD ===
        async function loadDashboard() {
    try {
        console.log('📊 Chargement dashboard...');

        const response = await fetch(`${API_BASE}/stats`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });
        const data = await response.json();
        if (data.success) {
            renderKPIStats(data.stats);
            renderRecentActivity(data.stats.activities);
            
            setTimeout(() => {
                createDashboardCharts(data.stats);
            }, 100);
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur de chargement', 'error');
    }
}
        function renderKPIStats(stats) {
    // Calcul correct du taux de conversion
    const tauxConversion = stats.clients.total > 0 ?
        ((stats.clients.convertis / stats.clients.total) * 100).toFixed(1) :
        0;

    // Calcul du taux de paiement (factures payées / total factures)
    const tauxPaiement = stats.invoices.total > 0 ?
        ((stats.invoices.paid / stats.invoices.total) * 100).toFixed(1) :
        0;

    document.getElementById('kpiStats').innerHTML = `
        <div class="stat-card">
            <div class="stat-value text-primary">${stats.clients.total}</div>
            <div class="stat-label">Clients Total</div>
        </div>
        <div class="stat-card">
            <div class="stat-value text-success">${tauxConversion}%</div>
            <div class="stat-label">Taux Conversion</div>
        </div>
        <div class="stat-card">
            <div class="stat-value text-info">${formatCurrency(stats.revenue.total_invoiced)}</div>
            <div class="stat-label">CA Facturé</div>
        </div>
        <div class="stat-card">
            <div class="stat-value text-success">${formatCurrency(stats.revenue.total_paid)}</div>
            <div class="stat-label">CA Encaissé</div>
        </div>
        <div class="stat-card">
            <div class="stat-value text-warning">${tauxPaiement}%</div>
            <div class="stat-label">Taux Paiement</div>
        </div>
        <div class="stat-card">
            <div class="stat-value text-danger">${stats.invoices.overdue}</div>
            <div class="stat-label">Factures en Retard</div>
        </div>
    `;
}

        function renderRecentActivity(activities) {
            const html = activities && activities.length ? activities.map(a => `
                <div style="border-bottom: 1px solid var(--border); padding: 0.75rem 0;">
                    <div class="font-semibold">${a.action}</div>
                    <div class="text-sm text-secondary">${a.details}</div>
                    <div class="text-xs text-secondary">${a.user_name} - ${formatDate(a.created_at)}</div>
                </div>
            `).join('') : '<div class="text-center text-secondary">Aucune activité</div>';
            document.getElementById('recentActivity').innerHTML = html;
        }

        // === CLIENTS ===
       async function loadClients(search = '', statut = '', prestation = '', page = 1) {
    try {
        console.log('📋 Début chargement clients...');

        // Construire l'URL avec les filtres
        const url = new URL(`${API_BASE}/clients`);
        url.searchParams.append('all', 'true'); // Charger TOUS les clients
        if (search) url.searchParams.append('search', search);
        if (statut) url.searchParams.append('statut', statut);
        if (prestation) url.searchParams.append('prestation', prestation);

        console.log('🔗 URL:', url.toString());

        const response = await fetch(url, {
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        });

        console.log('📡 Statut réponse:', response.status);

        const data = await response.json();
        console.log('📦 Données reçues:', data);

        if (data.success) {
            // ✅ CORRECTION CRITIQUE : Gérer les 2 formats possibles
            // Format 1 : data.clients = array direct
            // Format 2 : data.clients.data = array dans objet paginé Laravel

            let clientsArray = [];
            let paginationInfo = null;

            if (Array.isArray(data.clients)) {
                // Format direct
                clientsArray = data.clients;
                console.log('✅ Format direct détecté');
            } else if (data.clients && data.clients.data && Array.isArray(data.clients.data)) {
                // Format paginé Laravel
                clientsArray = data.clients.data;
                paginationInfo = {
                    current_page: data.clients.current_page,
                    last_page: data.clients.last_page,
                    total: data.clients.total,
                    per_page: data.clients.per_page
                };
                console.log('✅ Format paginé détecté');
            } else if (data.clients && Array.isArray(data.clients)) {
                clientsArray = data.clients;
            } else {
                console.error('❌ Format de données non reconnu:', data.clients);
                showToast('Format de données invalide', 'error');
                return;
            }

            console.log('✅ Nombre de clients trouvés:', clientsArray.length);

            // Stocker globalement
            currentClients = clientsArray;

            // Afficher dans le tableau
            renderClientsTable(clientsArray);

            // Afficher les informations de pagination si disponibles
            if (paginationInfo) {
                updatePaginationInfo(paginationInfo);
            }

        } else {
            console.error('❌ Erreur API:', data);
            showToast(data.error || 'Erreur de chargement', 'error');
        }
    } catch (error) {
        console.error('❌ Erreur loadClients:', error);
        showToast('Erreur de chargement des clients: ' + error.message, 'error');
    }
}

function updatePaginationInfo(info) {
    const paginationDiv = document.getElementById('clientsPaginationInfo');
    if (paginationDiv && info) {
        paginationDiv.innerHTML = `
            <div style="text-align: center; padding: 10px; color: #64748b; font-size: 0.9rem;">
                Affichage de ${info.total} client${info.total > 1 ? 's' : ''} au total
            </div>
        `;
    }
}
        function renderClientsTable(clients) {
    const tbody = document.getElementById('clientsTableBody');
    
    if (!clients || clients.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-secondary">Aucun client trouvé</td></tr>';
        return;
    }
    
    tbody.innerHTML = clients.map(c => {
        // Calcul du statut de relance
        let relanceBadge = '';
        let derniereRelance = 'Jamais relancé';
        let relanceClass = 'badge-danger';
        let relanceIcon = '⚠️';
        
        if (c.relances && Array.isArray(c.relances) && c.relances.length > 0) {
            const lastRelance = c.relances[0];
            const dateRelance = new Date(lastRelance.date_relance);
            const joursDepuis = Math.floor((new Date() - dateRelance) / (1000 * 60 * 60 * 24));
            
            derniereRelance = formatDate(lastRelance.date_relance) + ` (il y a ${joursDepuis} jour${joursDepuis > 1 ? 's' : ''})`;
            
            if (joursDepuis <= 7) {
                relanceClass = 'badge-success';
                relanceIcon = '✅';
                relanceBadge = `<span class="badge ${relanceClass}">${relanceIcon} Relancé récemment</span>`;
            } else if (joursDepuis <= 14) {
                relanceClass = 'badge-warning';
                relanceIcon = '⏰';
                relanceBadge = `<span class="badge ${relanceClass}">${relanceIcon} À relancer bientôt</span>`;
            } else {
                relanceClass = 'badge-danger';
                relanceIcon = '🔴';
                relanceBadge = `<span class="badge ${relanceClass}">${relanceIcon} Relance urgente</span>`;
            }
        } else {
            relanceBadge = `<span class="badge badge-danger">⚠️ Non relancé</span>`;
        }
        
        // ✅ GESTION DES BOUTONS SELON LES PERMISSIONS
        const editButton = userActionPermissions.edit_clients ? 
            `<button class="btn btn-sm btn-warning" onclick="showEditClientModal(${c.id})" title="Modifier">✏️</button>` : '';
        
        const deleteButton = userActionPermissions.delete_clients ? 
            `<button class="btn btn-sm btn-danger" onclick="deleteClient(${c.id})" title="Supprimer">🗑️</button>` : '';
        
        return `
            <tr>
                <td class="text-xs font-mono">${c.uid.substring(0, 8)}</td>
                <td>
                    <div class="font-semibold">${c.nom} ${c.prenoms || ''}</div>
                    <div class="text-xs text-secondary">${c.email || ''}</div>
                </td>
                <td>${c.contact}</td>
                <td>${c.prestation}</td>
                <td>${formatCurrency(c.budget)}</td>
                <td>
                    <span class="badge ${getStatusBadgeClass(c.statut)} status-badge-clickable" 
                          title="Cliquez pour changer le statut"
                          onclick="quickChangeStatut(${c.id}, '${c.statut.replace(/'/g, "\\'")}', event)"
                          style="cursor: pointer; user-select: none;">
                        ${c.statut}
                    </span>
                </td>
                <td>
                    <div class="text-sm">${c.agent}</div>
                    <div class="text-xs text-secondary">${formatDate(c.date_creation)}</div>
                </td>
                <td>
                    <div style="margin-bottom: 0.25rem;">${relanceBadge}</div>
                    <div class="text-xs text-secondary">${derniereRelance}</div>
                </td>
                <td>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-info" onclick="viewClient(${c.id})" title="Voir">👁️</button>
                        ${editButton}
                        <button class="btn btn-sm btn-primary" onclick="quickInvoiceForClient(${c.id})" title="Créer facture">💰</button>
                        <button class="btn btn-sm btn-warning" onclick="quickRelanceClient(${c.id})" title="Relancer">📞</button>
                        <button class="btn btn-sm" style="background: #8b5cf6; color: white;" onclick="showAddCommentaireModal(${c.id})" title="Commentaire">💬</button>
                        ${deleteButton}
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

    // === RECHERCHE ET FILTRES CLIENTS ===
let searchTimeout;

// Attacher les événements au chargement
document.addEventListener('DOMContentLoaded', function() {
    // Recherche avec délai
    const searchInput = document.getElementById('clientSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 500);
        });
    }
    
    // Filtres immédiats
    const statusFilter = document.getElementById('statusFilter');
    const prestationFilter = document.getElementById('prestationFilter');

    if (statusFilter) {
        statusFilter.addEventListener('change', applyFilters);
    }
    if (prestationFilter) {
        prestationFilter.addEventListener('change', applyFilters);
    }

    // === GESTION DU CHAMP "AUTRE" STATUT ===
    // Pour le modal d'ajout de client
    const addStatutSelect = document.querySelector('#clientForm select[name="statut"]');
    if (addStatutSelect) {
        addStatutSelect.addEventListener('change', function() {
            const autreField = document.getElementById('addAutreStatutField');
            const autreInput = document.getElementById('addStatutAutre');

            if (this.value === 'Autre') {
                autreField.style.display = 'block';
                autreInput.required = true;
            } else {
                autreField.style.display = 'none';
                autreInput.required = false;
                autreInput.value = '';
            }
        });
    }

    // Pour le modal de modification de client
    const editStatutSelect = document.getElementById('editClientStatut');
    if (editStatutSelect) {
        editStatutSelect.addEventListener('change', function() {
            const autreField = document.getElementById('editAutreStatutField');
            const autreInput = document.getElementById('editStatutAutre');

            if (this.value === 'Autre') {
                autreField.style.display = 'block';
                autreInput.required = true;
            } else {
                autreField.style.display = 'none';
                autreInput.required = false;
                autreInput.value = '';
            }
        });
    }
});

// === GESTION DU CHAMP "AUTRE" POUR LE MODAL QUICK CHANGE ===
// Cette fonction sera appelée dynamiquement lors de l'ouverture du modal quick change
function initQuickStatutAutreField() {
    const quickStatutSelect = document.getElementById('quickStatutSelect');
    if (quickStatutSelect) {
        quickStatutSelect.addEventListener('change', function() {
            const autreField = document.getElementById('quickAutreStatutField');
            const autreInput = document.getElementById('quickStatutAutre');

            if (this.value === 'Autre') {
                autreField.style.display = 'block';
                autreInput.required = true;
            } else {
                autreField.style.display = 'none';
                autreInput.required = false;
                autreInput.value = '';
            }
        });

        // Vérifier l'état initial au cas où "Autre" serait déjà sélectionné
        if (quickStatutSelect.value === 'Autre') {
            document.getElementById('quickAutreStatutField').style.display = 'block';
            document.getElementById('quickStatutAutre').required = true;
        }
    }
}

function applyFilters() {
    const search = document.getElementById('clientSearch')?.value || '';
    const statut = document.getElementById('statusFilter')?.value || '';
    const prestation = document.getElementById('prestationFilter')?.value || '';

    console.log('🔍 Application des filtres:', { search, statut, prestation });
    loadClients(search, statut, prestation);
}

        function showAddClientModal() {
            document.getElementById('clientModal').classList.remove('hidden');
        }

        function closeClientModal() {
            document.getElementById('clientModal').classList.add('hidden');
            document.getElementById('clientForm').reset();
            // Masquer le champ "Autre" statut
            document.getElementById('addAutreStatutField').style.display = 'none';
            document.getElementById('addStatutAutre').value = '';
        }

    async function saveClient(event) {
    event.preventDefault();
    const form = document.getElementById('clientForm');
    const formData = new FormData(form);
    
    // ✅ CONVERTIR FormData EN OBJET
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });
    
    // ✅ GESTION DU CHAMP PERSONNALISÉ POUR CATÉGORIE "AUTRES"
    const categorie = data.categorie;
    if (categorie === 'Autres') {
        const customPrestationInput = document.getElementById('customPrestationInput');
        if (customPrestationInput && customPrestationInput.value.trim()) {
            data.prestation = customPrestationInput.value.trim();
        }
    }

    // ✅ GESTION DU CHAMP PERSONNALISÉ POUR STATUT "AUTRE"
    if (data.statut === 'Autre') {
        const statutAutreInput = document.getElementById('addStatutAutre');
        const statutAutreValue = statutAutreInput?.value?.trim();

        if (!statutAutreValue) {
            showToast('❌ Veuillez préciser le statut personnalisé', 'error');
            statutAutreInput?.focus();
            return;
        }

        data.statut = statutAutreValue;
    }

    // ✅ VALIDATION CÔTÉ CLIENT
    if (!data.nom || !data.contact || !data.prestation || !data.statut) {
        showToast('❌ Veuillez remplir tous les champs obligatoires', 'error');
        return;
    }
    
    console.log('📤 Envoi des données client:', data);
    
    try {
        const response = await fetch(`${API_BASE}/clients`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        console.log('📥 Statut réponse:', response.status);
        
        const result = await response.json();
        console.log('📋 Résultat:', result);
        
        if (result.success) {
            showToast(result.message || '✅ Client créé avec succès.', 'success');
            closeClientModal();
            await loadClients();
        } else {
            // Afficher les erreurs de validation
            if (result.errors) {
                const errorMessages = Object.values(result.errors).flat().join('\n');
                showToast('❌ Erreurs de validation:\n' + errorMessages, 'error');
            } else {
                showToast('❌ ' + (result.error || 'Erreur lors de la création'), 'error');
            }
        }
    } catch (error) {
        console.error('❌ Erreur complète:', error);
        showToast('❌ Erreur de connexion: ' + error.message, 'error');
    }
}
   async function viewClient(clientId) {
    const client = currentClients.find(c => c.id === clientId);
    if (!client) return;
    
    selectedClient = client;
    
    try {
        // Charger les factures
        const invoiceResponse = await fetch(`${API_BASE}/clients/${clientId}/invoices`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });
        const invoiceData = await invoiceResponse.json();
        const invoices = invoiceData.success ? invoiceData.invoices : [];
        
        // Charger les commentaires
        const commentaireResponse = await fetch(`${API_BASE}/clients/${clientId}/commentaires`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });
        const commentaireData = await commentaireResponse.json();
        const commentaires = commentaireData.success ? commentaireData.commentaires : [];
        
        // Calculer les statistiques factures
        const totalInvoiced = invoices.reduce((sum, inv) => sum + parseFloat(inv.amount || 0), 0);
        const totalPaid = invoices.reduce((sum, inv) => sum + parseFloat(inv.paid_amount || 0), 0);
        const totalRemaining = totalInvoiced - totalPaid;
        
        // Section statistiques
        let statsHtml = '';
        if (invoices.length > 0) {
            statsHtml = `
                <div style="margin-top: 1.5rem; padding: 1rem; background: var(--bg-secondary); border-radius: 8px;">
                    <h4 class="font-semibold mb-3">Statistiques de Facturation</h4>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <div class="text-xs text-secondary">Total Facturé</div>
                            <div class="font-semibold text-primary">${formatCurrency(totalInvoiced)}</div>
                        </div>
                        <div>
                            <div class="text-xs text-secondary">Total Payé</div>
                            <div class="font-semibold text-success">${formatCurrency(totalPaid)}</div>
                        </div>
                        <div>
                            <div class="text-xs text-secondary">Reste à Payer</div>
                            <div class="font-semibold text-${totalRemaining > 0 ? 'warning' : 'success'}">${formatCurrency(totalRemaining)}</div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Section historique commentaires
        let commentairesHtml = '';
        if (commentaires.length > 0) {
            commentairesHtml = `
                <div style="margin-top: 1.5rem; padding: 1rem; background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%); border-left: 4px solid #8b5cf6; border-radius: 8px;">
                    <h4 class="font-semibold mb-3">💬 Historique des Commentaires (${commentaires.length})</h4>
                    <div style="max-height: 300px; overflow-y: auto;">
                        ${commentaires.map(com => `
                            <div style="padding: 0.75rem; background: white; border-radius: 6px; margin-bottom: 0.5rem; border-left: 3px solid #8b5cf6;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                    <div>
                                        <span class="font-semibold" style="color: #8b5cf6;">${com.agent_name}</span>
                                    </div>
                                    <div class="text-xs text-secondary">${formatDate(com.created_at)}</div>
                                </div>
                                <div class="text-sm" style="color: #475569;">${com.commentaire}</div>
                            </div>
                        `).join('')}
                    </div>
                    <button class="btn btn-sm btn-primary w-full mt-3" onclick="showAddCommentaireModal(${client.id})">
                        ➕ Ajouter un commentaire
                    </button>
                </div>
            `;
        } else {
            commentairesHtml = `
                <div style="margin-top: 1.5rem; padding: 1rem; background: var(--bg-secondary); border-radius: 8px; text-align: center;">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">💬</div>
                    <p class="text-secondary">Aucun commentaire pour ce client</p>
                    <button class="btn btn-sm btn-primary mt-2" onclick="showAddCommentaireModal(${client.id})">
                        ➕ Ajouter le premier commentaire
                    </button>
                </div>
            `;
        }
        
        // Section factures (conservée)
        let invoicesHtml = '';
        if (invoices.length > 0) {
            invoicesHtml = `
                <div style="margin-top: 1.5rem;">
                    <h4 class="font-semibold mb-2">Factures du Client (${invoices.length})</h4>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>N° FACTURE</th>
                                    <th>SERVICE</th>
                                    <th>MONTANT</th>
                                    <th>PAYÉ</th>
                                    <th>RESTANT</th>
                                    <th>STATUT</th>
                                    <th>ÉCHÉANCE</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${invoices.map(inv => {
                                    const remaining = inv.amount - inv.paid_amount;
                                    const isOverdue = new Date(inv.due_date) < new Date() && inv.status !== 'paid';
                                    
                                    return `
                                        <tr>
                                            <td class="font-semibold">${inv.number}</td>
                                            <td class="text-sm">${inv.service}</td>
                                            <td>${formatCurrency(inv.amount)}</td>
                                            <td class="text-success">${formatCurrency(inv.paid_amount)}</td>
                                            <td class="text-${remaining > 0 ? 'warning' : 'success'}">${formatCurrency(remaining)}</td>
                                            <td>
                                                <span class="badge badge-${inv.status === 'paid' ? 'success' : isOverdue ? 'danger' : 'warning'}">
                                                    ${inv.status === 'paid' ? 'PAYÉ' : isOverdue ? 'EN RETARD' : inv.status === 'partial' ? 'PARTIEL' : 'EN ATTENTE'}
                                                </span>
                                            </td>
                                            <td class="text-sm ${isOverdue ? 'text-danger' : ''}">${formatDate(inv.due_date)}</td>
                                            <td>
                                                <div class="flex gap-1">
                                                    <button class="btn btn-sm btn-info" onclick="viewInvoiceFromClient(${inv.id})" title="Voir">👁️</button>
                                                    <!-- PAIEMENT DÉSACTIVÉ : Les paiements se font maintenant dans la caisse -->
                                                    <button class="btn btn-sm btn-primary" onclick="printInvoice(${inv.id})" title="Imprimer">🖨️</button>
                                                </div>
                                            </td>
                                        </tr>
                                    `;
                                }).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
        }
        
        // Détails complets
        const details = `
            <div style="line-height: 2;">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p><strong>UID:</strong> ${client.uid}</p>
                        <p><strong>Nom:</strong> ${client.nom} ${client.prenoms || ''}</p>
                        <p><strong>Contact:</strong> ${client.contact}</p>
                        <p><strong>Email:</strong> ${client.email || 'Non renseigné'}</p>
                    </div>
                    <div>
                        <p><strong>Média:</strong> <span class="badge badge-info">${client.media || 'Non renseigné'}</span></p>
                        <p><strong>Prestation:</strong> ${client.prestation}</p>
                        <p><strong>Montant Facture:</strong> ${formatCurrency(client.budget)}</p>
                        <p><strong>Statut:</strong> 
                            <span class="badge ${getStatusBadgeClass(client.statut)}">${client.statut}</span>
                        </p>
                    </div>
                </div>
                <div style="margin-top: 1rem;">
                    <p><strong>Agent:</strong> ${client.agent}</p>
                    <p><strong>Date:</strong> ${formatDate(client.date_creation)}</p>
                    ${client.commentaire ? `<p><strong>Commentaire initial:</strong> ${client.commentaire}</p>` : ''}
                </div>
                
                ${commentairesHtml}
                ${statsHtml}
                ${invoicesHtml}
            </div>
        `;
        
        document.getElementById('clientDetails').innerHTML = details;
        document.getElementById('viewClientModal').classList.remove('hidden');
        
    } catch (error) {
        console.error('Erreur chargement détails client:', error);
        showToast('Erreur de chargement', 'error');
    }
}

        function closeViewClientModal() {
            document.getElementById('viewClientModal').classList.add('hidden');
            selectedClient = null;
        }

        function createInvoiceForClient() {
            if (selectedClient) {
                closeViewClientModal();
                quickInvoiceForClient(selectedClient.id);
            }
        }

        async function quickInvoiceForClient(clientId) {
            const client = currentClients.find(c => c.id === clientId);
            if (!client) {
                console.error('Client non trouvé avec ID:', clientId);
                return;
            }

            currentClientForInvoice = client;

            // Charger tous les clients
            await loadClientsForSelect();

            // Pré-remplir automatiquement le client sélectionné
            const clientName = `${client.nom || ''} ${client.prenoms || ''}`.trim();
            selectClient(client.id, clientName);

            // Pré-remplir la date d'échéance (30 jours)
            const dueDate = new Date();
            dueDate.setDate(dueDate.getDate() + 30);
            const dueDateInput = document.querySelector('#invoiceForm [name="due_date"]');
            if (dueDateInput) dueDateInput.value = dueDate.toISOString().split('T')[0];

            showCreateInvoiceModal();
        }

        async function deleteClient(id) {
    // Vérifier d'abord les permissions
    if (!userActionPermissions.delete_clients) {
        showToast('❌ Vous n\'avez pas la permission de supprimer des clients', 'error');
        return;
    }
    
    if (!confirm('⚠️ Supprimer ce client ?\n\nCette action est irréversible.')) return;
    
    try {
        const response = await fetch(`${API_BASE}/clients/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });
        
        if (response.status === 403) {
            showToast('❌ Accès refusé : Vous n\'avez pas la permission de supprimer des clients', 'error');
            return;
        }
        
        const data = await response.json();
        
        if (data.success) {
            showToast('✅ Client supprimé', 'success');
            loadClients();
        } else {
            showToast(data.error || '❌ Erreur', 'error');
        }
    } catch (error) {
        showToast('❌ Erreur', 'error');
    }
}

        function exportClients() {
            showToast('Export en cours...', 'info');
        }

        // === INVOICES ===
        async function loadInvoices(filters = {}) {
            try {
                // Construire l'URL avec les paramètres de recherche
                const params = new URLSearchParams();
                
                if (filters.search) params.append('search', filters.search);
                if (filters.status) params.append('status', filters.status);
                if (filters.date_from) params.append('date_from', filters.date_from);
                if (filters.date_to) params.append('date_to', filters.date_to);
                
                const queryString = params.toString();
                const url = `${API_BASE}/invoices${queryString ? '?' + queryString : ''}`;
                
                const response = await fetch(url, {
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (data.success) {
                    renderInvoicesTable(data.invoices.data);
                    updateInvoiceStats(data.invoices.data);
                    await loadClientsForSelect();
                }
            } catch (error) {
                console.error('Erreur:', error);
                showToast('Erreur de chargement des factures', 'error');
            }
        }

        // ✅ NOUVELLE FONCTION : Rechercher les factures
        function searchInvoices() {
            const filters = {
                search: document.getElementById('invoiceSearch').value,
                status: document.getElementById('invoiceStatusFilter').value,
                date_from: document.getElementById('invoiceDateFrom').value,
                date_to: document.getElementById('invoiceDateTo').value
            };
            loadInvoices(filters);
        }

        // ✅ NOUVELLE FONCTION : Réinitialiser les filtres
        function resetInvoiceFilters() {
            document.getElementById('invoiceSearch').value = '';
            document.getElementById('invoiceStatusFilter').value = '';
            document.getElementById('invoiceDateFrom').value = '';
            document.getElementById('invoiceDateTo').value = '';
            loadInvoices();
        }

        // ✅ AJOUT : Recherche en temps réel lors de la saisie
        document.addEventListener('DOMContentLoaded', function() {
            const invoiceSearch = document.getElementById('invoiceSearch');
            if (invoiceSearch) {
                invoiceSearch.addEventListener('keyup', function(e) {
                    if (e.key === 'Enter') {
                        searchInvoices();
                    }
                });
            }
        });

        function updateInvoiceStats(invoices) {
            const total = invoices.length;
            const paid = invoices.filter(i => i.status === 'paid').length;
            const pending = invoices.filter(i => i.status === 'pending').length;
            const overdue = invoices.filter(i => i.status === 'overdue').length;
            
            document.getElementById('totalInvoices').textContent = total;
            document.getElementById('paidInvoices').textContent = paid;
            document.getElementById('pendingInvoices').textContent = pending;
            document.getElementById('overdueInvoices').textContent = overdue;
        }

        function renderInvoicesTable(invoices) {
    const tbody = document.getElementById('invoicesTableBody');
    if (!invoices || invoices.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-secondary">Aucune facture</td></tr>';
        return;
    }
    
    tbody.innerHTML = invoices.map(inv => {
        const remaining = inv.amount - inv.paid_amount;
        const isOverdue = new Date(inv.due_date) < new Date() && inv.status !== 'paid';
        
        // ✅ BOUTONS SELON PERMISSIONS
        const editButton = userActionPermissions.edit_invoices ?
            `<button class="btn btn-sm btn-warning" onclick="showEditInvoiceModal(${inv.id})" title="Modifier">✏️</button>` : '';

        const deleteButton = userActionPermissions.delete_invoices ?
            `<button class="btn btn-sm btn-danger" onclick="deleteInvoice(${inv.id})" title="Supprimer">🗑️</button>` : '';

        return `
            <tr>
                <td class="font-semibold">${inv.number}</td>
                <td>${inv.client_name}</td>
                <td>${inv.service}</td>
                <td>${formatCurrency(inv.amount)}</td>
                <td class="text-success">${formatCurrency(inv.paid_amount)}</td>
                <td class="${remaining > 0 ? 'text-warning' : 'text-success'}">${formatCurrency(remaining)}</td>
                <td>
                    <span class="badge badge-${inv.status === 'paid' ? 'success' : isOverdue ? 'danger' : 'warning'}">
                        ${inv.status === 'paid' ? 'PAYÉ' : isOverdue ? 'EN RETARD' : inv.status === 'partial' ? 'PARTIEL' : 'EN ATTENTE'}
                    </span>
                </td>
                <td class="${isOverdue ? 'text-danger' : ''}">${formatDate(inv.due_date)}</td>
                <td>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-info" onclick="viewInvoiceDetails(${inv.id})" title="Voir détails">👁️</button>
                        ${editButton}
                        <!-- PAIEMENT DÉSACTIVÉ : Les paiements se font maintenant dans la caisse -->
                        <button class="btn btn-sm btn-primary" onclick="printInvoice(${inv.id})" title="Imprimer">📄</button>
                        <button class="btn btn-sm btn-secondary" onclick="generateInvoiceLink(${inv.id})" title="Lien de Facturation">🔗</button>
                        ${remaining > 0 ? `
                            <button class="btn btn-sm btn-info" onclick="sendReminder(${inv.id})" title="Envoyer rappel">📧</button>
                        ` : ''}
                        ${deleteButton}
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

        // Variable globale pour stocker tous les clients
        let allClientsForInvoice = [];

        async function loadClientsForSelect() {
            try {
                const url = new URL(`${API_BASE}/clients`);
                url.searchParams.append('all', 'true'); // Charger TOUS les clients

                const response = await fetch(url, {
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (data.success) {
                    // Gérer les 2 formats possibles
                    if (Array.isArray(data.clients)) {
                        allClientsForInvoice = data.clients;
                    } else if (data.clients && data.clients.data && Array.isArray(data.clients.data)) {
                        allClientsForInvoice = data.clients.data;
                    }
                    console.log(`✅ ${allClientsForInvoice.length} clients chargés pour le modal de facturation`);
                    filterClients(''); // Afficher tous les clients initialement
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Fonction de normalisation du texte
        function normalizeText(text) {
            if (!text) return '';
            return text.toString()
                .toLowerCase()
                .normalize("NFD").replace(/[\u0300-\u036f]/g, "") // Enlever les accents
                .replace(/[^a-z0-9\s]/g, '') // Enlever les caractères spéciaux
                .trim()
                .replace(/\s+/g, ' '); // Normaliser les espaces
        }

        // Filtrer les clients selon la recherche
        function filterClients(searchTerm) {
            const searchNormalized = normalizeText(searchTerm);
            const dropdown = document.getElementById('clientDropdown');
            const list = document.getElementById('clientDropdownList');
            const countSpan = document.getElementById('clientResultsCount');

            let filteredClients = allClientsForInvoice;

            if (searchNormalized) {
                filteredClients = allClientsForInvoice.filter(client => {
                    const nom = normalizeText(client.nom || '');
                    const prenoms = normalizeText(client.prenoms || '');
                    const telephone = normalizeText(client.telephone || '');
                    const email = normalizeText(client.email || '');
                    const prestation = normalizeText(client.prestation || '');

                    return nom.includes(searchNormalized) ||
                           prenoms.includes(searchNormalized) ||
                           telephone.includes(searchNormalized) ||
                           email.includes(searchNormalized) ||
                           prestation.includes(searchNormalized);
                });
            }

            // Mettre à jour le compteur
            countSpan.textContent = `${filteredClients.length} client${filteredClients.length > 1 ? 's' : ''} trouvé${filteredClients.length > 1 ? 's' : ''}`;

            // Générer la liste
            if (filteredClients.length === 0) {
                list.innerHTML = '<div class="client-dropdown-empty">Aucun client trouvé</div>';
            } else {
                list.innerHTML = filteredClients.map(client => `
                    <div class="client-dropdown-item" onclick="selectClient(${client.id}, '${(client.nom || '').replace(/'/g, "\\'")} ${(client.prenoms || '').replace(/'/g, "\\'")}')">
                        <div class="client-dropdown-item-name">
                            ${client.nom || ''} ${client.prenoms || ''}
                        </div>
                        <div class="client-dropdown-item-details">
                            ${client.telephone ? `<span>📞 ${client.telephone}</span>` : ''}
                            ${client.prestation ? `<span>💼 ${client.prestation}</span>` : ''}
                        </div>
                    </div>
                `).join('');
            }

            dropdown.style.display = 'block';
        }

        // Afficher le dropdown
        function showClientDropdown() {
            const searchInput = document.getElementById('invoiceClientSearch');
            filterClients(searchInput.value);
        }

        // Sélectionner un client
        function selectClient(clientId, clientName) {
            const searchInput = document.getElementById('invoiceClientSearch');
            const clientIdInput = document.getElementById('invoiceClientId');
            const dropdown = document.getElementById('clientDropdown');

            searchInput.value = clientName;
            clientIdInput.value = clientId;
            dropdown.style.display = 'none';

            // Charger les données du client pour pré-remplir le formulaire
            const client = allClientsForInvoice.find(c => c.id === clientId);
            if (client) {
                currentClientForInvoice = client;
            }
        }

        // Fermer le dropdown si on clique en dehors
        document.addEventListener('click', function(event) {
            const searchInput = document.getElementById('invoiceClientSearch');
            const dropdown = document.getElementById('clientDropdown');

            if (searchInput && dropdown && !searchInput.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });

        function showCreateInvoiceModal() {
            selectedServices = [];
            renderServicesLines();
            document.getElementById('invoiceModal').classList.remove('hidden');
        }

        // Fermer le modal et réinitialiser
        function closeInvoiceModal() {
            const modal = document.getElementById('invoiceModal');
            if (modal) {
                modal.classList.add('hidden');
            }
            document.getElementById('invoiceForm').reset();
            document.getElementById('invoiceClientSearch').value = '';
            document.getElementById('invoiceClientId').value = '';
            document.getElementById('clientDropdown').style.display = 'none';
            selectedServices = [];
            renderServicesLines();
            currentClientForInvoice = null;
        }

        async function saveInvoice(event) {
    event.preventDefault();

    const clientId = document.getElementById('invoiceClientId').value;
    const categorie = document.getElementById('invoiceCategorie').value;
    const dueDate = document.querySelector('#invoiceForm [name="due_date"]').value;
    const notes = document.querySelector('#invoiceForm [name="notes"]').value;

    // Validations
    if (!clientId) {
        showToast('Veuillez sélectionner un client', 'error');
        return;
    }
    
    if (!categorie) {
        showToast('Veuillez sélectionner une catégorie', 'error');
        return;
    }
    
    if (selectedServices.length === 0) {
        showToast('Veuillez ajouter au moins un service', 'error');
        return;
    }
    
    const invalidServices = selectedServices.filter(s => !s.service || s.montant <= 0);
    if (invalidServices.length > 0) {
        showToast('Veuillez remplir tous les services et montants', 'error');
        return;
    }
    
    if (!dueDate) {
        showToast('Veuillez sélectionner une date d\'échéance', 'error');
        return;
    }
    
    // Calculer le montant total
    const totalAmount = selectedServices.reduce((sum, s) => sum + parseFloat(s.montant), 0);
    
    // Créer une description TEXTE des services (PAS de JSON)
    // Format: "Catégorie: XXX | Service 1 (montant), Service 2 (montant)"
    const servicesText = `Catégorie: ${categorie} | ${selectedServices.map(s => 
        `${s.service} (${new Intl.NumberFormat('fr-FR').format(s.montant)} FCFA)`
    ).join(', ')}`;
    
    // DONNÉES SIMPLES - seulement les colonnes qui existent en BDD
    const data = {
        client_id: parseInt(clientId),
        service: servicesText,  // Tout en texte simple
        amount: totalAmount,
        due_date: dueDate,
        notes: notes || ''
    };
    
    console.log('📤 Envoi des données:', data);
    
    try {
        const response = await fetch(`${API_BASE}/invoices`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        console.log('📥 Statut:', response.status);
        
        const result = await response.json();
        console.log('📋 Résultat:', result);
        
        if (result.success) {
            showToast('Facture créée avec succès', 'success');
            closeInvoiceModal();
            await loadInvoices();
        } else {
            console.error('Erreur:', result);
            showToast(result.error || result.message || 'Erreur de création', 'error');
        }
    } catch (error) {
        console.error('Erreur complète:', error);
        showToast('Erreur: ' + error.message, 'error');
    }
}
        

        async function recordPayment(invoiceId, remaining) {
    try {
        // Récupérer les détails de la facture
        const response = await fetch(`${API_BASE}/invoices/${invoiceId}`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });
        const data = await response.json();
        
        if (data.success) {
            const invoice = data.invoice;
            
            // Remplir le modal
            document.getElementById('paymentInvoiceId').value = invoice.id;
            document.getElementById('paymentInvoiceNumber').value = invoice.number;
            document.getElementById('paymentRemaining').value = formatCurrency(remaining);
            document.getElementById('paymentAmount').value = remaining;
            document.getElementById('paymentAmount').max = remaining;
            
            // Ouvrir le modal
            document.getElementById('paymentModal').classList.remove('hidden');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur de chargement', 'error');
    }
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    document.getElementById('paymentForm').reset();
}

async function submitPayment(event) {
    event.preventDefault();
    
    const invoiceId = document.getElementById('paymentInvoiceId').value;
    const amount = parseFloat(document.getElementById('paymentAmount').value);
    const method = document.getElementById('paymentMethod').value;
    const notes = document.getElementById('paymentNotes').value;
    
    try {
        const response = await fetch(`${API_BASE}/invoices/${invoiceId}/payment`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                amount: amount,
                payment_method: method,
                notes: notes
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Paiement enregistré avec succès', 'success');
            closePaymentModal();
            loadInvoices();
        } else {
            showToast(data.error || 'Erreur', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur de connexion', 'error');
    }
}
        // === FONCTIONS MANQUANTES POUR LES ACTIONS DE FACTURATION ===

// Voir les détails d'une facture
async function viewInvoiceDetails(invoiceId) {
    try {
        const response = await fetch(`${API_BASE}/invoices/${invoiceId}`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });
        const data = await response.json();
        
        if (data.success) {
            const inv = data.invoice;
            const remaining = inv.amount - inv.paid_amount;
            
            // Historique des paiements avec services
            let paymentsHtml = '';
            // Dans la fonction viewInvoiceDetails, section paiements :
if (inv.payments && inv.payments.length > 0) {
    // ✅ BOUTONS SELON PERMISSIONS
    const editPaymentButton = (paymentId) => userActionPermissions.edit_payments ? 
        `<button class="btn btn-sm btn-warning" onclick="showEditPaymentModal(${paymentId})" title="Modifier">✏️</button>` : '';
    
    const deletePaymentButton = (paymentId) => userActionPermissions.delete_payments ? 
        `<button class="btn btn-sm btn-danger" onclick="deletePayment(${paymentId})" title="Supprimer">🗑️</button>` : '';
    
    paymentsHtml = `
        <div style="margin-top: 1.5rem;">
            <h4 class="font-semibold mb-2">📋 Historique des Paiements</h4>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>DATE</th>
                            <th>MONTANT</th>
                            <th>MÉTHODE</th>
                            <th>NOTES</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${inv.payments.map(p => `
                            <tr>
                                <td>${formatDate(p.payment_date)}</td>
                                <td class="text-success font-semibold">${formatCurrency(p.amount)}</td>
                                <td><span class="badge badge-info">${p.payment_method || 'Espèces'}</span></td>
                                <td class="text-sm">${p.notes || '-'}</td>
                                <td>
                                    <div class="flex gap-1">
                                        ${editPaymentButton(p.id)}
                                        ${deletePaymentButton(p.id)}
                                    </div>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    `;
}
            
            // Section des services
            const servicesHtml = `
                <div style="margin-top: 1.5rem; padding: 1rem; background: var(--bg-secondary); border-radius: 8px;">
                    <h4 class="font-semibold mb-2">🎯 Service Principal</h4>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div class="font-semibold text-primary">${inv.service}</div>
                            <div class="text-sm text-secondary">Agent: ${inv.agent}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold text-primary">${formatCurrency(inv.amount)}</div>
                            <div class="text-xs text-success">Payé: ${formatCurrency(inv.paid_amount)}</div>
                        </div>
                    </div>
                </div>
            `;
            
            const details = `
                <div style="line-height: 2;">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p><strong>N° Facture:</strong> ${inv.number}</p>
                            <p><strong>Client:</strong> ${inv.client_name}</p>
                            <p><strong>Contact:</strong> ${inv.client?.contact || '-'}</p>
                            <p><strong>Email:</strong> ${inv.client?.email || '-'}</p>
                        </div>
                        <div>
                            <p><strong>Montant Total:</strong> <span class="text-primary font-semibold">${formatCurrency(inv.amount)}</span></p>
                            <p><strong>Montant Payé:</strong> <span class="text-success font-semibold">${formatCurrency(inv.paid_amount)}</span></p>
                            <p><strong>Restant:</strong> <span class="text-${remaining > 0 ? 'warning' : 'success'} font-semibold">${formatCurrency(remaining)}</span></p>
                            <p><strong>Statut:</strong> ${getInvoiceStatusBadge(inv.status, inv.due_date)}</p>
                        </div>
                    </div>
                    <div style="margin-top: 1rem;">
                        <p><strong>Date Création:</strong> ${formatDate(inv.created_at)}</p>
                        <p><strong>Date Échéance:</strong> ${formatDate(inv.due_date)}</p>
                        ${inv.notes ? `<p><strong>Notes:</strong> ${inv.notes}</p>` : ''}
                    </div>
                    ${servicesHtml}
                    ${paymentsHtml}
                </div>
            `;
            
            document.getElementById('invoiceDetails').innerHTML = details;
            window.currentInvoiceId = invoiceId;
            document.getElementById('viewInvoiceModal').classList.remove('hidden');
        } else {
            showToast(data.error || 'Erreur', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur de chargement', 'error');
    }
}

// Fonction pour générer le badge de statut de facture
function getInvoiceStatusBadge(status, dueDate) {
    const isOverdue = new Date(dueDate) < new Date() && status !== 'paid';
    
    if (status === 'paid') {
        return '<span class="badge badge-success">PAYÉ ✓</span>';
    } else if (isOverdue) {
        return '<span class="badge badge-danger">EN RETARD ⚠️</span>';
    } else if (status === 'partial') {
        return '<span class="badge badge-warning">PARTIEL</span>';
    } else {
        return '<span class="badge badge-info">EN ATTENTE</span>';
    }
}

function closeEditInvoiceModal() {
    document.getElementById('editInvoiceModal').classList.add('hidden');
    document.getElementById('editInvoiceForm').reset();
}

async function showEditInvoiceModal(invoiceId) {
    // Vérifier d'abord les permissions
    if (!userActionPermissions.edit_invoices) {
        showToast('❌ Vous n\'avez pas la permission de modifier les factures', 'error');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/invoices/${invoiceId}`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });

        if (response.status === 403) {
            showToast('❌ Accès refusé : Vous n\'avez pas la permission de modifier cette facture', 'error');
            return;
        }

        const data = await response.json();

        if (data.success) {
            const invoice = data.invoice;

            // Vérifier si la facture est validée par le client
            if (invoice.client_validated_at) {
                showToast('❌ Impossible de modifier une facture déjà validée par le client', 'error');
                return;
            }

            // Remplir le formulaire
            document.getElementById('editInvoiceId').value = invoice.id;
            document.getElementById('editInvoiceClientId').value = invoice.client_id;
            document.getElementById('editInvoiceService').value = invoice.service;
            document.getElementById('editInvoiceAmount').value = invoice.amount;
            document.getElementById('editInvoiceDueDate').value = invoice.due_date;
            document.getElementById('editInvoiceNotes').value = invoice.notes || '';

            // Afficher le modal
            document.getElementById('editInvoiceModal').classList.remove('hidden');
        } else {
            showToast('❌ Erreur : ' + (data.error || 'Impossible de charger la facture'), 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('❌ Erreur de connexion au serveur', 'error');
    }
}

async function updateInvoice(event) {
    event.preventDefault();

    const invoiceId = document.getElementById('editInvoiceId').value;
    const data = {
        client_id: document.getElementById('editInvoiceClientId').value,
        service: document.getElementById('editInvoiceService').value,
        amount: document.getElementById('editInvoiceAmount').value,
        due_date: document.getElementById('editInvoiceDueDate').value,
        notes: document.getElementById('editInvoiceNotes').value
    };

    try {
        const response = await fetch(`${API_BASE}/invoices/${invoiceId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showToast('✅ Facture mise à jour avec succès', 'success');
            closeEditInvoiceModal();
            loadInvoices();
        } else {
            const errorMsg = result.error || result.errors ?
                (typeof result.errors === 'object' ? Object.values(result.errors).flat().join(', ') : result.errors) :
                'Erreur lors de la mise à jour';
            showToast(errorMsg, 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('❌ Erreur de connexion au serveur', 'error');
    }
}

// Générer le lien de facturation
async function generateInvoiceLink(invoiceId) {
    try {
        const response = await fetch(`${API_BASE}/invoices/${invoiceId}/generate-link`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            // Créer une modal pour afficher le lien
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.style.display = 'flex';
            modal.style.zIndex = '10000';

            const modalContent = document.createElement('div');
            modalContent.className = 'modal-content';

            const modalHeader = document.createElement('div');
            modalHeader.className = 'modal-header';

            const modalTitle = document.createElement('h3');
            modalTitle.className = 'modal-title';
            modalTitle.textContent = '🔗 Lien de Facturation Généré';

            const closeBtn = document.createElement('button');
            closeBtn.className = 'close-btn';
            closeBtn.textContent = '×';
            closeBtn.onclick = () => modal.remove();

            modalHeader.appendChild(modalTitle);
            modalHeader.appendChild(closeBtn);

            const modalBody = document.createElement('div');
            modalBody.style.marginTop = '1rem';

            const description = document.createElement('div');
            description.style.marginBottom = '1rem';
            description.innerHTML = `
                <p style="margin-bottom: 1rem; font-weight: 500;">Cher(e) Candidat(e),</p>
                <p style="margin-bottom: 1rem;">Pour finaliser l'activation de votre dossier, veuillez suivre attentivement les étapes suivantes :</p>
                <ol style="margin-left: 1.5rem; margin-bottom: 1rem; line-height: 1.8;">
                    <li>Accéder au lien de facturation ci-dessous</li>
                    <li>Valider votre facture (lire et accepter les conditions générales)</li>
                    <li>Signer électroniquement le document</li>
                </ol>
                <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 4px;">
                    <strong>⚠️ Important :</strong> La validation et la signature sont obligatoires pour le traitement de votre dossier.
                </div>
                <p style="margin-top: 1rem; font-style: italic; color: #1e3c72; font-weight: 500;">PSI AFRICA – Votre projet, notre engagement.</p>
            `;

            const linkContainer = document.createElement('div');
            linkContainer.style.cssText = 'background: #f8fafc; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; word-break: break-all; border: 1px solid #e2e8f0;';

            const linkLabel = document.createElement('strong');
            linkLabel.textContent = 'Lien:';

            const linkBreak = document.createElement('br');

            const linkElement = document.createElement('a');
            linkElement.href = data.url;
            linkElement.target = '_blank';
            linkElement.style.color = 'var(--primary)';
            linkElement.textContent = data.url;

            linkContainer.appendChild(linkLabel);
            linkContainer.appendChild(linkBreak);
            linkContainer.appendChild(linkElement);

            const copyBtn = document.createElement('button');
            copyBtn.className = 'btn btn-primary w-full';
            copyBtn.innerHTML = '📋 Copier le message et le lien';
            copyBtn.setAttribute('data-url', data.url);
            copyBtn.style.marginTop = '1rem';
            copyBtn.onclick = function() {
                const url = this.getAttribute('data-url');
                const fullMessage = `Cher(e) Candidat(e),

Pour finaliser l'activation de votre dossier, veuillez suivre attentivement les étapes suivantes :

1. Accéder au lien de facturation ci-dessous
2. Valider votre facture (lire et accepter les conditions générales)
3. Signer électroniquement le document

⚠️ Important : La validation et la signature sont obligatoires pour le traitement de votre dossier.

Lien de facturation :
${url}

PSI AFRICA – Votre projet, notre engagement.`;
                copyInvoiceLinkToClipboard(fullMessage, this);
            };

            modalBody.appendChild(description);
            modalBody.appendChild(linkContainer);
            modalBody.appendChild(copyBtn);
            modalContent.appendChild(modalHeader);
            modalContent.appendChild(modalBody);
            modal.appendChild(modalContent);

            document.body.appendChild(modal);
            showToast('✅ Lien généré avec succès', 'success');
        } else {
            showToast('❌ ' + (data.error || 'Erreur lors de la génération du lien'), 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('❌ Erreur lors de la génération du lien', 'error');
    }
}

// Générer le lien de facturation unique (toutes les factures et paiements du client)
async function generateClientPortalLink(clientId) {
    try {
        const response = await fetch(`${API_BASE}/clients/${clientId}/generate-portal-link`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            // Créer une modal pour afficher le lien
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.style.display = 'flex';
            modal.style.zIndex = '10000';

            const modalContent = document.createElement('div');
            modalContent.className = 'modal-content';

            const modalHeader = document.createElement('div');
            modalHeader.className = 'modal-header';

            const modalTitle = document.createElement('h3');
            modalTitle.className = 'modal-title';
            modalTitle.textContent = '🔗 Lien de Facturation Généré';

            const closeBtn = document.createElement('button');
            closeBtn.className = 'close-btn';
            closeBtn.textContent = '×';
            closeBtn.onclick = () => modal.remove();

            modalHeader.appendChild(modalTitle);
            modalHeader.appendChild(closeBtn);

            const modalBody = document.createElement('div');
            modalBody.style.marginTop = '1rem';

            const description = document.createElement('div');
            description.style.marginBottom = '1rem';
            description.innerHTML = `
                <p style="margin-bottom: 1rem; font-weight: 500;">Cher(e) Candidat(e),</p>
                <p style="margin-bottom: 1rem;">Pour finaliser l'activation de votre dossier, veuillez suivre attentivement les étapes suivantes :</p>
                <ol style="margin-left: 1.5rem; margin-bottom: 1rem; line-height: 1.8;">
                    <li>Accéder au lien de facturation ci-dessous</li>
                    <li>Valider votre facture (lire et accepter les conditions générales)</li>
                    <li>Signer électroniquement le document</li>
                </ol>
                <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 4px;">
                    <strong>⚠️ Important :</strong> La validation et la signature sont obligatoires pour le traitement de votre dossier.
                </div>
                <p style="margin-top: 1rem; font-style: italic; color: #1e3c72; font-weight: 500;">PSI AFRICA – Votre projet, notre engagement.</p>
            `;

            const linkContainer = document.createElement('div');
            linkContainer.style.cssText = 'background: #f8fafc; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; word-break: break-all; border: 1px solid #e2e8f0;';

            const linkLabel = document.createElement('strong');
            linkLabel.textContent = 'Lien de Facturation:';

            const linkBreak = document.createElement('br');

            const linkElement = document.createElement('a');
            linkElement.href = data.url;
            linkElement.target = '_blank';
            linkElement.style.color = 'var(--primary)';
            linkElement.textContent = data.url;

            linkContainer.appendChild(linkLabel);
            linkContainer.appendChild(linkBreak);
            linkContainer.appendChild(linkElement);

            const copyBtn = document.createElement('button');
            copyBtn.className = 'btn btn-primary w-full';
            copyBtn.innerHTML = '📋 Copier le message et le lien';
            copyBtn.setAttribute('data-url', data.url);
            copyBtn.style.marginTop = '1rem';
            copyBtn.onclick = function() {
                const url = this.getAttribute('data-url');
                const fullMessage = `Cher(e) Candidat(e),

Pour finaliser l'activation de votre dossier, veuillez suivre attentivement les étapes suivantes :

1. Accéder au lien de facturation ci-dessous
2. Valider votre facture (lire et accepter les conditions générales)
3. Signer électroniquement le document

⚠️ Important : La validation et la signature sont obligatoires pour le traitement de votre dossier.

Lien de facturation :
${url}

PSI AFRICA – Votre projet, notre engagement.`;
                copyInvoiceLinkToClipboard(fullMessage, this);
            };

            modalBody.appendChild(description);
            modalBody.appendChild(linkContainer);
            modalBody.appendChild(copyBtn);
            modalContent.appendChild(modalHeader);
            modalContent.appendChild(modalBody);
            modal.appendChild(modalContent);

            document.body.appendChild(modal);
            showToast('✅ Lien de facturation généré avec succès', 'success');
        } else {
            showToast('❌ ' + (data.error || 'Erreur lors de la génération du lien'), 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('❌ Erreur lors de la génération du lien', 'error');
    }
}

// Fonction pour copier le lien de facturation dans le presse-papiers
function copyInvoiceLinkToClipboard(text, button) {
    if (!navigator.clipboard) {
        // Fallback pour les navigateurs plus anciens
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            document.execCommand('copy');
            const originalText = button.innerHTML;
            button.innerHTML = '✅ Copié !';
            button.disabled = true;
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            }, 2000);
            showToast('✅ Message et lien copiés dans le presse-papiers', 'success');
        } catch (err) {
            console.error('Erreur de copie:', err);
            showToast('❌ Erreur lors de la copie', 'error');
        }

        document.body.removeChild(textArea);
        return;
    }

    navigator.clipboard.writeText(text).then(() => {
        const originalText = button.innerHTML;
        button.innerHTML = '✅ Copié !';
        button.disabled = true;
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        }, 2000);
        showToast('✅ Lien copié dans le presse-papiers', 'success');
    }).catch(err => {
        console.error('Erreur de copie:', err);
        showToast('❌ Erreur lors de la copie', 'error');
    });
}

// Supprimer une facture
async function deleteInvoice(invoiceId) {
    // Vérifier d'abord les permissions
    if (!userActionPermissions.delete_invoices) {
        showToast('❌ Vous n\'avez pas la permission de supprimer des factures', 'error');
        return;
    }
    
    if (!confirm('⚠️ ATTENTION : Supprimer cette facture ?\n\nCette action est irréversible.')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/invoices/${invoiceId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        });
        
        if (response.status === 403) {
            showToast('❌ Accès refusé : Vous n\'avez pas la permission de supprimer des factures', 'error');
            return;
        }
        
        const data = await response.json();
        
        if (data.success) {
            showToast('✅ Facture supprimée avec succès', 'success');
            loadInvoices();
        } else {
            showToast('❌ ' + (data.error || 'Erreur'), 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('❌ Erreur de suppression', 'error');
    }
}

// Imprimer une facture
async function printInvoice(invoiceId) {
    try {
        const response = await fetch(`${API_BASE}/invoices/${invoiceId}/print`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Créer une fenêtre d'impression
            const printWindow = window.open('', '_blank');
            const inv = data.invoice;
            
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Facture ${inv.number}</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 40px; }
                        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #2563eb; padding-bottom: 20px; }
                        .info { display: flex; justify-content: space-between; margin-bottom: 30px; }
                        .section { margin-bottom: 20px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
                        th { background-color: #f8fafc; font-weight: bold; }
                        .total { font-size: 1.5em; font-weight: bold; text-align: right; margin-top: 20px; }
                        .footer { margin-top: 50px; text-align: center; font-size: 0.9em; color: #666; }
                        @media print { 
                            body { padding: 20px; }
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>PSI AFRICA</h1>
                        <p>Système de Gestion Intégré</p>
                    </div>
                    
                    <div class="info">
                        <div>
                            <h3>FACTURE ${inv.number}</h3>
                            <p>Date: ${formatDate(inv.created_at)}</p>
                            <p>Échéance: ${formatDate(inv.due_date)}</p>
                        </div>
                        <div>
                            <h3>CLIENT</h3>
                            <p><strong>${inv.client_name}</strong></p>
                            <p>${inv.client ? inv.client.contact : ''}</p>
                            <p>${inv.client ? inv.client.email || '' : ''}</p>
                        </div>
                    </div>
                    
                    <div class="section">
                        <table>
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Agent</th>
                                    <th style="text-align: right;">Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>${inv.service}</td>
                                    <td>${inv.agent}</td>
                                    <td style="text-align: right;">${formatCurrency(inv.amount)}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="total">
                        <p>Montant Total: ${formatCurrency(inv.amount)}</p>
                        <p style="color: #16a34a;">Montant Payé: ${formatCurrency(inv.paid_amount)}</p>
                        <p style="color: ${inv.amount - inv.paid_amount > 0 ? '#f59e0b' : '#16a34a'};">
                            Reste à Payer: ${formatCurrency(inv.amount - inv.paid_amount)}
                        </p>
                    </div>
                    
                    ${inv.notes ? `<div class="section"><strong>Notes:</strong><br>${inv.notes}</div>` : ''}
                    
                    <div class="footer">
                        <p>Merci pour votre confiance</p>
                        <p>PSI AFRICA - Votre partenaire de confiance</p>
                    </div>
                    
                    <div class="no-print" style="margin-top: 30px; text-align: center;">
                        <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer;">
                            Imprimer
                        </button>
                        <button onclick="window.close()" style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
                            Fermer
                        </button>
                    </div>
                </body>
                </html>
            `);
            
            printWindow.document.close();
            
            showToast('Facture prête pour impression', 'success');
        } else {
            showToast(data.error || 'Erreur', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur d\'impression', 'error');
    }
}

        

        // === RECOVERY ===
        async function loadRecovery() {
            try {
                const response = await fetch(`${API_BASE}/recovery`, {
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (data.success) {
                    renderRecoveryData(data);
                }
            } catch (error) {
                showToast('Erreur de chargement', 'error');
            }
        }

        function renderRecoveryData(data) {
    // Calcul correct du taux de recouvrement
    // Taux = Montant récupéré / (Montant récupéré + Montant encore en retard) * 100
    const totalDue = data.total_overdue + (data.total_recovered || 0);
    const recoveryRate = totalDue > 0 ? 
        (((data.total_recovered || 0) / totalDue) * 100).toFixed(1) : 
        0;
    
    document.getElementById('recoveryContent').innerHTML = `
        <div class="stats-grid mb-6">
            <div class="stat-card">
                <div class="stat-value text-danger">${formatCurrency(data.total_overdue)}</div>
                <div class="stat-label">Montant en Retard</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-warning">${data.count}</div>
                <div class="stat-label">Factures en Retard</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-info">${recoveryRate}%</div>
                <div class="stat-label">Taux de Recouvrement</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-success">${formatCurrency(data.total_recovered || 0)}</div>
                <div class="stat-label">Récupéré ce mois</div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3>⚡ Actions de Recouvrement</h3>
                ${data.overdue_invoices.length > 0 ? `
                    <button class="btn btn-warning" onclick="sendBulkReminders()">
                        📧 Rappel Groupé (${data.overdue_invoices.length})
                    </button>
                ` : ''}
            </div>
            
            ${data.overdue_invoices.length > 0 ? `
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>N° FACTURE</th>
                                <th>CLIENT</th>
                                <th>CONTACT</th>
                                <th>MONTANT RESTANT</th>
                                <th>ÉCHÉANCE</th>
                                <th>RETARD</th>
                                <th>RAPPELS</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.overdue_invoices.map(inv => {
                                const remaining = inv.amount - inv.paid_amount;
                                const daysOverdue = Math.floor((new Date() - new Date(inv.due_date)) / (1000 * 60 * 60 * 24));
                                const urgency = daysOverdue > 60 ? 'danger' : daysOverdue > 30 ? 'warning' : 'info';
                                
                                return `
                                    <tr>
                                        <td>
                                            <div class="font-semibold">${inv.number}</div>
                                            <div class="text-xs text-secondary">${inv.service}</div>
                                        </td>
                                        <td>
                                            <div class="font-semibold">${inv.client_name}</div>
                                            <div class="text-xs text-secondary">${inv.client_email || ''}</div>
                                        </td>
                                        <td class="text-sm">${inv.client_contact || '-'}</td>
                                        <td>
                                            <div class="font-semibold text-danger">${formatCurrency(remaining)}</div>
                                            <div class="text-xs text-secondary">sur ${formatCurrency(inv.amount)}</div>
                                        </td>
                                        <td class="text-sm">${formatDate(inv.due_date)}</td>
                                        <td>
                                            <span class="badge badge-${urgency}">
                                                ${daysOverdue} jour${daysOverdue > 1 ? 's' : ''}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-info">${inv.reminders_count || 0}</span>
                                            ${inv.last_reminder_at ? `<div class="text-xs text-secondary">${formatDate(inv.last_reminder_at)}</div>` : ''}
                                        </td>
                                        <td>
                                            <div class="flex gap-2">
                                                <!-- PAIEMENT DÉSACTIVÉ : Les paiements se font maintenant dans la caisse -->
                                                <button class="btn btn-sm btn-info" onclick="sendReminder(${inv.id})" title="Rappel">📧</button>
                                                <button class="btn btn-sm btn-primary" onclick="viewInvoiceDetails(${inv.id})" title="Détails">👁️</button>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4 text-sm text-secondary">
                    💡 <strong>Actions recommandées :</strong>
                    <ul style="margin-left: 1.5rem; margin-top: 0.5rem;">
                        ${data.overdue_invoices.filter(i => Math.floor((new Date() - new Date(i.due_date)) / (1000 * 60 * 60 * 24)) > 60).length > 0 ? 
                            `<li>⚠️ <strong>${data.overdue_invoices.filter(i => Math.floor((new Date() - new Date(i.due_date)) / (1000 * 60 * 60 * 24)) > 60).length}</strong> facture(s) en retard de plus de 60 jours - Action urgente requise</li>` : ''}
                        ${data.overdue_invoices.filter(i => (i.reminders_count || 0) === 0).length > 0 ? 
                            `<li>📧 <strong>${data.overdue_invoices.filter(i => (i.reminders_count || 0) === 0).length}</strong> facture(s) sans rappel envoyé</li>` : ''}
                        <li>📞 Privilégier les relances téléphoniques pour les montants supérieurs à 500 000 FCFA</li>
                    </ul>
                </div>
            ` : `
                <div class="text-center" style="padding: 3rem 0;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">🎉</div>
                    <h3 class="text-success font-bold mb-2">Excellent travail !</h3>
                    <p class="text-secondary">Aucune facture en retard à traiter.</p>
                    <p class="text-sm text-secondary mt-2">Toutes les factures sont à jour ou en cours de paiement.</p>
                </div>
            `}
        </div>
    `;
}

// Fonction pour envoyer des rappels groupés
async function sendBulkReminders() {
    if (!confirm('Envoyer un rappel à tous les clients avec des factures en retard ?')) return;
    
    try {
        const response = await fetch(`${API_BASE}/recovery/bulk-reminders`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        const data = await response.json();
        
        if (data.success) {
            showToast(`${data.count} rappel(s) envoyé(s) avec succès`, 'success');
            loadRecovery();
        } else {
            showToast(data.error || 'Erreur', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur d\'envoi', 'error');
    }
}

        // === PERFORMANCE ===
        async function loadPerformance() {
            try {
                const response = await fetch(`${API_BASE}/performance`, {
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (data.success) {
                    renderPerformanceData(data);
                } else {
                    document.getElementById('performanceContent').innerHTML = '<div class="text-center text-danger">Accès refusé</div>';
                }
            } catch (error) {
                showToast('Erreur', 'error');
            }
        }

       function renderPerformanceData(data) {
    const stats = data.stats || {};
    const agents = data.agent_performance || [];
    const monthly = data.monthly_revenue || [];
    
    console.log('📊 Données reçues:', data);
    
    document.getElementById('performanceContent').innerHTML = `
        <!-- Filtre période -->
        <div class="flex justify-between items-center mb-6">
            <div></div>
            <select class="form-control" style="width: 200px;" onchange="loadPerformance(this.value)">
                <option value="month">Ce mois</option>
                <option value="quarter">Ce trimestre</option>
                <option value="year">Cette année</option>
            </select>
        </div>

        <!-- KPIs Performance -->
        <div class="stats-grid mb-6">
            <div class="stat-card">
                <div class="stat-value text-primary">${formatCurrency(stats.total_revenue || 0)}</div>
                <div class="stat-label">Chiffre d'Affaires</div>
                <div class="text-xs text-success mt-2">+${stats.revenue_growth || 0}%</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-success">${(stats.conversion_rate || 0)}%</div>
                <div class="stat-label">Taux de Conversion</div>
                <div class="text-xs text-success mt-2">+${stats.conversion_growth || 0}%</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-info">${formatCurrency(stats.avg_deal_size || 0)}</div>
                <div class="stat-label">Taille Moyenne Affaire</div>
                <div class="text-xs text-success mt-2">+${stats.deal_growth || 0}%</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-warning">${stats.avg_sales_cycle || 0} j</div>
                <div class="stat-label">Cycle de Vente Moyen</div>
                <div class="text-xs text-danger mt-2">-${stats.cycle_improvement || 0} jours</div>
            </div>
        </div>

        ${agents.length > 0 ? `
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <!-- Performance par Agent -->
                <div class="card">
                    <h3>📊 Performance par Agent</h3>
                    <div style="position: relative; height: 350px; width: 100%;">
                        <canvas id="agentPerformanceChart"></canvas>
                    </div>
                </div>

                <!-- Évolution Mensuelle -->
                <div class="card">
                    <h3>💰 Évolution Mensuelle</h3>
                    <div style="position: relative; height: 350px; width: 100%;">
                        <canvas id="monthlyRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        ` : `
            <div class="card text-center" style="padding: 3rem 0;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">📊</div>
                <h3 class="font-bold mb-2">Aucune donnée de performance</h3>
                <p class="text-secondary mb-3">Les données apparaîtront lorsque vos agents auront :</p>
                <ul style="list-style: none; padding: 0; margin: 1rem 0;">
                    <li class="mb-2">✓ Créé au moins 1 client</li>
                    <li class="mb-2">✓ Généré au moins 1 facture</li>
                    <li class="mb-2">✓ Enregistré au moins 1 paiement</li>
                </ul>
                <p class="text-sm text-muted">Commencez dans l'onglet "Clients" 👥</p>
            </div>
        `}
    `;

    if (agents.length > 0) {
        setTimeout(() => {
            createAgentChart(agents);
            createMonthlyChart(monthly);
        }, 100);
    }
}
// Graphique Performance par Agent - AVEC HAUTEUR FIXE
function createAgentChart(agents) {
    const canvas = document.getElementById('agentPerformanceChart');
    if (!canvas) {
        console.error('❌ Canvas introuvable');
        return;
    }
    
    const filteredAgents = agents.filter(agent => {
        return agent.revenue > 0 || agent.total_clients > 0 || agent.total_invoices > 0;
    });
    
    if (filteredAgents.length === 0) {
        canvas.parentElement.innerHTML = `
            <div class="text-center text-secondary" style="padding: 3rem 0;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📊</div>
                <p>Aucune donnée disponible</p>
            </div>
        `;
        return;
    }
    
    const ctx = canvas.getContext('2d');
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: filteredAgents.map(a => a.name),
            datasets: [{
                label: "CA (FCFA)",
                data: filteredAgents.map(a => a.revenue || 0),
                backgroundColor: '#2563eb',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // IMPORTANT !
            plugins: {
                legend: { display: true, position: 'top' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR').format(value);
                        }
                    }
                }
            }
        }
    });
}

// Graphique Évolution Mensuelle - AVEC HAUTEUR FIXE
function createMonthlyChart(monthly) {
    const canvas = document.getElementById('monthlyRevenueChart');
    if (!canvas) {
        console.error('❌ Canvas introuvable');
        return;
    }
    
    const ctx = canvas.getContext('2d');
    const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthly.map(m => months[m.month - 1]),
            datasets: [{
                label: 'CA Mensuel (FCFA)',
                data: monthly.map(m => m.revenue || 0),
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22, 163, 74, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // IMPORTANT !
            plugins: {
                legend: { display: true, position: 'top' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR').format(value);
                        }
                    }
                }
            }
        }
    });
}
        

        // === ANALYTICS ===
        async function loadAnalytics() {
            try {
                const response = await fetch(`${API_BASE}/analytics`, {
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (data.success) {
                    renderAnalyticsData(data);
                } else {
                    document.getElementById('analyticsContent').innerHTML = '<div class="text-center text-danger">Accès refusé</div>';
                }
            } catch (error) {
                showToast('Erreur', 'error');
            }
        }

        function renderAnalyticsData(data) {
    document.getElementById('analyticsContent').innerHTML = `
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-bold text-xl"></h2>
            <button class="btn btn-primary" onclick="generateAnalyticsReport()">📈 Générer Rapport</button>
        </div>

        <div class="stats-grid mb-6">
            <div class="stat-card">
                <div class="stat-value text-primary">${data.total_customers}</div>
                <div class="stat-label">Clients Total</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-success">${data.active_customers}</div>
                <div class="stat-label">Clients Actifs</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-info">${formatCurrency(data.customer_lifetime_value)}</div>
                <div class="stat-label">Valeur Vie Client</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-warning">${data.churn_rate}%</div>
                <div class="stat-label">Taux d'Attrition</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div class="card">
                <h3>📈 Analyse des Tendances</h3>
                <div style="position: relative; height: 350px;">
                    <canvas id="trendsChart"></canvas>
                </div>
            </div>
            <div class="card">
                <h3>🎯 Segmentation Clients</h3>
                <div style="position: relative; height: 350px;">
                    <canvas id="segmentationChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="card">
            <h3>📅 Évolution Mensuelle du CA</h3>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>MOIS</th>
                            <th>NOUVEAUX CLIENTS</th>
                            <th>CONVERSIONS</th>
                            <th>CA ESTIMÉ</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${generateMonthlyRevenueRows(data)}
                    </tbody>
                </table>
            </div>
        </div>
    `;
    
    setTimeout(() => {
        initializeAnalyticsCharts(data);
    }, 100);
}

function generateMonthlyRevenueRows(data) {
    const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
    
    if (!data.monthly_trends || data.monthly_trends.length === 0) {
        return '<tr><td colspan="4" class="text-center text-secondary">Aucune donnée disponible</td></tr>';
    }
    
    // Calculer le CA pour chaque mois en utilisant les conversions
    return data.monthly_trends.map(item => {
        const estimatedRevenue = item.conversions * (data.customer_lifetime_value || 0);
        return `
            <tr>
                <td class="font-semibold">${months[item.month - 1]}</td>
                <td class="text-primary">${item.nouveaux_clients}</td>
                <td class="text-success">${item.conversions}</td>
                <td class="text-success font-semibold">${formatCurrency(estimatedRevenue)}</td>
            </tr>
        `;
    }).join('');
}

function initializeAnalyticsCharts(data) {
    const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
    
    // Graphique des tendances
    const trendsCtx = document.getElementById('trendsChart');
    if (trendsCtx) {
        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Nouveaux Clients',
                        data: data.monthly_trends.map(m => m.nouveaux_clients),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Conversions',
                        data: data.monthly_trends.map(m => m.conversions),
                        borderColor: '#16a34a',
                        backgroundColor: 'rgba(22, 163, 74, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Graphique de segmentation (Pie Chart)
    const segmentationCtx = document.getElementById('segmentationChart');
    if (segmentationCtx && data.segmentation && data.segmentation.length > 0) {
        const colors = [
            '#2563eb', // Profil Visa
            '#16a34a', // Visa Etude
            '#f59e0b', // Visa Travail
            '#ef4444', // Visa Tourisme & Affaire
            '#8b5cf6',
            '#06b6d4'
        ];
        
        new Chart(segmentationCtx, {
            type: 'pie',
            data: {
                labels: data.segmentation.map(s => s.prestation),
                datasets: [{
                    data: data.segmentation.map(s => s.count),
                    backgroundColor: colors
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        display: true, 
                        position: 'right' 
                    }
                }
            }
        });
    }
}

// Fonction pour générer le rapport Analytics
function generateAnalyticsReport() {
    showToast('Génération du rapport en cours...', 'info');
    
    fetch(`${API_BASE}/analytics`, {
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const totalConversions = data.monthly_trends.reduce((sum, m) => sum + m.conversions, 0);
            const conversionRate = data.total_customers > 0 ? 
                ((totalConversions / data.total_customers) * 100).toFixed(1) : 0;
            
            const report = `
=== RAPPORT ANALYTICS PSI AFRICA ===
Date: ${new Date().toLocaleDateString('fr-FR')}

MÉTRIQUES PRINCIPALES:
- Clients total: ${data.total_customers}
- Clients actifs: ${data.active_customers}
- Taux de conversion: ${conversionRate}%
- Valeur vie client: ${formatCurrency(data.customer_lifetime_value)}
- Taux d'attrition: ${data.churn_rate}%

SEGMENTATION PAR PRESTATION:
${data.segmentation.map(s => `- ${s.prestation}: ${s.count} clients`).join('\n')}

ÉVOLUTION MENSUELLE:
${data.monthly_trends.map((m, i) => {
    const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
    return `- ${months[m.month - 1]}: ${m.nouveaux_clients} nouveaux clients, ${m.conversions} conversions`;
}).join('\n')}
            `.trim();
            
            // Créer un blob et télécharger
            const blob = new Blob([report], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `rapport_analytics_${new Date().toISOString().split('T')[0]}.txt`;
            a.click();
            URL.revokeObjectURL(url);
            
            showToast('Rapport téléchargé avec succès', 'success');
        } else {
            showToast('Erreur lors de la génération du rapport', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('Erreur de génération du rapport', 'error');
    });
}
       // === ADMIN ===
async function loadAdmin() {
    try {
        const response = await fetch(`${API_BASE}/admin`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });
        const data = await response.json();
        if (data.success) {
            renderAdminData(data);
        } else {
            document.getElementById('adminContent').innerHTML = '<div class="text-center text-danger">Accès refusé</div>';
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur', 'error');
    }
}

function renderAdminData(data) {
    document.getElementById('adminContent').innerHTML = `
        <!-- Stats System -->
        <div class="stats-grid mb-6">
            <div class="stat-card">
                <div class="stat-value text-primary">${data.system_stats.total_users}</div>
                <div class="stat-label">Utilisateurs Système</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-info">${data.system_stats.total_clients}</div>
                <div class="stat-label">Clients</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-warning">${data.system_stats.total_invoices}</div>
                <div class="stat-label">Factures</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-success">${formatCurrency(data.system_stats.total_revenue)}</div>
                <div class="stat-label">CA Total</div>
            </div>
        </div>

        <!-- Gestion des Utilisateurs -->
        <div class="admin-section">
            <h4>👥 Gestion des Utilisateurs</h4>
            <div class="user-cards-grid" id="userCardsGrid">
                ${renderUserCards(data.users)}
            </div>
            <div class="flex gap-2">
                <button class="btn btn-primary" onclick="addNewUser()">➕ Ajouter Utilisateur</button>
                <button class="btn btn-success" onclick="saveAllUsers()">💾 Sauvegarder Tous</button>
                <button class="btn btn-warning" onclick="resetAllPasswords()">🔄 Reset Mots de Passe</button>
            </div>
        </div>

        <!-- Gestion des Données -->
        <div class="admin-section">
            <h4>🗃️ Gestion des Données</h4>
            <div class="data-management-grid">
                <div class="data-card">
                    <h5>Clients</h5>
                    <p class="text-secondary">Total: <strong>${data.system_stats.total_clients}</strong></p>
                    <div class="user-actions">
                        <button class="btn btn-sm btn-success" onclick="exportClients()">📊 Exporter</button>
                        <button class="btn btn-sm btn-warning" onclick="purgeOldClients()">🧹 Purger Anciens</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteAllClients()">🗑️ Supprimer Tous</button>
                    </div>
                </div>
                <div class="data-card">
                    <h5>Factures</h5>
                    <p class="text-secondary">Total: <strong>${data.system_stats.total_invoices}</strong></p>
                    <div class="user-actions">
                        <button class="btn btn-sm btn-success" onclick="exportInvoices()">📊 Exporter</button>
                        <button class="btn btn-sm btn-warning" onclick="purgeOldInvoices()">🧹 Purger Anciennes</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteAllInvoices()">🗑️ Supprimer Toutes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Système -->
        <div class="admin-section">
            <h4>🛠️ Actions Système</h4>
            <div class="data-management-grid">
                <div class="data-card">
                    <h5>Sauvegarde</h5>
                    <div class="user-actions">
                        <button class="btn btn-sm btn-primary" onclick="backupData()">💾 Sauvegarder</button>
                        <button class="btn btn-sm btn-secondary" onclick="restoreData()">📂 Restaurer</button>
                    </div>
                </div>
                <div class="data-card">
                    <h5>Maintenance</h5>
                    <div class="user-actions">
                        <button class="btn btn-sm btn-warning" onclick="optimizeDatabase()">⚡ Optimiser</button>
                        <button class="btn btn-sm btn-info" onclick="clearCache()">🧹 Vider Cache</button>
                    </div>
                </div>
                <div class="data-card">
                    <h5>Logs & Monitoring</h5>
                    <div class="user-actions">
                        <button class="btn btn-sm btn-info" onclick="viewSystemLogs()">📋 Voir Logs</button>
                        <button class="btn btn-sm btn-danger" onclick="clearLogs()">🗑️ Effacer Logs</button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function renderUserCards(users) {
    if (!users || users.length === 0) {
        return '<div class="text-center text-secondary">Aucun utilisateur avec rôle administratif</div>';
    }

    return users.map(user => {
        const roleLabel = getRoleLabel(user.roles);
        const statusLabel = user.etat == 1 ? 'Actif' : 'Bloqué';
        const statusClass = user.etat == 1 ? 'success' : 'danger';
        
        // Définir les permissions selon le rôle
        let permissions = [];
        
        if (roleLabel === 'Super Admin' || roleLabel === 'Admin') {
            permissions = ['DASHBOARD', 'CLIENTS', 'INVOICING', 'RECOVERY', 'PERFORMANCE', 'ANALYTICS', 'ADMIN'];
        } else if (roleLabel === 'Manager' || roleLabel === 'Commercial') {
            permissions = ['DASHBOARD', 'CLIENTS', 'INVOICING', 'RECOVERY', 'PERFORMANCE'];
        } else if (roleLabel === 'Agent Comptoir') {
            permissions = ['DASHBOARD', 'CLIENTS', 'INVOICING', 'RECOVERY'];
        } else {
            permissions = ['DASHBOARD', 'CLIENTS', 'INVOICING'];
        }
        
        return `
            <div class="user-card">
                <div class="user-card-header">${user.name}</div>
                <div class="user-card-info"><strong>Username:</strong> ${user.email}</div>
                <div class="user-card-info"><strong>Rôle:</strong> <span class="badge badge-info">${roleLabel}</span></div>
                <div class="user-card-info">
                    <strong>Statut:</strong> 
                    <span class="badge badge-${statusClass}">${statusLabel}</span>
                </div>
                <div class="user-card-info">
                    <strong>Activité:</strong> <b>${user.crm_clients_count || 0}</b> clients, ${user.crm_invoices_count || 0} factures
                </div>
                
                <div class="user-card-info"><strong>Permissions:</strong></div>
                <div class="permissions-badges">
                    ${permissions.map(p => `<span class="permission-badge">${p}</span>`).join('')}
                </div>
                
                <div class="user-actions">
                    <button class="btn btn-sm ${user.etat == 1 ? 'btn-warning' : 'btn-success'}" 
                            onclick="toggleUserStatus(${user.id}, ${user.etat})">
                        ${user.etat == 1 ? 'Bloquer' : 'Débloquer'}
                    </button>
                    <button class="btn btn-sm btn-info" onclick="editUserPermissions(${user.id})">
                        Permissions
                    </button>
                    <button class="btn btn-sm btn-warning" onclick="resetUserPassword(${user.id})">
                        Reset MDP
                    </button>
                    ${user.id !== 1 ? `
                        <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})">
                            Supprimer
                        </button>
                    ` : ''}
                </div>
            </div>
        `;
    }).join('');
}

function getRoleLabel(roles) {
    if (!roles || roles.length === 0) return 'Utilisateur';
    const role = roles[0];
    
    const roleMap = {
        'Super Admin': 'Super Admin',
        'Admin': 'Admin',
        'Manager': 'Manager',
        'Commercial': 'Commercial',
        'Agent Comptoir': 'Agent Comptoir'
    };
    
    return roleMap[role] || 'Utilisateur';
}

function getRoleLabel(roles) {
    if (!roles || roles.length === 0) return 'Agent';
    const role = roles[0];
    
    const roleMap = {
        'Super Admin': 'Super Admin',
        'Admin': 'Admin',
        'Manager': 'Manager',
        'Commercial': 'Commercial',
        'Agent Comptoir': 'Agent Comptoir'
    };
    
    return roleMap[role] || 'Agent';
}

// Fonctions d'actions admin
function addNewUser() {
    showToast('Fonctionnalité d\'ajout d\'utilisateur - À implémenter', 'info');
}

function saveAllUsers() {
    showToast('Sauvegarde des utilisateurs...', 'success');
}

function resetAllPasswords() {
    if (confirm('Réinitialiser tous les mots de passe ?')) {
        showToast('Mots de passe réinitialisés', 'warning');
    }
}

async function toggleUserStatus(userId, currentStatus) {
    if (confirm(`${currentStatus == 1 ? 'Bloquer' : 'Débloquer'} cet utilisateur ?`)) {
        showToast('Statut mis à jour', 'success');
        loadAdmin();
    }
}

function editUserPermissions(userId) {
    showToast('Gestion des permissions - À implémenter', 'info');
}

function resetUserPassword(userId) {
    if (confirm('Réinitialiser le mot de passe de cet utilisateur ?')) {
        showToast('Mot de passe réinitialisé', 'success');
    }
}

function deleteUser(userId) {
    if (confirm('Supprimer définitivement cet utilisateur ?')) {
        showToast('Utilisateur supprimé', 'warning');
        loadAdmin();
    }
}

function purgeOldClients() {
    if (confirm('Purger les anciens clients ?')) {
        showToast('Anciens clients purgés', 'warning');
    }
}

function deleteAllClients() {
    if (confirm('⚠️ ATTENTION : Supprimer TOUS les clients ?')) {
        showToast('Tous les clients supprimés', 'error');
    }
}

function purgeOldInvoices() {
    if (confirm('Purger les anciennes factures ?')) {
        showToast('Anciennes factures purgées', 'warning');
    }
}

function deleteAllInvoices() {
    if (confirm('⚠️ ATTENTION : Supprimer TOUTES les factures ?')) {
        showToast('Toutes les factures supprimées', 'error');
    }
}

function backupData() {
    showToast('Sauvegarde en cours...', 'info');
    setTimeout(() => {
        showToast('Sauvegarde terminée', 'success');
    }, 2000);
}

function restoreData() {
    if (confirm('Restaurer les données depuis une sauvegarde ?')) {
        showToast('Restauration en cours...', 'info');
    }
}

function optimizeDatabase() {
    showToast('Optimisation de la base de données...', 'info');
    setTimeout(() => {
        showToast('Base de données optimisée', 'success');
    }, 2000);
}

function clearCache() {
    showToast('Cache vidé', 'success');
}

function viewSystemLogs() {
    showToast('Affichage des logs système', 'info');
}

function clearLogs() {
    if (confirm('Effacer tous les logs ?')) {
        showToast('Logs effacés', 'warning');
    }
}

function exportInvoices() {
    showToast('Export des factures en cours...', 'info');
}
        

       // Variables globales pour les permissions
let currentEditingUser = null;

// Ouvrir la modal de permissions
// Ouvrir la modal de permissions - VERSION CORRIGÉE
async function editUserPermissions(userId) {
    try {
        console.log('🔧 Ouverture modal permissions pour user:', userId);
        
        const response = await fetch(`${API_BASE}/admin/users/${userId}`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });
        const data = await response.json();
        
        if (data.success) {
            currentEditingUser = data.user;
            
            // ✅ REMPLIR LE FORMULAIRE
            document.getElementById('permissionUserId').value = data.user.id;
            document.getElementById('permissionUserName').value = data.user.name;
            document.getElementById('permissionRole').value = data.user.roles[0] || 'Commercial';
            
            // ✅ DÉCOCHER TOUTES LES PERMISSIONS D'ABORD
            document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // ✅ COCHER LES PERMISSIONS DE L'UTILISATEUR
            if (data.user.permissions && Array.isArray(data.user.permissions)) {
                console.log('📋 Permissions utilisateur:', data.user.permissions);
                
                data.user.permissions.forEach(permission => {
                    const checkbox = document.getElementById('perm_' + permission);
                    if (checkbox) {
                        checkbox.checked = true;
                        console.log('✅ Permission cochée:', permission);
                    }
                });
            } else {
                console.log('⚠️ Aucune permission trouvée, utilisation des permissions par défaut');
                updatePermissionsByRole(data.user.roles[0]);
            }
            
            // ✅ OUVRIR LE MODAL CORRECTEMENT
            const modal = document.getElementById('permissionsModal');
            if (modal) {
                modal.classList.remove('hidden');
                console.log('✅ Modal ouvert');
            } else {
                console.error('❌ Modal permissionsModal introuvable dans le DOM');
            }
            
        } else {
            showToast('Erreur de chargement', 'error');
        }
    } catch (error) {
        console.error('❌ Erreur editUserPermissions:', error);
        showToast('Erreur de chargement', 'error');
    }
}

// ✅ AJOUTER UN ÉCOUTEUR SUR LE SELECT DE RÔLE
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('permissionRole');
    if (roleSelect) {
        roleSelect.addEventListener('change', function() {
            updatePermissionsByRole(this.value);
        });
    }
});
function closePermissionsModal() {
    document.getElementById('permissionsModal').classList.add('hidden');
    currentEditingUser = null;
}

// Mettre à jour les permissions selon le rôle
function updatePermissionsByRole(role) {
    // Réinitialiser TOUTES les permissions
    document.getElementById('perm_dashboard').checked = false;
    document.getElementById('perm_clients').checked = false;
    document.getElementById('perm_invoicing').checked = false;
    document.getElementById('perm_recovery').checked = false;
    document.getElementById('perm_performance').checked = false;
    document.getElementById('perm_analytics').checked = false;
    document.getElementById('perm_admin').checked = false;
    document.getElementById('perm_edit_clients').checked = false;
    document.getElementById('perm_delete_clients').checked = false;
    document.getElementById('perm_edit_invoices').checked = false;
    document.getElementById('perm_delete_invoices').checked = false;
    document.getElementById('perm_edit_payments').checked = false;
    document.getElementById('perm_delete_payments').checked = false;
    
    // Cocher selon le rôle
    if (role === 'Admin' || role === 'Super Admin') {
        // TOUTES les permissions
        document.getElementById('perm_dashboard').checked = true;
        document.getElementById('perm_clients').checked = true;
        document.getElementById('perm_invoicing').checked = true;
        document.getElementById('perm_recovery').checked = true;
        document.getElementById('perm_performance').checked = true;
        document.getElementById('perm_analytics').checked = true;
        document.getElementById('perm_admin').checked = true;
        document.getElementById('perm_edit_clients').checked = true;
        document.getElementById('perm_delete_clients').checked = true;
        document.getElementById('perm_edit_invoices').checked = true;
        document.getElementById('perm_delete_invoices').checked = true;
        document.getElementById('perm_edit_payments').checked = true;
        document.getElementById('perm_delete_payments').checked = true;
    } else if (role === 'Manager' || role === 'Commercial') {
        // Modules + Édition (pas de suppression)
        document.getElementById('perm_dashboard').checked = true;
        document.getElementById('perm_clients').checked = true;
        document.getElementById('perm_invoicing').checked = true;
        document.getElementById('perm_recovery').checked = true;
        document.getElementById('perm_performance').checked = true;
        document.getElementById('perm_edit_clients').checked = true;
        document.getElementById('perm_edit_invoices').checked = true;
        document.getElementById('perm_edit_payments').checked = true;
    } else if (role === 'Agent Comptoir') {
        // Modules limités + Édition clients et paiements
        document.getElementById('perm_dashboard').checked = true;
        document.getElementById('perm_clients').checked = true;
        document.getElementById('perm_invoicing').checked = true;
        document.getElementById('perm_recovery').checked = true;
        document.getElementById('perm_edit_clients').checked = true;
        document.getElementById('perm_edit_payments').checked = true;
    } else {
        // Par défaut, au moins dashboard, clients et invoicing
        document.getElementById('perm_dashboard').checked = true;
        document.getElementById('perm_clients').checked = true;
        document.getElementById('perm_invoicing').checked = true;
    }
}

// Écouter les changements de rôle
document.addEventListener('DOMContentLoaded', async () => {
    console.log('🚀 Initialisation du CRM...');
    
    // ✅ CHARGER LES PERMISSIONS D'ACTIONS
    await loadActionPermissions();

    // Charger les permissions de modules
    await loadUserPermissions();

    // Mettre à jour la date
    const dateElement = document.getElementById('dashboardDate');
    if (dateElement) {
        dateElement.textContent = new Date().toLocaleDateString('fr-FR', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
    }
    
    console.log('✅ CRM initialisé');
});
// Sauvegarder les permissions
async function savePermissions(event) {
    event.preventDefault();
    
    const userId = document.getElementById('permissionUserId').value;
    const role = document.getElementById('permissionRole').value;
    
    // Récupérer TOUTES les permissions cochées (sans forcer les 3 de base)
    const permissions = [];
    document.querySelectorAll('input[name="permissions[]"]:checked').forEach(checkbox => {
        permissions.push(checkbox.value);
    });
    
    console.log('Envoi des permissions:', permissions);
    
    try {
        const response = await fetch(`${API_BASE}/admin/users/${userId}/permissions`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                role: role, 
                permissions: permissions 
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Permissions mises à jour avec succès', 'success');
            closePermissionsModal();
            loadAdmin();
        } else {
            showToast(data.error || 'Erreur lors de la mise à jour', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur de sauvegarde', 'error');
    }
}

// Fonction pour bloquer/débloquer un utilisateur
async function toggleUserStatus(userId, currentStatus) {
    const action = currentStatus == 1 ? 'bloquer' : 'débloquer';
    
    if (!confirm(`Voulez-vous ${action} cet utilisateur ?`)) return;
    
    try {
        const response = await fetch(`${API_BASE}/admin/users/${userId}/toggle-status`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status: currentStatus == 1 ? 0 : 1 })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast(`Utilisateur ${action === 'bloquer' ? 'bloqué' : 'débloqué'}`, 'success');
            loadAdmin();
        } else {
            showToast(data.error || 'Erreur', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur', 'error');
    }
}

// Réinitialiser le mot de passe
async function resetUserPassword(userId) {
    if (!confirm('Réinitialiser le mot de passe de cet utilisateur à "password123" ?')) return;
    
    try {
        const response = await fetch(`${API_BASE}/admin/users/${userId}/reset-password`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Mot de passe réinitialisé à "password123"', 'success');
        } else {
            showToast(data.error || 'Erreur', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur', 'error');
    }
}

// Supprimer un utilisateur
async function deleteUser(userId) {
    if (!confirm('⚠️ ATTENTION : Supprimer définitivement cet utilisateur et toutes ses données ?')) return;
    
    try {
        const response = await fetch(`${API_BASE}/admin/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Utilisateur supprimé', 'success');
            loadAdmin();
        } else {
            showToast(data.error || 'Erreur', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur', 'error');
    }
}

let userPermissions = [];
let initialLoadComplete = false;

async function loadUserPermissions() {
    try {
        const response = await fetch(`${API_BASE}/user/permissions`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });
        const data = await response.json();
        
        if (data.success) {
            userPermissions = data.permissions;
            console.log('Permissions utilisateur:', userPermissions);
            
            // Cacher les onglets non autorisés
            hideUnauthorizedTabs();
            
            // Charger le premier module autorisé
            loadFirstAuthorizedModule();
        }
    } catch (error) {
        console.error('Erreur chargement permissions:', error);
    }
}

// Charger le premier module autorisé
function loadFirstAuthorizedModule() {
    const modulePriority = ['dashboard', 'clients', 'invoicing', 'recovery', 'performance', 'analytics', 'admin'];
    
    for (const module of modulePriority) {
        if (userPermissions.includes(module)) {
            console.log('Chargement du module autorisé:', module);
            
            // Activer l'onglet
            document.querySelectorAll('.nav-tab').forEach(tab => tab.classList.remove('active'));
            const targetTab = document.querySelector(`[data-panel="${module}"]`);
            if (targetTab) {
                targetTab.classList.add('active');
            }
            
            // Afficher le panel
            document.querySelectorAll('.panel').forEach(panel => panel.classList.add('hidden'));
            const targetPanel = document.getElementById(`${module}-panel`);
            if (targetPanel) {
                targetPanel.classList.remove('hidden');
            }
            
            // Charger le contenu
            loadPanelContent(module);
            initialLoadComplete = true;
            return;
        }
    }
    
    // Si aucun module autorisé, afficher un message d'erreur
    showNoPermissionsMessage();
}

function showNoPermissionsMessage() {
    document.querySelectorAll('.panel').forEach(panel => panel.classList.add('hidden'));
    document.getElementById('dashboard-panel').classList.remove('hidden');
    document.getElementById('dashboard-panel').innerHTML = `
        <div class="text-center" style="padding: 3rem;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">🔒</div>
            <h2 class="text-danger font-bold mb-3">Accès Refusé</h2>
            <p class="text-secondary">Vous n'avez pas les permissions nécessaires pour accéder au système CRM.</p>
            <p class="text-secondary">Veuillez contacter votre administrateur système.</p>
        </div>
    `;
}

function hideUnauthorizedTabs() {
    const tabMapping = {
        'dashboard': 'dashboard',
        'clients': 'clients',
        'invoicing': 'invoicing',
        'recovery': 'recovery',
        'performance': 'performance',
        'analytics': 'analytics',
        'admin': 'admin'
    };
    
    document.querySelectorAll('.nav-tab').forEach(tab => {
        const panel = tab.getAttribute('data-panel');
        
        if (tabMapping[panel] && !userPermissions.includes(tabMapping[panel])) {
            tab.style.display = 'none';
        } else {
            tab.style.display = 'inline-flex';
        }
    });
}

function hasPermission(permission) {
    return userPermissions.includes(permission);
}

// ✅ switchToPanel et loadPanelContent sont définis plus bas dans le fichier (éviter la duplication)

function loadPanelContent(panelName) {
    const permissionMapping = {
        'dashboard': 'dashboard',
        'clients': 'clients',
        'invoicing': 'invoicing',
        'recovery': 'recovery',
        'relances': 'clients', // ✅ AJOUTÉ
        'performance': 'performance',
        'analytics': 'analytics',
        'admin': 'admin'
    };
    
    // Vérifier les permissions
    if (permissionMapping[panelName] && !hasPermission(permissionMapping[panelName])) {
        document.getElementById(`${panelName}-panel`).innerHTML = `
            <div class="text-center" style="padding: 3rem;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">🔒</div>
                <h2 class="text-danger font-bold mb-3">Accès Refusé</h2>
                <p class="text-secondary">Vous n'avez pas la permission d'accéder à ce module.</p>
            </div>
        `;
        return;
    }
    
    // Charger le contenu normalement
    switch (panelName) {
        case 'dashboard': loadDashboard(); break;
        case 'clients': loadClients(); break;
        case 'invoicing': loadInvoices(); break;
        case 'recovery': loadRecovery(); break;
        case 'relances': loadRelances(); break; // ✅ AJOUTÉ
        case 'performance': loadPerformance(); break;
        case 'analytics': loadAnalytics(); break;
        case 'admin': loadAdmin(); break;
    }
}

// Modifier le DOMContentLoaded
document.addEventListener('DOMContentLoaded', async () => {
    // Charger les permissions et le premier module autorisé
    await loadUserPermissions();

    // ✅ Attacher les événements de navigation APRÈS le chargement des permissions
    document.querySelectorAll('.nav-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            const panel = tab.getAttribute('data-panel');
            switchToPanel(panel);
        });
    });

    // Mettre à jour la date
    document.getElementById('dashboardDate').textContent = new Date().toLocaleDateString('fr-FR', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    });
});

// Cacher les onglets non autorisés
function hideUnauthorizedTabs() {
    const tabMapping = {
        'dashboard': 'dashboard',
        'clients': 'clients',
        'invoicing': 'invoicing',
        'recovery': 'recovery',
        'performance': 'performance',
        'analytics': 'analytics',
        'admin': 'admin'
    };
    
    document.querySelectorAll('.nav-tab').forEach(tab => {
        const panel = tab.getAttribute('data-panel');
        
        if (tabMapping[panel] && !userPermissions.includes(tabMapping[panel])) {
            tab.style.display = 'none';
        } else {
            tab.style.display = 'inline-flex';
        }
    });
}

// Vérifier si l'utilisateur a une permission
function hasPermission(permission) {
    return userPermissions.includes(permission);
}

// Modifier switchToPanel pour vérifier les permissions
function switchToPanel(panelName) {
    const permissionMapping = {
        'dashboard': 'dashboard',
        'clients': 'clients',
        'invoicing': 'invoicing',
        'recovery': 'recovery',
        'relances': 'clients', // ✅ RELANCES UTILISE LA PERMISSION CLIENTS
        'performance': 'performance',
        'analytics': 'analytics',
        'admin': 'admin'
    };
    
    // Vérifier si l'utilisateur a la permission
    if (permissionMapping[panelName] && !hasPermission(permissionMapping[panelName])) {
        showToast('Accès refusé - Vous n\'avez pas la permission d\'accéder à ce module', 'error');
        return;
    }
    
    document.querySelectorAll('.nav-tab').forEach(tab => tab.classList.remove('active'));
    const targetTab = document.querySelector(`[data-panel="${panelName}"]`);
    if (targetTab) {
        targetTab.classList.add('active');
    }
    
    document.querySelectorAll('.panel').forEach(panel => panel.classList.add('hidden'));
    const targetPanel = document.getElementById(`${panelName}-panel`);
    if (targetPanel) {
        targetPanel.classList.remove('hidden');
    }
    
    loadPanelContent(panelName);
}

// Définition des prestations par catégorie - AVEC CATÉGORIE AUTRES
const prestationsParCategorie = {
    "Frais du Cabinet": [
        "Profil Visa",
        "Inscription",
        "Assistance"
    ],
    "Documents de Voyage": [
        "Réservation d'hôtel",
        "Billet d'avion",
        "Assurance",
        "Circuit touristique"
    ],
    "Autres": [
        "Autre (précisez)"
    ]
};

// Mettre à jour les options de prestation pour le formulaire Client
// Mettre à jour les options de prestation pour le formulaire Client
function updatePrestationOptions() {
    const categorieSelect = document.getElementById('clientCategorie');
    const prestationSelect = document.getElementById('clientPrestation');
    
    // ✅ VÉRIFIER QUE LES ÉLÉMENTS EXISTENT (pour éviter erreur sur modal facture)
    if (!categorieSelect || !prestationSelect) {
        console.log('Éléments client non trouvés, probablement dans modal facture');
        return;
    }
    
    const categorie = categorieSelect.value;
    
    // Réinitialiser
    prestationSelect.innerHTML = '<option value="">Sélectionner une prestation</option>';
    
    // Supprimer le champ libre s'il existe
    const existingCustomField = document.getElementById('customPrestationField');
    if (existingCustomField) {
        existingCustomField.remove();
    }
    
    if (categorie && prestationsParCategorie[categorie]) {
        prestationSelect.disabled = false;
        prestationsParCategorie[categorie].forEach(prestation => {
            const option = document.createElement('option');
            option.value = prestation;
            option.textContent = prestation;
            prestationSelect.appendChild(option);
        });
        
        // Si catégorie "Autres", ajouter un champ de saisie libre
        if (categorie === "Autres") {
            const customFieldDiv = document.createElement('div');
            customFieldDiv.className = 'form-group';
            customFieldDiv.id = 'customPrestationField';
            customFieldDiv.innerHTML = `
                <label class="form-label">Précisez la prestation *</label>
                <input type="text" id="customPrestationInput" class="form-control" 
                       placeholder="Entrez la prestation personnalisée" required>
            `;
            prestationSelect.closest('.form-group').insertAdjacentElement('afterend', customFieldDiv);
            
            // Écouter les changements
            prestationSelect.addEventListener('change', function() {
                const customInput = document.getElementById('customPrestationInput');
                if (this.value === "Autre (précisez)" && customInput) {
                    customInput.style.display = 'block';
                    customInput.required = true;
                }
            });
        }
    } else {
        prestationSelect.disabled = true;
    }
}

// Mettre à jour les options de service pour le formulaire Facture
// Mettre à jour les options de service pour le formulaire Facture
function updateServiceOptions() {
    const categorieSelect = document.getElementById('invoiceCategorie');
    const serviceSelect = document.getElementById('invoiceService');
    
    // ✅ VÉRIFIER QUE LES ÉLÉMENTS EXISTENT
    if (!categorieSelect || !serviceSelect) {
        console.log('Cette fonction est obsolète avec le nouveau système multi-services');
        return;
    }
    
    const categorie = categorieSelect.value;
    
    // Réinitialiser
    serviceSelect.innerHTML = '<option value="">Sélectionner un service</option>';
    
    // Supprimer le champ personnalisé s'il existe
    const existingCustomField = document.getElementById('customServiceField');
    if (existingCustomField) {
        existingCustomField.remove();
    }
    
    if (categorie && prestationsParCategorie[categorie]) {
        serviceSelect.disabled = false;
        prestationsParCategorie[categorie].forEach(service => {
            const option = document.createElement('option');
            option.value = service;
            option.textContent = service;
            serviceSelect.appendChild(option);
        });
        
        // Si catégorie "Autres", ajouter un champ de saisie libre
        if (categorie === "Autres") {
            const customFieldDiv = document.createElement('div');
            customFieldDiv.className = 'form-group';
            customFieldDiv.id = 'customServiceField';
            customFieldDiv.innerHTML = `
                <label class="form-label">Précisez le service *</label>
                <input type="text" id="customServiceInput" class="form-control" 
                       placeholder="Entrez le service personnalisé" required>
            `;
            serviceSelect.closest('.form-group').insertAdjacentElement('afterend', customFieldDiv);
            
            // Écouter les changements
            serviceSelect.addEventListener('change', function() {
                const customInput = document.getElementById('customServiceInput');
                if (this.value === "Autre (précisez)" && customInput) {
                    customInput.style.display = 'block';
                    customInput.required = true;
                }
            });
        }
    } else {
        serviceSelect.disabled = true;
    }
}
// Réinitialiser les formulaires lors de la fermeture
function closeClientModal() {
    const modal = document.getElementById('clientModal');
    const form = document.getElementById('clientForm');
    const prestationSelect = document.getElementById('clientPrestation');
    
    if (modal) modal.classList.add('hidden');
    if (form) form.reset();
    
    if (prestationSelect) {
        prestationSelect.disabled = true;
        prestationSelect.innerHTML = '<option value="">Sélectionner d\'abord une catégorie</option>';
    }
    
    // Supprimer le champ personnalisé s'il existe
    const customField = document.getElementById('customPrestationField');
    if (customField) {
        customField.remove();
    }
}
function closeInvoiceModal() {
    const modal = document.getElementById('invoiceModal');
    const form = document.getElementById('invoiceForm');
    
    if (modal) modal.classList.add('hidden');
    if (form) form.reset();
    
    // Réinitialiser les services
    selectedServices = [];
    renderServicesLines();
    
    // Supprimer le champ personnalisé s'il existe
    const customField = document.getElementById('customServiceField');
    if (customField) {
        customField.remove();
    }
    
    currentClientForInvoice = null;
}

// === GRAPHIQUES DASHBOARD ===
function createDashboardCharts(stats) {
    // Graphique Évolution du CA
    createRevenueChart(stats);
    
    // Graphique Statuts Clients
    createClientsStatusChart(stats);
}

function createRevenueChart(stats) {
    const canvas = document.getElementById('revenueChart');
    if (!canvas) {
        console.error('❌ Canvas revenueChart introuvable');
        return;
    }

    const ctx = canvas.getContext('2d');

    // ✅ Utiliser les vraies données du backend
    const monthNames = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
    const revenusParMois = stats.revenue?.par_mois || {};

    // Créer un tableau pour tous les mois de l'année
    const months = [];
    const revenueData = [];
    for (let i = 1; i <= 12; i++) {
        months.push(monthNames[i - 1]);
        revenueData.push(revenusParMois[i] || 0);
    }

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Chiffre d\'Affaires',
                data: revenueData,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#2563eb',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'CA: ' + new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FCFA';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR').format(value);
                        }
                    }
                }
            }
        }
    });
}

function createClientsStatusChart(stats) {
    const canvas = document.getElementById('clientsStatusChart');
    if (!canvas) {
        console.error('❌ Canvas clientsStatusChart introuvable');
        return;
    }

    const ctx = canvas.getContext('2d');

    // ✅ Utiliser les vraies données du backend
    const statusData = stats.clients?.par_statut || {};

    // Si pas de données, afficher un message
    if (Object.keys(statusData).length === 0) {
        console.log('⚠️ Aucune donnée de statut disponible');
        statusData['Aucun client'] = 1;
    }
    
    // ✅ Générer des couleurs dynamiques basées sur les statuts
    const colorMap = {
        'Lead': '#f59e0b',
        'Prospect': '#06b6d4',
        'Opportunité': '#8b5cf6',
        'Négociation': '#f97316',
        'Converti': '#16a34a',
        'Visa validé': '#22c55e',
        'Perdu': '#ef4444',
        'En attente': '#eab308',
        'En cours': '#3b82f6'
    };

    const colors = Object.keys(statusData).map(status =>
        colorMap[status] || '#' + Math.floor(Math.random()*16777215).toString(16)
    );

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(statusData),
            datasets: [{
                data: Object.values(statusData),
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'right',
                    labels: {
                        boxWidth: 15,
                        padding: 10
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

// Fonction pour changer le statut d'un client
async function changeClientStatus(clientId, currentStatus) {
    const statuts = ['Lead', 'Prospect', 'Opportunité', 'Négociation', 'Converti', 'Perdu'];
    
    // Créer un sélecteur de statut
    const statusHtml = statuts.map(s => 
        `<option value="${s}" ${s === currentStatus ? 'selected' : ''}>${s}</option>`
    ).join('');
    
    const statusSelect = `
        <select id="newStatusSelect" class="form-control" style="margin: 1rem 0;">
            ${statusHtml}
        </select>
    `;
    
    // Créer une modale simple pour changer le statut
    const modalHtml = `
        <div class="modal-overlay" id="changeStatusModal" style="display: flex;">
            <div class="modal-content" style="max-width: 400px;">
                <div class="modal-header">
                    <h3 class="modal-title">Modifier le Statut</h3>
                    <button class="close-btn" onclick="closeChangeStatusModal()">×</button>
                </div>
                <div style="padding: 1rem 0;">
                    <label class="form-label">Nouveau Statut</label>
                    ${statusSelect}
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-success w-full" onclick="saveClientStatus(${clientId})">💾 Enregistrer</button>
                    <button class="btn w-full" onclick="closeChangeStatusModal()">Annuler</button>
                </div>
            </div>
        </div>
    `;
    
    // Ajouter la modale au DOM
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = modalHtml;
    document.body.appendChild(tempDiv.firstElementChild);
}

function closeChangeStatusModal() {
    const modal = document.getElementById('changeStatusModal');
    if (modal) {
        modal.remove();
    }
}

async function saveClientStatus(clientId) {
    const newStatus = document.getElementById('newStatusSelect').value;
    
    if (!newStatus) {
        showToast('Veuillez sélectionner un statut', 'error');
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/clients/${clientId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ statut: newStatus })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('Statut mis à jour avec succès', 'success');
            closeChangeStatusModal();
            loadClients();
        } else {
            showToast(result.error || 'Erreur', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur de mise à jour', 'error');
    }
}
// Fonction corrigée pour fermer le modal de détails de facture
function closeViewInvoiceModal() {
    const modal = document.getElementById('viewInvoiceModal');
    if (modal) {
        modal.classList.add('hidden');
    }
    window.currentInvoiceId = null;
}
// Fonction pour imprimer la facture actuelle
function printCurrentInvoice() {
    if (window.currentInvoiceId) {
        printInvoice(window.currentInvoiceId);
    }
}

// Voir une facture depuis le modal client
function viewInvoiceFromClient(invoiceId) {
    closeViewClientModal();
    viewInvoiceDetails(invoiceId);
}

// Enregistrer un paiement depuis le modal client
function recordPaymentFromClient(invoiceId, remaining) {
    closeViewClientModal();
    recordPayment(invoiceId, remaining);
}

// Ajouter une ligne de service
function addServiceLine() {
    const categorie = document.getElementById('invoiceCategorie').value;
    
    if (!categorie) {
        showToast('Veuillez d\'abord sélectionner une catégorie', 'error');
        return;
    }
    
    const serviceId = Date.now();
    selectedServices.push({
        id: serviceId,
        service: '',
        montant: 0
    });
    
    renderServicesLines();
}

// Supprimer une ligne de service
function removeServiceLine(serviceId) {
    selectedServices = selectedServices.filter(s => s.id !== serviceId);
    renderServicesLines();
    calculateTotalAmount();
}

// Afficher les lignes de services
function renderServicesLines() {
    const container = document.getElementById('servicesLinesContainer');
    
    if (selectedServices.length === 0) {
        container.innerHTML = `
            <div class="text-center text-secondary" style="padding: 1rem;">
                Aucun service ajouté
            </div>
        `;
        return;
    }
    
    const categorie = document.getElementById('invoiceCategorie').value;
    const servicesList = prestationsParCategorie[categorie] || [];
    
    container.innerHTML = selectedServices.map((service, index) => `
        <div class="service-line" style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 0.5rem; margin-bottom: 0.75rem; padding: 0.75rem; background: var(--bg-secondary); border-radius: 6px;">
            <div class="form-group" style="margin: 0;">
                <label class="form-label" style="margin-bottom: 0.25rem;">Service ${index + 1}</label>
                ${categorie === 'Autres' ? `
                    <input type="text" 
                           class="form-control" 
                           placeholder="Précisez le service"
                           value="${service.service}"
                           onchange="updateServiceLine(${service.id}, 'service', this.value)"
                           required>
                ` : `
                    <select class="form-control" 
                            onchange="updateServiceLine(${service.id}, 'service', this.value)"
                            required>
                        <option value="">Sélectionner</option>
                        ${servicesList.map(s => `
                            <option value="${s}" ${service.service === s ? 'selected' : ''}>${s}</option>
                        `).join('')}
                    </select>
                `}
            </div>
            
            <div class="form-group" style="margin: 0;">
                <label class="form-label" style="margin-bottom: 0.25rem;">Montant (FCFA)</label>
                <input type="number" 
                       class="form-control" 
                       placeholder="0"
                       value="${service.montant || ''}"
                       onchange="updateServiceLine(${service.id}, 'montant', parseFloat(this.value) || 0)"
                       min="0"
                       required>
            </div>
            
            <div style="display: flex; align-items: flex-end;">
                <button type="button" 
                        class="btn btn-danger btn-sm" 
                        onclick="removeServiceLine(${service.id})"
                        title="Supprimer">
                    🗑️
                </button>
            </div>
        </div>
    `).join('');
    
    calculateTotalAmount();
}

// Mettre à jour une ligne de service
function updateServiceLine(serviceId, field, value) {
    const service = selectedServices.find(s => s.id === serviceId);
    if (service) {
        service[field] = value;
        calculateTotalAmount();
    }
}

// Calculer le montant total
function calculateTotalAmount() {
    const total = selectedServices.reduce((sum, service) => {
        return sum + (parseFloat(service.montant) || 0);
    }, 0);
    
    const totalElement = document.getElementById('totalAmount');
    if (totalElement) {
        totalElement.textContent = formatCurrency(total);
    }
}

// Réinitialiser les services lors du changement de catégorie
// Réinitialiser les services lors du changement de catégorie dans le modal FACTURE
function onCategorieChange() {
    const categorie = document.getElementById('invoiceCategorie');
    
    // ✅ VÉRIFIER QUE L'ÉLÉMENT EXISTE
    if (!categorie) {
        console.error('Élément invoiceCategorie non trouvé');
        return;
    }
    
    if (selectedServices.length > 0) {
        if (!confirm('Changer de catégorie va réinitialiser les services. Continuer ?')) {
            // Rétablir la catégorie précédente
            return;
        }
    }
    
    selectedServices = [];
    renderServicesLines();
}

// === RELANCES ===

async function loadRelances() {
    try {
        console.log('📞 Chargement des relances...');
        
        const statut = document.getElementById('relanceStatusFilter')?.value || '';
        const url = new URL(`${API_BASE}/relances`);
        if (statut) url.searchParams.append('statut', statut);
        
        const response = await fetch(url, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // ✅ STOCKER LES CLIENTS À RELANCER
            clientsARelancer = data.clients_a_relancer || [];
            
            // ✅ AJOUTER CES CLIENTS À currentClients S'ILS N'Y SONT PAS
            clientsARelancer.forEach(client => {
                const exists = currentClients.find(c => c.id === client.id);
                if (!exists) {
                    currentClients.push(client);
                }
            });
            
            console.log('📊 Clients à relancer:', clientsARelancer.length);
            console.log('📊 Total clients en mémoire:', currentClients.length);
            
            // Afficher les relances
            renderRelancesTable(data.relances.data || []);
            
            // Afficher les clients à relancer
            renderClientsARelancer(clientsARelancer);
            
            // Charger les stats
            await loadRelancesStats();
            
            // Charger les clients pour le select
            await loadClientsForRelanceSelect();
            
            console.log('✅ Relances chargées avec succès');
        } else {
            console.error('❌ Erreur dans la réponse:', data);
            showToast('Erreur de chargement des relances', 'error');
        }
    } catch (error) {
        console.error('❌ Erreur loadRelances:', error);
        showToast('Erreur de chargement des relances: ' + error.message, 'error');
    }
}

async function loadRelancesStats() {
    try {
        const response = await fetch(`${API_BASE}/relances/stats`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });
        const data = await response.json();

        if (data.success) {
            // Mettre à jour les stats dans la section Relances uniquement
            const relancesStatsEl = document.getElementById('relancesStats');
            if (relancesStatsEl) {
                relancesStatsEl.innerHTML = `
                    <div class="stat-card">
                        <div class="stat-value text-primary">${data.stats.total || 0}</div>
                        <div class="stat-label">Total Relances</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value text-warning">${data.stats.en_cours || 0}</div>
                        <div class="stat-label">En Cours</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value text-success">${data.stats.clotures || 0}</div>
                        <div class="stat-label">Clôturés</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value text-info">${data.stats.cette_semaine || 0}</div>
                        <div class="stat-label">Cette Semaine</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value text-danger">${data.stats.a_relancer_aujourd_hui || 0}</div>
                        <div class="stat-label">À Relancer Aujourd'hui</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value text-secondary">${data.stats.jamais_relances || 0}</div>
                        <div class="stat-label">Jamais Relancés</div>
                    </div>
                `;
            }
        }
    } catch (error) {
        console.error('Erreur:', error);
    }
}

// ✅ Fonction helper pour rafraîchir toutes les statistiques de relances
async function refreshAllRelancesStats() {
    console.log('🔄 Rafraîchissement des statistiques de relances...');
    await loadRelancesStats();
}

function renderRelancesTable(relances) {
    const tbody = document.getElementById('relancesTableBody');
    
    console.log('📋 Affichage de', relances?.length || 0, 'relances');
    
    if (!tbody) {
        console.error('❌ Table tbody relancesTableBody introuvable');
        return;
    }
    
    if (!relances || relances.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center" style="padding: 3rem;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">📞</div>
                    <p class="text-secondary font-semibold">Aucune relance enregistrée</p>
                    <p class="text-sm text-muted">Les relances apparaîtront ici une fois effectuées</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = relances.map(r => {
        const dateRelance = new Date(r.date_relance);
        const prochaineRelance = r.prochaine_relance ? new Date(r.prochaine_relance) : null;
        const isPasse = prochaineRelance && prochaineRelance < new Date();
        
        // ✅ CORRECTION : S'assurer que r.client existe
        const clientNom = r.client ? `${r.client.nom} ${r.client.prenoms || ''}` : 'Client inconnu';
        const clientContact = r.client ? r.client.contact : '-';
        const clientId = r.client ? r.client.id : null;
        
        return `
            <tr>
                <td>
                    <div class="font-semibold">${clientNom}</div>
                    <div class="text-xs text-secondary">${clientContact}</div>
                </td>
                <td>
                    <div class="font-semibold text-primary" style="font-size: 0.9rem;">
                        👤 ${r.agent_name || 'Non spécifié'}
                    </div>
                    ${r.canal ? `<div class="text-xs text-secondary">via ${r.canal}</div>` : ''}
                </td>
                <td>
                    <div class="font-semibold">${formatDate(r.date_relance)}</div>
                    <div class="text-xs text-secondary">${dateRelance.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'})}</div>
                </td>
                <td class="text-sm">${r.commentaire || '-'}</td>
                <td>
                    <span class="badge ${r.statut === 'Clôturé' ? 'badge-success' : 'badge-warning'}">
                        ${r.statut}
                    </span>
                </td>
                <td>
                    ${prochaineRelance ? `
                        <div class="text-sm ${isPasse ? 'text-danger font-semibold' : 'text-info'}">
                            ${formatDate(r.prochaine_relance)}
                            ${isPasse ? '<br><span class="badge badge-danger">⚠️ URGENT</span>' : ''}
                        </div>
                    ` : '<span class="text-secondary">-</span>'}
                </td>
                <td>
                    <div class="flex gap-2">
                        ${r.statut === 'En cours' ? `
                            <button class="btn btn-sm btn-success" 
                                    onclick="cloturerRelance(${r.id})" 
                                    title="Clôturer">
                                ✓
                            </button>
                        ` : ''}
                        ${clientId ? `
                            <button class="btn btn-sm btn-info" 
                                    onclick="viewClientRelances(${clientId})" 
                                    title="Historique">
                                📋
                            </button>
                            <button class="btn btn-sm btn-primary" 
                                    onclick="quickRelanceClient(${clientId})" 
                                    title="Nouvelle relance">
                                📞
                            </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function renderClientsARelancer(clients) {
    const container = document.getElementById('clientsARelancerList');
    
    if (!clients || clients.length === 0) {
        container.innerHTML = `
            <div class="text-center" style="padding: 2rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🎉</div>
                <p class="text-success font-semibold">Tous les clients ont été relancés cette semaine !</p>
                <p class="text-sm text-secondary">Excellent travail d'équipe</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = `
        <div class="table-container">
            <table class="table">
                <thead style="position: sticky; top: 0; background: white; z-index: 10; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <tr>
                        <th>UID</th>
                        <th>NOM</th>
                        <th>CONTACT</th>
                        <th>PRESTATION</th>
                        <th>STATUT</th>
                        <th>DERNIÈRE RELANCE</th>
                        <th>URGENCE</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    ${clients.map(c => {
                        let derniereRelance = 'Jamais relancé';
                        let agentRelance = '';
                        let urgenceClass = 'badge-danger';
                        let urgenceIcon = '🔴';
                        let urgenceText = 'URGENT';

                        if (c.relances && c.relances.length > 0) {
                            const lastRelance = c.relances[0];
                            const dateRelance = new Date(lastRelance.date_relance);
                            const joursDepuis = Math.floor((new Date() - dateRelance) / (1000 * 60 * 60 * 24));

                            derniereRelance = `${formatDate(lastRelance.date_relance)} (il y a ${joursDepuis} jour${joursDepuis > 1 ? 's' : ''})`;
                            agentRelance = lastRelance.agent_name ? `<br><small class="text-muted">👤 Par: ${lastRelance.agent_name}</small>` : '';

                            if (joursDepuis <= 7) {
                                urgenceClass = 'badge-success';
                                urgenceIcon = '✅';
                                urgenceText = 'OK';
                            } else if (joursDepuis <= 14) {
                                urgenceClass = 'badge-warning';
                                urgenceIcon = '⏰';
                                urgenceText = 'À FAIRE';
                            }
                        }
                        
                        return `
                            <tr>
                                <td class="text-xs font-mono">${c.uid.substring(0, 8)}</td>
                                <td>
                                    <div class="font-semibold">${c.nom} ${c.prenoms || ''}</div>
                                    <div class="text-xs text-secondary">${c.email || '-'}</div>
                                </td>
                                <td class="text-sm">${c.contact}</td>
                                <td class="text-sm">${c.prestation}</td>
                                <td>
                                    <span class="badge ${getStatusBadgeClass(c.statut)}">${c.statut}</span>
                                </td>
                                <td>
                                    <div class="text-xs text-secondary">${derniereRelance}${agentRelance}</div>
                                </td>
                                <td>
                                    <span class="badge ${urgenceClass}" style="font-weight: bold;">
                                        ${urgenceIcon} ${urgenceText}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        <button class="btn btn-sm btn-primary" 
                                                onclick="safeQuickRelance(${c.id})" 
                                                title="Relancer maintenant">
                                            📞 Relancer
                                        </button>
                                        <button class="btn btn-sm btn-info" 
                                                onclick="safeViewClient(${c.id})" 
                                                title="Voir détails">
                                            👁️
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
        </div>
    `;
}

async function loadClientsForRelanceSelect() {
    try {
        console.log('📋 Chargement des clients pour relance...');

        const url = new URL(`${API_BASE}/clients`);
        url.searchParams.append('all', 'true'); // Charger TOUS les clients

        const response = await fetch(url, {
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        });

        console.log('📡 Réponse reçue:', response.status);

        const data = await response.json();
        console.log('📊 Données clients:', data);

        if (data.success && data.clients) {
            // ✅ GESTION DES 2 FORMATS POSSIBLES
            const clients = data.clients.data || data.clients;

            console.log('✅ Nombre de clients:', clients.length);
            
            const select = document.getElementById('relanceClientSelect');
            
            if (!select) {
                console.error('❌ Select relanceClientSelect introuvable');
                return;
            }
            
            // VIDER LE SELECT
            select.innerHTML = '<option value="">Sélectionner un client</option>';
            
            // AJOUTER TOUS LES CLIENTS
            clients.forEach(client => {
                const option = document.createElement('option');
                option.value = client.id; // ✅ IMPORTANT : Utiliser client.id (number)
                option.textContent = `${client.nom} ${client.prenoms || ''} - ${client.contact}`;
                select.appendChild(option);
            });
            
            console.log('✅ Select peuplé avec', clients.length, 'clients');
            
        } else {
            console.error('❌ Erreur dans les données:', data);
            showToast('Erreur de chargement des clients', 'error');
        }
    } catch (error) {
        console.error('❌ Erreur loadClientsForRelanceSelect:', error);
        showToast('Erreur de connexion', 'error');
    }
}
// ✅ NOUVEAU CODE (CHARGE LES CLIENTS)
async function showAddRelanceModal() {
    // Réinitialiser
    document.getElementById('relanceId').value = '';
    document.getElementById('relanceForm').reset();
    
    // Charger les clients si le select est vide
    const select = document.getElementById('relanceClientSelect');
    if (!select || select.options.length <= 1) {
        console.log('📋 Chargement initial des clients...');
        await loadClientsForRelanceSelect();
        await new Promise(resolve => setTimeout(resolve, 200));
    }
    
    // Ouvrir le modal
    document.getElementById('relanceModal').classList.remove('hidden');
}

function closeRelanceModal() {
    document.getElementById('relanceModal').classList.add('hidden');
    document.getElementById('relanceForm').reset();
}

async function quickRelanceClient(clientId) {
    try {
        console.log('📞 Début quickRelanceClient pour client ID:', clientId, 'Type:', typeof clientId);

        // ✅ VÉRIFICATION : Empêcher la relance des clients avec "Visa validé"
        const client = currentClients.find(c => c.id === clientId);
        if (client && client.statut === 'Visa validé') {
            showToast('❌ Ce client a obtenu son visa validé. Les relances ne sont plus nécessaires.', 'info');
            return;
        }

        // 1️⃣ RÉINITIALISER LE FORMULAIRE
        document.getElementById('relanceForm').reset();
        
        // 2️⃣ CHARGER LES CLIENTS
        console.log('📋 Chargement de la liste des clients...');
        await loadClientsForRelanceSelect();
        
        // 3️⃣ ATTENDRE QUE LE SELECT SOIT BIEN REMPLI
        await new Promise(resolve => setTimeout(resolve, 300));
        
        // 4️⃣ RÉCUPÉRER LE SELECT
        const select = document.getElementById('relanceClientSelect');
        
        if (!select) {
            console.error('❌ Select relanceClientSelect introuvable');
            showToast('Erreur: Formulaire non trouvé', 'error');
            return;
        }
        
        console.log('📝 Nombre d\'options dans le select:', select.options.length);
        
        // 5️⃣ CONVERTIR clientId EN STRING (car les valeurs du select sont des strings)
        const clientIdStr = String(clientId);
        console.log('🔍 Recherche du client avec ID (string):', clientIdStr);
        
        // 6️⃣ VÉRIFIER QUE LE CLIENT EXISTE DANS LE SELECT
        let clientFound = false;
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].value === clientIdStr) {
                clientFound = true;
                console.log('✅ Client trouvé à l\'index', i, ':', select.options[i].text);
                break;
            }
        }
        
        if (!clientFound) {
            console.error('❌ Client non trouvé dans le select. Options disponibles:', 
                Array.from(select.options).map(o => ({value: o.value, text: o.text})));
            showToast('Erreur: Client non trouvé dans la liste', 'error');
            return;
        }
        
        // 7️⃣ SÉLECTIONNER LE CLIENT
        select.value = clientIdStr;
        console.log('🎯 Valeur du select après affectation:', select.value);
        
        // 8️⃣ DÉCLENCHER L'ÉVÉNEMENT CHANGE POUR METTRE À JOUR L'AFFICHAGE
        const changeEvent = new Event('change', { bubbles: true });
        select.dispatchEvent(changeEvent);
        
        // 9️⃣ VÉRIFICATION FINALE
        if (select.value === clientIdStr) {
            console.log('✅ Client correctement sélectionné !');
            updateSelectedClientInfo();
        } else {
            console.error('❌ Échec de la sélection. Valeur actuelle:', select.value);
            showToast('Erreur: Impossible de sélectionner le client', 'error');
            return;
        }
        
        // 🔟 OUVRIR LE MODAL
        document.getElementById('relanceModal').classList.remove('hidden');
        
        // FOCUS SUR LE COMMENTAIRE
        setTimeout(() => {
            document.getElementById('relanceCommentaire').focus();
        }, 100);
        
        console.log('✅ Modal ouvert avec succès');
        
    } catch (error) {
        console.error('❌ Erreur complète quickRelanceClient:', error);
        showToast('Erreur lors de l\'ouverture du formulaire: ' + error.message, 'error');
    }
}

async function cloturerRelance(relanceId) {
    if (!confirm('Clôturer cette relance ?')) return;

    try {
        const response = await fetch(`${API_BASE}/relances/${relanceId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                statut: 'Clôturé',
                commentaire: 'Relance clôturée'
            })
        });

        const result = await response.json();

        if (result.success) {
            showToast('Relance clôturée', 'success');
            await loadRelances();
            await refreshAllRelancesStats(); // ✅ Rafraîchir toutes les stats de relances
        } else {
            showToast(result.error || 'Erreur', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur', 'error');
    }
}

async function viewClientRelances(clientId) {
    try {
        const response = await fetch(`${API_BASE}/relances/client/${clientId}`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });
        const data = await response.json();
        
        if (data.success) {
            const client = data.client;
            const relances = data.relances;
            
            const relancesHtml = relances.length > 0 ? `
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>DATE/HEURE</th>
                                <th>AGENT</th>
                                <th>COMMENTAIRE</th>
                                <th>STATUT</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${relances.map(r => {
                                const date = new Date(r.date_relance);
                                return `
                                    <tr>
                                        <td>
                                            <div class="font-semibold">${formatDate(r.date_relance)}</div>
                                            <div class="text-xs text-secondary">${date.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'})}</div>
                                        </td>
                                        <td>${r.agent_name}</td>
                                        <td class="text-sm">${r.commentaire}</td>
                                        <td><span class="badge ${r.statut === 'Clôturé' ? 'badge-success' : 'badge-warning'}">${r.statut}</span></td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
            ` : '<p class="text-center text-secondary">Aucune relance pour ce client</p>';
            
            const modalHtml = `
                <div class="modal-overlay" style="display: flex;" id="relancesHistoryModal">
                    <div class="modal-content" style="max-width: 800px;">
                        <div class="modal-header">
                            <h3 class="modal-title">📋 Historique Relances - ${client.nom} ${client.prenoms || ''}</h3>
                            <button class="close-btn" onclick="closeRelancesHistoryModal()">×</button>
                        </div>
                        <div style="margin-bottom: 1rem;">
                            <p class="text-sm text-secondary">Contact: ${client.contact}</p>
                            <p class="text-sm text-secondary">Total relances: <strong>${relances.length}</strong></p>
                        </div>
                        ${relancesHtml}
                        <div class="flex gap-2 mt-4">
                            <button class="btn btn-primary" onclick="quickRelanceClient(${client.id}); closeRelancesHistoryModal();">
                                📞 Nouvelle Relance
                            </button>
                            <button class="btn w-full" onclick="closeRelancesHistoryModal()">Fermer</button>
                        </div>
                    </div>
                </div>
            `;
            
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = modalHtml;
            document.body.appendChild(tempDiv.firstElementChild);
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur', 'error');
    }
}

function closeRelancesHistoryModal() {
    const modal = document.getElementById('relancesHistoryModal');
    if (modal) modal.remove();
}

let relanceSearchTimeout;
function searchRelances() {
    clearTimeout(relanceSearchTimeout);
    relanceSearchTimeout = setTimeout(() => {
        loadRelances();
    }, 500);
}

function updateSelectedClientInfo() {
    const select = document.getElementById('relanceClientSelect');
    const infoDiv = document.getElementById('selectedClientInfo');
    const nameDiv = document.getElementById('selectedClientName');
    
    if (select && select.value && infoDiv && nameDiv) {
        const selectedText = select.options[select.selectedIndex].text;
        nameDiv.textContent = selectedText;
        infoDiv.style.display = 'block';
    } else if (infoDiv) {
        infoDiv.style.display = 'none';
    }
}

// ✅ CODE JAVASCRIPT CORRIGÉ POUR LE MODAL DE RELANCE

// ==================== VARIABLES GLOBALES ====================
let currentRelanceClient = null;
let relanceTemplates = {};
let selectedCanal = 'whatsapp';

// ==================== CHARGER LES TEMPLATES DE RELANCES ====================
async function loadRelanceTemplates(statutClient, canal = 'whatsapp') {
    try {
        console.log('🌐 Appel API pour:', statutClient, '/', canal);
        
        const url = `${API_BASE}/relances/templates?statut=${encodeURIComponent(statutClient)}&canal=${encodeURIComponent(canal)}`;
        console.log('📡 URL:', url);
        
        const response = await fetch(url, {
            headers: { 
                'X-CSRF-TOKEN': CSRF_TOKEN, 
                'Accept': 'application/json' 
            }
        });
        
        console.log('📥 Réponse HTTP:', response.status);
        
        if (!response.ok) {
            console.error('❌ Erreur HTTP:', response.status, response.statusText);
            return null;
        }
        
        const data = await response.json();
        console.log('📦 Données reçues:', data);
        
        if (data.success && data.template) {
            console.log('✅ Template trouvé:', data.template);
            return data.template;
        } else {
            console.warn('⚠️ Aucun template dans la réponse');
            return null;
        }
        
    } catch (error) {
        console.error('❌ Erreur loadRelanceTemplates:', error);
        return null;
    }
}

// ==================== AFFICHER LE MODAL DE RELANCE AVEC TEMPLATES ====================
async function showAddRelanceModal() {
    // Réinitialiser
    document.getElementById('relanceId').value = '';
    document.getElementById('relanceForm').reset();
    selectedCanal = 'whatsapp';
    
    // Charger les clients
    const select = document.getElementById('relanceClientSelect');
    if (!select || select.options.length <= 1) {
        await loadClientsForRelanceSelect();
        await new Promise(resolve => setTimeout(resolve, 200));
    }
    
    // Afficher les boutons de canal
    renderCanalButtons();
    
    // Ouvrir le modal
    document.getElementById('relanceModal').classList.remove('hidden');
}

// ==================== RENDU DES BOUTONS DE CANAL ====================
function renderCanalButtons() {
    const container = document.getElementById('relanceCanalButtons');
    if (!container) return;
    
    const canaux = [
        { value: 'whatsapp', label: 'WhatsApp', icon: '💬', color: '#25D366' },
        { value: 'sms', label: 'SMS', icon: '📱', color: '#007AFF' },
        { value: 'email', label: 'Email', icon: '📧', color: '#EA4335' }
    ];
    
    container.innerHTML = canaux.map(canal => `
        <button type="button" 
                class="canal-btn ${selectedCanal === canal.value ? 'active' : ''}" 
                onclick="selectCanal('${canal.value}')"
                style="
                    flex: 1;
                    padding: 0.75rem;
                    border-radius: 8px;
                    border: 2px solid ${selectedCanal === canal.value ? canal.color : '#e2e8f0'};
                    background: ${selectedCanal === canal.value ? canal.color + '15' : 'white'};
                    color: ${selectedCanal === canal.value ? canal.color : '#64748b'};
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.2s;
                ">
            ${canal.icon} ${canal.label}
        </button>
    `).join('');
}

// ==================== SÉLECTIONNER UN CANAL ====================
async function selectCanal(canal) {
    selectedCanal = canal;
    renderCanalButtons();
    
    // Recharger le template si un client est sélectionné
    const clientSelect = document.getElementById('relanceClientSelect');
    if (clientSelect && clientSelect.value) {
        const client = currentClients.find(c => c.id === parseInt(clientSelect.value));
        if (client) {
            await updateRelanceTemplate(client.statut);
        }
    }
}

// ==================== METTRE À JOUR LE TEMPLATE DE RELANCE ====================
async function updateRelanceTemplate(statutClient) {
    console.log('📝 Chargement template pour statut:', statutClient, '- Canal:', selectedCanal);
    
    const commentaireField = document.getElementById('relanceCommentaire');
    const emailSubjectField = document.getElementById('relanceEmailSubject');
    
    if (!commentaireField) {
        console.error('❌ Champ commentaire non trouvé');
        return;
    }
    
    // Afficher un loader
    commentaireField.value = '⏳ Chargement du template...';
    commentaireField.disabled = true;
    
    try {
        const template = await loadRelanceTemplates(statutClient, selectedCanal);
        
        console.log('📄 Template reçu:', template);
        
        if (!template) {
            console.warn('⚠️ Aucun template disponible');
            commentaireField.value = '';
            commentaireField.placeholder = `Aucun template disponible pour ${statutClient} via ${selectedCanal}`;
            commentaireField.disabled = false;
            return;
        }
        
        // Afficher/masquer le champ sujet email
        const subjectContainer = document.getElementById('emailSubjectContainer');
        if (subjectContainer) {
            subjectContainer.style.display = selectedCanal === 'email' ? 'block' : 'none';
        }
        
        if (selectedCanal === 'email' && typeof template === 'object') {
            // Email avec sujet + corps
            if (emailSubjectField) {
                emailSubjectField.value = template.subject || '';
            }
            commentaireField.value = template.body || '';
            commentaireField.placeholder = "Corps du message email...";
        } else {
            // SMS/WhatsApp
            const icon = selectedCanal === 'sms' ? '📱' : '💬';
            commentaireField.value = template || '';
            commentaireField.placeholder = `Message ${selectedCanal}...`;
        }
        
        commentaireField.disabled = false;
        
        // Afficher une confirmation
        console.log('✅ Template chargé avec succès');
        
    } catch (error) {
        console.error('❌ Erreur lors du chargement du template:', error);
        commentaireField.value = '';
        commentaireField.placeholder = 'Erreur de chargement du template';
        commentaireField.disabled = false;
        showToast('Erreur de chargement du template', 'error');
    }
}

// ==================== RELANCE RAPIDE D'UN CLIENT ====================
async function quickRelanceClient(clientId) {
    try {
        console.log('📞 Début quickRelanceClient pour client ID:', clientId);
        
        // ✅ CORRECTION 1 : Réinitialiser le formulaire
        document.getElementById('relanceForm').reset();
        selectedCanal = 'whatsapp';
        
        // ✅ CORRECTION 2 : S'assurer que le client existe dans currentClients
        let client = currentClients.find(c => c.id === parseInt(clientId));
        
        // Si pas trouvé, essayer de charger depuis l'API
        if (!client) {
            console.log('⚠️ Client non trouvé dans currentClients, chargement depuis API...');
            
            const response = await fetch(`${API_BASE}/clients/${clientId}`, {
                headers: { 
                    'X-CSRF-TOKEN': CSRF_TOKEN, 
                    'Accept': 'application/json' 
                }
            });
            
            const data = await response.json();
            
            if (data.success && data.client) {
                client = data.client;
                
                // Ajouter à currentClients pour la prochaine fois
                currentClients.push(client);
                
                console.log('✅ Client chargé depuis API:', client.nom);
            } else {
                throw new Error('Client introuvable dans la base de données');
            }
        }

        // ✅ VÉRIFICATION : Empêcher la relance des clients avec "Visa validé"
        if (client && client.statut === 'Visa validé') {
            showToast('❌ Ce client a obtenu son visa validé. Les relances ne sont plus nécessaires.', 'info');
            return;
        }

        // ✅ CORRECTION 3 : Charger TOUS les clients pour le select
        console.log('📋 Chargement de la liste des clients...');
        await loadClientsForRelanceSelect();
        
        // Attendre que le select soit bien rempli
        await new Promise(resolve => setTimeout(resolve, 300));
        
        // ✅ CORRECTION 4 : Récupérer et peupler le select
        const select = document.getElementById('relanceClientSelect');
        
        if (!select) {
            throw new Error('Select relanceClientSelect introuvable');
        }
        
        console.log('🔍 Nombre d\'options dans le select:', select.options.length);
        
        // ✅ CORRECTION 5 : Convertir en STRING (valeurs du select sont TOUJOURS des strings)
        const clientIdStr = String(clientId);
        
        // ✅ CORRECTION 6 : Vérifier si le client existe dans le select
        let optionExists = false;
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].value === clientIdStr) {
                optionExists = true;
                console.log('✅ Client trouvé à l\'index', i);
                break;
            }
        }
        
        if (!optionExists) {
            // ✅ CORRECTION 7 : AJOUTER MANUELLEMENT L'OPTION SI ELLE N'EXISTE PAS
            console.log('⚠️ Option manquante, ajout manuel...');
            
            const option = document.createElement('option');
            option.value = clientIdStr;
            option.textContent = `${client.nom} ${client.prenoms || ''} - ${client.contact}`;
            select.appendChild(option);
            
            console.log('✅ Option ajoutée manuellement');
        }
        
        // ✅ CORRECTION 8 : Sélectionner le client
        select.value = clientIdStr;
        
        console.log('🎯 Valeur du select après affectation:', select.value);
        
        // ✅ CORRECTION 9 : Déclencher l'événement change
        const changeEvent = new Event('change', { bubbles: true });
        select.dispatchEvent(changeEvent);
        
        // ✅ CORRECTION 10 : Vérification finale
        if (select.value !== clientIdStr) {
            throw new Error(`Échec de la sélection. Valeur actuelle: ${select.value}, attendue: ${clientIdStr}`);
        }
        
        console.log('✅ Client correctement sélectionné !');
        
        // Stocker le client pour référence
        currentRelanceClient = client;
        
        // Mettre à jour l'affichage
        updateSelectedClientInfo();
        renderCanalButtons();
        
        // ✅ CORRECTION 11 : Charger le template APRÈS avoir sélectionné le client
        await updateRelanceTemplate(client.statut);
        
        // Ouvrir le modal
        document.getElementById('relanceModal').classList.remove('hidden');
        
        // Focus sur le commentaire
        setTimeout(() => {
            document.getElementById('relanceCommentaire').focus();
        }, 100);
        
        console.log('✅ Modal ouvert avec succès');
        
    } catch (error) {
        console.error('❌ Erreur quickRelanceClient:', error);
        showToast('Erreur: ' + error.message, 'error');
    }
}

// ==================== SAUVEGARDER LA RELANCE AVEC CANAL ====================
async function saveRelance(event) {
    event.preventDefault();
    
    const form = document.getElementById('relanceForm');
    const formData = new FormData(form);
    
    // Récupérer les données
    const clientId = formData.get('client_id');
    const commentaire = formData.get('commentaire');
    const statut = formData.get('statut');
    
    // Validation
    if (!clientId) {
        showToast('Veuillez sélectionner un client', 'error');
        return;
    }
    
    if (!commentaire || commentaire.trim() === '') {
        showToast('Veuillez ajouter un commentaire', 'error');
        return;
    }
    
    // ✅ Ajouter le canal sélectionné
    const data = {
        client_id: parseInt(clientId),
        commentaire: commentaire.trim(),
        statut: statut,
        canal: selectedCanal
    };
    
    // Ajouter le sujet email si nécessaire
    if (selectedCanal === 'email') {
        const emailSubject = document.getElementById('relanceEmailSubject')?.value;
        if (emailSubject) {
            data.email_subject = emailSubject;
        }
    }
    
    try {
        const response = await fetch(`${API_BASE}/relances`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('✅ ' + result.message, 'success');
            closeRelanceModal();
            await loadRelances();
            await refreshAllRelancesStats(); // ✅ Rafraîchir toutes les stats de relances
        } else {
            showToast(result.error || 'Erreur', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur de connexion', 'error');
    }
}

// ==================== FERMER LE MODAL ====================
function closeRelanceModal() {
    // Réafficher le select pour la prochaine fois
    const select = document.getElementById('relanceClientSelect');
    if (select) {
        const selectGroup = select.closest('.form-group');
        if (selectGroup) {
            selectGroup.style.display = 'block';
        }
    }
    
    document.getElementById('relanceModal').classList.add('hidden');
    document.getElementById('relanceForm').reset();
    currentRelanceClient = null;
    selectedCanal = 'whatsapp';
}
// ==================== METTRE À JOUR L'INFO CLIENT SÉLECTIONNÉ ====================
// ==================== METTRE À JOUR L'INFO CLIENT SÉLECTIONNÉ ====================
async function updateSelectedClientInfo() {
    const select = document.getElementById('relanceClientSelect');
    const infoDiv = document.getElementById('selectedClientInfo');
    const nameDiv = document.getElementById('selectedClientName');
    
    if (select && select.value && infoDiv && nameDiv) {
        const selectedText = select.options[select.selectedIndex].text;
        nameDiv.textContent = selectedText;
        infoDiv.style.display = 'block';
        
        // Charger le template automatiquement
        const clientId = parseInt(select.value);
        console.log('🔍 Client sélectionné ID:', clientId);
        
        // ✅ CORRECTION : Chercher dans currentClients OU charger depuis API
        let client = currentClients.find(c => c.id === clientId);
        
        if (!client) {
            console.log('⚠️ Client non trouvé, rechargement depuis API...');
            
            try {
                const response = await fetch(`${API_BASE}/clients/${clientId}`, {
                    headers: { 
                        'X-CSRF-TOKEN': CSRF_TOKEN, 
                        'Accept': 'application/json' 
                    }
                });
                
                const data = await response.json();
                
                if (data.success && data.client) {
                    client = data.client;
                    currentClients.push(client);
                    console.log('✅ Client chargé:', client.nom);
                } else {
                    console.error('❌ Client non trouvé dans la réponse API');
                    return;
                }
            } catch (error) {
                console.error('❌ Erreur chargement client:', error);
                return;
            }
        }
        
        console.log('✅ Client trouvé:', client.nom, '- Statut:', client.statut);
        currentRelanceClient = client;
        
        // ✅ CHARGER LE TEMPLATE
        await updateRelanceTemplate(client.statut);
        
        // ✅ AJOUTER BOUTON CHANGEMENT STATUT
        addStatutChangeButton(client);
        
    } else if (infoDiv) {
        infoDiv.style.display = 'none';
    }
}

// ✅ NOUVELLE FONCTION : Ajouter un bouton pour changer le statut du client
function addStatutChangeButton(client) {
    const infoDiv = document.getElementById('selectedClientInfo');
    
    // Vérifier si le bouton existe déjà
    let buttonContainer = document.getElementById('changeStatutContainer');
    
    if (!buttonContainer) {
        buttonContainer = document.createElement('div');
        buttonContainer.id = 'changeStatutContainer';
        buttonContainer.style.marginTop = '0.75rem';
        buttonContainer.style.padding = '0.75rem';
        buttonContainer.style.background = 'linear-gradient(135deg, #fef3c715 0%, #fde68a15 100%)';
        buttonContainer.style.borderRadius = '6px';
        buttonContainer.style.borderLeft = '4px solid #f59e0b';
        infoDiv.appendChild(buttonContainer);
    }
    
    buttonContainer.innerHTML = `
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
            <div>
                <div style="font-weight: 600; color: #92400e; margin-bottom: 0.25rem;">
                    📊 Statut actuel : <span class="badge ${getStatusBadgeClass(client.statut)}">${client.statut}</span>
                </div>
                <div style="font-size: 0.85rem; color: #78350f;">
                    Changez le statut pour adapter le message de relance
                </div>
            </div>
            <button type="button" class="btn btn-warning btn-sm" onclick="showChangeStatutModal(${client.id}, '${client.statut}')">
                🔄 Changer
            </button>
        </div>
    `;
}

// ✅ NOUVELLE FONCTION : Afficher le modal de changement de statut
function showChangeStatutModal(clientId, currentStatut) {
    const modalHtml = `
        <div class="modal-overlay" id="changeStatutModal" style="display: flex;">
            <div class="modal-content" style="max-width: 700px;">
                <div class="modal-header">
                    <h3 class="modal-title">🔄 Changer le Statut du Client</h3>
                    <button class="close-btn" onclick="closeChangeStatutModal()">×</button>
                </div>
                
                <div style="padding: 1rem 0;">
                    <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f8fafc; border-radius: 8px;">
                        <div style="font-weight: 600; margin-bottom: 0.5rem;">💡 Comment utiliser les phases :</div>
                        <ul style="font-size: 0.9rem; color: #475569; margin: 0; padding-left: 1.5rem;">
                            <li><strong>Phase 1</strong> : Découverte (Lead → Prospect → À convertir)</li>
                            <li><strong>Phase 2</strong> : Engagement (Documents, paiements, RDV)</li>
                            <li><strong>Phase 3</strong> : Visa (Ambassade, décision)</li>
                            <li><strong>Phase 4</strong> : Voyage (Billet, départ)</li>
                            <li><strong>Phase 5</strong> : Relance spéciale</li>
                        </ul>
                    </div>
                    
                    <label class="form-label">Nouveau Statut *</label>
                    <select id="newStatutSelect" class="form-control" style="margin-bottom: 1rem;" onchange="previewStatutTemplate()">
                        <optgroup label="🔵 PHASE 1 - DÉCOUVERTE">
                            <option value="Lead" ${currentStatut === 'Lead' ? 'selected' : ''}>Lead (nouveau contact)</option>
                            <option value="Prospect" ${currentStatut === 'Prospect' ? 'selected' : ''}>Prospect (intéressé)</option>
                            <option value="À convertir" ${currentStatut === 'À convertir' ? 'selected' : ''}>À convertir (prêt à payer)</option>
                            <option value="Perdu" ${currentStatut === 'Perdu' ? 'selected' : ''}>Perdu (abandon)</option>
                        </optgroup>
                        
                        <optgroup label="🟢 PHASE 2 - ENGAGEMENT">
                            <option value="Profil visa payé" ${currentStatut === 'Profil visa payé' ? 'selected' : ''}>Profil visa payé</option>
                            <option value="En attente de paiement des frais de profil visa et d'inscription">
                                En attente frais profil visa (115.000 F)
                            </option>
                            <option value="En attente de paiement des frais de cabinet">
                                En attente frais de cabinet (500.000 F)
                            </option>
                            <option value="Frais d'assistance payés" ${currentStatut === "Frais d'assistance payés" ? 'selected' : ''}>Frais d'assistance payés</option>
                            <option value="En attente de documents" ${currentStatut === 'En attente de documents' ? 'selected' : ''}>En attente de documents</option>
                            <option value="Documents validés" ${currentStatut === 'Documents validés' ? 'selected' : ''}>Documents validés</option>
                            <option value="Rendez-vous au bureau PSI" ${currentStatut === 'Rendez-vous au bureau PSI' ? 'selected' : ''}>Rendez-vous au bureau PSI</option>
                            <option value="Rendez-vous d'urgence" ${currentStatut === "Rendez-vous d'urgence" ? 'selected' : ''}>Rendez-vous d'urgence</option>
                        </optgroup>
                        
                        <optgroup label="🟡 PHASE 3 - VISA">
                            <option value="Prise de RDV ambassade confirmée" ${currentStatut === 'Prise de RDV ambassade confirmée' ? 'selected' : ''}>Prise de RDV ambassade</option>
                            <option value="En attente de décision visa" ${currentStatut === 'En attente de décision visa' ? 'selected' : ''}>En attente décision visa</option>
                            <option value="Visa accepté" ${currentStatut === 'Visa accepté' ? 'selected' : ''}>Visa accepté</option>
                            <option value="Visa refusé" ${currentStatut === 'Visa refusé' ? 'selected' : ''}>Visa refusé</option>
                            <option value="Visa validé" ${currentStatut === 'Visa validé' ? 'selected' : ''}>Visa validé</option>
                        </optgroup>
                        
                        <optgroup label="🟣 PHASE 4 - VOYAGE">
                            <option value="Billet d'avion payé" ${currentStatut === "Billet d'avion payé" ? 'selected' : ''}>Billet d'avion payé</option>
                            <option value="Départ confirmé" ${currentStatut === 'Départ confirmé' ? 'selected' : ''}>Départ confirmé</option>
                            <option value="En suivi post-départ" ${currentStatut === 'En suivi post-départ' ? 'selected' : ''}>En suivi post-départ</option>
                        </optgroup>
                        
                        <optgroup label="🔴 PHASE 5 - RELANCE SPÉCIALE">
                            <option value="Négociation" ${currentStatut === 'Négociation' ? 'selected' : ''}>Négociation en cours</option>
                            <option value="Message d'urgence" ${currentStatut === "Message d'urgence" ? 'selected' : ''}>Message d'urgence</option>
                            <option value="Opportunité" ${currentStatut === 'Opportunité' ? 'selected' : ''}>Opportunité (reprise)</option>
                        </optgroup>
                    </select>
                    
                    <div id="templatePreview" style="padding: 1rem; background: #f8fafc; border-radius: 8px; font-size: 0.9rem; color: #475569;">
                        Sélectionnez un statut pour voir le template...
                    </div>
                </div>
                
                <div class="flex gap-2 mt-4">
                    <button type="button" class="btn btn-success w-full" onclick="saveNewStatut(${clientId})">
                        💾 Enregistrer et Recharger le Template
                    </button>
                    <button type="button" class="btn w-full" onclick="closeChangeStatutModal()">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Injecter dans le DOM
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = modalHtml;
    document.body.appendChild(tempDiv.firstElementChild);
    
    // Prévisualiser immédiatement
    previewStatutTemplate();
}

// ✅ NOUVELLE FONCTION : Prévisualiser le template selon le statut sélectionné
async function previewStatutTemplate() {
    const select = document.getElementById('newStatutSelect');
    const preview = document.getElementById('templatePreview');
    
    if (!select || !preview) return;
    
    const newStatut = select.value;
    const canal = selectedCanal || 'whatsapp';
    
    preview.innerHTML = '<div class="text-center">⏳ Chargement du template...</div>';
    
    try {
        const response = await fetch(`${API_BASE}/relances/templates?statut=${encodeURIComponent(newStatut)}&canal=${encodeURIComponent(canal)}`, {
            headers: { 
                'X-CSRF-TOKEN': CSRF_TOKEN, 
                'Accept': 'application/json' 
            }
        });
        
        const data = await response.json();
        
        if (data.success && data.template) {
            let templateText = '';
            
            if (typeof data.template === 'object' && data.template.subject) {
                // Email
                templateText = `<strong>📧 Objet:</strong> ${data.template.subject}<br><br><strong>Corps:</strong><br>${data.template.body.replace(/\n/g, '<br>')}`;
            } else {
                // SMS/WhatsApp
                const icon = canal === 'sms' ? '📱' : '💬';
                templateText = `<strong>${icon} ${canal.toUpperCase()}:</strong><br><br>${data.template}`;
            }
            
            preview.innerHTML = `
                <div style="font-weight: 600; margin-bottom: 0.5rem; color: #16a34a;">
                    ✅ Aperçu du template pour "${newStatut}" :
                </div>
                <div style="padding: 0.75rem; background: white; border-radius: 6px; border-left: 4px solid #16a34a;">
                    ${templateText}
                </div>
            `;
        } else {
            preview.innerHTML = '<div class="text-danger">❌ Aucun template disponible pour ce statut</div>';
        }
    } catch (error) {
        console.error('Erreur:', error);
        preview.innerHTML = '<div class="text-danger">❌ Erreur de chargement du template</div>';
    }
}

// ✅ NOUVELLE FONCTION : Sauvegarder le nouveau statut et recharger le template
// ✅ NOUVELLE FONCTION : Sauvegarder le nouveau statut et recharger le template
async function saveNewStatut(clientId) {
    const select = document.getElementById('newStatutSelect');
    const newStatut = select.value;
    
    if (!newStatut) {
        showToast('Veuillez sélectionner un statut', 'error');
        return;
    }
    
    try {
        // Mettre à jour le client
        const response = await fetch(`${API_BASE}/clients/${clientId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ statut: newStatut })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('✅ Statut mis à jour avec succès', 'success');
            
            // Fermer le modal
            closeChangeStatutModal();
            
            // ✅ METTRE À JOUR LE CLIENT DANS TOUS LES TABLEAUX
            const clientIndex = currentClients.findIndex(c => c.id === clientId);
            if (clientIndex !== -1) {
                currentClients[clientIndex].statut = newStatut;
            }
            
            // ✅ AUSSI DANS clientsARelancer
            if (clientsARelancer && clientsARelancer.length > 0) {
                const clientRelanceIndex = clientsARelancer.findIndex(c => c.id === clientId);
                if (clientRelanceIndex !== -1) {
                    clientsARelancer[clientRelanceIndex].statut = newStatut;
                }
            }
            
            // ✅ RAFRAÎCHIR L'AFFICHAGE SELON LE MODULE ACTIF
            const activePanel = document.querySelector('.panel:not(.hidden)');
            
            if (activePanel) {
                const panelId = activePanel.id;
                console.log('📍 Panel actif:', panelId);
                
                // Rafraîchir selon le panel actif
                if (panelId === 'clients-panel') {
                    console.log('🔄 Rafraîchissement du module Clients...');
                    await loadClients();
                } else if (panelId === 'relances-panel') {
                    console.log('🔄 Rafraîchissement du module Relances...');
                    await loadRelances();
                } else if (panelId === 'dashboard-panel') {
                    console.log('🔄 Rafraîchissement du Dashboard...');
                    await loadDashboard();
                }
            }
            
            // ✅ SI ON EST DANS LE MODAL DE RELANCE
            if (!document.getElementById('relanceModal').classList.contains('hidden')) {
                // Recharger le template de relance avec le nouveau statut
                await updateRelanceTemplate(newStatut);
                
                // Mettre à jour l'affichage du client sélectionné
                await updateSelectedClientInfo();
            }
            
            // ✅ SI ON EST DANS LE MODAL DE DÉTAILS CLIENT
            if (!document.getElementById('viewClientModal').classList.contains('hidden')) {
                // Recharger les détails du client
                await viewClient(clientId);
            }
            
        } else {
            showToast('❌ Erreur : ' + (data.error || 'Impossible de mettre à jour'), 'error');
        }
        
    } catch (error) {
        console.error('Erreur saveNewStatut:', error);
        showToast('❌ Erreur de connexion', 'error');
    }
}
// ✅ FERMER LE MODAL DE CHANGEMENT DE STATUT
function closeChangeStatutModal() {
    const modal = document.getElementById('changeStatutModal');
    if (modal) {
        modal.remove();
    }
}   

// ==================== COMMENTAIRES CLIENT ====================

function showAddCommentaireModal(clientId) {
    const client = currentClients.find(c => c.id === clientId);
    if (!client) {
        showToast('Client introuvable', 'error');
        return;
    }
    
    document.getElementById('commentaireClientId').value = clientId;
    document.getElementById('commentaireClientName').textContent = `${client.nom} ${client.prenoms || ''} - ${client.contact}`;
    document.getElementById('commentaireText').value = '';
    
    document.getElementById('commentaireModal').classList.remove('hidden');
    
    // Focus sur le textarea
    setTimeout(() => {
        document.getElementById('commentaireText').focus();
    }, 100);
}

function closeCommentaireModal() {
    document.getElementById('commentaireModal').classList.add('hidden');
    document.getElementById('commentaireForm').reset();
}

async function saveCommentaire(event) {
    event.preventDefault();
    
    const clientId = document.getElementById('commentaireClientId').value;
    const commentaire = document.getElementById('commentaireText').value;
    
    if (!commentaire || commentaire.trim() === '') {
        showToast('Veuillez saisir un commentaire', 'error');
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/clients/${clientId}/commentaire`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ commentaire: commentaire.trim() })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('✅ Commentaire ajouté avec succès', 'success');
            closeCommentaireModal();
            
            // Rafraîchir la vue si on est dans les détails client
            if (selectedClient && selectedClient.id == clientId) {
                await viewClient(clientId);
            }
        } else {
            showToast('❌ Erreur : ' + (result.error || 'Impossible d\'ajouter le commentaire'), 'error');
        }
        
    } catch (error) {
        console.error('Erreur saveCommentaire:', error);
        showToast('❌ Erreur de connexion', 'error');
    }
}

// ✅ FONCTION POUR DÉBOGUER LES CLICS
function debugClick(clientId, action) {
    console.log('🔍 Action déclenchée:', action, 'pour client:', clientId);
    
    if (!clientId) {
        console.error('❌ ID client manquant');
        showToast('Erreur: ID client manquant', 'error');
        return false;
    }
    
    return true;
}

// ✅ WRAPPER SÉCURISÉ POUR quickRelanceClient
window.safeQuickRelance = async function(clientId) {
    if (!debugClick(clientId, 'quickRelance')) return;
    
    try {
        await quickRelanceClient(clientId);
    } catch (error) {
        console.error('❌ Erreur quickRelance:', error);
        showToast('Erreur: ' + error.message, 'error');
    }
};

// ✅ WRAPPER SÉCURISÉ POUR viewClient
window.safeViewClient = async function(clientId) {
    if (!debugClick(clientId, 'viewClient')) return;
    
    try {
        await viewClient(clientId);
    } catch (error) {
        console.error('❌ Erreur viewClient:', error);
        showToast('Erreur: ' + error.message, 'error');
    }
};

// ✅ NOUVELLE FONCTION : Changer le statut rapidement depuis le tableau
function quickChangeStatut(clientId, currentStatut, event) {
    // Empêcher la propagation de l'événement
    if (event) {
        event.stopPropagation();
    }
    
    console.log('🔄 Changement rapide de statut pour client:', clientId, '- Statut actuel:', currentStatut);
    
    // Créer le sélecteur de statut
    const modalHtml = `
        <div class="modal-overlay" id="quickStatutModal" style="display: flex; z-index: 10000;">
            <div class="modal-content" style="max-width: 500px; animation: slideInUp 0.3s ease-out;">
                <div class="modal-header">
                    <h3 class="modal-title">🔄 Modifier le Statut</h3>
                    <button class="close-btn" onclick="closeQuickStatutModal()">×</button>
                </div>
                
                <div style="padding: 1rem 0;">
                    <div style="margin-bottom: 1rem; padding: 0.75rem; background: #f8fafc; border-left: 4px solid #667eea; border-radius: 6px;">
                        <div style="font-weight: 600; color: #475569; margin-bottom: 0.25rem;">
                            Client : ${currentClients.find(c => c.id === clientId)?.nom || 'Client'}
                        </div>
                        <div style="font-size: 0.85rem; color: #64748b;">
                            Statut actuel : <span class="badge ${getStatusBadgeClass(currentStatut)}">${currentStatut}</span>
                        </div>
                    </div>
                    
                    <label class="form-label">Nouveau Statut *</label>
                    <select id="quickStatutSelect" class="form-control" style="margin-bottom: 1rem;">
                        <optgroup label="🔵 PHASE 1 - DÉCOUVERTE">
                            <option value="Lead" ${currentStatut === 'Lead' ? 'selected' : ''}>Lead</option>
                            <option value="Prospect" ${currentStatut === 'Prospect' ? 'selected' : ''}>Prospect</option>
                            <option value="À convertir" ${currentStatut === 'À convertir' ? 'selected' : ''}>À convertir</option>
                            <option value="Perdu" ${currentStatut === 'Perdu' ? 'selected' : ''}>Perdu</option>
                        </optgroup>
                        
                        <optgroup label="🟢 PHASE 2 - ENGAGEMENT">
                            <option value="Profil visa payé" ${currentStatut === 'Profil visa payé' ? 'selected' : ''}>Profil visa payé</option>

                                <option value="En attente de paiement des frais de profil visa et d'inscription" 
                                    ${currentStatut === 'En attente de paiement des frais de profil visa et d\'inscription' ? 'selected' : ''}>
                                En attente frais profil visa (115.000 F)
                            </option>
                            <option value="En attente de paiement des frais de cabinet" 
                                    ${currentStatut === 'En attente de paiement des frais de cabinet' ? 'selected' : ''}>
                                En attente frais de cabinet (500.000 F)
                            </option>
                            
                            <option value="Profil visa payé" ${currentStatut === 'Profil visa payé' ? 'selected' : ''}>
                                Profil visa payé
                            </option>

                            <option value="Frais d'assistance payés" ${currentStatut === "Frais d'assistance payés" ? 'selected' : ''}>Frais d'assistance payés</option>
                            <option value="En attente de documents" ${currentStatut === 'En attente de documents' ? 'selected' : ''}>En attente de documents</option>
                            <option value="Documents validés" ${currentStatut === 'Documents validés' ? 'selected' : ''}>Documents validés</option>
                            <option value="Rendez-vous au bureau PSI" ${currentStatut === 'Rendez-vous au bureau PSI' ? 'selected' : ''}>Rendez-vous au bureau PSI</option>
                            <option value="Rendez-vous d'urgence" ${currentStatut === "Rendez-vous d'urgence" ? 'selected' : ''}>Rendez-vous d'urgence</option>
                        </optgroup>
                        
                        <optgroup label="🟡 PHASE 3 - VISA">
                            <option value="Prise de RDV ambassade confirmée" ${currentStatut === 'Prise de RDV ambassade confirmée' ? 'selected' : ''}>Prise de RDV ambassade</option>
                            <option value="En attente de décision visa" ${currentStatut === 'En attente de décision visa' ? 'selected' : ''}>En attente décision visa</option>
                            <option value="Visa accepté" ${currentStatut === 'Visa accepté' ? 'selected' : ''}>Visa accepté</option>
                            <option value="Visa refusé" ${currentStatut === 'Visa refusé' ? 'selected' : ''}>Visa refusé</option>
                            <option value="Visa validé" ${currentStatut === 'Visa validé' ? 'selected' : ''}>Visa validé</option>
                        </optgroup>
                        
                        <optgroup label="🟣 PHASE 4 - VOYAGE">
                            <option value="Billet d'avion payé" ${currentStatut === "Billet d'avion payé" ? 'selected' : ''}>Billet d'avion payé</option>
                            <option value="Départ confirmé" ${currentStatut === 'Départ confirmé' ? 'selected' : ''}>Départ confirmé</option>
                            <option value="En suivi post-départ" ${currentStatut === 'En suivi post-départ' ? 'selected' : ''}>En suivi post-départ</option>
                        </optgroup>
                        
                        <optgroup label="🔴 PHASE 5 - RELANCE SPÉCIALE">
                            <option value="Négociation" ${currentStatut === 'Négociation' ? 'selected' : ''}>Négociation</option>
                            <option value="Message d'urgence" ${currentStatut === "Message d'urgence" ? 'selected' : ''}>Message d'urgence</option>
                            <option value="Opportunité" ${currentStatut === 'Opportunité' ? 'selected' : ''}>Opportunité</option>
                            <option value="Converti" ${currentStatut === 'Converti' ? 'selected' : ''}>Converti</option>
                        </optgroup>

                        <optgroup label="⚪ AUTRE">
                            <option value="Autre" ${currentStatut === 'Autre' ? 'selected' : ''}>Autre (Personnalisé)</option>
                        </optgroup>
                    </select>

                    <!-- Champ personnalisé pour "Autre" statut dans modal quick change -->
                    <div id="quickAutreStatutField" style="display: none; margin-top: 1rem;">
                        <label class="form-label">Précisez le statut personnalisé *</label>
                        <input type="text" id="quickStatutAutre" class="form-control"
                               placeholder="Entrez le statut personnalisé...">
                        <small style="display: block; margin-top: 0.25rem; color: #64748b; font-size: 0.85rem;">
                            Ce champ est obligatoire si vous sélectionnez "Autre"
                        </small>
                    </div>

                    <div style="padding: 0.75rem; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 6px; font-size: 0.85rem; color: #78350f; margin-top: 1rem;">
                        💡 <strong>Astuce :</strong> Le changement de statut mettra automatiquement à jour le template de relance pour ce client
                    </div>
                </div>
                
                <div class="flex gap-2 mt-4">
                    <button type="button" class="btn btn-success w-full" onclick="saveQuickStatut(${clientId})">
                        💾 Enregistrer
                    </button>
                    <button type="button" class="btn w-full" onclick="closeQuickStatutModal()">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Injecter dans le DOM
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = modalHtml;
    document.body.appendChild(tempDiv.firstElementChild);

    // Initialiser la gestion du champ "Autre"
    initQuickStatutAutreField();
}

// ✅ FONCTION POUR SAUVEGARDER LE CHANGEMENT RAPIDE DE STATUT
// ✅ FONCTION POUR SAUVEGARDER LE CHANGEMENT RAPIDE DE STATUT
async function saveQuickStatut(clientId) {
    const select = document.getElementById('quickStatutSelect');
    let newStatut = select.value;

    if (!newStatut) {
        showToast('Veuillez sélectionner un statut', 'error');
        return;
    }

    // Si "Autre" est sélectionné, vérifier et utiliser le champ personnalisé
    if (newStatut === 'Autre') {
        const autreInput = document.getElementById('quickStatutAutre');
        const autreValue = autreInput?.value?.trim();

        if (!autreValue) {
            showToast('Veuillez préciser le statut personnalisé', 'error');
            autreInput?.focus();
            return;
        }

        newStatut = autreValue;
    }

    // Afficher un indicateur de chargement
    const saveButton = document.querySelector('#quickStatutModal .btn-success');
    const originalText = saveButton.innerHTML;
    saveButton.innerHTML = '⏳ Mise à jour...';
    saveButton.disabled = true;

    try {
        console.log('📤 Envoi mise à jour statut:', { clientId, newStatut });

        const response = await fetch(`${API_BASE}/clients/${clientId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ statut: newStatut })
        });
        
        console.log('📥 Réponse HTTP:', response.status);
        
        const data = await response.json();
        console.log('📊 Données reçues:', data);
        
        if (data.success) {
            showToast('✅ Statut mis à jour avec succès', 'success');
            
            // Fermer le modal
            closeQuickStatutModal();
            
            // ✅ CORRECTION PRINCIPALE : Mettre à jour le client dans tous les tableaux
            const clientIndex = currentClients.findIndex(c => c.id === clientId);
            if (clientIndex !== -1) {
                currentClients[clientIndex].statut = newStatut;
                console.log('✅ Client mis à jour dans currentClients');
            }
            
            if (clientsARelancer && clientsARelancer.length > 0) {
                const clientRelanceIndex = clientsARelancer.findIndex(c => c.id === clientId);
                if (clientRelanceIndex !== -1) {
                    clientsARelancer[clientRelanceIndex].statut = newStatut;
                    console.log('✅ Client mis à jour dans clientsARelancer');
                }
            }
            
            // ✅ RAFRAÎCHIR L'AFFICHAGE SELON LE MODULE ACTIF
            const activePanel = document.querySelector('.panel:not(.hidden)');
            
            if (activePanel) {
                const panelId = activePanel.id;
                console.log('📍 Panel actif:', panelId);
                
                // Attendre 500ms avant de rafraîchir pour laisser le temps au serveur
                setTimeout(async () => {
                    if (panelId === 'clients-panel') {
                        console.log('🔄 Rafraîchissement du module Clients...');
                        await loadClients();
                    } else if (panelId === 'relances-panel') {
                        console.log('🔄 Rafraîchissement du module Relances...');
                        await loadRelances();
                    } else if (panelId === 'dashboard-panel') {
                        console.log('🔄 Rafraîchissement du Dashboard...');
                        await loadDashboard();
                    }
                    
                    console.log('✅ Rafraîchissement terminé');
                }, 500);
            }
        } else {
            saveButton.innerHTML = originalText;
            saveButton.disabled = false;
            showToast('❌ Erreur : ' + (data.error || 'Impossible de mettre à jour'), 'error');
        }
        
    } catch (error) {
        console.error('❌ Erreur saveQuickStatut:', error);
        saveButton.innerHTML = originalText;
        saveButton.disabled = false;
        showToast('❌ Erreur de connexion: ' + error.message, 'error');
    }
}

// ✅ FERMER LE MODAL RAPIDE
function closeQuickStatutModal() {
    const modal = document.getElementById('quickStatutModal');
    if (modal) {
        modal.remove();
    }
}

// ==================== ÉDITION CLIENT ====================

async function showEditClientModal(clientId) {
    // Vérifier d'abord les permissions
    if (!userActionPermissions.edit_clients) {
        showToast('❌ Vous n\'avez pas la permission de modifier les clients', 'error');
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/clients/${clientId}`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });
        
        if (response.status === 403) {
            showToast('❌ Accès refusé : Vous n\'avez pas la permission de modifier ce client', 'error');
            return;
        }
        
        const data = await response.json();
        
        if (data.success) {
            const client = data.client;
            
            document.getElementById('editClientId').value = client.id;
            document.getElementById('editClientNom').value = client.nom;
            document.getElementById('editClientPrenoms').value = client.prenoms || '';
            document.getElementById('editClientContact').value = client.contact;
            document.getElementById('editClientEmail').value = client.email || '';
            document.getElementById('editClientBudget').value = client.budget || 0;

            // ✅ GESTION DU STATUT PERSONNALISÉ LORS DU CHARGEMENT
            const statutsStandards = [
                'Lead', 'Prospect', 'À convertir', 'Perdu',
                'En attente de paiement des frais de profil visa et d\'inscription', 'Profil visa payé',
                'En attente de paiement des frais de cabinet', 'Frais d\'assistance payés',
                'En attente de documents', 'Documents validés', 'Rendez-vous au bureau PSI', 'Rendez-vous d\'urgence',
                'Prise de RDV ambassade confirmée', 'En attente de décision visa', 'Visa accepté', 'Visa refusé', 'Visa validé',
                'Billet d\'avion payé', 'Départ confirmé', 'En suivi post-départ',
                'Négociation', 'Message d\'urgence', 'Opportunité', 'Converti'
            ];

            if (statutsStandards.includes(client.statut)) {
                // Statut standard trouvé
                document.getElementById('editClientStatut').value = client.statut;
            } else {
                // Statut personnalisé
                document.getElementById('editClientStatut').value = 'Autre';
                document.getElementById('editStatutAutre').value = client.statut;
                document.getElementById('editAutreStatutField').style.display = 'block';
            }

            document.getElementById('editClientMedia').value = client.media || 'Facebook';
            document.getElementById('editClientCommentaire').value = client.commentaire || '';
            
            // Extraire catégorie et prestation
            const prestation = client.prestation;
            let categorie = '';
            
            if (prestationsParCategorie['Frais du Cabinet'].includes(prestation)) {
                categorie = 'Frais du Cabinet';
            } else if (prestationsParCategorie['Documents de Voyage'].includes(prestation)) {
                categorie = 'Documents de Voyage';
            } else {
                categorie = 'Autres';
            }
            
            document.getElementById('editClientCategorie').value = categorie;
            updateEditPrestationOptions();
            document.getElementById('editClientPrestation').value = prestation;
            
            document.getElementById('editClientModal').classList.remove('hidden');
        } else {
            showToast('Erreur de chargement', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur de chargement', 'error');
    }
}

function closeEditClientModal() {
    document.getElementById('editClientModal').classList.add('hidden');
    document.getElementById('editClientForm').reset();
    // Masquer le champ "Autre" statut
    document.getElementById('editAutreStatutField').style.display = 'none';
    document.getElementById('editStatutAutre').value = '';
}

function updateEditPrestationOptions() {
    const categorieSelect = document.getElementById('editClientCategorie');
    const prestationSelect = document.getElementById('editClientPrestation');
    
    if (!categorieSelect || !prestationSelect) return;
    
    const categorie = categorieSelect.value;
    prestationSelect.innerHTML = '<option value="">Sélectionner une prestation</option>';
    
    if (categorie && prestationsParCategorie[categorie]) {
        prestationSelect.disabled = false;
        prestationsParCategorie[categorie].forEach(prestation => {
            const option = document.createElement('option');
            option.value = prestation;
            option.textContent = prestation;
            prestationSelect.appendChild(option);
        });
    } else {
        prestationSelect.disabled = true;
    }
}

async function updateClient(event) {
    event.preventDefault();

    const form = document.getElementById('editClientForm');
    const formData = new FormData(form);
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });

    // ✅ GESTION DU CHAMP PERSONNALISÉ POUR STATUT "AUTRE"
    if (data.statut === 'Autre') {
        const statutAutreInput = document.getElementById('editStatutAutre');
        const statutAutreValue = statutAutreInput?.value?.trim();

        if (!statutAutreValue) {
            showToast('❌ Veuillez préciser le statut personnalisé', 'error');
            statutAutreInput?.focus();
            return;
        }

        data.statut = statutAutreValue;
    }

    const clientId = document.getElementById('editClientId').value;

    try {
        const response = await fetch(`${API_BASE}/clients/${clientId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('✅ Client mis à jour avec succès', 'success');
            closeEditClientModal();
            await loadClients();
        } else {
            showToast('❌ ' + (result.error || 'Erreur de mise à jour'), 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('❌ Erreur de connexion', 'error');
    }
}

// ==================== ÉDITION PAIEMENT ====================

async function showEditPaymentModal(paymentId) {
    // Vérifier d'abord les permissions
    if (!userActionPermissions.edit_payments) {
        showToast('❌ Vous n\'avez pas la permission de modifier les paiements', 'error');
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/payments/${paymentId}`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });
        
        if (response.status === 403) {
            showToast('❌ Accès refusé : Vous n\'avez pas la permission de modifier les paiements', 'error');
            return;
        }
        
        const data = await response.json();
        
        if (data.success) {
            const payment = data.payment;
            
            window.currentInvoiceIdForRefresh = payment.invoice.id;
            
            document.getElementById('editPaymentId').value = payment.id;
            document.getElementById('editPaymentInvoiceNumber').value = payment.invoice.number;
            document.getElementById('editPaymentAmount').value = payment.amount;
            document.getElementById('editPaymentMethod').value = payment.payment_method;
            document.getElementById('editPaymentDate').value = payment.payment_date ? payment.payment_date.split(' ')[0] : '';
            document.getElementById('editPaymentNotes').value = payment.notes || '';
            
            document.getElementById('editPaymentModal').classList.remove('hidden');
        } else {
            showToast('Erreur de chargement', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur de chargement', 'error');
    }
}

function closeEditPaymentModal() {
    document.getElementById('editPaymentModal').classList.add('hidden');
    document.getElementById('editPaymentForm').reset();
}

async function updatePayment(event) {
    event.preventDefault();
    
    const paymentId = document.getElementById('editPaymentId').value;
    const data = {
        amount: parseFloat(document.getElementById('editPaymentAmount').value),
        payment_method: document.getElementById('editPaymentMethod').value,
        payment_date: document.getElementById('editPaymentDate').value,
        notes: document.getElementById('editPaymentNotes').value
    };
    
    try {
        const response = await fetch(`${API_BASE}/payments/${paymentId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('✅ Paiement mis à jour avec succès', 'success');
            closeEditPaymentModal();
            await loadInvoices();
            
            // Rafraîchir les détails si un modal est ouvert
            if (!document.getElementById('viewInvoiceModal').classList.contains('hidden')) {
                await viewInvoiceDetails(result.invoice.id);
            }
        } else {
            showToast('❌ ' + (result.error || 'Erreur de mise à jour'), 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('❌ Erreur de connexion', 'error');
    }
}

async function deletePayment(paymentId) {
    // Vérifier d'abord les permissions
    if (!userActionPermissions.delete_payments) {
        showToast('❌ Vous n\'avez pas la permission de supprimer des paiements', 'error');
        return;
    }
    
    if (!confirm('⚠️ ATTENTION : Supprimer ce paiement ?\n\nLe montant sera retiré de la facture.')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/payments/${paymentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        });
        
        if (response.status === 403) {
            showToast('❌ Accès refusé : Seuls les Super Admins peuvent supprimer des paiements', 'error');
            return;
        }
        
        const data = await response.json();
        
        if (data.success) {
            showToast('✅ Paiement supprimé', 'success');
            await loadInvoices();
            
            if (!document.getElementById('viewInvoiceModal').classList.contains('hidden')) {
                await viewInvoiceDetails(data.invoice.id);
            }
        } else {
            showToast('❌ ' + (data.error || 'Erreur'), 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('❌ Erreur de connexion', 'error');
    }
}



// ==================== ÉCOUTER LES CHANGEMENTS DU SELECT CLIENT ====================
// Note: L'écouteur est attaché dynamiquement dans showAddRelanceModal() et quickRelanceClient()
// pour éviter les problèmes de timing avec le DOM

    </script>
</body>
</html>
