<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminPackageController extends Controller
{
    /**
     * Afficher la liste des packages
     */
    public function index(Request $request)
    {
        try {
            $query = Package::with(['event']);

            // Filtre par événement
            if ($request->has('event_id') && $request->event_id) {
                $query->where('event_id', $request->event_id);
            }

            // Filtre par statut
            if ($request->has('status') && $request->status) {
                if ($request->status === 'active') {
                    $query->where('is_active', true);
                } elseif ($request->status === 'inactive') {
                    $query->where('is_active', false);
                }
            }

            // Recherche
            if ($request->has('search') && $request->search) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%");
                });
            }

            $packages = $query->ordered()->paginate(15)->appends($request->all());
            $events = Event::orderBy('title')->get();

            return view('admin.packages.index', compact('packages', 'events'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des packages admin: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Afficher le formulaire de création
     */
    public function create(Request $request)
    {
        try {
            $events = Event::orderBy('title')->get();
            $selectedEventId = $request->get('event_id');
            
            return view('admin.packages.create', compact('events', 'selectedEventId'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement du formulaire de création de package: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Créer un nouveau package
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'price' => 'required|numeric|min:0',
                'benefits' => 'nullable|array',
                'benefits.*' => 'string|max:255',
                'event_id' => 'required|exists:events,id',
                'is_active' => 'boolean',
                'is_featured' => 'boolean',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            // Traitement des bénéfices
            if (isset($validated['benefits'])) {
                $benefits = array_filter($validated['benefits'], function($benefit) {
                    return !empty(trim($benefit));
                });
                $validated['benefits'] = array_values($benefits);
            }

            $package = Package::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Package créé avec succès !',
                'redirect' => route('admin.packages.index')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du package: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la création'], 500);
        }
    }

    /**
     * Afficher les détails d'un package
     */
    public function show(Package $package)
    {
        try {
            $package->load(['event', 'sponsorships.user']);
            return view('admin.packages.show', compact('package'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des détails du package: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Package $package)
    {
        try {
            $events = Event::orderBy('title')->get();
            return view('admin.packages.edit', compact('package', 'events'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement du formulaire d\'édition du package: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Mettre à jour un package
     */
    public function update(Request $request, Package $package)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'price' => 'required|numeric|min:0',
                'benefits' => 'nullable|array',
                'benefits.*' => 'string|max:255',
                'event_id' => 'required|exists:events,id',
                'is_active' => 'boolean',
                'is_featured' => 'boolean',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            // Traitement des bénéfices
            if (isset($validated['benefits'])) {
                $benefits = array_filter($validated['benefits'], function($benefit) {
                    return !empty(trim($benefit));
                });
                $validated['benefits'] = array_values($benefits);
            }

            $package->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Package mis à jour avec succès !',
                'redirect' => route('admin.packages.index')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du package: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la mise à jour'], 500);
        }
    }

    /**
     * Supprimer un package
     */
    public function destroy(Package $package)
    {
        try {
            // Vérifier s'il y a des sponsorships liés
            $sponsorshipsCount = $package->sponsorships()->count();
            
            if ($sponsorshipsCount > 0) {
                return response()->json([
                    'success' => false,
                    'error' => "Impossible de supprimer ce package car il est lié à {$sponsorshipsCount} sponsorship(s)."
                ], 422);
            }

            $package->delete();

            return response()->json([
                'success' => true,
                'message' => 'Package supprimé avec succès !'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du package: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la suppression'], 500);
        }
    }

    /**
     * Toggle le statut actif d'un package
     */
    public function toggleStatus(Package $package)
    {
        try {
            $package->update(['is_active' => !$package->is_active]);
            
            $status = $package->is_active ? 'activé' : 'désactivé';
            
            return response()->json([
                'success' => true,
                'message' => "Package {$status} avec succès !",
                'new_status' => $package->is_active
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors du changement de statut du package: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors du changement de statut'], 500);
        }
    }

    /**
     * Dupliquer un package
     */
    public function duplicate(Package $package)
    {
        try {
            $newPackage = $package->replicate();
            $newPackage->name = $package->name . ' (Copie)';
            $newPackage->is_active = false; // Désactiver la copie par défaut
            $newPackage->save();

            return response()->json([
                'success' => true,
                'message' => 'Package dupliqué avec succès !',
                'redirect' => route('admin.packages.edit', $newPackage->id)
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la duplication du package: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la duplication'], 500);
        }
    }
}