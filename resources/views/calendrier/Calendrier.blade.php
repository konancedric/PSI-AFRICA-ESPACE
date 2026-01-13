<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Calendrier de Suivi des Candidats</title>
    <link rel="icon" href="{{ asset('favicon.png') }}"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4A6FA5; /* BLEU DOUX */
            --secondary: #FF9E58; /* ORANGE DOUX */
            --success: #66BB6A; /* VERT DOUX */
            --warning: #EF5350; /* ROUGE DOUX */
            --light: #f8f9fa;
            --dark: #37474F; /* GRIS FONCÉ DOUX */
            --gray: #78909C; /* GRIS MOYEN */
            --light-gray: #ECEFF1; /* GRIS TRÈS CLAIR */
            --weekend: #FFEBEE; /* ROUGE TRÈS CLAIR POUR WEEKEND */
            --holiday: #FFF3E0; /* ORANGE CLAIR POUR JOURS FÉRIÉS */
            --soft-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            --soft-border-radius: 10px;
            --transition: all 0.25s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fb;
            color: var(--dark);
            line-height: 1.6;
            padding: 10px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 15px;
        }
        
        header {
            background: linear-gradient(135deg, var(--primary), #3A5A8A);
            color: white;
            padding: 20px 0;
            border-radius: var(--soft-border-radius);
            margin-bottom: 20px;
            box-shadow: var(--soft-shadow);
            border: none;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 25px;
        }
        
        h1 {
            font-size: 2rem;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .app-description {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .team-indicator {
            background-color: rgba(255, 255, 255, 0.25);
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 500;
            border: 1px solid rgba(255,255,255,0.3);
        }
        
        .main-content {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 20px;
        }
        
        @media (max-width: 1100px) {
            .main-content {
                grid-template-columns: 1fr;
            }
        }
        
        .sidebar {
            background-color: white;
            border-radius: var(--soft-border-radius);
            padding: 20px;
            box-shadow: var(--soft-shadow);
            height: fit-content;
            border: none;
        }
        
        .calendar-section {
            background-color: white;
            border-radius: var(--soft-border-radius);
            padding: 20px;
            box-shadow: var(--soft-shadow);
            border: none;
        }
        
        .section-title {
            font-size: 1.2rem;
            margin-bottom: 15px;
            color: var(--primary);
            border-bottom: 2px solid var(--primary);
            padding-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .section-title i {
            color: var(--primary);
        }
        
        .date-selector {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            gap: 8px;
        }
        
        .date-input {
            flex: 1;
            padding: 10px 12px;
            border: 1px solid var(--light-gray);
            border-radius: 6px;
            font-size: 0.9rem;
            transition: var(--transition);
            background-color: white;
        }
        
        .date-input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(74, 111, 165, 0.2);
        }
        
        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
            border: 1px solid var(--primary);
        }
        
        .btn-primary:hover {
            background-color: #3A5A8A;
            transform: translateY(-1px);
            box-shadow: var(--soft-shadow);
        }
        
        .btn-success {
            background-color: var(--success);
            color: white;
            border: 1px solid var(--success);
        }
        
        .btn-success:hover {
            background-color: #4CAF50;
            transform: translateY(-1px);
            box-shadow: var(--soft-shadow);
        }
        
        .btn-warning {
            background-color: var(--warning);
            color: white;
            border: 1px solid var(--warning);
        }
        
        .btn-warning:hover {
            background-color: #E53935;
            transform: translateY(-1px);
            box-shadow: var(--soft-shadow);
        }
        
        .btn-secondary {
            background-color: var(--secondary);
            color: white;
            border: 1px solid var(--secondary);
        }
        
        .btn-secondary:hover {
            background-color: #FF8A50;
            transform: translateY(-1px);
            box-shadow: var(--soft-shadow);
        }
        
        .btn-light {
            background-color: var(--light-gray);
            color: var(--dark);
            border: 1px solid #CFD8DC;
        }
        
        .btn-light:hover {
            background-color: #E1E5E9;
            transform: translateY(-1px);
        }
        
        .btn-block {
            width: 100%;
            justify-content: center;
        }
        
        .events-list {
            list-style-type: none;
            margin-bottom: 20px;
            max-height: 350px;
            overflow-y: auto;
            border-radius: 6px;
            border: 1px solid var(--light-gray);
        }
        
        .event-item {
            padding: 12px;
            border-left: 4px solid var(--primary);
            background-color: white;
            margin-bottom: 0;
            border-bottom: 1px solid var(--light-gray);
            cursor: pointer;
            transition: var(--transition);
        }
        
        .event-item:hover {
            background-color: #F5F9FF;
        }
        
        .event-item.alarm {
            border-left-color: var(--warning);
            background-color: #FFF5F5;
        }
        
        .event-item.urgent {
            border-left-color: var(--warning);
            background-color: #FFF5F5;
        }
        
        .event-item.success {
            border-left-color: var(--success);
            background-color: #F1F8E9;
        }
        
        .event-item.secondary {
            border-left-color: var(--secondary);
            background-color: #FFF8E1;
        }
        
        .event-time {
            font-weight: bold;
            color: var(--primary);
            font-size: 0.85rem;
        }
        
        .event-title {
            font-weight: 600;
            margin: 3px 0;
            font-size: 0.95rem;
        }
        
        .event-candidate {
            color: var(--gray);
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .event-alarm-indicator {
            color: var(--warning);
            font-size: 0.8rem;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 600;
        }
        
        .event-details {
            background-color: #F5F9FF;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            border-left: 4px solid var(--primary);
            border: 1px solid var(--light-gray);
        }
        
        .event-detail-item {
            margin-bottom: 10px;
            display: flex;
        }
        
        .detail-label {
            font-weight: 600;
            min-width: 100px;
            color: var(--primary);
            font-size: 0.9rem;
        }
        
        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 6px;
            margin-bottom: 20px;
        }
        
        .calendar-header {
            text-align: center;
            font-weight: 600;
            padding: 10px;
            background-color: var(--primary);
            color: white;
            border-radius: 5px;
            border: none;
            font-size: 0.9rem;
        }
        
        .calendar-day {
            background-color: white;
            padding: 8px 6px;
            text-align: center;
            border-radius: 6px;
            cursor: pointer;
            transition: var(--transition);
            min-height: 70px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 1px solid var(--light-gray);
            position: relative;
        }
        
        .calendar-day:hover {
            background-color: #F5F9FF;
            border-color: var(--primary);
        }
        
        .calendar-day.today {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
            font-weight: bold;
        }
        
        .calendar-day.weekend {
            background-color: #FAFAFA;
            color: #999;
        }
        
        .calendar-day.holiday {
            background-color: #FAFAFA;
            color: #999;
        }

        /* Classes anciennes - remplacées par les classes de priorité
        .calendar-day.has-events {
            border-color: var(--success);
            background-color: #F1F8E9;
        }

        .calendar-day.urgent-day {
            border-color: var(--warning);
            background-color: #FFF5F5;
        }

        .calendar-day.many-events {
            border-color: var(--secondary);
            background-color: #FFF8E1;
        }
        */
        
        .day-number {
            font-size: 1rem;
            font-weight: bold;
        }
        
        .day-indicator {
            position: absolute;
            top: 4px;
            right: 4px;
            font-size: 0.65rem;
            padding: 1px 3px;
            border-radius: 2px;
            color: white;
        }
        
        .day-indicator.weekend {
            background-color: var(--gray);
        }
        
        .day-indicator.holiday {
            background-color: var(--secondary);
        }
        
        .event-dots {
            display: flex;
            justify-content: center;
            gap: 2px;
            margin-top: 3px;
        }
        
        .event-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }
        
        .event-dot.primary {
            background-color: var(--primary);
        }
        
        .event-dot.warning {
            background-color: var(--warning);
        }
        
        .event-dot.success {
            background-color: var(--success);
        }
        
        .event-dot.secondary {
            background-color: var(--secondary);
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            padding: 15px;
        }
        
        .modal-content {
            background-color: white;
            padding: 25px;
            border-radius: var(--soft-border-radius);
            width: 100%;
            max-width: 550px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            max-height: 90vh;
            overflow-y: auto;
            border: none;
        }
        
        .modal-title {
            margin-bottom: 15px;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
        }
        
        input, textarea, select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--light-gray);
            border-radius: 6px;
            font-size: 0.9rem;
            transition: var(--transition);
            background-color: white;
        }
        
        input:focus, textarea:focus, select:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(74, 111, 165, 0.2);
        }
        
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .checkbox-group input {
            width: auto;
            transform: scale(1.2);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 20px;
        }
        
        .alarm-active {
            display: none;
            background: linear-gradient(135deg, var(--warning), #E53935);
            color: white;
            padding: 15px;
            border-radius: var(--soft-border-radius);
            margin-top: 15px;
            text-align: center;
            animation: pulse 1.5s infinite;
            box-shadow: var(--soft-shadow);
            border: none;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
        
        .empty-state {
            text-align: center;
            padding: 25px 15px;
            color: var(--gray);
        }
        
        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: var(--light-gray);
        }
        
        .upcoming-events {
            margin-top: 25px;
        }
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: var(--soft-shadow);
            text-align: center;
            border-top: 4px solid var(--primary);
        }
        
        .stat-card.warning {
            border-top-color: var(--warning);
        }
        
        .stat-card.success {
            border-top-color: var(--success);
        }
        
        .stat-card.secondary {
            border-top-color: var(--secondary);
        }
        
        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .stat-card.warning .stat-value {
            color: var(--warning);
        }
        
        .stat-card.success .stat-value {
            color: var(--success);
        }
        
        .stat-card.secondary .stat-value {
            color: var(--secondary);
        }
        
        .stat-label {
            font-size: 0.85rem;
            color: var(--gray);
            margin-top: 5px;
            font-weight: 600;
        }
        
        .calendar-header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .month-navigation {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .current-month {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary);
            min-width: 180px;
            text-align: center;
        }
        
        .filters {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
        }
        
        .filter-btn {
            padding: 6px 12px;
            background-color: white;
            border: 1px solid var(--primary);
            border-radius: 5px;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .filter-btn.active {
            background-color: var(--primary);
            color: white;
        }
        
        .filter-btn:hover {
            transform: translateY(-1px);
        }
        
        .search-section {
            background-color: white;
            border-radius: var(--soft-border-radius);
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: var(--soft-shadow);
            border: none;
        }
        
        .search-form {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 12px;
            align-items: end;
        }
        
        @media (max-width: 900px) {
            .search-form {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        @media (max-width: 600px) {
            .search-form {
                grid-template-columns: 1fr;
            }
        }
        
        .search-results {
            margin-top: 20px;
            max-height: 350px;
            overflow-y: auto;
        }
        
        .team-members {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }
        
        .team-member {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.8rem;
            border: 2px solid white;
            box-shadow: var(--soft-shadow);
        }
        
        .priority-high {
            color: var(--warning);
            font-weight: 700;
            background-color: #FFF5F5;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.8rem;
        }
        
        .priority-medium {
            color: var(--secondary);
            font-weight: 700;
            background-color: #FFF8E1;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.8rem;
        }
        
        .priority-low {
            color: var(--success);
            font-weight: 700;
            background-color: #F1F8E9;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.8rem;
        }
        
        .legend {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 12px;
            flex-wrap: wrap;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.85rem;
        }
        
        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 2px;
        }
        
        .legend-color.primary {
            background-color: var(--primary);
        }
        
        .legend-color.warning {
            background-color: var(--warning);
        }
        
        .legend-color.success {
            background-color: var(--success);
        }
        
        .legend-color.secondary {
            background-color: var(--secondary);
        }
        
        .legend-color.weekend {
            background-color: #FAFAFA;
            border: 1px solid #DDD;
        }
        
        .legend-color.holiday {
            background-color: #FAFAFA;
            border: 1px solid #DDD;
        }
        
        .agent-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background-color: var(--primary);
            color: white;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .alarm-controls {
            background-color: #FFF8E1;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
            border: 1px solid #FFE082;
        }
        
        .alarm-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .alarm-details {
            margin-bottom: 10px;
        }

        /* Classes de priorité pour les jours du calendrier */
        .calendar-day.priority-low {
            border-color: #66BB6A !important;
            background-color: #E8F5E9 !important;
        }

        .calendar-day.priority-medium {
            border-color: #FF9E58 !important;
            background-color: #FFF3E0 !important;
        }

        .calendar-day.priority-high {
            border-color: #EF5350 !important;
            background-color: #FFEBEE !important;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-value" id="todayInterviews">0</div>
                <div class="stat-label">Entretiens aujourd'hui</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-value" id="weekInterviews">0</div>
                <div class="stat-label">Entretiens cette semaine</div>
            </div>
            <div class="stat-card success">
                <div class="stat-value" id="pendingDecisions">0</div>
                <div class="stat-label">Décisions en attente</div>
            </div>
            <div class="stat-card secondary">
                <div class="stat-value" id="agentEvents">0</div>
                <div class="stat-label">Vos entretiens</div>
            </div>
        </div>
        
        <div class="search-section">
            <h2 class="section-title"><i class="fas fa-search"></i> Recherche avancée</h2>
            <div class="search-form">
                <div class="form-group">
                    <label for="searchKeyword">Mot-clé</label>
                    <input type="text" id="searchKeyword" placeholder="Nom, poste, description...">
                </div>
                <div class="form-group">
                    <label for="searchType">Type d'entretien</label>
                    <select id="searchType">
                        <option value="">Tous les types</option>
                        <option value="phone">Téléphonique</option>
                        <option value="video">Visioconférence</option>
                        <option value="in-person">En présentiel</option>
                        <option value="technical">Technique</option>
                        <option value="hr">RH</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="searchAgent">Agent</label>
                    <select id="searchAgent">
                        <option value="">Tous les agents</option>
                        @foreach($agents ?? [] as $agent)
                            <option value="{{ $agent['name'] }}">{{ $agent['name'] }}@if($agent['matricule']) ({{ $agent['matricule'] }})@endif</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <button id="searchBtn" class="btn btn-primary">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                </div>
            </div>
            
            <div id="searchResults" class="search-results" style="display: none;">
                <!-- Les résultats de recherche seront affichés ici -->
            </div>
        </div>
        
        <div class="main-content">
            <div class="sidebar">
                <h2 class="section-title"><i class="fas fa-tasks"></i> Gestion des Entretiens</h2>
                
                <div class="date-selector">
                    <input type="date" id="datePicker" class="date-input">
                    <button id="todayBtn" class="btn btn-secondary">
                        <i class="fas fa-calendar-day"></i> Aujourd'hui
                    </button>
                </div>
                
                <button id="addEventBtn" class="btn btn-success btn-block">
                    <i class="fas fa-plus"></i> Nouvel Entretien
                </button>
                
                <div class="filters">
                    <button class="filter-btn active" data-filter="all">Tous</button>
                    <button class="filter-btn" data-filter="today">Aujourd'hui</button>
                    <button class="filter-btn" data-filter="week">Cette semaine</button>
                    <button class="filter-btn" data-filter="my">Mes entretiens</button>
                </div>
                
                <h3 class="section-title"><i class="fas fa-list"></i> Entretiens du Jour</h3>
                <ul id="eventsList" class="events-list">
                    <!-- Les événements seront ajoutés ici dynamiquement -->
                </ul>
                
                <div id="eventDetails" class="event-details" style="display: none;">
                    <!-- Les détails de l'événement sélectionné seront affichés ici -->
                </div>
                
                <div class="event-actions" style="display: none;" id="eventActions">
                    <button id="editEventBtn" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Modifier
                    </button>
                    <button id="deleteEventBtn" class="btn btn-light">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </div>
                
                <div id="alarmActive" class="alarm-active">
                    <!-- Alarme active -->
                </div>
            </div>
            
            <div class="calendar-section">
                <div class="calendar-header-bar">
                    <div class="month-navigation">
                        <button id="prevMonth" class="btn btn-light">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <div class="current-month" id="currentMonth">Mai 2023</div>
                        <button id="nextMonth" class="btn btn-light">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    
                    <div>
                        <button id="viewToggle" class="btn btn-primary">
                            <i class="fas fa-sync-alt"></i> Actualiser
                        </button>
                    </div>
                </div>
                
                <div id="calendar" class="calendar">
                    <!-- Le calendrier sera généré ici dynamiquement -->
                </div>
                
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color primary"></div>
                        <span>Entretien normal</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color warning"></div>
                        <span>Urgent/Priorité haute</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color success"></div>
                        <span>Confirmé/Validé</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color secondary"></div>
                        <span>Plusieurs entretiens</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color weekend"></div>
                        <span>Week-end</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color holiday"></div>
                        <span>Jour férié</span>
                    </div>
                </div>
                
                <div class="upcoming-events">
                    <h3 class="section-title"><i class="fas fa-clock"></i> Entretiens à Venir (7 jours)</h3>
                    <ul id="upcomingEvents" class="events-list">
                        <!-- Les événements à venir seront ajoutés ici dynamiquement -->
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal pour ajouter/modifier un événement -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle" class="modal-title"><i class="fas fa-calendar-plus"></i> Nouvel Entretien</h2>
            <form id="eventForm">
                <div class="form-group">
                    <label for="eventTitle">Prise de Rendez-vous*</label>
                    <input type="text" id="eventTitle" required placeholder="Ex: Développeur Frontend">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="candidateName">Nom du candidat *</label>
                        <input type="text" id="candidateName" required placeholder="Ex: Jean Dupont">
                    </div>
                    
                    <div class="form-group">
                        <label for="candidateContact">Contact</label>
                        <input type="text" id="candidateContact" placeholder="Email ou téléphone">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="eventDate">Date *</label>
                        <input type="date" id="eventDate" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="eventTime">Heure *</label>
                        <input type="time" id="eventTime" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="eventType">Type d'entretien</label>
                        <select id="eventType">
                            <option value="phone">Téléphonique</option>
                            <option value="video">Visioconférence</option>
                            <option value="in-person">En présentiel</option>
                            <option value="technical">Technique</option>
                            <option value="hr">RH</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="eventPriority">Priorité</label>
                        <select id="eventPriority">
                            <option value="low">Faible</option>
                            <option value="medium">Moyenne</option>
                            <option value="high">Élevée</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="eventAgent">Agent responsable</label>
                    <input type="text" id="eventAgent" value="{{ Auth::user()->name ?? '' }}" readonly required style="background-color: #f5f5f5; cursor: not-allowed;">
                    <small style="color: #666; font-size: 0.85em; display: block; margin-top: 0.25rem;">
                        <i class="fas fa-info-circle"></i> Vous êtes automatiquement assigné comme agent responsable
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="eventDescription">Notes / Description</label>
                    <textarea id="eventDescription" placeholder="Notes sur le candidat, détails de l'entretien..."></textarea>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="eventAlarm">
                        <label for="eventAlarm">Activer l'alarme</label>
                    </div>
                </div>
                
                <div id="alarmSettings">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="alarmDate">Date de l'alarme</label>
                            <input type="date" id="alarmDate">
                        </div>
                        
                        <div class="form-group">
                            <label for="alarmTime">Heure de l'alarme</label>
                            <input type="time" id="alarmTime">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="alarmFrequency">Fréquence de répétition</label>
                        <select id="alarmFrequency">
                            <option value="once">Une fois</option>
                            <option value="daily">Quotidienne</option>
                            <option value="weekly">Hebdomadaire</option>
                            <option value="monthly">Mensuelle</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" id="cancelBtn" class="btn btn-light">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Configuration API
        const API_BASE_URL = '/crm/calendrier';
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

        // Variables globales
        let events = [];
        let selectedDate = new Date();
        let selectedEventId = null;
        let alarmCheckInterval = null;
        let currentFilter = 'all';
        let currentAgent = '{{ Auth::user()->name ?? "agent1" }}'; // Agent connecté

        // Fonction pour charger les événements depuis l'API
        async function loadEventsFromAPI() {
            try {
                const response = await fetch(`${API_BASE_URL}/events`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    }
                });

                const data = await response.json();
                if (data.success) {
                    events = data.events;
                    return events;
                } else {
                    console.error('Erreur lors du chargement des événements:', data.message);
                    return [];
                }
            } catch (error) {
                console.error('Erreur réseau:', error);
                return [];
            }
        }

        // Fonction pour sauvegarder un événement via API
        async function saveEventToAPI(eventData) {
            try {
                const url = eventData.id && !eventData.id.toString().startsWith('event_')
                    ? `${API_BASE_URL}/events/${eventData.id}`
                    : `${API_BASE_URL}/events`;

                const method = eventData.id && !eventData.id.toString().startsWith('event_') ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify(eventData)
                });

                const data = await response.json();
                if (data.success) {
                    return data.event;
                } else {
                    console.error('Erreur lors de la sauvegarde:', data.message);
                    alert('Erreur lors de la sauvegarde: ' + (data.message || 'Erreur inconnue'));
                    return null;
                }
            } catch (error) {
                console.error('Erreur réseau:', error);
                alert('Erreur réseau lors de la sauvegarde');
                return null;
            }
        }

        // Fonction pour supprimer un événement via API
        async function deleteEventFromAPI(eventId) {
            try {
                const response = await fetch(`${API_BASE_URL}/events/${eventId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    }
                });

                const data = await response.json();
                return data.success;
            } catch (error) {
                console.error('Erreur réseau:', error);
                return false;
            }
        }

        // Jours fériés en France pour 2023
        const holidays = [
            '2023-01-01', // Nouvel An
            '2023-04-10', // Lundi de Pâques
            '2023-05-01', // Fête du Travail
            '2023-05-08', // Victoire 1945
            '2023-05-18', // Ascension
            '2023-05-29', // Lundi de Pentecôte
            '2023-07-14', // Fête Nationale
            '2023-08-15', // Assomption
            '2023-11-01', // Toussaint
            '2023-11-11', // Armistice 1918
            '2023-12-25'  // Noël
        ];

        // Agents du système (récupérés depuis le contrôleur)
        const systemAgents = @json($agents ?? []);

        // Convertir en objet pour compatibilité avec le code existant
        const agents = {};
        systemAgents.forEach(agent => {
            agents[agent.name] = agent.name + (agent.matricule ? ' (' + agent.matricule + ')' : '');
        });

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            initializeApp();
        });

        async function initializeApp() {
            // Charger les événements depuis l'API
            await loadEventsFromAPI();

            // Initialiser les écouteurs d'événements
            document.getElementById('datePicker').value = formatDateForInput(selectedDate);
            document.getElementById('eventDate').value = formatDateForInput(selectedDate);
            document.getElementById('alarmDate').value = formatDateForInput(selectedDate);

            document.getElementById('addEventBtn').addEventListener('click', openAddEventModal);
            document.getElementById('todayBtn').addEventListener('click', setToday);
            document.getElementById('datePicker').addEventListener('change', handleDateChange);
            document.getElementById('eventForm').addEventListener('submit', handleEventFormSubmit);
            document.getElementById('cancelBtn').addEventListener('click', closeEventModal);
            document.getElementById('editEventBtn').addEventListener('click', openEditEventModal);
            document.getElementById('deleteEventBtn').addEventListener('click', deleteEvent);
            document.getElementById('prevMonth').addEventListener('click', previousMonth);
            document.getElementById('nextMonth').addEventListener('click', nextMonth);
            document.getElementById('viewToggle').addEventListener('click', refreshView);
            document.getElementById('searchBtn').addEventListener('click', performSearch);
            document.getElementById('eventAlarm').addEventListener('change', toggleAlarmSettings);

            // Filtres
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentFilter = this.getAttribute('data-filter');
                    loadEvents();
                    loadUpcomingEvents();
                });
            });

            // Générer le calendrier et charger les événements
            generateCalendar();
            loadEvents();
            loadUpcomingEvents();
            updateStats();
            
            // Démarrer la vérification des alarmes
            startAlarmChecker();
        }

        // Fonction pour afficher/masquer les paramètres d'alarme
        function toggleAlarmSettings() {
            const alarmSettings = document.getElementById('alarmSettings');
            if (document.getElementById('eventAlarm').checked) {
                alarmSettings.style.display = 'block';
            } else {
                alarmSettings.style.display = 'none';
            }
        }

        // Fonctions pour gérer les dates
        function formatDateForInput(date) {
            return date.toISOString().split('T')[0];
        }

        function formatDateForDisplay(date) {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return date.toLocaleDateString('fr-FR', options);
        }

        function setToday() {
            selectedDate = new Date();
            document.getElementById('datePicker').value = formatDateForInput(selectedDate);
            generateCalendar();
            loadEvents();
            updateStats();
        }

        function handleDateChange() {
            selectedDate = new Date(document.getElementById('datePicker').value);
            generateCalendar();
            loadEvents();
        }

        function previousMonth() {
            selectedDate.setMonth(selectedDate.getMonth() - 1);
            generateCalendar();
        }

        function nextMonth() {
            selectedDate.setMonth(selectedDate.getMonth() + 1);
            generateCalendar();
        }

        function refreshView() {
            generateCalendar();
            loadEvents();
            loadUpcomingEvents();
            updateStats();
        }

        // Fonctions pour générer le calendrier (début lundi, fin dimanche)
        function generateCalendar() {
            const calendarElement = document.getElementById('calendar');
            calendarElement.innerHTML = '';
            
            // Mettre à jour l'affichage du mois
            const monthYear = selectedDate.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
            document.getElementById('currentMonth').textContent = monthYear.charAt(0).toUpperCase() + monthYear.slice(1);
            
            // En-têtes des jours de la semaine (lundi à dimanche)
            const daysOfWeek = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
            daysOfWeek.forEach(day => {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-header';
                dayElement.textContent = day;
                calendarElement.appendChild(dayElement);
            });
            
            // Premier jour du mois
            const firstDay = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1);
            // Dernier jour du mois
            const lastDay = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + 1, 0);
            
            // Ajuster pour commencer le lundi (1 au lieu de 0 pour dimanche)
            let startingDayOfWeek = firstDay.getDay();
            startingDayOfWeek = startingDayOfWeek === 0 ? 6 : startingDayOfWeek - 1;
            
            // Date du jour pour comparaison
            const today = new Date();
            const isToday = (date) => {
                return date.getDate() === today.getDate() && 
                       date.getMonth() === today.getMonth() && 
                       date.getFullYear() === today.getFullYear();
            };
            
            // Remplir les jours du mois précédent
            const prevMonthLastDay = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 0).getDate();
            for (let i = startingDayOfWeek - 1; i >= 0; i--) {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day';
                dayElement.textContent = prevMonthLastDay - i;
                dayElement.style.opacity = '0.5';
                dayElement.style.backgroundColor = '#FAFAFA';
                calendarElement.appendChild(dayElement);
            }
            
            // Remplir les jours du mois actuel
            for (let i = 1; i <= lastDay.getDate(); i++) {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day';
                
                const dayNumber = document.createElement('div');
                dayNumber.className = 'day-number';
                dayNumber.textContent = i;
                dayElement.appendChild(dayNumber);
                
                // Vérifier si c'est un week-end
                const currentDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), i);
                const dayOfWeek = currentDate.getDay();
                if (dayOfWeek === 0 || dayOfWeek === 6) {
                    dayElement.classList.add('weekend');
                    dayElement.style.backgroundColor = '#FAFAFA';
                    dayElement.style.color = '#999';
                    
                    const indicator = document.createElement('div');
                    indicator.className = 'day-indicator weekend';
                    indicator.textContent = 'WE';
                    dayElement.appendChild(indicator);
                }
                
                // Vérifier si c'est un jour férié
                const dateString = formatDateForInput(currentDate);
                if (holidays.includes(dateString)) {
                    dayElement.classList.add('holiday');
                    dayElement.style.backgroundColor = '#FAFAFA';
                    dayElement.style.color = '#999';
                    
                    const indicator = document.createElement('div');
                    indicator.className = 'day-indicator holiday';
                    indicator.textContent = 'FÉRIÉ';
                    dayElement.appendChild(indicator);
                }
                
                // Vérifier si ce jour a des événements
                const dayEvents = events.filter(event => event.date === dateString);

                if (dayEvents.length > 0) {
                    // Déterminer la priorité la plus élevée du jour
                    let highestPriority = 'low';
                    dayEvents.forEach(event => {
                        if (event.priority === 'high') {
                            highestPriority = 'high';
                        } else if (event.priority === 'medium' && highestPriority !== 'high') {
                            highestPriority = 'medium';
                        }
                    });

                    // Appliquer la classe de priorité correspondante
                    dayElement.classList.add('priority-' + highestPriority);

                    const eventDots = document.createElement('div');
                    eventDots.className = 'event-dots';

                    // Ajouter des points colorés pour représenter les événements
                    dayEvents.forEach(event => {
                        const dot = document.createElement('div');
                        dot.className = 'event-dot';

                        if (event.priority === 'high') {
                            dot.classList.add('warning');
                        } else if (event.status === 'confirmed') {
                            dot.classList.add('success');
                        } else if (dayEvents.length > 2) {
                            dot.classList.add('secondary');
                        } else {
                            dot.classList.add('primary');
                        }

                        eventDots.appendChild(dot);
                    });

                    dayElement.appendChild(eventDots);
                }
                
                if (isToday(currentDate)) {
                    dayElement.classList.add('today');
                }
                
                // Ajouter un écouteur d'événements pour sélectionner la date
                dayElement.addEventListener('click', () => {
                    selectedDate = currentDate;
                    document.getElementById('datePicker').value = formatDateForInput(selectedDate);
                    loadEvents();
                    generateCalendar(); // Régénérer pour mettre à jour la sélection
                });
                
                calendarElement.appendChild(dayElement);
            }
            
            // Remplir les jours du mois suivant
            const totalCells = 42; // 6 semaines * 7 jours
            const daysInCalendar = startingDayOfWeek + lastDay.getDate();
            const nextMonthDays = totalCells - daysInCalendar;
            
            for (let i = 1; i <= nextMonthDays; i++) {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day';
                dayElement.textContent = i;
                dayElement.style.opacity = '0.5';
                dayElement.style.backgroundColor = '#FAFAFA';
                calendarElement.appendChild(dayElement);
            }
        }

        // Fonctions pour gérer les événements
        function loadEvents() {
            const eventsList = document.getElementById('eventsList');
            eventsList.innerHTML = '';
            
            let filteredEvents = [];
            const today = new Date();
            const dateString = formatDateForInput(selectedDate);
            
            if (currentFilter === 'all') {
                filteredEvents = events.filter(event => event.date === dateString);
            } else if (currentFilter === 'today') {
                const todayString = formatDateForInput(today);
                filteredEvents = events.filter(event => event.date === todayString);
            } else if (currentFilter === 'week') {
                const nextWeek = new Date(today);
                nextWeek.setDate(today.getDate() + 7);
                filteredEvents = events.filter(event => {
                    const eventDate = new Date(event.date);
                    return eventDate >= today && eventDate <= nextWeek;
                });
            } else if (currentFilter === 'my') {
                filteredEvents = events.filter(event => event.agent === currentAgent && event.date === dateString);
            }
            
            if (filteredEvents.length === 0) {
                eventsList.innerHTML = `
                    <div class="empty-state">
                        <i class="far fa-calendar-times"></i>
                        <p>Aucun entretien pour cette période</p>
                    </div>
                `;
                document.getElementById('eventDetails').style.display = 'none';
                document.getElementById('eventActions').style.display = 'none';
                return;
            }
            
            // Trier les événements par heure
            filteredEvents.sort((a, b) => a.time.localeCompare(b.time));
            
            filteredEvents.forEach(event => {
                const eventItem = document.createElement('li');
                eventItem.className = 'event-item';
                
                // Appliquer la classe en fonction de la priorité
                if (event.priority === 'high') {
                    eventItem.classList.add('urgent');
                } else if (event.status === 'confirmed') {
                    eventItem.classList.add('success');
                } else if (event.priority === 'medium') {
                    eventItem.classList.add('secondary');
                }
                
                if (event.alarm) eventItem.classList.add('alarm');
                eventItem.dataset.id = event.id;
                
                eventItem.innerHTML = `
                    <div class="event-time">${event.time}</div>
                    <div class="event-title">${event.title}</div>
                    <div class="event-candidate">
                        <i class="fas fa-user"></i> ${event.candidateName}
                    </div>
                    <div class="event-candidate">
                        <i class="fas fa-user-tie"></i> ${agents[event.agent]}
                    </div>
                    ${event.alarm ? '<div class="event-alarm-indicator"><i class="fas fa-bell"></i> Alarme activée</div>' : ''}
                    ${event.priority === 'high' ? '<div class="event-alarm-indicator"><i class="fas fa-exclamation-circle"></i> Priorité élevée</div>' : ''}
                `;
                
                eventItem.addEventListener('click', () => selectEvent(event.id));
                eventsList.appendChild(eventItem);
            });
            
            // Sélectionner le premier événement par défaut
            if (filteredEvents.length > 0) {
                selectEvent(filteredEvents[0].id);
            }
        }

        function loadUpcomingEvents() {
            const upcomingList = document.getElementById('upcomingEvents');
            upcomingList.innerHTML = '';
            
            const today = new Date();
            const nextWeek = new Date(today);
            nextWeek.setDate(today.getDate() + 7);
            
            const upcomingEvents = events.filter(event => {
                const eventDate = new Date(event.date);
                return eventDate >= today && eventDate <= nextWeek;
            });
            
            if (upcomingEvents.length === 0) {
                upcomingList.innerHTML = `
                    <div class="empty-state">
                        <i class="far fa-calendar-plus"></i>
                        <p>Aucun entretien à venir cette semaine</p>
                    </div>
                `;
                return;
            }
            
            // Trier par date et heure
            upcomingEvents.sort((a, b) => {
                if (a.date === b.date) {
                    return a.time.localeCompare(b.time);
                }
                return new Date(a.date) - new Date(b.date);
            });
            
            upcomingEvents.forEach(event => {
                const eventItem = document.createElement('li');
                eventItem.className = 'event-item';
                
                // Appliquer la classe en fonction de la priorité
                if (event.priority === 'high') {
                    eventItem.classList.add('urgent');
                } else if (event.status === 'confirmed') {
                    eventItem.classList.add('success');
                } else if (event.priority === 'medium') {
                    eventItem.classList.add('secondary');
                }
                
                if (event.alarm) eventItem.classList.add('alarm');
                
                const eventDate = new Date(event.date);
                const dateString = formatDateForDisplay(eventDate);
                
                eventItem.innerHTML = `
                    <div class="event-time">${eventDate.toLocaleDateString('fr-FR', { weekday: 'short', day: 'numeric', month: 'short' })} à ${event.time}</div>
                    <div class="event-title">${event.title}</div>
                    <div class="event-candidate">
                        <i class="fas fa-user"></i> ${event.candidateName}
                        <span class="agent-badge"><i class="fas fa-user-tie"></i> ${agents[event.agent]}</span>
                    </div>
                    <div class="event-candidate">
                        ${event.priority === 'high' ? '<span class="priority-high"><i class="fas fa-exclamation-circle"></i> Urgent</span>' : ''}
                        ${event.priority === 'medium' ? '<span class="priority-medium"><i class="fas fa-info-circle"></i> Moyen</span>' : ''}
                        ${event.priority === 'low' ? '<span class="priority-low"><i class="fas fa-check-circle"></i> Faible</span>' : ''}
                    </div>
                `;
                
                upcomingList.appendChild(eventItem);
            });
        }

        function selectEvent(eventId) {
            selectedEventId = eventId;
            const event = events.find(e => e.id === eventId);
            
            if (!event) return;
            
            const eventDetails = document.getElementById('eventDetails');
            eventDetails.style.display = 'block';
            
            const priorityText = event.priority === 'high' ? 
                '<span class="priority-high">Élevée</span>' : 
                event.priority === 'medium' ? 
                '<span class="priority-medium">Moyenne</span>' : 
                '<span class="priority-low">Faible</span>';
            
            eventDetails.innerHTML = `
                <h3>Détails de l'entretien</h3>
                <div class="event-detail-item">
                    <span class="detail-label">Poste:</span>
                    <span>${event.title}</span>
                </div>
                <div class="event-detail-item">
                    <span class="detail-label">Candidat:</span>
                    <span>${event.candidateName}</span>
                </div>
                <div class="event-detail-item">
                    <span class="detail-label">Contact:</span>
                    <span>${event.candidateContact || 'Non spécifié'}</span>
                </div>
                <div class="event-detail-item">
                    <span class="detail-label">Agent:</span>
                    <span>${agents[event.agent]}</span>
                </div>
                <div class="event-detail-item">
                    <span class="detail-label">Date:</span>
                    <span>${formatDateForDisplay(new Date(event.date))}</span>
                </div>
                <div class="event-detail-item">
                    <span class="detail-label">Heure:</span>
                    <span>${event.time}</span>
                </div>
                <div class="event-detail-item">
                    <span class="detail-label">Type:</span>
                    <span>${getEventTypeText(event.eventType)}</span>
                </div>
                <div class="event-detail-item">
                    <span class="detail-label">Priorité:</span>
                    <span>${priorityText}</span>
                </div>
                <div class="event-detail-item">
                    <span class="detail-label">Alarme:</span>
                    <span>${event.alarm ? 'Activée' : 'Désactivée'}</span>
                </div>
                <div class="event-detail-item">
                    <span class="detail-label">Notes:</span>
                    <span>${event.description || 'Aucune note'}</span>
                </div>
            `;
            
            document.getElementById('eventActions').style.display = 'block';
        }

        function getEventTypeText(type) {
            const types = {
                'phone': 'Téléphonique',
                'video': 'Visioconférence',
                'in-person': 'En présentiel',
                'technical': 'Technique',
                'hr': 'RH'
            };
            return types[type] || type;
        }

        // Fonctions pour la modale d'événement
        function openAddEventModal() {
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-calendar-plus"></i> Nouvel Entretien';
            document.getElementById('eventForm').reset();
            document.getElementById('eventDate').value = formatDateForInput(selectedDate);
            document.getElementById('alarmDate').value = formatDateForInput(selectedDate);

            // Définir l'agent connecté comme agent responsable (champ en lecture seule)
            document.getElementById('eventAgent').value = currentAgent;

            document.getElementById('eventModal').style.display = 'flex';
            document.getElementById('alarmSettings').style.display = 'none';
            selectedEventId = null;
        }

        function openEditEventModal() {
            if (!selectedEventId) return;
            
            const event = events.find(e => e.id === selectedEventId);
            if (!event) return;
            
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i> Modifier l\'Entretien';
            document.getElementById('eventTitle').value = event.title;
            document.getElementById('candidateName').value = event.candidateName;
            document.getElementById('candidateContact').value = event.candidateContact || '';
            document.getElementById('eventDate').value = event.date;
            document.getElementById('eventTime').value = event.time;
            document.getElementById('eventType').value = event.eventType || 'phone';
            document.getElementById('eventPriority').value = event.priority || 'medium';
            document.getElementById('eventAgent').value = event.agent || currentAgent;
            document.getElementById('eventDescription').value = event.description || '';
            document.getElementById('eventAlarm').checked = event.alarm;
            
            // Remplir les champs d'alarme si disponibles
            if (event.alarmDate) {
                document.getElementById('alarmDate').value = event.alarmDate;
            }
            if (event.alarmTime) {
                document.getElementById('alarmTime').value = event.alarmTime;
            }
            if (event.alarmFrequency) {
                document.getElementById('alarmFrequency').value = event.alarmFrequency;
            }
            
            document.getElementById('eventModal').style.display = 'flex';
            
            // Afficher/masquer les paramètres d'alarme
            toggleAlarmSettings();
        }

        function closeEventModal() {
            document.getElementById('eventModal').style.display = 'none';
        }

        async function handleEventFormSubmit(e) {
            e.preventDefault();

            const eventData = {
                id: selectedEventId,
                title: document.getElementById('eventTitle').value,
                candidateName: document.getElementById('candidateName').value,
                candidateContact: document.getElementById('candidateContact').value,
                date: document.getElementById('eventDate').value,
                time: document.getElementById('eventTime').value,
                eventType: document.getElementById('eventType').value,
                priority: document.getElementById('eventPriority').value,
                agent: document.getElementById('eventAgent').value,
                description: document.getElementById('eventDescription').value,
                alarm: document.getElementById('eventAlarm').checked
            };

            // Ajouter les données d'alarme si activées
            if (eventData.alarm) {
                eventData.alarmDate = document.getElementById('alarmDate').value;
                eventData.alarmTime = document.getElementById('alarmTime').value;
                eventData.alarmFrequency = document.getElementById('alarmFrequency').value;
            }

            // Sauvegarder via API
            const savedEvent = await saveEventToAPI(eventData);

            if (savedEvent) {
                // Mettre à jour le tableau local
                if (selectedEventId) {
                    const index = events.findIndex(e => e.id === selectedEventId);
                    if (index !== -1) {
                        events[index] = savedEvent;
                    }
                } else {
                    events.push(savedEvent);
                }

                // Mettre à jour l'affichage
                loadEvents();
                loadUpcomingEvents();
                generateCalendar();
                updateStats();

                // Fermer la modale
                closeEventModal();
            }
        }

        async function deleteEvent() {
            if (!selectedEventId) return;

            if (confirm('Êtes-vous sûr de vouloir supprimer cet entretien ?')) {
                const success = await deleteEventFromAPI(selectedEventId);

                if (success) {
                    // Retirer de la liste locale
                    events = events.filter(e => e.id !== selectedEventId);

                    // Mettre à jour l'affichage
                    loadEvents();
                    loadUpcomingEvents();
                    generateCalendar();
                    updateStats();

                    document.getElementById('eventDetails').style.display = 'none';
                    document.getElementById('eventActions').style.display = 'none';
                } else {
                    alert('Erreur lors de la suppression de l\'événement');
                }
            }
        }

        // Fonctions pour les statistiques
        function updateStats() {
            const today = new Date();
            const todayString = formatDateForInput(today);
            
            // Entretiens aujourd'hui
            const todayInterviews = events.filter(event => event.date === todayString).length;
            document.getElementById('todayInterviews').textContent = todayInterviews;
            
            // Entretiens cette semaine
            const nextWeek = new Date(today);
            nextWeek.setDate(today.getDate() + 7);
            const weekInterviews = events.filter(event => {
                const eventDate = new Date(event.date);
                return eventDate >= today && eventDate <= nextWeek;
            }).length;
            document.getElementById('weekInterviews').textContent = weekInterviews;
            
            // Décisions en attente (événements passés sans statut de décision)
            const pendingDecisions = events.filter(event => {
                const eventDate = new Date(event.date);
                return eventDate < today && !event.decision;
            }).length;
            document.getElementById('pendingDecisions').textContent = pendingDecisions;
            
            // Entretiens de l'agent connecté
            const agentEvents = events.filter(event => event.agent === currentAgent).length;
            document.getElementById('agentEvents').textContent = agentEvents;
        }

        // Fonctions pour la recherche
        function performSearch() {
            const keyword = document.getElementById('searchKeyword').value.toLowerCase();
            const type = document.getElementById('searchType').value;
            const agent = document.getElementById('searchAgent').value;
            
            let results = events;
            
            // Filtrer par mot-clé
            if (keyword) {
                results = results.filter(event => 
                    event.title.toLowerCase().includes(keyword) ||
                    event.candidateName.toLowerCase().includes(keyword) ||
                    (event.description && event.description.toLowerCase().includes(keyword)) ||
                    event.eventType.toLowerCase().includes(keyword)
                );
            }
            
            // Filtrer par type
            if (type) {
                results = results.filter(event => event.eventType === type);
            }
            
            // Filtrer par agent
            if (agent) {
                results = results.filter(event => event.agent === agent);
            }
            
            displaySearchResults(results);
        }

        function displaySearchResults(results) {
            const resultsContainer = document.getElementById('searchResults');
            resultsContainer.innerHTML = '';
            resultsContainer.style.display = 'block';
            
            if (results.length === 0) {
                resultsContainer.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <p>Aucun résultat trouvé</p>
                    </div>
                `;
                return;
            }
            
            const resultsList = document.createElement('ul');
            resultsList.className = 'events-list';
            
            results.forEach(event => {
                const eventItem = document.createElement('li');
                eventItem.className = 'event-item';
                
                // Appliquer la classe en fonction de la priorité
                if (event.priority === 'high') {
                    eventItem.classList.add('urgent');
                } else if (event.status === 'confirmed') {
                    eventItem.classList.add('success');
                } else if (event.priority === 'medium') {
                    eventItem.classList.add('secondary');
                }
                
                const eventDate = new Date(event.date);
                const dateString = formatDateForDisplay(eventDate);
                
                eventItem.innerHTML = `
                    <div class="event-time">${dateString} à ${event.time}</div>
                    <div class="event-title">${event.title}</div>
                    <div class="event-candidate">
                        <i class="fas fa-user"></i> ${event.candidateName}
                        <span class="agent-badge"><i class="fas fa-user-tie"></i> ${agents[event.agent]}</span>
                    </div>
                    <div class="event-candidate">
                        <i class="fas fa-calendar-alt"></i> ${getEventTypeText(event.eventType)}
                        ${event.priority === 'high' ? '<span class="priority-high"><i class="fas fa-exclamation-circle"></i> Urgent</span>' : ''}
                    </div>
                `;
                
                eventItem.addEventListener('click', () => {
                    selectedDate = new Date(event.date);
                    document.getElementById('datePicker').value = formatDateForInput(selectedDate);
                    loadEvents();
                    generateCalendar();
                    selectEvent(event.id);
                    resultsContainer.style.display = 'none';
                });
                
                resultsList.appendChild(eventItem);
            });
            
            resultsContainer.appendChild(resultsList);
        }

        // Fonctions pour les alarmes
        function startAlarmChecker() {
            alarmCheckInterval = setInterval(checkAlarms, 30000); // Vérifier toutes les 30 secondes
        }

        function checkAlarms() {
            const now = new Date();
            const currentTime = now.toTimeString().substring(0, 5);
            const currentDate = formatDateForInput(now);
            
            events.forEach(event => {
                if (!event.alarm) return;
                
                // Vérifier si l'événement est aujourd'hui
                if (event.alarmDate !== currentDate) return;
                
                // Vérifier si l'alarme doit sonner maintenant
                if (event.alarmTime === currentTime) {
                    triggerAlarm(event);
                }
            });
        }

        function triggerAlarm(event) {
            const alarmElement = document.getElementById('alarmActive');
            alarmElement.innerHTML = `
                <h3><i class="fas fa-bell"></i> ALARME - ENTRETIEN</h3>
                <div class="alarm-details">
                    <p><strong>${event.title}</strong> avec ${event.candidateName}</p>
                    <p>${event.description || ''}</p>
                    <p>À ${event.time} - ${getEventTypeText(event.eventType)}</p>
                    <p>Agent: ${agents[event.agent]}</p>
                </div>
                <div class="alarm-actions">
                    <button onclick="closeAlarm()" class="btn btn-light">
                        <i class="fas fa-check"></i> Clôturer
                    </button>
                    <button onclick="rescheduleEvent('${event.id}')" class="btn btn-secondary">
                        <i class="fas fa-calendar-plus"></i> Reprogrammer
                    </button>
                </div>
            `;
            alarmElement.style.display = 'block';
            
            // Jouer un son d'alarme
            playAlarmSound();
        }

        function closeAlarm() {
            document.getElementById('alarmActive').style.display = 'none';
        }

        function rescheduleEvent(eventId) {
            closeAlarm();
            const event = events.find(e => e.id === eventId);
            if (event) {
                selectedEventId = eventId;
                openEditEventModal();
            }
        }

        function playAlarmSound() {
            // Créer un contexte audio
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.type = 'sine';
                oscillator.frequency.value = 800;
                gainNode.gain.value = 0.3;
                
                oscillator.start();
                
                // Arrêter après 2 secondes
                setTimeout(() => {
                    oscillator.stop();
                }, 2000);
            } catch (e) {
                console.log("Les alertes audio ne sont pas supportées dans cet environnement");
            }
        }
    </script>
</body>
</html>