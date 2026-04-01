<?php
// views/public_company_profile.php - Profil de l'entreprise visible par tous

global $pdo;

// 1. Vérification de l'ID dans l'URL
$employeur_id = (int)($_GET['id'] ?? 0);

if ($employeur_id <= 0) {
    $_SESSION['error_message'] = "ID d'entreprise invalide.";
    redirect_to('home');
}

// 2. Récupération des données publiques
$company = get_public_company_profile($pdo, $employeur_id);

// NOUVEAU : Récupération des offres d'emploi actives
$active_jobs = get_jobs_by_employer($pdo, $employeur_id);


if ($company === null) {
    $_SESSION['error_message'] = "Profil d'entreprise non trouvé ou accès non autorisé.";
    redirect_to('home');
}
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-lg-12">
            <header class="mb-4 d-flex justify-content-between align-items-center">
                <h1 class="display-4"><i class="fas fa-building text-info"></i> <?php echo htmlspecialchars($company['nom']); ?></h1>
                
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </header>
            <hr>
        </div>
    </div>

    <div class="row">
        
        <div class="col-md-8 mb-4">
            <div class="card shadow-lg p-4">
                <h2 class="h4 mb-3 text-primary">À propos de l'Entreprise</h2>
                <p><?php echo nl2br(htmlspecialchars($company['description'])); ?></p>
            </div>
            
            <!-- <div class="card shadow-lg p-4 mt-4">
                <h2 class="h4 mb-3 text-primary">Offres d'Emploi Actuelles</h2>
                <p class="text-muted">
                    <i class="fas fa-briefcase"></i> Cette section listera toutes les offres d'emploi actives publiées par **<?php echo htmlspecialchars($company['nom']); ?>**. (Nécessite une fonction `get_jobs_by_employer($pdo, $employeur_id)` pour être implémentée).
                </p>
            </div> -->
            <div class="card shadow-lg p-4 mt-4">
                <h2 class="h4 mb-3 text-primary">Offres d'Emploi Actuelles (<?php echo count($active_jobs); ?>)</h2>

                <?php if (empty($active_jobs)): ?>
                    <p class="text-muted">
                        <i class="fas fa-exclamation-circle"></i> Cette entreprise n'a actuellement aucune offre d'emploi active.
                    </p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($active_jobs as $job): ?>
                            <a href="index.php?page=job_details&id=<?php echo $job['id']; ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 text-primary"><?php echo htmlspecialchars($job['titre']); ?></h6>
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['lieu']); ?> &bull; 
                                        <i class="fas fa-file-contract"></i> <?php echo htmlspecialchars($job['type_contrat']); ?>
                                    </small>
                                </div>
                                <small class="text-nowrap ms-3">Publié le <?php echo date('d/m/Y', strtotime($job['date_publication'])); ?></small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Informations Clés</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>Secteur :</strong> <?php echo htmlspecialchars($company['secteur_activite']); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Siège :</strong> <?php echo htmlspecialchars($company['adresse_siege']); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Contact :</strong> <a href="mailto:<?php echo htmlspecialchars($company['email']); ?>"><?php echo htmlspecialchars($company['email']); ?></a>
                    </li>
                    <li class="list-group-item">
                        <strong>Téléphone :</strong> <?php echo htmlspecialchars($company['telephone']); ?>
                    </li>
                    <?php if (!empty($company['site_web'])): ?>
                    <li class="list-group-item">
                        <strong>Site Web :</strong> <a href="<?php echo htmlspecialchars($company['site_web']); ?>" target="_blank" rel="noopener noreferrer">Visiter le site <i class="fas fa-external-link-alt"></i></a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>