<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    /**
     * Liste des catégories
     */
    public function index(Request $request)
    {
        try {
            $user = $request->auth;
            if (!$user) {
                return response()->json(['error' => 'Veuillez vous connecter pour accéder aux catégories.'], 401);
            }

            // Check if user has admin or organizer role
            if (!in_array($user->role, ['admin', 'organizer'])) {
                return redirect()->route('home')->with('error', 'Accès non autorisé : rôle insuffisant.');
            }

            $categories = Category::paginate(10);
            return view('pages.backOffice.categories.index', compact('categories'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des catégories: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Formulaire de création
     */
    public function create(Request $request)
    {
        try {
            $user = $request->auth;
            if (!$user) {
                return response()->json(['error' => 'Veuillez vous connecter pour accéder aux catégories.'], 401);
            }

            // Check if user has admin or organizer role
            if (!in_array($user->role, ['admin', 'organizer'])) {
                return redirect()->route('home')->with('error', 'Accès non autorisé : rôle insuffisant.');
            }

            return view('pages.backOffice.categories.create');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage du formulaire de création de catégorie: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Créer une catégorie
     */
    public function store(Request $request)
    {
        try {
            $user = $request->auth;
            if (!$user) {
                return response()->json(['error' => 'Veuillez vous connecter pour accéder aux catégories.'], 401);
            }

            // Check if user has admin or organizer role
            if (!in_array($user->role, ['admin', 'organizer'])) {
                return response()->json(['error' => 'Accès non autorisé : rôle insuffisant.'], 403);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories',
                'description' => 'nullable|string|max:500',
            ]);

            $category = Category::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Catégorie créée avec succès.',
                'redirect' => route('admin.categories.index')
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de catégorie: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la création'], 500);
        }
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Request $request, Category $category)
    {
        try {
            $user = $request->auth;
            if (!$user) {
                return response()->json(['error' => 'Veuillez vous connecter pour accéder aux catégories.'], 401);
            }

            // Check if user has admin or organizer role
            if (!in_array($user->role, ['admin', 'organizer'])) {
                return redirect()->route('home')->with('error', 'Accès non autorisé : rôle insuffisant.');
            }

            return view('pages.backOffice.categories.edit', compact('category'));
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'édition de la catégorie ID ' . $category->id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Mettre à jour une catégorie
     */
    public function update(Request $request, Category $category)
    {
        try {
            $user = $request->auth;
            if (!$user) {
                return response()->json(['error' => 'Veuillez vous connecter pour accéder aux catégories.'], 401);
            }

            // Check if user has admin or organizer role
            if (!in_array($user->role, ['admin', 'organizer'])) {
                return response()->json(['error' => 'Accès non autorisé : rôle insuffisant.'], 403);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
                'description' => 'nullable|string|max:500', // Add description validation
            ]);

            $category->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Catégorie mise à jour avec succès.',
                'redirect' => route('admin.categories.index')
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la catégorie ID ' . $category->id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la mise à jour'], 500);
        }
    }

    /**
     * Supprimer une catégorie
     */
    public function destroy(Request $request, Category $category)
    {
        try {
            $user = $request->auth;
            if (!$user) {
                return response()->json(['error' => 'Veuillez vous connecter pour accéder aux catégories.'], 401);
            }

            // Check if user has admin or organizer role
            if (!in_array($user->role, ['admin', 'organizer'])) {
                return response()->json(['error' => 'Accès non autorisé : rôle insuffisant.'], 403);
            }

            if (!$category) {
                return response()->json(['error' => 'Catégorie non trouvée'], 404);
            }
            $category->delete();
            return response()->json([
                'success' => true,
                'message' => 'Catégorie supprimée avec succès.'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la catégorie ID ' . ($category->id ?? 'unknown') . ': ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la suppression'], 500);
        }
    }
}
