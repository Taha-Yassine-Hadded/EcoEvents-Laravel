<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notification->subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .notification-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔔 Echofy</h1>
        <p>Notification Sponsor</p>
    </div>
    
    <div class="content">
        <div class="notification-content">
            <h2>{{ $notification->subject }}</h2>
            <div style="white-space: pre-line;">{{ $notification->content }}</div>
        </div>
        
        <div style="text-align: center;">
            <a href="{{ url('/sponsor/dashboard') }}" class="btn">Accéder au Dashboard</a>
        </div>
        
        <div class="footer">
            <p>Cette notification a été envoyée automatiquement par Echofy.</p>
            <p>Si vous ne souhaitez plus recevoir ces notifications, vous pouvez modifier vos préférences dans votre profil.</p>
        </div>
    </div>
</body>
</html>
