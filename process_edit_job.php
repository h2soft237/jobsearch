<?php
// process_edit_job.php

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

// 2. Récupérer et nettoyer les données du formulaire
$employeur_id = $_SESSION['user_id'];
$job_id       = (int)($_POST['job_id'] ?? 0);
$titre        = trim($_POST['titre'] ?? '');
$description  = trim($_POST['description'] ?? '');
$lieu         = trim($_POST['lieu'] ?? '');
$salaire      = trim($_POST['salaire'] ?? '');
$type_contrat = $_POST['type_contrat'] ?? ''; 
$est_actif    = isset($_POST['est_actif']) ? 1 : 0; // 1 si coché, 0 sinon

$errors = [];

// 3. Validation des données côté serveur
if ($job_id === 0) {
    $errors[] = 'ID de l\'offre manquant pour la modification.';
}
if (empty($titre) || empty($description) || empty($lieu) || empty($type_contrat)) {
    $errors[] = 'Les champs obligatoires ne peuvent pas être vides.';
}

$allowed_contrats = ['CDI', 'CDD', 'Intérim', 'Stage', 'Freelance'];
if (!in_array($type_contrat, $allowed_contrats)) {
    $errors[] = 'Le type de contrat sélectionné est invalide.';
}


// 4. Traitement ou retour avec les erreurs
if (!empty($errors)) {
    $_SESSION['edit_job_result'] = [
        'success' => false,
        'message' => implode('<br>', $errors)
    ];
    $_SESSION['post_data'] = $_POST; // Conserver les données postées
    
    // Rediriger vers la page de modification de cette offre
    redirect_to('edit_job&id=' . $job_id);
} else {
    
    // 5. Préparer les données pour la fonction de mise à jour
    $job_data_to_update = [
        'titre' => $titre,
        'description' => $description,
        'lieu' => $lieu,
        'salaire' => $salaire,
        'type_contrat' => $type_contrat,
        'est_actif' => $est_actif
    ];
    
    // 6. Appel de la fonction de mise à jour sécurisée
    $result = update_job($pdo, $job_id, $employeur_id, $job_data_to_update);
    
    // 7. Redirection
    $_SESSION['edit_job_result'] = $result;

    if ($result['success']) {
        // Rediriger vers le tableau de bord ou la liste des offres publiées
        redirect_to('dashboard'); 
    } else {
        // En cas d'erreur DB, revenir au formulaire
        $_SESSION['post_data'] = $_POST;
        redirect_to('edit_job&id=' . $job_id);
    }
}
?>