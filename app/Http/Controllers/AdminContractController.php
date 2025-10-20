<?php

namespace App\Http\Controllers;

use App\Models\SponsorshipTemp;
use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AdminContractController extends Controller
{
    /**
     * Afficher la liste de tous les contrats
     */
    public function index(Request $request)
    {
        $query = SponsorshipTemp::where('status', 'approved')
            ->whereNotNull('contract_pdf')
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

        $contracts = $query->paginate(15);

        // Statistiques
        $stats = [
            'total_contracts' => SponsorshipTemp::where('status', 'approved')->whereNotNull('contract_pdf')->count(),
            'total_amount' => SponsorshipTemp::where('status', 'approved')->whereNotNull('contract_pdf')->sum('amount'),
            'this_month' => SponsorshipTemp::where('status', 'approved')
                ->whereNotNull('contract_pdf')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'unique_sponsors' => SponsorshipTemp::where('status', 'approved')
                ->whereNotNull('contract_pdf')
                ->distinct('user_id')
                ->count('user_id'),
        ];

        return view('admin.contracts.index', compact('contracts', 'stats'));
    }

    /**
     * Afficher les détails d'un contrat
     */
    public function show($id)
    {
        $sponsorship = SponsorshipTemp::where('status', 'approved')
            ->whereNotNull('contract_pdf')
            ->with(['user', 'event'])
            ->findOrFail($id);

        return view('admin.contracts.show', compact('sponsorship'));
    }

    /**
     * Télécharger un contrat
     */
    public function download($id)
    {
        try {
            $sponsorship = SponsorshipTemp::where('status', 'approved')
                ->whereNotNull('contract_pdf')
                ->findOrFail($id);

            $contractPath = $sponsorship->contract_pdf;

            if (!Storage::disk('local')->exists($contractPath)) {
                // Si le fichier n'existe pas, le régénérer
                $contractPath = $this->regenerateContract($sponsorship);
            }

            $fileName = 'contrat_' . $sponsorship->user->company_name . '_' . $sponsorship->event_title . '_' . date('Ymd', strtotime($sponsorship->created_at)) . '.html';
            $fileName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $fileName);

            return Storage::disk('local')->download($contractPath, $fileName);

        } catch (\Exception $e) {
            Log::error('AdminContractController: Erreur download', ['error' => $e->getMessage(), 'id' => $id]);
            return redirect()->back()->with('error', 'Erreur lors du téléchargement du contrat.');
        }
    }

    /**
     * Voir un contrat dans le navigateur
     */
    public function view($id)
    {
        try {
            $sponsorship = SponsorshipTemp::where('status', 'approved')
                ->whereNotNull('contract_pdf')
                ->with(['user', 'event'])
                ->findOrFail($id);

            $contractPath = $sponsorship->contract_pdf;

            if (!Storage::disk('local')->exists($contractPath)) {
                // Si le fichier n'existe pas, le régénérer
                $contractPath = $this->regenerateContract($sponsorship);
            }

            $contractContent = Storage::disk('local')->get($contractPath);

            return response($contractContent)
                ->header('Content-Type', 'text/html; charset=utf-8');

        } catch (\Exception $e) {
            Log::error('AdminContractController: Erreur view', ['error' => $e->getMessage(), 'id' => $id]);
            return redirect()->back()->with('error', 'Erreur lors de l\'affichage du contrat.');
        }
    }

    /**
     * Régénérer un contrat
     */
    public function regenerate($id)
    {
        try {
            $sponsorship = SponsorshipTemp::where('status', 'approved')
                ->with(['user', 'event'])
                ->findOrFail($id);

            $contractPath = $this->regenerateContract($sponsorship);

            return response()->json([
                'success' => true,
                'message' => 'Contrat régénéré avec succès !',
                'contract_path' => $contractPath
            ]);

        } catch (\Exception $e) {
            Log::error('AdminContractController: Erreur regenerate', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la régénération du contrat.'
            ], 500);
        }
    }

    /**
     * Supprimer un contrat
     */
    public function delete($id)
    {
        try {
            $sponsorship = SponsorshipTemp::where('status', 'approved')
                ->whereNotNull('contract_pdf')
                ->findOrFail($id);

            // Supprimer le fichier physique
            if ($sponsorship->contract_pdf && Storage::disk('local')->exists($sponsorship->contract_pdf)) {
                Storage::disk('local')->delete($sponsorship->contract_pdf);
            }

            // Supprimer la référence dans la base de données
            $sponsorship->update(['contract_pdf' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Contrat supprimé avec succès !'
            ]);

        } catch (\Exception $e) {
            Log::error('AdminContractController: Erreur delete', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la suppression du contrat.'
            ], 500);
        }
    }

    /**
     * Exporter tous les contrats en ZIP
     */
    public function exportAll()
    {
        try {
            $contracts = SponsorshipTemp::where('status', 'approved')
                ->whereNotNull('contract_pdf')
                ->with(['user', 'event'])
                ->get();

            if ($contracts->isEmpty()) {
                return redirect()->back()->with('error', 'Aucun contrat à exporter.');
            }

            $zipFileName = 'contrats_' . date('Ymd_His') . '.zip';
            $zipPath = 'exports/' . $zipFileName;

            // Créer le dossier exports s'il n'existe pas
            if (!Storage::disk('local')->exists('exports')) {
                Storage::disk('local')->makeDirectory('exports');
            }

            $zip = new \ZipArchive();
            $fullZipPath = Storage::disk('local')->path($zipPath);

            if ($zip->open($fullZipPath, \ZipArchive::CREATE) !== TRUE) {
                throw new \Exception('Impossible de créer le fichier ZIP.');
            }

            foreach ($contracts as $contract) {
                if ($contract->contract_pdf && Storage::disk('local')->exists($contract->contract_pdf)) {
                    $fileName = 'contrat_' . $contract->user->company_name . '_' . $contract->event_title . '_' . $contract->id . '.html';
                    $fileName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $fileName);
                    
                    $zip->addFile(
                        Storage::disk('local')->path($contract->contract_pdf),
                        $fileName
                    );
                }
            }

            $zip->close();

            return Storage::disk('local')->download($zipPath, $zipFileName);

        } catch (\Exception $e) {
            Log::error('AdminContractController: Erreur exportAll', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de l\'export des contrats.');
        }
    }

    /**
     * Régénérer un contrat (méthode privée)
     */
    private function regenerateContract($sponsorship)
    {
        $contractController = app(\App\Http\Controllers\ContractController::class);
        
        // Générer le HTML du contrat
        $html = $contractController->generateContractHTML($sponsorship);
        
        $contractDir = 'contracts/' . $sponsorship->id;
        $contractFile = 'contrat_sponsorship_' . $sponsorship->id . '_' . date('Ymd_His') . '.html';
        
        // Créer le dossier s'il n'existe pas
        if (!Storage::disk('local')->exists($contractDir)) {
            Storage::disk('local')->makeDirectory($contractDir);
        }
        
        // Sauvegarder le fichier
        Storage::disk('local')->put($contractDir . '/' . $contractFile, $html);
        
        // Mettre à jour la référence dans la base de données
        $sponsorship->update(['contract_pdf' => $contractDir . '/' . $contractFile]);
        
        return $contractDir . '/' . $contractFile;
    }
}
