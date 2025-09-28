<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    // -------------------
    // FrontOffice: Public events (all roles)
    // -------------------
    public function index()
    {
        try {
            $events = Event::with('category', 'organizer')->paginate(10);
            return view('pages.frontOffice.events.index', compact('events'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des événements: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    public function backIndex()
    {
        try {
            $events = Event::with('category', 'organizer')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

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

    public function show(Event $event)
    {
        try {
            return view('pages.frontOffice.events.show', compact('event'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement de l\'événement ID ' . $event->id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Événement non trouvé'], 404);
        }
    }

    // -------------------
    // Organizer FrontOffice: Add/Edit own events
    // -------------------
    public function createAdmin(Request $request)
    {
        $this->authorizeRole($request, 'admin'); // Changed to allow admins

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
    $this->authorizeRole($request, 'admin');

    try {
        Log::info('StoreAdmin Request Data: ', $request->all());

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => ['required', 'date', 'after_or_equal:' . now()->toDateString()],
            'location' => 'required|string|max:255',
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
    public function showAdmin(Event $event)
    {
        try {
            $this->authorizeRole(request(), 'admin'); // Ensure only admins can view

            return view('pages.backOffice.events.show', compact('event'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des détails de l\'événement ID ' . $event->id . ' pour admin: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur ou accès interdit'], 403);
        }
    }

public function editAdmin(Request $request, Event $event)
{
    $this->authorizeRole($request, 'admin');

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
    $this->authorizeRole($request, 'admin');

    try {
        Log::info('UpdateAdmin Request Data: ', $request->all());

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => ['required', 'date', 'after_or_equal:' . now()->toDateString()],
            'location' => 'required|string|max:255',
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
        $this->authorizeRole($request, 'admin'); // Changed to allow admins

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

    // -------------------
    // Authorization helpers using JWT user
    // -------------------
    private function authorizeRole(Request $request, $role)
    {
        if ($request->auth->role !== 'admin' && $request->auth->role !== $role) {
            abort(403, 'Accès interdit');
        }
    }

    private function authorizeOrganizerEvent(Request $request, Event $event)
    {
        if ($request->auth->role === 'organizer' && $event->organizer_id !== $request->auth->id) {
            abort(403, 'Accès interdit');
        }
        // Admins can bypass this check
    }
}