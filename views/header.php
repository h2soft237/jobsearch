<?php
// views/header.php
// Inclut le début du HTML, le <head>, et la barre de navigation.
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Board | <?php echo ucfirst($page); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Styles de base personnalisés */
        body { padding-top: 56px; } /* Pour la barre de navigation fixe */
        .footer { background-color: #f8f9fa; padding: 20px 0; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <?php echo htmlspecialchars($GLOBALS['settings']['site_name'] ?? 'Nom du Site'); ?> 
        </a>
        <!-- <a class="navbar-brand" href="index.php">JobBoard PHP</a> -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($page == 'home') ? 'active' : ''; ?>" href="index.php">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($page == 'jobs') ? 'active' : ''; ?>" href="index.php?page=jobs">Offres</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=contact">Contactez-nous</a>
                </li>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'employeur'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'post_job') ? 'active' : ''; ?>" href="index.php?page=post_job">Publier</a>
                    </li>
                <?php endif; ?>
            </ul>
            
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=dashboard">Tableau de bord</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-warning" href="process_logout.php">Déconnexion</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-success me-2" href="index.php?page=login">Connexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light" href="index.php?page=register">Inscription</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main>