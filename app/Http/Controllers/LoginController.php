<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        try {
            return view('auth.login');
        } catch (\Exception $e) {
            Log::error('LoginController::showLoginForm: Erreur', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur lors du chargement du formulaire: ' . $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            // Forcer la réponse JSON
            $request->headers->set('Accept', 'application/json');

            // Log des données reçues
            Log::info('LoginController::login: Requête reçue', [
                'input' => $request->all(),
                'headers' => $request->headers->all(),
                'json' => $request->json()->all(),
            ]);

            // Vérifier JWT_TTL
            $jwtTtl = (int) config('jwt.ttl');
            Log::info('LoginController::login: JWT_TTL', ['ttl' => $jwtTtl]);
            if (!is_int($jwtTtl)) {
                Log::error('LoginController::login: JWT_TTL n\'est pas un entier', ['ttl' => $jwtTtl]);
                return response()->json(['error' => 'Configuration JWT invalide: TTL doit être un entier'], 500);
            }

            // Récupérer les données JSON ou du formulaire
            $input = $request->json()->all() ?: $request->all();
            $credentials = [
                'email' => $input['email'] ?? null,
                'password' => $input['password'] ?? null,
            ];

            // Valider les credentials
            $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            // Vérifier si l'utilisateur existe
            $user = \App\Models\User::where('email', $credentials['email'])->first();
            if (!$user) {
                Log::warning('LoginController::login: Utilisateur non trouvé', ['email' => $credentials['email']]);
                return response()->json(['error' => 'Utilisateur non trouvé'], 401);
            }

            // Tenter de générer un token
            if (!$token = JWTAuth::attempt($credentials)) {
                Log::warning('LoginController::login: Échec de l\'authentification', ['email' => $credentials['email']]);
                return response()->json(['error' => 'Identifiants invalides'], 401);
            }

            $user = Auth::user();

            // Log du succès
            Log::info('LoginController::login: Connexion réussie', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
            ]);

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('LoginController::login: Erreur de validation', ['errors' => $e->errors()]);
            return response()->json(['error' => 'Données invalides: ' . implode(', ', array_merge(...array_values($e->errors())))], 422);
        } catch (JWTException $e) {
            Log::error('LoginController::login: Erreur JWT', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Erreur lors de la génération du token: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error('LoginController::login: Erreur inattendue', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Erreur lors de la connexion: ' . $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Vérifier le token dans l'en-tête ou le cookie
            $token = $request->bearerToken() ?: $request->cookie('jwt_token');
            if (!$token) {
                Log::warning('LoginController::logout: Token non fourni', [
                    'headers' => $request->headers->all(),
                    'cookies' => $request->cookies->all(),
                ]);
                return response()->json(['error' => 'Token non fourni'], 401);
            }

            // Invalider le token
            JWTAuth::setToken($token)->invalidate();

            // Supprimer le cookie
            cookie()->queue(cookie()->forget('jwt_token'));

            Log::info('LoginController::logout: Déconnexion réussie');
            return response()->json([
                'success' => true,
                'message' => 'Déconnexion réussie',
                'clear_local_storage' => true,
            ]);
        } catch (JWTException $e) {
            Log::error('LoginController::logout: Erreur JWT', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur lors de la déconnexion: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error('LoginController::logout: Erreur inattendue', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur inattendue: ' . $e->getMessage()], 500);
        }
    }
}
