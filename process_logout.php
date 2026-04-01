<?php
// process_logout.php

// 1. Démarrer la session (nécessaire pour accéder aux variables de session à détruire)
session_start();

// 2. Inclure le fichier de fonctions où la fonction logout_user() est définie
require_once 'includes/functions.php';

// 3. Appeler la fonction de déconnexion
// Cette fonction gère la destruction de la session, la suppression des cookies (si utilisés)
// et la redirection vers la page de connexion ou d'accueil.
logout_user();

// Le script s'arrête ici après la redirection effectuée par logout_user().
?>