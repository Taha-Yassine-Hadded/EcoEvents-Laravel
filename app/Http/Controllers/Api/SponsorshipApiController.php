<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SponsorshipTemp;
use App\Models\Event;
use App\Models\Package;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SponsorshipApiController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    // ==================== CRUD OPERATIONS ====================

    /**
     * Liste des sponsoring du sponsor
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;

            $query = SponsorshipTemp::where('user_id', $user->id)
                ->with(['event', 'user'])
                ->orderBy('created_at', 'desc');

            // Filtres
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('event_id')) {
                $query->where('event_id', $request->event_id);
            }

            if ($request->has('date_from') && $request->has('date_to')) {
                $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
            }

            $sponsorships = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $sponsorships
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorshipApiController: Erreur index', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des sponsoring'
            ], 500);
        }
    }

    /**
     * Détails d'un sponsoring
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $user = $request->auth;

            $sponsorship = SponsorshipTemp::where('user_id', $user->id)
                ->with(['event', 'user'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => ['sponsorship' => $sponsorship]
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorshipApiController: Erreur show', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Sponsoring non trouvé'
            ], 404);
        }
    }

    /**
     * Créer un nouveau sponsoring
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;

            $validator = Validator::make($request->all(), [
                'event_id' => 'required|exists:events,id',
                'package_id' => 'required|numeric|min:1',
                'amount' => 'required|numeric|min:0',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Vérifier si l'utilisateur a déjà proposé un sponsorship pour cet événement
            $existingSponsorship = SponsorshipTemp::where('user_id', $user->id)
                ->where('event_id', $request->event_id)
                ->first();

            if ($existingSponsorship) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous avez déjà proposé un sponsorship pour cet événement'
                ], 422);
            }

            // Récupérer les détails de l'événement et du package
            $event = Event::findOrFail($request->event_id);
            $package = Package::findOrFail($request->package_id);

            DB::beginTransaction();

            $sponsorship = SponsorshipTemp::create([
                'user_id' => $user->id,
                'event_id' => $request->event_id,
                'campaign_id' => $request->event_id, // Compatibilité
                'package_id' => $request->package_id,
                'package_name' => $package->name,
                'amount' => $request->amount,
                'status' => 'pending',
                'notes' => $request->notes,
                'event_title' => $event->title,
                'event_description' => $event->description ?? 'Aucune description disponible',
                'event_date' => $event->date ?? null,
                'event_location' => $event->location ?? 'Lieu non spécifié',
            ]);

            DB::commit();

            // Envoyer notification à l'admin
            $this->notificationService->sendNotification(
                User::where('role', 'admin')->first(),
                'new_sponsorship_proposal',
                [
                    'sponsor_name' => $user->name,
                    'event_title' => $event->title,
                    'amount' => $request->amount,
                    'sponsorship_id' => $sponsorship->id
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Proposition de sponsoring créée avec succès',
                'data' => ['sponsorship' => $sponsorship]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SponsorshipApiController: Erreur store', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du sponsoring'
            ], 500);
        }
    }

    /**
     * Mettre à jour un sponsoring
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $user = $request->auth;

            $sponsorship = SponsorshipTemp::where('user_id', $user->id)
                ->findOrFail($id);

            // Vérifier si le sponsoring peut être modifié
            if (!in_array($sponsorship->status, ['pending', 'draft'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce sponsoring ne peut plus être modifié'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'amount' => 'sometimes|numeric|min:0',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $sponsorship->update($request->only(['amount', 'notes']));

            return response()->json([
                'success' => true,
                'message' => 'Sponsoring mis à jour avec succès',
                'data' => ['sponsorship' => $sponsorship]
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorshipApiController: Erreur update', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du sponsoring'
            ], 500);
        }
    }

    /**
     * Supprimer un sponsoring
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $user = $request->auth;

            $sponsorship = SponsorshipTemp::where('user_id', $user->id)
                ->findOrFail($id);

            // Vérifier si le sponsoring peut être supprimé
            if (!in_array($sponsorship->status, ['pending', 'draft', 'rejected'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce sponsoring ne peut plus être supprimé'
                ], 422);
            }

            $sponsorship->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sponsoring supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorshipApiController: Erreur destroy', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du sponsoring'
            ], 500);
        }
    }

    // ==================== STATUS MANAGEMENT ====================

    /**
     * Annuler un sponsoring
     */
    public function cancel(Request $request, $id): JsonResponse
    {
        try {
            $user = $request->auth;

            $sponsorship = SponsorshipTemp::where('user_id', $user->id)
                ->findOrFail($id);

            if (!in_array($sponsorship->status, ['pending', 'approved'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce sponsoring ne peut plus être annulé'
                ], 422);
            }

            $sponsorship->update(['status' => 'cancelled']);

            // Envoyer notification à l'admin
            $this->notificationService->sendNotification(
                User::where('role', 'admin')->first(),
                'sponsorship_cancelled',
                [
                    'sponsor_name' => $user->name,
                    'event_title' => $sponsorship->event_title,
                    'sponsorship_id' => $sponsorship->id
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Sponsoring annulé avec succès',
                'data' => ['sponsorship' => $sponsorship]
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorshipApiController: Erreur cancel', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation du sponsoring'
            ], 500);
        }
    }

    /**
     * Réactiver un sponsoring
     */
    public function reactivate(Request $request, $id): JsonResponse
    {
        try {
            $user = $request->auth;

            $sponsorship = SponsorshipTemp::where('user_id', $user->id)
                ->findOrFail($id);

            if ($sponsorship->status !== 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Seuls les sponsoring annulés peuvent être réactivés'
                ], 422);
            }

            $sponsorship->update(['status' => 'pending']);

            return response()->json([
                'success' => true,
                'message' => 'Sponsoring réactivé avec succès',
                'data' => ['sponsorship' => $sponsorship]
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorshipApiController: Erreur reactivate', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la réactivation du sponsoring'
            ], 500);
        }
    }

    // ==================== BULK OPERATIONS ====================

    /**
     * Annulation en masse
     */
    public function bulkCancel(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;

            $validator = Validator::make($request->all(), [
                'sponsorship_ids' => 'required|array|min:1',
                'sponsorship_ids.*' => 'integer|exists:sponsorships_temp,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $sponsorships = SponsorshipTemp::where('user_id', $user->id)
                ->whereIn('id', $request->sponsorship_ids)
                ->whereIn('status', ['pending', 'approved'])
                ->get();

            $cancelledCount = 0;
            foreach ($sponsorships as $sponsorship) {
                $sponsorship->update(['status' => 'cancelled']);
                $cancelledCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "{$cancelledCount} sponsoring(s) annulé(s) avec succès",
                'data' => ['cancelled_count' => $cancelledCount]
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorshipApiController: Erreur bulkCancel', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation en masse'
            ], 500);
        }
    }

    // ==================== FILTERING & SEARCH ====================

    /**
     * Recherche de sponsoring
     */
    public function search(Request $request, $query): JsonResponse
    {
        try {
            $user = $request->auth;

            $sponsorships = SponsorshipTemp::where('user_id', $user->id)
                ->with(['event', 'user'])
                ->where(function($q) use ($query) {
                    $q->where('event_title', 'like', "%{$query}%")
                      ->orWhere('package_name', 'like', "%{$query}%")
                      ->orWhere('notes', 'like', "%{$query}%");
                })
                ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $sponsorships
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorshipApiController: Erreur search', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la recherche'
            ], 500);
        }
    }

    /**
     * Filtrer par statut
     */
    public function filterByStatus(Request $request, $status): JsonResponse
    {
        try {
            $user = $request->auth;

            $sponsorships = SponsorshipTemp::where('user_id', $user->id)
                ->where('status', $status)
                ->with(['event', 'user'])
                ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $sponsorships
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorshipApiController: Erreur filterByStatus', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du filtrage'
            ], 500);
        }
    }

    // ==================== EXPORT & REPORTING ====================

    /**
     * Export CSV des sponsoring
     */
    public function exportCsv(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;

            $sponsorships = SponsorshipTemp::where('user_id', $user->id)
                ->with(['event'])
                ->get();

            $csvData = [];
            $csvData[] = ['ID', 'Événement', 'Package', 'Montant', 'Statut', 'Date de création'];

            foreach ($sponsorships as $sponsorship) {
                $csvData[] = [
                    $sponsorship->id,
                    $sponsorship->event_title,
                    $sponsorship->package_name,
                    $sponsorship->amount,
                    $sponsorship->status,
                    $sponsorship->created_at->format('d/m/Y H:i')
                ];
            }

            $filename = 'sponsorships_' . $user->id . '_' . date('Ymd_His') . '.csv';
            $filepath = storage_path('app/exports/' . $filename);

            // Créer le répertoire s'il n'existe pas
            if (!file_exists(dirname($filepath))) {
                mkdir(dirname($filepath), 0755, true);
            }

            $file = fopen($filepath, 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);

            return response()->json([
                'success' => true,
                'message' => 'Export CSV généré avec succès',
                'data' => [
                    'filename' => $filename,
                    'download_url' => url('api/exports/' . $filename)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorshipApiController: Erreur exportCsv', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'export CSV'
            ], 500);
        }
    }

    /**
     * Rapport de synthèse
     */
    public function getSummaryReport(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;

            $sponsorships = SponsorshipTemp::where('user_id', $user->id)->get();

            $summary = [
                'total_sponsorships' => $sponsorships->count(),
                'pending_sponsorships' => $sponsorships->where('status', 'pending')->count(),
                'approved_sponsorships' => $sponsorships->where('status', 'approved')->count(),
                'rejected_sponsorships' => $sponsorships->where('status', 'rejected')->count(),
                'cancelled_sponsorships' => $sponsorships->where('status', 'cancelled')->count(),
                'total_invested' => $sponsorships->where('status', 'approved')->sum('amount'),
                'average_investment' => $sponsorships->where('status', 'approved')->avg('amount'),
                'success_rate' => $sponsorships->count() > 0 
                    ? round(($sponsorships->where('status', 'approved')->count() / $sponsorships->count()) * 100, 2)
                    : 0
            ];

            return response()->json([
                'success' => true,
                'data' => ['summary' => $summary]
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorshipApiController: Erreur getSummaryReport', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du rapport'
            ], 500);
        }
    }

    // ==================== WEBHOOKS ====================

    /**
     * Gérer le succès de paiement
     */
    public function handlePaymentSuccess(Request $request): JsonResponse
    {
        try {
            // Logique de traitement du paiement réussi
            // Mise à jour du statut du sponsoring, etc.

            return response()->json([
                'success' => true,
                'message' => 'Paiement traité avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorshipApiController: Erreur handlePaymentSuccess', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du traitement du paiement'
            ], 500);
        }
    }
}
