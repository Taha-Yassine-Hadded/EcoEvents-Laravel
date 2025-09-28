<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegistrationController extends Controller
{
    /**
     * Inscrire un utilisateur à un événement
     */
    public function register(Request $request, Event $event)
    {
        try {
            Registration::create([
                'event_id' => $event->id,
                'user_id' => $request->auth->id, // JWT authenticated user
            ]);

            return redirect()->route('front.events.show', $event->id)
                ->with('success', 'Inscription réussie.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'inscription à l\'événement ID ' . $event->id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de l\'inscription'], 500);
        }
    }

    /**
     * Afficher les inscriptions de l'utilisateur connecté
     */
    public function myRegistrations(Request $request)
    {
        try {
            $registrations = Registration::with('event')
                ->where('user_id', $request->auth->id) // JWT authenticated user
                ->get();

            return view('registrations.index', compact('registrations'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des inscriptions de l\'utilisateur ID ' . $request->auth->id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }
}
