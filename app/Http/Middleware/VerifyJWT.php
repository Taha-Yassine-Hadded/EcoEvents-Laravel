<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class VerifyJWT
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->bearerToken() ?: $request->header('X-JWT-Token');
            if (!$token) {
                return response()->json(['error' => 'Token non fourni'], 401);
            }

            $user = JWTAuth::setToken($token)->authenticate();
            if (!$user) {
                return response()->json(['error' => 'Utilisateur non trouvé'], 401);
            }

            $request->auth = $user;

            return $next($request);
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token expiré'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token invalide'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur d\'authentification: ' . $e->getMessage()], 500);
        }
    }
}
