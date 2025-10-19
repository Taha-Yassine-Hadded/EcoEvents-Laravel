<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation d'inscription - {{ $event->title }}</title>
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
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 25px;
            border-radius: 8px;
            border-left: 4px solid #28a745;
            margin: 20px 0;
        }
        .event-details {
            background-color: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .event-details h3 {
            color: #28a745;
            margin-top: 0;
        }
        .detail-row {
            display: flex;
            margin: 10px 0;
            align-items: center;
        }
        .detail-icon {
            width: 20px;
            margin-right: 10px;
            color: #28a745;
        }
        .registration-info {
            background-color: #e8f5e8;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
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
        .cta-button.black-style {
            background: #fff !important;
            color: #000 !important;
            border: 2px solid #000 !important;
        }
        .cta-button.black-style:hover {
            background: #f8f9fa !important;
            color: #000 !important;
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
            .header, .content, .footer {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">

        <!-- Content -->
        <div class="content">
            <h2>Bonjour {{ $user->name }} ! 👋</h2>
            
            <div class="success-message">
                <p><strong>Félicitations !</strong> Votre inscription à l'événement <strong>"{{ $event->title }}"</strong> a été confirmée avec succès.</p>
            </div>

            <!-- Event Details -->
            <div class="event-details">
                
                <div class="detail-row">
                    <span class="detail-icon">📝</span>
                    <strong>Titre :</strong> {{ $event->title }}
                </div>
                
                <div class="detail-row">
                    <span class="detail-icon">📅</span>
                    <strong>Date :</strong> {{ $event->date ? $event->date->format('d M Y à H:i') : 'Date non définie' }}
                </div>
                
                <div class="detail-row">
                    <span class="detail-icon">📍</span>
                    <strong>Lieu :</strong> {{ $event->location }}
                </div>
                
                @if($event->organizer)
                <div class="detail-row">
                    <span class="detail-icon">👤</span>
                    <strong>Organisateur :</strong> {{ $event->organizer->name }}
                </div>
                @endif
                
                @if($event->capacity)
                <div class="detail-row">
                    <span class="detail-icon">👥</span>
                    <strong>Capacité :</strong> {{ $event->capacity }} personnes
                </div>
                @endif
            </div>

            <!-- Registration Information -->
            <div class="registration-info">
                <h3 style="color: #28a745; margin-top: 0;">📋 Vos informations d'inscription</h3>
                
                <div class="detail-row">
                    <span class="detail-icon">🎭</span>
                    <strong>Rôle de bénévole :</strong> {{ $registration->role ?? 'Non spécifié' }}
                </div>
                
                <div class="detail-row">
                    <span class="detail-icon">🛠️</span>
                    <strong>Vos compétences :</strong> {{ $registration->skills ?? 'Non spécifiées' }}
                </div>
                
                @if($registration->has_transportation)
                <div class="detail-row">
                    <span class="detail-icon">🚗</span>
                    <strong>Transport :</strong> Vous avez votre propre moyen de transport
                </div>
                @endif
                
                @if($registration->has_participated_before)
                <div class="detail-row">
                    <span class="detail-icon">⭐</span>
                    <strong>Expérience :</strong> Vous avez déjà participé à des éco-événements
                </div>
                @endif
                
                @if($registration->emergency_contact)
                <div class="detail-row">
                    <span class="detail-icon">🚨</span>
                    <strong>Contact d'urgence :</strong> {{ $registration->emergency_contact }}
                </div>
                @endif
            </div>

            <div style="text-align: center; margin: 40px 0;">
                <a href="{{ route('front.events.show', $event->id) }}" class="cta-button black-style">
                    📖 Voir les détails de l'événement
                </a>
            </div>

            <div style="background-color: #fff3cd; padding: 20px; border-radius: 8px; margin: 30px 0; border-left: 4px solid #ffc107;">
                <h3 style="color: #856404; margin-top: 0;">📝 Prochaines étapes :</h3>
                <ul style="margin: 0; padding-left: 20px; color: #856404;">
                    <li>L'organisateur vous contactera avec plus de détails</li>
                    <li>Préparez-vous selon votre rôle de bénévole</li>
                    <li>Arrivez 15 minutes avant le début de l'événement</li>
                    <li>Apportez votre bonne humeur et votre engagement écologique !</li>
                </ul>
            </div>

            <p>Si vous avez des questions concernant cet événement, n'hésitez pas à contacter l'organisateur ou notre équipe de support.</p>

            <p style="margin-top: 30px;">
                Merci pour votre engagement écologique !<br>
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
                Vous recevez cet email car vous vous êtes inscrit à un événement sur EcoEvents.<br>
                Si vous n'êtes pas à l'origine de cette inscription, veuillez nous contacter.
            </p>
        </div>
    </div>
</body>
</html>
