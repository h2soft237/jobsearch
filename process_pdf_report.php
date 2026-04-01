<?php
// process_pdf_report.php

session_start();
require 'vendor/autoload.php'; // Charger l'autoloader de Composer (inclut Dompdf)
require_once 'includes/db.php';
require_once 'includes/functions.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Vérification de Sécurité: Seuls les administrateurs peuvent générer des rapports PDF
if (!is_role('admin')) {
    redirect_to('dashboard');
    exit;
}

$pdo = connect_db();
$report_type = $_GET['report'] ?? '';

// --- 1. Préparation des données et du titre ---
$title = "Rapport Administratif";
$data = [];

switch ($report_type) {
    case 'employeurs':
        $data = get_employer_report($pdo);
        $title = "Rapport Détaillé des Employeurs";
        break;
        
    case 'candidats':
        $data = get_candidat_report($pdo);
        $title = "Rapport Détaillé des Candidats";
        break;
        
    case 'confirmations':
        $data = get_hiring_confirmation_report($pdo);
        $title = "Rapport de Confirmation d'Embauche";
        break;
        
    default:
        die("Type de rapport invalide.");
}

// --- 2. Génération du Contenu HTML pour le PDF ---

// Début du HTML (avec CSS pour l'impression)
$html = '<!DOCTYPE html><html><head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>'.$title.'</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        h1, h2 { color: #333; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { text-align: center; font-size: 8pt; margin-top: 30px; }
        .badge { display: inline-block; padding: 3px 6px; background-color: #007bff; color: white; border-radius: 3px; font-size: 8pt; }
    </style>
</head><body>';

$html .= '<h1>' . $title . '</h1>';
$html .= '<p>Généré le : ' . date('d/m/Y H:i:s') . '</p>';


// Logique de construction du tableau HTML en fonction du type de rapport
if ($report_type === 'employeurs') {
    $html .= '<table><thead><tr>
        <th>Nom Entreprise</th><th>Email</th><th>Date Inscription</th><th>Offres Publiées</th><th>Candidatures Reçues</th>
    </tr></thead><tbody>';
    foreach ($data as $emp) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($emp['nom']) . '</td>';
        $html .= '<td>' . htmlspecialchars($emp['email']) . '</td>';
        $html .= '<td>' . date('d/m/Y', strtotime($emp['date_inscription'])) . '</td>';
        $html .= '<td><span class="badge">' . $emp['total_offres'] . '</span></td>';
        $html .= '<td><span class="badge">' . $emp['total_candidatures_recues'] . '</span></td>';
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';
} 
// Ajoutez des conditions similaires (elseif) pour 'candidats' et 'confirmations'
elseif ($report_type === 'candidats') {
    // ... (Code pour générer le tableau des candidats) ...
     $html .= '<table><thead><tr>
        <th>Nom Candidat</th><th>Email</th><th>Date Inscription</th><th>Candidatures Déposées</th><th>Dépôt Confirmé</th>
    </tr></thead><tbody>';
    foreach ($data as $cand) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($cand['nom']) . '</td>';
        $html .= '<td>' . htmlspecialchars($cand['email']) . '</td>';
        $html .= '<td>' . date('d/m/Y', strtotime($cand['date_inscription'])) . '</td>';
        $html .= '<td><span class="badge">' . $cand['total_candidatures_deposees'] . '</span></td>';
        $html .= '<td>' . ($cand['total_candidatures_deposees'] > 0 ? 'Oui' : 'Non') . '</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';

} elseif ($report_type === 'confirmations') {
    // ... (Code pour générer le tableau des confirmations) ...
     $html .= '<table><thead><tr>
        <th>Offre</th><th>Employeur</th><th>Candidat Retenu</th><th>Email Candidat</th><th>Date Candidature</th>
    </tr></thead><tbody>';
    foreach ($data as $conf) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($conf['offre_titre']) . '</td>';
        $html .= '<td>' . htmlspecialchars($conf['employeur_nom']) . '</td>';
        $html .= '<td>' . htmlspecialchars($conf['candidat_nom']) . '</td>';
        $html .= '<td>' . htmlspecialchars($conf['candidat_email']) . '</td>';
        $html .= '<td>' . date('d/m/Y', strtotime($conf['date_candidature'])) . '</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';
}


$html .= '<div class="footer">Rapport généré automatiquement par le système de gestion d\'emploi.</div>';
$html .= '</body></html>';

// --- 3. Conversion HTML vers PDF avec Dompdf ---

// Configurez les options
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', false); // Désactiver l'accès distant pour la sécurité

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);

// (Optionnel) Définir la taille et l'orientation du papier
$dompdf->setPaper('A4', 'portrait');

// Rendre le HTML
$dompdf->render();

// --- 4. Sortie du fichier PDF ---

// Nom du fichier pour le téléchargement
$filename = str_replace(' ', '_', $title) . '_' . date('Ymd') . '.pdf';

// Streamer le fichier au navigateur pour le téléchargement
$dompdf->stream($filename, ["Attachment" => true]); // true = Téléchargement forcé

exit;
?>