<?php
// views/login.php

// Redirection si l'utilisateur est déjà connecté
if (is_logged_in()) {
    // Rediriger vers la page d'accueil ou le tableau de bord
    redirect_to('dashboard'); 
}

// Initialisation du tableau pour stocker les messages de retour
$result = [];

// Vérifier si un message de retour est disponible dans la session (venant de l'inscription ou du traitement de connexion)
if (isset($_SESSION['login_result'])) {
    $result = $_SESSION['login_result'];
    // Nettoyer la session après affichage
    unset($_SESSION['login_result']); 
}

// Récupérer l'email si l'utilisateur a échoué la connexion pour lui éviter de le retaper
$email_value = htmlspecialchars($_SESSION['post_data']['email'] ?? '');
// Nettoyer les données postées
unset($_SESSION['post_data']); 
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white text-center">
                    <h2>Connexion</h2>
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

                    <form action="process_login.php" method="POST">
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse Email</label>
                            <input type="email" class="form-control" id="email" name="email" required 
                                value="<?php echo $email_value; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="mot_de_passe" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Se connecter</button>
                    </form>

                    <div class="text-center mt-3">
                        Pas encore de compte ? <a href="index.php?page=register">Créez-en un ici</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>