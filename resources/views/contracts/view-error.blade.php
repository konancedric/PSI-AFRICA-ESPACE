<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur - PSI Africa</title>
    <link rel="icon" href="{{ asset('favicon.png') }}"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f9fafb 0%, #e5e7eb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .error-container {
            max-width: 500px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            text-align: center;
        }

        .error-icon {
            font-size: 4rem;
            color: #dc2626;
            margin-bottom: 1.5rem;
        }

        .error-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .error-message {
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .btn-home {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 58, 138, 0.3);
            color: white;
        }

        .support-info {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
            font-size: 0.875rem;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <h1 class="error-title">{{ $error }}</h1>
        <p class="error-message">{{ $message }}</p>

        <div class="support-info">
            <p class="mb-2"><strong>Besoin d'aide ?</strong></p>
            <p class="mb-0">Contactez notre service client pour plus d'informations</p>
        </div>
    </div>
</body>
</html>
