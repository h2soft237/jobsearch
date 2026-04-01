<?php
// views/company_profile.php - Gestion du Profil Employeur

global $pdo;

// 1. VÉRIFICATION DE SÉCURITÉ
if (!is_role('employeur')) {
    redirect_to('dashboard');
}

$employeur_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name']; // Nom de l'entreprise (tiré de la table utilisateurs)

// 2. Récupération des données du profil détaillé
$profile_data = get_company_profile($pdo, $employeur_id);

if ($profile_data === null) {
    // Gestion d'une erreur critique de DB
    $_SESSION['error_message'] = "Erreur critique : Impossible de charger le profil d'entreprise.";
    redirect_to('dashboard_employeur');
}

// Récupération des messages de session
$result = [];
if (isset($_SESSION['profile_result'])) {
    $result = $_SESSION['profile_result'];
    unset($_SESSION['profile_result']);
}
?>

<div class="container mt-5">
    <header class="mb-4">
        <h1 class="display-5"><i class="fas fa-building text-primary"></i> Profil de l'Entreprise</h1>
        <p class="lead">Gérez les informations publiques de **<?php echo htmlspecialchars($user_name); ?>**.</p>
        <hr>
    </header>

    <?php if (!empty($result)): ?>
        <div class="alert alert-<?php echo $result['success'] ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($result['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <a href="index.php?page=dashboard_employeur" class="btn btn-sm btn-outline-secondary mb-4">
        <i class="fas fa-arrow-left"></i> Retour au Tableau de Bord
    </a>

    <div class="card shadow-lg">
        <div class="card-body p-4 p-md-5">
            <form action="process_company_profile.php" method="POST">
                
                <h2 class="h5 mb-4 text-primary">Informations de Contact et Générales</h2>

                <div class="mb-3">
                    <label for="name" class="form-label">Nom de l'Entreprise</label>
                    <input type="text" class="form-control" id="name" value="<?php echo htmlspecialchars($user_name); ?>" disabled>
                    <div class="form-text">Ce champ est le nom de votre compte, contactez l'administration pour le modifier.</div>
                </div>

                <div class="mb-3">
                    <label for="secteur_activite" class="form-label">Secteur d'Activité</label>
                    <input type="text" class="form-control" id="secteur_activite" name="secteur_activite" 
                           value="<?php echo htmlspecialchars($profile_data['secteur_activite']); ?>" 
                           placeholder="Ex: Technologies, Finance, Bâtiment">
                </div>

                <div class="mb-3">
                    <label for="site_web" class="form-label">Site Web</label>
                    <input type="url" class="form-control" id="site_web" name="site_web" 
                           value="<?php echo htmlspecialchars($profile_data['site_web']); ?>" 
                           placeholder="Ex: https://www.monentreprise.com">
                </div>
                
                <h2 class="h5 mb-4 mt-5 text-primary">Description et Adresse</h2>

                <div class="mb-3">
                    <label for="description" class="form-label">Description de l'Entreprise (Visible publiquement)</label>
                    <textarea class="form-control" id="description" name="description" rows="5" 
                              placeholder="Décrivez votre entreprise, sa mission, et sa culture."><?php echo htmlspecialchars($profile_data['description']); ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="adresse_siege" class="form-label">Adresse du Siège Social</label>
                    <input type="text" class="form-control" id="adresse_siege" name="adresse_siege" 
                           value="<?php echo htmlspecialchars($profile_data['adresse_siege']); ?>" 
                           placeholder="Adresse complète du siège">
                </div>

                <div class="mb-5">
                    <label for="telephone" class="form-label">Téléphone de Contact</label>
                    <input type="tel" class="form-control" id="telephone" name="telephone" 
                           value="<?php echo htmlspecialchars($profile_data['telephone']); ?>" 
                           placeholder="Ex: +33 1 23 45 67 89">
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Enregistrer le Profil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>