<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrat de Sponsoring - {{ $sponsorship->event_title ?? ($sponsorship->event->title ?? 'Événement') }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #2c5530;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2c5530;
            margin-bottom: 10px;
        }
        .contract-title {
            font-size: 28px;
            font-weight: bold;
            color: #2c5530;
            margin: 20px 0;
        }
        .section {
            margin: 25px 0;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #2c5530;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #2c5530;
        }
        .info-label {
            font-weight: bold;
            color: #2c5530;
        }
        .terms {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .signature-section {
            margin-top: 40px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }
        .signature-box {
            border-top: 1px solid #333;
            padding-top: 10px;
            text-align: center;
            min-height: 80px;
        }
        .date {
            text-align: right;
            margin-top: 30px;
            font-style: italic;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #2c5530;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">ECHOFY</div>
        <div class="contract-title">CONTRAT DE SPONSORING</div>
        <p>Plateforme d'événements écologiques</p>
    </div>

    <div class="section">
        <div class="section-title">Informations du Contrat</div>
        <div class="info-grid">
            <div class="info-box">
                <div class="info-label">Numéro de Contrat:</div>
                <div>SPONS-{{ str_pad($sponsorship->id, 6, '0', STR_PAD_LEFT) }}-{{ date('Y') }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">Date de Signature:</div>
                <div>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">Statut:</div>
                <div>
                    <span class="status-badge status-approved">Approuvé</span>
                </div>
            </div>
            <div class="info-box">
                <div class="info-label">Durée du Contrat:</div>
                <div>Du {{ \Carbon\Carbon::now()->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($sponsorship->event_date ?? ($sponsorship->event->date ?? now()))->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Parties Contractuelles</div>
        <div class="info-grid">
            <div class="info-box">
                <div class="info-label">SPONSOR:</div>
                <div><strong>{{ $sponsorship->user->name }}</strong></div>
                <div>{{ $sponsorship->user->company_name ?? 'Entreprise non spécifiée' }}</div>
                <div>Email: {{ $sponsorship->user->email }}</div>
                @if($sponsorship->user->phone)
                    <div>Téléphone: {{ $sponsorship->user->phone }}</div>
                @endif
            </div>
            <div class="info-box">
                <div class="info-label">ORGANISATEUR:</div>
                <div><strong>ECHOFY</strong></div>
                <div>Plateforme d'événements écologiques</div>
                <div>Email: contact@echofy.com</div>
                <div>Téléphone: +33 1 23 45 67 89</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Événement Concerné</div>
        <div class="info-grid">
            <div class="info-box">
                <div class="info-label">Titre de l'Événement:</div>
                <div><strong>{{ $sponsorship->event_title ?? ($sponsorship->event->title ?? 'Événement non spécifié') }}</strong></div>
            </div>
            <div class="info-box">
                <div class="info-label">Date de l'Événement:</div>
                <div>{{ \Carbon\Carbon::parse($sponsorship->event_date ?? ($sponsorship->event->date ?? now()))->format('d/m/Y à H:i') }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">Lieu:</div>
                <div>{{ $sponsorship->event_location ?? ($sponsorship->event->location ?? 'Lieu non spécifié') }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">Description:</div>
                <div>{{ $sponsorship->event_description ?? ($sponsorship->event->description ?? 'Aucune description disponible') }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Détails du Sponsoring</div>
        <div class="info-grid">
            <div class="info-box">
                <div class="info-label">Package Choisi:</div>
                <div><strong>{{ $sponsorship->package_name }}</strong></div>
            </div>
            <div class="info-box">
                <div class="info-label">Montant du Sponsoring:</div>
                <div class="amount">{{ number_format($sponsorship->amount, 0, ',', ' ') }} €</div>
            </div>
            <div class="info-box">
                <div class="info-label">Notes du Sponsor:</div>
                <div>{{ $sponsorship->notes ?? 'Aucune note spécifiée' }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Conditions Générales</div>
        <div class="terms">
            <p><strong>Article 1 - Objet du Contrat</strong></p>
            <p>Le présent contrat a pour objet de définir les conditions dans lesquelles le Sponsor participe au financement et à la promotion de l'événement {{ $sponsorship->event->title ?? 'l\'événement' }} organisé par ECHOFY.</p>

            <p><strong>Article 2 - Obligations du Sponsor</strong></p>
            <p>Le Sponsor s'engage à :</p>
            <ul>
                <li>Verser la somme de {{ number_format($sponsorship->amount, 0, ',', ' ') }} € dans les 30 jours suivant la signature du présent contrat</li>
                <li>Respecter les valeurs écologiques et éthiques portées par ECHOFY</li>
                <li>Ne pas porter atteinte à l'image de l'événement ou d'ECHOFY</li>
                <li>Informer ECHOFY de tout changement significatif dans sa situation</li>
            </ul>

            <p><strong>Article 3 - Obligations d'ECHOFY</strong></p>
            <p>ECHOFY s'engage à :</p>
            <ul>
                <li>Organiser l'événement {{ $sponsorship->event->title ?? 'l\'événement' }} dans les conditions annoncées</li>
                <li>Assurer la visibilité du Sponsor selon le package {{ $sponsorship->package_name }}</li>
                <li>Fournir un rapport post-événement sur l'impact du sponsoring</li>
                <li>Respecter les délais de paiement convenus</li>
            </ul>

            <p><strong>Article 4 - Annulation</strong></p>
            <p>En cas d'annulation de l'événement par ECHOFY, le Sponsor sera intégralement remboursé. En cas d'annulation par le Sponsor, les conditions de remboursement seront définies selon la politique d'ECHOFY.</p>

            <p><strong>Article 5 - Droit Applicable</strong></p>
            <p>Le présent contrat est soumis au droit français. En cas de litige, les tribunaux français seront seuls compétents.</p>
        </div>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <p><strong>Signature du Sponsor</strong></p>
            <br><br>
            <p>Date: _______________</p>
        </div>
        <div class="signature-box">
            <p><strong>Signature d'ECHOFY</strong></p>
            <br><br>
            <p>Date: _______________</p>
        </div>
    </div>

    <div class="date">
        Fait à Paris, le {{ \Carbon\Carbon::now()->format('d/m/Y') }}
    </div>

    <div class="footer">
        <p>Ce contrat a été généré automatiquement par la plateforme ECHOFY</p>
        <p>Pour toute question, contactez-nous à contact@echofy.com</p>
        <p>Document généré le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i:s') }}</p>
    </div>
</body>
</html>
