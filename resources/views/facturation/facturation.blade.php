@php
    // Définir $invoice pour compatibilité avec le reste du code
    $invoice = $currentInvoice ?? $invoice ?? null;
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PSI AFRICA - Validation de Facture et Signature de Reçu</title>
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
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 30px;
            border-radius: 15px 15px 0 0;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin: 0 auto;
            max-width: 1200px;
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

        .client-message {
            background: white;
            padding: 30px;
            border-radius: 0 0 15px 15px;
            margin: 0 auto 20px auto;
            max-width: 1200px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            line-height: 1.6;
        }

        .documents-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px auto;
            max-width: 1200px;
        }

        @media (max-width: 768px) {
            .documents-section {
                grid-template-columns: 1fr;
            }
        }

        .document-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            text-align: center;
            position: relative;
        }

        .document-card.locked {
            opacity: 0.6;
            background: #f8f9fa;
        }

        .lock-icon {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 1.5em;
            color: #dc3545;
        }

        .document-card:hover {
            transform: translateY(-5px);
        }

        .document-icon {
            font-size: 3em;
            text-align: center;
            margin-bottom: 15px;
        }

        .document-title {
            color: #1e3c72;
            font-size: 1.4em;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .btn-view {
            display: inline-block;
            background: #1e3c72;
            color: white;
            padding: 12px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            margin: 10px 5px;
            transition: background 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-view:hover {
            background: #2a5298;
        }

        .btn-view:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }

        .features-list {
            list-style: none;
            margin: 20px 0;
            text-align: left;
        }

        .features-list li {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
            position: relative;
            padding-left: 25px;
        }

        .features-list li:before {
            content: "✓";
            color: #28a745;
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        .benefits-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin: 20px auto;
            max-width: 1200px;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .benefits-grid {
                grid-template-columns: 1fr;
            }
        }

        .benefit-item {
            text-align: center;
            padding: 20px;
        }

        .benefit-icon {
            font-size: 2.5em;
            margin-bottom: 15px;
        }

        .next-steps {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin: 20px auto;
            max-width: 1200px;
        }

        .btn {
            display: inline-block;
            background: #1e3c72;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 15px;
            transition: background 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background: #2a5298;
        }

        .contact-footer {
            background: #2c3e50;
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin: 20px auto;
            max-width: 1200px;
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

        /* Styles pour la prévisualisation des documents */
        .preview-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin: 20px auto;
            max-width: 1200px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            display: none;
        }

        .preview-title {
            color: #1e3c72;
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.8em;
        }

        .document-preview {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 25px;
            margin: 20px 0;
            background: #fafafa;
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
            margin: 20px 0;
        }

        .client-info, .invoice-details {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .info-group {
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: bold;
            color: #1e3c72;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
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
        }

        .conditions {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px dashed #ccc;
        }

        .signature-area {
            margin-top: 30px;
            text-align: center;
            padding: 20px;
            border: 1px dashed #ccc;
            border-radius: 8px;
        }

        .payment-status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            margin-left: 10px;
        }

        .status-paid {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 20px 0;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .download-btn {
            background: #28a745;
            color: white;
        }

        .download-btn:hover {
            background: #218838;
        }

        .print-btn {
            background: #17a2b8;
            color: white;
        }

        .print-btn:hover {
            background: #138496;
        }

        .email-btn {
            background: #dc3545;
            color: white;
        }

        .email-btn:hover {
            background: #c82333;
        }

        .close-btn {
            background: #6c757d;
            color: white;
            margin-top: 10px;
        }

        .close-btn:hover {
            background: #545b62;
        }

        .sign-btn {
            background: #ffc107;
            color: #212529;
        }

        .sign-btn:hover {
            background: #e0a800;
        }

        .validate-btn {
            background: #28a745;
            color: white;
        }

        .validate-btn:hover {
            background: #218838;
        }

        .active-preview {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Styles pour la validation de facture */
        .validation-section {
            background: #e7f3ff;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #1e3c72;
        }

        .checkbox-group {
            margin: 15px 0;
            text-align: left;
        }

        .checkbox-group label {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .checkbox-group input[type="checkbox"] {
            margin-top: 3px;
        }

        .signature-pad {
            border: 2px dashed #ccc;
            border-radius: 5px;
            margin: 20px 0;
            background: white;
            cursor: crosshair;
        }

        .signature-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 15px;
        }

        .signature-clear {
            background: #6c757d;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 3px;
            cursor: pointer;
        }

        .status-indicator {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8em;
            font-weight: bold;
            margin-left: 10px;
        }

        .status-waiting {
            background: #fff3cd;
            color: #856404;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            position: relative;
        }

        .progress-steps:before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e0e0e0;
            z-index: 1;
        }

        .progress-step {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .step-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
        }

        .step-icon.active {
            background: #1e3c72;
            color: white;
        }

        .step-icon.completed {
            background: #28a745;
            color: white;
        }

        .step-label {
            font-size: 0.9em;
            font-weight: bold;
        }

        /* Styles des boutons d'actions */
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .action-btn:active {
            transform: translateY(0);
        }

        /* Styles pour l'impression */
        /* Styles pour les cartes d'historique */
        .invoice-card {
            transition: all 0.3s ease;
        }

        .invoice-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
        }

        .invoice-card:active {
            transform: translateY(-1px);
        }

        /* Responsive pour les menus déroulants */
        @media (max-width: 768px) {
            .history-grid {
                grid-template-columns: 1fr !important;
            }

            /* Responsive pour la section de reçu détaillé */
            #receipt-detail-section {
                padding: 15px !important;
                margin: 10px 0 !important;
            }

            #receipt-detail-section > div[style*="grid-template-columns"] {
                grid-template-columns: 1fr !important;
            }

            #receipt-detail-section h2 {
                font-size: 1.4em !important;
            }

            #receipt-detail-section h3 {
                font-size: 1em !important;
            }

            #receipt-actions {
                flex-direction: column !important;
            }

            #receipt-actions button {
                width: 100% !important;
            }

            #payment-amount-display {
                font-size: 1.1em !important;
            }
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .header,
            .client-message,
            .documents-section,
            .action-buttons,
            .close-btn,
            .footer {
                display: none !important;
            }

            .preview-section {
                display: block !important;
                position: static !important;
                background: white !important;
                padding: 20px;
            }

            .preview-section#invoice-preview {
                display: block !important;
            }

            .document-preview {
                box-shadow: none;
                border: 1px solid #000;
                page-break-inside: avoid;
            }

            .company-header {
                border-bottom: 2px solid #000;
                margin-bottom: 20px;
            }

            table {
                page-break-inside: avoid;
            }

            button {
                display: none !important;
            }

            .validation-section .action-buttons {
                display: none !important;
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

        @if($invoice->isValidatedByClient())
        <!-- Barre d'actions pour facture validée -->
        <div style="background: white; padding: 15px; border-radius: 0 0 15px 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 20px; display: flex; justify-content: center; gap: 15px;">
            <button class="action-btn" onclick="downloadInvoicePDF()" style="background: #28a745; padding: 12px 25px; border: none; border-radius: 25px; color: white; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.3s;">
                <span>📥</span> Télécharger PDF
            </button>
            <button class="action-btn" onclick="printInvoice()" style="background: #17a2b8; padding: 12px 25px; border: none; border-radius: 25px; color: white; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.3s;">
                <span>🖨️</span> Imprimer
            </button>
        </div>
        @endif

        <!-- Message au client -->
        <div class="client-message">
            <h2 style="color: #1e3c72; margin-bottom: 15px;">Cher(e) {{ $client->nom ?? 'Client' }} {{ $client->prenoms ?? '' }},</h2>
            <p>Chez <strong>PSI AFRICA INTERNATIONAL</strong>, nous mettons un point d'honneur à allier <strong>professionnalisme</strong> et <strong>transparence</strong> dans toutes nos interactions. Votre satisfaction et votre confiance sont au cœur de notre engagement.</p>
            <p style="margin-top: 15px;">Pour finaliser votre dossier, nous vous invitons à suivre le processus de validation en deux étapes :</p>

            <div class="progress-steps">
                <div class="progress-step">
                    <div id="step1-icon" class="step-icon active">1</div>
                    <div class="step-label">Validation de la Facture</div>
                </div>
                <div class="progress-step">
                    <div id="step2-icon" class="step-icon">2</div>
                    <div class="step-label">Signature du Reçu</div>
                </div>
                <div class="progress-step">
                    <div id="step3-icon" class="step-icon">3</div>
                    <div class="step-label">Reçu Définitif</div>
                </div>
            </div>
        </div>

        <!-- Section des documents -->
        <div class="documents-section">
            <!-- Facture -->
            <div>
                @if(isset($allInvoices) && $allInvoices->count() >= 1)
                <div style="background: #f8f9fa; padding: 10px 15px; margin: 10px 0; border-radius: 6px; border-left: 3px solid #2a5298;">
                    <label for="invoice-selector" style="display: block; font-weight: 600; color: #1e3c72; margin-bottom: 8px; font-size: 0.9em;">
                        📋 Sélectionner une facture
                        <span style="background: #2a5298; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.7em; margin-left: 5px;">{{ $allInvoices->count() }}</span>
                    </label>
                    <select id="invoice-selector"
                            style="width: 100%; padding: 8px 12px; border: 1px solid #2a5298; border-radius: 5px; font-size: 0.9rem; background: white; cursor: pointer;"
                            onchange="changeInvoice(this)">
                        <option value="" disabled>-- Choisissez une facture --</option>
                        @foreach($allInvoices as $inv)
                            @php
                                $serviceShort = strlen($inv->service) > 40 ? substr($inv->service, 0, 40) . '...' : $inv->service;
                                $statusText = '';
                                if($inv->receipt_signed_at) {
                                    $statusText = ' ✅';
                                } elseif($inv->client_validated_at) {
                                    $statusText = ' ✅';
                                } else {
                                    $statusText = ' ⏳';
                                }
                            @endphp
                            <option value="{{ $inv->id }}"
                                    {{ $inv->id == $currentInvoice->id ? 'selected' : '' }}
                                    data-token="{{ $inv->view_token }}">
                                {{ $inv->number }} - {{ $serviceShort }} ({{ number_format($inv->amount, 0, ',', ' ') }} F){{ $statusText }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="document-card">
                <div class="document-icon">📄</div>
                <div class="document-title">Votre Facture Électronique<br>N° {{ $invoice->number }}</div>
                <p>Veuillez valider votre facture en cochant les cases ci-dessous avant de pouvoir accéder au reçu de paiement.</p>

                <ul class="features-list">
                    <li>Description claire et transparente des prestations</li>
                    <li>Détail des coûts sans surprise</li>
                    <li>Conditions générales et modalités de paiement</li>
                    <li>Validation requise avant signature du reçu</li>
                </ul>

                @if($invoice->isValidatedByClient())
                    <button class="btn-view" onclick="showPreview('invoice-preview')">Voir la Facture</button>
                    <div class="status-indicator status-completed" id="invoice-status">
                        ✅ Validée le {{ $invoice->client_validated_at->format('d/m/Y à H:i') }}
                    </div>
                @else
                    <button class="btn-view" onclick="showPreview('invoice-preview')">Valider la Facture</button>
                    <div class="status-indicator status-waiting" id="invoice-status">En attente de validation</div>
                @endif
            </div>
            </div>

            <!-- Reçu -->
            <div>
            @php
                $hasPayment = $invoice->paid_amount > 0 && $invoice->payments->count() > 0;
                // Vérifier si au moins un reçu peut être signé
                $canSignReceipt = false;
                // Vérifier si au moins un reçu est signé
                $isReceiptSigned = false;
                $allReceiptsSigned = false;

                if ($invoice->payments->count() > 0) {
                    $signedCount = 0;
                    foreach ($invoice->payments as $payment) {
                        if ($payment->isReceiptSigned()) {
                            $isReceiptSigned = true;
                            $signedCount++;
                        }
                        if ($payment->canBeSigned()) {
                            $canSignReceipt = true;
                        }
                    }
                    $allReceiptsSigned = $signedCount === $invoice->payments->count() && $signedCount > 0;
                }
            @endphp

            @if($invoice->payments && $invoice->payments->count() > 0)
            <div style="background: #f8f9fa; padding: 10px 15px; margin: 10px 0; border-radius: 6px; border-left: 3px solid #28a745;">
                <label for="payment-selector" style="display: block; font-weight: 600; color: #1e3c72; margin-bottom: 8px; font-size: 0.9em;">
                    ✅ Sélectionner un paiement
                    <span style="background: #28a745; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.7em; margin-left: 5px;">{{ $invoice->payments->count() }}</span>
                </label>

                <select id="payment-selector"
                        style="width: 100%; padding: 8px 12px; border: 1px solid #28a745; border-radius: 5px; font-size: 0.9rem; background: white; cursor: pointer;"
                        onchange="showPaymentReceipt(this)">
                    <option value="" disabled selected>-- Choisissez un paiement --</option>
                    @foreach($invoice->payments as $index => $payment)
                        @php
                            // Générer le numéro de reçu ou utiliser celui enregistré
                            $receiptNumber = $payment->receipt_number ?? ('BC-' . date('Y') . '-' . str_pad($invoice->id, 3, '0', STR_PAD_LEFT) . '-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT));

                            // Déterminer le statut de ce paiement spécifique
                            $statusText = '';
                            $isSigned = $payment->isReceiptSigned();
                            $canBeSigned = $payment->canBeSigned();

                            if($isSigned) {
                                $statusText = ' ✅';
                            } elseif($canBeSigned) {
                                $statusText = ' ✍️';
                            } else {
                                $statusText = ' ⏳';
                            }
                        @endphp
                        <option value="{{ $index }}"
                                data-payment-id="{{ $payment->id }}"
                                data-receipt-number="{{ $receiptNumber }}"
                                data-payment-date="{{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : 'N/A' }}"
                                data-payment-method="{{ $payment->payment_method ?? 'Non spécifié' }}"
                                data-amount="{{ $payment->amount }}"
                                data-is-signed="{{ $isSigned ? '1' : '0' }}"
                                data-can-be-signed="{{ $canBeSigned ? '1' : '0' }}"
                                data-signature="{{ $payment->receipt_signature ?? '' }}"
                                data-signed-at="{{ $isSigned ? $payment->receipt_signed_at->format('d/m/Y à H:i') : '' }}"
                                data-caisse-ref="{{ $payment->notes ?? '' }}">
                            {{ $receiptNumber }} - {{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : 'N/A' }} - {{ number_format($payment->amount, 0, ',', ' ') }} F{{ $statusText }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="document-card {{ ($canSignReceipt || $isReceiptSigned) ? '' : 'locked' }}" id="receipt-card">
                @if(!$canSignReceipt && !$isReceiptSigned)
                    <div class="lock-icon">🔒</div>
                @endif
                <div class="document-icon">✅</div>
                <div class="document-title">Vos Reçus de Caisse Électroniques</div>

                @if($allReceiptsSigned)
                    <p>✅ Tous vos reçus de paiement ont été signés. Sélectionnez un paiement ci-dessous pour voir son reçu.</p>
                @elseif($isReceiptSigned && $canSignReceipt)
                    <p>📝 Certains reçus sont signés, d'autres en attente de signature. Sélectionnez un paiement ci-dessous.</p>
                @elseif($isReceiptSigned)
                    <p>✅ Vos reçus de paiement sont signés. Sélectionnez un paiement ci-dessous pour voir son reçu.</p>
                @elseif($canSignReceipt)
                    <p>Vos paiements ont été reçus ! Sélectionnez un paiement ci-dessous pour signer son reçu.</p>
                @elseif($invoice->isValidatedByClient() && !$hasPayment)
                    <p>Facture validée ✓ - En attente du paiement pour signer le reçu.</p>
                @else
                    <p>Ce reçu sera accessible après validation de votre facture et réception de votre paiement.</p>
                @endif

                <ul class="features-list">
                    <li>Accusé de réception formel de votre règlement</li>
                    <li>Signature électronique requise</li>
                    <li>Reçu définitif après validation PSI AFRICA</li>
                    <li>Traçabilité complète avec signatures</li>
                </ul>

                <!-- Zone d'affichage dynamique pour les informations du reçu sélectionné -->
                <div id="selected-receipt-info" style="margin: 20px 0; display: none;">
                    <!-- Contenu injecté dynamiquement par JavaScript -->
                </div>

                @if(false) {{-- Bouton remplacé par affichage dynamique via JavaScript --}}
                    <div class="action-buttons" style="margin-top: 20px;">
                        <button class="action-btn" id="view-sign-receipt-btn"
                                onclick="handleReceiptAction()"
                                style="background: #1e3c72;">
                            <span>📄</span> Voir reçu
                        </button>
                    </div>

                    <script>
                    function handleReceiptAction() {
                        const paymentSelector = document.getElementById('payment-selector');

                        if (!paymentSelector || paymentSelector.options.length <= 1) {
                            alert('Aucun paiement disponible.');
                            return;
                        }

                        // Sélectionner automatiquement le dernier paiement
                        const lastIndex = paymentSelector.options.length - 1;
                        paymentSelector.selectedIndex = lastIndex;

                        // Déclencher l'affichage du reçu
                        const selectedOption = paymentSelector.options[lastIndex];
                        const paymentId = selectedOption.getAttribute('data-payment-id');
                        const isSigned = selectedOption.getAttribute('data-is-signed') === '1';
                        const canBeSigned = selectedOption.getAttribute('data-can-be-signed') === '1';

                        // Afficher la section de détail d'abord
                        paymentSelector.dispatchEvent(new Event('change'));

                        // Puis ouvrir le modal approprié après un court délai
                        setTimeout(() => {
                            if (isSigned) {
                                viewSignedPaymentReceipt(paymentId);
                            } else if (canBeSigned) {
                                signPaymentReceipt(paymentId);
                            }
                        }, 300);
                    }
                    </script>
                @endif
            </div>
            </div>
        </div>

        <!-- Prévisualisation de la Facture -->
        <div id="invoice-preview" class="preview-section">
            <h2 class="preview-title">VALIDATION DE VOTRE FACTURE</h2>

            <div class="document-preview">
                <div class="company-header">
                    <div class="company-name">FACTURE PSI AFRICA</div>
                    <div>PSI AFRICA INTERNATIONAL</div>
                    <div>Cabinet conseil en immigration légale et en mobilité internationale</div>
                </div>

                <div class="invoice-info">
                    <div class="invoice-details">
                        <div class="info-group"><span class="info-label">FACTURE N° :</span> {{ $invoice->number }}</div>
                        <div class="info-group"><span class="info-label">Date :</span> {{ $invoice->issue_date ? $invoice->issue_date->format('d/m/Y') : date('d/m/Y') }}</div>
                        <div class="info-group"><span class="info-label">Référence dossier PSI :</span> {{ $client->uid ?? 'N/A' }}</div>
                        <div class="info-group"><span class="info-label">Date d'échéance :</span> {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'N/A' }}</div>
                    </div>

                    <div class="client-info">
                        <div class="info-group"><span class="info-label">FACTURÉ À :</span></div>
                        <div class="info-group"><span class="info-label">Nom / Prénom :</span> {{ $client->nom ?? 'N/A' }} {{ $client->prenoms ?? '' }}</div>
                        <div class="info-group"><span class="info-label">Contact :</span> {{ $client->contact ?? 'N/A' }}</div>
                        <div class="info-group"><span class="info-label">Email :</span> {{ $client->email ?? 'N/A' }}</div>
                        <div class="info-group"><span class="info-label">Conseiller en charge :</span> {{ $invoice->user->name ?? $invoice->agent ?? 'N/A' }}</div>
                    </div>
                </div>

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

                @if($invoice->payments->count() > 0)
                    <div style="margin-top: 25px; margin-bottom: 25px;">
                        <h3 style="color: #1e3c72; border-bottom: 2px solid #1e3c72; padding-bottom: 8px; margin-bottom: 15px;">💰 HISTORIQUE DES PAIEMENTS</h3>

                        @foreach($invoice->payments as $index => $payment)
                            @php
                                $receiptNumber = $payment->receipt_number ?? ('BC-' . date('Y') . '-' . str_pad($invoice->id, 3, '0', STR_PAD_LEFT) . '-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT));

                                // Récupérer les informations du payeur depuis l'entrée de caisse
                                $payeurInfo = null;
                                if ($payment->notes && preg_match('/Ref:\s*([A-Z]+-\d{8}-\d{4})/', $payment->notes, $matches)) {
                                    $caisseRef = $matches[1];
                                    $payeurInfo = \App\Models\CaisseEntree::where('ref', $caisseRef)->first();
                                }
                            @endphp

                            <div style="background: {{ $payment->isReceiptSigned() ? '#d4edda' : '#fff3cd' }}; padding: 15px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid {{ $payment->isReceiptSigned() ? '#28a745' : '#ffc107' }};">
                                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 10px;">
                                    <div><strong>📄 Reçu N° :</strong> {{ $receiptNumber }}</div>
                                    <div><strong>📅 Date :</strong> {{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : 'N/A' }}</div>
                                    <div><strong>💵 Montant :</strong> {{ number_format($payment->amount, 0, ',', ' ') }} FCFA</div>
                                    <div><strong>💳 Mode :</strong> {{ $payment->payment_method ?? 'Non spécifié' }}</div>
                                </div>

                                @if($payeurInfo)
                                    <div style="background: {{ $payeurInfo->type_payeur === 'autre_personne' ? '#fff3cd' : '#e8f5e9' }}; padding: 12px; border-radius: 6px; margin-top: 10px; border-left: 3px solid {{ $payeurInfo->type_payeur === 'autre_personne' ? '#ffc107' : '#4caf50' }};">
                                        <div style="font-weight: bold; color: #2e7d32; margin-bottom: 8px;">👤 INFORMATION DU PAYEUR</div>
                                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; font-size: 0.95em;">
                                            <div><strong>Type :</strong> {{ $payeurInfo->type_payeur === 'autre_personne' ? 'Autre personne' : 'Lui-même' }}</div>

                                            @if($payeurInfo->type_payeur === 'autre_personne')
                                                <div><strong>Nom :</strong> {{ $payeurInfo->payeur_nom_prenom ?? '-' }}</div>
                                                <div><strong>Téléphone :</strong> {{ $payeurInfo->payeur_telephone ?? '-' }}</div>
                                                <div><strong>Relation :</strong> {{ $payeurInfo->payeur_relation ?? '-' }}</div>
                                            @else
                                                <div><strong>Nom :</strong> {{ $payeurInfo->nom ?? '' }} {{ $payeurInfo->prenoms ?? '' }}</div>
                                            @endif

                                            <div><strong>Référence :</strong> {{ $payeurInfo->payeur_reference_dossier ?? $payeurInfo->ref ?? '-' }}</div>
                                        </div>
                                    </div>
                                @endif

                                <div style="text-align: right; margin-top: 10px; font-size: 0.9em; color: #6c757d;">
                                    {{ $payment->isReceiptSigned() ? '✅ Reçu signé le ' . $payment->receipt_signed_at->format('d/m/Y à H:i') : '⏳ En attente de signature' }}
                                </div>
                            </div>
                        @endforeach

                        <div style="background: #e7f3ff; padding: 15px; border-radius: 8px; border-left: 4px solid #2a5298;">
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; font-weight: bold;">
                                <div style="color: #1e3c72;">💵 Total facturé :</div>
                                <div style="text-align: right; color: #1e3c72;">{{ number_format($invoice->amount, 0, ',', ' ') }} FCFA</div>

                                <div style="color: #28a745;">✅ Total payé :</div>
                                <div style="text-align: right; color: #28a745;">{{ number_format($invoice->paid_amount, 0, ',', ' ') }} FCFA</div>

                                @php $remaining = $invoice->amount - $invoice->paid_amount; @endphp
                                <div style="color: {{ $remaining > 0 ? '#856404' : '#28a745' }};">{{ $remaining > 0 ? '⚠️ Reste à payer :' : '✅ Solde :' }}</div>
                                <div style="text-align: right; font-size: 1.1em; color: {{ $remaining > 0 ? '#856404' : '#28a745' }};">{{ number_format($remaining, 0, ',', ' ') }} FCFA</div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(!$invoice->isValidatedByClient())
                    <div class="validation-section">
                        <h3>VALIDATION ET ACCEPTATION DE LA FACTURE</h3>
                        <p>Veuillez lire attentivement et cocher chaque case pour valider votre facture :</p>

                        <div class="checkbox-group">
                            <label>
                                <input type="checkbox" id="accept-amount">
                                <span>Je reconnais avoir pris connaissance du montant total de <strong>{{ number_format($invoice->amount, 0, ',', ' ') }} FCFA</strong> et l'accepte.</span>
                            </label>

                            <label>
                                <input type="checkbox" id="accept-services">
                                <span>Je confirme avoir compris la nature des services inclus dans cette facture.</span>
                            </label>

                            <label>
                                <input type="checkbox" id="accept-conditions">
                                <span>J'accepte les conditions générales de vente et les modalités de paiement.</span>
                            </label>

                            <label>
                                <input type="checkbox" id="accept-nonrefundable">
                                <span>Je comprends que les frais versés sont non remboursables sauf mention contraire écrite.</span>
                            </label>
                        </div>

                        <div class="action-buttons" style="margin-top: 25px;">
                            <button class="action-btn validate-btn" onclick="validateInvoice()" id="validate-invoice-btn">
                                <span>✅</span> Valider et Accepter la Facture
                            </button>
                        </div>
                    </div>
                @else
                    <div class="validation-section" style="background: #d4edda; border-color: #c3e6cb;">
                        <h3 style="color: #155724;">✅ FACTURE VALIDÉE</h3>
                        <p style="color: #155724;">Cette facture a été validée et acceptée le <strong>{{ $invoice->client_validated_at->format('d/m/Y à H:i') }}</strong>.</p>
                        <p style="color: #155724; margin-top: 10px;">Ce document fait foi et est juridiquement valable.</p>

                        <div class="action-buttons" style="margin-top: 20px; display: flex; gap: 10px; justify-content: center;">
                            <button class="action-btn" onclick="downloadInvoicePDF()" style="background: #28a745;">
                                <span>📥</span> Télécharger PDF
                            </button>
                            <button class="action-btn" onclick="printInvoice()" style="background: #17a2b8;">
                                <span>🖨️</span> Imprimer
                            </button>
                        </div>
                    </div>
                @endif

                <div class="conditions">
                    <h3>CONDITIONS GÉNÉRALES</h3>
                    <p>Les conseils fournis par PSI AFRICA sont 100 % gratuits.</p>
                    <p>Cette facture concerne exclusivement les frais de dossier et/ou prestations payantes acceptées par le client.</p>
                    <p>Toute facture non réglée à la date d'échéance peut entraîner des pénalités de retard de 10 % par mois ou la suspension du suivi.</p>
                    <p>Les frais versés sont <strong>non remboursables</strong> sauf mention contraire écrite.</p>
                    <p>PSI AFRICA s'engage à fournir un <strong>suivi personnalisé</strong> et à informer régulièrement le client de l'avancement de son dossier.</p>
                </div>
            </div>

            <div class="action-buttons">
                <button class="action-btn close-btn" onclick="hidePreviews()">
                    <span>❌</span> Fermer
                </button>
            </div>
        </div>

        <!-- Prévisualisation du Reçu -->
        <div id="receipt-preview" class="preview-section">
            <h2 class="preview-title">SIGNATURE DE VOTRE REÇU</h2>

            <div class="document-preview">
                <div class="company-header">
                    <div class="company-name">REÇU DE CAISSE PSI AFRICA</div>
                    <div>PSI AFRICA INTERNATIONAL</div>
                    <div>Cabinet conseil en immigration légale et en mobilité internationale</div>
                </div>

                <div class="invoice-info">
                    <div class="invoice-details">
                        <div class="info-group"><span class="info-label">BON D'ENTRÉE DE CAISSE N° :</span> <span id="preview-receipt-number">BC-{{ date('Y') }}-{{ str_pad($invoice->id, 3, '0', STR_PAD_LEFT) }}</span></div>
                        <div class="info-group"><span class="info-label">Date :</span> <span id="preview-current-date">{{ date('d/m/Y H:i') }}</span></div>
                        <div class="info-group"><span class="info-label">Référence dossier PSI :</span> {{ $client->uid ?? 'N/A' }}</div>

                        <!-- Informations du Payeur -->
                        <div id="payeur-info-section" style="display: none;">
                            <div id="payeur-info-content"></div>
                        </div>
                    </div>

                    <div class="client-info">
                        <div class="info-group"><span class="info-label">FACTURÉ À :</span></div>
                        <div class="info-group"><span class="info-label">Nom / Prénom :</span> {{ $client->nom ?? 'N/A' }} {{ $client->prenoms ?? '' }}</div>
                        <div class="info-group"><span class="info-label">Contact :</span> {{ $client->contact ?? 'N/A' }}</div>
                        <div class="info-group"><span class="info-label">Email :</span> {{ $client->email ?? 'N/A' }}</div>
                        <div class="info-group"><span class="info-label">Conseiller en charge :</span> {{ $invoice->user->name ?? $invoice->agent ?? 'N/A' }}</div>
                    </div>
                </div>

                <h3>DÉTAIL DU PAIEMENT</h3>
                <div style="background: #e7f3ff; padding: 12px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid #2a5298;">
                    <p style="margin: 0; color: #1e3c72; font-size: 0.95em;">
                        <strong>📋 Référence facture :</strong> {{ $invoice->number }}<br>
                        <strong>💵 Montant total facture :</strong> {{ number_format($invoice->amount, 0, ',', ' ') }} FCFA
                    </p>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Désignation</th>
                            <th>Mode de paiement</th>
                            <th>Montant (FCFA)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="preview-payment-date">-</td>
                            <td>{{ $invoice->service }}</td>
                            <td id="preview-payment-method">-</td>
                            <td id="preview-payment-amount">-</td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="3"><strong>Montant de ce paiement</strong></td>
                            <td><strong id="preview-payment-total">-</strong></td>
                        </tr>
                    </tbody>
                </table>

                <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;">
                    <h4 style="margin: 0 0 10px 0; color: #856404;">💼 SITUATION DU DOSSIER APRÈS CE PAIEMENT</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tbody>
                            <tr style="border-bottom: 1px solid #e0e0e0;">
                                <td style="padding: 8px; color: #495057;">Montant total du dossier</td>
                                <td style="padding: 8px; text-align: right; font-weight: bold;">{{ number_format($invoice->amount, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #e0e0e0;">
                                <td style="padding: 8px; color: #495057;">Total déjà versé</td>
                                <td style="padding: 8px; text-align: right; font-weight: bold; color: #28a745;">{{ number_format($invoice->paid_amount, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            @php
                                $remaining = $invoice->amount - $invoice->paid_amount;
                            @endphp
                            <tr style="background: {{ $remaining > 0 ? '#fff3cd' : '#d4edda' }};">
                                <td style="padding: 10px; font-weight: bold;">Reste à payer</td>
                                <td style="padding: 10px; text-align: right; font-weight: bold; color: {{ $remaining > 0 ? '#856404' : '#28a745' }};">
                                    {{ number_format($remaining, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="validation-section">
                    <h3>SIGNATURE ÉLECTRONIQUE DU REÇU</h3>
                    <p>Veuillez signer dans le cadre ci-dessous pour confirmer la réception de ce reçu :</p>

                    <div style="border: 2px dashed #dee2e6; background: #f8f9fa; border-radius: 8px; padding: 10px; margin: 20px 0;">
                        <canvas id="signature-canvas" style="width: 100%; height: 200px; background: white; border-radius: 4px; cursor: crosshair;"></canvas>
                    </div>

                    <div class="signature-actions">
                        <button class="signature-clear" onclick="clearSignature()">Effacer la signature</button>
                    </div>

                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" id="confirm-receipt">
                            <span>Je confirme avoir reçu ce reçu de paiement et en accepte les termes.</span>
                        </label>
                    </div>

                    <div class="action-buttons">
                        <button class="action-btn sign-btn" onclick="signReceipt()" id="sign-receipt-btn">
                            <span>✍️</span> Signer le Reçu
                        </button>
                    </div>
                </div>

                <div style="margin: 25px 0;">
                    <h3>SITUATION DU CLIENT APRÈS PAIEMENT</h3>
                    <table>
                        <tbody>
                            <tr>
                                <td>Montant total du dossier</td>
                                <td>{{ number_format($invoice->amount, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr>
                                <td>Montant déjà versé</td>
                                <td>{{ number_format($invoice->paid_amount, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr class="total-row">
                                <td><strong>Reste à payer</strong></td>
                                <td>
                                    <strong>{{ number_format($invoice->remaining, 0, ',', ' ') }} FCFA</strong>
                                    @if($invoice->remaining <= 0)
                                        <span class="payment-status status-paid">SOLDE</span>
                                    @else
                                        <span class="payment-status status-pending">EN ATTENTE</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="action-buttons">
                <button class="action-btn close-btn" onclick="hidePreviews()">
                    <span>❌</span> Fermer
                </button>
            </div>
        </div>

        <!-- Reçu Définitif -->
        <div id="final-receipt-preview" class="preview-section">
            <h2 class="preview-title"> REÇU ELECTRONIQUE</h2>

            <div class="action-buttons">
                <button class="action-btn download-btn" onclick="downloadDocument('recu-definitif')">
                    <span>📥</span> Télécharger le Reçu Définitif
                </button>
                <button class="action-btn print-btn" onclick="printDocument('final-receipt-preview')">
                    <span>🖨️</span> Imprimer
                </button>
                <button class="action-btn email-btn" onclick="sendEmail('recu-definitif')">
                    <span>✉️</span> Envoyer par email
                </button>
            </div>

            <div class="document-preview">
                <div class="company-header">
                    <div class="company-name">REÇU DE CAISSE PSI AFRICA</div>
                    <div>PSI AFRICA INTERNATIONAL</div>
                    <div>Cabinet conseil en immigration légale et en mobilité internationale</div>
                    <div style="color: green; font-weight: bold;">✅ DOCUMENT SIGNÉ ET APPROUVÉ</div>
                </div>

                <div class="invoice-info">
                    <div class="invoice-details">
                        <div class="info-group"><span class="info-label">BON D'ENTRÉE DE CAISSE N° :</span> <span id="final-receipt-number">BC-{{ date('Y') }}-{{ str_pad($invoice->id, 3, '0', STR_PAD_LEFT) }}</span></div>
                        <div class="info-group"><span class="info-label">Date :</span> <span id="final-current-date">{{ date('d/m/Y H:i') }}</span></div>
                        <div class="info-group"><span class="info-label">Référence dossier PSI :</span> {{ $client->uid ?? 'N/A' }}</div>
                    </div>

                    <div class="client-info">
                        <div class="info-group"><span class="info-label">FACTURÉ À :</span></div>
                        <div class="info-group"><span class="info-label">Nom / Prénom :</span> {{ $client->nom ?? 'N/A' }} {{ $client->prenoms ?? '' }}</div>
                        <div class="info-group"><span class="info-label">Contact :</span> {{ $client->contact ?? 'N/A' }}</div>
                        <div class="info-group"><span class="info-label">Email :</span> {{ $client->email ?? 'N/A' }}</div>
                        <div class="info-group"><span class="info-label">Conseiller en charge :</span> {{ $invoice->user->name ?? $invoice->agent ?? 'N/A' }}</div>
                    </div>
                </div>

                <h3>DÉTAIL DU PAIEMENT</h3>
                <div style="background: #d4edda; padding: 12px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid #28a745;">
                    <p style="margin: 0; color: #155724; font-size: 0.95em;">
                        <strong>📋 Référence facture :</strong> {{ $invoice->number }}<br>
                        <strong>💵 Montant total facture :</strong> {{ number_format($invoice->amount, 0, ',', ' ') }} FCFA<br>
                        <strong>✅ Statut :</strong> Reçu signé et validé
                    </p>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Désignation</th>
                            <th>Mode de paiement</th>
                            <th>Montant (FCFA)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="final-payment-date">-</td>
                            <td>{{ $invoice->service }}</td>
                            <td id="final-payment-method">-</td>
                            <td id="final-payment-amount">-</td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="3"><strong>Montant de ce paiement</strong></td>
                            <td><strong id="final-payment-total">-</strong></td>
                        </tr>
                    </tbody>
                </table>

                <div style="margin-top: 20px; padding: 15px; background: #d4edda; border-radius: 8px; border-left: 4px solid #28a745;">
                    <h4 style="margin: 0 0 10px 0; color: #155724;">💼 SITUATION DU DOSSIER APRÈS CE PAIEMENT</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tbody>
                            <tr style="border-bottom: 1px solid #c3e6cb;">
                                <td style="padding: 8px; color: #155724;">Montant total du dossier</td>
                                <td style="padding: 8px; text-align: right; font-weight: bold;">{{ number_format($invoice->amount, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #c3e6cb;">
                                <td style="padding: 8px; color: #155724;">Total déjà versé</td>
                                <td style="padding: 8px; text-align: right; font-weight: bold;">{{ number_format($invoice->paid_amount, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            @php
                                $remaining = $invoice->amount - $invoice->paid_amount;
                            @endphp
                            <tr style="background: {{ $remaining > 0 ? '#fff3cd' : '#d4edda' }};">
                                <td style="padding: 10px; font-weight: bold;">Reste à payer</td>
                                <td style="padding: 10px; text-align: right; font-weight: bold; color: {{ $remaining > 0 ? '#856404' : '#155724' }};">
                                    {{ number_format($remaining, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="signature-area">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                        <div>
                            <p><strong>Signature du Client :</strong></p>
                            <div id="final-client-signature-display" style="margin-top: 20px; min-height: 80px; border: 1px solid #ccc; background: white; display: flex; align-items: center; justify-content: center;">
                                <span style="color: #999;">En attente de signature</span>
                            </div>
                            <p style="margin-top: 10px;">{{ $client->nom ?? 'N/A' }} {{ $client->prenoms ?? '' }}</p>
                            <p id="final-client-signature-date"><em>Signé électroniquement le <span id="final-signature-date-text">-</span></em></p>
                        </div>
                        <div>
                            <p><strong>Signature PSI AFRICA :</strong></p>
                            <div style="margin-top: 20px; min-height: 80px; border: 1px solid #ccc; background: white; display: flex; align-items: center; justify-content: center;">
                                <div style="text-align: center;">
                                    <div style="font-weight: bold; font-size: 1.2em;">PSI AFRICA INTERNATIONAL</div>
                                    <div>Cachet et signature électronique</div>
                                </div>
                            </div>
                            <p style="margin-top: 10px;">{{ $invoice->user->name ?? $invoice->agent ?? 'Agent PSI AFRICA' }}</p>
                            <p><em>Signé électroniquement le {{ date('d/m/Y') }}</em></p>
                        </div>
                    </div>
                </div>

                <div class="conditions" style="margin-top: 30px; font-style: italic;">
                    <p>✅ <em>Ce reçu définitif a été signé électroniquement par les deux parties et fait foi.</em></p>
                </div>
            </div>

            <div class="action-buttons">
                <button class="action-btn close-btn" onclick="hidePreviews()">
                    <span>❌</span> Fermer
                </button>
            </div>
        </div>

        <!-- Avantages -->
        <div class="benefits-section">
            <h2 style="color: #1e3c72; text-align: center; margin-bottom: 10px;">🔒 Pourquoi Choisir Nos Documents Électroniques ?</h2>
            <p style="text-align: center;">Nous innovons pour votre confort et la préservation de notre environnement</p>

            <div class="benefits-grid">
                <div class="benefit-item">
                    <div class="benefit-icon">🌱</div>
                    <h3>Écologique</h3>
                    <p>Nous réduisons ensemble notre empreinte environnementale</p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">⚡</div>
                    <h3>Immédiat</h3>
                    <p>Plus d'attente, vos documents envoyés instantanément</p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">🔐</div>
                    <h3>Sécurisé</h3>
                    <p>Signature électronique et validation sécurisée</p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">📱</div>
                    <h3>Pratique</h3>
                    <p>Validation et signature en ligne depuis votre appareil</p>
                </div>
            </div>
        </div>

        <!-- Prochaines étapes -->
        <div class="next-steps">
            <h2>💝 Votre Suivi Personnalisé Démarre Maintenant !</h2>
            <p style="margin: 15px 0;">Avec la validation de votre facture et la signature de votre reçu, votre conseiller dédié <strong>{{ $invoice->user->name ?? $invoice->agent ?? 'votre conseiller' }}</strong> va maintenant pouvoir activer votre dossier et vous accompagner pas à pas vers la concrétisation de votre projet.</p>
            <p>Vous recevrez très prochainement de ses nouvelles ainsi qu'un planning prévisionnel des prochaines étapes.</p>
        </div>

        <!-- Pied de page -->
        <div class="contact-footer">
            <p><strong>Nous vous remercions chaleureusement pour la confiance que vous accordez à PSI AFRICA INTERNATIONAL.</strong></p>
            <p style="margin: 15px 0; font-style: italic;">Bien à vous,<br>L'Équipe PSI AFRICA INTERNATIONAL<br>Votre Partenaire en Mobilité Internationale</p>

            <div class="contact-info">
                <div class="contact-item">📍 Angré Terminus 81/82, à 20m de la pharmacie du Jubilé</div>
                <div class="contact-item">📞 +225 01 04 04 04 05</div>
                <div class="contact-item">📧 infos@psiafrica.com</div>
                <div class="contact-item">🌐 www.psiafrica.com</div>
            </div>
        </div>
    </div>

    <script>
        // Fonction pour changer de facture
        function changeInvoice(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const token = selectedOption.getAttribute('data-token');

            if (token) {
                // Rediriger vers la facture sélectionnée
                window.location.href = '/facturation/' + token;
            }
        }

        // Fonction pour naviguer vers une facture via son token
        function navigateToInvoice(token) {
            if (token) {
                window.location.href = '/facturation/' + token;
            }
        }

        // Fonction pour afficher le reçu de caisse lors de la sélection d'un paiement
        function showPaymentReceipt(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const infoContainer = document.getElementById('selected-receipt-info');

            // Si aucune option valide n'est sélectionnée
            if (!selectedOption || !selectedOption.value) {
                infoContainer.style.display = 'none';
                return;
            }

            // Récupérer les données du paiement depuis les attributs data
            const paymentId = selectedOption.getAttribute('data-payment-id');
            const receiptNumber = selectedOption.getAttribute('data-receipt-number');
            const isSigned = selectedOption.getAttribute('data-is-signed') === '1';
            const canBeSigned = selectedOption.getAttribute('data-can-be-signed') === '1';
            const signedAt = selectedOption.getAttribute('data-signed-at');

            // Construire l'affichage selon le statut
            if (isSigned) {
                // Reçu signé : afficher bouton + date/heure sur même ligne
                infoContainer.innerHTML = `
                    <div style="display: flex; justify-content: center; align-items: center; gap: 20px; flex-wrap: wrap;">
                        <button class="action-btn" onclick="viewSignedPaymentReceipt(${paymentId})"
                                style="background: #28a745;">
                            <span>📄</span> Voir le reçu
                        </button>
                        <div class="status-indicator status-completed">
                            ✅ Signé le ${signedAt}
                        </div>
                    </div>
                `;
                infoContainer.style.display = 'block';
            } else if (canBeSigned) {
                // Reçu prêt à signer : afficher bouton de signature
                infoContainer.innerHTML = `
                    <div style="display: flex; justify-content: center; align-items: center; gap: 20px; flex-wrap: wrap;">
                        <button class="action-btn" onclick="signPaymentReceipt(${paymentId})"
                                style="background: #ffc107; color: #212529;">
                            <span>✍️</span> Signer ce reçu
                        </button>
                        <div class="status-indicator status-pending">
                            ✍️ Prêt à signer
                        </div>
                    </div>
                `;
                infoContainer.style.display = 'block';
            } else {
                // Reçu non disponible
                infoContainer.innerHTML = `
                    <div style="text-align: center;">
                        <div class="status-indicator status-waiting">
                            ⏳ Veuillez valider la facture d'abord
                        </div>
                    </div>
                `;
                infoContainer.style.display = 'block';
            }
        }

        // Fonction pour signer un reçu de paiement individuel
        function signPaymentReceipt(paymentId) {
            // Stocker l'ID du paiement
            window.currentPaymentId = paymentId;

            // Récupérer les données du paiement sélectionné depuis le dropdown
            const paymentSelector = document.getElementById('payment-selector');
            const selectedOption = paymentSelector.options[paymentSelector.selectedIndex];

            if (selectedOption) {
                // Remplir le preview avec les données du paiement
                fillPaymentPreview(selectedOption);
            }

            // Afficher le modal de signature
            showPreview('receipt-preview');
        }

        // Fonction pour prévisualiser un reçu avant signature
        function previewPaymentReceipt(paymentId) {
            // Stocker l'ID du paiement
            window.currentPaymentId = paymentId;

            // Récupérer les données du paiement sélectionné depuis le dropdown
            const paymentSelector = document.getElementById('payment-selector');
            const selectedOption = paymentSelector.options[paymentSelector.selectedIndex];

            if (selectedOption) {
                // Remplir le preview avec les données du paiement
                fillPaymentPreview(selectedOption);
            }

            // Afficher le modal de prévisualisation
            showPreview('receipt-preview');
        }

        // Fonction pour remplir le preview avec les données d'un paiement
        function fillPaymentPreview(selectedOption) {
            const receiptNumber = selectedOption.getAttribute('data-receipt-number');
            const paymentDate = selectedOption.getAttribute('data-payment-date');
            const paymentMethod = selectedOption.getAttribute('data-payment-method');
            const amount = selectedOption.getAttribute('data-amount');

            // Icônes pour les modes de paiement
            const methodIcons = {
                'Espèces': '💵',
                'Virement': '🏦',
                'Chèque': '📝',
                'Carte bancaire': '💳',
                'Mobile Money': '📱'
            };
            const icon = methodIcons[paymentMethod] || '💰';

            // Formater le montant
            const formattedAmount = new Intl.NumberFormat('fr-FR').format(amount) + ' FCFA';

            // Remplir les champs du preview
            document.getElementById('preview-receipt-number').textContent = receiptNumber;
            document.getElementById('preview-payment-date').textContent = paymentDate;
            document.getElementById('preview-payment-method').innerHTML = `${icon} ${paymentMethod}`;
            document.getElementById('preview-payment-amount').textContent = formattedAmount;
            document.getElementById('preview-payment-total').textContent = formattedAmount;

            // Charger les informations du payeur depuis l'entrée de caisse
            loadPayeurInfo(selectedOption);
        }

        // Fonction pour voir un reçu déjà signé
        function viewSignedPaymentReceipt(paymentId) {
            // Stocker l'ID du paiement
            window.currentPaymentId = paymentId;

            // Récupérer les données du paiement sélectionné depuis le dropdown
            const paymentSelector = document.getElementById('payment-selector');
            const selectedOption = paymentSelector.options[paymentSelector.selectedIndex];

            if (selectedOption) {
                // Remplir le reçu définitif avec les données du paiement
                fillFinalReceiptPreview(selectedOption);
            }

            // Afficher le modal du reçu signé
            showPreview('final-receipt-preview');
        }

        // Fonction pour remplir le reçu définitif avec les données d'un paiement
        function fillFinalReceiptPreview(selectedOption) {
            const receiptNumber = selectedOption.getAttribute('data-receipt-number');
            const paymentDate = selectedOption.getAttribute('data-payment-date');
            const paymentMethod = selectedOption.getAttribute('data-payment-method');
            const amount = selectedOption.getAttribute('data-amount');

            // Icônes pour les modes de paiement
            const methodIcons = {
                'Espèces': '💵',
                'Virement': '🏦',
                'Chèque': '📝',
                'Carte bancaire': '💳',
                'Mobile Money': '📱'
            };
            const icon = methodIcons[paymentMethod] || '💰';

            // Formater le montant
            const formattedAmount = new Intl.NumberFormat('fr-FR').format(amount) + ' FCFA';

            // Remplir les champs du reçu définitif
            document.getElementById('final-receipt-number').textContent = receiptNumber;
            document.getElementById('final-payment-date').textContent = paymentDate;
            document.getElementById('final-payment-method').innerHTML = `${icon} ${paymentMethod}`;
            document.getElementById('final-payment-amount').textContent = formattedAmount;
            document.getElementById('final-payment-total').textContent = formattedAmount;

            // Afficher la signature du client si elle existe
            const signature = selectedOption.getAttribute('data-signature');
            const signedAt = selectedOption.getAttribute('data-signed-at');
            const signatureDisplay = document.getElementById('final-client-signature-display');
            const signatureDateText = document.getElementById('final-signature-date-text');

            if (signature && signature !== '') {
                signatureDisplay.innerHTML = '<img src="' + signature + '" alt="Signature client" style="max-width: 100%; max-height: 70px;">';
                signatureDateText.textContent = signedAt || 'Date non disponible';
            } else {
                signatureDisplay.innerHTML = '<span style="color: #999;">En attente de signature</span>';
                signatureDateText.textContent = '-';
            }

            // Charger les informations du payeur depuis l'entrée de caisse
            loadPayeurInfo(selectedOption);
        }

        async function loadPayeurInfo(selectedOption) {
            const caisseRef = selectedOption.getAttribute('data-caisse-ref');
            const payeurSection = document.getElementById('payeur-info-section');
            const payeurContent = document.getElementById('payeur-info-content');

            if (!caisseRef || !caisseRef.includes('Ref:')) {
                payeurSection.style.display = 'none';
                return;
            }

            // Extraire la référence de l'entrée de caisse (format: "Paiement caisse - Ref: ENT-XXXXXXXX-XXXX")
            const refMatch = caisseRef.match(/Ref:\s*([A-Z]+-\d{8}-\d{4})/);
            if (!refMatch) {
                payeurSection.style.display = 'none';
                return;
            }

            const entreeRef = refMatch[1];

            try {
                // Charger l'entrée de caisse depuis l'API par référence
                const response = await fetch(`/caisse/api/entrees/ref/${entreeRef}`);
                if (!response.ok) {
                    payeurSection.style.display = 'none';
                    return;
                }

                const data = await response.json();
                if (!data.success || !data.data) {
                    payeurSection.style.display = 'none';
                    return;
                }

                const entree = data.data;

                // Afficher les informations du payeur
                const typePayeur = entree.type_payeur || 'lui_meme';
                const typePayeurLabel = typePayeur === 'autre_personne' ? 'Autre personne' : 'Lui-même';

                let html = `<div class="info-group"><span class="info-label">Type de payeur :</span> ${typePayeurLabel}</div>`;

                if (typePayeur === 'autre_personne') {
                    html += `<div class="info-group"><span class="info-label">Nom et Prénom :</span> ${entree.payeur_nom_prenom || '-'}</div>`;
                    html += `<div class="info-group"><span class="info-label">Téléphone :</span> ${entree.payeur_telephone || '-'}</div>`;
                    html += `<div class="info-group"><span class="info-label">Relation avec le client :</span> ${entree.payeur_relation || '-'}</div>`;
                } else {
                    html += `<div class="info-group"><span class="info-label">Nom et Prénom :</span> ${entree.nom || ''} ${entree.prenoms || ''}</div>`;
                }

                html += `<div class="info-group"><span class="info-label">Référence du dossier :</span> ${entree.payeur_reference_dossier || entree.ref || '-'}</div>`;

                payeurContent.innerHTML = html;
                payeurSection.style.display = 'block';

            } catch (error) {
                console.error('Erreur chargement informations payeur:', error);
                payeurSection.style.display = 'none';
            }
        }

        // Fonction pour télécharger le PDF d'un reçu de paiement
        function downloadPaymentReceipt(paymentId) {
            // Rediriger vers l'URL de téléchargement du reçu de paiement
            window.location.href = '/facturation/payment/' + paymentId + '/download-receipt';
        }

        // Fonction pour imprimer un reçu de paiement
        function printPaymentReceipt(paymentId) {
            // Ouvrir la fenêtre d'impression
            window.print();
        }

        // Variables d'état
        let factureValidated = {{ $invoice->isValidatedByClient() ? 'true' : 'false' }};
        let hasPayment = {{ $invoice->paid_amount > 0 ? 'true' : 'false' }};
        let receiptSigned = {{ $invoice->isReceiptSigned() ? 'true' : 'false' }};
        let canSignReceipt = factureValidated && hasPayment && !receiptSigned;
        let canvas;
        let ctx;
        let isDrawing = false;

        // Initialisation du canvas de signature pour le reçu
        function initSignaturePad() {
            canvas = document.getElementById('signature-canvas');
            if (!canvas) return;

            // Redimensionner le canvas correctement
            resizeCanvas();

            ctx = canvas.getContext('2d');
            ctx.strokeStyle = '#000';
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';

            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDrawing);
            canvas.addEventListener('mouseout', stopDrawing);

            // Support tactile
            canvas.addEventListener('touchstart', startDrawingTouch);
            canvas.addEventListener('touchmove', drawTouch);
            canvas.addEventListener('touchend', stopDrawing);

            // Redimensionner lors du changement de taille de fenêtre
            window.addEventListener('resize', resizeCanvas);
        }

        // Fonction pour redimensionner le canvas correctement
        function resizeCanvas() {
            if (!canvas) return;

            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext('2d').scale(ratio, ratio);

            // Réinitialiser le style après redimensionnement
            if (ctx) {
                ctx.strokeStyle = '#000';
                ctx.lineWidth = 2;
                ctx.lineCap = 'round';
            }
        }

        function startDrawing(e) {
            isDrawing = true;
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            ctx.beginPath();
            ctx.moveTo(x, y);
        }

        function draw(e) {
            if (!isDrawing) return;

            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            ctx.lineTo(x, y);
            ctx.stroke();
        }

        function stopDrawing() {
            isDrawing = false;
            ctx.beginPath();
        }

        function startDrawingTouch(e) {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent('mousedown', {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            canvas.dispatchEvent(mouseEvent);
        }

        function drawTouch(e) {
            e.preventDefault();
            const touch = e.touches[0];
            const rect = canvas.getBoundingClientRect();
            const offsetX = touch.clientX - rect.left;
            const offsetY = touch.clientY - rect.top;

            const mouseEvent = new MouseEvent('mousemove', {
                clientX: touch.clientX,
                clientY: touch.clientY,
                offsetX: offsetX,
                offsetY: offsetY
            });
            canvas.dispatchEvent(mouseEvent);
        }

        function clearSignature() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        // Fonction pour afficher une prévisualisation
        function showPreview(previewId) {
            // Masquer toutes les prévisualisations
            hidePreviews();

            // Afficher la prévisualisation demandée
            document.getElementById(previewId).classList.add('active-preview');

            // Initialiser le pad de signature si c'est le reçu
            if (previewId === 'receipt-preview') {
                setTimeout(initSignaturePad, 100);
            }

            // Faire défiler jusqu'à la prévisualisation
            document.getElementById(previewId).scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        // Fonction pour masquer toutes les prévisualisations
        function hidePreviews() {
            const previews = document.querySelectorAll('.preview-section');
            previews.forEach(preview => {
                preview.classList.remove('active-preview');
            });
        }

        // Validation de la facture
        async function validateInvoice() {
            const checkboxes = [
                document.getElementById('accept-amount'),
                document.getElementById('accept-services'),
                document.getElementById('accept-conditions'),
                document.getElementById('accept-nonrefundable')
            ];

            const allChecked = checkboxes.every(checkbox => checkbox.checked);

            if (!allChecked) {
                alert('Veuillez cocher toutes les cases pour valider la facture.');
                return;
            }

            // Désactiver le bouton pour éviter les doubles clics
            const validateBtn = document.getElementById('validate-invoice-btn');
            validateBtn.disabled = true;
            validateBtn.innerHTML = '<span>⏳</span> Validation en cours...';

            try {
                // Envoyer la requête de validation au serveur
                const response = await fetch(window.location.pathname + '/validate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        validated: true
                    })
                });

                const data = await response.json();

                if (data.success) {
                    alert('✅ Facture validée avec succès !\n\nVous allez être redirigé vers votre facture finale.');
                    // Rediriger vers la même URL pour afficher la vue validée
                    window.location.href = window.location.href;
                } else {
                    throw new Error(data.error || 'Erreur de validation');
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('❌ Une erreur est survenue lors de la validation.\n\n' + error.message);
                validateBtn.disabled = false;
                validateBtn.innerHTML = '<span>✅</span> Valider et Accepter la Facture';
            }
        }

        // Télécharger la facture en PDF
        function downloadInvoicePDF() {
            // Utiliser l'impression avec option "Enregistrer au format PDF"
            window.print();
        }

        // Imprimer la facture
        function printInvoice() {
            // Ouvrir la boîte de dialogue d'impression
            window.print();
        }

        // Signature du reçu
        async function signReceipt() {
            if (!factureValidated) {
                alert('Veuillez d\'abord valider votre facture.');
                return;
            }

            if (!hasPayment) {
                alert('En attente de la réception de votre paiement. Veuillez effectuer votre paiement pour pouvoir signer le reçu.');
                return;
            }

            // Vérifier si un paiement est sélectionné et récupérer ses données
            if (!window.currentPaymentId) {
                alert('Veuillez sélectionner un paiement à signer.');
                return;
            }

            const paymentSelector = document.getElementById('payment-selector');
            const selectedOption = paymentSelector.options[paymentSelector.selectedIndex];

            if (!selectedOption) {
                alert('Veuillez sélectionner un paiement à signer.');
                return;
            }

            // Vérifier si ce paiement spécifique est déjà signé
            const isPaymentSigned = selectedOption.getAttribute('data-is-signed') === '1';
            if (isPaymentSigned) {
                alert('Ce reçu de paiement a déjà été signé.');
                return;
            }

            // Vérifier si ce paiement peut être signé
            const canBeSigned = selectedOption.getAttribute('data-can-be-signed') === '1';
            if (!canBeSigned) {
                alert('Ce paiement ne peut pas encore être signé. Veuillez d\'abord valider la facture.');
                return;
            }

            if (!document.getElementById('confirm-receipt').checked) {
                alert('Veuillez confirmer la réception du reçu.');
                return;
            }

            // Vérifier si la signature n'est pas vide
            const signatureData = canvas.toDataURL();
            const blankCanvas = document.createElement('canvas');
            blankCanvas.width = canvas.width;
            blankCanvas.height = canvas.height;

            if (signatureData === blankCanvas.toDataURL()) {
                alert('Veuillez ajouter votre signature.');
                return;
            }

            // Désactiver le bouton pendant le traitement
            const signBtn = document.getElementById('sign-receipt-btn');
            signBtn.disabled = true;
            signBtn.innerHTML = '<span>⏳</span> Signature en cours...';

            try {
                // Envoyer la signature au serveur avec l'ID du paiement
                const response = await fetch(window.location.pathname + '/sign-receipt', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        payment_id: window.currentPaymentId,
                        signature_data: signatureData
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Afficher la signature dans le reçu définitif
                    const signatureDisplay = document.getElementById('client-signature-display');
                    signatureDisplay.innerHTML = '<img src="' + signatureData + '" alt="Signature client" style="max-width: 100%; max-height: 70px;">';

                    alert('✅ Reçu de paiement signé avec succès !\n\nVous allez être redirigé vers votre reçu définitif.');

                    // Recharger la page pour afficher le nouvel état
                    window.location.reload();
                } else {
                    throw new Error(data.error || 'Erreur lors de la signature');
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('❌ Une erreur est survenue lors de la signature du reçu.\n\n' + error.message);
                signBtn.disabled = false;
                signBtn.innerHTML = '<span>✍️</span> Signer le Reçu';
            }
        }

        // Fonction pour télécharger un document
        function downloadDocument(type) {
            let fileName, documentName;

            if (type === 'recu-definitif') {
                fileName = 'Recu_Definitif_Signe_PSI_AFRICA_{{ $invoice->number }}.pdf';
                documentName = 'reçu définitif signé';
            } else {
                fileName = 'Facture_PSI_AFRICA_{{ $invoice->number }}.pdf';
                documentName = 'facture';
            }

            alert(`Téléchargement du ${documentName} : ${fileName}\n\nDans une application réelle, le fichier PDF serait généré et téléchargé.`);
        }

        // Fonction pour imprimer un document
        function printDocument(previewId) {
            const printContent = document.getElementById(previewId).innerHTML;
            const originalContent = document.body.innerHTML;

            document.body.innerHTML = printContent;
            window.print();
            document.body.innerHTML = originalContent;

            // Recharger la page pour restaurer les fonctionnalités JavaScript
            window.location.reload();
        }

        // Fonction pour envoyer un document par email
        function sendEmail(type) {
            let subject, body;

            if (type === 'recu-definitif') {
                subject = 'Votre reçu définitif PSI AFRICA - {{ $invoice->number }}';
                body = 'Bonjour,%0D%0A%0D%0AVeuillez trouver ci-joint votre reçu définitif signé PSI AFRICA.%0D%0A%0D%0ACordialement,%0D%0AL\'équipe PSI AFRICA';
            } else {
                subject = 'Votre facture PSI AFRICA - {{ $invoice->number }}';
                body = 'Bonjour,%0D%0A%0D%0AVeuillez trouver ci-joint votre facture PSI AFRICA.%0D%0A%0D%0ACordialement,%0D%0AL\'équipe PSI AFRICA';
            }

            window.location.href = `mailto:{{ $client->email ?? '' }}?subject=${subject}&body=${body}`;
        }

        // Masquer les prévisualisations au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            hidePreviews();

            // Sélectionner automatiquement le dernier paiement
            const paymentSelector = document.getElementById('payment-selector');
            if (paymentSelector && paymentSelector.options.length > 1) {
                // Sélectionner la dernière option (le dernier paiement)
                const lastIndex = paymentSelector.options.length - 1;
                paymentSelector.selectedIndex = lastIndex;

                // Déclencher l'événement de changement pour afficher les détails
                setTimeout(() => {
                    paymentSelector.dispatchEvent(new Event('change'));
                }, 100);
            }

            // Auto-ouvrir la section signature du reçu si #receipt dans l'URL
            // et si la facture est validée ET qu'un paiement a été reçu
            if (window.location.hash === '#receipt' && canSignReceipt) {
                setTimeout(() => {
                    showPreview('receipt-preview');
                    // Scroll vers la section
                    document.getElementById('receipt-preview').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 300);
            }
        });
    </script>
</body>
</html>
