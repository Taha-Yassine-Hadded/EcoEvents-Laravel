<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Support\Facades\Log;

class VerifyJWT
{
    /**
     * Extrait le token JWT depuis l'en-tête Cookie manuellement.
     *
     * @param string|null $cookieHeader
     * @return string|null
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

    public function handle(Request $request, Closure $next)
    {
        try {
            // Vérifier le token dans l'en-tête Authorization, X-JWT-Token, ou cookie
            $token = $request->bearerToken() ?: $request->header('X-JWT-Token') ?: $request->cookie('jwt_token');

            // Si le cookie Laravel retourne null, extraire manuellement depuis l'en-tête Cookie
            if (!$token) {
                $token = $this->extractTokenFromCookieHeader($request->header('cookie'));
            }

            Log::info('VerifyJWT: Vérification du token', [
                'url' => $request->url(),
                'bearer_token' => $request->bearerToken() ? substr($request->bearerToken(), 0, 20) . '...' : null,
                'x_jwt_token' => $request->header('X-JWT-Token') ? substr($request->header('X-JWT-Token'), 0, 20) . '...' : null,
                'cookie_jwt_token' => $request->cookie('jwt_token') ? substr($request->cookie('jwt_token'), 0, 20) . '...' : null,
                'manual_cookie_token' => $token ? substr($token, 0, 20) . '...' : null,
                'raw_cookies' => $request->header('cookie'),
                'headers' => $request->headers->all(),
                'cookies' => $request->cookies->all(),
            ]);

            if (!$token) {
                Log::warning('VerifyJWT: Token non fourni', [
                    'url' => $request->url(),
                    'headers' => $request->headers->all(),
                    'cookies' => $request->cookies->all(),
                    'raw_cookies' => $request->header('cookie'),
                ]);
                
                // For web requests, redirect to login instead of JSON response
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json(['error' => 'Token non fourni'], 401);
                }
                
                return redirect()->route('login')->with('error', 'Veuillez vous connecter pour accéder à cette page.');
            }

            $user = JWTAuth::setToken($token)->authenticate();
            if (!$user) {
                Log::warning('VerifyJWT: Utilisateur non trouvé pour le token', [
                    'token' => substr($token, 0, 20) . '...',
                    'url' => $request->url(),
                ]);
                
                // For web requests, redirect to login instead of JSON response
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json(['error' => 'Utilisateur non trouvé'], 401);
                }
                
                return redirect()->route('login')->with('error', 'Session invalide. Veuillez vous reconnecter.');
            }

            $request->auth = $user;

            Log::info('VerifyJWT: Authentification réussie', [
                'user_id' => $user->id,
                'email' => $user->email,
                'url' => $request->url(),
            ]);

            return $next($request);
        } catch (TokenExpiredException $e) {
            Log::error('VerifyJWT: Token expiré', ['error' => $e->getMessage()]);
            
            // For web requests, redirect to login instead of JSON response
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Token expiré'], 401);
            }
            
            return redirect()->route('login')->with('error', 'Votre session a expiré. Veuillez vous reconnecter.');
        } catch (TokenInvalidException $e) {
            Log::error('VerifyJWT: Token invalide', ['error' => $e->getMessage()]);
            
            // For web requests, redirect to login instead of JSON response
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Token invalide'], 401);
            }
            
            return redirect()->route('login')->with('error', 'Session invalide. Veuillez vous reconnecter.');
        } catch (\Exception $e) {
            Log::error('VerifyJWT: Erreur inattendue', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            // For web requests, redirect to login instead of JSON response
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Erreur d\'authentification: ' . $e->getMessage()], 500);
            }
            
            return redirect()->route('login')->with('error', 'Erreur d\'authentification. Veuillez vous reconnecter.');
        }
    }
}
