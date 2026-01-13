<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur - PSI AFRICA</title>
    <link rel="icon" href="{{ asset('favicon.png') }}"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
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
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-container {
            text-align: center;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 50px;
            max-width: 600px;
        }

        .error-icon {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 20px;
        }

        .error-title {
            color: var(--psi-blue);
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .error-message {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        .contact-box {
            background: #f8f9fa;
            border-left: 4px solid var(--psi-blue);
            padding: 20px;
            border-radius: 4px;
            margin-top: 30px;
            text-align: left;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--psi-gold);
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="logo-text">PSI AFRICA</div>

        <i class="bi bi-exclamation-triangle error-icon"></i>

        <h1 class="error-title">{{ $error }}</h1>

        <p class="error-message">{{ $message }}</p>

        <div class="contact-box">
            <h5><i class="bi bi-telephone"></i> Besoin d'aide ?</h5>
            <p class="mb-2">
                <strong>Email :</strong> contact@psiafrica.com<br>
                <strong>Téléphone :</strong> +225 01 04 04 04 05
            </p>
            <p class="mb-0">
                <small class="text-muted">
                    Notre équipe est disponible du lundi au vendredi de 8h à 17h
                </small>
            </p>
        </div>

        <div class="mt-4">
            <button class="btn btn-secondary" onclick="closeOrGoBack()">
                <i class="bi bi-x-circle"></i> Fermer
            </button>
        </div>

        <script>
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
    </div>
</body>
</html>
