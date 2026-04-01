<?php
// views/job_details.php

// Connexion à la base de données (déjà établie dans index.php)
global $pdo; 

// ----------------------------------------------------
// 1. RÉCUPÉRATION ET VÉRIFICATION DE L'OFFRE
// ----------------------------------------------------
$job_id = $_GET['id'] ?? 0;
$job_id = (int)$job_id; // S'assurer que l'ID est un entier

// Récupérer les détails de l'offre
$job = get_job_details($pdo, $job_id);

if (!$job) {
    // Si l'offre n'existe pas ou est inactive, inclure la page 404
    include 'views/404.php';
    return; // Arrêter l'exécution du script
}

// ----------------------------------------------------
// 2. LOGIQUE DU BOUTON POSTULER
// ----------------------------------------------------
$can_apply = false;
$apply_link = 'index.php?page=login'; // Lien par défaut vers la connexion

if (is_logged_in()) {
    if (is_role('candidat')) {
        $can_apply = true;
        // Le lien pointera vers le formulaire de candidature
        $apply_link = 'index.php?page=apply&job_id=' . $job['id']; 
    } else {
        // Employeur : doit voir le bouton de gestion
        $apply_link = 'index.php?page=dashboard'; 
    }
}

?>

<div class="container mt-5">
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-lg p-4">
                <h1 class="text-primary mb-3"><?php echo htmlspecialchars($job['titre']); ?></h1>
                <div class="mb-4">
                    <h2 class="h5 text-muted">Publié par : **<?php echo htmlspecialchars($job['nom_entreprise']); ?>**</h2>
                    <p class="text-secondary small">Publié le : <?php echo date('d/m/Y', strtotime($job['date_publication'])); ?></p>
                </div>
                
                <hr>

                <div class="row mb-4 text-center">
                    <div class="col-md-4">
                        <i class="fas fa-map-marker-alt fa-2x text-info"></i>
                        <p class="mb-0 fw-bold">Lieu</p>
                        <p><?php echo htmlspecialchars($job['lieu']); ?></p>
                    </div>
                    <div class="col-md-4">
                        <i class="fas fa-clock fa-2x text-info"></i>
                        <p class="mb-0 fw-bold">Contrat</p>
                        <p><?php echo htmlspecialchars($job['type_contrat']); ?></p>
                    </div>
                    <div class="col-md-4">
                        <i class="fas fa-money-bill-wave fa-2x text-info"></i>
                        <p class="mb-0 fw-bold">Salaire</p>
                        <p><?php echo empty($job['salaire']) ? 'Non spécifié' : htmlspecialchars($job['salaire']); ?></p>
                    </div>
                </div>
                
                <hr>

                <h3 class="mt-4 mb-3">Description du Poste</h3>
                <div class="job-description-content">
                    <?php 
                    // Utiliser nl2br pour préserver les sauts de ligne de la zone de texte
                    echo nl2br(htmlspecialchars($job['description'])); 
                    ?>
                </div>
            </div>
        </div>
        <div class="col-lg-4"> 
            <div class="card shadow p-3 mb-4 sticky-top" style="top: 70px;">
                <h4 class="card-title text-center">Action</h4>
                
                <?php if ($can_apply): ?>
                    <p class="text-center text-success">Prêt à postuler ?</p>
                    <a href="<?php echo $apply_link; ?>" class="btn btn-lg btn-success w-100">
                        <i class="fas fa-paper-plane"></i> Postuler maintenant
                    </a>
                <?php elseif (is_role('employeur')): ?>
                    <p class="text-center text-primary">Ceci est votre propre annonce.</p>
                    <a href="index.php?page=edit_job&id=<?php echo $job['id']; ?>" class="btn btn-lg btn-warning w-100">
                        <i class="fas fa-edit"></i> Modifier l'offre
                    </a>
                <?php else: ?>
                    <p class="text-center text-danger">Vous devez être connecté(e) en tant que Candidat pour postuler.</p>
                    <a href="<?php echo $apply_link; ?>" class="btn btn-lg btn-primary w-100">
                        <i class="fas fa-sign-in-alt"></i> Se connecter pour postuler
                    </a>
                <?php endif; ?>
            </div>  
            <div class="card p-3 shadow-sm">
                <h4 class="card-title mb-3">À propos de l'Entreprise</h4>
                <p>Nom : **<?php echo htmlspecialchars($job['nom_entreprise']); ?>**</p>
                <p>Email de contact : <?php echo htmlspecialchars($job['email_entreprise']); ?></p>
                <a href="index.php?page=public_company_profile&id=<?php echo $job['employeur_id']; ?>" class="btn btn-sm btn-outline-secondary">Voir le profil</a>
            </div>
        </div> 
    </div>
    <div class="text-center mt-5">
        <a href="index.php?page=jobs" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste des offres
        </a>
    </div>
</div>