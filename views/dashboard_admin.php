<?php
// views/dashboard_admin.php

global $pdo; 

// 1. VÉRIFICATION DE SÉCURITÉ
if (!is_role('admin')) {
    $_SESSION['error_message'] = 'Accès refusé. Seuls les administrateurs peuvent accéder à ce panneau.';
    redirect_to('dashboard'); 
}

// 2. Récupération des données
$counts = get_total_counts($pdo);
$latest_jobs = get_latest_jobs($pdo);
$latest_users = get_latest_users($pdo);

// Récupération des messages de session (pour les actions futures)
$result = [];
if (isset($_SESSION['admin_result'])) {
    $result = $_SESSION['admin_result'];
    unset($_SESSION['admin_result']); 
}
?>

<div class="container mt-5">
    <header class="mb-4">
        <h1 class="display-4"><i class="fas fa-user-shield text-danger"></i> Panneau d'Administration</h1>
        <p class="lead">Bienvenue, **<?php echo htmlspecialchars($_SESSION['user_name']); ?>**. Gestion centralisée de la plateforme.</p>
        <hr>
    </header>

    <?php 
    if (!empty($result)): ?>
        <div class="alert alert-<?php echo $result['success'] ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($result['message']); ?>
        </div>
    <?php endif; ?>

    <div class="row mb-5">
        <h2 class="h5 mb-3">Statistiques Clés</h2>
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <h5 class="card-title">Utilisateurs Totaux</h5>
                    <p class="card-text display-4"><?php echo $counts['utilisateurs']; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <h5 class="card-title">Offres Publiées</h5>
                    <p class="card-text display-4"><?php echo $counts['offres']; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow">
                <div class="card-body">
                    <h5 class="card-title">Candidatures</h5>
                    <p class="card-text display-4"><?php echo $counts['candidatures']; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark shadow">
                <div class="card-body">
                    <h5 class="card-title">Employeurs</h5>
                    <p class="card-text display-4"><?php echo $counts['employeurs']; ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Dernières Offres Publiées</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <?php if (empty($latest_jobs)): ?>
                        <li class="list-group-item text-muted">Aucune offre récente.</li>
                    <?php else: ?>
                        <?php foreach ($latest_jobs as $job): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><?php echo htmlspecialchars($job['titre']); ?></span>
                                <small class="text-muted"><?php echo date('d/m/Y', strtotime($job['date_publication'])); ?></small>
                                <a href="index.php?page=job_details&id=<?php echo $job['id']; ?>" class="btn btn-sm btn-outline-primary">Voir</a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Nouveaux Utilisateurs</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <?php if (empty($latest_users)): ?>
                        <li class="list-group-item text-muted">Aucun nouvel utilisateur.</li>
                    <?php else: ?>
                        <?php foreach ($latest_users as $user): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><?php echo htmlspecialchars($user['nom']); ?> (<span class="text-capitalize text-info"><?php echo $user['role']; ?></span>)</span>
                                <small class="text-muted"><?php echo date('d/m/Y', strtotime($user['date_inscription'])); ?></small>
                                <a href="#" class="btn btn-sm btn-outline-secondary disabled">Gérer</a> 
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

    </div>

    <div class="card p-4 mt-4 shadow">
        <h5 class="mb-3">Tâches Administratives Rapides</h5>
        <div class="d-grid gap-2 d-md-block">
            <a href="index.php?page=admin_users" class="btn btn-danger me-2"><i class="fas fa-trash"></i> Gérer les Utilisateurs</a>
            <a href="index.php?page=admin_jobs" class="btn btn-warning me-2">
                <i class="fas fa-briefcase"></i> Gérer Toutes les Offres
            </a>
            <!-- <a href="index.php?page=admin_users" class="btn btn-danger me-2">
                <i class="fas fa-trash"></i> Gérer les Utilisateurs
            </a> -->
            <!-- <a href="#" class="btn btn-warning disabled me-2"><i class="fas fa-briefcase"></i> Gérer Toutes les Offres</a> -->
            <a href="clean_orphaned_files.php" target="_blank" class="btn btn-secondary me-2" onclick="alert('Ceci exécute le script de nettoyage des CV. Il est préférable de le lancer via CRON.');">
                <i class="fas fa-broom"></i> Nettoyer Fichiers
            </a>
            <a href="index.php?page=admin_reports" class="btn btn-info me-2">
                <i class="fas fa-chart-line"></i> Rapports et Statistiques
            </a>
            <a href="index.php?page=admin_settings" class="btn btn-primary me-2">
                <i class="fas fa-sliders-h"></i> Paramètres
            </a>
        </div>
    </div>
</div>