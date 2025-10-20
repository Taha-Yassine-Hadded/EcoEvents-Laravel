# 🚀 API MÉTIER POUR LA GESTION DES SPONSORS - ECHOFY

## 📋 **Vue d'ensemble**

Cette API métier complète offre **plus de 80 endpoints** spécialisés pour la gestion avancée des sponsors dans le système Echofy. Elle couvre tous les aspects métier : authentification, profil, sponsoring, contrats, analytics, recommandations IA, notifications, et administration.

---

## 🔐 **AUTHENTIFICATION**

### **Base URL**
```
/api/auth/sponsor/
```

### **Endpoints d'authentification**
- `POST /register` - Enregistrement d'un nouveau sponsor
- `POST /login` - Connexion sponsor
- `POST /logout` - Déconnexion (nécessite token)
- `POST /refresh` - Rafraîchir le token JWT
- `POST /forgot-password` - Mot de passe oublié
- `POST /reset-password` - Réinitialisation du mot de passe

### **Exemple d'enregistrement**
```json
POST /api/auth/sponsor/register
{
    "name": "John Doe",
    "email": "john@company.com",
    "password": "SecurePass123!",
    "password_confirmation": "SecurePass123!",
    "company_name": "EcoTech Solutions",
    "phone": "+33123456789",
    "city": "Paris",
    "address": "123 Rue de la Paix",
    "bio": "Entreprise spécialisée dans les solutions écologiques",
    "interests": ["Environnement", "Technologie", "Innovation"],
    "budget": 50000,
    "sector": "Technologie"
}
```

---

## 👤 **GESTION DU PROFIL**

### **Base URL**
```
/api/sponsors/
```

### **Endpoints de profil**
- `GET /profile` - Récupérer le profil complet
- `PUT /profile` - Mettre à jour le profil
- `POST /profile/avatar` - Upload d'avatar
- `DELETE /profile/avatar` - Supprimer l'avatar
- `PUT /profile/password` - Changer le mot de passe

### **Endpoints entreprise**
- `GET /company` - Informations de l'entreprise
- `PUT /company` - Mettre à jour l'entreprise
- `POST /company/logo` - Upload du logo
- `DELETE /company/logo` - Supprimer le logo

### **Exemple de mise à jour de profil**
```json
PUT /api/sponsors/profile
{
    "name": "John Doe Updated",
    "phone": "+33987654321",
    "city": "Lyon",
    "bio": "Nouvelle description mise à jour",
    "interests": ["Environnement", "Développement durable"],
    "budget": 75000
}
```

---

## 🤝 **GESTION DES SPONSORING**

### **Base URL**
```
/api/sponsorships/
```

### **CRUD Operations**
- `GET /` - Liste des sponsoring (avec filtres)
- `GET /{id}` - Détails d'un sponsoring
- `POST /` - Créer un nouveau sponsoring
- `PUT /{id}` - Mettre à jour un sponsoring
- `DELETE /{id}` - Supprimer un sponsoring

### **Gestion des statuts**
- `POST /{id}/cancel` - Annuler un sponsoring
- `POST /{id}/reactivate` - Réactiver un sponsoring

### **Operations en masse**
- `POST /bulk-cancel` - Annulation en masse
- `POST /bulk-update` - Mise à jour en masse

### **Filtrage et recherche**
- `GET /search/{query}` - Recherche textuelle
- `GET /filter/by-status/{status}` - Filtrer par statut
- `GET /filter/by-event/{eventId}` - Filtrer par événement
- `GET /filter/by-date-range` - Filtrer par période

### **Export et rapports**
- `GET /export/csv` - Export CSV
- `GET /export/pdf` - Export PDF
- `GET /report/summary` - Rapport de synthèse

### **Exemple de création de sponsoring**
```json
POST /api/sponsorships/
{
    "event_id": 15,
    "package_id": 3,
    "amount": 2500.00,
    "notes": "Sponsoring pour l'événement Green Tech Summit"
}
```

---

## 📄 **GESTION DES CONTRATS**

### **Base URL**
```
/api/contracts/
```

### **Endpoints de contrats**
- `GET /` - Liste des contrats
- `GET /{id}` - Détails d'un contrat
- `GET /{id}/download` - Télécharger un contrat
- `GET /{id}/view` - Visualiser un contrat

### **Actions sur les contrats**
- `POST /{id}/sign` - Signer un contrat
- `POST /{id}/request-changes` - Demander des modifications
- `POST /{id}/approve` - Approuver un contrat

### **Operations en masse**
- `POST /bulk-download` - Téléchargement en masse
- `GET /export/all` - Export de tous les contrats

---

## 📊 **ANALYTICS & REPORTING**

### **Base URL**
```
/api/analytics/
```

### **Dashboard et vue d'ensemble**
- `GET /dashboard` - Données du dashboard
- `GET /overview` - Vue d'ensemble complète

