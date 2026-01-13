<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Signature de Contrat - PSI AFRICA</title>
    <link rel="icon" href="{{ asset('favicon.png') }}"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <style>
        :root {
            --psi-blue: #0A2463;
            --psi-gold: #D4AF37;
            --psi-light: #F8F9FA;
        }

        body {
            background: linear-gradient(135deg, var(--psi-light) 0%, #e6e9f0 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .header-psi {
            background: var(--psi-blue);
            color: white;
            padding: 20px 0;
            border-bottom: 4px solid var(--psi-gold);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .logo-text {
            font-size: 2rem;
            font-weight: bold;
            color: var(--psi-gold);
        }

        .contract-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 25px;
            margin: 20px 0;
            max-height: 75vh;
            overflow-y: auto;
        }

        .contract-content {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            font-size: 0.95rem;
        }

        .contract-article {
            margin-bottom: 18px;
        }

        .contract-article h4 {
            color: var(--psi-blue);
            border-bottom: 2px solid #eee;
            padding-bottom: 6px;
            margin-bottom: 12px;
            font-size: 1rem;
        }

        .contract-article h5 {
            color: var(--psi-blue);
            margin-top: 12px;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .signature-pad-container {
            border: 2px dashed #dee2e6;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 10px;
            margin: 20px 0;
        }

        .signature-pad {
            width: 100%;
            height: 200px;
            border-radius: 4px;
            background: white;
            cursor: crosshair;
        }

        .btn-psi {
            background: var(--psi-blue);
            border-color: var(--psi-blue);
            color: white;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-psi:hover {
            background: #08318a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.3);
        }

        .info-badge {
            background: #e8f4f8;
            border-left: 4px solid var(--psi-blue);
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }

        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .success-message {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
            }

            .contract-container {
                box-shadow: none;
                padding: 15px;
                max-height: none;
                overflow: visible;
            }

            .contract-content {
                line-height: 1.4;
                font-size: 0.9rem;
            }

            .contract-article {
                margin-bottom: 15px;
                page-break-inside: avoid;
            }

            .contract-article h4 {
                font-size: 0.95rem;
                margin-bottom: 8px;
            }

            .contract-article h5 {
                font-size: 0.9rem;
                margin-top: 10px;
                margin-bottom: 6px;
            }

            table {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header-psi no-print">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="logo-text">PSI AFRICA</div>
                    <small>Cabinet Conseiller Expert en Immigration</small>
                </div>
                <div class="text-end">
                    <i class="bi bi-shield-check" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <!-- Info du contrat -->
        <div class="info-badge no-print">
            <h5 class="mb-3"><i class="bi bi-file-earmark-text"></i> Informations du Contrat</h5>
            <div class="row">
                <div class="col-md-3">
                    <strong>Numéro :</strong><br>
                    {{ $contract->numero_contrat }}
                </div>
                <div class="col-md-4">
                    <strong>Client :</strong><br>
                    {{ $contract->nom_complet }}
                </div>
                <div class="col-md-3">
                    <strong>Type de Visa :</strong><br>
                    {{ $contract->type_visa }}
                </div>
                <div class="col-md-2">
                    <strong>Montant :</strong><br>
                    {{ number_format($contract->montant_contrat, 0, ',', ' ') }} FCFA
                </div>
            </div>
        </div>

        <!-- Contenu du contrat COMPLET -->
        <div class="contract-container">
            <div class="contract-content">
                <!-- En-tête -->
                <div style="text-align: center; border-bottom: 2px solid #0A2463; padding-bottom: 20px; margin-bottom: 30px;">
                    <h1 style="color: #0A2463; margin-bottom: 5px;">CABINET CONSEILLER EXPERT (CCE)</h1>
                    <h2 style="color: #D4AF37; margin-bottom: 10px;">CONTRAT D'ASSISTANCE ET DE CONSEIL EN PROCÉDURE DE VISA</h2>
                    <p style="color: #666;">Cabinet de conseil spécialisé en immigration légale</p>
                </div>

                <!-- Les parties -->
                <div style="margin-bottom: 20px;">
                    <p><strong>Entre les soussignés :</strong></p>
                    <p><strong>CABINET CONSEILLER EXPERT (CCE)</strong>, Cabinet de conseil spécialisé en immigration légale, immatriculé en Côte d'Ivoire,</p>
                    <p>ci-après désigné <em>« le Cabinet »</em> ou <em>« CCE »</em>,</p>

                    <p><strong>Et :</strong></p>
                    <p><strong>M./Mme : {{ $contract->prenom }} {{ $contract->nom }}</strong></p>
                    <p>Né(e) le : {{ $contract->date_naissance->format('d/m/Y') }} à {{ $contract->lieu_naissance }}</p>
                    <p>Nationalité : {{ $contract->nationalite }}</p>
                    <p>Profession : {{ $contract->profession }}</p>
                    <p>Téléphone : {{ $contract->telephone_mobile }}</p>
                    <p>Adresse e-mail : {{ $contract->email }}</p>
                    <p>ci-après désigné <em>« le Client »</em>.</p>

                    <p>Les deux parties, ci-après désignées collectivement <em>« les Parties »</em>, ont convenu de ce qui suit :</p>
                </div>

                <!-- Article 1 -->
                <div class="contract-article">
                    <h4>Article 1 : Objet du contrat</h4>
                    <p>Le présent contrat a pour objet de définir les conditions dans lesquelles <strong>CCE</strong> assure au Client :</p>
                    <ul>
                        <li>Une <strong>assistance administrative, documentaire et logistique</strong> dans le cadre de sa <strong>demande de visa pour {{ $contract->pays_destination }}</strong> ;</li>
                        <li>Un <strong>accompagnement personnalisé</strong> pour la constitution du dossier, la prise de rendez-vous, la préparation à l'entretien et le suivi de la procédure jusqu'à décision finale.</li>
                    </ul>
                    <p><strong>CCE</strong> agit uniquement comme <strong>cabinet de conseil et d'assistance</strong> et <strong>n'intervient pas dans la décision d'obtention du visa</strong>, celle-ci relevant exclusivement des autorités consulaires.</p>
                </div>

                <!-- Article 2 -->
                <div class="contract-article">
                    <h4>Article 2 : Nature des prestations</h4>
                    <p>Les prestations fournies par le Cabinet comprennent notamment :</p>
                    <ol>
                        <li>L'étude du profil du Client et la définition du type de visa approprié ({{ $contract->type_visa }}) ;</li>
                        <li>L'assistance pour la constitution du dossier (formulaires, pièces, justificatifs, traduction) ;</li>
                        <li>La préparation à l'entretien consulaire (simulation, coaching, conseils personnalisés) ;</li>
                        <li>La prise de rendez-vous et le suivi administratif de la demande ;</li>
                        <li>Le conseil général sur les démarches liées au voyage (hébergement, assurance, billet d'avion, etc.).</li>
                    </ol>
                    <p>Toute prestation non mentionnée dans la liste ci-dessus fera l'objet d'un <strong>avenant</strong> ou d'un <strong>devis séparé</strong>.</p>
                </div>

                <!-- Article 3 -->
                <div class="contract-article">
                    <h4>Article 3 : Modalités financières</h4>
                    <p>Les frais dus à <strong>CCE</strong> comprennent deux catégories :</p>
                    <ul>
                        <li><strong>Frais de cabinet</strong> : couvrant le conseil, le suivi et la gestion du dossier ;</li>
                        <li><strong>Frais d'assistance et d'inscription</strong>, le cas échéant.</li>
                    </ul>

                    <h5>3.1 Montant et conditions de paiement</h5>
                    <p><strong>Montant total des frais :</strong> {{ number_format($contract->montant_contrat, 0, ',', ' ') }} FCFA</p>
                    @if($contract->montant_lettres)
                    <p><em>({{ $contract->montant_lettres }})</em></p>
                    @endif

                    @php
                        $avance = $contract->avance ?? 0;
                        $restePayer = $contract->reste_payer ?? ($contract->montant_contrat - $avance);
                    @endphp

                    @if($avance > 0)
                    <p><strong>✅ Avance versée :</strong> <span style="color: #28a745; font-weight: bold;">{{ number_format($avance, 0, ',', ' ') }} FCFA</span></p>
                    @endif

                    @if($restePayer > 0)
                    <p><strong>⏳ Reste à payer :</strong> <span style="color: #dc3545; font-weight: bold;">{{ number_format($restePayer, 0, ',', ' ') }} FCFA</span></p>
                    @endif

                    @if($contract->mode_paiement)
                    <p><strong>Mode de paiement :</strong> {{ $contract->mode_paiement }}</p>
                    @endif

                    @if($contract->date_echeance)
                    <p><strong>Date d'échéance :</strong> {{ $contract->date_echeance->format('d/m/Y') }}</p>
                    @endif

                    <p style="margin-top: 15px;"><strong>Clause importante :</strong> Tout retard de paiement supérieur à 10 jours entraîne une <strong>suspension du suivi du dossier</strong> jusqu'à régularisation complète.</p>
                </div>

                <!-- Article 4 -->
                <div class="contract-article">
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

                <!-- Article 5 -->
                <div class="contract-article">
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

                <!-- Article 6 -->
                <div class="contract-article">
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

                <!-- Article 7 -->
                <div class="contract-article">
                    <h4>Article 7 : Confidentialité et protection des données</h4>
                    <p><strong>CCE</strong> s'engage à protéger la confidentialité de toutes les informations fournies par le Client et à ne les utiliser que dans le cadre strict de la prestation d'assistance.</p>
                    <p>Aucune donnée ne sera transmise à un tiers sans l'accord écrit du Client.</p>
                </div>

                <!-- Article 8 -->
                <div class="contract-article">
                    <h4>Article 8 : Durée du contrat</h4>
                    <p>Le présent contrat prend effet à la date de signature et reste valable <strong>jusqu'à la fin de la procédure engagée</strong>.</p>
                    <p>Il prend automatiquement fin :</p>
                    <ul>
                        <li>À la délivrance du visa ;</li>
                        <li>Ou à la réception d'une décision de refus ;</li>
                        <li>Ou en cas de résiliation motivée par une faute grave du Client.</li>
                    </ul>
                </div>

                <!-- Article 9 -->
                <div class="contract-article">
                    <h4>Article 9 : Résiliation</h4>
                    <p>En cas de manquement grave par l'une des Parties à ses obligations contractuelles, le contrat pourra être résilié de plein droit après <strong>mise en demeure restée sans effet pendant 7 jours</strong>.</p>
                    <p>Aucune somme déjà versée ne sera remboursée.</p>
                </div>

                <!-- Article 10 -->
                <div class="contract-article">
                    <h4>Article 10 : Clause de non-garantie de résultat</h4>
                    <p>Le Cabinet s'engage à <strong>mettre en œuvre tous les moyens possibles</strong> pour l'aboutissement du dossier, sans pour autant garantir l'obtention du visa.</p>
                    <p>Le Client reconnaît avoir compris que <strong>le résultat dépend de la décision souveraine des autorités consulaires.</strong></p>
                </div>

                <!-- Article 11 -->
                <div class="contract-article">
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

                <!-- Article 12 -->
                <div class="contract-article">
                    <h4>Article 12 : Juridiction compétente</h4>
                    <p>En cas de litige, les Parties s'engagent d'abord à rechercher une solution amiable.</p>
                    <p>À défaut, compétence expresse est attribuée aux tribunaux du ressort du <strong>siège social du Cabinet Conseiller Expert (CCE)</strong>.</p>
                </div>

                <!-- Article 13 -->
                <div class="contract-article">
                    <h4>Article 13 : Acceptation</h4>
                    <p>Le Client déclare avoir lu, compris et accepté sans réserve les termes du présent contrat, ainsi que les <strong>conditions générales d'assistance du Cabinet Conseiller Expert (CCE)</strong> annexées au document.</p>
                </div>

                <!-- Signatures -->
                <div style="margin-top: 50px; border-top: 1px solid #000; padding-top: 20px;">
                    <p><strong>Fait à {{ $contract->lieu_contrat }}, le {{ $contract->date_contrat->format('d/m/Y') }}</strong></p>
                    <p>En deux exemplaires originaux.</p>
                </div>
            </div>
        </div>

        <!-- Section Signature -->
        <div id="signature-section" class="card mt-4 no-print">
            <div class="card-body">
                <h4 class="mb-4"><i class="bi bi-pen"></i> Signature Électronique du Contrat</h4>

                <div class="warning-box mb-4">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Important :</strong> En signant ce contrat, vous acceptez tous les termes et conditions ci-dessus.
                    Votre signature électronique a la même valeur légale qu'une signature manuscrite.
                </div>

                <div class="mb-3">
                    <label for="nom-signataire" class="form-label">Votre Nom Complet *</label>
                    <input type="text" class="form-control" id="nom-signataire"
                           value="{{ $contract->prenom }} {{ $contract->nom }}" required>
                    <small class="text-muted">Veuillez taper "Lu et approuvé" suivi de votre nom</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Votre Signature Électronique *</label>
                    <div class="signature-pad-container">
                        <canvas id="signature-pad" class="signature-pad"></canvas>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSignature()">
                            <i class="bi bi-eraser"></i> Effacer
                        </button>
                        <small class="text-muted">Dessinez votre signature ci-dessus avec votre souris ou doigt</small>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="acceptation" required>
                        <label class="form-check-label" for="acceptation">
                            <strong>Je déclare avoir lu et accepté l'intégralité des termes et conditions de ce contrat</strong>
                        </label>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-psi btn-lg" onclick="submitSignature()">
                        <i class="bi bi-check-circle"></i> Valider et Signer le Contrat
                    </button>
                </div>

                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="bi bi-shield-lock"></i> Signature sécurisée et confidentielle - Ce lien expire après utilisation
                    </small>
                </div>
            </div>
        </div>

        <!-- Message de succès (caché par défaut) -->
        <div id="success-message" class="success-message mt-4" style="display: none;">
            <i class="bi bi-check-circle" style="font-size: 3rem; color: #28a745;"></i>
            <h3 class="mt-3">Contrat Signé avec Succès !</h3>
            <p class="mb-0">Votre contrat <strong id="contract-number"></strong> a été signé le <strong id="signature-date"></strong></p>
            <p class="mt-3">Vous recevrez une copie par email sous peu.</p>
            <p class="mt-3">
                <button class="btn btn-primary me-2" onclick="window.print()">
                    <i class="bi bi-printer"></i> Imprimer le Contrat
                </button>
                <button class="btn btn-secondary" onclick="closeOrGoBack()">
                    <i class="bi bi-x-circle"></i> Fermer
                </button>
            </p>
        </div>
    </div>

    <script>
        // Configuration
        const token = '{{ $token }}';
        const csrfToken = '{{ csrf_token() }}';
        let signaturePad;

        // Initialisation de la signature pad
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('signature-pad');
            signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)'
            });

            // Redimensionner le canvas
            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);
        });

        function resizeCanvas() {
            const canvas = document.getElementById('signature-pad');
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
        }

        function clearSignature() {
            signaturePad.clear();
        }

        async function submitSignature() {
            // Validation
            if (!document.getElementById('acceptation').checked) {
                alert('Veuillez accepter les termes et conditions du contrat');
                return;
            }

            const nomSignataire = document.getElementById('nom-signataire').value.trim();
            if (!nomSignataire) {
                alert('Veuillez saisir votre nom complet');
                return;
            }

            if (signaturePad.isEmpty()) {
                alert('Veuillez apposer votre signature');
                return;
            }

            // Désactiver le bouton pendant le traitement
            const btn = document.querySelector('button[onclick="submitSignature()"]');
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Signature en cours...';

            try {
                // Envoi au serveur
                const response = await fetch(`/signature/${token}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        signature: signaturePad.toDataURL(),
                        nom_signataire: nomSignataire,
                        acceptation: 1
                    })
                });

                const result = await response.json();

                if (result.success) {
                    // Afficher le message de succès
                    document.getElementById('signature-section').style.display = 'none';
                    document.getElementById('contract-number').textContent = result.contract.numero_contrat;
                    document.getElementById('signature-date').textContent = result.contract.date_signature;
                    document.getElementById('success-message').style.display = 'block';

                    // Scroll vers le message
                    document.getElementById('success-message').scrollIntoView({ behavior: 'smooth' });
                } else {
                    alert('Erreur: ' + result.message);
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                }
            } catch (error) {
                console.error('Erreur complète:', error);
                let errorMessage = 'Une erreur est survenue lors de la signature.';

                // Afficher plus de détails sur l'erreur pour le débogage
                if (error.message) {
                    errorMessage += '\nDétails: ' + error.message;
                }

                alert(errorMessage + '\nVeuillez réessayer ou contacter le support.');
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            }
        }

        /**
         * Fonction pour fermer la fenêtre ou revenir en arrière
         */
        function closeOrGoBack() {
            // Essayer de fermer la fenêtre (fonctionne si ouverte par JavaScript)
            if (window.opener) {
                window.close();
            } else {
                // Sinon, retourner à la page précédente
                if (document.referrer) {
                    window.history.back();
                } else {
                    // Si pas d'historique, rediriger vers l'accueil
                    window.location.href = '/';
                }
            }
        }
    </script>
</body>
</html>
