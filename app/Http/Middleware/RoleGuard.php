<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleGuard
{
    public function handle(Request $request, Closure $next, $roles = 'admin,organizer')
    {
        try {
            // Récupérer l'utilisateur depuis la requête (défini par VerifyJWT)
            $user = $request->auth;

            // Parse les rôles autorisés (séparés par des virgules)
            $allowedRoles = array_map('trim', explode(',', $roles));

            // Log pour déboguer l'état de l'utilisateur
            Log::info('RoleGuard: Vérification du rôle', [
                'user_exists' => !is_null($user),
                'user_id' => $user ? $user->id : null,
                'user_role' => $user ? $user->role : null,
                'allowed_roles' => $allowedRoles,
                'url' => $request->url(),
                'headers' => $request->headers->all(),
            ]);

            // Vérifier si l'utilisateur a un des rôles autorisés
            if (!$user || !in_array($user->role, $allowedRoles)) {
                Log::warning('RoleGuard: Accès non autorisé, rôle insuffisant', [
                    'user_id' => $user ? $user->id : null,
                    'user_role' => $user ? $user->role : null,
                    'allowed_roles' => $allowedRoles,
                    'url' => $request->url(),
                ]);
                return redirect()->route('home')->with('error', 'Accès non autorisé : rôle insuffisant.');
            }

            Log::info('RoleGuard: Autorisation réussie', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'url' => $request->url(),
            ]);

            return $next($request);
        } catch (\Exception $e) {
            Log::error('RoleGuard: Erreur inattendue', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'url' => $request->url(),
            ]);
            return redirect()->route('home')->with('error', 'Erreur d\'autorisation.');
        }
    }
}
