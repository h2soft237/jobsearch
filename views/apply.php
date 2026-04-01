<?php
// views/apply.php

// Connexion à la base de données
global $pdo; 

// ----------------------------------------------------
// 1. VÉRIFICATIONS DE SÉCURITÉ ET PRÉPARATION DES DONNÉES
// ----------------------------------------------------

// Vérifier si l'utilisateur est connecté et est un CANDIDAT
if (!is_role('candidat')) {
    // Rediriger si non connecté, ou s'il s'agit d'un employeur
    redirect_to('login'); 
}

// Récupérer l'ID de l'offre depuis l'URL
$job_id = $_GET['job_id'] ?? 0;
$job_id = (int)$job_id;

// Récupérer les détails de l'offre pour l'affichage et la vérification
$job = get_job_details($pdo, $job_id);

if (!$job) {
    // Si l'offre n'existe pas
    include 'views/404.php';
    return;
}

// Vérifier si le candidat a déjà postulé à cette offre (optionnel mais fortement recommandé)
// Nécessite une fonction à créer dans functions.php
// Vérifier si le candidat a déjà postulé à cette offre (MAINTENANT ACTIVÉ)
if (has_applied($pdo, $_SESSION['user_id'], $job_id)) {
    $_SESSION['apply_result'] = [
        'success' => false,
        'message' => 'Vous avez déjà soumis votre candidature pour cette offre. Vous ne pouvez postuler qu\'une seule fois.'
    ];
    // Rediriger vers la page de détails de l'offre pour afficher l'erreur
    redirect_to('job_details&id=' . $job_id); 
    return; // Arrêter l'exécution du script
}

/*
if (has_applied($pdo, $_SESSION['user_id'], $job_id)) {
    $_SESSION['apply_result'] = [
        'success' => false,
        'message' => 'Vous avez déjà soumis votre candidature pour cette offre.'
    ];
    redirect_to('job_details&id=' . $job_id);
}
*/

// Récupération des messages de session (après traitement)
$result = [];
if (isset($_SESSION['apply_result'])) {
    $result = $_SESSION['apply_result'];
    unset($_SESSION['apply_result']); 
}

// Récupérer l'ancienne lettre de motivation postée en cas d'erreur
$old_data = $_SESSION['post_data'] ?? [];
unset($_SESSION['post_data']); 
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white">
                    <h2 class="mb-0">Candidature pour : **<?php echo htmlspecialchars($job['titre']); ?>**</h2>
                    <p class="mb-0 small">chez <?php echo htmlspecialchars($job['nom_entreprise']); ?></p>
                </div>
                <div class="card-body">
                    
                    <?php 
                    // Affichage des messages de succès ou d'erreur
                    if (!empty($result)): ?>
                        <div class="alert alert-<?php echo $result['success'] ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($result['message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <p class="alert alert-info">Veuillez télécharger votre CV et rédiger votre lettre de motivation pour postuler.</p>

                    <form action="process_apply.php" method="POST" enctype="multipart/form-data">
                        
                        <input type="hidden" name="offre_id" value="<?php echo $job['id']; ?>">
                        
                        <div class="mb-3">
                            <label for="cv_file" class="form-label fw-bold">Votre Curriculum Vitae (CV)</label>
                            <input class="form-control" type="file" id="cv_file" name="cv_file" accept=".pdf,.doc,.docx" required>
                            <div class="form-text">Formats acceptés : PDF, DOC, DOCX. Taille max. : 5 Mo.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="lettre_motivation" class="form-label fw-bold">Lettre de Motivation (Optionnel)</label>
                            <textarea class="form-control" id="lettre_motivation" name="lettre_motivation" rows="8" 
                                placeholder="Rédigez ici votre lettre de motivation ou ajoutez un court message."><?php echo htmlspecialchars($old_data['lettre_motivation'] ?? ''); ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 mt-3">
                            <i class="fas fa-check-circle"></i> Confirmer et Postuler
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="index.php?page=job_details&id=<?php echo $job['id']; ?>">Annuler et retourner à l'offre</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>