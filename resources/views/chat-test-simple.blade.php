<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Chat Simple - Echofy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .chat-container { height: 500px; border: 1px solid #ddd; border-radius: 10px; }
        .messages-area { height: 400px; overflow-y: auto; padding: 10px; background: #f8f9fa; }
        .message { margin: 10px 0; padding: 10px; border-radius: 10px; }
        .own-message { background: #007bff; color: white; margin-left: 20%; }
        .other-message { background: white; margin-right: 20%; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-comments me-2"></i>Test Chat Simple</h4>
                        <small>Test sans authentification JWT</small>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Mode Test :</strong> Cette version fonctionne sans authentification pour tester la connectivité.
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Utilisateur de test :</label>
                                <select id="test-user" class="form-select">
                                    <option value="1">Organisateur Test</option>
                                    <option value="2">Utilisateur 1</option>
                                    <option value="3">Utilisateur 2</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Statut de connexion :</label>
                                <div id="connection-status" class="badge bg-secondary">Non connecté</div>
                            </div>
                        </div>

                        <div class="chat-container">
                            <div id="messages-area" class="messages-area">
                                <div class="text-center text-muted">
                                    <i class="fas fa-comments fa-2x mb-3"></i>
                                    <p>Messages du chat apparaîtront ici...</p>
                                </div>
                            </div>
                            <div class="input-group p-3">
                                <input type="text" id="message-input" class="form-control" placeholder="Tapez votre message..." maxlength="500">
                                <button class="btn btn-primary" id="send-button" onclick="sendMessage()">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="d-grid gap-2 d-md-flex">
                                <button class="btn btn-success" onclick="connectToChat()">
                                    <i class="fas fa-plug me-2"></i>Se connecter au chat
                                </button>
                                <button class="btn btn-outline-danger" onclick="disconnectFromChat()">
                                    <i class="fas fa-unlink me-2"></i>Se déconnecter
                                </button>
                                <button class="btn btn-outline-secondary" onclick="clearMessages()">
                                    <i class="fas fa-trash me-2"></i>Effacer messages
                                </button>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h6>Console de Debug :</h6>
                            <div id="debug-console" class="bg-dark text-light p-2 rounded" style="height: 150px; overflow-y: auto; font-family: monospace; font-size: 12px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentUser = null;
        let isConnected = false;
        let messageCount = 0;

        function log(message) {
            const console = document.getElementById('debug-console');
            const timestamp = new Date().toLocaleTimeString();
            console.innerHTML += `<div>[${timestamp}] ${message}</div>`;
            console.scrollTop = console.scrollHeight;
        }

        function updateConnectionStatus(status, isConnected) {
            const statusElement = document.getElementById('connection-status');
            statusElement.textContent = status;
            statusElement.className = isConnected ? 'badge bg-success' : 'badge bg-danger';
        }

        function addMessage(content, isOwn = false) {
            const messagesArea = document.getElementById('messages-area');
            const emptyMessage = messagesArea.querySelector('.text-center');
            if (emptyMessage) {
                emptyMessage.remove();
            }

            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isOwn ? 'own-message' : 'other-message'}`;
            messageDiv.innerHTML = `
                <strong>${isOwn ? 'Vous' : 'Autre utilisateur'}:</strong> ${content}
                <small class="d-block text-muted">${new Date().toLocaleTimeString()}</small>
            `;
            
            messagesArea.appendChild(messageDiv);
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }

        function connectToChat() {
            const userId = document.getElementById('test-user').value;
            const users = {
                '1': { id: 1, name: 'Organisateur Test' },
                '2': { id: 2, name: 'Utilisateur 1' },
                '3': { id: 3, name: 'Utilisateur 2' }
            };
            
            currentUser = users[userId];
            log(`🔌 Connexion en tant que: ${currentUser.name}`);
            
            // Test de connexion Echo
            if (typeof window.Echo !== 'undefined') {
                try {
                    log('📡 Test de connexion Echo...');
                    
                    // Test avec un canal public d'abord
                    const channel = Echo.channel('test-channel');
                    
                    channel.subscribed(() => {
                        log('✅ Canal public souscrit avec succès');
                        updateConnectionStatus('Connecté', true);
                        isConnected = true;
                        
                        // Ajouter un message de bienvenue
                        addMessage(`Connecté en tant que ${currentUser.name}`, true);
                    });

                    channel.error((error) => {
                        log('❌ Erreur canal: ' + JSON.stringify(error));
                        updateConnectionStatus('Erreur', false);
                    });

                } catch (error) {
                    log('❌ Erreur Echo: ' + error.message);
                    updateConnectionStatus('Erreur', false);
                }
            } else {
                log('❌ Echo n\'est pas disponible');
                updateConnectionStatus('Echo non disponible', false);
            }
        }

        function disconnectFromChat() {
            if (typeof window.Echo !== 'undefined') {
                Echo.leaveChannel('test-channel');
                log('🔌 Déconnexion du chat');
                updateConnectionStatus('Déconnecté', false);
                isConnected = false;
                currentUser = null;
            }
        }

        function sendMessage() {
            const input = document.getElementById('message-input');
            const message = input.value.trim();
            
            if (!message) return;
            
            if (!isConnected) {
                log('❌ Vous devez d\'abord vous connecter');
                return;
            }

            // Ajouter le message à l'interface
            addMessage(message, true);
            input.value = '';
            
            log(`📨 Message envoyé: ${message}`);
            messageCount++;
            
            // Simuler une réponse (pour le test)
            setTimeout(() => {
                addMessage(`Réponse automatique #${messageCount}`, false);
            }, 1000);
        }

        function clearMessages() {
            const messagesArea = document.getElementById('messages-area');
            messagesArea.innerHTML = `
                <div class="text-center text-muted">
                    <i class="fas fa-comments fa-2x mb-3"></i>
                    <p>Messages du chat apparaîtront ici...</p>
                </div>
            `;
            log('🗑️ Messages effacés');
        }

        // Gestion de la touche Entrée
        document.getElementById('message-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            log('🚀 Page de test simple chargée');
            log('📊 Echo disponible: ' + (typeof window.Echo !== 'undefined' ? 'Oui' : 'Non'));
            
            // Test automatique de la configuration
            if (typeof window.Echo !== 'undefined') {
                log('✅ Configuration Echo OK');
            } else {
                log('❌ Configuration Echo manquante');
            }
        });
    </script>
</body>
</html>
