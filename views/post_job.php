<?php
// views/post_job.php

// ----------------------------------------------------
// 1. VÉRIFICATION DE SÉCURITÉ : SEUL L'EMPLOYEUR PEUT ACCÉDER
// ----------------------------------------------------
if (!is_role('employeur')) {
    // Rediriger si non connecté ou n'est pas un employeur
    redirect_to('home'); 
}

// Récupération des messages de session
$result = [];
if (isset($_SESSION['post_job_result'])) {
    $result = $_SESSION['post_job_result'];
    unset($_SESSION['post_job_result']); 
}

// Récupérer les anciennes données postées en cas d'erreur
$old_data = $_SESSION['post_data'] ?? [];
unset($_SESSION['post_data']); 

// Options pour le type de contrat
$contrat_options = ['CDI', 'CDD', 'Intérim', 'Stage', 'Freelance'];
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h2>Publier une Nouvelle Offre d'Emploi</h2>
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

                    <form action="process_post_job.php" method="POST">
                        
                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre du Poste</label>
                            <input type="text" class="form-control" id="titre" name="titre" required 
                                value="<?php echo htmlspecialchars($old_data['titre'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description Détaillée du Poste</label>
                            <textarea class="form-control" id="description" name="description" rows="8" required><?php echo htmlspecialchars($old_data['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="lieu" class="form-label">Lieu de l'Emploi</label>
                                <input type="text" class="form-control" id="lieu" name="lieu" required 
                                    value="<?php echo htmlspecialchars($old_data['lieu'] ?? ''); ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="salaire" class="form-label">Salaire ou Rémunération (Ex: 35k - 40k €)</label>
                                <input type="text" class="form-control" id="salaire" name="salaire" 
                                    value="<?php echo htmlspecialchars($old_data['salaire'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="type_contrat" class="form-label">Type de Contrat</label>
                            <select class="form-select" id="type_contrat" name="type_contrat" required>
                                <?php foreach ($contrat_options as $option): ?>
                                    <option value="<?php echo $option; ?>" 
                                        <?php echo (isset($old_data['type_contrat']) && $old_data['type_contrat'] == $option) ? 'selected' : ''; ?>>
                                        <?php echo $option; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-3">Publier l'Offre</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>