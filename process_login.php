<?php
// process_login.php

// 1. Démarrer la session et inclure les fichiers nécessaires
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Vérifier si le formulaire a été soumis via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('login');
}

// Connexion à la base de données
$pdo = connect_db();

// 2. Récupérer et nettoyer les données
$email      = trim($_POST['email'] ?? '');
$mot_de_passe = $_POST['mot_de_passe'] ?? '';

// Tableau pour stocker les erreurs de validation côté serveur
$errors = [];

// 3. Validation de base des données
if (empty($email) || empty($mot_de_passe)) {
    $errors[] = 'Veuillez saisir votre email et votre mot de passe.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) {
    $errors[] = 'Le format de l\'email est invalide.';
}


// 4. Traitement ou retour avec les erreurs
if (!empty($errors)) {
    // Stocker le message d'erreur et les données postées
    $_SESSION['login_result'] = [
        'success' => false,
        'message' => implode('<br>', $errors)
    ];
    $_SESSION['post_data'] = ['email' => $email];
    
    // Rediriger vers le formulaire
    redirect_to('login');
} else {
    // 5. Appeler la fonction de connexion
    $result = login_user($pdo, $email, $mot_de_passe);
    
    // Stocker le résultat (succès/erreur) dans la session
    $_SESSION['login_result'] = $result;

    // 6. Redirection finale
    if ($result['success']) {
        // Rediriger vers le tableau de bord après la connexion réussie
        redirect_to('dashboard'); 
    } else {
        // En cas d'échec de la connexion, revenir au formulaire et conserver l'email
        $_SESSION['post_data'] = ['email' => $email];
        redirect_to('login');
    }
}
?>