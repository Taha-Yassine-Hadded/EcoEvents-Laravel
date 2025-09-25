<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{
    /**
     * Afficher le formulaire de login
     */
    public function showLoginForm()
    {
        try {
            return view('auth.login');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement du formulaire: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Gérer la connexion et générer le token JWT
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            // Tenter de générer un token JWT
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Identifiants invalides'], 401);
            }

            // Retourner le token
            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => Auth::user(),
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la connexion: ' . $e->getMessage()], 500);
        }
    }



    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        try {
            // Récupérer le token depuis l'en-tête Authorization
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['error' => 'Token non fourni'], 401);
            }

            // Invalider le token
            JWTAuth::setToken($token)->invalidate();

            return response()->json(['success' => true, 'message' => 'Déconnexion réussie']);
        } catch (JWTException $e) {
            \Log::error('Erreur lors de la déconnexion: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la déconnexion: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            \Log::error('Erreur inattendue lors de la déconnexion: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur inattendue: ' . $e->getMessage()], 500);
        }
    }
}
