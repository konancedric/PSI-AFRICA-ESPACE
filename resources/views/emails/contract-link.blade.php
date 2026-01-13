<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre contrat PSI Africa</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }

        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: #ffffff;
            padding: 2rem;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 1.75rem;
        }

        .header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }

        .content {
            padding: 2rem;
            color: #1f2937;
        }

        .greeting {
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .message {
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .contract-info {
            background-color: #f3f4f6;
            border-left: 4px solid #3b82f6;
            padding: 1rem;
            margin: 1.5rem 0;
        }

        .contract-info p {
            margin: 0.5rem 0;
        }

        .contract-info strong {
            color: #1e3a8a;
        }

        .btn-container {
            text-align: center;
            margin: 2rem 0;
        }

        .btn-view {
            display: inline-block;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 1rem 2.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
        }

        .link-info {
            background-color: #fef3c7;
            border: 1px solid #fbbf24;
            border-radius: 8px;
            padding: 1rem;
            margin: 1.5rem 0;
            font-size: 0.875rem;
        }

        .link-url {
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            padding: 0.75rem;
            border-radius: 4px;
            margin-top: 0.5rem;
            word-break: break-all;
            font-family: monospace;
            font-size: 0.875rem;
        }

        .footer {
            background-color: #f3f4f6;
            padding: 1.5rem;
            text-align: center;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .footer p {
            margin: 0.25rem 0;
        }

        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 1.5rem 0;
        }

        .features {
            margin: 1.5rem 0;
        }

        .feature-item {
            display: flex;
            align-items: start;
            margin-bottom: 1rem;
        }

        .feature-icon {
            color: #3b82f6;
            margin-right: 0.75rem;
            font-size: 1.25rem;
        }

        .feature-text {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>PSI Africa</h1>
            <p>Votre partenaire de confiance pour vos projets d'immigration</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p class="greeting">Bonjour <strong>{{ $contract->prenom }} {{ $contract->nom }}</strong>,</p>

            <div class="message">
                <p>Nous avons le plaisir de vous informer que votre contrat est maintenant disponible en ligne.</p>
            </div>

            <!-- Contract Info -->
            <div class="contract-info">
                <p><strong>Numéro de contrat :</strong> {{ $contract->numero_contrat }}</p>
                <p><strong>Type de service :</strong> {{ $contract->type_visa ?? 'N/A' }}</p>
                <p><strong>Destination :</strong> {{ $contract->pays_destination ?? 'N/A' }}</p>
                <p><strong>Date :</strong> {{ $contract->date_contrat ? $contract->date_contrat->format('d/m/Y') : 'N/A' }}</p>
            </div>

            <!-- Call to Action Button -->
            <div class="btn-container">
                <a href="{{ $viewLink }}" class="btn-view">
                    Consulter mon contrat
                </a>
            </div>

            <!-- Features -->
            <div class="features">
                <div class="feature-item">
                    <span class="feature-icon">✓</span>
                    <div class="feature-text">
                        <strong>Consultez votre contrat</strong> en ligne à tout moment
                    </div>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">✓</span>
                    <div class="feature-text">
                        <strong>Téléchargez</strong> une copie PDF de votre contrat
                    </div>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">✓</span>
                    <div class="feature-text">
                        <strong>Imprimez</strong> votre contrat directement depuis votre navigateur
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            <!-- Alternative Link -->
            <div class="link-info">
                <p style="margin: 0 0 0.5rem 0; font-weight: 600; color: #92400e;">
                    Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :
                </p>
                <div class="link-url">{{ $viewLink }}</div>
            </div>

            <div class="message">
                <p>Ce lien est personnel et sécurisé. Ne le partagez pas.</p>
                <p>Si vous avez des questions concernant votre contrat, n'hésitez pas à nous contacter.</p>
            </div>

            <p style="margin-top: 2rem;">
                Cordialement,<br>
                <strong>L'équipe PSI Africa</strong>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>PSI Africa</strong></p>
            <p>Agence de Voyage et Immigration</p>
            <p style="margin-top: 1rem; font-size: 0.75rem; color: #9ca3af;">
                Cet email a été envoyé automatiquement, merci de ne pas y répondre directement.
            </p>
        </div>
    </div>
</body>
</html>
