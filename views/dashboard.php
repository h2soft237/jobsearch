<?php
// views/dashboard.php

// ----------------------------------------------------
// 1. VÉRIFICATION DE SÉCURITÉ ET REDIRECTION
// ----------------------------------------------------
// Si l'utilisateur n'est pas connecté, le rediriger immédiatement
if (!is_logged_in()) {
    redirect_to('login');
}

// Récupérer le rôle de l'utilisateur connecté
$user_role = $_SESSION['user_role'] ?? 'candidat'; 
$user_name = $_SESSION['user_name'] ?? 'Utilisateur';

// Connexion à la base de données (déjà faite dans index.php, mais bonne pratique)
// Globaliser $pdo si nécessaire, ou le passer aux fonctions (meilleure pratique)
global $pdo; 

// ----------------------------------------------------
// 2. CONTENU DU TABLEAU DE BORD
// ----------------------------------------------------
?>

<div class="container mt-5">
    <header class="mb-4">
        <h1 class="display-4">Tableau de Bord</h1>
        <p class="lead">Bienvenue, **<?php echo htmlspecialchars($user_name); ?>** (Rôle : **<?php echo ucfirst($user_role); ?>**)</p>
        <hr>
    </header>

    <?php 
    // ----------------------------------------------------
    // 3. LOGIQUE CONDITIONNELLE SELON LE RÔLE
    // ----------------------------------------------------
    
    if ($user_role === 'candidat') {
        // Inclure le contenu spécifique au Candidat
        include 'dashboard_candidat.php'; 
    } elseif ($user_role === 'employeur') {
        // Inclure le contenu spécifique à l'Employeur
        include 'dashboard_employeur.php'; 
    } else {
        // Cas d'erreur ou rôle non reconnu
        echo '<div class="alert alert-warning">Votre rôle utilisateur est inconnu. Veuillez contacter l\'administrateur.</div>';
    }
    ?>

</div>