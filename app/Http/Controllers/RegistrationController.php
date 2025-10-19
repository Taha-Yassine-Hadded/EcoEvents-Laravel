<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Mail\EventRegistrationConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
            $registration = Registration::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'status' => 'registered',
                'registered_at' => now(),
                'role' => $request->input('role', null),
                'skills' => $request->input('skills', null),
                'has_transportation' => $request->input('has_transportation', false),
                'has_participated_before' => $request->input('has_participated_before', false),
                'emergency_contact' => $request->input('emergency_contact', null),
            ]);

            // Send confirmation email
            try {
                Mail::to($user->email)->send(new EventRegistrationConfirmation($user, $event, $registration));
                Log::info('Registration confirmation email sent via AJAX', [
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'registration_id' => $registration->id
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send registration confirmation email via AJAX', [
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'error' => $e->getMessage()
                ]);
                // Don't fail the registration if email fails
            }

            return response()->json([
                'success' => true,
                'message' => 'Inscription réussie ! Un email de confirmation a été envoyé.'
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
            // Validation des données du formulaire
            $request->validate([
                'role' => 'required|string|max:255',
                'skills' => 'required|string|max:255',
                'has_transportation' => 'nullable|boolean',
                'has_participated_before' => 'nullable|boolean',
                'emergency_contact' => 'nullable|string|max:255',
            ], [
                'role.required' => 'Le rôle de bénévole est obligatoire',
                'skills.required' => 'Veuillez indiquer vos compétences',
            ]);
            
            // Get authenticated user from JWT middleware
            $user = $request->auth ?? JWTAuth::user() ?? auth()->user();
            
            // Debug logging
            Log::info('Registration attempt', [
                'request_auth' => $request->auth ? $request->auth->id : 'null',
                'jwt_user' => JWTAuth::user() ? JWTAuth::user()->id : 'null',
                'session_user' => auth()->user() ? auth()->user()->id : 'null',
                'bearer_token' => $request->bearerToken() ? 'present' : 'missing',
                'headers' => $request->headers->all(),
            ]);
            
            if (!$user) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Vous devez être connecté pour vous inscrire.'
                    ], 401);
                }
                return redirect()->route('login')
                    ->with('error', 'Vous devez être connecté pour vous inscrire.');
            }
            
            // Validate the form data
            $validated = $request->validate([
                'role' => 'required|string',
                'skills' => 'required|string',
                'emergency_contact' => 'required|string|max:255',
            ], [
                'role.required' => 'Veuillez sélectionner un rôle',
                'skills.required' => 'Veuillez sélectionner au moins une compétence',
                'emergency_contact.required' => 'Les informations de contact d\'urgence sont requises',
            ]);

            // Check if user is already registered
            $existingRegistration = Registration::where('event_id', $event->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingRegistration) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Vous êtes déjà inscrit à cet événement.'
                    ], 400);
                }
                return redirect()->route('front.events.show', $event->id)
                    ->with('error', 'Vous êtes déjà inscrit à cet événement.');
            }

            // Check if event is still accepting registrations
            if ($event->status !== 'upcoming') {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cet événement n\'accepte plus d\'inscriptions.'
                    ], 400);
                }
                return redirect()->route('front.events.show', $event->id)
                    ->with('error', 'Cet événement n\'accepte plus d\'inscriptions.');
            }

            // Check event capacity if set
            if ($event->capacity) {
                $currentRegistrations = Registration::where('event_id', $event->id)->count();
                if ($currentRegistrations >= $event->capacity) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Cet événement est complet.'
                        ], 400);
                    }
                    return redirect()->route('front.events.show', $event->id)
                        ->with('error', 'Cet événement est complet.');
                }
            }

            $registration = Registration::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'status' => 'registered',
                'registered_at' => now(),
                'role' => $request->role,
                'skills' => $request->skills,
                'has_transportation' => $request->has('has_transportation') ? true : false,
                'has_participated_before' => $request->has('has_participated_before') ? true : false,
                'emergency_contact' => $request->emergency_contact,
            ]);

            // Send confirmation email
            try {
                Mail::to($user->email)->send(new EventRegistrationConfirmation($user, $event, $registration));
                Log::info('Registration confirmation email sent', [
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'registration_id' => $registration->id
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send registration confirmation email', [
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'error' => $e->getMessage()
                ]);
                // Don't fail the registration if email fails
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Inscription réussie. Un email de confirmation a été envoyé.'
                ]);
            }
            
            return redirect()->route('front.events.show', $event->id)
                ->with('success', 'Inscription réussie. Un email de confirmation a été envoyé.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'inscription à l\'événement ID ' . $event->id . ': ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'inscription: ' . $e->getMessage()
                ], 500);
            }
            
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
            // Get authenticated user from middleware
            $user = $request->auth ?? JWTAuth::user() ?? auth()->user();
            
            if (!$user) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Vous devez être connecté'], 401);
                }
                return redirect()->route('login')->with('error', 'Vous devez être connecté pour voir vos inscriptions.');
            }
            
            // Check if this is an AJAX request for JSON data
            if ($request->expectsJson() || $request->header('Accept') === 'application/json') {
                $registrations = Registration::with(['event.category', 'event.organizer'])
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                return response()->json(['registrations' => $registrations]);
            }
            
            // For regular web requests, get registrations with pagination and search
            $query = Registration::with(['event.category', 'event.organizer'])
                ->where('user_id', $user->id);
            
            // Add search functionality
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->whereHas('event', function($q) use ($searchTerm) {
                    $q->where('title', 'like', "%{$searchTerm}%")
                      ->orWhere('location', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%");
                });
            }
            
            // Add status filter
            if ($request->has('status') && !empty($request->status)) {
                $query->whereHas('event', function($q) use ($request) {
                    $q->where('status', $request->status);
                });
            }
            
            $registrations = $query->orderBy('created_at', 'desc')->paginate(4);
            
            // Get summary statistics
            $totalRegistrations = Registration::where('user_id', $user->id)->count();
            $upcomingCount = Registration::where('user_id', $user->id)
                ->whereHas('event', function($q) {
                    $q->where('status', 'upcoming');
                })->count();
            $ongoingCount = Registration::where('user_id', $user->id)
                ->whereHas('event', function($q) {
                    $q->where('status', 'ongoing');
                })->count();
            $completedCount = Registration::where('user_id', $user->id)
                ->whereHas('event', function($q) {
                    $q->where('status', 'completed');
                })->count();

            return view('pages.frontOffice.registrations.index', compact('registrations', 'totalRegistrations', 'upcomingCount', 'ongoingCount', 'completedCount'));
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

public function getEventRegistrations(Request $request, Event $event)
    {
        try {
            // Fetch registrations with related user and event data
            $registrations = Registration::where('event_id', $event->id)
                ->with(['user' => function ($query) {
                    $query->select('id', 'name', 'email');
                }, 'event' => function ($query) {
                    $query->select('id', 'title', 'organizer_id');
                }])
                ->get([
                    'id',
                    'event_id',
                    'user_id',
                    'status',
                    'registered_at',
                    'role',
                    'skills',
                    'has_transportation',
                    'has_participated_before',
                    'emergency_contact'
                ]);

            return response()->json([
                'success' => true,
                'registrations' => $registrations,
                'total' => $registrations->count(),
                'event' => [
                    'id' => $event->id,
                    'title' => $event->title,
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des inscriptions pour l\'événement ID ' . $event->id . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des inscriptions. Veuillez réessayer.'
            ], 500);
        }
    }
}
