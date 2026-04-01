<?php
// process_update_status.php

session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// ----------------------------------------------------
// 1. VÉRIFICATION D'ACCÈS
// ----------------------------------------------------
if (!is_role('employeur') || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('home'); 
}

$pdo = connect_db();

// 2. Récupération des données
$candidature_id = (int)($_POST['candidature_id'] ?? 0);
$new_status     = trim($_POST['new_status'] ?? '');
$offre_id       = (int)($_POST['offre_id'] ?? 0);
$employeur_id   = $_SESSION['user_id'];

// 3. Validation
$allowed_statuses = ['en attente', 'examiné', 'accepté', 'refusé'];

if ($candidature_id === 0 || !in_array($new_status, $allowed_statuses)) {
    $_SESSION['applicant_status_result'] = ['success' => false, 'message' => 'Données de mise à jour invalides.'];
    redirect_to('view_applicants&job_id=' . $offre_id);
}

// ----------------------------------------------------------------------
// ÉTAPE NOUVELLE : Récupérer les détails AVANT la mise à jour
// ----------------------------------------------------------------------
$notification_details = get_notification_details($pdo, $candidature_id);

// 4. Traitement
$result = update_application_status($pdo, $candidature_id, $new_status, $employeur_id);

// ----------------------------------------------------------------------
// ÉTAPE NOUVELLE : Envoyer la notification si la mise à jour a réussi
// ----------------------------------------------------------------------
if ($result['success'] && $notification_details) {
    send_candidate_status_update(
        $notification_details['candidat_email'], 
        $notification_details['offre_titre'], 
        $new_status,
        $notification_details['nom_entreprise']
    );
    // Optionnel : ajouter à un message pour l'employeur
    $result['message'] .= " (Candidat notifié par e-mail)."; 
}

// 5. Traitement
$result = update_application_status($pdo, $candidature_id, $new_status, $employeur_id);

// 6. Redirection
$_SESSION['applicant_status_result'] = $result;
redirect_to('view_applicants&job_id=' . $offre_id);

?>