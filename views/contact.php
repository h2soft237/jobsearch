<?php
// views/contact.php
// Assurez-vous que les messages de session sont inclus si vous en avez besoin
$result = [];
if (isset($_SESSION['contact_result'])) {
    $result = $_SESSION['contact_result'];
    unset($_SESSION['contact_result']);
}

// Définir l'email de contact à partir des paramètres globaux
$contact_email = $GLOBALS['settings']['contact_email'] ?? 'contact@votreplateforme.com';

// Pré-remplir les champs si l'utilisateur est connecté
$user_name = is_logged_in() ? htmlspecialchars($_SESSION['user_name'] ?? '') : '';
$user_email = is_logged_in() ? htmlspecialchars($_SESSION['user_email'] ?? $contact_email) : '';
//die($user_name);
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <header class="mb-4 text-center">
                <h1 class="display-5"><i class="fas fa-headset text-primary"></i> Contactez-nous</h1>
                <p class="lead">Envoyez-nous un message et nous vous répondrons dans les plus brefs délais.</p>
            </header>

            <?php if (!empty($result)): ?>
                <div class="alert alert-<?php echo $result['success'] ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($result['message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-lg">
                <div class="card-body p-4 p-md-5">
                    
                    <p class="text-muted text-center mb-4">
                        Vous pouvez également nous écrire directement à : 
                        <a href="mailto:<?php echo $contact_email; ?>"><?php echo $contact_email; ?></a>
                    </p>

                    <form action="process_contact.php" method="POST">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Votre Nom Complet</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="name" 
                                   name="name" 
                                   value="<?php echo $user_name;?>" 
                                   required <?php echo is_logged_in() ? 'readonly' : ''; ?>>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Votre Adresse Email</label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="<?php echo $user_email; ?>" 
                                   required <?php echo is_logged_in() ? 'readonly' : ''; ?>>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Sujet</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane"></i> Envoyer le Message
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>