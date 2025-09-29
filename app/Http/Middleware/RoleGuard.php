<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleGuard
{
    public function handle(Request $request, Closure $next, $role = 'admin')
    {
        try {
            // Récupérer l'utilisateur depuis la requête (défini par VerifyJWT)
            $user = $request->auth;

            // Log pour déboguer l'état de l'utilisateur
            Log::info('RoleGuard: Vérification du rôle', [
                'user_exists' => !is_null($user),
                'user_id' => $user ? $user->id : null,
                'user_role' => $user ? $user->role : null,
                'required_role' => $role,
                'url' => $request->url(),
                'headers' => $request->headers->all(),
            ]);

            // Vérifier si l'utilisateur a le rôle requis
            if (!$user || $user->role !== $role) {
                Log::warning('RoleGuard: Accès non autorisé, rôle insuffisant', [
                    'user_id' => $user ? $user->id : null,
                    'user_role' => $user ? $user->role : null,
                    'required_role' => $role,
                    'url' => $request->url(),
                ]);
                
                $roleMessages = [
                    'admin' => 'administrateurs',
                    'organizer' => 'organisateurs',
                    'sponsor' => 'sponsors',
                    'user' => 'utilisateurs'
                ];
                
                $roleMessage = $roleMessages[$role] ?? $role;
                return redirect()->route('home')->with('error', "Accès non autorisé : réservé aux {$roleMessage}.");
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
