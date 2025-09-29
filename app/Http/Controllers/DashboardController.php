<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->auth;
            if (!$user) {
                return response()->json(['error' => 'Veuillez vous connecter pour accéder au tableau de bord.'], 401);
            }

            // Si c'est un admin, utiliser le dashboard backoffice existant
            if ($user->role === 'admin') {
                return view('pages.backOffice.dashboard', ['user' => $user]);
            }

            // Si c'est un sponsor, rediriger vers le dashboard sponsor
            if ($user->role === 'sponsor') {
                return redirect()->route('sponsor.dashboard');
            }

            // Dashboard par défaut pour les autres rôles
            return view('pages.backOffice.dashboard', ['user' => $user]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('DashboardController: Erreur', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur d\'authentification.'], 500);
        }
    }

}
