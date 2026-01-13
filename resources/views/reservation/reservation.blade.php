<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PSI AFRICA - Génération de Documents</title>
    <link rel="icon" href="{{ asset('favicon.png') }}"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>
    <style>
        :root {
            --psi-blue: #0b3c8c;
            --psi-light-blue: #2a5cbd;
            --psi-orange: #ff7b00;
            --psi-gray: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fb;
            color: #333;
        }
        
        .header {
            background: linear-gradient(135deg, var(--psi-blue) 0%, var(--psi-light-blue) 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-icon {
            width: 50px;
            height: 50px;
            background-color: white;
            color: var(--psi-blue);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .logo-text {
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .tagline {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .nav-tabs .nav-link {
            color: var(--psi-blue);
            font-weight: 500;
            border: none;
            padding: 12px 25px;
        }
        
        .nav-tabs .nav-link.active {
            background-color: var(--psi-blue);
            color: white;
            border: none;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: var(--psi-blue);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
            font-weight: 600;
        }
        
        .btn-psi {
            background-color: var(--psi-blue);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 500;
        }
        
        .btn-psi:hover {
            background-color: var(--psi-light-blue);
            color: white;
        }
        
        .btn-outline-psi {
            border: 1px solid var(--psi-blue);
            color: var(--psi-blue);
            background-color: transparent;
        }
        
        .btn-outline-psi:hover {
            background-color: var(--psi-blue);
            color: white;
        }
        
        .form-label {
            font-weight: 500;
            color: #555;
        }
        
        .document-preview {
            background-color: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 0;
            min-height: 800px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            overflow: auto;
            max-height: 1000px;
        }

        .document-preview .document-print {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .document-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--psi-blue);
        }
        
        .document-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--psi-blue);
            margin-bottom: 10px;
        }
        
        .document-reference {
            font-size: 1rem;
            color: #666;
            margin-bottom: 5px;
        }
        
        .document-date {
            font-size: 1rem;
            color: #666;
        }
        
        .document-body {
            line-height: 1.6;
        }
        
        .document-signature {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .signature-text {
            text-align: right;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin-left: auto;
            margin-bottom: 5px;
        }
        
        .qr-code {
            width: 70px;
            height: 70px;
            margin-top: 10px;
        }

        .qr-code img {
            width: 70px !important;
            height: 70px !important;
            display: block !important;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
            image-rendering: pixelated;
        }
        
        .traveler-item {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid var(--psi-blue);
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .archive-item {
            border-left: 4px solid var(--psi-blue);
            padding: 15px;
            margin-bottom: 15px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            z-index: 1000;
            display: flex;
            align-items: center;
            max-width: 400px;
            transform: translateX(150%);
            transition: transform 0.3s;
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification-success {
            background-color: #28a745;
        }
        
        .notification-info {
            background-color: var(--psi-light-blue);
        }
        
        /* Styles pour l'impression A4 */
        @media print {
            body * {
                visibility: hidden;
            }
            .document-print, .document-print * {
                visibility: visible;
            }
            .document-print {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
            }
            .no-print {
                display: none !important;
            }
        }
        
        .document-print {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.4;
            padding: 19mm 28mm 19mm 28mm;
            max-width: 510mm;
            max-height: 597mm;
            box-sizing: border-box;
            background: white;
            page-break-after: auto;
            page-break-inside: avoid;
            overflow: hidden;
        }

        .company-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 6px;
            padding-bottom: 6px;
            border-bottom: 2px solid #ff9933;
        }

        .company-logo {
            width: 180px;
            height: auto;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .company-info {
            flex: 1;
            text-align: right;
            font-size: 7.5pt;
            color: #555;
            line-height: 1.4;
        }

        .company-info p {
            margin: 1px 0;
            color: #666;
        }

        .company-info .company-note {
            color: #0066cc;
            font-size: 7pt;
            margin-top: 3px;
            font-style: italic;
        }
        
        .document-print .document-header {
            margin-bottom: 8px;
            padding-bottom: 5px;
        }

        .document-print .document-title {
            font-size: 14pt;
            margin-bottom: 5px;
            line-height: 1.2;
            color: #6699cc;
            text-align: center;
        }

        .document-print .document-reference,
        .document-print .document-date {
            font-size: 9pt;
            line-height: 1.3;
        }

        .document-print .document-body {
            font-size: 10pt;
            line-height: 1.4;
        }

        .document-print table {
            margin: 12px 0 !important;
            width: 100%;
        }

        .document-print table td,
        .document-print table th {
            padding: 8px 10px !important;
            font-size: 10pt !important;
            line-height: 1.5 !important;
        }

        .document-print p {
            margin: 8px 0;
        }

        .document-print .mt-4 {
            margin-top: 18px !important;
        }

        .document-print .mb-4 {
            margin-bottom: 15px !important;
        }

        .document-print .qr-code {
            width: 70px;
            height: 70px;
        }

        .document-print .qr-code img {
            width: 70px !important;
            height: 70px !important;
            display: block !important;
            max-width: none !important;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
            image-rendering: pixelated;
        }

        .document-print .document-signature {
            margin-top: 20px !important;
            padding-top: 8px !important;
        }

        .document-print .signature-text {
            font-size: 9pt;
            line-height: 2.5;
        }

        .document-print .signature-text div {
            margin-top: 6px !important;
        }

        .document-print .signature-text strong {
            display: block;
            margin-top: 4px;
        }
        
        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }

            .document-preview {
                padding: 0;
                min-height: auto;
            }

            .document-preview .document-print {
                width: 100%;
                min-height: auto;
                transform: scale(0.5);
                transform-origin: top left;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header no-print">
        <div class="container">
            <div class="logo">
                <div class="logo-icon">P</div>
                <div>
                    <div class="logo-text">PSI AFRICA</div>
                    <div class="tagline">Système Automatique de Génération de Documents</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-4 no-print">
        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs mb-4" id="mainTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="billet-tab" data-bs-toggle="tab" data-bs-target="#billet" type="button" role="tab">
                    <i class="fas fa-plane me-2"></i>Billet d'Avion
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="hotel-tab" data-bs-toggle="tab" data-bs-target="#hotel" type="button" role="tab">
                    <i class="fas fa-hotel me-2"></i>Réservation Hôtel
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="archives-tab" data-bs-toggle="tab" data-bs-target="#archives" type="button" role="tab">
                    <i class="fas fa-archive me-2"></i>Archives
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="parametres-tab" data-bs-toggle="tab" data-bs-target="#parametres" type="button" role="tab">
                    <i class="fas fa-cog me-2"></i>Paramètres
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="mainTabsContent">
            <!-- Billet d'Avion Tab -->
            <div class="tab-pane fade show active" id="billet" role="tabpanel">
                <div class="row">
                    <!-- Formulaire -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-plane me-2"></i>Informations du Billet d'Avion
                            </div>
                            <div class="card-body">
                                <form id="billetForm">
                                    <div class="mb-3">
                                        <label class="form-label">Date du document</label>
                                        <input type="date" class="form-control" id="billetDate" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Référence</label>
                                        <input type="text" class="form-control" id="billetReference" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Voyageur(s)</label>
                                        <div id="voyageursList">
                                            <!-- Les voyageurs seront ajoutés ici dynamiquement -->
                                        </div>
                                        <button type="button" class="btn btn-outline-psi btn-sm mt-2" onclick="addVoyageur()">
                                            <i class="fas fa-plus me-1"></i> Ajouter un voyageur
                                        </button>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Destination</label>
                                        <input type="text" class="form-control" id="destination" placeholder="Ex: France">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Ville</label>
                                        <input type="text" class="form-control" id="ville" placeholder="Ex: Paris">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Compagnie Aérienne</label>
                                        <input type="text" class="form-control" id="compagnie" placeholder="Ex: Air France">
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Date de Départ</label>
                                            <input type="date" class="form-control" id="dateDepart">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Date de Retour</label>
                                            <input type="date" class="form-control" id="dateRetour">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Référence de Réservation</label>
                                        <input type="text" class="form-control" id="refReservation" placeholder="Ex: YUGP3H">
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-psi" onclick="genererApercuBillet()">
                                            <i class="fas fa-eye me-2"></i> Générer l'Aperçu
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Aperçu -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-file-alt me-2"></i>Aperçu du Document
                            </div>
                            <div class="card-body">
                                <div class="document-preview" id="billetPreview">
                                    <!-- L'aperçu du billet sera généré ici -->
                                    <div class="text-center text-muted mt-5">
                                        <i class="fas fa-plane fa-3x mb-3"></i>
                                        <p>L'aperçu du billet d'avion apparaîtra ici</p>
                                    </div>
                                </div>
                                
                                <div class="action-buttons mt-3" id="billetActions" style="display: none;">
                                    <button class="btn btn-psi" onclick="genererPDF('billet')">
                                        <i class="fas fa-file-pdf me-2"></i> PDF
                                    </button>
                                    <button class="btn btn-psi" onclick="genererWord('billet')">
                                        <i class="fas fa-file-word me-2"></i> Word
                                    </button>
                                    <button class="btn btn-outline-psi" onclick="genererLien('billet')">
                                        <i class="fas fa-link me-2"></i> Lien
                                    </button>
                                    <button class="btn btn-outline-psi" onclick="envoyerWhatsApp('billet')">
                                        <i class="fab fa-whatsapp me-2"></i> WhatsApp
                                    </button>
                                    <button class="btn btn-outline-psi" onclick="archiverDocument('billet')">
                                        <i class="fas fa-save me-2"></i> Archiver
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Réservation Hôtel Tab -->
            <div class="tab-pane fade" id="hotel" role="tabpanel">
                <div class="row">
                    <!-- Formulaire -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-hotel me-2"></i>Informations de l'Hôtel
                            </div>
                            <div class="card-body">
                                <form id="hotelForm">
                                    <div class="mb-3">
                                        <label class="form-label">Date du document</label>
                                        <input type="date" class="form-control" id="hotelDate" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Référence</label>
                                        <input type="text" class="form-control" id="hotelReference" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Nom(s) du/des client(s)</label>
                                        <textarea class="form-control" id="hotelClients" rows="2" placeholder="Un nom par ligne"></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Nom de l'Hôtel</label>
                                        <input type="text" class="form-control" id="nomHotel" placeholder="Nom de l'établissement">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Adresse de l'Hôtel</label>
                                        <textarea class="form-control" id="adresseHotel" rows="2" placeholder="Adresse complète"></textarea>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Téléphone de l'Hôtel</label>
                                            <input type="text" class="form-control" id="telephoneHotel" placeholder="+33 1 XX XX XX XX">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email de l'Hôtel</label>
                                            <input type="email" class="form-control" id="emailHotel" placeholder="contact@hotel.com">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Date d'Arrivée</label>
                                            <input type="date" class="form-control" id="dateArrivee">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Date de Départ</label>
                                            <input type="date" class="form-control" id="dateDepartHotel">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Nombre de Nuits</label>
                                            <input type="number" class="form-control" id="nuits" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Type d'Appartement</label>
                                            <input type="text" class="form-control" id="typeAppartement" placeholder="Ex: 1 Appartement">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Nombre d'Adultes</label>
                                            <input type="number" class="form-control" id="adultes" min="1" placeholder="1">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Nombre d'Enfants</label>
                                            <input type="number" class="form-control" id="enfants" min="0" placeholder="0">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Tarif Total (Euro)</label>
                                        <input type="number" class="form-control" id="tarifEuro" placeholder="0">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Tarif en FCFA</label>
                                        <input type="text" class="form-control" id="tarifFCFA" readonly>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-psi" onclick="genererApercuHotel()">
                                            <i class="fas fa-eye me-2"></i> Générer l'Aperçu
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Aperçu -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-file-alt me-2"></i>Aperçu du Document
                            </div>
                            <div class="card-body">
                                <div class="document-preview" id="hotelPreview">
                                    <!-- L'aperçu de l'hôtel sera généré ici -->
                                    <div class="text-center text-muted mt-5">
                                        <i class="fas fa-hotel fa-3x mb-3"></i>
                                        <p>L'aperçu de la réservation d'hôtel apparaîtra ici</p>
                                    </div>
                                </div>
                                
                                <div class="action-buttons mt-3" id="hotelActions" style="display: none;">
                                    <button class="btn btn-psi" onclick="genererPDF('hotel')">
                                        <i class="fas fa-file-pdf me-2"></i> PDF
                                    </button>
                                    <button class="btn btn-psi" onclick="genererWord('hotel')">
                                        <i class="fas fa-file-word me-2"></i> Word
                                    </button>
                                    <button class="btn btn-outline-psi" onclick="genererLien('hotel')">
                                        <i class="fas fa-link me-2"></i> Lien
                                    </button>
                                    <button class="btn btn-outline-psi" onclick="envoyerWhatsApp('hotel')">
                                        <i class="fab fa-whatsapp me-2"></i> Envoyer WhatsApp
                                    </button>
                                    <button class="btn btn-outline-psi" onclick="archiverDocument('hotel')">
                                        <i class="fas fa-save me-2"></i> Archiver
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Archives Tab -->
            <div class="tab-pane fade" id="archives" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-archive me-2"></i>Documents Archivés
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="searchArchive" placeholder="Rechercher dans les archives...">
                        </div>
                        <div id="archivesList">
                            <!-- Les documents archivés seront affichés ici -->
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Paramètres Tab -->
            <div class="tab-pane fade" id="parametres" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-user me-2"></i>Paramètres de l'Agent
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Nom de l'Agent</label>
                                    <input type="text" class="form-control" id="agentName" value="{{ Auth::user()->name ?? 'Agent' }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Fonction</label>
                                    <input type="text" class="form-control" id="agentFonction" value="Agent Comptoir">
                                </div>
                                <button class="btn btn-psi" onclick="sauvegarderParametres()">
                                    <i class="fas fa-save me-2"></i> Sauvegarder
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-cog me-2"></i>Paramètres du Système
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Taux de Change Euro/FCFA</label>
                                    <input type="number" class="form-control" id="tauxChange" value="655" step="0.01">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Format de Référence</label>
                                    <input type="text" class="form-control" id="formatReference" value="PSIA-{date}-{num}">
                                    <div class="form-text">{date} = date du jour, {num} = numéro séquentiel</div>
                                </div>
                                <button class="btn btn-psi" onclick="sauvegarderParametres()">
                                    <i class="fas fa-save me-2"></i> Sauvegarder
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Zone d'impression cachée -->
    <div id="printZone" style="display: none;"></div>

    <!-- Notifications -->
    <div class="notification notification-success" id="successNotification">
        <div class="notification-icon"><i class="fas fa-check-circle"></i></div>
        <div>Document généré avec succès!</div>
    </div>
    
    <div class="notification notification-info" id="linkNotification">
        <div class="notification-icon"><i class="fas fa-link"></i></div>
        <div>Lien copié dans le presse-papier!</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variables globales
        let voyageurs = [];
        let archives = JSON.parse(localStorage.getItem('psiArchives')) || [];
        let agentName = localStorage.getItem('psiAgentName') || "{{ Auth::user()->name ?? 'Agent' }}";
        let agentFonction = localStorage.getItem('psiAgentFonction') || "{{ Auth::user()->fonction ?? 'Agent Comptoir' }}";
        let tauxChange = localStorage.getItem('psiTauxChange') || 655;
        let formatReference = localStorage.getItem('psiFormatReference') || "PSIA-{date}-{num}";
        let currentDocumentData = null;
        
        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            // Initialiser les paramètres
            document.getElementById('agentName').value = agentName;
            document.getElementById('agentFonction').value = agentFonction;
            document.getElementById('tauxChange').value = tauxChange;
            document.getElementById('formatReference').value = formatReference;

            // Définir la date du jour automatiquement
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('billetDate').value = today;
            document.getElementById('hotelDate').value = today;

            // Générer les références initiales automatiquement
            genererReference('billet');
            genererReference('hotel');

            // Charger les archives
            chargerArchives();

            // Ajouter un voyageur vide par défaut
            addVoyageur();

            // Écouteurs d'événements pour le calcul automatique
            document.getElementById('dateArrivee').addEventListener('change', calculerNuits);
            document.getElementById('dateDepartHotel').addEventListener('change', calculerNuits);
            document.getElementById('tarifEuro').addEventListener('input', convertirTarif);

            // Vérifier si un document est demandé via URL
            const urlParams = new URLSearchParams(window.location.search);
            const docId = urlParams.get('doc');
            if (docId) {
                afficherDocumentFromURL(docId);
            }
        });
        
        // Ajouter un voyageur avec données optionnelles
        function addVoyageur(data = {}) {
            const voyageursList = document.getElementById('voyageursList');
            const index = voyageurs.length;
            
            const voyageurDiv = document.createElement('div');
            voyageurDiv.className = 'traveler-item';
            voyageurDiv.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Nom et Prénom</label>
                        <input type="text" class="form-control voyageur-nom" placeholder="Nom et prénom du voyageur" value="${data.nom || ''}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Numéro de Passeport</label>
                        <input type="text" class="form-control voyageur-passeport" placeholder="Numéro de passeport" value="${data.passeport || ''}">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-4">
                        <label class="form-label">Pays d'Émission</label>
                        <select class="form-control voyageur-pays">
                            <option value="">Sélectionner</option>
                            <option value="Côte d'Ivoire" ${data.pays === "Côte d'Ivoire" ? 'selected' : ''}>Côte d'Ivoire</option>
                            <option value="France" ${data.pays === "France" ? 'selected' : ''}>France</option>
                            <option value="Sénégal" ${data.pays === "Sénégal" ? 'selected' : ''}>Sénégal</option>
                            <option value="Mali" ${data.pays === "Mali" ? 'selected' : ''}>Mali</option>
                            <option value="Burkina Faso" ${data.pays === "Burkina Faso" ? 'selected' : ''}>Burkina Faso</option>
                            <option value="Ghana" ${data.pays === "Ghana" ? 'selected' : ''}>Ghana</option>
                            <option value="Autre" ${data.pays === "Autre" ? 'selected' : ''}>Autre</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date de Délivrance</label>
                        <input type="date" class="form-control voyageur-delivrance" value="${data.delivrance || ''}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date d'Expiration</label>
                        <input type="date" class="form-control voyageur-expiration" value="${data.expiration || ''}">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <label class="form-label">Lieu d'établissement</label>
                        <input type="text" class="form-control voyageur-lieu" placeholder="Lieu d'établissement du passeport" value="${data.lieu || ''}">
                    </div>
                </div>
                ${index > 0 ? `<button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="removeVoyageur(${index})">
                    <i class="fas fa-trash me-1"></i> Supprimer
                </button>` : ''}
            `;
            
            voyageursList.appendChild(voyageurDiv);
            voyageurs.push({
                nom: data.nom || '',
                passeport: data.passeport || '',
                pays: data.pays || '',
                delivrance: data.delivrance || '',
                expiration: data.expiration || '',
                lieu: data.lieu || ''
            });
        }
        
        // Les autres fonctions restent inchangées mais sont nécessaires au fonctionnement
        function removeVoyageur(index) {
            voyageurs.splice(index, 1);
            document.getElementById('voyageursList').children[index].remove();
            // Réindexer les voyageurs restants
            for (let i = index; i < voyageurs.length; i++) {
                const removeBtn = document.getElementById('voyageursList').children[i].querySelector('button');
                if (removeBtn) {
                    removeBtn.setAttribute('onclick', `removeVoyageur(${i})`);
                }
            }
        }
        
        function calculerNuits() {
            const arriveeVal = document.getElementById('dateArrivee').value;
            const departVal = document.getElementById('dateDepartHotel').value;

            if (arriveeVal && departVal) {
                const arrivee = new Date(arriveeVal);
                const depart = new Date(departVal);

                if (depart > arrivee) {
                    const diffTime = depart - arrivee;
                    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                    document.getElementById('nuits').value = diffDays;
                } else {
                    document.getElementById('nuits').value = '';
                }
            } else {
                document.getElementById('nuits').value = '';
            }
        }
        
        function convertirTarif() {
            const tarifEuro = parseFloat(document.getElementById('tarifEuro').value) || 0;
            const tarifFCFA = tarifEuro * tauxChange;
            document.getElementById('tarifFCFA').value = formatCurrency(tarifFCFA);
        }
        
        function genererReference(type) {
            const now = new Date();
            const dateStr = now.toISOString().slice(0, 10).replace(/-/g, '');
            const count = archives.filter(doc => doc.type === type && 
                doc.date.slice(0, 10) === now.toISOString().slice(0, 10)).length + 1;
            
            let reference = formatReference
                .replace('{date}', dateStr)
                .replace('{num}', count.toString().padStart(3, '0'));
            
            document.getElementById(type + 'Reference').value = reference;
            return reference;
        }
        
        function genererApercuBillet() {
            // Récupérer les données des voyageurs
            voyageurs = [];
            const voyageurElements = document.getElementById('voyageursList').children;
            
            for (let i = 0; i < voyageurElements.length; i++) {
                const element = voyageurElements[i];
                voyageurs.push({
                    nom: element.querySelector('.voyageur-nom').value,
                    passeport: element.querySelector('.voyageur-passeport').value,
                    pays: element.querySelector('.voyageur-pays').value,
                    delivrance: element.querySelector('.voyageur-delivrance').value,
                    expiration: element.querySelector('.voyageur-expiration').value,
                    lieu: element.querySelector('.voyageur-lieu').value
                });
            }
            
            const dateDoc = document.getElementById('billetDate').value;
            const reference = document.getElementById('billetReference').value;
            const destination = document.getElementById('destination').value;
            const ville = document.getElementById('ville').value;
            const compagnie = document.getElementById('compagnie').value;
            const dateDepart = document.getElementById('dateDepart').value;
            const dateRetour = document.getElementById('dateRetour').value;
            const refReservation = document.getElementById('refReservation').value;
            
            // Calculer la durée
            let duree = 0;
            if (dateDepart && dateRetour) {
                const depart = new Date(dateDepart);
                const retour = new Date(dateRetour);
                duree = Math.floor((retour - depart) / (1000 * 60 * 60 * 24));
            }
            
            // Stocker les données pour le PDF
            currentDocumentData = {
                type: 'billet',
                dateDoc: dateDoc,
                reference: reference,
                destination: destination,
                ville: ville,
                compagnie: compagnie,
                dateDepart: dateDepart,
                dateRetour: dateRetour,
                duree: duree,
                refReservation: refReservation,
                voyageurs: [...voyageurs]
            };
            
            // Générer l'aperçu HTML
            let html = `
                <div class="document-print">
                    <div class="company-header">
                        <img src="/img/logo.png" alt="PSI INTERNATIONAL AFRICA" class="company-logo">
                        <div class="company-info">
                            <p><strong>Cabinet Conseiller Expert (CCE)</strong> - Capital Social de 1.000.000 F CFA - RC CI - ABJ-03-2022-B13-09932 - CC 2244494 - Centre Impôts D.G.E Yopougon Zone Industrielle de Yopougon 21 B.P. 3167 Abidjan 21 - Tél : +225 27 22 216 602 - &#128231; - +225 01 04 04 94 95 - N°SIA BANQUE - IBAN : CI65 CI04 2012 0107 8800 7029 0101</p>
                            <p class="company-note">PSI AFRICA est le nom commercial officiellement exploité par CCE - Cabinet Conseiller Expert.</p>
                        </div>
                    </div>

                    <div class="document-header">
                        <h1 class="document-title">ATTESTATION OFFICIELLE DE RÉSERVATION DE BILLET D'AVION</h1>
                        <div class="document-reference">REF : ${reference}</div>
                        <div class="document-date">Date : ${formatDate(dateDoc)}</div>
                    </div>
                    
                    <div class="document-body">
                        <p>
                            Nous soussignés, <strong>PSI INTERNATIONAL - AFRICA</strong>, <strong>Agence de Voyages et Tourisme</strong>
                            sis à Yopougon, Carrefour zone industriel, RC-CI-ABJ-03-2022-B13-09932, N°CC : 2244494,
                            21 BP 3167 ABJ 21, attestons avoir effectué une réservation de billet d'avion en faveur de :
                        </p>
                        
                        <div class="mt-4 mb-4">
            `;
            
            // Ajouter les voyageurs
            voyageurs.forEach(voyageur => {
                if (voyageur.nom) {
                    html += `
                        <p><strong>${voyageur.nom}</strong></p>
                        <p>Passeport : ${voyageur.passeport}, établi à ${voyageur.lieu || voyageur.pays}, du ${formatDate(voyageur.delivrance)} au ${formatDate(voyageur.expiration)}</p>
                    `;
                }
            });
            
            html += `
                        </div>

                        <table width="100%" style="margin: 20px 0;">
                            <tr>
                                <td style="padding: 8px;"><strong>Pays de destination</strong></td>
                                <td style="padding: 8px;">${destination}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px;"><strong>Ville de destination</strong></td>
                                <td style="padding: 8px;">${ville}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px;"><strong>Compagnie aérienne</strong></td>
                                <td style="padding: 8px;">${compagnie}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px;"><strong>Date de départ</strong></td>
                                <td style="padding: 8px;">${formatDate(dateDepart)}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px;"><strong>Date de retour</strong></td>
                                <td style="padding: 8px;">${formatDate(dateRetour)}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px;"><strong>Durée du séjour</strong></td>
                                <td style="padding: 8px;">${duree} jours</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px;"><strong>Nombre de voyageurs</strong></td>
                                <td style="padding: 8px;">${voyageurs.filter(v => v.nom && v.nom !== '[NOM COMPLET DU 2ᵉ VOYAGEUR]' && v.nom !== '[NOM COMPLET DU 3ᵉ VOYAGEUR]').length} personnes</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px;"><strong>Référence réservation</strong></td>
                                <td style="padding: 8px;">${refReservation}</td>
                            </tr>
                        </table>
                        
                        <p class="mt-4">
                            En foi de quoi, nous lui délivrons la présente attestation pour servir et valoir ce que de droit.
                        </p>
                        
                        <div class="document-signature">
                            <div class="qr-code" id="billetQRCode"></div>
                            <div class="signature-text">
                                <div>Fait à Abidjan, le ${formatDate(dateDoc)}</div>
                                <div style="margin-top: 10px;"><strong>Signature et cachet</strong></div>
                                <div style="margin-top: 5px;"><strong>PSI AFRICA – Agence de Voyages & Tourisme</strong></div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('billetPreview').innerHTML = html;
            document.getElementById('billetActions').style.display = 'flex';
            
            // Générer le QR code
            genererQRCode('billetQRCode', reference, 'billet');
        }
        
        function genererApercuHotel() {
            const dateDoc = document.getElementById('hotelDate').value;
            const reference = document.getElementById('hotelReference').value;
            const clients = document.getElementById('hotelClients').value;
            const nomHotel = document.getElementById('nomHotel').value;
            const adresseHotel = document.getElementById('adresseHotel').value;
            const telephoneHotel = document.getElementById('telephoneHotel').value;
            const emailHotel = document.getElementById('emailHotel').value;
            const dateArrivee = document.getElementById('dateArrivee').value;
            const dateDepartHotel = document.getElementById('dateDepartHotel').value;
            const nuits = document.getElementById('nuits').value;
            const typeAppartement = document.getElementById('typeAppartement').value;
            const adultes = document.getElementById('adultes').value;
            const enfants = document.getElementById('enfants').value;
            const tarifEuro = document.getElementById('tarifEuro').value;
            const tarifFCFA = document.getElementById('tarifFCFA').value;
            
            // Stocker les données pour le PDF
            currentDocumentData = {
                type: 'hotel',
                dateDoc: dateDoc,
                reference: reference,
                clients: clients,
                nomHotel: nomHotel,
                adresseHotel: adresseHotel,
                telephoneHotel: telephoneHotel,
                emailHotel: emailHotel,
                dateArrivee: dateArrivee,
                dateDepartHotel: dateDepartHotel,
                nuits: nuits,
                typeAppartement: typeAppartement,
                adultes: adultes,
                enfants: enfants,
                tarifEuro: tarifEuro,
                tarifFCFA: tarifFCFA
            };
            
            // Générer l'aperçu HTML
            let html = `
                <div class="document-print">
                    <div class="company-header">
                        <img src="/img/logo.png" alt="PSI INTERNATIONAL AFRICA" class="company-logo">
                        <div class="company-info">
                            <p><strong>Cabinet Conseiller Expert (CCE)</strong> - Capital Social de 1.000.000 F CFA - RC CI - ABJ-03-2022-B13-09932 - CC 2244494 - Centre Impôts D.G.E Yopougon Zone Industrielle de Yopougon 21 B.P. 3167 Abidjan 21 - Tél : +225 27 22 216 602 - &#128231; - +225 01 04 04 94 95 - N°SIA BANQUE - IBAN : CI65 CI04 2012 0107 8800 7029 0101</p>
                            <p class="company-note">PSI AFRICA est le nom commercial officiellement exploité par CCE - Cabinet Conseiller Expert.</p>
                        </div>
                    </div>

                    <div class="document-header">
                        <h1 class="document-title">ATTESTATION OFFICIELLE DE RÉSERVATION D'HÔTEL</h1>
                        <div class="document-reference">REF : ${reference}</div>
                        <div class="document-date">Date : ${formatDate(dateDoc)}</div>
                    </div>

                    <div class="document-body">
                        <p>
                            Nous soussignés, <strong>PSI INTERNATIONAL - AFRICA</strong>, <strong>Agence de Voyages et Tourisme</strong>
                            sis à Yopougon, Carrefour zone industriel, RC-CI-ABJ-03-2022-B13-09932, N°CC : 2244494,
                            21 BP 3167 ABJ 21, attestons avoir effectué une réservation d'Hôtel en faveur de :
                        </p>
                        
                        <div class="mt-3">
                            ${clients.split('\n').map(client => `<p><strong>${client}</strong></p>`).join('')}
                        </div>
                        
                        <table width="100%" style="margin: 20px 0;">
                            <tr>
                                <td style="padding: 8px;"><strong>Pays / Ville</strong></td>
                                <td style="padding: 8px;">FRANCE / PARIS</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px;"><strong>Établissement</strong></td>
                                <td style="padding: 8px;">${nomHotel}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px;"><strong>Adresse</strong></td>
                                <td style="padding: 8px;">${adresseHotel}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px;"><strong>Téléphone</strong></td>
                                <td style="padding: 8px;">${telephoneHotel}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px;"><strong>Email</strong></td>
                                <td style="padding: 8px;">${emailHotel}</td>
                            </tr>
                        </table>

                        <table width="100%" style="margin: 20px 0;">
                            <tr>
                                <td style="padding: 8px;"><strong>Date d'arrivée</strong></td>
                                <td style="padding: 8px;">${formatDate(dateArrivee)}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px;"><strong>Date de départ</strong></td>
                                <td style="padding: 8px;">${formatDate(dateDepartHotel)}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px;"><strong>Durée du séjour</strong></td>
                                <td style="padding: 8px;">${nuits} nuits</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px;"><strong>Type d'hébergement</strong></td>
                                <td style="padding: 8px;">${typeAppartement}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px;"><strong>Nombre de voyageurs</strong></td>
                                <td style="padding: 8px;">${adultes} adulte(s), ${enfants} enfant(s)</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px;"><strong>Tarif total du séjour</strong></td>
                                <td style="padding: 8px;"><strong>${tarifEuro} Euros (${tarifFCFA})</strong></td>
                            </tr>
                        </table>
                        
                        <p class="mt-4">
                            En foi de quoi, nous lui délivrons la présente attestation pour servir et valoir ce que de droit.
                        </p>
                        
                        <div class="document-signature">
                            <div class="qr-code" id="hotelQRCode"></div>
                            <div class="signature-text">
                                <div>Fait à Abidjan, le ${formatDate(dateDoc)}</div>
                                <div style="margin-top: 10px;"><strong>Signature et cachet</strong></div>
                                <div style="margin-top: 5px;"><strong>PSI AFRICA – Agence de Voyages & Tourisme</strong></div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('hotelPreview').innerHTML = html;
            document.getElementById('hotelActions').style.display = 'flex';
            
            // Générer le QR code
            genererQRCode('hotelQRCode', reference, 'hotel');
        }
        
        // Les autres fonctions (genererQRCode, genererPDF, etc.) restent inchangées
        function genererQRCode(elementId, data, type) {
            const docId = generateId();
            const docData = {
                id: docId,
                type: type,
                reference: data,
                timestamp: new Date().toISOString(),
                data: currentDocumentData
            };

            localStorage.setItem('psiDoc_' + docId, JSON.stringify(docData));

            const url = `${window.location.origin}${window.location.pathname}?doc=${docId}`;

            const qr = qrcode(0, 'H'); // Utiliser 'H' pour une meilleure qualité
            qr.addData(url);
            qr.make();

            // Créer l'image avec une taille fixe et un style inline
            const imgTag = qr.createImgTag(3); // Taille du cellule
            const container = document.getElementById(elementId);
            container.innerHTML = imgTag;

            // Ajouter des styles inline pour s'assurer que l'image est bien rendue
            const img = container.querySelector('img');
            if (img) {
                img.style.width = '70px';
                img.style.height = '70px';
                img.style.display = 'block';
                img.style.imageRendering = 'pixelated';
            }
        }
        
        function genererPDF(type) {
            const element = document.getElementById(type + 'Preview');
            const documentPrint = element.querySelector('.document-print');

            if (!documentPrint) {
                alert('Veuillez d\'abord générer l\'aperçu');
                return;
            }

            // Attendre un moment pour s'assurer que le QR code est bien rendu
            setTimeout(() => {
                const opt = {
                    margin: 0,
                    filename: `${type}_${document.getElementById(type + 'Reference').value}.pdf`,
                    image: { type: 'jpeg', quality: 1 },
                    html2canvas: {
                        scale: 2,
                        useCORS: true,
                        logging: false,
                        allowTaint: true,
                        foreignObjectRendering: false,
                        imageTimeout: 0,
                        letterRendering: true,
                        scrollX: 0,
                        scrollY: 0
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: 'a4',
                        orientation: 'portrait',
                        compress: true
                    },
                    pagebreak: { mode: 'avoid-all', before: '.page-break' }
                };

                html2pdf().set(opt).from(documentPrint).save().then(() => {
                    // Auto-archiver le document après génération du PDF
                    archiverDocument(type);
                }).catch(err => {
                    console.error('Erreur PDF:', err);
                    alert('Erreur lors de la génération du PDF');
                });
            }, 800);
        }

        function genererWord(type) {
            if (!currentDocumentData) {
                alert('Veuillez d\'abord générer l\'aperçu du document');
                return;
            }

            // Préparer les données pour le document Word
            const data = {
                type: type,
                reference: document.getElementById(type + 'Reference').value,
                date_document: document.getElementById(type + 'Date').value,
                agent_name: agentName,
                agent_fonction: agentFonction
            };

            if (type === 'billet') {
                data.destination = currentDocumentData.destination;
                data.ville = currentDocumentData.ville;
                data.compagnie = currentDocumentData.compagnie;
                data.date_depart = currentDocumentData.dateDepart;
                data.date_retour = currentDocumentData.dateRetour;
                data.ref_reservation = currentDocumentData.refReservation;
                data.voyageurs = JSON.stringify(currentDocumentData.voyageurs);
            } else {
                data.clients = document.getElementById('hotelClients').value;
                data.nom_hotel = currentDocumentData.nomHotel;
                data.adresse_hotel = currentDocumentData.adresseHotel;
                data.telephone_hotel = currentDocumentData.telephoneHotel;
                data.email_hotel = currentDocumentData.emailHotel;
                data.date_arrivee = currentDocumentData.dateArrivee;
                data.date_depart_hotel = currentDocumentData.dateDepartHotel;
                data.nuits = currentDocumentData.nuits;
                data.type_appartement = currentDocumentData.typeAppartement;
                data.adultes = currentDocumentData.adultes;
                data.enfants = currentDocumentData.enfants;
                data.tarif_euro = currentDocumentData.tarifEuro;
                data.tarif_fcfa = currentDocumentData.tarifFCFA ? parseFloat(currentDocumentData.tarifFCFA.replace(/[^\d]/g, '')) : 0;
            }

            // Créer un formulaire pour le téléchargement
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/reservation/generate-word';
            form.style.display = 'none';

            // CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfInput);

            // Ajouter les données au formulaire
            for (const key in data) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = data[key] || '';
                form.appendChild(input);
            }

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);

            // Auto-archiver le document après génération du Word
            archiverDocument(type);
        }

        function genererLien(type) {
            const reference = document.getElementById(type + 'Reference').value;
            const docId = generateId();
            const docData = {
                id: docId,
                type: type,
                reference: reference,
                timestamp: new Date().toISOString(),
                data: currentDocumentData
            };
            
            localStorage.setItem('psiDoc_' + docId, JSON.stringify(docData));
            const link = `${window.location.origin}${window.location.pathname}?doc=${docId}`;
            
            navigator.clipboard.writeText(link).then(() => {
                showNotification('linkNotification');
            });
        }
        
        function envoyerWhatsApp(type) {
            const reference = document.getElementById(type + 'Reference').value;
            const message = `Bonjour, voici votre document PSI AFRICA : ${reference}. Accédez-y via ce lien : [LIEN]`;
            
            genererLien(type);
            
            setTimeout(() => {
                alert(`Message WhatsApp préparé : "${message}"\n\nDans un système réel, cette fonctionnalité enverrait automatiquement le message.`);
            }, 1000);
        }
        
        function archiverDocument(type) {
            const reference = document.getElementById(type + 'Reference').value;
            const date = document.getElementById(type + 'Date').value;
            const content = document.getElementById(type + 'Preview').innerHTML;

            // Préparer les données pour la base de données
            const data = {
                type: type,
                reference: reference,
                date_document: date,
                clients: type === 'billet' ? voyageurs.map(v => v.nom).join('\n') : document.getElementById('hotelClients').value,
                destination: currentDocumentData.destination || null,
                ville: currentDocumentData.ville || null,
                compagnie: currentDocumentData.compagnie || null,
                date_depart: currentDocumentData.dateDepart || null,
                date_retour: currentDocumentData.dateRetour || null,
                ref_reservation: currentDocumentData.refReservation || null,
                voyageurs: type === 'billet' ? JSON.stringify(voyageurs) : null,
                nom_hotel: currentDocumentData.nomHotel || null,
                adresse_hotel: currentDocumentData.adresseHotel || null,
                telephone_hotel: currentDocumentData.telephoneHotel || null,
                email_hotel: currentDocumentData.emailHotel || null,
                date_arrivee: currentDocumentData.dateArrivee || null,
                date_depart_hotel: currentDocumentData.dateDepartHotel || null,
                nuits: currentDocumentData.nuits || null,
                type_appartement: currentDocumentData.typeAppartement || null,
                adultes: currentDocumentData.adultes || null,
                enfants: currentDocumentData.enfants || null,
                tarif_euro: currentDocumentData.tarifEuro || null,
                tarif_fcfa: currentDocumentData.tarifFCFA ? parseFloat(currentDocumentData.tarifFCFA.replace(/[^\d]/g, '')) : null,
                agent_name: agentName,
                agent_fonction: agentFonction
            };

            // Envoyer à la base de données
            fetch('/reservation/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // Aussi sauvegarder en localStorage pour backup
                    const documentArchive = {
                        id: result.reservation.id,
                        type: type,
                        reference: reference,
                        date: date,
                        content: content,
                        archivedAt: new Date().toISOString(),
                        agent: agentName,
                        data: currentDocumentData
                    };

                    archives.push(documentArchive);
                    localStorage.setItem('psiArchives', JSON.stringify(archives));

                    showNotification('successNotification');
                    chargerArchives();
                    genererReference(type);
                } else {
                    alert('Erreur lors de l\'enregistrement: ' + (result.message || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur de connexion au serveur');
            });
        }
        
        function chargerArchives() {
            const archivesList = document.getElementById('archivesList');
            archivesList.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Chargement...</div>';

            // Charger depuis la base de données
            fetch('/reservation/list')
                .then(response => response.json())
                .then(data => {
                    archivesList.innerHTML = '';

                    if (data.length === 0) {
                        archivesList.innerHTML = '<div class="text-center text-muted py-4">Aucun document archivé</div>';
                        return;
                    }

                    data.forEach(doc => {
                        const archiveItem = document.createElement('div');
                        archiveItem.className = 'archive-item';
                        archiveItem.innerHTML = `
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5>${doc.reference}</h5>
                                    <p class="mb-1">${doc.type === 'billet' ? 'Billet d\'Avion' : 'Réservation Hôtel'}</p>
                                    <p class="mb-1 text-muted">Date: ${formatDate(doc.date_document)} | Archivé: ${formatDate(doc.created_at)}</p>
                                    <p class="mb-0 text-muted">Agent: ${doc.agent_name || 'N/A'}</p>
                                </div>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-outline-psi" onclick="afficherArchiveDB(${doc.id})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-psi" onclick="supprimerArchiveDB(${doc.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                        archivesList.appendChild(archiveItem);
                    });
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    // Fallback vers localStorage
                    archivesList.innerHTML = '';
                    if (archives.length === 0) {
                        archivesList.innerHTML = '<div class="text-center text-muted py-4">Aucun document archivé</div>';
                        return;
                    }

                    const sortedArchives = [...archives].sort((a, b) => new Date(b.archivedAt) - new Date(a.archivedAt));

                    sortedArchives.forEach(doc => {
                        const archiveItem = document.createElement('div');
                        archiveItem.className = 'archive-item';
                        archiveItem.innerHTML = `
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5>${doc.reference}</h5>
                                    <p class="mb-1">${doc.type === 'billet' ? 'Billet d\'Avion' : 'Réservation Hôtel'}</p>
                                    <p class="mb-1 text-muted">Date: ${formatDate(doc.date)} | Archivé: ${formatDate(doc.archivedAt)}</p>
                                    <p class="mb-0 text-muted">Agent: ${doc.agent}</p>
                                </div>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-outline-psi" onclick="afficherArchive('${doc.id}')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-psi" onclick="supprimerArchive('${doc.id}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                        archivesList.appendChild(archiveItem);
                    });
                });
        }

        function afficherArchiveDB(id) {
            fetch('/reservation/' + id)
                .then(response => response.json())
                .then(doc => {
                    // Recréer currentDocumentData à partir des données de la BD
                    currentDocumentData = doc;

                    if (doc.type === 'billet') {
                        document.getElementById('billet-tab').click();
                        // Regenerate preview based on doc data
                        genererApercuBilletFromData(doc);
                    } else {
                        document.getElementById('hotel-tab').click();
                        genererApercuHotelFromData(doc);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors du chargement du document');
                });
        }

        function supprimerArchiveDB(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette archive?')) {
                fetch('/reservation/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        chargerArchives();
                        showNotification('successNotification');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                });
            }
        }

        function genererApercuBilletFromData(doc) {
            // Générer l'aperçu à partir des données de la BD
            const voyageursData = doc.voyageurs ? JSON.parse(doc.voyageurs) : [];
            const duree = doc.date_depart && doc.date_retour ? Math.ceil((new Date(doc.date_retour) - new Date(doc.date_depart)) / (1000 * 60 * 60 * 24)) : 0;

            let voyageursRows = '';
            voyageursData.forEach((voyageur, index) => {
                if (voyageur.nom) {
                    voyageursRows += `
                        <tr>
                            <td style="padding: 8px; text-align: center;">${index + 1}</td>
                            <td style="padding: 8px;">${voyageur.nom}</td>
                            <td style="padding: 8px;">${voyageur.passeport || ''}</td>
                            <td style="padding: 8px;">${voyageur.lieu || voyageur.pays || ''}</td>
                            <td style="padding: 8px;">${formatDate(voyageur.delivrance)}</td>
                            <td style="padding: 8px;">${formatDate(voyageur.expiration)}</td>
                        </tr>
                    `;
                }
            });

            let html = `
                <div class="document-print">
                    <div class="company-header">
                        <img src="/img/logo.png" alt="PSI INTERNATIONAL AFRICA" class="company-logo">
                        <div class="company-info">
                            <p><strong>Cabinet Conseiller Expert (CCE)</strong> - Capital Social de 1.000.000 F CFA - RC CI - ABJ-03-2022-B13-09932 - CC 2244494 - Centre Impôts D.G.E Yopougon Zone Industrielle de Yopougon 21 B.P. 3167 Abidjan 21 - Tél : +225 27 22 216 602 - &#128231; - +225 01 04 04 94 95 - N°SIA BANQUE - IBAN : CI65 CI04 2012 0107 8800 7029 0101</p>
                            <p class="company-note">PSI AFRICA est le nom commercial officiellement exploité par CCE - Cabinet Conseiller Expert.</p>
                        </div>
                    </div>

                    <div class="document-header">
                        <h1 class="document-title">ATTESTATION OFFICIELLE DE RÉSERVATION DE BILLET D'AVION</h1>
                        <div class="document-reference">REF : ${doc.reference}</div>
                        <div class="document-date">Date : ${formatDate(doc.date_document)}</div>
                    </div>

                    <div class="document-body">
                        <p>
                            Nous soussignés, <strong>PSI INTERNATIONAL - AFRICA</strong>, <strong>Agence de Voyages et Tourisme</strong>
                            sis à Yopougon, Carrefour zone industriel, RC-CI-ABJ-03-2022-B13-09932, N°CC : 2244494,
                            21 BP 3167 ABJ 21, attestons avoir effectué une réservation de billet d'avion en faveur de :
                        </p>

                        <table width="100%" style="margin: 20px 0;">
                            <tr style="background-color: #f5f5f5;">
                                <th style="padding: 8px;">N°</th>
                                <th style="padding: 8px;">Nom & Prénoms</th>
                                <th style="padding: 8px;">N° Passeport</th>
                                <th style="padding: 8px;">Lieu</th>
                                <th style="padding: 8px;">Délivrance</th>
                                <th style="padding: 8px;">Expiration</th>
                            </tr>
                            ${voyageursRows}
                        </table>

                        <table width="100%" style="margin: 20px 0;">
                            <tr><td style="padding: 8px;"><strong>Pays de destination</strong></td><td style="padding: 8px;">${doc.destination || ''}</td></tr>
                            <tr><td style="padding: 8px;"><strong>Ville de destination</strong></td><td style="padding: 8px;">${doc.ville || ''}</td></tr>
                            <tr><td style="padding: 8px;"><strong>Compagnie aérienne</strong></td><td style="padding: 8px;">${doc.compagnie || ''}</td></tr>
                            <tr><td style="padding: 8px;"><strong>Date de départ</strong></td><td style="padding: 8px;">${formatDate(doc.date_depart)}</td></tr>
                            <tr><td style="padding: 8px;"><strong>Date de retour</strong></td><td style="padding: 8px;">${formatDate(doc.date_retour)}</td></tr>
                            <tr><td style="padding: 8px;"><strong>Durée du séjour</strong></td><td style="padding: 8px;">${duree} jours</td></tr>
                            <tr><td style="padding: 8px;"><strong>Référence réservation</strong></td><td style="padding: 8px;">${doc.ref_reservation || ''}</td></tr>
                        </table>

                        <p class="mt-4">
                            En foi de quoi, nous lui délivrons la présente attestation pour servir et valoir ce que de droit.
                        </p>

                        <div class="document-signature">
                            <div class="qr-code" id="billetQRCodeArchive"></div>
                            <div class="signature-text">
                                <div>Fait à Abidjan, le ${formatDate(doc.date_document)}</div>
                                <div style="margin-top: 10px;"><strong>Signature et cachet</strong></div>
                                <div style="margin-top: 5px;"><strong>PSI AFRICA – Agence de Voyages & Tourisme</strong></div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('billetPreview').innerHTML = html;
            document.getElementById('billetActions').style.display = 'flex';

            // Générer le QR code
            genererQRCode('billetQRCodeArchive', doc.reference, 'billet');
        }

        function genererApercuHotelFromData(doc) {
            let html = `
                <div class="document-print">
                    <div class="company-header">
                        <img src="/img/logo.png" alt="PSI INTERNATIONAL AFRICA" class="company-logo">
                        <div class="company-info">
                            <p><strong>Cabinet Conseiller Expert (CCE)</strong> - Capital Social de 1.000.000 F CFA - RC CI - ABJ-03-2022-B13-09932 - CC 2244494 - Centre Impôts D.G.E Yopougon Zone Industrielle de Yopougon 21 B.P. 3167 Abidjan 21 - Tél : +225 27 22 216 602 - &#128231; - +225 01 04 04 94 95 - N°SIA BANQUE - IBAN : CI65 CI04 2012 0107 8800 7029 0101</p>
                            <p class="company-note">PSI AFRICA est le nom commercial officiellement exploité par CCE - Cabinet Conseiller Expert.</p>
                        </div>
                    </div>

                    <div class="document-header">
                        <h1 class="document-title">ATTESTATION OFFICIELLE DE RÉSERVATION D'HÔTEL</h1>
                        <div class="document-reference">REF : ${doc.reference}</div>
                        <div class="document-date">Date : ${formatDate(doc.date_document)}</div>
                    </div>

                    <div class="document-body">
                        <p>
                            Nous soussignés, <strong>PSI INTERNATIONAL - AFRICA</strong>, <strong>Agence de Voyages et Tourisme</strong>
                            sis à Yopougon, Carrefour zone industriel, RC-CI-ABJ-03-2022-B13-09932, N°CC : 2244494,
                            21 BP 3167 ABJ 21, attestons avoir effectué une réservation d'Hôtel en faveur de :
                        </p>

                        <div class="mt-3">
                            ${(doc.clients || '').split('\n').map(client => `<p><strong>${client}</strong></p>`).join('')}
                        </div>

                        <table width="100%" style="margin: 20px 0;">
                            <tr><td style="padding: 8px;"><strong>Établissement</strong></td><td style="padding: 8px;">${doc.nom_hotel || ''}</td></tr>
                            <tr><td style="padding: 8px;"><strong>Adresse</strong></td><td style="padding: 8px;">${doc.adresse_hotel || ''}</td></tr>
                            <tr><td style="padding: 8px;"><strong>Téléphone</strong></td><td style="padding: 8px;">${doc.telephone_hotel || ''}</td></tr>
                            <tr><td style="padding: 8px;"><strong>Email</strong></td><td style="padding: 8px;">${doc.email_hotel || ''}</td></tr>
                        </table>

                        <table width="100%" style="margin: 20px 0;">
                            <tr><td style="padding: 8px;"><strong>Date d'arrivée</strong></td><td style="padding: 8px;">${formatDate(doc.date_arrivee)}</td></tr>
                            <tr><td style="padding: 8px;"><strong>Date de départ</strong></td><td style="padding: 8px;">${formatDate(doc.date_depart_hotel)}</td></tr>
                            <tr><td style="padding: 8px;"><strong>Durée du séjour</strong></td><td style="padding: 8px;">${doc.nuits || ''} nuits</td></tr>
                            <tr><td style="padding: 8px;"><strong>Type d'hébergement</strong></td><td style="padding: 8px;">${doc.type_appartement || ''}</td></tr>
                            <tr><td style="padding: 8px;"><strong>Nombre de voyageurs</strong></td><td style="padding: 8px;">${doc.adultes || 0} adulte(s), ${doc.enfants || 0} enfant(s)</td></tr>
                            <tr><td style="padding: 8px;"><strong>Tarif total du séjour</strong></td><td style="padding: 8px;"><strong>${doc.tarif_euro || 0} Euros (${formatCurrency(doc.tarif_fcfa || 0)})</strong></td></tr>
                        </table>

                        <p class="mt-4">
                            En foi de quoi, nous lui délivrons la présente attestation pour servir et valoir ce que de droit.
                        </p>

                        <div class="document-signature">
                            <div class="qr-code" id="hotelQRCodeArchive"></div>
                            <div class="signature-text">
                                <div>Fait à Abidjan, le ${formatDate(doc.date_document)}</div>
                                <div style="margin-top: 10px;"><strong>Signature et cachet</strong></div>
                                <div style="margin-top: 5px;"><strong>PSI AFRICA – Agence de Voyages & Tourisme</strong></div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('hotelPreview').innerHTML = html;
            document.getElementById('hotelActions').style.display = 'flex';

            // Générer le QR code
            genererQRCode('hotelQRCodeArchive', doc.reference, 'hotel');
        }
        
        function afficherArchive(id) {
            const doc = archives.find(d => d.id === id);
            if (doc) {
                if (doc.type === 'billet') {
                    document.getElementById('billet-tab').click();
                    document.getElementById('billetPreview').innerHTML = genererHTMLFromData(doc);
                    document.getElementById('billetActions').style.display = 'flex';
                } else {
                    document.getElementById('hotel-tab').click();
                    document.getElementById('hotelPreview').innerHTML = genererHTMLFromData(doc);
                    document.getElementById('hotelActions').style.display = 'flex';
                }
            }
        }
        
        function supprimerArchive(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette archive?')) {
                archives = archives.filter(d => d.id !== id);
                localStorage.setItem('psiArchives', JSON.stringify(archives));
                chargerArchives();
            }
        }
        
        function sauvegarderParametres() {
            agentName = document.getElementById('agentName').value;
            agentFonction = document.getElementById('agentFonction').value;
            tauxChange = parseFloat(document.getElementById('tauxChange').value);
            formatReference = document.getElementById('formatReference').value;
            
            localStorage.setItem('psiAgentName', agentName);
            localStorage.setItem('psiAgentFonction', agentFonction);
            localStorage.setItem('psiTauxChange', tauxChange);
            localStorage.setItem('psiFormatReference', formatReference);
            
            showNotification('successNotification');
        }
        
        function showNotification(id) {
            const notification = document.getElementById(id);
            notification.classList.add('show');
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }
        
        // Utilitaires
        function generateId() {
            return 'id_' + Math.random().toString(36).substr(2, 9);
        }
        
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR');
        }
        
        function formatCurrency(amount) {
            return new Intl.NumberFormat('fr-FR', { 
                style: 'currency', 
                currency: 'XOF' 
            }).format(amount);
        }
        
        function genererHTMLFromData(doc) {
            // Cette fonction génère le HTML à partir des données archivées
            // Implémentation simplifiée pour l'exemple
            return doc.content;
        }
        
        function afficherDocumentFromURL(docId) {
            const docData = localStorage.getItem('psiDoc_' + docId);
            if (docData) {
                const doc = JSON.parse(docData);
                
                if (doc.type === 'billet') {
                    document.getElementById('billet-tab').click();
                    document.getElementById('billetPreview').innerHTML = genererHTMLFromData(doc);
                    document.getElementById('billetActions').style.display = 'flex';
                } else {
                    document.getElementById('hotel-tab').click();
                    document.getElementById('hotelPreview').innerHTML = genererHTMLFromData(doc);
                    document.getElementById('hotelActions').style.display = 'flex';
                }
            } else {
                alert('Document non trouvé');
            }
        }
    </script>
</body>
</html>