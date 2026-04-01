<?php
// process_company_profile.php - Sauvegarde du profil de l'entreprise

session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// 1. VÉRIFICATION DE SÉCURITÉ
if (!is_role('employeur') || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('home'); 
}

$pdo = connect_db();
$employeur_id = $_SESSION['user_id'];

// 2. Récupération des données POST
$data = [
    'description' => trim($_POST['description'] ?? ''),
    'secteur_activite' => trim($_POST['secteur_activite'] ?? ''),
    'adresse_siege' => trim($_POST['adresse_siege'] ?? ''),
    'telephone' => trim($_POST['telephone'] ?? ''),
    'site_web' => trim($_POST['site_web'] ?? '')
];

// Validation minimale (vérifier si au moins un champ essentiel est rempli)
if (empty($data['description'])) {
    $_SESSION['profile_result'] = [
        'success' => false,
        'message' => 'La description de l\'entreprise est requise.'
    ];
    redirect_to('company_profile');
}

// 3. Sauvegarde des données
$result = save_company_profile($pdo, $employeur_id, $data);

// 4. Redirection
$_SESSION['profile_result'] = $result;
redirect_to('company_profile');