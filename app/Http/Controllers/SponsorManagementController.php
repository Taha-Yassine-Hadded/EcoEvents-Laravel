<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sponsor;
use App\Models\Package;
use App\Models\Sponsorship;
use App\Models\SponsorshipTemp;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class SponsorManagementController extends Controller
{
    // ==================== SPONSOR PROFILE MANAGEMENT ====================
    
    /**
     * Afficher le profil sponsor complet
     */
    public function showProfile(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return redirect()->route('home')->with('error', 'Accès non autorisé.');
        }

        // Récupérer ou créer le profil sponsor
        $sponsor = null;
        try {
            $sponsor = $user->sponsor;
        } catch (\Exception $e) {
            // Table sponsors n'existe pas ou erreur
            $sponsor = null;
        }
        
        // Statistiques du sponsor (valeurs par défaut si pas de données)
        $stats = [
            'total_sponsorships' => 0,
            'total_invested' => 0,
            'pending_sponsorships' => 0,
            'approved_sponsorships' => 0,
        ];
        
        if ($sponsor) {
            try {
                $stats = [
                    'total_sponsorships' => $sponsor->sponsorships()->count(),
                    'total_invested' => $sponsor->sponsorships()->sum('amount'),
                    'pending_sponsorships' => $sponsor->sponsorships()->where('status', 'pending')->count(),
                    'approved_sponsorships' => $sponsor->sponsorships()->where('status', 'approved')->count(),
                ];
            } catch (\Exception $e) {
                // Tables n'existent pas encore
            }
        }

        return view('pages.backOffice.sponsor-profile', compact('user', 'sponsor', 'stats'));
    }

    /**
     * Mettre à jour le profil sponsor
     */
    public function updateProfile(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        try {
            // Validation flexible
            $rules = [];
            $updateData = [];
            
            if ($request->has('name') && !empty($request->name)) {
                $rules['name'] = 'string|max:255';
                $updateData['name'] = $request->name;
            }
            
            if ($request->has('email') && !empty($request->email)) {
                $rules['email'] = 'email|unique:users,email,' . $user->id;
                $updateData['email'] = $request->email;
            }
            
            if ($request->has('phone')) {
                $rules['phone'] = 'nullable|string|max:20';
                $updateData['phone'] = $request->phone;
            }
            
            if ($request->has('address')) {
                $rules['address'] = 'nullable|string|max:255';
                $updateData['address'] = $request->address;
            }
            
            if ($request->has('city')) {
                $rules['city'] = 'nullable|string|max:100';
                $updateData['city'] = $request->city;
            }
            
            if ($request->has('bio')) {
                $rules['bio'] = 'nullable|string|max:1000';
                $updateData['bio'] = $request->bio;
            }
            
            if ($request->hasFile('profile_image')) {
                $rules['profile_image'] = 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048';
            }
            
            // Valider seulement si il y a des règles
            if (!empty($rules)) {
                $request->validate($rules);
            }

            // Mise à jour des données utilisateur
            if (!empty($updateData)) {
                $user->update($updateData);
            }

            // Gérer l'upload de l'image de profil
            if ($request->hasFile('profile_image')) {
                // Supprimer l'ancienne image si elle existe
                if ($user->profile_image) {
                    Storage::disk('public')->delete($user->profile_image);
                }
                
                $user->update([
                    'profile_image' => $request->file('profile_image')->store('profile-images', 'public')
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour avec succès !',
                'user' => $user
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SponsorManagementController: Erreur updateProfile', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur lors de la mise à jour du profil.'], 500);
        }
    }

    /**
     * Mettre à jour le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $user = $request->auth;

        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        try {
            $validated = $request->validate([
                'current_password' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if (!Hash::check($validated['current_password'], $user->password)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'current_password' => ['Le mot de passe actuel est incorrect.'],
                ]);
            }

            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            return response()->json(['success' => true, 'message' => 'Mot de passe mis à jour avec succès !']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SponsorManagementController: Erreur updatePassword', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur lors de la mise à jour du mot de passe.'], 500);
        }
    }

    /**
     * Supprimer le profil du sponsor
     */
    public function deleteProfile(Request $request)
    {
        $user = $request->auth;

        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        try {
            // Supprimer tous les sponsorships de l'utilisateur
            SponsorshipTemp::where('user_id', $user->id)->delete();

            // Supprimer l'image de profil si elle existe
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // Supprimer le compte utilisateur
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Votre profil a été supprimé avec succès. Vous allez être redirigé vers la page d\'accueil.',
                'redirect' => route('home')
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SponsorManagementController: Erreur deleteProfile', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Erreur lors de la suppression du profil.'], 500);
        }
    }

    // ==================== SPONSOR COMPANY MANAGEMENT ====================

    /**
     * Afficher les informations de l'entreprise sponsor
     */
    public function showCompany(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return redirect()->route('home')->with('error', 'Accès non autorisé.');
        }

        // Utiliser les données de l'utilisateur directement
        // car nous stockons maintenant les infos entreprise dans la table users
        $sponsor = (object) [
            'company_name' => $user->company_name,
            'website' => $user->website,
            'phone' => $user->phone,
            'description' => $user->bio, // Utiliser bio comme description
            'logo' => $user->logo,
            'status' => 'active', // Statut par défaut pour les sponsors
        ];

        // Calculer les statistiques des sponsorships
        $sponsorshipsCount = 0;
        $totalAmount = 0;
        $activeSponsorships = 0;
        
        try {
            $sponsorships = SponsorshipTemp::where('user_id', $user->id)->get();
            $sponsorshipsCount = $sponsorships->count();
            $totalAmount = $sponsorships->sum('amount');
            $activeSponsorships = $sponsorships->whereIn('status', ['pending', 'approved'])->count();
        } catch (\Exception $e) {
            // Table n'existe pas encore
        }

        $stats = (object) [
            'sponsorships_count' => $sponsorshipsCount,
            'total_amount' => $totalAmount,
            'active_sponsorships' => $activeSponsorships,
        ];
        
        return view('pages.backOffice.sponsor-company', compact('user', 'sponsor', 'stats'));
    }

    /**
     * Créer ou mettre à jour les informations de l'entreprise
     */
    public function updateCompany(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        try {
            $validated = $request->validate([
                'company_name' => 'required|string|max:255',
                'website' => 'nullable|url',
                'phone' => 'nullable|string|max:20',
                'description' => 'nullable|string|max:1000',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            ]);

            // Sauvegarder les données dans la table users
            $updateData = [
                'company_name' => $validated['company_name'],
                'website' => $validated['website'],
                'phone' => $validated['phone'],
                'bio' => $validated['description'], // Utiliser bio pour description
            ];

            // Gérer l'upload du logo
            if ($request->hasFile('logo')) {
                // Supprimer l'ancien logo s'il existe
                if ($user->logo) {
                    Storage::disk('public')->delete($user->logo);
                }
                $updateData['logo'] = $request->file('logo')->store('sponsor-logos', 'public');
            }

            // Mettre à jour l'utilisateur
            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Informations de l\'entreprise mises à jour avec succès !',
                'data' => $user->fresh() // Récupérer les données mises à jour
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SponsorManagementController: Erreur updateCompany', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur lors de la mise à jour des informations.'], 500);
        }
    }

    // ==================== CAMPAIGNS MANAGEMENT ====================

    /**
     * Afficher les campagnes disponibles
     */
    public function showCampaigns(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return redirect()->route('home')->with('error', 'Accès non autorisé.');
        }

        try {
            // Récupérer toutes les campagnes (pas de filtre par date pour les tests)
            $campaigns = Campaign::orderBy('created_at', 'desc')
                ->paginate(12);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SponsorManagementController: Erreur showCampaigns', ['error' => $e->getMessage()]);
            // Créer une pagination vide en cas d'erreur
            $campaigns = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 12);
        }

        return view('pages.backOffice.sponsor-campaigns', compact('user', 'campaigns'));
    }

    /**
     * Afficher les détails d'une campagne
     */
    public function showCampaignDetails(Request $request, $id)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return redirect()->route('home')->with('error', 'Accès non autorisé.');
        }

        $campaign = Campaign::findOrFail($id);
        
        // Vérifier si l'utilisateur a déjà proposé un sponsorship pour cette campagne
        $existingSponsorship = null;
        try {
            $existingSponsorship = SponsorshipTemp::where('user_id', $user->id)
                ->where('campaign_id', $id)
                ->first();
        } catch (\Exception $e) {
            // Table n'existe pas encore
        }
        
        // Créer des packages de test directement
        $packages = collect([
            (object) [
                'id' => 1,
                'name' => 'Bronze',
                'price' => 500,
                'description' => 'Package Bronze - Parfait pour les petites entreprises',
                'benefits' => [
                    'Logo sur les supports de communication',
                    'Mention dans les réseaux sociaux',
                    'Stand de 2m²'
                ],
                'is_active' => true
            ],
            (object) [
                'id' => 2,
                'name' => 'Silver',
                'price' => 1000,
                'description' => 'Package Silver - Idéal pour les entreprises moyennes',
                'benefits' => [
                    'Logo sur les supports de communication',
                    'Mention dans les réseaux sociaux',
                    'Stand de 4m²',
                    'Intervention de 5 minutes',
                    'Distribution de flyers'
                ],
                'is_active' => true
            ],
            (object) [
                'id' => 3,
                'name' => 'Gold',
                'price' => 2000,
                'description' => 'Package Gold - Pour les grandes entreprises',
                'benefits' => [
                    'Logo sur les supports de communication',
                    'Mention dans les réseaux sociaux',
                    'Stand de 6m²',
                    'Intervention de 10 minutes',
                    'Distribution de flyers',
                    'Bannières publicitaires',
                    'Interview média'
                ],
                'is_active' => true
            ]
        ]);

        return view('pages.backOffice.sponsor-campaign-details', compact('user', 'campaign', 'packages', 'existingSponsorship'));
    }

    // ==================== SPONSORSHIPS MANAGEMENT ====================

    /**
     * Afficher les sponsorships du sponsor
     */
    public function showSponsorships(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return redirect()->route('home')->with('error', 'Accès non autorisé.');
        }

        // Récupérer les sponsorships de l'utilisateur depuis la table temporaire
        try {
            $sponsorships = SponsorshipTemp::where('user_id', $user->id)
                ->with(['campaign'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SponsorManagementController: Erreur showSponsorships', ['error' => $e->getMessage()]);
            $sponsorships = collect([]);
        }

        return view('pages.backOffice.sponsor-sponsorships', compact('user', 'sponsorships'));
    }

    /**
     * Créer un nouveau sponsorship
     */
    public function createSponsorship(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        try {
            $validated = $request->validate([
                'campaign_id' => 'required|exists:campaigns,id',
                'package_id' => 'required|numeric|min:1', // Validation simplifiée
                'amount' => 'required|numeric|min:0',
                'notes' => 'nullable|string|max:1000',
            ]);

            // Vérifier si l'utilisateur a déjà proposé un sponsorship pour cette campagne
            $existingSponsorship = SponsorshipTemp::where('user_id', $user->id)
                ->where('campaign_id', $validated['campaign_id'])
                ->first();

            if ($existingSponsorship) {
                return response()->json([
                    'error' => 'Vous avez déjà proposé un sponsorship pour cette campagne. Vous ne pouvez proposer qu\'un seul sponsorship par campagne.'
                ], 422);
            }

            // Sauvegarder le sponsorship dans la table temporaire
            $sponsorship = SponsorshipTemp::create([
                'user_id' => $user->id,
                'campaign_id' => $validated['campaign_id'],
                'package_id' => $validated['package_id'],
                'package_name' => $this->getPackageName($validated['package_id']),
                'amount' => $validated['amount'],
                'status' => 'pending',
                'notes' => $validated['notes'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sponsorship proposé avec succès !',
                'sponsorship' => $sponsorship->load(['campaign'])
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SponsorManagementController: Erreur createSponsorship', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur lors de la création du sponsorship.'], 500);
        }
    }

    /**
     * Annuler un sponsorship
     */
    public function cancelSponsorship(Request $request, $id)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        try {
            // Récupérer le sponsorship depuis la table temporaire
            $sponsorship = SponsorshipTemp::where('user_id', $user->id)
                ->where('id', $id)
                ->first();

            if (!$sponsorship) {
                \Illuminate\Support\Facades\Log::info('Sponsorship non trouvé', ['user_id' => $user->id, 'sponsorship_id' => $id]);
                return response()->json(['error' => 'Sponsorship non trouvé.'], 404);
            }

            \Illuminate\Support\Facades\Log::info('Sponsorship trouvé avant suppression', [
                'id' => $sponsorship->id,
                'user_id' => $sponsorship->user_id,
                'campaign_id' => $sponsorship->campaign_id,
                'status' => $sponsorship->status
            ]);

            if ($sponsorship->status === 'completed') {
                return response()->json(['error' => 'Impossible d\'annuler un sponsorship terminé.'], 422);
            }

            if ($sponsorship->status === 'cancelled') {
                return response()->json(['error' => 'Ce sponsorship est déjà annulé.'], 422);
            }

            // Supprimer complètement le sponsorship au lieu de le marquer comme annulé
            $sponsorship->delete();

            \Illuminate\Support\Facades\Log::info('Sponsorship supprimé avec succès', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Sponsorship supprimé avec succès !'
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SponsorManagementController: Erreur cancelSponsorship', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur lors de l\'annulation du sponsorship.'], 500);
        }
    }

    // ==================== STATISTICS ====================

    /**
     * Afficher les statistiques du sponsor
     */
    public function showStatistics(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return redirect()->route('home')->with('error', 'Accès non autorisé.');
        }

        $sponsor = null;
        try {
            $sponsor = $user->sponsor;
        } catch (\Exception $e) {
            // Table sponsors n'existe pas ou erreur
            $sponsor = null;
        }
        
        if (!$sponsor) {
            $stats = [
                'total_sponsorships' => 0,
                'total_invested' => 0,
                'pending_sponsorships' => 0,
                'approved_sponsorships' => 0,
                'rejected_sponsorships' => 0,
                'completed_sponsorships' => 0,
            ];
        } else {
            try {
                $stats = [
                    'total_sponsorships' => $sponsor->sponsorships()->count(),
                    'total_invested' => $sponsor->sponsorships()->sum('amount'),
                    'pending_sponsorships' => $sponsor->sponsorships()->where('status', 'pending')->count(),
                    'approved_sponsorships' => $sponsor->sponsorships()->where('status', 'approved')->count(),
                    'rejected_sponsorships' => $sponsor->sponsorships()->where('status', 'rejected')->count(),
                    'completed_sponsorships' => $sponsor->sponsorships()->where('status', 'completed')->count(),
                ];
            } catch (\Exception $e) {
                // Tables n'existent pas encore
                $stats = [
                    'total_sponsorships' => 0,
                    'total_invested' => 0,
                    'pending_sponsorships' => 0,
                    'approved_sponsorships' => 0,
                    'rejected_sponsorships' => 0,
                    'completed_sponsorships' => 0,
                ];
            }
        }

        return view('pages.backOffice.sponsor-statistics', compact('user', 'stats'));
    }

    /**
     * Obtenir le nom du package par son ID
     */
    private function getPackageName($packageId)
    {
        $packages = [
            1 => 'Bronze',
            2 => 'Silver', 
            3 => 'Gold'
        ];
        
        return $packages[$packageId] ?? 'Package Inconnu';
    }
}