### **Métriques de performance**
- `GET /performance` - Métriques de performance
- `GET /roi-analysis` - Analyse ROI détaillée
- `GET /success-rate` - Taux de succès
- `GET /financial-summary` - Résumé financier
- `GET /budget-utilization` - Utilisation du budget
- `GET /investment-trends` - Tendances d'investissement

### **Analytics événements**
- `GET /event-performance` - Performance des événements
- `GET /category-analysis` - Analyse par catégorie
- `GET /geographic-analysis` - Analyse géographique

### **Rapports temporels**
- `GET /monthly-report` - Rapport mensuel
- `GET /yearly-report` - Rapport annuel
- `GET /custom-date-range` - Rapport personnalisé

### **Données pour graphiques**
- `GET /charts/sponsorship-trends` - Graphique des tendances
- `GET /charts/roi-distribution` - Distribution ROI
- `GET /charts/event-categories` - Catégories d'événements
- `GET /charts/budget-allocation` - Allocation du budget

### **Exemple d'analyse ROI**
```json
GET /api/analytics/roi-analysis
Response:
{
    "success": true,
    "data": {
        "total_investment": 25000.00,
        "estimated_return": 28750.00,
        "roi_percentage": 15.0,
        "best_performing_events": [...],
        "roi_by_category": [...]
    }
}
```

---

## 🤖 **RECOMMANDATIONS IA**

### **Base URL**
```
/api/recommendations/
```

### **Endpoints de recommandations**
- `GET /events` - Recommandations d'événements
- `GET /events/personalized` - Recommandations personnalisées
- `GET /events/similar-sponsors` - Basées sur sponsors similaires

### **Préférences et feedback**
- `GET /preferences` - Préférences de recommandation
- `PUT /preferences` - Mettre à jour les préférences
- `POST /feedback` - Soumettre du feedback
- `GET /feedback/history` - Historique du feedback

### **Smart Matching**
- `GET /smart-match` - Correspondances intelligentes
- `POST /smart-match/refresh` - Actualiser les correspondances

### **Exemple de recommandations**
```json
GET /api/recommendations/events?limit=5
Response:
{
    "success": true,
    "data": {
        "recommendations": [
            {
                "event": {...},
                "score": 85.5,
                "reasons": ["Secteur compatible", "Budget adapté"],
                "estimated_roi": 1.2,
                "risk_level": "low"
            }
        ],
        "total": 5
    }
}
```

---

## 🔔 **GESTION DES NOTIFICATIONS**

### **Base URL**
```
/api/notifications/
```

### **CRUD Notifications**
- `GET /` - Liste des notifications
- `GET /unread` - Notifications non lues
- `GET /{id}` - Détails d'une notification
- `PUT /{id}/mark-read` - Marquer comme lue
- `PUT /{id}/mark-unread` - Marquer comme non lue
- `DELETE /{id}` - Supprimer une notification

### **Operations en masse**
- `POST /mark-all-read` - Tout marquer comme lu
- `POST /mark-all-unread` - Tout marquer comme non lu
- `DELETE /bulk-delete` - Suppression en masse

### **Préférences**
- `GET /preferences` - Préférences de notification
- `PUT /preferences` - Mettre à jour les préférences
- `GET /types` - Types de notifications
- `GET /templates` - Templates disponibles

---

## 🎯 **DÉCOUVERTE D'ÉVÉNEMENTS**

### **Base URL**
```
/api/events/
```

### **Découverte d'événements**
- `GET /discover` - Découvrir des événements
- `GET /featured` - Événements en vedette
- `GET /upcoming` - Événements à venir
- `GET /recommended` - Événements recommandés

### **Détails et filtrage**
- `GET /{id}` - Détails d'un événement
- `GET /{id}/packages` - Packages d'un événement
- `GET /{id}/sponsors` - Sponsors d'un événement
- `GET /filter/by-category/{categoryId}` - Filtrer par catégorie
- `GET /filter/by-location` - Filtrer par localisation
- `GET /filter/by-budget` - Filtrer par budget
- `GET /filter/by-date` - Filtrer par date

### **Recherche**
- `GET /search/{query}` - Recherche d'événements
- `GET /search/advanced` - Recherche avancée

---

## 👨‍💼 **ADMINISTRATION**

### **Base URL**
```
/api/admin/sponsors/
```

### **Gestion des sponsors**
- `GET /` - Liste des sponsors (admin)
- `GET /{id}` - Détails d'un sponsor (admin)
- `PUT /{id}` - Mettre à jour un sponsor (admin)
- `DELETE /{id}` - Supprimer un sponsor (admin)

### **Gestion des statuts**
- `POST /{id}/approve` - Approuver un sponsor
- `POST /{id}/reject` - Rejeter un sponsor
- `POST /{id}/suspend` - Suspendre un sponsor
- `POST /{id}/activate` - Activer un sponsor

