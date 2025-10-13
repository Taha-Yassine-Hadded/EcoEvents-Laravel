<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification du code - EcoEvents</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .verify-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .eco-brand {
            color: #28a745;
            font-weight: bold;
            font-size: 2rem;
        }
        
        .btn-eco {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-eco:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
        
        .form-control {
            border-radius: 15px;
            border: 2px solid #e9ecef;
            padding: 12px 20px;
            transition: all 0.3s ease;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            letter-spacing: 0.5rem;
        }
        
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        
        .alert {
            border-radius: 15px;
            border: none;
        }
        
        .back-link {
            color: #6c757d;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .back-link:hover {
            color: #28a745;
        }
        
        .code-info {
            background: rgba(40, 167, 69, 0.1);
            border-radius: 15px;
            padding: 15px;
            border-left: 4px solid #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="verify-container p-5">
                    <div class="text-center mb-4">
                        <h1 class="eco-brand">
                            <i class="fas fa-leaf"></i> EcoEvents
                        </h1>
                        <h3 class="text-muted mb-3">Vérification du code</h3>
                        <div class="code-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Un code de 6 chiffres a été envoyé à <strong>{{ session('reset_email') }}</strong>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.verify') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('reset_email') }}">
                        
                        <div class="mb-4">
                            <label for="code" class="form-label">
                                <i class="fas fa-key me-2"></i>Code de récupération
                            </label>
                            <input 
                                type="text" 
                                class="form-control @error('code') is-invalid @enderror" 
                                id="code" 
                                name="code" 
                                maxlength="6" 
                                pattern="[0-9]{6}"
                                required 
                                placeholder="000000"
                                autocomplete="off"
                            >
                            @error('code')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-clock me-1"></i>
                                Le code expire dans 15 minutes
                            </div>
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-eco btn-lg text-white">
                                <i class="fas fa-check me-2"></i>
                                Vérifier le code
                            </button>
                        </div>
                    </form>

                    <div class="text-center">
                        <a href="{{ route('password.request') }}" class="back-link me-3">
                            <i class="fas fa-arrow-left me-2"></i>
                            Retour
                        </a>
                        <a href="{{ route('password.request') }}" class="back-link">
                            <i class="fas fa-redo me-2"></i>
                            Renvoyer le code
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-format du code (accepter seulement les chiffres)
        document.getElementById('code').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>
