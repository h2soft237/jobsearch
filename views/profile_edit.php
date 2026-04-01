<?php
// views/profile_edit.php

// Connexion à la base de données
global $pdo; 

// ----------------------------------------------------
// 1. VÉRIFICATION DE SÉCURITÉ
// ----------------------------------------------------
if (!is_logged_in()) {
    redirect_to('login'); 
}

$user_id = $_SESSION['user_id'];

// Récupérer les données actuelles de l'utilisateur
$profile_data = get_user_profile($pdo, $user_id);

//****************** */
// Récupérer le chemin actuel de la photo (vous devez ajouter photo_profil_chemin 
// à la requête de get_user_profile si ce n'est pas fait)
$current_photo_path = $profile_data['photo_profil_chemin'] ?? 'assets/img/default_profile.png';
$display_data['photo_profil_chemin'] = $current_photo_path;

// Messages spécifiques à l'upload de photo
$photo_result = [];
if (isset($_SESSION['photo_upload_result'])) {
    $photo_result = $_SESSION['photo_upload_result'];
    unset($_SESSION['photo_upload_result']); 
}
//****************** */

if (!$profile_data) {
    // Si l'utilisateur n'est pas trouvé (cas peu probable si is_logged_in est vrai)
    redirect_to('logout');
}

// Récupération des messages de session (pour les deux formulaires)
$info_result = [];
if (isset($_SESSION['profile_info_result'])) {
    $info_result = $_SESSION['profile_info_result'];
    unset($_SESSION['profile_info_result']); 
}

$password_result = [];
if (isset($_SESSION['password_change_result'])) {
    $password_result = $_SESSION['password_change_result'];
    unset($_SESSION['password_change_result']); 
}

// Les données postées sont utilisées pour pré-remplir en cas d'erreur
$display_data = $_SESSION['post_data'] ?? $profile_data;
unset($_SESSION['post_data']); 
?>

<div class="container mt-5">
    <header class="mb-4">
        <h1 class="display-5">Gestion de Mon Profil</h1>
        <p class="lead">Mettez à jour vos informations et votre mot de passe.</p>
        <hr>
    </header>

    <div class="row">
        
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <i class="fas fa-image"></i> Photo de Profil
                </div>
                <div class="card-body text-center">
                    
                    <img src="<?php echo htmlspecialchars($current_photo_path); ?>" 
                        alt="Photo de profil" 
                        class="rounded-circle mb-3" 
                        style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #ccc;">

                    <?php if (!empty($photo_result)): ?>
                        <div class="alert alert-<?php echo $photo_result['success'] ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($photo_result['message']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="process_profile_update.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="upload_photo">
                        
                        <div class="input-group mb-3">
                            <input type="file" class="form-control" id="photo_file" name="photo_file" accept="image/jpeg,image/png,image/gif" required>
                            <button class="btn btn-secondary" type="submit">Uploader</button>
                        </div>
                        <div class="form-text">JPG, PNG ou GIF. Max 2 Mo.</div>
                    </form>
                    
                    <?php if ($current_photo_path !== 'assets/img/default_profile.png'): ?>
                    <form action="process_profile_update.php" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer votre photo de profil actuelle ?')">
                        <input type="hidden" name="action" value="delete_photo">
                        <input type="hidden" name="old_path" value="<?php echo htmlspecialchars($current_photo_path); ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger mt-2">Supprimer la photo</button>
                    </form>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-user-edit"></i> Modifier mes Informations
                </div>
                <div class="card-body">
                    <?php 
                    // Affichage des messages de succès ou d'erreur pour les infos
                    if (!empty($info_result)): ?>
                        <div class="alert alert-<?php echo $info_result['success'] ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($info_result['message']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="process_profile_update.php" method="POST">
                        <input type="hidden" name="action" value="update_info">
                        
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom Complet</label>
                            <input type="text" class="form-control" id="nom" name="nom" required 
                                value="<?php echo htmlspecialchars($display_data['nom'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse Email</label>
                            <input type="email" class="form-control" id="email" name="email" required 
                                value="<?php echo htmlspecialchars($display_data['email'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Rôle</label>
                            <p class="form-control-static text-capitalize fw-bold"><?php echo htmlspecialchars($profile_data['role'] ?? ''); ?></p>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Enregistrer les Infos</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-lock"></i> Changer le Mot de Passe
                </div>
                <div class="card-body">
                    <?php 
                    // Affichage des messages de succès ou d'erreur pour le mot de passe
                    if (!empty($password_result)): ?>
                        <div class="alert alert-<?php echo $password_result['success'] ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($password_result['message']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="process_profile_update.php" method="POST">
                        <input type="hidden" name="action" value="update_password">
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mot de passe Actuel</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        
                        <hr>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nouveau Mot de Passe</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmer Nouveau Mot de Passe</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-danger w-100">Changer le Mot de Passe</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <div class="text-center mt-3">
        <a href="index.php?page=dashboard" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour au Tableau de Bord
        </a>
    </div>
</div>