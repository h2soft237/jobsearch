<?php
// views/alerts.php - Gestion des Alertes d'Emploi

global $pdo;

// 1. VÉRIFICATION DE SÉCURITÉ
if (!is_role('candidat')) {
    $_SESSION['error_message'] = 'Seuls les candidats peuvent gérer les alertes d\'emploi.';
    redirect_to('dashboard');
}

$user_id = $_SESSION['user_id'];
$alerts = get_user_alerts($pdo, $user_id);

// Récupération des messages de session
$result = [];
if (isset($_SESSION['alert_result'])) {
    $result = $_SESSION['alert_result'];
    unset($_SESSION['alert_result']);
}
?>

<div class="container mt-5">
    <header class="mb-4">
        <h1 class="display-5"><i class="fas fa-bell text-warning"></i> Mes Alertes d'Emploi</h1>
        <p class="lead">Créez et gérez vos critères de recherche pour recevoir de nouvelles offres par email.</p>
        <hr>
    </header>

    <?php if (!empty($result)): ?>
        <div class="alert alert-<?php echo $result['success'] ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($result['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    Créer une Nouvelle Alerte
                </div>
                <div class="card-body">
                    <form action="process_alert_action.php" method="POST">
                        <input type="hidden" name="action" value="create_alert">
                        
                        <div class="mb-3">
                            <label for="mots_cles" class="form-label">Mots-clés (obligatoire)</label>
                            <input type="text" class="form-control" id="mots_cles" name="mots_cles" placeholder="Ex: Développeur PHP, Marketing" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="lieu" class="form-label">Lieu (Optionnel)</label>
                            <input type="text" class="form-control" id="lieu" name="lieu" placeholder="Ex: Paris, Télétravail">
                        </div>
                        
                        <div class="mb-3">
                            <label for="type_contrat" class="form-label">Type de Contrat (Optionnel)</label>
                            <select class="form-select" id="type_contrat" name="type_contrat">
                                <option value="">Tous types</option>
                                <option value="CDI">CDI</option>
                                <option value="CDD">CDD</option>
                                <option value="Intérim">Intérim</option>
                                <option value="Stage">Stage</option>
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-plus-circle"></i> Enregistrer l'Alerte
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header bg-light">
                    Alertes Actives (<?php echo count($alerts); ?>)
                </div>
                <div class="list-group list-group-flush">
                    <?php if (empty($alerts)): ?>
                        <div class="p-4 text-center text-muted">Vous n'avez aucune alerte active pour le moment.</div>
                    <?php else: ?>
                        <?php foreach ($alerts as $alert): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="alert-criteria">
                                    <h6 class="mb-1 text-primary"><?php echo htmlspecialchars($alert['mots_cles']); ?></h6>
                                    <small class="text-muted">
                                        Lieu: **<?php echo htmlspecialchars($alert['lieu'] ?: 'Partout'); ?>** | Contrat: **<?php echo htmlspecialchars($alert['type_contrat'] ?: 'Tous'); ?>**
                                        (Créée le: <?php echo date('d/m/Y', strtotime($alert['date_creation'])); ?>)
                                    </small>
                                </div>
                                <div class="alert-actions">
                                    <button type="button" class="btn btn-sm btn-outline-info me-2" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $alert['id']; ?>" title="Modifier l'alerte">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <a href="process_alert_action.php?action=delete_alert&id=<?php echo $alert['id']; ?>"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Voulez-vous vraiment supprimer cette alerte ?')"
                                    title="Supprimer l'alerte">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="modal fade" id="editModal<?php echo $alert['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $alert['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                    <form action="process_alert_action.php" method="POST">
                                        <input type="hidden" name="action" value="update_alert">
                                        <input type="hidden" name="alert_id" value="<?php echo $alert['id']; ?>">
                                        
                                        <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel<?php echo $alert['id']; ?>">Modifier l'Alerte : <?php echo htmlspecialchars($alert['mots_cles']); ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="mots_cles" class="form-label">Mots-clés (obligatoire)</label>
                                            <input type="text" class="form-control" name="mots_cles" value="<?php echo htmlspecialchars($alert['mots_cles']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="lieu" class="form-label">Lieu (Optionnel)</label>
                                            <input type="text" class="form-control" name="lieu" value="<?php echo htmlspecialchars($alert['lieu']); ?>" placeholder="Ex: Paris, Télétravail">
                                        </div>
                                        <div class="mb-3">
                                            <label for="type_contrat" class="form-label">Type de Contrat (Optionnel)</label>
                                            <select class="form-select" name="type_contrat">
                                                <option value="">Tous types</option>
                                                <?php $selected_type = htmlspecialchars($alert['type_contrat']); ?>
                                                <option value="CDI" <?php echo $selected_type === 'CDI' ? 'selected' : ''; ?>>CDI</option>
                                                <option value="CDD" <?php echo $selected_type === 'CDD' ? 'selected' : ''; ?>>CDD</option>
                                                <option value="Intérim" <?php echo $selected_type === 'Intérim' ? 'selected' : ''; ?>>Intérim</option>
                                                <option value="Stage" <?php echo $selected_type === 'Stage' ? 'selected' : ''; ?>>Stage</option>
                                            </select>
                                        </div>
                                        </div>
                                        <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-info">Sauvegarder les Modifications</button>
                                        </div>
                                    </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>