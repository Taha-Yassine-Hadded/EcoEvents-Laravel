<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class EventController extends Controller
{
    // -------------------
    // FrontOffice: Public events (all roles)
    // -------------------
    public function index(Request $request)
    {
        try {
            // Base query with relationships
            $query = Event::with(['category', 'organizer', 'registrations']);
            
            // Apply search filter if provided
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('title', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%")
                      ->orWhere('location', 'like', "%{$searchTerm}%");
                });
            }
            
            // Apply category filter if provided
            if ($request->has('category') && !empty($request->category)) {
                $query->whereHas('category', function($q) use ($request) {
                    $q->where('name', $request->category);
                });
            }
            
            // Apply status filter if provided
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }
            
            // Apply organizer filter if provided (for organizers to see only their events)
            if ($request->has('organizer_filter') && $request->organizer_filter === 'mine' && $request->has('organizer_id')) {
                $organizerId = $request->get('organizer_id');
                
                // Validate that the organizer_id is numeric and exists
                if (is_numeric($organizerId)) {
                    $query->where('organizer_id', $organizerId);
                    Log::info('Applied organizer filter for user ID: ' . $organizerId);
                } else {
                    Log::warning('Invalid organizer_id provided: ' . $organizerId);
                }
            }
            
            // Order by date (upcoming events first)
            $query->orderBy('date', 'asc');
            
            // Paginate results
            $events = $query->paginate(16)->appends($request->all());
            
            // Get categories for sidebar
            $categories = Category::withCount('events')->orderBy('name')->get();
            
            // Get recent events for sidebar (limit 3)
            $recentEvents = Event::with(['category'])
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
            
            // Get statistics for sidebar
            $totalEvents = Event::count();
            $upcomingEvents = Event::where('status', 'upcoming')->count();
            $ongoingEvents = Event::where('status', 'ongoing')->count();
            
            return view('pages.frontOffice.events.index', compact(
                'events', 
                'categories', 
                'recentEvents', 
                'totalEvents', 
                'upcomingEvents', 
                'ongoingEvents'
            ));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des événements: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors du chargement des événements.');
        }
    }

    public function backIndex(Request $request)
    {
        try {
            $this->authorizeRole($request, 'admin,organizer');
            
            $user = $request->auth;
            $query = Event::with('category', 'organizer');
            
            // If user is organizer, only show their events
            if ($user->role === 'organizer') {
                $query->where('organizer_id', $user->id);
            }
            
            $events = $query->orderBy('created_at', 'desc')->paginate(10);

            // Prepare JS-friendly data
            $eventsForJs = $events->map(function($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'content' => $event->description,
                    'category' => $event->category->name ?? 'N/A',
                    'status' => $event->status,
                    'date' => $event->date?->toDateString(),
                    'location' => $event->location,
                    'img' => $event->img ? Storage::url($event->img) : null,
                    'created_at' => $event->created_at?->toDateString(),
                    'organizer' => $event->organizer ? [
                        'name' => $event->organizer->name,
                        'email' => $event->organizer->email
                    ] : null
                ];
            });

            return view('pages.backOffice.events.index', compact('events', 'eventsForJs'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des événements admin: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    public function show($id)
    {
        try {
            $event = Event::with(['category', 'organizer', 'registrations'])->findOrFail($id);
            
            // Get similar events (same category, different event, not cancelled)
            $similarEvents = Event::with(['category', 'organizer'])
                ->where('category_id', $event->category_id)
                ->where('id', '!=', $event->id)
                ->where('status', '!=', 'cancelled')
                ->take(6)
                ->get();

            return view('pages.frontOffice.events.show', compact('event', 'similarEvents'));
        } catch (\Exception $e) {
            \Log::error('Error loading event details: ' . $e->getMessage());
            return redirect()->route('front.events.index')->with('error', 'Événement introuvable.');
        }
    }

    // -------------------
    // Organizer FrontOffice: Add/Edit own events
    // -------------------
    public function store(Request $request)
    {
        try {
            $user = JWTAuth::user();
            
            // Validate user is organizer
            if (!$user || $user->role !== 'organizer') {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }
            
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date' => 'required|date|after:now',
                'location' => 'required|string|max:255',
                'capacity' => 'nullable|integer|min:1',
                'category_id' => 'required|exists:categories,id',
                'status' => 'required|in:upcoming,ongoing,completed,cancelled',
                'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]);
            
            $event = new Event();
            $event->title = $request->title;
            $event->description = $request->description;
            $event->date = $request->date;
            $event->location = $request->location;
            $event->capacity = $request->capacity;
            $event->category_id = $request->category_id;
            $event->status = $request->status;
            $event->organizer_id = $user->id;
            
            // Handle image upload
            if ($request->hasFile('img')) {
                $imagePath = $request->file('img')->store('events', 'public');
                $event->img = $imagePath;
            }
            
            $event->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Événement créé avec succès',
                'event' => $event
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error creating event: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'événement'
            ], 500);
        }
    }

    public function createAdmin(Request $request)
    {
        $this->authorizeRole($request, 'admin,organizer'); // Allow both admins and organizers

        try {
            $categories = Category::all();
            return view('pages.backOffice.events.create', compact('categories')); // Use backOffice view
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement du formulaire de création admin: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    public function storeAdmin(Request $request)
    {
    $this->authorizeRole($request, 'admin,organizer');

    try {
        Log::info('StoreAdmin Request Data: ', $request->all());

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => ['required', 'date', 'after_or_equal:' . now()->toDateString()],
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:ongoing,upcoming',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'capacity' => 'nullable|integer|min:0',
        ]);

        $event = new Event($validated);
        $event->organizer_id = $request->auth->id;
        if ($request->hasFile('img')) {
            $path = $request->file('img')->store('events', 'public');
            $event->img = $path;
        }
        $event->save();

        return response()->json(['success' => true, 'redirect' => route('admin.events.index')]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation Errors: ', $e->validator->errors()->toArray());
        throw $e;
    } catch (\Exception $e) {
        Log::error('Erreur lors de la création de l\'événement admin: ' . $e->getMessage(), ['exception' => $e]);
        return response()->json(['error' => 'Erreur lors de la création: ' . $e->getMessage()], 500);
    }
}

    // -------------------
    // BackOffice: Admin event details
    // -------------------
    public function showAdmin(Request $request, Event $event)
    {
        try {
            $this->authorizeRole($request, 'admin,organizer'); // Allow both admins and organizers
            $this->authorizeOrganizerEvent($request, $event); // Check if organizer owns the event

            return view('pages.backOffice.events.show', compact('event'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des détails de l\'événement ID ' . $event->id . ' pour admin: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur ou accès interdit'], 403);
        }
    }

public function editAdmin(Request $request, Event $event)
{
    $this->authorizeRole($request, 'admin,organizer');
    $this->authorizeOrganizerEvent($request, $event);

    try {
        $categories = Category::all();
        return view('pages.backOffice.events.edit', compact('event', 'categories'));
    } catch (\Exception $e) {
        Log::error('Erreur lors du chargement du formulaire d\'édition admin pour l\'événement ID ' . $event->id . ': ' . $e->getMessage());
        return response()->json(['error' => 'Erreur serveur'], 500);
    }
}

public function updateAdmin(Request $request, Event $event)
{
    $this->authorizeRole($request, 'admin,organizer');
    $this->authorizeOrganizerEvent($request, $event);

    try {
        Log::info('UpdateAdmin Request Data: ', $request->all());

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => ['required', 'date', 'after_or_equal:' . now()->toDateString()],
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:completed,cancelled,ongoing,upcoming',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'capacity' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('img')) {
            if ($event->img && Storage::disk('public')->exists($event->img)) {
                Storage::disk('public')->delete($event->img);
            }
            $path = $request->file('img')->store('events', 'public');
            $validated['img'] = $path;
        }

        $event->update($validated);

        return response()->json(['success' => true, 'redirect' => route('admin.events.index')]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation Errors: ', $e->validator->errors()->toArray());
        throw $e;
    } catch (\Exception $e) {
        Log::error('Erreur lors de la mise à jour de l\'événement ID ' . $event->id . ': ' . $e->getMessage(), ['exception' => $e]);
        return response()->json(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()], 500);
    }
}

    public function destroyAdmin(Request $request, Event $event)
    {
        $this->authorizeRole($request, 'admin,organizer');
        $this->authorizeOrganizerEvent($request, $event);

        try {
            if ($event->img && Storage::disk('public')->exists($event->img)) {
                Storage::disk('public')->delete($event->img);
            }
            $event->delete();
            return redirect()->route('admin.events.index')->with('success', 'Événement supprimé avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l\'événement ID ' . $event->id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la suppression'], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $user = JWTAuth::user();
            $event = Event::findOrFail($id);
            
            // Check if user is the organizer of this event
            if ($event->organizer_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date' => 'required|date',
                'location' => 'required|string|max:255',
                'capacity' => 'nullable|integer|min:1',
                'category_id' => 'required|exists:categories,id',
                'status' => 'required|in:upcoming,ongoing,completed,cancelled',
                'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
            
            $event->title = $request->title;
            $event->description = $request->description;
            $event->date = $request->date;
            $event->location = $request->location;
            $event->capacity = $request->capacity;
            $event->category_id = $request->category_id;
            $event->status = $request->status;
            
            // Handle image upload
            if ($request->hasFile('img')) {
                // Delete old image if exists
                if ($event->img && Storage::disk('public')->exists($event->img)) {
                    Storage::disk('public')->delete($event->img);
                }
                
                // Store new image
                $imagePath = $request->file('img')->store('events', 'public');
                $event->img = $imagePath;
            }
            
            $event->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Événement mis à jour avec succès',
                'event' => $event
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error updating event: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'événement'
            ], 500);
        }
    }

    // Organizer delete method for the standard route
    public function destroy(Event $event)
    {
        try {
            $user = JWTAuth::user();
            
            // Check if user is the organizer of this event
            if ($event->organizer_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }
            
            // Delete associated image if exists
            if ($event->img && Storage::disk('public')->exists($event->img)) {
                Storage::disk('public')->delete($event->img);
            }
            
            $event->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Événement supprimé avec succès'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error deleting event: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'événement'
            ], 500);
        }
    }



    // -------------------
    // Authorization helpers using JWT user
    // -------------------
    private function authorizeRole(Request $request, $roles)
    {
        $user = $request->auth;
        if (!$user) {
            abort(401, 'Non authentifié');
        }

        $allowedRoles = array_map('trim', explode(',', $roles));
        if (!in_array($user->role, $allowedRoles)) {
            abort(403, 'Accès interdit : rôle insuffisant');
        }
    }

    private function authorizeOrganizerEvent(Request $request, Event $event)
    {
        if ($request->auth->role === 'organizer' && $event->organizer_id !== $request->auth->id) {
            abort(403, 'Accès interdit');
        }
        // Admins can bypass this check
    }

    /**
     * Organizer Dashboard - View and manage events
     */
    public function organizerDashboard(Request $request)
    {
        try {
            $user = $request->auth ?? JWTAuth::user() ?? auth()->user();
            
            if (!$user || $user->role !== 'organizer') {
                return redirect()->route('login')->with('error', 'Accès non autorisé.');
            }

            // Get organizer's events with pagination and search
            $query = Event::with(['category', 'registrations'])
                ->where('organizer_id', $user->id);
            
            // Add search functionality
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('title', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%")
                      ->orWhere('location', 'like', "%{$searchTerm}%");
                });
            }
            
            // Add status filter
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }
            
            $events = $query->orderBy('created_at', 'desc')->paginate(6);
            
            // Get statistics
            $totalEvents = Event::where('organizer_id', $user->id)->count();
            $upcomingEvents = Event::where('organizer_id', $user->id)->where('status', 'upcoming')->count();
            $ongoingEvents = Event::where('organizer_id', $user->id)->where('status', 'ongoing')->count();
            $completedEvents = Event::where('organizer_id', $user->id)->where('status', 'completed')->count();
            $totalSubscribers = Registration::whereHas('event', function($q) use ($user) {
                $q->where('organizer_id', $user->id);
            })->count();

            return view('pages.frontOffice.organizer.dashboard', compact(
                'events', 'totalEvents', 'upcomingEvents', 'ongoingEvents', 
                'completedEvents', 'totalSubscribers'
            ));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement du tableau de bord organisateur: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors du chargement du tableau de bord.');
        }
    }

    /**
     * View subscribers for a specific event
     */
    public function eventSubscribers(Request $request, Event $event)
    {
        try {
            $user = $request->auth ?? JWTAuth::user() ?? auth()->user();
            
            if (!$user || $user->role !== 'organizer') {
                return redirect()->route('login')->with('error', 'Accès non autorisé.');
            }

            // Check if the event belongs to the organizer
            if ($event->organizer_id !== $user->id) {
                abort(403, 'Accès interdit à cet événement.');
            }

            // Get subscribers with pagination and search
            $query = Registration::with(['user', 'event'])
                ->where('event_id', $event->id);
            
            // Add search functionality
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->whereHas('user', function($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('email', 'like', "%{$searchTerm}%");
                });
            }
            
            $subscribers = $query->orderBy('created_at', 'desc')->paginate(10);
            
            return view('pages.frontOffice.organizer.event-subscribers', compact('event', 'subscribers'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des abonnés: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors du chargement des abonnés.');
        }
    }
}