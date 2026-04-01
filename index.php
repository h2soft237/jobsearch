<?php
// index.php

// ----------------------------------------------------
// 1. DÉMARRAGE ET INCLUSIONS ESSENTIELLES
// ----------------------------------------------------
// Démarrer la session pour gérer la connexion utilisateur
session_start(); 

//require_once 'vendor/autoload.php'; 
//require_once 'includes/smtp_config.php';
// Inclure le fichier de connexion à la base de données
require_once 'includes/db.php'; 
// -> AJOUTEZ CELA :
require_once 'includes/functions.php'; // Inclure toutes les fonctions utiles
// Établir la connexion à la base de données et la stocker
$pdo = connect_db();

// 2. Chargement des paramètres du site dans une variable globale
$GLOBALS['settings'] = load_site_settings($pdo); // <-- NOUVEAU

// 3. VÉRIFICATION GLOBALE DU MODE MAINTENANCE
if (($GLOBALS['settings']['maintenance_mode'] ?? '0') == '1' && !is_role('admin')) {
    // Si le mode maintenance est activé ET l'utilisateur n'est PAS un admin, 
    // afficher la page de maintenance et arrêter le script.
    include 'views/header.php';
    include 'views/maintenance.php'; // Vous devrez créer ce fichier
    include 'views/footer.php';
    exit();
}
// ----------------------------------------------------
// 2. LOGIQUE DE ROUTAGE (Procédurale)
// ----------------------------------------------------

// Récupérer le paramètre 'page' de l'URL ou utiliser 'home' par défaut
$page = isset($_GET['page']) ? $_GET['page'] : 'home';


// Redirection du tableau de bord basée sur le rôle
if ($page === 'dashboard' && is_logged_in()) {
    $role = $_SESSION['user_role'] ?? 'candidat'; 
    
    if ($role === 'employeur') {
        $page = 'dashboard_employeur';
    } elseif ($role === 'admin') {
        $page = 'dashboard_admin'; // <--- NOUVEAU ROLE ADMIN
    } else {
        $page = 'dashboard_candidat';
    }
}
/* if ($page === 'alerts_summary' && is_role('employeur')) {
    require 'views/employer_alerts_summary.php';
} */

// LOGIQUE DE DÉCONNEXION
/* if ($page === 'logout') {
    logout_user(); // Appelle la fonction de déconnexion
} */


// Déterminer le chemin vers le fichier de la vue à afficher
$view_path = 'views/' . $page . '.php';
//die($view_path);
// ----------------------------------------------------
// 3. LOGIQUE MÉTIER DE LA PAGE
// ----------------------------------------------------
// Vérification de la page spécifique (si vous n'utilisez pas de routeur dynamique)
/* if ($page === 'alerts_summary' && is_role('employeur')) {
    $view_file = 'views/employer_alerts_summary.php';
} */
//
// Ici, vous pourriez inclure des fichiers de fonctions spécifiques à la page
// avant d'inclure la vue, par exemple :
// if ($page === 'jobs') {
//     $jobs = get_all_jobs($pdo); // Une fonction qui récupère les offres
// }


// ----------------------------------------------------
// 4. AFFICHAGE DES VUES (HTML)
// ----------------------------------------------------

// Inclure l'en-tête (Head, navigation, etc.)
include 'views/header.php';

// Vérifier si le fichier de vue existe et l'inclure
if (file_exists($view_path)) {
    // Afficher le contenu de la page demandée
    include $view_path;
} else {
    // Afficher une page d'erreur 404 si la page n'existe pas
    include 'views/404.php';
}

// Inclure le pied de page
include 'views/footer.php';

// ----------------------------------------------------
// 5. FIN DU SCRIPT
// ----------------------------------------------------
?>