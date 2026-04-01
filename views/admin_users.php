<?php
// views/admin_users.php - Gestion des Utilisateurs

global $pdo; 

// 1. VÉRIFICATION DE SÉCURITÉ
if (!is_role('admin')) {
    redirect_to('dashboard'); 
}

// 2. Récupération des données
$users = get_all_users($pdo);

// Récupération des messages de session
$result = [];
if (isset($_SESSION['admin_user_result'])) {
    $result = $_SESSION['admin_user_result'];
    unset($_SESSION['admin_user_result']); 
}
?>

<div class="container mt-5">
    <header class="mb-4">
        <h1 class="display-5"><i class="fas fa-users-cog text-danger"></i> Gestion des Utilisateurs</h1>
        <p class="lead">Visualisez, modifiez et supprimez les comptes utilisateurs de la plateforme.</p>
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
                            <th>Nom / Email</th>
                            <th>Rôle</th>
                            <th>Offres / Cands.</th>
                            <th>Inscrit le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($user['nom']); ?></strong><br>
                                <small class="text-muted"><?php echo htmlspecialchars($user['email']); ?></small>
                            </td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo ($user['role'] === 'admin') ? 'danger' : ''; 
                                    echo ($user['role'] === 'employeur') ? 'warning' : ''; 
                                    echo ($user['role'] === 'candidat') ? 'info' : ''; 
                                ?> text-capitalize"><?php echo $user['role']; ?></span>
                            </td>
                            <td>
                                <?php echo $user['total_offres']; ?> Offres<br>
                                <?php echo $user['total_candidatures']; ?> Cands.
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($user['date_inscription'])); ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary me-2" 
                                        data-bs-toggle="modal" data-bs-target="#editUserModal"
                                        data-user-id="<?php echo $user['id']; ?>"
                                        data-user-nom="<?php echo htmlspecialchars($user['nom']); ?>"
                                        data-user-email="<?php echo htmlspecialchars($user['email']); ?>"
                                        data-user-role="<?php echo $user['role']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <a href="process_admin_action.php?action=delete_user&id=<?php echo $user['id']; ?>"
                                   class="btn btn-sm btn-danger 
                                       <?php echo ($user['id'] == ($_SESSION['user_id'] ?? 0)) ? 'disabled' : ''; ?>"
                                   onclick="return confirm('ATTENTION : Êtes-vous sûr de vouloir supprimer <?php echo htmlspecialchars($user['nom']); ?> ? Cette action est IRREVERSIBLE et supprime toutes les données associées (offres, candidatures).')">
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

<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="process_admin_action.php" method="POST">
                <input type="hidden" name="action" value="update_user">
                <input type="hidden" name="user_id" id="modal-user-id">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editUserModalLabel">Modifier l'Utilisateur: <span id="modal-user-nom"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                    <div class="mb-3">
                        <label for="modal-user-email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="modal-user-email" name="new_email" required>
                    </div>

                    <div class="mb-3">
                        <label for="modal-user-role" class="form-label">Rôle</label>
                        <select class="form-select" id="modal-user-role" name="new_role" required>
                            <option value="candidat">Candidat</option>
                            <option value="employeur">Employeur</option>
                            <option value="admin">Administrateur</option>
                        </select>
                    </div>

                    <div class="alert alert-warning small mt-3" role="alert">
                        **Attention :** La modification du rôle ou de l'email ne change pas le mot de passe.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les Changements</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var editUserModal = document.getElementById('editUserModal');
    editUserModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        
        var userId = button.getAttribute('data-user-id');
        var userNom = button.getAttribute('data-user-nom');
        var userEmail = button.getAttribute('data-user-email');
        var userRole = button.getAttribute('data-user-role');
        
        var modalTitle = editUserModal.querySelector('#modal-user-nom');
        var modalIdInput = editUserModal.querySelector('#modal-user-id');
        var modalEmailInput = editUserModal.querySelector('#modal-user-email');
        var modalRoleSelect = editUserModal.querySelector('#modal-user-role');

        modalTitle.textContent = userNom;
        modalIdInput.value = userId;
        modalEmailInput.value = userEmail;
        modalRoleSelect.value = userRole;
    });
});
</script>