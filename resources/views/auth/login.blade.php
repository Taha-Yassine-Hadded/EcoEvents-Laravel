@extends('layouts.app')

@section('title', 'Connexion - EcoEvents')

@section('content')
    <!--==================================================-->
    <!-- Start EcoEvents Login Area -->
    <!--==================================================-->
    <div class="login-area">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="login-form-wrapper">
                        <div class="section-title center">
                            <h4><img src="{{ asset('assets/images/home6/section-title-shape.png') }}" alt="">EcoEvents</h4>
                            <h1>Connexion</h1>
                            <p>Connectez-vous pour rejoindre notre communauté écologique</p>
                        </div>

                        <form id="login-form" class="login-form">
                            @csrf
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password">Mot de passe *</label>
                                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group text-center">
                                <div class="echofy-button style-five">
                                    <button type="submit">Se connecter<i class="bi bi-arrow-right-short"></i></button>
                                </div>
                            </div>

                            <div class="text-center mt-3">
                                <p>Pas encore de compte ? <a href="{{ route('register') }}">Inscrivez-vous</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--==================================================-->
    <!-- End EcoEvents Login Area -->
    <!--==================================================-->
@endsection

@push('styles')
    <style>
        .login-area {
            padding: 100px 0;
            background: #f8f9fa;
        }

        .login-form-wrapper {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .echofy-button button {
            background: #28a745;
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .echofy-button button:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }

        .is-invalid {
            border-color: #dc3545;
        }

        .text-center a {
            color: #28a745;
            font-weight: 600;
        }

        .text-center a:hover {
            color: #218838;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.getElementById('login-form').addEventListener('submit', async function(event) {
            event.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const csrfToken = document.querySelector('input[name="_token"]').value;

            try {
                const response = await fetch('{{ route("login") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ email, password }),
                });

                const data = await response.json();

                if (response.ok) {
                    // Stocker le token dans localStorage
                    localStorage.setItem('jwt_token', data.token);
                    // Rediriger vers la page d'accueil
                    window.location.href = '{{ route("home") }}';
                } else {
                    // Afficher l'erreur
                    alert(data.error || 'Erreur lors de la connexion');
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Une erreur est survenue. Veuillez réessayer.');
            }
        });
    </script>
@endpush