### **Operations en masse**
- `POST /bulk-approve` - Approbation en masse
- `POST /bulk-reject` - Rejet en masse
- `POST /bulk-suspend` - Suspension en masse

### **Analytics admin**
- `GET /analytics/overview` - Vue d'ensemble admin
- `GET /analytics/sponsor-performance` - Performance des sponsors
- `GET /analytics/financial-summary` - Résumé financier admin

### **Rapports admin**
- `GET /reports/sponsor-list` - Liste des sponsors
- `GET /reports/sponsorship-summary` - Résumé des sponsoring
- `GET /reports/export/sponsors` - Export des sponsors

---

## 🌐 **API PUBLIQUE**

### **Base URL**
```
/api/public/sponsors/
```

### **Endpoints publics**
- `GET /featured` - Sponsors en vedette
- `GET /{id}/public-profile` - Profil public d'un sponsor
- `GET /search` - Recherche publique
- `GET /events/upcoming` - Événements à venir (public)
- `GET /events/{id}/public` - Détails d'événement (public)

---

## 🔗 **WEBHOOKS & INTÉGRATIONS**

### **Base URL**
```
/api/webhooks/
```

### **Webhooks de paiement**
- `POST /payment/success` - Paiement réussi
- `POST /payment/failed` - Paiement échoué

### **Webhooks externes**
- `POST /external/event-update` - Mise à jour d'événement externe
- `POST /external/sponsor-update` - Mise à jour de sponsor externe

---

## 📚 **DOCUMENTATION & SANTÉ**

### **Base URL**
```
/api/api/
```

### **Endpoints utilitaires**
- `GET /health` - Vérification de santé de l'API
- `GET /docs` - Documentation de l'API

---

## 🔧 **CONFIGURATION**

### **Middleware requis**
- `auth:api` - Authentification JWT
- `role:sponsor` - Rôle sponsor requis
- `role:admin` - Rôle admin requis (pour endpoints admin)

### **Headers requis**
```
Authorization: Bearer {jwt_token}
Content-Type: application/json
Accept: application/json
```

### **Codes de réponse**
- `200` - Succès
- `201` - Créé avec succès
- `400` - Requête invalide
- `401` - Non authentifié
- `403` - Non autorisé
- `404` - Non trouvé
- `422` - Erreur de validation
- `500` - Erreur serveur

---

## 🚀 **EXEMPLES D'UTILISATION**

### **1. Flux complet d'un sponsor**
```bash
# 1. Enregistrement
POST /api/auth/sponsor/register

# 2. Connexion
POST /api/auth/sponsor/login

# 3. Découverte d'événements
GET /api/events/discover

# 4. Création de sponsoring
POST /api/sponsorships/

# 5. Suivi des analytics
GET /api/analytics/dashboard

# 6. Gestion des contrats
GET /api/contracts/
```

### **2. Analytics avancées**
```bash
# Analyse ROI
GET /api/analytics/roi-analysis

# Rapport mensuel
GET /api/analytics/monthly-report?month=2025-01

# Graphiques
GET /api/analytics/charts/sponsorship-trends
```

### **3. Recommandations IA**
```bash
# Recommandations personnalisées
GET /api/recommendations/events/personalized

# Feedback sur recommandations
POST /api/recommendations/feedback
```

---

## 🎯 **AVANTAGES DE CETTE API**

### **✅ Fonctionnalités Avancées**
- **80+ endpoints** spécialisés
- **Analytics prédictives** avec IA
- **Recommandations intelligentes**
- **Gestion complète des contrats**
- **Notifications multi-canaux**
- **Export et rapports avancés**

### **✅ Sécurité Enterprise**
- **JWT Authentication** robuste
- **Middleware de rôles** granulaire
- **Validation** multi-niveaux
- **Logging** complet

### **✅ Performance**
- **Pagination** intelligente
- **Filtrage** avancé
- **Cache** optimisé
- **Requêtes** optimisées

### **✅ Extensibilité**
- **Architecture modulaire**
- **Webhooks** pour intégrations
- **API publique** pour partenaires
- **Documentation** complète

---

## 🔮 **ROADMAP FUTURE**

### **Phase 2 - Intégrations**
- Intégration Stripe/PayPal
- Webhooks Slack/Discord
- API mobile React Native
- Intégration CRM externe

### **Phase 3 - IA Avancée**
- Machine Learning pour recommandations
- Prédiction de succès des événements
- Analyse de sentiment des sponsors
- Optimisation automatique des budgets

### **Phase 4 - Analytics Avancées**
- Tableaux de bord temps réel
- Alertes intelligentes
- Prédictions financières
- Benchmarking concurrentiel

---

**Cette API métier transforme Echofy en une plateforme SaaS enterprise complète pour la gestion des sponsors !** 🚀
