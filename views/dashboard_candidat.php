<?php
// views/dashboard_candidat.php (Inclus par dashboard.php)

// Ici, vous auriez besoin d'une fonction dans includes/functions.php
// pour récupérer toutes les candidatures de cet utilisateur :
// $candidatures = get_user_applications($pdo, $_SESSION['user_id']);

// Simuler les données pour la structure
$candidatures = [
    ['id' => 101, 'titre_offre' => 'Développeur Full-Stack', 'entreprise' => 'TechCorp', 'statut' => 'Accepté', 'date_candidature' => '2025-10-01'],
    ['id' => 102, 'titre_offre' => 'Analyste Financier', 'entreprise' => 'FinancePro', 'statut' => 'En attente', 'date_candidature' => '2025-10-15'],
    ['id' => 103, 'titre_offre' => 'Chef de Projet Junior', 'entreprise' => 'GlobalSolutions', 'statut' => 'Refusé', 'date_candidature' => '2025-11-05'],
];
?>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card text-center bg-info text-white mb-4">
                <div class="card-body">
                    <i class="fas fa-search fa-3x mb-2"></i>
                    <h5 class="card-title">Rechercher des Offres</h5>
                    <p class="card-text">Commencez de nouvelles recherches.</p>
                    <a href="index.php?page=jobs" class="btn btn-light btn-sm">Voir les offres</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card text-center bg-secondary text-white mb-4">
                <div class="card-body">
                    <i class="fas fa-user-circle fa-3x mb-2"></i>
                    <h5 class="card-title">Modifier Mon Profil</h5>
                    <p class="card-text">Mettez à jour votre CV et compétences.</p>
                    <a href="index.php?page=profile_edit" class="btn btn-light btn-sm">Éditer le profil</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card text-center bg-warning text-dark mb-4">
                <div class="card-body">
                    <i class="fas fa-bell fa-3x mb-2"></i>
                    <h5 class="card-title">Alertes d'Emploi</h5>
                    <p class="card-text">Gérez vos notifications personnalisées.</p>
                    <a href="index.php?page=alerts" class="btn btn-light btn-sm">Gérer les alertes</a>
                </div>
            </div>
        </div>
    </div>

    <h3 class="mt-4">Suivi des Candidatures (<?php echo count($candidatures); ?>)</h3>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Offre</th>
                <th>Entreprise</th>
                <th>Date</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($candidatures)): ?>
                <tr><td colspan="5" class="text-center">Vous n'avez postulé à aucune offre pour le moment.</td></tr>
            <?php else: ?>
                <?php foreach ($candidatures as $app): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($app['titre_offre']); ?></td>
                        <td><?php echo htmlspecialchars($app['entreprise']); ?></td>
                        <td><?php echo htmlspecialchars($app['date_candidature']); ?></td>
                        <td>
                            <span class="badge bg-<?php 
                                if ($app['statut'] === 'Accepté') echo 'success';
                                elseif ($app['statut'] === 'Refusé') echo 'danger';
                                else echo 'secondary';
                            ?>"><?php echo htmlspecialchars($app['statut']); ?></span>
                        </td>
                        <td><a href="index.php?page=job_details&id=<?php echo $app['id']; ?>" class="btn btn-sm btn-outline-primary">Voir l'offre</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>