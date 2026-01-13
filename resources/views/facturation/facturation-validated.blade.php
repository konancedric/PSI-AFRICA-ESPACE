<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PSI AFRICA - Facture {{ $invoice->number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 30px;
            border-radius: 15px 15px 0 0;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .logo {
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .tagline {
            font-style: italic;
            opacity: 0.9;
        }

        .action-bar {
            background: white;
            padding: 20px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-download {
            background: #28a745;
            color: white;
        }

        .btn-download:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .btn-print {
            background: #17a2b8;
            color: white;
        }

        .btn-print:hover {
            background: #138496;
            transform: translateY(-2px);
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #c3e6cb;
        }

        .invoice-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .company-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #1e3c72;
        }

        .company-name {
            font-size: 1.8em;
            font-weight: bold;
            color: #1e3c72;
            margin-bottom: 10px;
        }

        .invoice-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
        }

        .client-info, .invoice-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
        }

        .info-group {
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .info-label {
            font-weight: bold;
            color: #1e3c72;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        th {
            background: #1e3c72;
            color: white;
            padding: 12px;
            text-align: left;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .total-row {
            background: #f0f8ff;
            font-weight: bold;
            font-size: 1.1em;
        }

        .signature-area {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #e0e0e0;
        }

        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }

        .signature-box {
            text-align: center;
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            background: #f8f9fa;
        }

        .signature-box h4 {
            color: #1e3c72;
            margin-bottom: 15px;
        }

        .signature-image {
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .signature-image img {
            max-height: 70px;
        }

        .validation-badge {
            display: inline-block;
            background: #d4edda;
            color: #155724;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            margin-top: 10px;
        }

        .footer {
            background: #2c3e50;
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
        }

        .contact-info {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 15px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .header, .action-bar, .success-message, .footer {
                display: none;
            }

            .invoice-container {
                box-shadow: none;
                border: 1px solid #000;
            }
        }

        @media (max-width: 768px) {
            .invoice-info, .signatures {
                grid-template-columns: 1fr;
            }

            .action-bar {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <div class="logo">PSI AFRICA</div>
            <div class="tagline">Cabinet conseil en immigration légale et en mobilité internationale</div>
        </div>

        <!-- Barre d'actions -->
        <div class="action-bar">
            <button class="btn btn-download" onclick="downloadPDF()">
                <span>📥</span> Télécharger PDF
            </button>
            <button class="btn btn-print" onclick="window.print()">
                <span>🖨️</span> Imprimer
            </button>
        </div>

        <!-- Message de succès -->
        <div class="success-message">
            <h3 style="margin-bottom: 10px;">✅ Facture validée avec succès !</h3>
            <p>Votre facture a été validée le {{ $invoice->client_validated_at->format('d/m/Y à H:i') }}</p>
            <div class="validation-badge">Document officiel PSI AFRICA</div>
        </div>

        <!-- Facture -->
        <div class="invoice-container" id="invoice-content">
            <div class="company-header">
                <div class="company-name">FACTURE PSI AFRICA</div>
                <div>PSI AFRICA INTERNATIONAL</div>
                <div>Cabinet conseil en immigration légale et en mobilité internationale</div>
                <div style="margin-top: 10px; color: #28a745; font-weight: bold;">✅ FACTURE VALIDÉE</div>
            </div>

            <div class="invoice-info">
                <div class="invoice-details">
                    <h4 style="color: #1e3c72; margin-bottom: 15px;">Informations Facture</h4>
                    <div class="info-group"><span class="info-label">Numéro :</span> {{ $invoice->number }}</div>
                    <div class="info-group"><span class="info-label">Date d'émission :</span> {{ $invoice->issue_date ? $invoice->issue_date->format('d/m/Y') : date('d/m/Y') }}</div>
                    <div class="info-group"><span class="info-label">Date d'échéance :</span> {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'N/A' }}</div>
                    <div class="info-group"><span class="info-label">Référence PSI :</span> {{ $client->uid ?? 'N/A' }}</div>
                </div>

                <div class="client-info">
                    <h4 style="color: #1e3c72; margin-bottom: 15px;">Client</h4>
                    <div class="info-group"><span class="info-label">Nom :</span> {{ $client->nom ?? 'N/A' }} {{ $client->prenoms ?? '' }}</div>
                    <div class="info-group"><span class="info-label">Contact :</span> {{ $client->contact ?? 'N/A' }}</div>
                    <div class="info-group"><span class="info-label">Email :</span> {{ $client->email ?? 'N/A' }}</div>
                    <div class="info-group"><span class="info-label">Conseiller :</span> {{ $invoice->user->name ?? $invoice->agent ?? 'N/A' }}</div>
                </div>
            </div>

            <h3 style="color: #1e3c72; margin-bottom: 15px;">Détail des Prestations</h3>
            <table>
                <thead>
                    <tr>
                        <th>Désignation</th>
                        <th>Quantité</th>
                        <th>Prix unitaire (FCFA)</th>
                        <th>Total (FCFA)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $invoice->service }}</td>
                        <td>1</td>
                        <td>{{ number_format($invoice->amount, 0, ',', ' ') }}</td>
                        <td>{{ number_format($invoice->amount, 0, ',', ' ') }}</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3" style="text-align: right;"><strong>TOTAL TTC</strong></td>
                        <td><strong>{{ number_format($invoice->amount, 0, ',', ' ') }} FCFA</strong></td>
                    </tr>
                </tbody>
            </table>

            @if($invoice->notes)
            <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107;">
                <strong>Notes :</strong> {{ $invoice->notes }}
            </div>
            @endif

            <div class="signature-area">
                <h3 style="color: #1e3c72; text-align: center; margin-bottom: 20px;">Validation et Certification</h3>

                <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 10px; margin-bottom: 20px;">
                    <div style="margin-bottom: 15px;">
                        <h4 style="color: #1e3c72; margin-bottom: 10px;">✅ Facture Validée par le Client</h4>
                        <p><strong>{{ $client->nom ?? '' }} {{ $client->prenoms ?? '' }}</strong></p>
                        <p style="font-size: 0.9em; color: #6c757d;">
                            Validée le {{ $invoice->client_validated_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>
                </div>

                <div class="signatures">
                    <div class="signature-box">
                        <h4>PSI AFRICA INTERNATIONAL</h4>
                        <div class="signature-image">
                            <div style="text-align: center;">
                                <div style="font-weight: bold; font-size: 1.2em; color: #1e3c72;">PSI AFRICA</div>
                                <div style="font-size: 0.9em; color: #6c757d;">Cachet et signature</div>
                            </div>
                        </div>
                        <p><strong>{{ $invoice->user->name ?? $invoice->agent ?? 'Agent PSI AFRICA' }}</strong></p>
                        <p style="font-size: 0.9em; color: #6c757d;">
                            Document certifié conforme
                        </p>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
                    <p style="color: #6c757d; font-style: italic;">
                        ✅ Ce document a été validé électroniquement et fait foi.
                    </p>
                </div>
            </div>
        </div>

        <!-- Pied de page -->
        <div class="footer">
            <p><strong>Merci de votre confiance</strong></p>
            <p style="margin: 15px 0; font-style: italic;">PSI AFRICA INTERNATIONAL<br>Votre Partenaire en Mobilité Internationale</p>

            <div class="contact-info">
                <div class="contact-item">📍 Angré Terminus 81/82</div>
                <div class="contact-item">📞 +225 01 04 04 04 05</div>
                <div class="contact-item">📧 infos@psiafrica.com</div>
                <div class="contact-item">🌐 www.psiafrica.com</div>
            </div>
        </div>
    </div>

    <script>
        function downloadPDF() {
            // Dans une application réelle, cette fonction appellerait une route pour générer un PDF
            alert('Le téléchargement du PDF sera disponible prochainement.\n\nPour l\'instant, utilisez le bouton "Imprimer" puis "Enregistrer au format PDF".');

            // Alternative : déclencher l'impression avec option PDF
            window.print();
        }

        // Empêcher les clics droits sur la facture (protection basique)
        document.getElementById('invoice-content').addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });
    </script>
</body>
</html>
