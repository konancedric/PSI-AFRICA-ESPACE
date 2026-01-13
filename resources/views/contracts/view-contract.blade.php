<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrat {{ $contract->numero_contrat }} - CCE</title>
    <link rel="icon" href="{{ asset('favicon.png') }}"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
            padding: 20px;
        }

        .container-wrapper {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .action-bar {
            background: linear-gradient(135deg, #0A2463 0%, #1e3a8a 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .action-bar h5 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-custom {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-download {
            background: #D4AF37;
            color: white;
        }

        .btn-download:hover {
            background: #b8951f;
            transform: translateY(-2px);
            color: white;
        }

        .btn-print {
            background: white;
            color: #0A2463;
        }

        .btn-print:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
        }

        .contrat-content {
            padding: 40px;
        }

        .contrat-article {
            margin-bottom: 30px;
        }

        .contrat-article h4 {
            color: #0A2463;
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 2px solid #D4AF37;
            padding-bottom: 5px;
        }

        .contrat-article h5 {
            color: #0A2463;
            font-weight: 600;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .contrat-article p {
            margin-bottom: 10px;
            text-align: justify;
        }

        .contrat-article ul, .contrat-article ol {
            margin-left: 20px;
            margin-bottom: 15px;
        }

        .contrat-article li {
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #0A2463;
        }

        strong {
            color: #0A2463;
        }

        em {
            color: #666;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .container-wrapper {
                box-shadow: none;
                border-radius: 0;
            }

            .action-bar {
                display: none !important;
            }

            .contrat-content {
                padding: 20px;
            }
        }

        @media (max-width: 768px) {
            .contrat-content {
                padding: 20px;
            }

            .action-bar {
                flex-direction: column;
                text-align: center;
            }

            .action-buttons {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container-wrapper">
        <!-- Barre d'actions -->
        <div class="action-bar">
            <h5><i class="bi bi-file-earmark-text"></i> Contrat {{ $contract->numero_contrat }}</h5>
            <div class="action-buttons">
                <a href="{{ route('contracts.download-pdf', $contract->view_token) }}" class="btn-custom btn-download">
                    <i class="bi bi-download"></i>
                    Télécharger PDF
                </a>
                <button onclick="window.print()" class="btn-custom btn-print">
                    <i class="bi bi-printer"></i>
                    Imprimer
                </button>
            </div>
        </div>

        <!-- Contenu du contrat -->
        <div class="contrat-content">
            <!-- En-tête du contrat -->
            <div style="text-align: center; border-bottom: 2px solid #0A2463; padding-bottom: 20px; margin-bottom: 30px;">
                <h1 style="color: #0A2463; margin-bottom: 5px;">CABINET CONSEILLER EXPERT (CCE)</h1>
                <h2 style="color: #D4AF37; margin-bottom: 10px;">CONTRAT D'ASSISTANCE ET DE CONSEIL EN PROCÉDURE DE VISA</h2>
                <p style="color: #666;">Cabinet de conseil spécialisé en immigration légale</p>
            </div>

            <!-- Parties contractantes -->
            <div style="margin-bottom: 20px;">
                <p><strong>Entre les soussignés :</strong></p>
                <p><strong>CABINET CONSEILLER EXPERT (CCE)</strong>, Cabinet de conseil spécialisé en immigration légale, immatriculé en Côte d'Ivoire,</p>
                <p>ci-après désigné <em>« le Cabinet »</em> ou <em>« CCE »</em>,</p>

                <p><strong>Et :</strong></p>
                <p><strong>M./Mme : {{ $contract->prenom }} {{ $contract->nom }}</strong></p>
                <p>Né(e) le : {{ $contract->date_naissance ? $contract->date_naissance->format('d/m/Y') : 'N/A' }}</p>
                <p>Profession : {{ $contract->profession ?? 'Non renseigné' }}</p>
                <p>Téléphone : {{ $contract->telephone_mobile ?? 'N/A' }}</p>
                <p>Adresse e-mail : {{ $contract->email ?? 'N/A' }}</p>
                <p>ci-après désigné <em>« le Client »</em>.</p>

                <p>Les deux parties, ci-après désignées collectivement <em>« les Parties »</em>, ont convenu de ce qui suit :</p>
            </div>

            <!-- Article 1 -->
            <div class="contrat-article">
                <h4>Article 1 : Objet du contrat</h4>
                <p>Le présent contrat a pour objet de définir les conditions dans lesquelles <strong>CCE</strong> assure au Client :</p>
                <ul>
                    <li>Une <strong>assistance administrative, documentaire et logistique</strong> dans le cadre de sa <strong>demande de visa</strong> ;</li>
                    <li>Un <strong>accompagnement personnalisé</strong> pour la constitution du dossier, la prise de rendez-vous, la préparation à l'entretien et le suivi de la procédure jusqu'à décision finale.</li>
                </ul>
                <p><strong>CCE</strong> agit uniquement comme <strong>cabinet de conseil et d'assistance</strong> et <strong>n'intervient pas dans la décision d'obtention du visa</strong>, celle-ci relevant exclusivement des autorités consulaires.</p>
            </div>

            <!-- Article 2 -->
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

            <!-- Article 3 : Modalités financières -->
            <div class="contrat-article">
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

                @if($contract->avance > 0)
                <p><strong>✅ Avance versée :</strong> <span style="color: #28a745; font-weight: bold;">{{ number_format($contract->avance, 0, ',', ' ') }} FCFA</span></p>
                @endif

                @if($contract->reste_payer > 0)
                <p><strong>⏳ Reste à payer :</strong> <span style="color: #dc3545; font-weight: bold;">{{ number_format($contract->reste_payer, 0, ',', ' ') }} FCFA</span></p>
                @endif

                @if($contract->mode_paiement)
                <p><strong>Mode de paiement :</strong> {{ $contract->mode_paiement }}</p>
                @endif

                <p style="margin-top: 15px;"><strong>Clause importante :</strong> Tout retard de paiement supérieur à 10 jours entraîne une <strong>suspension du suivi du dossier</strong> jusqu'à régularisation complète.</p>
            </div>

            <!-- Article 4 -->
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

            <!-- Article 5 -->
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

            <!-- Article 6 -->
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

            <!-- Article 7 -->
            <div class="contrat-article">
                <h4>Article 7 : Confidentialité et protection des données</h4>
                <p><strong>CCE</strong> s'engage à protéger la confidentialité de toutes les informations fournies par le Client et à ne les utiliser que dans le cadre strict de la prestation d'assistance.</p>
                <p>Aucune donnée ne sera transmise à un tiers sans l'accord écrit du Client.</p>
            </div>

            <!-- Article 8 -->
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

            <!-- Article 9 -->
            <div class="contrat-article">
                <h4>Article 9 : Résiliation</h4>
                <p>En cas de manquement grave par l'une des Parties à ses obligations contractuelles, le contrat pourra être résilié de plein droit après <strong>mise en demeure restée sans effet pendant 7 jours</strong>.</p>
                <p>Aucune somme déjà versée ne sera remboursée.</p>
            </div>

            <!-- Article 10 -->
            <div class="contrat-article">
                <h4>Article 10 : Clause de non-garantie de résultat</h4>
                <p>Le Cabinet s'engage à <strong>mettre en œuvre tous les moyens possibles</strong> pour l'aboutissement du dossier, sans pour autant garantir l'obtention du visa.</p>
                <p>Le Client reconnaît avoir compris que <strong>le résultat dépend de la décision souveraine des autorités consulaires.</strong></p>
            </div>

            <!-- Article 11 -->
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

            <!-- Article 12 -->
            <div class="contrat-article">
                <h4>Article 12 : Juridiction compétente</h4>
                <p>En cas de litige, les Parties s'engagent d'abord à rechercher une solution amiable.</p>
                <p>À défaut, compétence expresse est attribuée aux tribunaux du ressort du <strong>siège social du Cabinet Conseiller Expert (CCE)</strong>.</p>
            </div>

            <!-- Article 13 -->
            <div class="contrat-article">
                <h4>Article 13 : Acceptation</h4>
                <p>Le Client déclare avoir lu, compris et accepté sans réserve les termes du présent contrat, ainsi que les <strong>conditions générales d'assistance du Cabinet Conseiller Expert (CCE)</strong> annexées au document.</p>
            </div>

            <!-- Signatures -->
            <div style="margin-top: 50px; border-top: 1px solid #000; padding-top: 20px;">
                <table style="width: 100%; border: none;">
                    <tr>
                        <td style="width: 50%; vertical-align: top; text-align: center; border: none; padding: 20px;">
                            <strong>Le Client</strong><br><br>
                            {{ $contract->prenom }} {{ $contract->nom }}<br><br>

                            @if($contract->signature)
                                <div style="margin: 10px 0;">
                                    <img src="{{ $contract->signature }}" alt="Signature" style="max-width: 200px; border: 0px solid #ddd; padding: 20px; background: white;">
                                </div>
                                <p style="margin-top: 5px; font-size: 0.9em; color: #666;">
                                    Signé le {{ $contract->date_signature ? $contract->date_signature->format('d/m/Y à H:i') : '' }}
                                    @if($contract->nom_signataire)
                                        <br>Par: {{ $contract->nom_signataire }}
                                    @endif
                                </p>
                            @else
                                _________________________<br>
                                <em>Signature précédée de la mention « Lu et approuvé »</em>
                            @endif
                        </td>
                        <td style="width: 50%; vertical-align: top; text-align: center; border: none; padding: 20px;">
                            <strong>CABINET CONSEILLER EXPERT (CCE)</strong><br><br>
                            {{ $contract->conseiller ?? 'Le Responsable' }}<br><br>
                            <div style="margin: 10px 0;">
                                <img src="/img/cachet-cce.jpg" alt="Cachet CCE" style="max-width: 180px; height: auto;" onerror="this.style.display='none'">
                            </div>
                            <em style="font-size: 0.85em; color: #666;">Cachet et signature du Cabinet</em>
                        </td>
                    </tr>
                </table>

                <div style="text-align: center; margin-top: 30px;">
                    <p><strong>Fait à {{ $contract->lieu_contrat ?? 'Abidjan' }}, le {{ $contract->date_contrat ? $contract->date_contrat->format('d/m/Y') : 'N/A' }}</strong></p>
                    <p>En deux exemplaires originaux.</p>
                </div>
            </div>

            <!-- Mention légale -->
            <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px; font-size: 0.9em;">
                <p><strong>Mention légale :</strong> Document officiel généré électroniquement - Toute modification rend ce document nul.</p>
                <p>Contrat généré le {{ now()->format('d/m/Y') }} à {{ now()->format('H:i') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
