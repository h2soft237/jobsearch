<?php
// process_profile_update.php

session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// ----------------------------------------------------
// 1. VÉRIFICATION DE SÉCURITÉ
// ----------------------------------------------------
if (!is_logged_in() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('login'); 
}

$pdo = connect_db();
$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$result = ['success' => false, 'message' => 'Action invalide.'];

// ----------------------------------------------------
// 2. TRAITEMENT DE LA MISE À JOUR DES INFORMATIONS
// ----------------------------------------------------
if ($action === 'update_info') {
    
    $nom   = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    $errors = [];
    if (empty($nom) || empty($email)) {
        $errors[] = 'Le nom et l\'email sont obligatoires.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Le format de l\'email est invalide.';
    }

    if (!empty($errors)) {
        $result = ['success' => false, 'message' => implode('<br>', $errors)];
    } else {
        // Appeler la fonction de mise à jour sécurisée
        $result = update_user_info($pdo, $user_id, $nom, $email);
    }
    
    $_SESSION['profile_info_result'] = $result;
    $_SESSION['post_data'] = $_POST;
    redirect_to('profile_edit');
    
} 
// ----------------------------------------------------
// 3. TRAITEMENT DU CHANGEMENT DE MOT DE PASSE
// ----------------------------------------------------
elseif ($action === 'update_password') {
    
    $current_password = $_POST['current_password'] ?? '';
    $new_password     = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    $errors = [];
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $errors[] = 'Tous les champs de mot de passe sont obligatoires.';
    }
    if ($new_password !== $confirm_password) {
        $errors[] = 'Le nouveau mot de passe et sa confirmation ne correspondent pas.';
    }
    if (strlen($new_password) < 6) {
        $errors[] = 'Le nouveau mot de passe doit contenir au moins 6 caractères.';
    }
    
    if (!empty($errors)) {
        $result = ['success' => false, 'message' => implode('<br>', $errors)];
    } else {
        // Appeler la fonction de changement de mot de passe sécurisée
        $result = update_user_password($pdo, $user_id, $current_password, $new_password);
    }

    $_SESSION['password_change_result'] = $result;
    redirect_to('profile_edit');
    
}// ----------------------------------------------------
// 4. TRAITEMENT DE L'UPLOAD DE PHOTO
// ----------------------------------------------------
elseif ($action === 'upload_photo') {
    
    $photo_file = $_FILES['photo_file'] ?? null;
    $result = ['success' => false, 'message' => 'Fichier manquant.'];

    if ($photo_file) {
        
        // 1. Gérer l'upload du nouveau fichier
        $upload_result = upload_profile_photo($photo_file, $user_id);

        if ($upload_result['success']) {
            
            // 2. Supprimer l'ancienne photo si elle existe
            $old_profile = get_user_profile($pdo, $user_id);
            if ($old_profile && !empty($old_profile['photo_profil_chemin']) && 
                $old_profile['photo_profil_chemin'] !== 'assets/img/default_profile.png' && 
                file_exists($old_profile['photo_profil_chemin'])) 
            {
                unlink($old_profile['photo_profil_chemin']);
            }

            // 3. Mettre à jour le chemin dans la DB
            if (update_profile_photo_path($pdo, $user_id, $upload_result['filepath'])) {
                 $result = ['success' => true, 'message' => 'Photo de profil mise à jour.'];
            } else {
                 $result = ['success' => false, 'message' => 'Photo téléchargée, mais erreur DB. Contactez l\'assistance.'];
            }
        } else {
            $result = $upload_result;
        }
    }

    $_SESSION['photo_upload_result'] = $result;
    redirect_to('profile_edit');

} 
// ----------------------------------------------------
// 5. TRAITEMENT DE LA SUPPRESSION DE PHOTO
// ----------------------------------------------------
elseif ($action === 'delete_photo') {
    
    $old_path = $_POST['old_path'] ?? '';
    
    if (!empty($old_path) && file_exists($old_path)) {
        // Supprimer le fichier physique
        if (unlink($old_path)) {
            // Mettre à jour le chemin dans la DB (mettre à NULL)
            update_profile_photo_path($pdo, $user_id, null);
            $result = ['success' => true, 'message' => 'Photo de profil supprimée.'];
        } else {
            $result = ['success' => false, 'message' => 'Erreur de suppression du fichier.'];
        }
    } else {
        // Mettre à jour le chemin dans la DB même si le fichier était introuvable
        update_profile_photo_path($pdo, $user_id, null);
        $result = ['success' => true, 'message' => 'Photo de profil retirée.'];
    }

    $_SESSION['photo_upload_result'] = $result;
    redirect_to('profile_edit');
} 
// ----------------------------------------------------
// 4. ACTION INVALIDE
// ----------------------------------------------------
else {
    $_SESSION['profile_info_result'] = $result;
    redirect_to('profile_edit');
}
?>