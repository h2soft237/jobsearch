<?php
// process_admin_action.php - Script central pour les actions administratives (CRUD)

session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// 1. VÉRIFICATION DE SÉCURITÉ DE RÔLE (strict)
if (!is_role('admin')) {
    redirect_to('dashboard'); 
}

$pdo = connect_db();
$action = $_REQUEST['action'] ?? ''; // Utilise REQUEST pour gérer POST (update) et GET (delete)
$result = ['success' => false, 'message' => 'Action administrative inconnue.'];

// ----------------------------------------------------
// A. SUPPRIMER UN UTILISATEUR (GET)
// ----------------------------------------------------
if ($action === 'delete_user' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    
    $user_id = (int)($_GET['id'] ?? 0);
    
    if ($user_id > 0) {
        $result = admin_delete_user($pdo, $user_id);
    } else {
        $result['message'] = 'ID utilisateur manquant pour la suppression.';
    }
    
    $_SESSION['admin_user_result'] = $result;
    redirect_to('admin_users');
    
} 
// ----------------------------------------------------
// B. METTRE À JOUR UN UTILISATEUR (POST)
// ----------------------------------------------------
elseif ($action === 'update_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $user_id = (int)($_POST['user_id'] ?? 0);
    $new_role = trim($_POST['new_role'] ?? '');
    $new_email = trim($_POST['new_email'] ?? '');
    
    if ($user_id > 0) {
        $result = admin_update_user($pdo, $user_id, $new_role, $new_email);
    } else {
        $result['message'] = 'ID utilisateur manquant pour la modification.';
    }
    
    $_SESSION['admin_user_result'] = $result;
    redirect_to('admin_users');

} 
// ... (code précédent pour update_user et delete_user) ...
// ----------------------------------------------------
// C. SUPPRIMER UNE OFFRE (GET)
// ----------------------------------------------------
elseif ($action === 'delete_admin_job' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    
    $job_id = (int)($_GET['id'] ?? 0);
    
    if ($job_id > 0) {
        $result = admin_delete_job($pdo, $job_id);
    } else {
        $result['message'] = 'ID de l\'offre manquant pour la suppression.';
    }
    
    $_SESSION['admin_job_result'] = $result;
    redirect_to('admin_jobs');
    
} 
// ----------------------------------------------------
// D. ACTIVER/DÉSACTIVER UNE OFFRE (GET)
// ----------------------------------------------------
elseif ($action === 'toggle_job_status' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    
    $job_id = (int)($_GET['id'] ?? 0);
    $status = (int)($_GET['status'] ?? 0); // 1 pour activer, 0 pour désactiver
    
    if ($job_id > 0) {
        $result = admin_toggle_job_status($pdo, $job_id, $status === 1);
    } else {
        $result['message'] = 'ID de l\'offre manquant pour la mise à jour du statut.';
    }
    
    $_SESSION['admin_job_result'] = $result;
    redirect_to('admin_jobs');

}
// ... (code précédent pour gestion des offres) ...
// ----------------------------------------------------
// E. METTRE À JOUR TOUS LES PARAMÈTRES (POST)
// ----------------------------------------------------
elseif ($action === 'update_settings' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $settings_to_update = $_POST['settings'] ?? [];
    $total_updated = 0;
    
    if (!empty($settings_to_update)) {
        
        $pdo->beginTransaction();
        try {
            foreach ($settings_to_update as $key => $value) {
                // S'assurer que la clé est une chaîne de caractères simple
                if (is_string($key)) {
                    $result_update = admin_update_setting($pdo, $key, trim($value));
                    if ($result_update['success']) {
                        $total_updated++;
                    }
                }
            }
            $pdo->commit();
            
            $result = ['success' => true, 'message' => "$total_updated paramètres mis à jour avec succès."];
            
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Transaction DB failed for settings update: " . $e->getMessage());
            $result = ['success' => false, 'message' => "Erreur critique : Échec de la mise à jour des paramètres. Aucune modification enregistrée."];
        }

    } else {
        $result['message'] = 'Aucun paramètre à mettre à jour.';
    }
    
    $_SESSION['admin_settings_result'] = $result;
    redirect_to('admin_settings');
}
// ... (reste de la gestion de l'action invalide) ...
// ----------------------------------------------------
// C. GÉRER LA REDIRECTION PAR DÉFAUT
// ----------------------------------------------------
else {
    $_SESSION['admin_user_result'] = ['success' => false, 'message' => "Requête administrative non supportée."];
    redirect_to('admin_users');
}
?>