<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur EcoEvents</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            background-color: #f8f9fa;
        }
        .container {
            background-color: white;
            padding: 0;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .welcome-message {
            background-color: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            border-left: 4px solid #28a745;
            margin: 20px 0;
        }
        .features {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin: 30px 0;
        }
        .feature {
            flex: 1;
            min-width: 200px;
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .feature-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            margin: 20px 0;
            transition: transform 0.2s ease;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            color: white;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .social-links {
            margin: 20px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #28a745;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            .features {
                flex-direction: column;
            }
            .header, .content, .footer {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🌱 Bienvenue sur EcoEvents !</h1>
            <p>Votre aventure écologique commence maintenant</p>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Bonjour {{ $user->name }} ! 👋</h2>
            
            <div class="welcome-message">
                <p><strong>Félicitations !</strong> Votre compte a été créé avec succès sur EcoEvents, la plateforme dédiée aux événements écologiques et aux communautés durables.</p>
            </div>

            <p>En tant que <strong>{{ $user->role === 'organizer' ? 'organisateur' : 'participant' }}</strong>, vous pouvez maintenant :</p>

            <div class="features">
                @if($user->role === 'organizer')
                    <div class="feature">
                        <div class="feature-icon">🎯</div>
                        <h3>Créer des événements</h3>
                        <p>Organisez des événements écologiques et mobilisez votre communauté</p>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">👥</div>
                        <h3>Gérer des communautés</h3>
                        <p>Créez et animez des communautés passionnées par l'écologie</p>
                    </div>
                @else
                    <div class="feature">
                        <div class="feature-icon">🎪</div>
                        <h3>Participer aux événements</h3>
                        <p>Découvrez et rejoignez des événements écologiques près de chez vous</p>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">🌍</div>
                        <h3>Rejoindre des communautés</h3>
                        <p>Connectez-vous avec des personnes partageant vos valeurs</p>
                    </div>
                @endif
            </div>

            <div style="text-align: center; margin: 40px 0;">
                <a href="{{ url('/') }}" class="cta-button">
                    🚀 Commencer l'exploration
                </a>
            </div>

            <div style="background-color: #e8f5e8; padding: 20px; border-radius: 8px; margin: 30px 0;">
                <h3 style="color: #28a745; margin-top: 0;">💡 Conseils pour bien commencer :</h3>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>Complétez votre profil pour une meilleure expérience</li>
                    <li>Explorez les communautés qui vous intéressent</li>
                    <li>Participez aux discussions et événements</li>
                    <li>Partagez vos initiatives écologiques</li>
                </ul>
            </div>

            <p>Si vous avez des questions, n'hésitez pas à nous contacter. Notre équipe est là pour vous accompagner dans votre démarche écologique !</p>

            <p style="margin-top: 30px;">
                Écologiquement vôtre,<br>
                <strong>L'équipe EcoEvents</strong> 🌱
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 10px 0; color: #666;">
                <strong>EcoEvents</strong> - La plateforme des événements écologiques
            </p>
            
            <div class="social-links">
                <a href="#">📧 Contact</a>
                <a href="#">🌐 Site Web</a>
                <a href="#">📱 Application</a>
            </div>
            
            <p style="font-size: 12px; color: #999; margin: 20px 0 0 0;">
                Vous recevez cet email car vous venez de créer un compte sur EcoEvents.<br>
                Si vous n'êtes pas à l'origine de cette inscription, veuillez nous contacter.
            </p>
        </div>
    </div>
</body>
</html>
