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
            return view('pages.backOffice.dashboard', ['user' => $user]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('DashboardController: Erreur', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur d\'authentification.'], 500);
        }
    }
}
