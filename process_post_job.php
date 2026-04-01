<?php
// process_post_job.php

session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// ----------------------------------------------------
// 1. VÉRIFICATION D'ACCÈS
// ----------------------------------------------------
// Seul un employeur connecté doit pouvoir traiter ce formulaire
if (!is_role('employeur') || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('home'); 
}

$pdo = connect_db();
// 2. Récupérer et nettoyer les données du formulaire
$employeur_id = $_SESSION['user_id']; // L'ID de l'employeur connecté est essentiel

// --------------------------------------------------------------------------
// NOUVEAU BLOC : VÉRIFICATION DE LA LIMITE MAXIMALE DE PUBLICATION
// --------------------------------------------------------------------------
// 1. Charger le paramètre global
// Note: Assurez-vous que $GLOBALS['settings'] est chargé dans index.php
if (!isset($GLOBALS['settings']['max_jobs_per_employeur'])) {
    // Si le paramètre n'est pas chargé ou n'existe pas, utiliser une valeur par défaut sûre
    $GLOBALS['settings']['max_jobs_per_employeur'] = '10'; 
}

$max_jobs_allowed = (int)($GLOBALS['settings']['max_jobs_per_employeur']);

// 2. Compter les offres actives de cet employeur
$current_job_count = count_employer_active_jobs($pdo, $employeur_id);

// 3. Vérifier si la limite est atteinte
if ($current_job_count >= $max_jobs_allowed) {
    
    // ACTION À EFFECTUER :
    $_SESSION['job_result'] = [
        'success' => false,
        'message' => "❌ **Limite Atteinte :** Vous avez déjà publié le maximum autorisé de $max_jobs_allowed offres d'emploi actives. Veuillez désactiver une offre existante pour en publier une nouvelle, ou contactez l'administration pour augmenter votre limite."
    ];
    
    // Rediriger vers la page de gestion des offres de l'employeur
    redirect_to('dashboard_employeur'); 
    exit(); // Arrêter l'exécution du script
}
// --------------------------------------------------------------------------


// ... (Le reste du script process_add_job.php continue ici si la limite n'est PAS atteinte)
// 1. Récupération et validation des données (titre, description, etc.)
// 2. Insertion de la nouvelle offre dans la table `offres_emploi`
// 3. Redirection finale vers le tableau de bord avec un message de succès
$titre        = trim($_POST['titre'] ?? '');
$description  = trim($_POST['description'] ?? '');
$lieu         = trim($_POST['lieu'] ?? '');
$salaire      = trim($_POST['salaire'] ?? '');
$type_contrat = $_POST['type_contrat'] ?? ''; 

$errors = [];

// 3. Validation des données côté serveur
if (empty($titre) || empty($description) || empty($lieu) || empty($type_contrat)) {
    $errors[] = 'Les champs Titre, Description, Lieu et Type de Contrat sont obligatoires.';
}

$allowed_contrats = ['CDI', 'CDD', 'Intérim', 'Stage', 'Freelance'];
if (!in_array($type_contrat, $allowed_contrats)) {
    $errors[] = 'Le type de contrat sélectionné est invalide.';
}


// 4. Traitement ou retour avec les erreurs
if (!empty($errors)) {
    // Stocker le message d'erreur et les données postées pour pré-remplir le formulaire
    $_SESSION['post_job_result'] = [
        'success' => false,
        'message' => implode('<br>', $errors)
    ];
    $_SESSION['post_data'] = $_POST; 
    
    redirect_to('post_job');
} else {
    
    // 5. Appel de la fonction de publication sécurisée
    $result = post_job($pdo, $employeur_id, $titre, $description, $lieu, $salaire, $type_contrat);
    
    // 6. Redirection
    if ($result['success']) {
        // Rediriger vers le tableau de bord ou la liste des offres publiées
        $_SESSION['post_job_result'] = $result; 
        redirect_to('dashboard'); 
    } else {
        // En cas d'erreur DB, revenir au formulaire
        $_SESSION['post_job_result'] = $result;
        $_SESSION['post_data'] = $_POST; // Conserver les données
        redirect_to('post_job');
    }
}
?>