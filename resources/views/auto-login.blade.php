<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Auto - Echofy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-sign-in-alt me-2"></i>Connexion Automatique</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Mode Test :</strong> Choisissez un compte de test pour vous connecter automatiquement.
                        </div>

                        <div class="d-grid gap-3">
                            <button class="btn btn-outline-primary btn-lg" onclick="loginAs('organizer@test.com', 'password')">
                                <i class="fas fa-user-tie me-2"></i>
                                Se connecter comme <strong>Organisateur</strong>
                            </button>
                            
                            <button class="btn btn-outline-success btn-lg" onclick="loginAs('user1@test.com', 'password')">
                                <i class="fas fa-user me-2"></i>
                                Se connecter comme <strong>Utilisateur 1</strong>
                            </button>
                            
                            <button class="btn btn-outline-info btn-lg" onclick="loginAs('user2@test.com', 'password')">
                                <i class="fas fa-user me-2"></i>
                                Se connecter comme <strong>Utilisateur 2</strong>
                            </button>
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <a href="/communities" class="btn btn-primary">
                                <i class="fas fa-users me-2"></i>Voir les Communautés
                            </a>
                            <a href="/test-chat-simple" class="btn btn-outline-secondary">
                                <i class="fas fa-comments me-2"></i>Test Chat Simple
                            </a>
                            <a href="/test-chat-debug" class="btn btn-outline-secondary">
                                <i class="fas fa-bug me-2"></i>Debug Chat
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function loginAs(email, password) {
            // Créer un formulaire de connexion automatique
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/login';
            
            // Ajouter le token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.content;
                form.appendChild(csrfInput);
            }
            
            // Ajouter l'email
            const emailInput = document.createElement('input');
            emailInput.type = 'hidden';
            emailInput.name = 'email';
            emailInput.value = email;
            form.appendChild(emailInput);
            
            // Ajouter le mot de passe
            const passwordInput = document.createElement('input');
            passwordInput.type = 'hidden';
            passwordInput.name = 'password';
            passwordInput.value = password;
            form.appendChild(passwordInput);
            
            // Soumettre le formulaire
            document.body.appendChild(form);
            form.submit();
        }

        // Vérifier si déjà connecté
        document.addEventListener('DOMContentLoaded', function() {
            // Si on est déjà sur une page avec authentification, rediriger
            if (window.location.pathname.includes('/communities') || 
                window.location.pathname.includes('/chat')) {
                return;
            }
        });
    </script>
</body>
</html>
