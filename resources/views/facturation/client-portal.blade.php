<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PSI AFRICA - Portail Client</title>
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
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
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

        .client-info-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .client-info-card h2 {
            color: #1e3c72;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .info-item {
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .info-label {
            font-weight: bold;
            color: #1e3c72;
            display: block;
            margin-bottom: 5px;
        }

        .section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .section h2 {
            color: #1e3c72;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
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

        tr:hover {
            background: #f8f9fa;
        }

        .badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.85em;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #2a5298;
            color: white;
        }

        .btn-primary:hover {
            background: #1e3c72;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        .empty-state img {
            max-width: 200px;
            opacity: 0.5;
            margin-bottom: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .stat-card.green {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
        }

        .stat-card.orange {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stat-card.blue {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stat-value {
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9em;
            opacity: 0.9;
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

            .header, .footer {
                display: none;
            }

            .section, .client-info-card {
                box-shadow: none;
                border: 1px solid #ddd;
                page-break-inside: avoid;
            }
        }

        @media (max-width: 768px) {
            .info-grid, .stats-grid {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 0.9em;
            }

            th, td {
                padding: 8px;
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

        <!-- Informations Client -->
        <div class="client-info-card">
            <h2>👤 Vos Informations</h2>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Référence PSI</span>
                    {{ $client->uid }}
                </div>
                <div class="info-item">
                    <span class="info-label">Nom / Prénom</span>
                    {{ $client->nom }} {{ $client->prenoms ?? '' }}
                </div>
                <div class="info-item">
                    <span class="info-label">Contact</span>
                    {{ $client->contact }}
                </div>
                <div class="info-item">
                    <span class="info-label">Email</span>
                    {{ $client->email ?? 'N/A' }}
                </div>
                <div class="info-item">
                    <span class="info-label">Conseiller en charge</span>
                    {{ $client->agent ?? 'N/A' }}
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{{ $invoices->count() }}</div>
                <div class="stat-label">Factures totales</div>
            </div>
            <div class="stat-card green">
                <div class="stat-value">{{ number_format($totalPaid, 0, ',', ' ') }} FCFA</div>
                <div class="stat-label">Total payé</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-value">{{ number_format($totalRemaining, 0, ',', ' ') }} FCFA</div>
                <div class="stat-label">Reste à payer</div>
            </div>
            <div class="stat-card blue">
                <div class="stat-value">{{ $payments->count() }}</div>
                <div class="stat-label">Paiements effectués</div>
            </div>
        </div>

        <!-- Factures -->
        <div class="section">
            <h2>📄 Vos Factures</h2>

            @if($invoices->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>N° Facture</th>
                            <th>Service</th>
                            <th>Montant</th>
                            <th>Payé</th>
                            <th>Reste</th>
                            <th>Statut</th>
                            <th>Date émission</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                            @php
                                $remaining = $invoice->amount - $invoice->paid_amount;
                                $isOverdue = $invoice->due_date && $invoice->due_date->isPast() && $invoice->status !== 'paid';
                            @endphp
                            <tr>
                                <td><strong>{{ $invoice->number }}</strong></td>
                                <td>{{ $invoice->service }}</td>
                                <td>{{ number_format($invoice->amount, 0, ',', ' ') }} FCFA</td>
                                <td style="color: #28a745;">{{ number_format($invoice->paid_amount, 0, ',', ' ') }} FCFA</td>
                                <td style="color: {{ $remaining > 0 ? '#f5576c' : '#28a745' }};">{{ number_format($remaining, 0, ',', ' ') }} FCFA</td>
                                <td>
                                    @if($invoice->status === 'paid')
                                        <span class="badge badge-success">PAYÉ</span>
                                    @elseif($isOverdue)
                                        <span class="badge badge-danger">EN RETARD</span>
                                    @elseif($invoice->status === 'partial')
                                        <span class="badge badge-warning">PARTIEL</span>
                                    @else
                                        <span class="badge badge-info">EN ATTENTE</span>
                                    @endif
                                </td>
                                <td>{{ $invoice->issue_date ? $invoice->issue_date->format('d/m/Y') : 'N/A' }}</td>
                                <td>
                                    @if($invoice->view_token)
                                        <a href="{{ url('/facturation/' . $invoice->view_token) }}" class="btn btn-primary" target="_blank">
                                            👁️ Voir
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <p>📭 Aucune facture pour le moment</p>
                </div>
            @endif
        </div>

        <!-- Paiements -->
        <div class="section">
            <h2>💳 Historique des Paiements</h2>

            @if($payments->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>N° Facture</th>
                            <th>Montant</th>
                            <th>Mode de paiement</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : 'N/A' }}</td>
                                <td><strong>{{ $payment->invoice->number }}</strong></td>
                                <td><strong style="color: #28a745;">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</strong></td>
                                <td>{{ $payment->payment_method ?? 'Non spécifié' }}</td>
                                <td>{{ $payment->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <p>💰 Aucun paiement enregistré pour le moment</p>
                </div>
            @endif
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
</body>
</html>
