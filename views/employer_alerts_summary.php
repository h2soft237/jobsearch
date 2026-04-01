<?php
// views/employer_alerts_summary.php - Aperçu des tendances de recherche

global $pdo;

// VÉRIFICATION DE SÉCURITÉ
if (!is_role('employeur')) {
    redirect_to('dashboard');
}

$top_keywords = get_top_job_alert_keywords($pdo, 15);
?>

<div class="container mt-5">
    <header class="mb-4">
        <h1 class="display-5"><i class="fas fa-chart-line text-success"></i> Tendances de Recherche (Alertes Candidats)</h1>
        <p class="lead">Consultez les mots-clés et les critères de recherche les plus populaires définis par les candidats. Ces données vous aident à optimiser le titre et la description de vos offres.</p>
        <hr>
    </header>

    <div class="card shadow-lg">
        <div class="card-header bg-success text-white">
            Top 15 des Mots-Clés d'Alerte les plus Demandés
        </div>
        <ul class="list-group list-group-flush">
            <?php if (empty($top_keywords)): ?>
                <li class="list-group-item text-center text-muted">Aucune donnée d'alerte disponible pour le moment.</li>
            <?php else: ?>
                <?php $rank = 1; ?>
                <?php foreach ($top_keywords as $item): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="badge bg-primary rounded-pill me-3"><?php echo $rank++; ?></span>
                        <h5 class="m-0 flex-grow-1"><?php echo htmlspecialchars($item['mots_cles']); ?></h5>
                        <span class="badge bg-secondary rounded-pill">
                            <?php echo $item['count']; ?> alertes
                        </span>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
    
    <footer class="mt-4">
        <p class="text-muted"><small>Ces données sont agrégées et anonymes, basées sur les critères d'alerte actifs de tous les candidats.</small></p>
    </footer>
</div>