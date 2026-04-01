<?php
// includes/db.php

// ----------------------------------------------------
// 1. PARAMÈTRES DE CONNEXION
// ----------------------------------------------------
define('DB_HOST', 'localhost'); // L'hôte de votre base de données (souvent localhost)
define('DB_NAME', 'job_board_db'); // Le nom de votre base de données
define('DB_USER', 'root'); // Votre nom d'utilisateur de base de données
define('DB_PASS', 'h2soft'); // Votre mot de passe de base de données (laissez vide si pas de mot de passe)

// ----------------------------------------------------
// 2. FONCTION DE CONNEXION (PDO)
// ----------------------------------------------------

/**
 * Établit et retourne une connexion PDO à la base de données.
 * @return PDO|null Retourne l'objet PDO ou null en cas d'erreur.
 */
function connect_db() {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $options = [
        // Mode d'erreur : Lancer des exceptions en cas d'erreur (recommandé)
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        // Mode de récupération par défaut : Retourner les résultats comme des tableaux associatifs
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // Désactiver l'émulation des requêtes préparées pour la sécurité
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        // Tentative d'établir la connexion
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // En cas d'échec de la connexion
        // ATTENTION : En production, ne jamais afficher l'erreur directement pour la sécurité.
        // On pourrait enregistrer l'erreur dans un fichier journal.
        echo "Erreur de connexion à la base de données: " . $e->getMessage();
        // Arrêter l'exécution du script en cas d'échec critique
        exit();
    }
}

// Exemple d'utilisation (peut être commenté ou supprimé si vous le souhaitez)
// $pdo = connect_db();
// if ($pdo) {
//     echo "Connexion à la base de données réussie !";
// }
?>