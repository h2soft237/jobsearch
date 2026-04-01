<?php
// process_delete_job.php

session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// ----------------------------------------------------
// 1. VÉRIFICATION D'ACCÈS
// ----------------------------------------------------
// Doit être un employeur et la méthode doit être POST (ou GET si confirmation est ignorée)
if (!is_role('employeur')) {
    redirect_to('login'); 
}

$pdo = connect_db();

// 2. Récupération des données (utilisation de GET est tolérée pour les liens d'action simples,
// mais POST est préférable pour des raisons de sécurité, même si la vérification de propriété est faite)
$job_id       = (int)($_REQUEST['id'] ?? $_POST['job_id'] ?? 0); 
$employeur_id = $_SESSION['user_id'];

// 3. Validation de base
if ($job_id === 0) {
    $_SESSION['delete_job_result'] = ['success' => false, 'message' => 'ID de l\'offre manquant pour la suppression.'];
    redirect_to('dashboard');
}

// 4. Appel de la fonction de suppression sécurisée
$result = delete_job($pdo, $job_id, $employeur_id);

// 5. Redirection
$_SESSION['delete_job_result'] = $result;

// Rediriger toujours vers le tableau de bord après la suppression
redirect_to('dashboard'); 
?>