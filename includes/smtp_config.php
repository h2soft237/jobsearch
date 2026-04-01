<?php
// includes/smtp_config.php

// Paramètres SMTP (à remplacer par ceux de votre hébergeur ou service d'email)
define('SMTP_HOST', 'sandbox.smtp.mailtrap.io'); // Ex: smtp.gmail.com
define('SMTP_USERNAME', '9ed83c9bdb8583');
define('SMTP_PASSWORD', '413b4caa9437b8');
define('SMTP_PORT', 2525); // ou 465 pour SSL
define('SMTP_SECURE', 'tls'); // ou 'ssl'
define('EMAIL_FROM', 'admin@gmail.com');
define('EMAIL_FROM_NAME', 'Job Search Pro');
?>
