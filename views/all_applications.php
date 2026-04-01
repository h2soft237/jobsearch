<?php
// views/all_applications.php - Vue de toutes les candidatures pour l'employeur

global $pdo;

// 1. VÉRIFICATION DE SÉCURITÉ
if (!is_role('employeur')) {
    redirect_to('dashboard');
}

$employeur_id = $_SESSION['user_id'];
$applications = get_all_employer_applications($pdo, $employeur_id);

// Les statuts doivent correspondre à ceux de votre table 'candidatures'
$statuts = [
    'en attente' => 'En attente',
    'en revue' => 'En Revue',
    'retenue' => 'Retenue',
    'rejetee' => 'Rejetée'
];
?>

<div class="container mt-5">
    <header class="mb-4">
        <h1 class="display-5"><i class="fas fa-inbox text-success"></i> Toutes les Candidatures</h1>
        <p class="lead">Gérez l'ensemble des postulations reçues pour toutes vos offres d'emploi.</p>
        <hr>
    </header>

    <a href="index.php?page=dashboard_employeur" class="btn btn-sm btn-outline-secondary mb-4">
        <i class="fas fa-arrow-left"></i> Retour au Tableau de Bord
    </a>

    <div class="card shadow-lg">
        <div class="card-header bg-success text-white">
            Récapitulatif (<?php echo count($applications); ?> candidatures totales)
        </div>
        <div class="card-body p-0">
            <?php if (empty($applications)): ?>
                <div class="p-4 text-center text-muted">Vous n'avez reçu aucune candidature pour le moment.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover m-0">
                        <thead>
                            <tr>
                                <th>Offre d'Emploi</th>
                                <th>Candidat</th>
                                <th>Date de Candidature</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td>
                                        <a href="index.php?page=job_details&id=<?php echo $app['offre_id']; ?>" title="Voir l'offre">
                                            **<?php echo htmlspecialchars($app['titre_offre']); ?>**
                                        </a>
                                    </td>
                                    
                                    <td>
                                        <i class="fas fa-user"></i> 
                                        <?php echo htmlspecialchars($app['nom_candidat']); ?>
                                        <br>
                                        <small><a href="mailto:<?php echo htmlspecialchars($app['email_candidat']); ?>"><?php echo htmlspecialchars($app['email_candidat']); ?></a></small>
                                    </td>
                                    
                                    <td>
                                        <?php echo date('d/m/Y H:i', strtotime($app['date_candidature'])); ?>
                                    </td>
                                    
                                    <td>
                                        <?php 
                                            $statut_key = $app['statut_candidature'];
                                            $badge_class = 'badge bg-secondary';
                                            if ($statut_key == 'retenue') $badge_class = 'badge bg-success';
                                            if ($statut_key == 'rejetee') $badge_class = 'badge bg-danger';
                                            if ($statut_key == 'en revue') $badge_class = 'badge bg-info';
                                        ?>
                                        <span class="<?php echo $badge_class; ?>"><?php echo $statuts[$statut_key] ?? $statut_key; ?></span>
                                    </td>
                                    
                                    <td>
                                        <a href="index.php?page=view_application&id=<?php echo $app['candidature_id']; ?>" class="btn btn-sm btn-primary" title="Voir les détails et documents">
                                            Détails <i class="fas fa-eye"></i>
                                        </a>
                                        </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>