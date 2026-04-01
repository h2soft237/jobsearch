<?php
// views/admin_jobs.php - Gestion de toutes les Offres d'Emploi

global $pdo; 

// 1. VÉRIFICATION DE SÉCURITÉ
if (!is_role('admin')) {
    redirect_to('dashboard'); 
}

// 2. Récupération des données
$jobs = get_all_jobs_for_admin($pdo);

// Récupération des messages de session (pour les actions DELETE/TOGGLE)
$result = [];
if (isset($_SESSION['admin_job_result'])) {
    $result = $_SESSION['admin_job_result'];
    unset($_SESSION['admin_job_result']); 
}
?>

<div class="container mt-5">
    <header class="mb-4">
        <h1 class="display-5"><i class="fas fa-briefcase text-warning"></i> Gestion des Offres d'Emploi</h1>
        <p class="lead">Visualisez et modérez toutes les offres publiées sur la plateforme.</p>
        <hr>
    </header>

    <?php 
    if (!empty($result)): ?>
        <div class="alert alert-<?php echo $result['success'] ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($result['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <a href="index.php?page=dashboard_admin" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Retour au Tableau de Bord
    </a>

    <div class="card shadow-lg">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre de l'Offre</th>
                            <th>Entreprise</th>
                            <th>Candidatures</th>
                            <th>Statut</th>
                            <th>Publié le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($jobs)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Aucune offre d'emploi n'a encore été publiée.</td>
                            </tr>
                        <?php endif; ?>
                        
                        <?php foreach ($jobs as $job): ?>
                        <tr>
                            <td><?php echo $job['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($job['titre']); ?></strong><br>
                                <small class="text-muted"><?php echo htmlspecialchars($job['lieu']); ?> / <?php echo htmlspecialchars($job['type_contrat']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($job['nom_entreprise']); ?></td>
                            <td>
                                <span class="badge bg-primary"><?php echo $job['total_candidatures']; ?></span>
                            </td>
                            <td>
                                <?php if ($job['est_actif']): ?>
                                    <span class="badge bg-success"><i class="fas fa-check-circle"></i> Actif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="fas fa-times-circle"></i> Inactif</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($job['date_publication'])); ?></td>
                            <td>
                                <a href="index.php?page=job_details&id=<?php echo $job['id']; ?>" class="btn btn-sm btn-info me-2" title="Voir les détails publics">
                                    <i class="fas fa-eye text-white"></i>
                                </a>

                                <?php 
                                $new_status = $job['est_actif'] ? 0 : 1; 
                                $btn_class = $job['est_actif'] ? 'btn-warning' : 'btn-success';
                                $btn_icon = $job['est_actif'] ? 'fas fa-toggle-off' : 'fas fa-toggle-on';
                                $title_text = $job['est_actif'] ? 'Désactiver' : 'Activer';
                                ?>
                                <a href="process_admin_action.php?action=toggle_job_status&id=<?php echo $job['id']; ?>&status=<?php echo $new_status; ?>"
                                   class="btn btn-sm <?php echo $btn_class; ?> me-2"
                                   onclick="return confirm('Voulez-vous vraiment <?php echo $title_text; ?> cette offre ?')"
                                   title="<?php echo $title_text; ?>">
                                    <i class="<?php echo $btn_icon; ?>"></i>
                                </a>
                                
                                <a href="process_admin_action.php?action=delete_admin_job&id=<?php echo $job['id']; ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('ATTENTION : Êtes-vous sûr de vouloir SUPPRIMER DÉFINITIVEMENT cette offre et toutes ses candidatures ?')"
                                   title="Supprimer définitivement">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>