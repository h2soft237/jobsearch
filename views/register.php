<?php
// views/register.php

// Si l'utilisateur est déjà connecté, le rediriger vers son tableau de bord
if (is_logged_in()) {
    redirect_to('dashboard');
}

// Initialisation du tableau pour stocker les messages de retour
$result = [];

// Vérifier si le traitement de l'inscription a renvoyé un résultat
if (isset($_SESSION['register_result'])) {
    $result = $_SESSION['register_result'];
    // Nettoyer la session après affichage pour éviter la réaffichage au rafraîchissement
    unset($_SESSION['register_result']); 
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h2>Inscription</h2>
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

                    <form action="process_register.php" method="POST">
                        
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom complet</label>
                            <input type="text" class="form-control" id="nom" name="nom" required 
                                value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse Email</label>
                            <input type="email" class="form-control" id="email" name="email" required 
                                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="mot_de_passe" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="password_confirm" name="mot_de_passe_confirm" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Je suis :</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role" id="roleCandidat" value="candidat" checked>
                                <label class="form-check-label" for="roleCandidat">
                                    Candidat (Je cherche un emploi)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role" id="roleEmployeur" value="employeur">
                                <label class="form-check-label" for="roleEmployeur">
                                    Employeur (Je publie des offres)
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
                    </form>

                    <div class="text-center mt-3">
                        Déjà un compte ? <a href="index.php?page=login">Connectez-vous ici</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>