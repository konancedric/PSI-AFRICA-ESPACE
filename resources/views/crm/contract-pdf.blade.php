<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Contrat {{ $contract->numero_contrat }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            line-height: 1.6;
            padding: 20px;
            color: #333;
            font-size: 11pt;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #0066cc;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #0066cc;
            font-size: 24pt;
            margin-bottom: 10px;
        }
        .contract-number {
            font-size: 14pt;
            color: #666;
            font-weight: bold;
        }
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: #0066cc;
            color: white;
            padding: 8px 12px;
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 8px;
            background-color: #f5f5f5;
            border-left: 4px solid #0066cc;
            vertical-align: top;
        }
        .info-table tr td:first-child {
            width: 50%;
            padding-right: 5px;
        }
        .info-table tr td:last-child {
            width: 50%;
            padding-left: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #0066cc;
            font-size: 10pt;
            text-transform: uppercase;
            display: block;
            margin-bottom: 3px;
        }
        .info-value {
            font-size: 12pt;
            color: #333;
        }
        .signature-section {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 2px solid #0066cc;
            page-break-inside: avoid;
        }
        .signature-image {
            max-width: 250px;
            max-height: 120px;
            border: 1px solid #ddd;
            padding: 5px;
            background-color: white;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10pt;
            color: #666;
            border-top: 2px solid #ddd;
            padding-top: 15px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            background-color: #28a745;
            color: white;
            font-weight: bold;
            font-size: 12pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CONTRAT DE SERVICE</h1>
        <div class="contract-number">N° {{ $contract->numero_contrat }}</div>
        <div style="margin-top: 10px;">
            <span class="status-badge">{{ $contract->statut }}</span>
        </div>
    </div>

    <!-- Informations Client -->
    <div class="section">
        <div class="section-title">INFORMATIONS CLIENT</div>
        <table class="info-table">
            <tr>
                <td>
                    <span class="info-label">Nom complet</span>
                    <span class="info-value">{{ $contract->prenom }} {{ $contract->nom }}</span>
                </td>
                <td>
                    <span class="info-label">Date de naissance</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($contract->date_naissance)->format('d/m/Y') }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="info-label">Lieu de naissance</span>
                    <span class="info-value">{{ $contract->lieu_naissance }}</span>
                </td>
                <td>
                    <span class="info-label">Nationalité</span>
                    <span class="info-value">{{ $contract->nationalite }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="info-label">Profession</span>
                    <span class="info-value">{{ $contract->profession }}</span>
                </td>
                <td>
                    <span class="info-label">Téléphone</span>
                    <span class="info-value">{{ $contract->telephone_mobile }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="info-label">Email</span>
                    <span class="info-value">{{ $contract->email }}</span>
                </td>
                <td>
                    <span class="info-label">Adresse</span>
                    <span class="info-value">{{ $contract->adresse }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Informations Visa -->
    <div class="section">
        <div class="section-title">INFORMATIONS VISA</div>
        <table class="info-table">
            <tr>
                <td>
                    <span class="info-label">Pays de destination</span>
                    <span class="info-value">{{ $contract->pays_destination }}</span>
                </td>
                <td>
                    <span class="info-label">Type de visa</span>
                    <span class="info-value">{{ $contract->type_visa }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Informations Financières -->
    <div class="section">
        <div class="section-title">INFORMATIONS FINANCIERES</div>
        <table class="info-table">
            <tr>
                <td>
                    <span class="info-label">Montant total</span>
                    <span class="info-value" style="font-size: 14pt; font-weight: bold; color: #28a745;">
                        {{ number_format($contract->montant_contrat, 0, ',', ' ') }} FCFA
                    </span>
                </td>
                <td>
                    <span class="info-label">Mode de paiement</span>
                    <span class="info-value">{{ $contract->mode_paiement }}</span>
                </td>
            </tr>
            @if($contract->avance > 0)
            <tr>
                <td>
                    <span class="info-label">Avance versée</span>
                    <span class="info-value">{{ number_format($contract->avance, 0, ',', ' ') }} FCFA</span>
                </td>
                <td>
                    <span class="info-label">Reste à payer</span>
                    <span class="info-value">{{ number_format($contract->reste_payer, 0, ',', ' ') }} FCFA</span>
                </td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Signature -->
    @if($contract->signature)
    <div class="signature-section">
        <div class="section-title">SIGNATURE</div>
        <div style="margin-bottom: 12px;">
            <span class="info-label">Signé par</span><br>
            <span class="info-value">{{ $contract->nom_signataire }}</span>
        </div>
        <div style="margin-bottom: 12px;">
            <span class="info-label">Date de signature</span><br>
            <span class="info-value">{{ \Carbon\Carbon::parse($contract->date_signature)->format('d/m/Y à H:i') }}</span>
        </div>
        <div>
            <span class="info-label">Signature</span><br>
            <img src="{{ $contract->signature }}" alt="Signature" class="signature-image">
        </div>
    </div>
    @endif

    <!-- Observations -->
    @if($contract->observations)
    <div class="section">
        <div class="section-title">OBSERVATIONS</div>
        <div style="padding: 12px; background-color: #f5f5f5;">
            {{ $contract->observations }}
        </div>
    </div>
    @endif

    <div class="footer">
        <p><strong>PSI AFRICA</strong></p>
        <p>Document généré le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}</p>
        <p>Contrat N° {{ $contract->numero_contrat }} - Date du contrat: {{ \Carbon\Carbon::parse($contract->date_contrat)->format('d/m/Y') }}</p>
    </div>
</body>
</html>
