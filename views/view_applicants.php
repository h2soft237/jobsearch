<?php
// views/view_applicants.php

// Connexion à la base de données
global $pdo; 

// ----------------------------------------------------
// 1. VÉRIFICATION DE SÉCURITÉ ET PRÉPARATION DES DONNÉES
// ----------------------------------------------------

// Vérifier si l'utilisateur est connecté et est un EMPLOYEUR
if (!is_role('employeur')) {
    redirect_to('login'); 
}

$offre_id = $_GET['job_id'] ?? 0;
$offre_id = (int)$offre_id;
$employeur_id = $_SESSION['user_id'];

// Récupérer les candidatures pour cette offre (avec vérification de propriété intégrée)
$applicants = get_applicants_for_job($pdo, $offre_id, $employeur_id);

// Récupérer le titre de l'offre (s'il y a des candidats, il sera dans le premier résultat)
$job_title = !empty($applicants) ? $applicants[0]['titre_offre'] : 'Offre Inconnue';

// Si aucun candidat n'est trouvé ET l'offre_id est valide, on peut supposer
// que l'offre est soit inexistante, soit qu'elle n'a pas encore de candidatures.
if (empty($applicants) && $offre_id > 0) {
    // Vérifier si l'employeur possède bien cette offre, même sans candidat.
    // Pour simplifier, on suppose ici que si l'offre_id est invalide ou n'appartient pas 
    // à l'employeur, $applicants sera vide.
    $job_title = $job_title; // Conserve le titre si la requête a réussi à le récupérer
} elseif ($offre_id === 0) {
     $_SESSION['error_message'] = 'ID de l\'offre manquant ou invalide.';
     redirect_to('dashboard');
}

// Récupération des messages de session (après traitement, ex: mise à jour du statut)
$result = [];
if (isset($_SESSION['applicant_status_result'])) {
    $result = $_SESSION['applicant_status_result'];
    unset($_SESSION['applicant_status_result']); 
}
?>

<div class="container mt-5">
    <header class="mb-4">
        <a href="index.php?page=dashboard" class="btn btn-secondary btn-sm mb-3">
            <i class="fas fa-arrow-left"></i> Retour au Tableau de Bord
        </a>
        <h1 class="display-5">Candidats pour : **<?php echo htmlspecialchars($job_title); ?>**</h1>
        <p class="lead">Gérez et évaluez les postulations pour cette offre.</p>
        <hr>
    </header>

    <?php 
    // Affichage des messages de succès ou d'erreur
    if (!empty($result)): ?>
        <div class="alert alert-<?php echo $result['success'] ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($result['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="card p-4 shadow-sm">
        <h3>Liste des Candidatures (<?php echo count($applicants); ?>)</h3>
        
        <?php if (empty($applicants)): ?>
            <div class="alert alert-info mt-3 text-center">
                Aucune candidature n'a encore été soumise pour cette offre.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover mt-3">
                    <thead>
                        <tr>
                            <th>Candidat</th>
                            <th>Date Postulation</th>
                            <th>Statut</th>
                            <th>CV / LM</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applicants as $app): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($app['nom_candidat']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($app['email_candidat']); ?></small>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($app['date_candidature'])); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        if ($app['statut'] === 'accepté') echo 'success';
                                        elseif ($app['statut'] === 'refusé') echo 'danger';
                                        else echo 'secondary';
                                    ?> text-capitalize"><?php echo htmlspecialchars($app['statut']); ?></span>
                                </td>
                                <td>
                                    <a href="<?php echo htmlspecialchars($app['cv_chemin']); ?>" 
                                       class="btn btn-sm btn-info" target="_blank" download>
                                        <i class="fas fa-file-pdf"></i> CV
                                    </a>
                                    <button type="button" class="btn btn-sm btn-light border" data-bs-toggle="modal" data-bs-target="#lmModal<?php echo $app['candidature_id']; ?>">
                                        LM
                                    </button>
                                </td>
                                <td>
                                    <form action="process_update_status.php" method="POST" class="d-flex">
                                        <input type="hidden" name="candidature_id" value="<?php echo $app['candidature_id']; ?>">
                                        <input type="hidden" name="offre_id" value="<?php echo $offre_id; ?>">
                                        
                                        <select name="new_status" class="form-select form-select-sm me-2">
                                            <option value="examiné" <?php echo ($app['statut'] == 'examiné') ? 'selected' : ''; ?>>Examiné</option>
                                            <option value="accepté" <?php echo ($app['statut'] == 'accepté') ? 'selected' : ''; ?>>Accepter</option>
                                            <option value="refusé" <?php echo ($app['statut'] == 'refusé') ? 'selected' : ''; ?>>Refuser</option>
                                            <option value="en attente" <?php echo ($app['statut'] == 'en attente') ? 'selected' : ''; ?>>En attente</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary">Mettre à jour</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php foreach ($applicants as $app): ?>
<div class="modal fade" id="lmModal<?php echo $app['candidature_id']; ?>" tabindex="-1" aria-labelledby="lmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="lmModalLabel">Lettre de Motivation de **<?php echo htmlspecialchars($app['nom_candidat']); ?>**</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (!empty($app['lettre_motivation'])): ?>
                    <p class="text-muted small">Soumise le <?php echo date('d/m/Y à H:i', strtotime($app['date_candidature'])); ?></p>
                    <hr>
                    <p style="white-space: pre-wrap;"><?php echo htmlspecialchars($app['lettre_motivation']); ?></p>
                <?php else: ?>
                    <p class="text-danger">Le candidat n'a pas fourni de lettre de motivation en texte.</p>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>