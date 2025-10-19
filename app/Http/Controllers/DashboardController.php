<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->auth;
            if (!$user) {
                return response()->json(['error' => 'Veuillez vous connecter pour accéder au tableau de bord.'], 401);
            }
            // Check if user has admin or organizer role
            if (!in_array($user->role, ['admin', 'organizer'])) {
                return redirect()->route('home')->with('error', 'Accès non autorisé : rôle insuffisant.');
            }

            // Get statistics based on user role
            if ($user->role === 'admin') {
                // Admin sees all data
                $totalUsers = User::count();
                $totalEvents = Event::count();
                $totalRegistrations = Registration::count();
                $upcomingEvents = Event::where('status', 'upcoming')->count();
                $ongoingEvents = Event::where('status', 'ongoing')->count();
                $completedEvents = Event::where('status', 'completed')->count();
                $cancelledEvents = Event::where('status', 'cancelled')->count();
            } else {
                // Organizer sees only their data
                $totalUsers = User::whereHas('registrations.event', function($q) use ($user) {
                    $q->where('organizer_id', $user->id);
                })->count();
                $totalEvents = $user->events()->count();
                $totalRegistrations = Registration::whereHas('event', function($q) use ($user) {
                    $q->where('organizer_id', $user->id);
                })->count();
                $upcomingEvents = $user->events()->where('status', 'upcoming')->count();
                $ongoingEvents = $user->events()->where('status', 'ongoing')->count();
                $completedEvents = $user->events()->where('status', 'completed')->count();
                $cancelledEvents = $user->events()->where('status', 'cancelled')->count();
            }

            return view('pages.backOffice.dashboard', compact(
                'user', 'totalUsers', 'totalEvents', 'totalRegistrations',
                'upcomingEvents', 'ongoingEvents', 'completedEvents', 'cancelledEvents'
            ));

            // Check if user has admin or organizer role
            if (!in_array($user->role, ['admin', 'organizer'])) {
                return redirect()->route('home')->with('error', 'Accès non autorisé : rôle insuffisant.');
            }

            // Get statistics based on user role
            if ($user->role === 'admin') {
                // Admin sees all data
                $totalUsers = User::count();
                $totalEvents = Event::count();
                $totalRegistrations = Registration::count();
                $upcomingEvents = Event::where('status', 'upcoming')->count();
                $ongoingEvents = Event::where('status', 'ongoing')->count();
                $completedEvents = Event::where('status', 'completed')->count();
                $cancelledEvents = Event::where('status', 'cancelled')->count();
                    } else {
                        // Organizer sees only their data
                        $totalUsers = User::whereHas('registrations.event', function($q) use ($user) {
                            $q->where('organizer_id', $user->id);
                        })->count();
                        $totalEvents = $user->events()->count();
                        $totalRegistrations = Registration::whereHas('event', function($q) use ($user) {
                            $q->where('organizer_id', $user->id);
                        })->count();
                        $upcomingEvents = $user->events()->where('status', 'upcoming')->count();
                        $ongoingEvents = $user->events()->where('status', 'ongoing')->count();
                        $completedEvents = $user->events()->where('status', 'completed')->count();
                        $cancelledEvents = $user->events()->where('status', 'cancelled')->count();
                    }

            return view('pages.backOffice.dashboard', compact(
                'user', 'totalUsers', 'totalEvents', 'totalRegistrations',
                'upcomingEvents', 'ongoingEvents', 'completedEvents', 'cancelledEvents'
            ));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('DashboardController: Erreur', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur d\'authentification.'], 500);
        }
    }
}
