<?php
// views/dashboard_employeur.php (Inclus par dashboard.php)

// Ici, vous auriez besoin d'une fonction dans includes/functions.php
// pour récupérer toutes les offres publiées par cet employeur :
// $offres_publiees = get_employer_jobs($pdo, $_SESSION['user_id']);

// Simuler les données pour la structure
$offres_publiees = [
    ['id' => 1, 'titre' => 'Développeur Full-Stack', 'candidatures' => 12, 'statut' => 'Actif', 'date_publication' => '2025-10-01'],
    ['id' => 2, 'titre' => 'Designer UX/UI', 'candidatures' => 5, 'statut' => 'Actif', 'date_publication' => '2025-10-20'],
    ['id' => 3, 'titre' => 'Responsable Marketing', 'candidatures' => 0, 'statut' => 'Inactif', 'date_publication' => '2025-11-01'],
];
?>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card text-center bg-primary text-white mb-4">
                <div class="card-body">
                    <i class="fas fa-plus-circle fa-3x mb-2"></i>
                    <h5 class="card-title">Publier une Offre</h5>
                    <p class="card-text">Créez une nouvelle annonce d'emploi.</p>
                    <a href="index.php?page=post_job" class="btn btn-light btn-sm">Nouvelle Offre</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center bg-success text-white mb-4">
                <div class="card-body">
                    <i class="fas fa-users fa-3x mb-2"></i>
                    <h5 class="card-title">Total Candidatures</h5>
                    <p class="card-text"> <?php echo array_sum(array_column($offres_publiees, 'candidatures')); ?> reçues.</p>
                    <a href="index.php?page=all_applications" class="btn btn-light btn-sm">Gérer tout</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card text-center bg-dark text-white mb-4">
                <div class="card-body">
                    <i class="fas fa-file-alt fa-3x mb-2"></i>
                    <h5 class="card-title">Profil d'Entreprise</h5>
                    <p class="card-text">Informations de votre société.</p>
                    <a href="index.php?page=company_profile" class="btn btn-light btn-sm">Éditer</a>
                </div>
            </div>
        </div>
    </div>

    <h3 class="mt-4">Gestion des Offres Publiées (<?php echo count($offres_publiees); ?>)</h3>
    <a href="index.php?page=employer_alerts_summary" class="btn btn-outline-success btn-lg">
        <i class="fas fa-search-dollar"></i> Tendances Candidats
    </a>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Titre de l'Offre</th>
                <th>Candidatures</th>
                <th>Statut</th>
                <th>Date Publiée</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($offres_publiees)): ?>
                <tr><td colspan="5" class="text-center">Vous n'avez publié aucune offre pour le moment.</td></tr>
            <?php else: ?>
                <?php foreach ($offres_publiees as $job): ?>
                    <tr>
                        <td><a href="index.php?page=view_applicants&job_id=<?php echo $job['id']; ?>"><?php echo htmlspecialchars($job['titre']); ?></a></td>
                        <td><span class="badge bg-primary"><?php echo $job['candidatures']; ?></span></td>
                        <td>
                            <span class="badge bg-<?php echo ($job['statut'] === 'Actif') ? 'success' : 'danger'; ?>">
                                <?php echo htmlspecialchars($job['statut']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($job['date_publication']); ?></td>
                        <td>
                            <a href="index.php?page=edit_job&id=<?php echo $job['id']; ?>" class="btn btn-sm btn-outline-info">Modifier</a>
                            <a href="process_delete_job.php?id=<?php echo $job['id']; ?>" 
                                class="btn btn-sm btn-danger" 
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette offre ? Cette action est irréversible et supprimera toutes les candidatures associées.');">
                                    Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>