<?php 
// views/maintenance.php 

// Définir le code de statut HTTP 503 Service Unavailable
if (!headers_sent()) {
    header('HTTP/1.1 503 Service Temporarily Unavailable');
    header('Retry-After: 3600'); // Réessayer dans une heure
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1 class="display-3 text-warning">
                <i class="fas fa-tools"></i> Maintenance en Cours
            </h1>
            
            <h2 class="mb-4">Site Temporairement Indisponible</h2>
            
            <p class="lead">
                Nous sommes actuellement en train d'effectuer des mises à jour importantes sur le site.
                <br>
                Le service sera rétabli dans les plus brefs délais.
            </p>
            
            <p class="text-muted mt-5">
                Merci de votre patience.
            </p>
            
            <?php 
            // Afficher des infos à l'admin s'il est connecté (pour le débogage)
            if (is_role('admin')): 
            ?>
                <div class="mt-5 p-3 bg-light border rounded text-start">
                    <p class="mb-0 small text-danger">
                        **NOTE ADMIN :** Le site est en mode maintenance. Seuls les administrateurs peuvent naviguer.
                        <br>
                        Désactivez le mode maintenance via le panneau d'administration pour remettre le site en ligne.
                    </p>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>