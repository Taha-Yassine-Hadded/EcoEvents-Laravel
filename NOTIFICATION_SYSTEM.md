# 🔔 Système de Notifications pour Sponsors

## Vue d'ensemble

Le système de notifications Echofy permet d'envoyer des notifications automatiques aux sponsors via différents canaux (email, SMS, push, in-app) selon leurs préférences.

## 🚀 Fonctionnalités

### 1. **Types de Notifications**
- **Sponsorship créé** : Confirmation de création d'un sponsorship
- **Sponsorship approuvé** : Notification d'approbation
- **Paiement dû** : Rappel de paiement
- **Événement bientôt** : Rappel 24h avant l'événement
- **Contrat expirant** : Rappel 30 jours avant expiration
- **Rapport mensuel** : Statistiques mensuelles
- **Maintenance système** : Alertes système
- **Offres marketing** : Promotions spéciales

### 2. **Canaux de Notification**
- **📧 Email** : Notifications détaillées avec template HTML
- **📱 SMS** : Messages courts pour alertes critiques
- **🔔 Push** : Notifications mobiles
- **💻 In-App** : Notifications dans l'interface

### 3. **Préférences Personnalisables**
- Chaque sponsor peut choisir ses canaux préférés
- Horaires de réception personnalisables
- Types de notifications activables/désactivables

## 📋 Utilisation

### 1. **Envoi Manuel de Notifications**

```php
use App\Services\NotificationService;

$notificationService = app(NotificationService::class);

// Envoyer une notification à un sponsor
$notificationService->sendNotification(
    $user, // Utilisateur sponsor
    'sponsorship_created', // Type de notification
    [
        'user_name' => $user->name,
        'event_title' => 'Mon Événement',
        'package_name' => 'Package Premium',
        'amount' => 1500
    ],
    ['email', 'in_app'] // Canaux
);

// Envoyer à plusieurs sponsors
$notificationService->sendBulkNotification(
    [1, 2, 3], // IDs des utilisateurs
    'event_starting_soon',
    $data,
    ['email', 'push', 'sms']
);
```

### 2. **Notifications Automatiques**

Les notifications sont automatiquement envoyées lors de certains événements :

```php
// Dans le modèle SponsorshipTemp
protected static function boot()
{
    parent::boot();

    // Notification automatique lors de la création
    static::created(function ($sponsorship) {
        $notificationService = app(NotificationService::class);
        $notificationService->sendNotification(
            $sponsorship->user,
            'sponsorship_created',
            [
                'user_name' => $sponsorship->user->name,
                'event_title' => $sponsorship->event_title,
                'package_name' => $sponsorship->package_name,
                'amount' => $sponsorship->amount
            ],
            ['email', 'in_app']
        );
    });
}
```

### 3. **Commandes Artisan**

```bash
# Tester les notifications
php artisan notifications:test

# Tester pour un utilisateur spécifique
php artisan notifications:test --user-id=1

# Envoyer des rappels automatiques
php artisan sponsors:send-reminders

# Programmer les rappels
php artisan sponsors:schedule-reminders
```

### 4. **API Endpoints**

```javascript
// Obtenir les notifications
GET /api/sponsor/notifications

// Obtenir les notifications non lues
GET /api/sponsor/notifications/unread

// Marquer comme lu
POST /api/sponsor/notifications/{id}/read

// Marquer tout comme lu
POST /api/sponsor/notifications/mark-all-read

// Obtenir les préférences
GET /api/sponsor/notifications/preferences

// Mettre à jour les préférences
PUT /api/sponsor/notifications/preferences

// Supprimer une notification
DELETE /api/sponsor/notifications/{id}
```

## 🎨 Templates de Notifications

### 1. **Créer un Nouveau Template**

```php
use App\Models\NotificationTemplate;

NotificationTemplate::create([
    'name' => 'Mon Template',
    'type' => 'email',
    'trigger_event' => 'custom_event',
    'subject' => 'Sujet: {{user_name}}',
    'content' => 'Bonjour {{user_name}},\n\nContenu avec {{variable}}...',
    'variables' => ['user_name', 'variable'],
    'is_active' => true
]);
```

### 2. **Variables Disponibles**

