<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Event;
use App\Models\SponsorshipTemp;
use App\Models\Category;
use App\Services\EventRecommendationAI;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class SponsorApiController extends Controller
{
    protected $recommendationAI;
    protected $notificationService;

    public function __construct(EventRecommendationAI $recommendationAI, NotificationService $notificationService)
    {
        $this->recommendationAI = $recommendationAI;
        $this->notificationService = $notificationService;
    }

    // ==================== AUTHENTIFICATION ====================

    /**
     * Enregistrement d'un nouveau sponsor
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'company_name' => 'required|string|min:2|max:255',
                'phone' => 'nullable|string|max:20',
                'city' => 'nullable|string|max:100',
                'address' => 'nullable|string|max:255',
                'bio' => 'nullable|string|max:1000',
                'interests' => 'nullable|array|max:10',
                'budget' => 'nullable|numeric|min:0',
                'sector' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'company_name' => $request->company_name,
                'phone' => $request->phone,
                'city' => $request->city,
                'address' => $request->address,
                'bio' => $request->bio,
                'interests' => $request->interests,
                'budget' => $request->budget,
                'sector' => $request->sector,
                'role' => 'sponsor',
                'status' => 'pending'
            ]);

            $token = JWTAuth::fromUser($user);

            // Envoyer notification de bienvenue
            $this->notificationService->sendNotification(
                $user,
                'sponsor_registered',
                ['sponsor_name' => $user->name]
            );

            return response()->json([
                'success' => true,
                'message' => 'Sponsor enregistré avec succès',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('jwt.ttl') * 60
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('SponsorApiController: Erreur registration', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement'
            ], 500);
        }
    }

    /**
     * Connexion d'un sponsor
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $credentials = $request->only('email', 'password');

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Identifiants invalides'
                ], 401);
            }

            $user = auth()->user();

            if ($user->role !== 'sponsor') {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('jwt.ttl') * 60
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorApiController: Erreur login', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la connexion'
            ], 500);
        }
    }

    /**
     * Déconnexion
     */
    public function logout(): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'success' => true,
                'message' => 'Déconnexion réussie'
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorApiController: Erreur logout', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la déconnexion'
            ], 500);
        }
    }

    // ==================== PROFILE MANAGEMENT ====================

    /**
     * Récupérer le profil du sponsor
     */
    public function getProfile(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;

            return response()->json([
                'success' => true,
                'data' => [
                    'profile' => $user,
                    'stats' => [
                        'total_sponsorships' => SponsorshipTemp::where('user_id', $user->id)->count(),
                        'approved_sponsorships' => SponsorshipTemp::where('user_id', $user->id)->where('status', 'approved')->count(),
                        'pending_sponsorships' => SponsorshipTemp::where('user_id', $user->id)->where('status', 'pending')->count(),
                        'total_invested' => SponsorshipTemp::where('user_id', $user->id)->where('status', 'approved')->sum('amount'),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorApiController: Erreur getProfile', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du profil'
            ], 500);
        }
    }

    /**
     * Mettre à jour le profil du sponsor
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|min:2|max:255',
                'phone' => 'nullable|string|max:20',
                'city' => 'nullable|string|max:100',
                'address' => 'nullable|string|max:255',
                'bio' => 'nullable|string|max:1000',
                'interests' => 'nullable|array|max:10',
                'budget' => 'nullable|numeric|min:0',
                'sector' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $user->update($request->only([
                'name', 'phone', 'city', 'address', 'bio', 'interests', 'budget', 'sector'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour avec succès',
                'data' => ['profile' => $user]
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorApiController: Erreur updateProfile', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du profil'
            ], 500);
        }
    }

    /**
     * Upload d'avatar
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;

            $validator = Validator::make($request->all(), [
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048|dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Supprimer l'ancien avatar s'il existe
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // Sauvegarder le nouvel avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['profile_image' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar mis à jour avec succès',
                'data' => [
                    'avatar_url' => asset('storage/' . $path)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorApiController: Erreur uploadAvatar', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'upload de l\'avatar'
            ], 500);
        }
    }

    // ==================== COMPANY MANAGEMENT ====================

    /**
     * Récupérer les informations de l'entreprise
     */
    public function getCompany(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;

            return response()->json([
                'success' => true,
                'data' => [
                    'company' => [
                        'name' => $user->company_name,
                        'website' => $user->website,
                        'logo' => $user->logo,
                        'logo_url' => $user->logo ? asset('storage/' . $user->logo) : null,
                        'phone' => $user->phone,
                        'address' => $user->address,
                        'city' => $user->city,
                        'sector' => $user->sector,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorApiController: Erreur getCompany', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des informations de l\'entreprise'
            ], 500);
        }
    }

    /**
     * Mettre à jour les informations de l'entreprise
     */
    public function updateCompany(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;

            $validator = Validator::make($request->all(), [
                'company_name' => 'sometimes|string|min:2|max:255',
                'website' => 'nullable|url|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:100',
                'sector' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $user->update($request->only([
                'company_name', 'website', 'phone', 'address', 'city', 'sector'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Informations de l\'entreprise mises à jour avec succès',
                'data' => ['company' => $user]
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorApiController: Erreur updateCompany', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour des informations de l\'entreprise'
            ], 500);
        }
    }

    // ==================== EVENT DISCOVERY ====================

    /**
     * Découvrir des événements
     */
    public function discoverEvents(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;

            $query = Event::with(['category', 'packages', 'organizer'])
                ->where('status', 'upcoming')
                ->orderBy('date', 'asc');

            // Filtres optionnels
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('city')) {
                $query->where('location', 'like', '%' . $request->city . '%');
            }

            if ($request->has('min_budget') && $request->has('max_budget')) {
                $query->whereHas('packages', function($q) use ($request) {
                    $q->whereBetween('price', [$request->min_budget, $request->max_budget]);
                });
            }

            $events = $query->paginate($request->get('per_page', 10));

            return response()->json([
                'success' => true,
                'data' => $events
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorApiController: Erreur discoverEvents', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la découverte des événements'
            ], 500);
        }
    }

    /**
     * Obtenir les recommandations d'événements
     */
    public function getRecommendedEvents(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;
            $limit = $request->get('limit', 10);

            $recommendations = $this->recommendationAI->recommendEvents($user, $limit);

            return response()->json([
                'success' => true,
                'data' => [
                    'recommendations' => $recommendations,
                    'total' => count($recommendations)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorApiController: Erreur getRecommendedEvents', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des recommandations'
            ], 500);
        }
    }

    // ==================== ADMIN FUNCTIONS ====================

    /**
     * Liste des sponsors pour l'admin
     */
    public function adminIndex(Request $request): JsonResponse
    {
        try {
            $query = User::where('role', 'sponsor')
                ->withCount(['sponsorships' => function($q) {
                    $q->where('status', 'approved');
                }]);

            // Filtres
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%");
                });
            }

            $sponsors = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $sponsors
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorApiController: Erreur adminIndex', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des sponsors'
            ], 500);
        }
    }

    /**
     * Approuver un sponsor (Admin)
     */
    public function adminApprove(Request $request, $id): JsonResponse
    {
        try {
            $sponsor = User::where('role', 'sponsor')->findOrFail($id);
            
            $sponsor->update(['status' => 'approved']);

            // Envoyer notification au sponsor
            $this->notificationService->sendNotification(
                $sponsor,
                'sponsor_approved',
                ['sponsor_name' => $sponsor->name]
            );

            return response()->json([
                'success' => true,
                'message' => 'Sponsor approuvé avec succès',
                'data' => ['sponsor' => $sponsor]
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorApiController: Erreur adminApprove', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'approbation du sponsor'
            ], 500);
        }
    }

    // ==================== UTILITY METHODS ====================

    /**
     * Recherche d'événements
     */
    public function searchEvents(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q');
            
            if (!$query) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terme de recherche requis'
                ], 400);
            }

            $events = Event::with(['category', 'packages'])
                ->where('status', 'upcoming')
                ->where(function($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('location', 'like', "%{$query}%");
                })
                ->paginate($request->get('per_page', 10));

            return response()->json([
                'success' => true,
                'data' => $events
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorApiController: Erreur searchEvents', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la recherche'
            ], 500);
        }
    }
}
