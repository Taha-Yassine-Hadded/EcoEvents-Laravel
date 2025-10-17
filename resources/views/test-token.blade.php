@extends('layouts.app')

@section('title', 'Test Token JWT - EcoEvents')

@section('content')
<section class="py-5">
    <div class="container">
        <style>
            .info { background: #e7f3ff; padding: 15px; margin: 10px 0; border-radius: 5px; }
            .error { background: #ffe7e7; padding: 15px; margin: 10px 0; border-radius: 5px; }
            .success { background: #e7ffe7; padding: 15px; margin: 10px 0; border-radius: 5px; }
        </style>
    <h1>🔍 Test Token JWT</h1>
    
    <div class="info">
        <h3>Étapes de diagnostic :</h3>
        <ol>
            <li>Vérifiez si vous avez un token dans localStorage</li>
            <li>Testez l'API avec ce token</li>
            <li>Vérifiez votre rôle utilisateur</li>
        </ol>
    </div>

    <button class="btn btn-primary me-2 mb-2" onclick="checkLocalStorage()">1. Vérifier localStorage</button>
    <button class="btn btn-info me-2 mb-2" onclick="testToken()">2. Tester le token</button>
    <button class="btn btn-success me-2 mb-2" onclick="getUserInfo()">3. Info utilisateur</button>
    <button class="btn btn-danger mb-2" onclick="clearToken()">❌ Effacer token</button>

    <div id="results"></div>
    </div>
</section>
@endsection

@push('scripts')
<script>
        function log(message, type = 'info') {
            const div = document.createElement('div');
            div.className = type;
            div.innerHTML = message;
            document.getElementById('results').appendChild(div);
        }

        function checkLocalStorage() {
            document.getElementById('results').innerHTML = '';
            const token = localStorage.getItem('jwt_token');
            
            if (token) {
                log(`✅ Token trouvé dans localStorage:<br><code>${token.substring(0, 50)}...</code>`, 'success');
            } else {
                log('❌ Aucun token trouvé dans localStorage', 'error');
                log('Vous devez vous connecter d\'abord !', 'error');
            }
        }

        function testToken() {
            const token = localStorage.getItem('jwt_token');
            if (!token) {
                log('❌ Pas de token. Connectez-vous d\'abord !', 'error');
                return;
            }

            fetch('/test-user', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                log(`📊 Résultat du test:<br><pre>${JSON.stringify(data, null, 2)}</pre>`, 'info');
            })
            .catch(error => {
                log(`❌ Erreur: ${error}`, 'error');
            });
        }

        function getUserInfo() {
            const token = localStorage.getItem('jwt_token');
            if (!token) {
                log('❌ Pas de token. Connectez-vous d\'abord !', 'error');
                return;
            }

            fetch('{{ route("user.get") }}', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    log(`✅ Utilisateur connecté:<br>
                         <strong>Nom:</strong> ${data.user.name}<br>
                         <strong>Email:</strong> ${data.user.email}<br>
                         <strong>Rôle:</strong> ${data.user.role}<br>
                         <strong>Est organisateur:</strong> ${data.user.role === 'organizer' ? 'OUI' : 'NON'}`, 'success');
                } else {
                    log(`❌ Erreur: ${data.message || 'Utilisateur non trouvé'}`, 'error');
                }
            })
            .catch(error => {
                log(`❌ Erreur réseau: ${error}`, 'error');
            });
        }

        function clearToken() {
            localStorage.removeItem('jwt_token');
            log('🗑️ Token supprimé du localStorage', 'info');
        }

        // Auto-check au chargement
        window.onload = function() {
            checkLocalStorage();
        };
</script>
@endpush
