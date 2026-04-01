<?php
// views/404.php

// Si cette page est incluse directement dans index.php (sans redirection),
// on peut définir le code de statut HTTP 404.
// Note : Si c'est inclus suite à un échec de redirection interne, il se peut que le header ait déjà été envoyé.
if (!headers_sent()) {
    header("HTTP/1.0 404 Not Found");
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            
            <h1 class="display-1 text-danger">404</h1>
            
            <h2 class="mb-4">Page Non Trouvée</h2>
            
            <p class="lead">
                Oups ! Il semble que la page que vous recherchez n'existe pas ou a été déplacée.
            </p>
            
            <p class="text-muted">
                Veuillez vérifier l'adresse URL ou utiliser les liens ci-dessous.
            </p>
            
            <div class="mt-5">
                <a href="index.php?page=home" class="btn btn-primary btn-lg me-3">
                    <i class="fas fa-home"></i> Retour à l'accueil
                </a>
                
                <a href="index.php?page=jobs" class="btn btn-secondary btn-lg">
                    <i class="fas fa-briefcase"></i> Voir toutes les offres
                </a>
            </div>
            
            <?php 
            // Optionnel : Afficher un message de débogage si l'utilisateur est administrateur ou en mode développement
            // if (is_logged_in() && is_role('admin')): 
            // ?>
                <?php // endif; ?>
            
        </div>
    </div>
</div>