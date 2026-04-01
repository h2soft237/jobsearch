<?php
// process_alert_action.php - Traitement des actions d'alertes

session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php'; 

// 1. VÉRIFICATION DE SÉCURITÉ
if (!is_role('candidat')) {
    redirect_to('home'); 
}

$pdo = connect_db();
$user_id = $_SESSION['user_id'];
$action = $_REQUEST['action'] ?? '';
$result = ['success' => false, 'message' => 'Action d\'alerte inconnue.'];


// ----------------------------------------------------
// A. CRÉER UNE ALERTE (POST)
// ----------------------------------------------------
if ($action === 'create_alert' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $mots_cles = trim($_POST['mots_cles'] ?? '');
    $lieu = trim($_POST['lieu'] ?? '');
    $type_contrat = trim($_POST['type_contrat'] ?? '');
    
    if (empty($mots_cles)) {
        $result['message'] = 'Les mots-clés sont obligatoires pour créer une alerte.';
    } else {
        $result = create_job_alert($pdo, $user_id, $mots_cles, $lieu, $type_contrat);
    }
    
    $_SESSION['alert_result'] = $result;
    redirect_to('alerts');
    
} 
// ----------------------------------------------------
// B. SUPPRIMER UNE ALERTE (GET)
// ----------------------------------------------------
elseif ($action === 'delete_alert' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    
    $alert_id = (int)($_GET['id'] ?? 0);
    
    if ($alert_id > 0) {
        $result = delete_job_alert($pdo, $alert_id, $user_id);
    } else {
        $result['message'] = 'ID d\'alerte manquant pour la suppression.';
    }
    
    $_SESSION['alert_result'] = $result;
    redirect_to('alerts');
}
// ----------------------------------------------------
// C. MODIFIER UNE ALERTE (POST)
// ----------------------------------------------------
elseif ($action === 'update_alert' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $alert_id = (int)($_POST['alert_id'] ?? 0);
    $mots_cles = trim($_POST['mots_cles'] ?? '');
    $lieu = trim($_POST['lieu'] ?? '');
    $type_contrat = trim($_POST['type_contrat'] ?? '');
    
    if ($alert_id <= 0 || empty($mots_cles)) {
        $result['message'] = 'Données manquantes ou invalides pour la modification.';
    } else {
        $result = update_job_alert($pdo, $alert_id, $user_id, $mots_cles, $lieu, $type_contrat);
    }
    
    $_SESSION['alert_result'] = $result;
    redirect_to('alerts');
    
} 
// ----------------------------------------------------
// C. REDIRECTION PAR DÉFAUT
// ----------------------------------------------------
else {
    redirect_to('dashboard');
}
?>