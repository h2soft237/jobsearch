<?php
// process_register.php

// 1. Démarrer la session et inclure les fichiers nécessaires
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Vérifier si le formulaire a été soumis via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Si la méthode n'est pas POST, rediriger vers la page d'inscription
    redirect_to('register');
}

// Connexion à la base de données
$pdo = connect_db();

// 2. Récupérer et nettoyer les données du formulaire
$nom        = trim($_POST['nom'] ?? '');
$email      = trim($_POST['email'] ?? '');
$mot_de_passe = $_POST['mot_de_passe'] ?? '';
$mot_de_passe_confirm = $_POST['mot_de_passe_confirm'] ?? '';
$role       = $_POST['role'] ?? 'candidat'; // Par défaut à candidat

// Tableau pour stocker les erreurs de validation côté serveur
$errors = [];

// 3. Validation des données
if (empty($nom) || empty($email) || empty($mot_de_passe) || empty($mot_de_passe_confirm)) {
    $errors[] = 'Tous les champs sont obligatoires.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Le format de l\'email est invalide.';
}

if ($mot_de_passe !== $mot_de_passe_confirm) {
    $errors[] = 'Les mots de passe ne correspondent pas.';
}

if (strlen($mot_de_passe) < 6) {
    $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
}

if (!in_array($role, ['candidat', 'employeur'])) {
    $errors[] = 'Rôle invalide.';
}


// 4. Traitement ou retour avec les erreurs
if (!empty($errors)) {
    // Stocker le premier message d'erreur dans la session
    $_SESSION['register_result'] = [
        'success' => false,
        'message' => implode('<br>', $errors) // Afficher toutes les erreurs
    ];
    // Stocker les données postées pour pré-remplir le formulaire
    $_SESSION['post_data'] = $_POST;
    
    // Rediriger vers le formulaire
    redirect_to('register');
} else {
    // 5. Appeler la fonction d'inscription sécurisée
    $result = register_user($pdo, $nom, $email, $mot_de_passe, $role);
    
    // Stocker le résultat (succès/erreur DB) dans la session
    $_SESSION['register_result'] = $result;

    // Si l'inscription a réussi, on peut rediriger vers la page de connexion
    if ($result['success']) {
        redirect_to('login');
    } else {
        // En cas d'erreur (email déjà utilisé, etc.), revenir au formulaire
        redirect_to('register');
    }
}
?>