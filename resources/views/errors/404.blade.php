<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page non trouvée - PSI Africa</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .error-container {
            text-align: center;
            background: rgba(255, 255, 255, 0.95);
            padding: 3rem 2rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
            line-height: 1;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .error-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin: 1rem 0;
        }
        
        .error-subtitle {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .search-container {
            margin: 2rem 0;
            position: relative;
        }
        
        .search-input {
            width: 100%;
            padding: 15px 20px;
            font-size: 1.1rem;
            border: 2px solid #e9ecef;
            border-radius: 50px;
            outline: none;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .search-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .search-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .search-btn:hover {
            transform: translateY(-50%) scale(1.05);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        
        .btn-custom {
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary-custom {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border-color: transparent;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            color: white;
            text-decoration: none;
        }
        
        .btn-outline-custom {
            background: transparent;
            color: #667eea;
            border-color: #667eea;
        }
        
        .btn-outline-custom:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            text-decoration: none;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin: 3rem 0;
        }
        
        .feature-item {
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 15px;
            transition: all 0.3s ease;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }
        
        .feature-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.9);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
        
        .feature-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .feature-desc {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        .footer-links {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(102, 126, 234, 0.2);
        }
        
        .footer-links a {
            color: #667eea;
            text-decoration: none;
            margin: 0 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .footer-links a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .error-code {
                font-size: 5rem;
            }
            
            .error-title {
                font-size: 2rem;
            }
            
            .error-subtitle {
                font-size: 1rem;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .features {
                grid-template-columns: 1fr;
            }
        }
        
        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .error-container {
            animation: fadeInUp 0.8s ease-out;
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="floating">
            <h1 class="error-code">404</h1>
        </div>
        
        <h2 class="error-title">
            <i class="fas fa-search me-2"></i>
            Page non trouvée
        </h2>
        
        <p class="error-subtitle">
            Désolé, la page que vous recherchez n'existe pas ou a été déplacée.<br>
            Mais ne vous inquiétez pas, nous pouvons vous aider à trouver ce que vous cherchez !
        </p>
        
        <!-- Barre de recherche -->
        <div class="search-container">
            <form action="{{ url('/') }}" method="GET" class="d-flex">
                <input type="text" 
                       name="search" 
                       class="search-input" 
                       placeholder="Rechercher une page, un service, ou des informations..."
                       value="{{ request('search') }}">
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        
        <!-- Boutons d'action -->
        <div class="action-buttons">
            <a href="{{ url('/') }}" class="btn-custom btn-primary-custom">
                <i class="fas fa-home"></i>
                Retour à l'accueil
            </a>
            
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-custom btn-outline-custom">
                    <i class="fas fa-tachometer-alt"></i>
                    Mon tableau de bord
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-custom btn-outline-custom">
                    <i class="fas fa-sign-in-alt"></i>
                    Se connecter
                </a>
            @endauth
        </div>
        
        <!-- Services populaires -->
        <div class="features">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-passport"></i>
                </div>
                <h3 class="feature-title">Profils Visa</h3>
                <p class="feature-desc">
                    Gérez vos demandes de visa en ligne facilement
                </p>
                @auth
                    <a href="{{ route('profil.visa.index') }}" class="btn-custom btn-outline-custom btn-sm mt-2">
                        <i class="fas fa-arrow-right"></i>
                        Accéder
                    </a>
                @endauth
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3 class="feature-title">Rendez-vous</h3>
                <p class="feature-desc">
                    Prenez rendez-vous pour vos démarches
                </p>
                @auth
                    <a href="{{ url('/rendez-vous') }}" class="btn-custom btn-outline-custom btn-sm mt-2">
                        <i class="fas fa-arrow-right"></i>
                        Planifier
                    </a>
                @endauth
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3 class="feature-title">Support</h3>
                <p class="feature-desc">
                    Notre équipe est là pour vous aider
                </p>
                <a href="{{ url('/contact') }}" class="btn-custom btn-outline-custom btn-sm mt-2">
                    <i class="fas fa-arrow-right"></i>
                    Contacter
                </a>
            </div>
        </div>
        
        <!-- Liens footer -->
        <div class="footer-links">
            <a href="{{ url('/') }}">Accueil</a>
            <a href="{{ url('/about') }}">À propos</a>
            <a href="{{ url('/services') }}">Services</a>
            <a href="{{ url('/contact') }}">Contact</a>
            <a href="{{ url('/faq') }}">FAQ</a>
        </div>
        
        <!-- Informations techniques pour les développeurs -->
        @if(config('app.debug'))
            <div class="mt-4 p-3" style="background: rgba(255, 193, 7, 0.1); border-radius: 10px; font-size: 0.9rem;">
                <strong>Information technique :</strong><br>
                URL demandée : <code>{{ request()->fullUrl() }}</code><br>
                Méthode : <code>{{ request()->method() }}</code><br>
                Utilisateur : <code>{{ auth()->check() ? auth()->user()->name . ' (' . auth()->user()->email . ')' : 'Non connecté' }}</code>
            </div>
        @endif
    </div>
    
    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Focus automatique sur la barre de recherche
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                searchInput.focus();
            }
        });
        
        // Animation des feature items
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeInUp 0.6s ease-out forwards';
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.feature-item').forEach(item => {
            item.style.opacity = '0';
            observer.observe(item);
        });
        
        // Raccourci clavier pour retourner à l'accueil
        document.addEventListener('keydown', function(e) {
            if (e.key === 'h' && e.ctrlKey) {
                e.preventDefault();
                window.location.href = '{{ url("/") }}';
            }
        });
        
        // Suggestion de pages similaires (simulation)
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            let timeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    const value = this.value.toLowerCase();
                    if (value.length > 2) {
                        // Ici vous pourriez implémenter une vraie recherche AJAX
                        console.log('Recherche de suggestions pour:', value);
                    }
                }, 300);
            });
        }
        
        // Analytics (si configuré)
        @if(config('services.google_analytics.tracking_id'))
            gtag('event', 'page_view', {
                page_title: '404 Error',
                page_location: window.location.href,
                custom_map: {
                    'dimension1': 'Error Page'
                }
            });
        @endif
    </script>
</body>
</html>