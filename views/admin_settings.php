<?php
// views/admin_settings.php - Gestion des Paramètres du Système

global $pdo; 

// 1. VÉRIFICATION DE SÉCURITÉ
if (!is_role('admin')) {
    redirect_to('dashboard'); 
}

// 2. Récupération des données
$settings = get_all_settings($pdo);

// Récupération des messages de session (pour les actions d'UPDATE)
$result = [];
if (isset($_SESSION['admin_settings_result'])) {
    $result = $_SESSION['admin_settings_result'];
    unset($_SESSION['admin_settings_result']); 
}
?>

<div class="container mt-5">
    <header class="mb-4">
        <h1 class="display-5"><i class="fas fa-sliders-h text-primary"></i> Gestion des Paramètres</h1>
        <p class="lead">Configurez les variables et le comportement global de la plateforme.</p>
        <hr>
    </header>

    <?php 
    if (!empty($result)): ?>
        <div class="alert alert-<?php echo $result['success'] ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($result['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <a href="index.php?page=dashboard_admin" class="btn btn-sm btn-outline-secondary mb-4">
        <i class="fas fa-arrow-left"></i> Retour au Tableau de Bord
    </a>

    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            Configuration Générale du Site
        </div>
        <div class="card-body">
            <form action="process_admin_action.php" method="POST">
                <input type="hidden" name="action" value="update_settings">
                
                <?php foreach ($settings as $key => $setting): ?>
                    <div class="mb-3 row">
                        <label for="<?php echo htmlspecialchars($key); ?>" class="col-sm-4 col-form-label text-md-end">
                            <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $key))); ?>
                        </label>
                        <div class="col-sm-8">
                            <?php if ($key === 'maintenance_mode'): ?>
                                <select class="form-select" id="<?php echo htmlspecialchars($key); ?>" name="settings[<?php echo htmlspecialchars($key); ?>]">
                                    <option value="0" <?php echo ($setting['value'] == 0) ? 'selected' : ''; ?>>Désactivé (Site En Ligne)</option>
                                    <option value="1" <?php echo ($setting['value'] == 1) ? 'selected' : ''; ?>>Activé (Mode Maintenance)</option>
                                </select>
                            <?php else: ?>
                                <input type="text" 
                                       class="form-control" 
                                       id="<?php echo htmlspecialchars($key); ?>" 
                                       name="settings[<?php echo htmlspecialchars($key); ?>]" 
                                       value="<?php echo htmlspecialchars($setting['value']); ?>" 
                                       required>
                            <?php endif; ?>
                            <div class="form-text"><?php echo htmlspecialchars($setting['description']); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Enregistrer Tous les Paramètres
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>