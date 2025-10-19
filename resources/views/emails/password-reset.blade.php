<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de récupération - EcoEvents</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: bold;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .code-box {
            background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
            border: 2px dashed #28a745;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        
        .reset-code {
            font-size: 3rem;
            font-weight: bold;
            color: #28a745;
            letter-spacing: 0.5rem;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
        }
        
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .eco-icon {
            color: #28a745;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🌱 EcoEvents</h1>
            <p>Récupération de mot de passe</p>
        </div>
        
        <div class="content">
            <h2>Bonjour {{ $user->name ?? 'Utilisateur' }},</h2>
            
            <p>Vous avez demandé la réinitialisation de votre mot de passe pour votre compte EcoEvents.</p>
            
            <p>Voici votre code de récupération :</p>
            
            <div class="code-box">
                <p><strong>Code de récupération</strong></p>
                <div class="reset-code">{{ $resetCode }}</div>
                <p><small>Ce code est valide pendant 15 minutes</small></p>
            </div>
            
            <div class="warning-box">
                <strong>⚠️ Important :</strong>
                <ul style="margin: 10px 0;">
                    <li>Ce code expire dans <strong>15 minutes</strong></li>
                    <li>Ne partagez jamais ce code avec personne</li>
                    <li>Si vous n'avez pas demandé cette réinitialisation, ignorez cet email</li>
                </ul>
            </div>
            
            <p>Pour compléter la réinitialisation :</p>
            <ol>
                <li>Retournez sur la page de récupération</li>
                <li>Entrez le code ci-dessus</li>
                <li>Choisissez votre nouveau mot de passe</li>
            </ol>
            
            <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>
            
            <p>Cordialement,<br>
            <strong>L'équipe EcoEvents</strong></p>
        </div>
        
        <div class="footer">
            <p>
                <span class="eco-icon">🌍</span> 
                EcoEvents - Ensemble pour un avenir durable
            </p>
            <p>
                Cet email a été envoyé automatiquement, merci de ne pas y répondre.
            </p>
        </div>
    </div>
</body>
</html>
