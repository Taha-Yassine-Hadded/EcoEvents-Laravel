<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegistrationController extends Controller
{
    /**
     * Subscribe user to an event (AJAX endpoint)
     */
    public function subscribe(Request $request, Event $event)
    {
        try {
            // Get authenticated user from JWT
            $user = JWTAuth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous devez être connecté pour vous inscrire.'
                ], 401);
            }

            // Check if user is already registered
            $existingRegistration = Registration::where('event_id', $event->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingRegistration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous êtes déjà inscrit à cet événement.'
                ], 400);
            }

            // Check if event is still accepting registrations
            if ($event->status !== 'upcoming') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cet événement n\'accepte plus d\'inscriptions.'
                ], 400);
            }

            // Check event capacity if set
            if ($event->capacity) {
                $currentRegistrations = Registration::where('event_id', $event->id)->count();
                if ($currentRegistrations >= $event->capacity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cet événement est complet.'
                    ], 400);
                }
            }

            // Create registration
            Registration::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'status' => 'registered',
                'registered_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Inscription réussie ! Vous recevrez plus d\'informations par email.'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'inscription à l\'événement ID ' . $event->id . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'inscription. Veuillez réessayer.'
            ], 500);
        }
    }

    /**
     * Inscrire un utilisateur à un événement (Form-based registration)
     */
    public function register(Request $request, Event $event)
    {
        try {
            // Get authenticated user from JWT
            $user = JWTAuth::user();
            
            if (!$user) {
                return redirect()->route('login')
                    ->with('error', 'Vous devez être connecté pour vous inscrire.');
            }

            // Check if user is already registered
            $existingRegistration = Registration::where('event_id', $event->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingRegistration) {
                return redirect()->route('front.events.show', $event->id)
                    ->with('error', 'Vous êtes déjà inscrit à cet événement.');
            }

            // Check if event is still accepting registrations
            if ($event->status !== 'upcoming') {
                return redirect()->route('front.events.show', $event->id)
                    ->with('error', 'Cet événement n\'accepte plus d\'inscriptions.');
            }

            // Check event capacity if set
            if ($event->capacity) {
                $currentRegistrations = Registration::where('event_id', $event->id)->count();
                if ($currentRegistrations >= $event->capacity) {
                    return redirect()->route('front.events.show', $event->id)
                        ->with('error', 'Cet événement est complet.');
                }
            }

            Registration::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'status' => 'registered',
                'registered_at' => now(),
            ]);

            return redirect()->route('front.events.show', $event->id)
                ->with('success', 'Inscription réussie.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'inscription à l\'événement ID ' . $event->id . ': ' . $e->getMessage());
            return redirect()->route('front.events.show', $event->id)
                ->with('error', 'Erreur lors de l\'inscription.');
        }
    }

    /**
     * Afficher les inscriptions de l'utilisateur connecté
     */
    public function myRegistrations(Request $request)
    {
        try {
            // Since this is a web page that needs to work with JS authentication,
            // we'll create a special endpoint that can handle both cookie and bearer token auth
            
            // Check if user is accessing this as an AJAX request with JWT token
            if ($request->expectsJson() || $request->header('Authorization')) {
                $user = JWTAuth::user();
                if (!$user) {
                    return response()->json(['error' => 'Vous devez être connecté'], 401);
                }
                
                $registrations = Registration::with('event.category')
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                return response()->json(['registrations' => $registrations]);
            }
            
            // For regular web requests, we need to check auth differently
            // First try to get token from various sources
            $token = $request->bearerToken() ?: $request->header('X-JWT-Token') ?: $request->cookie('jwt_token');
            
            // If no token in headers/cookies, check if there's a JS way to pass it
            if (!$token) {
                // Return the view with a script that will authenticate via JS
                return view('pages.frontOffice.registrations.index', ['needs_js_auth' => true]);
            }
            
            // Set token and authenticate user
            $user = JWTAuth::setToken($token)->authenticate();
            
            if (!$user) {
                return view('pages.frontOffice.registrations.index', ['needs_js_auth' => true]);
            }

            $registrations = Registration::with('event.category')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return view('pages.frontOffice.registrations.index', compact('registrations'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des inscriptions: ' . $e->getMessage());
            return view('pages.frontOffice.registrations.index', ['needs_js_auth' => true, 'error' => 'Erreur lors du chargement']);
        }
    }

    /**
     * Check if user is registered for an event
     */
    public function checkRegistration(Request $request, Event $event)
    {
        try {
            $user = JWTAuth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'registered' => false
                ]);
            }

            $isRegistered = Registration::isUserRegistered($event->id, $user->id);
            $registrationCount = Registration::getEventRegistrationCount($event->id);

            return response()->json([
                'success' => true,
                'registered' => $isRegistered,
                'registration_count' => $registrationCount,
                'capacity' => $event->capacity,
                'is_full' => $event->capacity ? ($registrationCount >= $event->capacity) : false
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification d\'inscription: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'registered' => false
            ], 500);
        }
    }

    /**
     * Cancel/Unsubscribe user from an event
     */
    public function unsubscribe(Request $request, Event $event)
    {
        try {
            $user = JWTAuth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous devez être connecté pour annuler votre inscription.'
                ], 401);
            }

            // Find the registration
            $registration = Registration::where('event_id', $event->id)
                ->where('user_id', $user->id)
                ->first();

            if (!$registration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas inscrit à cet événement.'
                ], 400);
            }

            // Check if event hasn't started yet (only allow cancellation for upcoming events)
            if ($event->status !== 'upcoming') {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible d\'annuler l\'inscription pour cet événement.'
                ], 400);
            }

            // Delete the registration
            $registration->delete();

            return response()->json([
                'success' => true,
                'message' => 'Inscription annulée avec succès.'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'annulation d\'inscription à l\'événement ID ' . $event->id . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation. Veuillez réessayer.'
            ], 500);
        }
    }
}
