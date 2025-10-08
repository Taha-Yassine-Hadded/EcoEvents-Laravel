<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    /**
     * Récupérer les informations de l'utilisateur connecté
     */
    public function getUser(Request $request)
    {
        try {
            $user = JWTAuth::user();

            if (!$user) {
                return response()->json(['error' => 'Utilisateur non trouvé'], 401);
            }

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'profile_image' => $user->profile_image_url, // sera null si pas d'image
                    'initials' => $user->initials, // utilise l'attribut du modèle
                    'has_image' => $user->hasProfileImage(), // pour savoir s'il faut afficher l'image ou les initiales
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération de l\'utilisateur : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération de l\'utilisateur : ' . $e->getMessage()], 500);
        }
    }
}
