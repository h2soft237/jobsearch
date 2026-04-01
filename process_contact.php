<?php
// process_contact.php

session_start();
// Charger les fonctions (qui incluent send_email_via_smtp et load_site_settings)
require_once 'includes/functions.php'; 
require_once 'includes/db.php';

// Initialisation de la connexion et des paramètres pour utiliser l'email de contact
$pdo = connect_db();
$GLOBALS['settings'] = load_site_settings($pdo);

// Vérifier si c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('contact'); 
}

// 1. Récupération et validation des données
$sender_name = trim($_POST['name'] ?? '');
$sender_email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message_body = trim($_POST['message'] ?? '');

if (empty($sender_name) || empty($sender_email) || empty($subject) || empty($message_body) || !filter_var($sender_email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['contact_result'] = [
        'success' => false, 
        'message' => 'Veuillez remplir tous les champs correctement.'
    ];
    redirect_to('contact');
}

// 2. Définir le destinataire (l'email de contact défini dans les paramètres admin)
$recipient_email = $GLOBALS['settings']['contact_email'] ?? 'contact@votreplateforme.com';

// 3. Préparer le contenu de l'email (vers l'Admin)
$email_subject = "[CONTACT SITE] " . htmlspecialchars($subject);
$html_content = "
<html>
<head><title>Nouveau Message de Contact</title></head>
<body>
    <h2>Nouveau Message de Contact Reçu</h2>
    <p><strong>De :</strong> " . htmlspecialchars($sender_name) . "</p>
    <p><strong>Email :</strong> <a href=\"mailto:" . htmlspecialchars($sender_email) . "\">" . htmlspecialchars($sender_email) . "</a></p>
    <p><strong>Sujet :</strong> " . htmlspecialchars($subject) . "</p>
    <hr>
    <h3>Message :</h3>
    <div style='border: 1px solid #ccc; padding: 15px; background-color: #f9f9f9;'>
        " . nl2br(htmlspecialchars($message_body)) . "
    </div>
    <hr>
    <p>Répondez directement à l'adresse de l'expéditeur.</p>
</body>
</html>
";

// 4. Envoi de l'email via la fonction SMTP (définie dans functions.php)
$success = send_email_via_smtp($recipient_email, $email_subject, $html_content);

if ($success) {
    $_SESSION['contact_result'] = [
        'success' => true, 
        'message' => '✅ Merci ! Votre message a été envoyé avec succès. Nous vous répondrons sous peu.'
    ];
} else {
    // Le message d'erreur est déjà loggé dans send_email_via_smtp
    $_SESSION['contact_result'] = [
        'success' => false, 
        'message' => '❌ Une erreur est survenue lors de l\'envoi du message. Veuillez réessayer ou nous contacter à ' . $recipient_email . '.'
    ];
}

redirect_to('contact');