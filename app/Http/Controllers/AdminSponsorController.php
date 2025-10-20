<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SponsorshipTemp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminSponsorController extends Controller
{
    /**
     * Afficher la liste des sponsors
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'sponsor');

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sponsors = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.sponsors.index', compact('sponsors'));
    }

    /**
     * Retourner les sponsors en JSON pour l'API
     */
    public function getSponsorsData(Request $request)
    {
        $query = User::where('role', 'sponsor');

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sponsors = $query->orderBy('created_at', 'desc')->get();

        return response()->json($sponsors);
    }

    /**
     * Afficher les détails d'un sponsor
     */
    public function show(Request $request, $id)
    {
        $sponsor = User::where('role', 'sponsor')->findOrFail($id);
        
        // Si c'est une requête AJAX (pour la modal), retourner JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($sponsor);
        }
        
        // Sinon, retourner la vue normale
        $sponsorships = SponsorshipTemp::where('user_id', $sponsor->id)
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->get();

        // Statistiques
        $stats = [
            'total_sponsorships' => $sponsorships->count(),
            'total_amount' => $sponsorships->sum('amount'),
            'pending_sponsorships' => $sponsorships->where('status', 'pending')->count(),
            'approved_sponsorships' => $sponsorships->where('status', 'approved')->count(),
            'completed_sponsorships' => $sponsorships->where('status', 'completed')->count(),
        ];

        return view('admin.sponsors.show', compact('sponsor', 'sponsorships', 'stats'));
    }

    /**
     * Approuver un sponsor
     */
    public function approve($id)
    {
        $sponsor = User::where('role', 'sponsor')->findOrFail($id);
        
        $sponsor->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'message' => 'Sponsor approuvé avec succès !'
        ]);
    }

    /**
     * Rejeter un sponsor
     */
    public function reject($id)
    {
        $sponsor = User::where('role', 'sponsor')->findOrFail($id);
        
        $sponsor->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'message' => 'Sponsor rejeté avec succès !'
        ]);
    }

    /**
     * Activer/Désactiver un sponsor
     */
    public function toggleStatus($id)
    {
        $sponsor = User::where('role', 'sponsor')->findOrFail($id);
        
        $newStatus = $sponsor->status === 'active' ? 'inactive' : 'active';
        $sponsor->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => "Sponsor {$newStatus} avec succès !",
            'new_status' => $newStatus
        ]);
    }

    /**
     * Supprimer un sponsor
     */
    public function destroy($id)
    {
        $sponsor = User::where('role', 'sponsor')->findOrFail($id);
        
        // Supprimer les sponsorships
        SponsorshipTemp::where('user_id', $sponsor->id)->delete();
        
        // Supprimer l'image de profil
        if ($sponsor->profile_image) {
            Storage::disk('public')->delete($sponsor->profile_image);
        }
        
        // Supprimer le logo
        if ($sponsor->logo) {
            Storage::disk('public')->delete($sponsor->logo);
        }
        
        // Supprimer le compte
        $sponsor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sponsor supprimé avec succès !'
        ]);
    }

    /**
     * Afficher tous les sponsorships approuvés avec contrats
     */
    public function approvedSponsorships(Request $request)
    {
        $query = SponsorshipTemp::where('status', 'approved')
            ->with(['user', 'event'])
            ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            })->orWhereHas('event', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sponsorships = $query->paginate(15);

        return view('admin.sponsors.approved-sponsorships', compact('sponsorships'));
    }

    /**
     * Afficher toutes les propositions de sponsoring en attente
     */
    public function pendingSponsorships(Request $request)
    {
        $query = SponsorshipTemp::where('status', 'pending')
            ->with(['user', 'event'])
            ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            })->orWhereHas('event', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sponsorships = $query->paginate(15);

        return view('admin.sponsors.pending-sponsorships', compact('sponsorships'));
    }

    /**
     * Approuver une proposition de sponsoring
     */
    public function approveSponsorship($id)
    {
        try {
            $sponsorship = SponsorshipTemp::findOrFail($id);
            
            // Vérifier que la proposition est bien en attente
            if ($sponsorship->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'error' => 'Cette proposition ne peut plus être approuvée (statut: ' . $sponsorship->status . ')'
                ], 422);
            }
            
            // Mettre à jour le statut et les détails de l'événement
            $event = $sponsorship->event;
            $updateData = ['status' => 'approved'];
            
            // Mettre à jour les détails de l'événement si pas déjà sauvegardés
            if (!$sponsorship->event_title && $event) {
                $updateData['event_title'] = $event->title;
                $updateData['event_description'] = $event->description;
                $updateData['event_date'] = $event->date;
                $updateData['event_location'] = $event->location;
            }
            
            $sponsorship->update($updateData);

            // Générer le contrat PDF
            $contractData = $this->generateContractData($sponsorship);
            
            // Sauvegarder un snapshot de contrat immédiatement et stocker son chemin
            // On stocke un HTML statique pour consultation ultérieure fiable
            $contractDir = 'contracts/' . $sponsorship->id;
            $contractFile = 'contrat_sponsorship_' . $sponsorship->id . '_' . date('Ymd_His') . '.html';
            try {
                $html = app(\App\Http\Controllers\ContractController::class)->
                    // Utiliser la méthode privée via génération directe HTML
                    // Fallback: reconstruire minimalement ici si nécessaire
                    // On appelle une méthode publique de snapshot si dispo
                    // Ici, on regénère les données et vue dédiée existante
                    // mais plus simple: réutiliser la vue blade existante
                    // Pour rester simple et robuste, on génère via la vue blade
                    // en important la vue contrats existante si approuvé
                    // Cependant, la vue blade dépend du rendu HTTP.
                    // On garde l'approche du ContractController via HTML generator
                    // en dupliquant la logique: on instancie et appelle generateContractPDF
                    // generateContractPDF est private, donc on la recopie ici rapidement
                    // => plus simple: on recompose HTML minimal depuis generateContractData
                    // Pour éviter la duplication complexe, on appelle la route view ultérieurement.
                    // Compromis: on génère un HTML simple avec les données
                    // (voir ci-dessous)
                    __invoke();
            } catch (\Throwable $t) {
                // Ignorer, nous allons construire le HTML ici
            }

            // Construction HTML simple basée sur generateContractData
            $contractData = $this->generateContractData($sponsorship);
            $htmlSnapshot = '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Contrat ' .
                htmlspecialchars($contractData['contract_number']) .
                '</title></head><body><h1>Contrat de Sponsoring</h1>' .
                '<p><strong>Contrat:</strong> ' . htmlspecialchars($contractData['contract_number']) . '</p>' .
                '<p><strong>Sponsor:</strong> ' . htmlspecialchars($contractData['sponsor_name']) . ' (' . htmlspecialchars($contractData['sponsor_company']) . ')</p>' .
                '<p><strong>Événement:</strong> ' . htmlspecialchars($contractData['event_title']) . ' - ' . htmlspecialchars((string) $contractData['event_date']) . '</p>' .
                '<p><strong>Package:</strong> ' . htmlspecialchars($contractData['package_name']) . '</p>' .
                '<p><strong>Montant:</strong> ' . htmlspecialchars((string) $contractData['amount']) . ' €</p>' .
                '<p><strong>Date d\'approbation:</strong> ' . htmlspecialchars($contractData['approval_date']->format('d/m/Y H:i')) . '</p>' .
                '<hr><p>Ce snapshot a été généré automatiquement lors de l\'approbation.</p></body></html>';

            \Illuminate\Support\Facades\Storage::disk('local')->put($contractDir . '/' . $contractFile, $htmlSnapshot);
            $sponsorship->update(['contract_pdf' => $contractDir . '/' . $contractFile]);
            
            return response()->json([
                'success' => true,
                'message' => 'Proposition de sponsoring approuvée avec succès !',
                'contract_generated' => true,
                'contract_data' => $contractData,
                'contract_download_url' => route('contracts.sponsorship.download', $id),
                'contract_view_url' => route('contracts.sponsorship.view', $id)
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AdminSponsorController: Erreur approveSponsorship', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de l\'approbation de la proposition'
            ], 500);
        }
    }

    /**
     * Générer les données du contrat
     */
    private function generateContractData($sponsorship)
    {
        return [
            'contract_number' => 'SPONS-' . str_pad($sponsorship->id, 6, '0', STR_PAD_LEFT) . '-' . date('Y'),
            'sponsor_name' => $sponsorship->user->name,
            'sponsor_company' => $sponsorship->user->company_name ?? 'Entreprise non spécifiée',
            'sponsor_email' => $sponsorship->user->email,
            'event_title' => $sponsorship->event_title ?? 'Événement non spécifié',
            'event_date' => $sponsorship->event_date ?? now(),
            'package_name' => $sponsorship->package_name,
            'amount' => $sponsorship->amount,
            'notes' => $sponsorship->notes,
            'approval_date' => now(),
        ];
    }

    /**
     * Afficher le contrat PDF
     */
    public function viewContract($id)
    {
        $sponsorship = SponsorshipTemp::findOrFail($id);
        
        if ($sponsorship->status !== 'approved') {
            abort(404, 'Contrat non disponible');
        }

        return view('contracts.sponsorship-contract', compact('sponsorship'));
    }

    /**
     * Rejeter une proposition de sponsoring
     */
    public function rejectSponsorship($id)
    {
        try {
            $sponsorship = SponsorshipTemp::findOrFail($id);
            
            // Vérifier que la proposition est bien en attente
            if ($sponsorship->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'error' => 'Cette proposition ne peut plus être rejetée (statut: ' . $sponsorship->status . ')'
                ], 422);
            }
            
            $sponsorship->update(['status' => 'rejected']);

            return response()->json([
                'success' => true,
                'message' => 'Proposition de sponsoring rejetée avec succès !'
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AdminSponsorController: Erreur rejectSponsorship', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors du rejet de la proposition'
            ], 500);
        }
    }

    /**
     * Marquer un sponsoring comme terminé
     */
    public function completeSponsorship($id)
    {
        $sponsorship = SponsorshipTemp::findOrFail($id);
        
        $sponsorship->update(['status' => 'completed']);

        return response()->json([
            'success' => true,
            'message' => 'Sponsoring marqué comme terminé !'
        ]);
    }

    /**
     * Mettre à jour les informations d'un sponsor
     */
    public function update(Request $request, $id)
    {
        $sponsor = User::where('role', 'sponsor')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'company_name' => 'nullable|string|max:255',
            'website' => 'nullable|url',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,approved,rejected,active,inactive',
        ]);

        $sponsor->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Informations du sponsor mises à jour avec succès !'
        ]);
    }
}