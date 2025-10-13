<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Chat - Echofy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-bug me-2"></i>Test du Chat - Debug</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5>🧪 Test de Configuration</h5>
                            <p>Cette page permet de tester la configuration du chat et de diagnostiquer les problèmes.</p>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h6>📊 Informations de Connexion</h6>
                                <div id="connection-info">
                                    <p><strong>Echo disponible:</strong> <span id="echo-status">Vérification...</span></p>
                                    <p><strong>Token JWT:</strong> <span id="jwt-status">Vérification...</span></p>
                                    <p><strong>Serveur Laravel:</strong> <span id="laravel-status">Vérification...</span></p>
                                    <p><strong>Serveur Reverb:</strong> <span id="reverb-status">Vérification...</span></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>🔧 Actions de Test</h6>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-outline-primary" onclick="testEchoConnection()">
                                        <i class="fas fa-wifi me-2"></i>Tester Echo
                                    </button>
                                    <button class="btn btn-outline-success" onclick="testMessageSend()">
                                        <i class="fas fa-paper-plane me-2"></i>Tester Envoi
                                    </button>
                                    <button class="btn btn-outline-info" onclick="testWebSocket()">
                                        <i class="fas fa-plug me-2"></i>Tester WebSocket
                                    </button>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-12">
                                <h6>📝 Console de Debug</h6>
                                <div id="debug-console" class="bg-dark text-light p-3 rounded" style="height: 200px; overflow-y: auto; font-family: monospace;">
                                    <div>🚀 Initialisation du test...</div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-12">
                                <h6>🔗 Liens de Test</h6>
                                <div class="d-grid gap-2 d-md-flex">
                                    <a href="/communities" class="btn btn-primary">
                                        <i class="fas fa-users me-2"></i>Voir Communautés
                                    </a>
                                    <a href="/test-chat" class="btn btn-outline-primary">
                                        <i class="fas fa-api me-2"></i>Test API
                                    </a>
                                    <button class="btn btn-outline-secondary" onclick="location.reload()">
                                        <i class="fas fa-refresh me-2"></i>Recharger
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function log(message) {
            const console = document.getElementById('debug-console');
            const timestamp = new Date().toLocaleTimeString();
            console.innerHTML += `<div>[${timestamp}] ${message}</div>`;
            console.scrollTop = console.scrollHeight;
        }

        function updateStatus(elementId, status, isGood = true) {
            const element = document.getElementById(elementId);
            element.textContent = status;
            element.className = isGood ? 'text-success' : 'text-danger';
        }

        // Test de la configuration
        function testConfiguration() {
            log('🔍 Test de la configuration...');
            
            // Test Echo
            if (typeof window.Echo !== 'undefined') {
                updateStatus('echo-status', '✅ Disponible', true);
                log('✅ Echo est disponible');
            } else {
                updateStatus('echo-status', '❌ Non disponible', false);
                log('❌ Echo n\'est pas disponible');
            }

            // Test JWT
            const token = localStorage.getItem('jwt_token');
            if (token) {
                updateStatus('jwt-status', '✅ Présent', true);
                log('✅ Token JWT trouvé');
            } else {
                updateStatus('jwt-status', '❌ Absent', false);
                log('❌ Token JWT manquant');
            }

            // Test serveurs
            testServers();
        }

        function testServers() {
            // Test Laravel
            fetch('/test-chat')
                .then(response => {
                    if (response.ok) {
                        updateStatus('laravel-status', '✅ Connecté', true);
                        log('✅ Serveur Laravel accessible');
                    } else {
                        updateStatus('laravel-status', '❌ Erreur', false);
                        log('❌ Serveur Laravel inaccessible');
                    }
                })
                .catch(error => {
                    updateStatus('laravel-status', '❌ Erreur', false);
                    log('❌ Erreur Laravel: ' + error.message);
                });

            // Test Reverb
            testWebSocket();
        }

        function testWebSocket() {
            log('🔌 Test de la connexion WebSocket...');
            
            try {
                const ws = new WebSocket('ws://localhost:8080');
                
                ws.onopen = function() {
                    updateStatus('reverb-status', '✅ Connecté', true);
                    log('✅ WebSocket Reverb connecté');
                    ws.close();
                };
                
                ws.onerror = function(error) {
                    updateStatus('reverb-status', '❌ Erreur', false);
                    log('❌ Erreur WebSocket: ' + error);
                };
                
                ws.onclose = function() {
                    log('🔌 Connexion WebSocket fermée');
                };
            } catch (error) {
                updateStatus('reverb-status', '❌ Erreur', false);
                log('❌ Erreur WebSocket: ' + error.message);
            }
        }

        function testEchoConnection() {
            log('📡 Test de la connexion Echo...');
            
            if (typeof window.Echo === 'undefined') {
                log('❌ Echo n\'est pas disponible');
                return;
            }

            try {
                // Test de connexion à un canal public
                const channel = Echo.channel('test-channel');
                
                channel.subscribed(() => {
                    log('✅ Canal Echo souscrit avec succès');
                    channel.unsubscribe();
                });

                channel.error((error) => {
                    log('❌ Erreur canal Echo: ' + JSON.stringify(error));
                });

            } catch (error) {
                log('❌ Erreur Echo: ' + error.message);
            }
        }

        function testMessageSend() {
            log('📨 Test d\'envoi de message...');
            
            const token = localStorage.getItem('jwt_token');
            if (!token) {
                log('❌ Token JWT manquant pour le test');
                return;
            }

            // Test avec une communauté existante (ID 2 d'après le seeder)
            fetch('/communities/2/chat/message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    content: 'Test de message depuis la page de debug'
                })
            })
            .then(response => {
                if (response.ok) {
                    log('✅ Message envoyé avec succès');
                    return response.json();
                } else {
                    throw new Error('HTTP ' + response.status);
                }
            })
            .then(data => {
                log('✅ Réponse: ' + JSON.stringify(data));
            })
            .catch(error => {
                log('❌ Erreur envoi: ' + error.message);
            });
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            log('🚀 Page de test chargée');
            testConfiguration();
        });
    </script>
</body>
</html>
