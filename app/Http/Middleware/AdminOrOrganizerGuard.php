<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminOrOrganizerGuard
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Récupérer l'utilisateur depuis la requête (défini par VerifyJWT)
            $user = $request->auth;

            // Rôles autorisés : admin et organizer
            $allowedRoles = ['admin', 'organizer'];

            // Log pour déboguer l'état de l'utilisateur
            Log::info('AdminOrOrganizerGuard: Vérification du rôle', [
                'user_exists' => !is_null($user),
                'user_id' => $user ? $user->id : null,
                'user_role' => $user ? $user->role : null,
                'allowed_roles' => $allowedRoles,
                'url' => $request->url(),
            ]);

            // Vérifier si l'utilisateur a un des rôles autorisés
            if (!$user || !in_array($user->role, $allowedRoles)) {
                Log::warning('AdminOrOrganizerGuard: Accès non autorisé, rôle insuffisant', [
                    'user_id' => $user ? $user->id : null,
                    'user_role' => $user ? $user->role : null,
                    'allowed_roles' => $allowedRoles,
                    'url' => $request->url(),
                ]);
                return redirect()->route('home')->with('error', 'Accès non autorisé : rôle insuffisant.');
            }

            Log::info('AdminOrOrganizerGuard: Autorisation réussie', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'url' => $request->url(),
            ]);

            return $next($request);
        } catch (\Exception $e) {
            Log::error('AdminOrOrganizerGuard: Erreur inattendue', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'url' => $request->url(),
            ]);
            return redirect()->route('home')->with('error', 'Erreur d\'autorisation.');
        }
    }
}
