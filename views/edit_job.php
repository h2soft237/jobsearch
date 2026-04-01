<?php
// views/edit_job.php

// Connexion à la base de données
global $pdo; 

// ----------------------------------------------------
// 1. VÉRIFICATION DE SÉCURITÉ ET RÉCUPÉRATION DES DONNÉES
// ----------------------------------------------------
if (!is_role('employeur')) {
    redirect_to('login'); 
}

$job_id = $_GET['id'] ?? 0;
$job_id = (int)$job_id;
$employeur_id = $_SESSION['user_id'];

// Récupérer les données de l'offre existante pour pré-remplir le formulaire
$job_data = get_job_for_editing($pdo, $job_id, $employeur_id);

if (!$job_data) {
    // Si l'offre n'existe pas, n'est pas active, ou n'appartient pas à l'employeur
    $_SESSION['error_message'] = 'Offre non trouvée ou accès non autorisé.';
    redirect_to('dashboard');
}

// Récupération des messages de session
$result = [];
if (isset($_SESSION['edit_job_result'])) {
    $result = $_SESSION['edit_job_result'];
    unset($_SESSION['edit_job_result']); 
}

// Les données à afficher proviennent soit de l'échec de la dernière soumission (SESSION),
// soit directement de la base de données ($job_data).
$display_data = $_SESSION['post_data'] ?? $job_data;
unset($_SESSION['post_data']); 

// Options pour le type de contrat
$contrat_options = ['CDI', 'CDD', 'Intérim', 'Stage', 'Freelance'];
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg">
                <div class="card-header bg-warning text-dark">
                    <h2>Modification de l'Offre : **<?php echo htmlspecialchars($job_data['titre']); ?>**</h2>
                </div>
                <div class="card-body">
                    
                    <?php 
                    if (!empty($result)): ?>
                        <div class="alert alert-<?php echo $result['success'] ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($result['message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="process_edit_job.php" method="POST">
                        
                        <input type="hidden" name="job_id" value="<?php echo $job_data['id']; ?>">
                        
                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre du Poste</label>
                            <input type="text" class="form-control" id="titre" name="titre" required 
                                value="<?php echo htmlspecialchars($display_data['titre'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description Détaillée du Poste</label>
                            <textarea class="form-control" id="description" name="description" rows="8" required><?php echo htmlspecialchars($display_data['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="lieu" class="form-label">Lieu de l'Emploi</label>
                                <input type="text" class="form-control" id="lieu" name="lieu" required 
                                    value="<?php echo htmlspecialchars($display_data['lieu'] ?? ''); ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="salaire" class="form-label">Salaire ou Rémunération</label>
                                <input type="text" class="form-control" id="salaire" name="salaire" 
                                    value="<?php echo htmlspecialchars($display_data['salaire'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="type_contrat" class="form-label">Type de Contrat</label>
                            <select class="form-select" id="type_contrat" name="type_contrat" required>
                                <?php foreach ($contrat_options as $option): ?>
                                    <option value="<?php echo $option; ?>" 
                                        <?php echo ($display_data['type_contrat'] == $option) ? 'selected' : ''; ?>>
                                        <?php echo $option; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="est_actif" name="est_actif" value="1" 
                                <?php echo ($display_data['est_actif'] == 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="est_actif">L'offre est **Active** (Visible par les candidats)</label>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 mt-3"><i class="fas fa-save"></i> Enregistrer les Modifications</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="index.php?page=dashboard">Annuler et retourner au tableau de bord</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>