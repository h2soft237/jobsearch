<?php
// process_apply.php

session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// ----------------------------------------------------
// 1. VÉRIFICATION D'ACCÈS
// ----------------------------------------------------
if (!is_role('candidat') || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('home'); 
}

$pdo = connect_db();

// 2. Préparation des variables
$candidat_id       = $_SESSION['user_id'];
$offre_id          = (int)($_POST['offre_id'] ?? 0);
$lettre_motivation = trim($_POST['lettre_motivation'] ?? '');
$cv_file           = $_FILES['cv_file'] ?? null;
$upload_dir        = 'uploads/cv/'; 

$errors = [];

// 3. Validation de base
if ($offre_id === 0) {
    $errors[] = 'ID de l\'offre manquant ou invalide.';
}
if ($cv_file === null || $cv_file['error'] !== UPLOAD_ERR_OK) {
    $errors[] = 'Veuillez télécharger un fichier CV valide.';
}


// 4. Validation spécifique du fichier CV
if ($cv_file && $cv_file['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    $max_size = 5 * 1024 * 1024; // 5 Mo
    
    // Vérification du type MIME
    if (!in_array($cv_file['type'], $allowed_types)) {
        $errors[] = 'Le format de fichier de votre CV doit être PDF, DOC ou DOCX.';
    }
    
    // Vérification de la taille
    if ($cv_file['size'] > $max_size) {
        $errors[] = 'La taille de votre CV est trop grande (max. 5 Mo).';
    }
}


// 5. Traitement ou retour avec les erreurs
if (!empty($errors)) {
    $_SESSION['apply_result'] = [
        'success' => false,
        'message' => implode('<br>', $errors)
    ];
    $_SESSION['post_data'] = ['lettre_motivation' => $lettre_motivation];
    
    // Rediriger vers le formulaire en conservant l'ID de l'offre
    redirect_to('apply&job_id=' . $offre_id); 
} else {
    
    // 6. UPLOAD SÉCURISÉ du fichier CV
    // Créer le dossier d'upload s'il n'existe pas
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Générer un nom de fichier unique et sécurisé
    $file_extension = pathinfo($cv_file['name'], PATHINFO_EXTENSION);
    $safe_filename = uniqid('cv_') . '_' . $candidat_id . '.' . $file_extension;
    $destination = $upload_dir . $safe_filename;

    if (move_uploaded_file($cv_file['tmp_name'], $destination)) {
        
        // 7. ENREGISTREMENT DB de la candidature
        $cv_chemin = $destination; // Chemin relatif à la racine du projet
        
        $sql = "INSERT INTO candidatures (candidat_id, offre_id, cv_chemin, lettre_motivation) 
                VALUES (?, ?, ?, ?)";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $candidat_id, 
                $offre_id, 
                $cv_chemin, 
                $lettre_motivation
            ]);

            // -----------------------------------------------------------------
            // 8. NOTIFICATION PAR EMAIL (NOUVELLE ÉTAPE)
            // -----------------------------------------------------------------
            
            // a. Récupérer les données nécessaires à la notification (Titre Offre, Email Employeur, Nom Candidat)
            
            // Récupérer le nom du candidat (déjà dans la session)
            $candidat_nom = $_SESSION['user_name'] ?? 'Candidat Inconnu'; 
            
            // Récupérer le titre de l'offre et l'email de l'employeur
            $sql_notify = "SELECT o.titre, u.email AS employeur_email 
                           FROM offres_emploi o 
                           JOIN utilisateurs u ON o.employeur_id = u.id 
                           WHERE o.id = ?";
            
            $stmt_notify = $pdo->prepare($sql_notify);
            $stmt_notify->execute([$offre_id]);
            $notification_data = $stmt_notify->fetch(PDO::FETCH_ASSOC);

            if ($notification_data) {
                send_new_application_notification(
                    $notification_data['employeur_email'], 
                    $notification_data['titre'], 
                    $candidat_nom
                );
            }
            
            $_SESSION['apply_result'] = [
                'success' => true, 
                'message' => 'Félicitations ! Votre candidature a été soumise avec succès.'
            ];
            // END NOTIFICATION
            

            
            // Rediriger vers la page de suivi ou le tableau de bord
            redirect_to('dashboard'); 
            
        } catch (PDOException $e) {
            // Loguer l'erreur DB
            error_log("DB Error in application submission: " . $e->getMessage()); 
            
            // Supprimer le fichier uploadé en cas d'échec de l'insertion DB
            if (file_exists($destination)) {
                unlink($destination);
            }
            
            $_SESSION['apply_result'] = [
                'success' => false, 
                'message' => 'Une erreur est survenue lors de l\'enregistrement de votre candidature.'
            ];
            redirect_to('apply&job_id=' . $offre_id);
        }

    } else {
        // Erreur lors du déplacement du fichier
        $_SESSION['apply_result'] = [
            'success' => false, 
            'message' => 'Erreur lors du téléchargement du fichier CV sur le serveur.'
        ];
        redirect_to('apply&job_id=' . $offre_id);
    }
}
?>