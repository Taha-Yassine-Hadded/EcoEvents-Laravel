<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class OptionalJWT
{
    /**
     * Handle an incoming request.
     * Ce middleware essaie d'authentifier l'utilisateur via JWT mais n'échoue pas si le token n'est pas présent.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Vérifier le token dans l'en-tête Authorization, X-JWT-Token, ou cookie
            $token = $request->bearerToken() ?: $request->header('X-JWT-Token') ?: $request->cookie('jwt_token');

            // Si le cookie Laravel retourne null, extraire manuellement depuis l'en-tête Cookie
            if (!$token) {
                $token = $this->extractTokenFromCookieHeader($request->header('cookie'));
            }

            if ($token) {
                $user = JWTAuth::setToken($token)->authenticate();
                if ($user) {
                    // Injecter l'utilisateur dans la session Laravel pour compatibilité
                    Auth::login($user, false); // false = ne pas se souvenir
                    
                    // Aussi l'ajouter aux attributs de la requête
                    $request->attributes->set('jwt_user', $user);
                    
                    Log::info('OptionalJWT: Utilisateur authentifié', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'role' => $user->role,
                        'url' => $request->url(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // En cas d'erreur, on continue sans authentification
            Log::info('OptionalJWT: Pas d\'authentification JWT', [
                'error' => $e->getMessage(),
                'url' => $request->url(),
            ]);
        }

        return $next($request);
    }

    /**
     * Extrait le token JWT depuis l'en-tête Cookie manuellement.
     */
    private function extractTokenFromCookieHeader($cookieHeader)
    {
        if (!$cookieHeader) {
            return null;
        }

        // Séparer les cookies
        $cookies = explode('; ', $cookieHeader);
        foreach ($cookies as $cookie) {
            if (strpos($cookie, 'jwt_token=') === 0) {
                return substr($cookie, strlen('jwt_token='));
            }
        }

        return null;
    }
}
