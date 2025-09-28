@extends('layouts.app')

@section('title', 'Test Communautés - EcoEvents')

@section('content')
<section class="py-5">
    <div class="container">
        <h1>🧪 Test d'accès aux communautés</h1>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5>📋 Tests d'accès</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>1. Page liste des communautés :</strong><br>
                            <a href="{{ route('communities.index') }}" class="btn btn-outline-primary btn-sm">
                                Tester /communities
                            </a>
                        </div>
                        
                        <div class="mb-3">
                            <strong>2. Page de connexion :</strong><br>
                            <a href="{{ route('login') }}" class="btn btn-outline-success btn-sm">
                                Tester /login
                            </a>
                        </div>
                        
                        <div class="mb-3">
                            <strong>3. Test token JWT :</strong><br>
                            <a href="/test-token" class="btn btn-outline-info btn-sm">
                                Tester /test-token
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5>✅ Vérifications</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>Routes publiques :</strong>
                            <span class="badge bg-success">OK</span>
                        </div>
                        <div class="mb-2">
                            <strong>Contrôleur :</strong>
                            <span class="badge bg-success">PublicCommunityController</span>
                        </div>
                        <div class="mb-2">
                            <strong>Navigation :</strong>
                            <span class="badge bg-success">Lien "Communautés" présent</span>
                        </div>
                        <div class="mb-2">
                            <strong>Authentification :</strong>
                            <span class="badge bg-warning">Optionnelle pour voir la liste</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="alert alert-info mt-4">
            <h5>🔍 Diagnostic :</h5>
            <p>Si vous ne pouvez pas accéder à la liste des communautés, vérifiez :</p>
            <ul>
                <li>Que le serveur Laravel fonctionne (<code>php artisan serve</code>)</li>
                <li>Que la base de données est connectée</li>
                <li>Qu'il y a des communautés dans la base de données</li>
                <li>Que les migrations ont été exécutées</li>
            </ul>
        </div>
        
        <div class="mt-4">
            <a href="{{ url('/') }}" class="btn btn-secondary">← Retour à l'accueil</a>
        </div>
    </div>
</section>
@endsection
