<?php

namespace App\Http\Controllers;

use App\Models\SponsorshipTemp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    /**
     * Générer et télécharger le contrat PDF
     */
    public function downloadContract($id)
    {
        try {
            $sponsorship = SponsorshipTemp::with(['user', 'event'])->findOrFail($id);
            
            if ($sponsorship->status !== 'approved') {
                abort(404, 'Contrat non disponible');
            }

            // S'assurer qu'un snapshot existe, sinon le générer et le sauvegarder
            $snapshotPath = $sponsorship->contract_pdf;
            if (!$snapshotPath || !Storage::disk('local')->exists($snapshotPath)) {
                // Créer le répertoire si nécessaire et régénérer
                $snapshotPath = $this->saveContractSnapshot($sponsorship);
                $sponsorship->update(['contract_pdf' => $snapshotPath]);
            }

            $absolutePath = storage_path('app/' . $snapshotPath);
            $downloadName = basename($snapshotPath);
            
            // Si le fichier n'existe toujours pas pour une raison quelconque, renvoyer le contenu en pièce jointe
            if (!file_exists($absolutePath)) {
                $html = Storage::disk('local')->get($snapshotPath); // peut lancer une exception capturée plus bas
                return response($html, 200, [
                    'Content-Type' => 'text/html; charset=UTF-8',
                    'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]);
            }

            return response()->download($absolutePath, $downloadName, [
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('ContractController: Erreur downloadContract', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Erreur lors de la génération du contrat'
            ], 500);
        }
    }

    /**
     * Afficher le contrat PDF dans le navigateur
     */
    public function viewContract($id)
    {
        try {
            $sponsorship = SponsorshipTemp::with(['user', 'event'])->findOrFail($id);
            
            if ($sponsorship->status !== 'approved') {
                abort(404, 'Contrat non disponible');
            }

            // S'assurer qu'un snapshot existe, sinon le générer et le sauvegarder
            $snapshotPath = $sponsorship->contract_pdf;
            if (!$snapshotPath || !Storage::disk('local')->exists($snapshotPath)) {
                $snapshotPath = $this->saveContractSnapshot($sponsorship);
                $sponsorship->update(['contract_pdf' => $snapshotPath]);
            }

            $html = Storage::disk('local')->get($snapshotPath);
            
            return response($html, 200, [
                'Content-Type' => 'text/html; charset=UTF-8',
                'Content-Disposition' => 'inline; filename="' . basename($snapshotPath) . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('ContractController: Erreur viewContract', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Erreur lors de l\'affichage du contrat'
            ], 500);
        }
    }

    /**
     * Générer le contenu PDF du contrat
     */
    private function generateContractPDF($sponsorship)
    {
        // Créer un PDF simple en HTML/CSS qui sera converti par le navigateur
        $html = $this->generateContractHTML($sponsorship);
        
        // Pour une vraie génération PDF, nous utiliserions une bibliothèque
        // Pour l'instant, nous retournons le HTML stylé pour impression
        return $html;
    }

    /**
     * Génère et sauvegarde un snapshot HTML du contrat, retourne le chemin relatif dans storage/app
     */
    private function saveContractSnapshot($sponsorship): string
    {
        $html = $this->generateContractHTML($sponsorship);
        $dir = 'contracts/' . $sponsorship->id;
        $filename = 'contrat_sponsorship_' . $sponsorship->id . '_' . date('Ymd_His') . '.html';
        $path = $dir . '/' . $filename;
        Storage::disk('local')->put($path, $html);
        return $path;
    }

    /**
     * Générer le HTML du contrat (méthode publique pour l'admin)
     */
    public function generateContractHTML($sponsorship)
    {
        $contractData = $this->prepareContractData($sponsorship);
        
        $html = '
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Contrat de Sponsoring</title>
            <style>
                @page {
                    margin: 2cm;
                    size: A4;
                }
                
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 800px;
                    margin: 0 auto;
                    padding: 20px;
                }
                
                .header {
                    text-align: center;
                    border-bottom: 3px solid #007bff;
                    padding-bottom: 20px;
                    margin-bottom: 30px;
                }
                
                .header h1 {
                    color: #007bff;
                    margin: 0;
                    font-size: 28px;
                }
                
                .header h2 {
                    color: #666;
                    margin: 10px 0 0 0;
                    font-size: 18px;
                    font-weight: normal;
                }
                
                .contract-info {
                    background: #f8f9fa;
                    padding: 20px;
                    border-radius: 8px;
                    margin-bottom: 30px;
                }
                
                .contract-info h3 {
                    color: #007bff;
                    margin-top: 0;
                    border-bottom: 2px solid #007bff;
                    padding-bottom: 10px;
                }
                
                .info-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 20px;
                    margin-bottom: 20px;
                }
                
                .info-item {
                    margin-bottom: 15px;
                }
                
                .info-label {
                    font-weight: bold;
                    color: #555;
                    margin-bottom: 5px;
                }
                
                .info-value {
                    color: #333;
                    padding: 8px;
                    background: white;
                    border-radius: 4px;
                    border: 1px solid #ddd;
                }
                
                .amount-highlight {
                    background: #e7f3ff;
                    border: 2px solid #007bff;
                    font-weight: bold;
                    font-size: 18px;
                    color: #007bff;
                }
                
                .terms-section {
                    margin: 30px 0;
                }
                
                .terms-section h3 {
                    color: #007bff;
                    border-bottom: 2px solid #007bff;
                    padding-bottom: 10px;
                }
                
                .terms-list {
                    margin: 20px 0;
                }
                
                .terms-list li {
                    margin-bottom: 10px;
                    padding-left: 20px;
                    position: relative;
                }
                
                .terms-list li:before {
                    content: "•";
                    color: #007bff;
                    font-weight: bold;
                    position: absolute;
                    left: 0;
                }
                
                .signature-section {
                    margin-top: 50px;
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 50px;
                }
                
                .signature-box {
                    text-align: center;
                    border-top: 2px solid #333;
                    padding-top: 20px;
                    margin-top: 60px;
                }
                
                .signature-box h4 {
                    margin: 0 0 10px 0;
                    color: #555;
                }
                
                .signature-line {
                    border-bottom: 1px solid #333;
                    height: 40px;
                    margin: 20px 0;
                }
                
                .footer {
                    margin-top: 50px;
                    text-align: center;
                    color: #666;
                    font-size: 12px;
                    border-top: 1px solid #ddd;
                    padding-top: 20px;
                }
                
                @media print {
                    body {
                        margin: 0;
                        padding: 0;
                    }
                    
                    .no-print {
                        display: none;
                    }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>CONTRAT DE SPONSORING</h1>
                <h2>Echofy Platform</h2>
            </div>
            
            <div class="contract-info">
                <h3>Informations du Contrat</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Numéro de Contrat</div>
                        <div class="info-value">' . $contractData['contract_number'] . '</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date de Signature</div>
                        <div class="info-value">' . $contractData['signature_date'] . '</div>
                    </div>
                </div>
            </div>
            
            <div class="contract-info">
                <h3>Informations du Sponsor</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Nom du Sponsor</div>
                        <div class="info-value">' . $contractData['sponsor_name'] . '</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Entreprise</div>
                        <div class="info-value">' . $contractData['sponsor_company'] . '</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value">' . $contractData['sponsor_email'] . '</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Téléphone</div>
                        <div class="info-value">' . $contractData['sponsor_phone'] . '</div>
                    </div>
                </div>
            </div>
            
            <div class="contract-info">
                <h3>Informations de l\'Événement</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Titre de l\'Événement</div>
                        <div class="info-value">' . $contractData['event_title'] . '</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date de l\'Événement</div>
                        <div class="info-value">' . $contractData['event_date'] . '</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Lieu</div>
                        <div class="info-value">' . $contractData['event_location'] . '</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Package Choisi</div>
                        <div class="info-value">' . $contractData['package_name'] . '</div>
                    </div>
                </div>
            </div>
            
            <div class="contract-info">
                <h3>Détails Financiers</h3>
                <div class="info-item">
                    <div class="info-label">Montant du Sponsoring</div>
                    <div class="info-value amount-highlight">' . $contractData['amount'] . ' €</div>
                </div>
            </div>
            
            <div class="terms-section">
                <h3>Conditions du Sponsoring</h3>
                <div class="terms-list">
                    <li>Le sponsor s\'engage à verser le montant de ' . $contractData['amount'] . ' € pour le package ' . $contractData['package_name'] . '</li>
                    <li>Le paiement doit être effectué dans les 30 jours suivant la signature de ce contrat</li>
                    <li>Le sponsor bénéficiera des avantages définis dans le package ' . $contractData['package_name'] . '</li>
                    <li>En cas d\'annulation de l\'événement par l\'organisateur, le montant sera intégralement remboursé</li>
                    <li>En cas d\'annulation par le sponsor moins de 15 jours avant l\'événement, 50% du montant sera retenu</li>
                    <li>Le sponsor s\'engage à respecter les valeurs et l\'image de l\'événement</li>
                    <li>Toute modification du contrat doit être approuvée par les deux parties par écrit</li>
                </div>
            </div>
            
            <div class="terms-section">
                <h3>Avantages du Package ' . $contractData['package_name'] . '</h3>
                <div class="terms-list">
                    ' . $contractData['package_benefits'] . '
                </div>
            </div>
            
            <div class="signature-section">
                <div class="signature-box">
                    <h4>Signature du Sponsor</h4>
                    <div class="signature-line"></div>
                    <p>' . $contractData['sponsor_name'] . '</p>
                    <p>' . $contractData['sponsor_company'] . '</p>
                    <p>Date: _______________</p>
                </div>
                
                <div class="signature-box">
                    <h4>Signature de l\'Administrateur</h4>
                    <div class="signature-line"></div>
                    <p>Echofy Platform</p>
                    <p>Administrateur</p>
                    <p>Date: _______________</p>
                </div>
            </div>
            
            <div class="footer">
                <p>Ce contrat a été généré automatiquement le ' . $contractData['generation_date'] . '</p>
                <p>Echofy Platform - Plateforme de Sponsoring d\'Événements</p>
            </div>
            
            <script>
                // Auto-print when opened
                window.onload = function() {
                    setTimeout(function() {
                        window.print();
                    }, 1000);
                };
            </script>
        </body>
        </html>';
        
        return $html;
    }

    /**
     * Préparer les données du contrat
     */
    private function prepareContractData($sponsorship)
    {
        $event = $sponsorship->event;
        $sponsor = $sponsorship->user;
        
        // Obtenir les avantages du package
        $packageBenefits = $this->getPackageBenefits($sponsorship->package_id);
        
        return [
            'contract_number' => 'SPONS-' . str_pad($sponsorship->id, 6, '0', STR_PAD_LEFT) . '-' . date('Y'),
            'sponsor_name' => $sponsor->name ?? 'Non spécifié',
            'sponsor_company' => $sponsor->company_name ?? 'Entreprise non spécifiée',
            'sponsor_email' => $sponsor->email ?? 'Non spécifié',
            'sponsor_phone' => $sponsor->phone ?? 'Non spécifié',
            'event_title' => $sponsorship->event_title ?? 'Événement non spécifié',
            'event_date' => $sponsorship->event_date 
                ? \Carbon\Carbon::parse($sponsorship->event_date)->format('d/m/Y à H:i')
                : 'Date non spécifiée',
            'event_location' => $sponsorship->event_location ?? 'Lieu non spécifié',
            'package_name' => $sponsorship->package_name ?? 'Package non spécifié',
            'amount' => number_format($sponsorship->amount, 2, ',', ' '),
            'package_benefits' => $packageBenefits,
            'signature_date' => now()->format('d/m/Y'),
            'generation_date' => now()->format('d/m/Y à H:i'),
        ];
    }

    /**
     * Obtenir les avantages du package
     */
    private function getPackageBenefits($packageId)
    {
        $benefits = [
            1 => [
                '<li>Logo sur les supports de communication</li>',
                '<li>Mention dans les réseaux sociaux</li>',
                '<li>Stand de 2m²</li>',
                '<li>Distribution de flyers</li>'
            ],
            2 => [
                '<li>Logo sur les supports de communication</li>',
                '<li>Mention dans les réseaux sociaux</li>',
                '<li>Stand de 4m²</li>',
                '<li>Intervention de 5 minutes</li>',
                '<li>Distribution de flyers</li>',
                '<li>Bannière publicitaire</li>'
            ],
            3 => [
                '<li>Logo sur les supports de communication</li>',
                '<li>Mention dans les réseaux sociaux</li>',
                '<li>Stand de 6m²</li>',
                '<li>Intervention de 10 minutes</li>',
                '<li>Distribution de flyers</li>',
                '<li>Bannières publicitaires</li>',
                '<li>Interview média</li>',
                '<li>Accès VIP</li>'
            ]
        ];
        
        return implode('', $benefits[$packageId] ?? ['<li>Avantages non spécifiés</li>']);
    }
}