- `{{user_name}}` : Nom de l'utilisateur
- `{{event_title}}` : Titre de l'événement
- `{{package_name}}` : Nom du package
- `{{amount}}` : Montant
- `{{event_date}}` : Date de l'événement
- `{{event_location}}` : Lieu de l'événement
- `{{time_remaining}}` : Temps restant
- `{{due_date}}` : Date d'échéance

## 🔧 Configuration

### 1. **Services Externes**

Pour activer les SMS et push notifications, configurez :

```env
# SMS (Twilio)
TWILIO_SID=your_sid
TWILIO_TOKEN=your_token
TWILIO_FROM=your_phone_number

# Push Notifications (Firebase)
FIREBASE_SERVER_KEY=your_server_key
```

### 2. **Planification des Rappels**

Ajoutez dans `app/Console/Kernel.php` :

```php
protected function schedule(Schedule $schedule)
{
    // Rappels quotidiens à 9h
    $schedule->command('sponsors:send-reminders')
             ->dailyAt('09:00');
    
    // Rapports mensuels le 1er du mois
    $schedule->command('sponsors:schedule-reminders')
             ->monthlyOn(1, '08:00');
}
```

## 📊 Interface Utilisateur

### 1. **Page des Notifications**
- Liste des notifications avec filtres
- Marquer comme lu/non lu
- Supprimer les notifications
- Gestion des préférences

### 2. **Compteur en Temps Réel**
- Badge avec nombre de notifications non lues
- Mise à jour automatique toutes les 30 secondes
- Animation lors de nouvelles notifications

## 🚨 Exemples d'Utilisation

### 1. **Rappel de Paiement**
```php
// Envoyer un rappel de paiement
$notificationService->sendNotification(
    $sponsor,
    'payment_due',
    [
        'user_name' => $sponsor->name,
        'event_title' => 'Tech Conference 2024',
        'amount' => 2500,
        'due_date' => '15/12/2024'
    ],
    ['email', 'sms'] // Email + SMS pour les paiements
);
```

### 2. **Alerte d'Événement**
```php
// Rappel 24h avant l'événement
$notificationService->sendNotification(
    $sponsor,
    'event_starting_soon',
    [
        'user_name' => $sponsor->name,
        'event_title' => 'Tech Conference 2024',
        'time_remaining' => '24 heures',
        'event_date' => '20/12/2024 14:00',
        'event_location' => 'Paris, France',
        'package_name' => 'Package Premium'
    ],
    ['email', 'push', 'sms'] // Tous les canaux pour les alertes critiques
);
```

### 3. **Rapport Mensuel**
```php
// Rapport mensuel avec statistiques
$notificationService->sendNotification(
    $sponsor,
    'monthly_report',
    [
        'user_name' => $sponsor->name,
        'month_year' => 'Novembre 2024',
        'active_sponsorships' => 3,
        'events_count' => 2,
        'total_invested' => 7500,
        'impressions' => 15000,
        'clicks' => 450,
        'ctr' => 3.0,
        'roi' => 180
    ],
    ['email'] // Seulement email pour les rapports
);
```

## 🔍 Monitoring et Logs

### 1. **Statut des Notifications**
- `pending` : En attente d'envoi
- `sent` : Envoyée
- `delivered` : Livrée
- `failed` : Échec
- `read` : Lue

### 2. **Logs**
```php
// Les erreurs sont automatiquement loggées
Log::error("Failed to send notification: " . $e->getMessage());
```

## 🎯 Bonnes Pratiques

1. **Respecter les préférences** : Toujours vérifier les préférences avant l'envoi
2. **Variables obligatoires** : S'assurer que toutes les variables sont fournies
3. **Canaux appropriés** : Utiliser les bons canaux selon l'urgence
4. **Tests réguliers** : Tester le système avec `php artisan notifications:test`
5. **Monitoring** : Surveiller les échecs d'envoi
6. **Templates** : Créer des templates clairs et informatifs

## 🚀 Prochaines Améliorations

- [ ] Intégration WhatsApp Business
- [ ] Notifications vocales
- [ ] IA pour personnalisation des messages
- [ ] Analytics avancés des notifications
- [ ] Templates visuels avec images
- [ ] Notifications géolocalisées
- [ ] Intégration Slack/Discord
- [ ] Système de feedback sur les notifications
