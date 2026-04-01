<?php
// clean_orphaned_files.php

// 1. Inclure les fichiers nécessaires
// Pas besoin de démarrer la session ici car c'est un script de maintenance
require_once 'includes/db.php'; 
require_once 'includes/functions.php';

// Définition du chemin du répertoire de téléchargement (à ajuster si nécessaire)
$upload_dir = 'uploads/cv/';
$log_file = 'maintenance_log.txt'; // Fichier pour enregistrer les actions

/**
 * Log une action de maintenance.
 * @param string $message Le message à enregistrer.
 */
function log_maintenance($message) {
    global $log_file;
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
    echo "[$timestamp] $message\n"; // Afficher également dans la console/terminal
}

log_maintenance("--- DÉMARRAGE DU NETTOYAGE DES FICHIERS ORPHELINS ---");

try {
    $pdo = connect_db();

    // ----------------------------------------------------
    // 2. RÉCUPÉRER TOUS LES CHEMINS DE CV VALIDES DE LA BASE DE DONNÉES
    // ----------------------------------------------------
    
    // Récupérer le chemin de tous les fichiers CV qui devraient exister
    $sql = "SELECT cv_chemin FROM candidatures";
    $stmt = $pdo->query($sql);
    
    // Convertir les résultats en un tableau associatif (chemin => true) pour une recherche rapide
    $valid_paths = [];
    while ($row = $stmt->fetch(PDO::FETCH_COLUMN)) {
        // Normaliser le chemin pour s'assurer qu'il correspond au chemin du système de fichiers
        $valid_paths[realpath($row)] = true;
    }
    
    log_maintenance("Nombre de chemins de CV enregistrés en base de données : " . count($valid_paths));


    // ----------------------------------------------------
    // 3. PARCOURIR LE RÉPERTOIRE D'UPLOAD ET VÉRIFIER CHAQUE FICHIER
    // ----------------------------------------------------
    
    $files_deleted_count = 0;
    $files_scanned_count = 0;

    // Utilisation de glob pour lister tous les fichiers
    $files = glob($upload_dir . '*'); 

    foreach ($files as $file_path) {
        $files_scanned_count++;
        
        // S'assurer que ce n'est pas un dossier
        if (is_dir($file_path)) {
            continue;
        }

        // 4. Vérifier si le chemin du fichier existe dans notre liste de chemins valides
        $real_file_path = realpath($file_path);
        
        if (!isset($valid_paths[$real_file_path])) {
            // Le fichier existe sur le disque, mais pas dans la table candidatures : C'EST UN ORPHELIN !
            
            if (unlink($real_file_path)) {
                $files_deleted_count++;
                log_maintenance("SUPPRIMÉ : Orphelin trouvé et supprimé : " . $file_path);
            } else {
                log_maintenance("ERREUR : Impossible de supprimer le fichier orphelin : " . $file_path);
            }
        } else {
            // log_maintenance("CONSERVÉ : Fichier valide trouvé : " . $file_path);
        }
    }

    log_maintenance("--- RAPPORT DE NETTOYAGE ---");
    log_maintenance("Fichiers scannés : $files_scanned_count");
    log_maintenance("Fichiers orphelins supprimés : $files_deleted_count");
    log_maintenance("--- FIN DU NETTOYAGE ---");

} catch (Exception $e) {
    log_maintenance("ERREUR CRITIQUE : Script interrompu. " . $e->getMessage());
}

// 5. Quitter le script
exit(0); 
?>