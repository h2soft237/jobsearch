<?php
// includes/functions.php

// ----------------------------------------------------
// FONCTIONS UTILITAIRES DE SESSION
// ----------------------------------------------------

/**
 * Vérifie si un utilisateur est actuellement connecté.
 * @return bool Vrai si l'utilisateur est connecté, Faux sinon.
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Vérifie si l'utilisateur connecté a un rôle spécifique.
 * @param string $role Le rôle à vérifier ('candidat' ou 'employeur').
 * @return bool Vrai si le rôle correspond, Faux sinon.
 */
function is_role($role) {
    return is_logged_in() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

/**
 * Redirige l'utilisateur vers une page spécifiée.
 * @param string $page Le nom du fichier de vue (ex: 'dashboard').
 */
function redirect_to($page) {
    header('Location: index.php?page=' . $page);
    exit();
}

// ----------------------------------------------------
// FONCTION D'INSCRIPTION (REGISTRATION)
// ----------------------------------------------------

/**
 * Enregistre un nouvel utilisateur dans la base de données.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param string $nom Le nom de l'utilisateur.
 * @param string $email L'email de l'utilisateur (doit être unique).
 * @param string $mot_de_passe Le mot de passe non haché.
 * @param string $role Le rôle de l'utilisateur ('candidat' ou 'employeur').
 * @return array Retourne un tableau avec 'success' (bool) et 'message' (string).
 */
function register_user($pdo, $nom, $email, $mot_de_passe, $role) {
    // 1. Vérification de l'existence de l'email
    $sql_check = "SELECT id FROM utilisateurs WHERE email = ?";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$email]);
    
    if ($stmt_check->rowCount() > 0) {
        return ['success' => false, 'message' => 'Cet email est déjà utilisé.'];
    }

    // 2. Hachage du mot de passe pour la sécurité
    $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // 3. Insertion du nouvel utilisateur
    $sql_insert = "INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)";
    
    try {
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([$nom, $email, $hashed_password, $role]);
        
        return ['success' => true, 'message' => 'Inscription réussie. Vous pouvez maintenant vous connecter.'];
        
    } catch (PDOException $e) {
        // En cas d'erreur de la base de données
        error_log("DB Error in registration: " . $e->getMessage()); 
        return ['success' => false, 'message' => 'Une erreur est survenue lors de l\'enregistrement.'];
    }
}

// ----------------------------------------------------
// FONCTION DE CONNEXION (LOGIN)
// ----------------------------------------------------

/**
 * Tente de connecter un utilisateur.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param string $email L'email de l'utilisateur.
 * @param string $mot_de_passe Le mot de passe fourni.
 * @return array Retourne un tableau avec 'success' (bool) et 'message' (string).
 */
function login_user($pdo, $email, $mot_de_passe) {
    // 1. Récupération de l'utilisateur par email
    $sql = "SELECT id, nom, email, mot_de_passe, role FROM utilisateurs WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        return ['success' => false, 'message' => 'Email ou mot de passe incorrect.'];
    }

    // 2. Vérification du mot de passe haché
    if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
        
        // 3. Connexion réussie : Initialisation de la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nom'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role']; // 'candidat' ou 'employeur'
        
        return ['success' => true, 'message' => 'Connexion réussie!'];
        
    } else {
        return ['success' => false, 'message' => 'Email ou mot de passe incorrect.'];
    }
}

// ----------------------------------------------------
// FONCTION DE DÉCONNEXION (LOGOUT)
// ----------------------------------------------------

/**
 * Déconnecte l'utilisateur en détruisant la session.
 */
function logout_user() {
    // Détruire toutes les variables de session
    $_SESSION = array(); 
    
    // Si la session utilise des cookies, les supprimer également.
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Finalement, détruire la session.
    session_destroy();
    
    // Rediriger vers la page d'accueil ou de connexion
    redirect_to('login');
}

// ----------------------------------------------------
// FONCTION D'OFFRE D'EMPLOI' (POST_JOB)
// ----------------------------------------------------

// includes/functions.php - Ajouter cette fonction

/**
 * Insère une nouvelle offre d'emploi dans la base de données.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param int $employeur_id L'ID de l'employeur qui publie.
 * @param string $titre Le titre de l'offre.
 * @param string $description La description détaillée.
 * @param string $lieu Le lieu de l'emploi.
 * @param string $salaire Le salaire proposé.
 * @param string $type_contrat Le type de contrat (ENUM).
 * @return array Résultat de l'opération.
 */
function post_job($pdo, $employeur_id, $titre, $description, $lieu, $salaire, $type_contrat) {
    $sql = "INSERT INTO offres_emploi (employeur_id, titre, description, lieu, salaire, type_contrat) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $employeur_id, 
            $titre, 
            $description, 
            $lieu, 
            $salaire, 
            $type_contrat
        ]);
        
        return ['success' => true, 'message' => 'L\'offre d\'emploi a été publiée avec succès!'];
        
    } catch (PDOException $e) {
        // En cas d'erreur de la base de données
        error_log("DB Error in job posting: " . $e->getMessage()); 
        return ['success' => false, 'message' => 'Une erreur est survenue lors de la publication de l\'offre.'];
    }
}


// ----------------------------------------------------
// FONCTION D'AFFICHAGE DES JOBS NEW WITH FILTER AND PAGINATION' (GET_ACTIVE_JOB)
// ----------------------------------------------------
// includes/functions.php - REMPLACER l'ancienne fonction get_active_jobs

/**
 * Construit la clause WHERE et les paramètres pour le filtrage des offres.
 * @param string $keyword Mot-clé de recherche.
 * @param string $lieu Filtre par lieu.
 * @param string $type_contrat Filtre par type de contrat.
 * @return array Contenant la clause SQL WHERE et le tableau de paramètres.
 */
function build_job_filter_query($keyword, $lieu, $type_contrat) {
    $where_clause = " WHERE o.est_actif = TRUE";
    $params = [];

    // 1. Filtrage par mot-clé (Titre, Description, Lieu)
    if (!empty($keyword)) {
        $where_clause .= " AND (o.titre LIKE ? OR o.description LIKE ?)";
        $search_term = '%' . $keyword . '%';
        $params[] = $search_term;
        $params[] = $search_term;
    }

    // 2. Filtrage par lieu
    if (!empty($lieu)) {
        $where_clause .= " AND o.lieu LIKE ?";
        $params[] = '%' . $lieu . '%';
    }
    
    // 3. Filtrage par type de contrat
    if (!empty($type_contrat) && $type_contrat !== 'all') {
        $where_clause .= " AND o.type_contrat = ?";
        $params[] = $type_contrat;
    }
    
    return ['where' => $where_clause, 'params' => $params];
}

/**
 * Récupère le nombre total d'offres actives après application des filtres.
 */
function get_active_jobs_count($pdo, $keyword = '', $lieu = '', $type_contrat = '') {
    $filter = build_job_filter_query($keyword, $lieu, $type_contrat);
    
    $sql = "SELECT COUNT(o.id) FROM offres_emploi o " . $filter['where'];
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($filter['params']);
        return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("DB Error in fetching job count: " . $e->getMessage()); 
        return 0;
    }
}

/**
 * Récupère les offres d'emploi actives, avec filtres et pagination.
 */
function get_active_jobs($pdo, $keyword = '', $lieu = '', $type_contrat = '', $limit = 10, $offset = 0) {
    $filter = build_job_filter_query($keyword, $lieu, $type_contrat);
    
    $sql = "SELECT 
                o.id, o.titre, o.description, o.lieu, o.salaire, o.type_contrat, o.date_publication,
                u.nom AS nom_entreprise 
            FROM 
                offres_emploi o
            JOIN 
                utilisateurs u ON o.employeur_id = u.id
            " . $filter['where'] . 
           " ORDER BY o.date_publication DESC
            LIMIT ? OFFSET ?";
    
    $params = $filter['params'];
    $params[] = $limit;
    $params[] = $offset;

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("DB Error in fetching jobs: " . $e->getMessage()); 
        return [];
    }
}

//************************************************************************************ */
// ----------------------------------------------------
// FONCTION D'AFFICHAGE DES JOBS OLD' (GET_ACTIVE_JOB)
// ----------------------------------------------------
// includes/functions.php - Ajouter cette fonction

/**
 * Récupère toutes les offres d'emploi actives, potentiellement filtrées par mot-clé.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param string $keyword Mot-clé de recherche optionnel.
 * @return array Tableau d'offres d'emploi.
 */
function get_active_jobs_old($pdo, $keyword = '') {
    // La requête sélectionne uniquement les offres marquées comme actives
    $sql = "SELECT 
                o.id, 
                o.titre, 
                o.description, 
                o.lieu, 
                o.salaire, 
                o.type_contrat,
                o.date_publication,
                u.nom AS nom_entreprise -- Jointure pour obtenir le nom de l'employeur
            FROM 
                offres_emploi o
            JOIN 
                utilisateurs u ON o.employeur_id = u.id
            WHERE 
                o.est_actif = TRUE ";
    
    $params = [];
    
    // Si un mot-clé est fourni, ajoutez la clause WHERE pour la recherche
    if (!empty($keyword)) {
        $sql .= " AND (o.titre LIKE ? OR o.description LIKE ? OR o.lieu LIKE ?)";
        $search_term = '%' . $keyword . '%';
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
    }
    
    // Trier par date de publication la plus récente
    $sql .= " ORDER BY o.date_publication DESC";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        // Gérer l'erreur (à loguer en production)
        error_log("DB Error in fetching jobs: " . $e->getMessage()); 
        return []; // Retourner un tableau vide en cas d'erreur
    }
}
//*********************************************************************************** */

// ----------------------------------------------------
// FONCTION D'AFFICHAGE DES DETAILS DU JOB' (GET_JOB_DETAILS)
// ----------------------------------------------------
// includes/functions.php - Ajouter cette fonction

/**
 * Récupère les détails d'une offre d'emploi spécifique par ID.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param int $job_id L'ID de l'offre d'emploi.
 * @return array|false Tableau associatif de l'offre ou false si non trouvée.
 */
function get_job_details($pdo, $job_id) {
    $sql = "SELECT 
                o.*, 
                u.nom AS nom_entreprise, 
                u.email AS email_entreprise 
            FROM 
                offres_emploi o
            JOIN 
                utilisateurs u ON o.employeur_id = u.id
            WHERE 
                o.id = ? AND o.est_actif = TRUE";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$job_id]);
        return $stmt->fetch(); // Récupère une seule ligne
        
    } catch (PDOException $e) {
        error_log("DB Error in fetching job details: " . $e->getMessage()); 
        return false;
    }
}

// ----------------------------------------------------
// FONCTION DE VERIFICATION SI UN CANDIDAT A DEJA POSTULER A UNE OFFRE ' (HAS_APPLIED)
// ----------------------------------------------------
// includes/functions.php - Ajouter cette fonction

/**
 * Vérifie si un candidat spécifique a déjà postulé à une offre donnée.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param int $candidat_id L'ID du candidat.
 * @param int $offre_id L'ID de l'offre d'emploi.
 * @return bool Retourne TRUE si une candidature existe, FALSE sinon.
 */
function has_applied($pdo, $candidat_id, $offre_id) {
    $sql = "SELECT COUNT(*) FROM candidatures 
            WHERE candidat_id = ? AND offre_id = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$candidat_id, $offre_id]);
        
        // Si le COUNT est supérieur à 0, une candidature existe.
        return $stmt->fetchColumn() > 0;
        
    } catch (PDOException $e) {
        error_log("DB Error checking if user has applied: " . $e->getMessage()); 
        // En cas d'erreur DB, pour des raisons de sécurité, on peut retourner TRUE 
        // pour empêcher une soumission non désirée, ou FALSE pour permettre la soumission. 
        // Ici, on retourne FALSE pour ne pas bloquer l'utilisateur en cas d'erreur temporaire.
        return false;
    }
}

// ----------------------------------------------------
// FONCTION GERER LES CANDIDATURES' (GET_APPLICANTS FOR JOB)
// ----------------------------------------------------

// includes/functions.php - Ajouter cette fonction

/**
 * Récupère toutes les candidatures pour une offre d'emploi spécifique.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param int $offre_id L'ID de l'offre d'emploi.
 * @param int $employeur_id L'ID de l'employeur (pour vérification de sécurité).
 * @return array Tableau des candidatures.
 */
function get_applicants_for_job($pdo, $offre_id, $employeur_id) {
    $sql = "SELECT 
                c.id AS candidature_id, 
                c.cv_chemin, 
                c.lettre_motivation, 
                c.statut, 
                c.date_candidature,
                u.nom AS nom_candidat,
                u.email AS email_candidat,
                o.titre AS titre_offre
            FROM 
                candidatures c
            JOIN 
                utilisateurs u ON c.candidat_id = u.id
            JOIN
                offres_emploi o ON c.offre_id = o.id
            WHERE 
                c.offre_id = ? AND o.employeur_id = ?
            ORDER BY 
                c.date_candidature DESC";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$offre_id, $employeur_id]);
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("DB Error in fetching applicants: " . $e->getMessage()); 
        return [];
    }
}

// ----------------------------------------------------
// FONCTION GERER LES MISES A JOUR DU STATUT DE CANDIDATURE' (UPDATE APPLICANT STATUS)
// ----------------------------------------------------

// includes/functions.php - Ajouter cette fonction

/**
 * Met à jour le statut d'une candidature spécifique.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param int $candidature_id L'ID de la candidature.
 * @param string $new_status Le nouveau statut.
 * @param int $employeur_id L'ID de l'employeur (pour sécurité).
 * @return array Résultat de l'opération.
 */
function update_application_status($pdo, $candidature_id, $new_status, $employeur_id) {
    // 1. Vérifier que l'offre appartient bien à l'employeur
    $sql_check = "SELECT 1 FROM candidatures c 
                  JOIN offres_emploi o ON c.offre_id = o.id 
                  WHERE c.id = ? AND o.employeur_id = ?";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$candidature_id, $employeur_id]);

    if ($stmt_check->rowCount() === 0) {
        return ['success' => false, 'message' => 'Erreur : Vous n\'avez pas l\'autorisation de modifier cette candidature.'];
    }

    // 2. Mettre à jour le statut
    $sql_update = "UPDATE candidatures SET statut = ? WHERE id = ?";
    
    try {
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$new_status, $candidature_id]);
        
        return ['success' => true, 'message' => 'Statut de la candidature mis à jour vers "' . ucfirst($new_status) . '".'];
        
    } catch (PDOException $e) {
        error_log("DB Error in status update: " . $e->getMessage()); 
        return ['success' => false, 'message' => 'Une erreur est survenue lors de la mise à jour du statut.'];
    }
}

// RECUPERATION DES JOBS POUR L'EDITION
// includes/functions.php - Ajouter cette fonction

/**
 * Récupère les détails d'une offre d'emploi spécifique pour l'édition, 
 * en vérifiant qu'elle appartient à l'employeur donné.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param int $job_id L'ID de l'offre d'emploi.
 * @param int $employeur_id L'ID de l'employeur connecté.
 * @return array|false Tableau associatif de l'offre ou false si non trouvée/non-autorisée.
 */
function get_job_for_editing($pdo, $job_id, $employeur_id) {
    $sql = "SELECT 
                id, titre, description, lieu, salaire, type_contrat, est_actif
            FROM 
                offres_emploi
            WHERE 
                id = ? AND employeur_id = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$job_id, $employeur_id]);
        return $stmt->fetch(); // Récupère une seule ligne
        
    } catch (PDOException $e) {
        error_log("DB Error in fetching job for editing: " . $e->getMessage()); 
        return false;
    }
}

// UPDATE JOB
// includes/functions.php - Ajouter cette fonction

/**
 * Met à jour une offre d'emploi existante.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param int $job_id L'ID de l'offre à modifier.
 * @param int $employeur_id L'ID de l'employeur (pour vérification de propriété).
 * @param array $data Les nouvelles données de l'offre.
 * @return array Résultat de l'opération.
 */
function update_job($pdo, $job_id, $employeur_id, $data) {
    $sql = "UPDATE offres_emploi SET 
                titre = ?, 
                description = ?, 
                lieu = ?, 
                salaire = ?, 
                type_contrat = ?,
                est_actif = ?
            WHERE 
                id = ? AND employeur_id = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['titre'], 
            $data['description'], 
            $data['lieu'], 
            $data['salaire'], 
            $data['type_contrat'],
            $data['est_actif'], // 1 ou 0
            $job_id,
            $employeur_id
        ]);
        
        // Vérifie si des lignes ont été affectées (si la mise à jour a eu lieu)
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'L\'offre d\'emploi a été mise à jour avec succès!'];
        } else {
             // Si rowCount est 0, soit les données n'ont pas changé, soit l'offre n'existe pas/n'appartient pas.
             // La vérification de propriété est faite par la requête, donc on suppose "pas de changement".
             return ['success' => true, 'message' => 'L\'offre a été consultée, mais aucune modification n\'a été appliquée.'];
        }
        
    } catch (PDOException $e) {
        error_log("DB Error in job update: " . $e->getMessage()); 
        return ['success' => false, 'message' => 'Une erreur est survenue lors de la mise à jour de l\'offre.'];
    }
}

// DELETE JOB
// includes/functions.php - Ajouter cette fonction

/**
 * Supprime une offre d'emploi spécifique en vérifiant qu'elle appartient à l'employeur donné.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param int $job_id L'ID de l'offre à supprimer.
 * @param int $employeur_id L'ID de l'employeur connecté.
 * @return array Résultat de l'opération.
 */
function delete_job($pdo, $job_id, $employeur_id) {
    $sql = "DELETE FROM offres_emploi 
            WHERE id = ? AND employeur_id = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$job_id, $employeur_id]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'L\'offre d\'emploi et toutes les candidatures liées ont été **supprimées** avec succès.'];
        } else {
             return ['success' => false, 'message' => 'Offre non trouvée ou vous n\'êtes pas autorisé à la supprimer.'];
        }
        
    } catch (PDOException $e) {
        error_log("DB Error in job deletion: " . $e->getMessage()); 
        return ['success' => false, 'message' => 'Une erreur est survenue lors de la suppression de l\'offre.'];
    }
}

// GESTION DU PROFIL UTILISATEUR - RECUPERER LES INFOS DE L'UTILISATEUR
// includes/functions.php - Ajouter ces fonctions

/**
 * Récupère les informations de base de l'utilisateur pour l'édition de profil.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param int $user_id L'ID de l'utilisateur connecté.
 * @return array|false Tableau des données de l'utilisateur.
 */
function get_user_profile($pdo, $user_id) {
    $sql = "SELECT nom, email, photo_profil_chemin, role FROM utilisateurs WHERE id = ?";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("DB Error fetching user profile: " . $e->getMessage()); 
        return false;
    }
}


// GESTION DU PROFIL UTILISATEUR - MIS A JOUR DES INFOS DE L'UTILISATEUR
/**
 * Met à jour le nom et l'email de l'utilisateur.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param int $user_id L'ID de l'utilisateur connecté.
 * @param string $nom Nouveau nom.
 * @param string $email Nouvel email.
 * @return array Résultat de l'opération.
 */
function update_user_info($pdo, $user_id, $nom, $email) {
    // 1. Vérification de l'unicité de l'email (hors email actuel de l'utilisateur)
    $sql_check = "SELECT id FROM utilisateurs WHERE email = ? AND id != ?";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$email, $user_id]);
    
    if ($stmt_check->rowCount() > 0) {
        return ['success' => false, 'message' => 'Cet email est déjà utilisé par un autre compte.'];
    }

    // 2. Mise à jour des informations
    $sql_update = "UPDATE utilisateurs SET nom = ?, email = ? WHERE id = ?";
    try {
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$nom, $email, $user_id]);
        
        // Mettre à jour la session en cas de changement de nom
        if ($stmt_update->rowCount() > 0) {
             $_SESSION['user_name'] = $nom;
             return ['success' => true, 'message' => 'Vos informations ont été mises à jour avec succès.'];
        } else {
             return ['success' => true, 'message' => 'Aucune modification détectée.'];
        }
    } catch (PDOException $e) {
        error_log("DB Error updating user info: " . $e->getMessage()); 
        return ['success' => false, 'message' => 'Une erreur est survenue lors de la mise à jour de la base de données.'];
    }
}


// GESTION DU PROFIL UTILISATEUR - MISE A JOUR DU MOT DE PASSE DE L'UTILISATEUR
/**
 * Met à jour le mot de passe de l'utilisateur.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param int $user_id L'ID de l'utilisateur connecté.
 * @param string $current_password Mot de passe actuel non haché.
 * @param string $new_password Nouveau mot de passe non haché.
 * @return array Résultat de l'opération.
 */
function update_user_password($pdo, $user_id, $current_password, $new_password) {
    // 1. Récupérer le hachage actuel pour vérification
    $sql_fetch = "SELECT mot_de_passe FROM utilisateurs WHERE id = ?";
    $stmt_fetch = $pdo->prepare($sql_fetch);
    $stmt_fetch->execute([$user_id]);
    $user = $stmt_fetch->fetch();

    if (!$user) {
        return ['success' => false, 'message' => 'Erreur utilisateur introuvable.'];
    }

    // 2. Vérifier le mot de passe actuel
    if (!password_verify($current_password, $user['mot_de_passe'])) {
        return ['success' => false, 'message' => 'Le mot de passe actuel est incorrect.'];
    }
    
    // 3. Hacher le nouveau mot de passe
    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // 4. Mettre à jour le mot de passe dans la base de données
    $sql_update = "UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?";
    try {
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$new_hashed_password, $user_id]);
        
        return ['success' => true, 'message' => 'Votre mot de passe a été mis à jour avec succès.'];
        
    } catch (PDOException $e) {
        error_log("DB Error updating user password: " . $e->getMessage()); 
        return ['success' => false, 'message' => 'Une erreur est survenue lors de la mise à jour du mot de passe.'];
    }
}


//GESTION DES PHOTOS DE PROFILE - UPLOAD
// includes/functions.php - Ajouter ces fonctions

/**
 * Gère le téléchargement sécurisé d'une photo de profil.
 * @param array $file L'entrée $_FILES['...'] du formulaire.
 * @param int $user_id L'ID de l'utilisateur.
 * @return array Contient 'success' (bool), 'message' (string), et 'filepath' (string|null).
 */
function upload_profile_photo($file, $user_id) {
    $upload_dir = 'uploads/profiles/';
    $max_size = 2 * 1024 * 1024; // 2 Mo
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Erreur lors du téléchargement du fichier.'];
    }

    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'La photo est trop volumineuse (max. 2 Mo).'];
    }

    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Format non supporté. Seuls JPG, PNG et GIF sont acceptés.'];
    }

    // Créer le dossier s'il n'existe pas
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Générer un nom de fichier unique et sécurisé (ex: profile_12_5f8a...)
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $safe_filename = 'profile_' . $user_id . '_' . uniqid() . '.' . $file_extension;
    $destination = $upload_dir . $safe_filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'message' => 'Photo téléchargée avec succès.', 'filepath' => $destination];
    } else {
        return ['success' => false, 'message' => 'Erreur de déplacement du fichier sur le serveur.'];
    }
}

//GESTION DES PHOTOS DE PROFILE - UPDATE PHOTO
/**
 * Met à jour le chemin de la photo de profil dans la base de données.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param int $user_id L'ID de l'utilisateur.
 * @param string|null $filepath Le chemin du fichier ou null pour supprimer.
 * @return bool
 */
function update_profile_photo_path($pdo, $user_id, $filepath = null) {
    $sql = "UPDATE utilisateurs SET photo_profil_chemin = ? WHERE id = ?";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$filepath, $user_id]);
        return true;
    } catch (PDOException $e) {
        error_log("DB Error updating profile photo path: " . $e->getMessage()); 
        return false;
    }
}


// SEND NOTIFICATION TO EMPLOYER VIA EMAIL USING SMTP
// includes/functions.php - MODIFICATION de la fonction existante

function send_new_application_notification($employeur_email, $offre_titre, $candidat_nom) {
    // 1. Définition des variables de l'email
    $subject = "Nouvelle Candidature pour: " . htmlspecialchars($offre_titre);

    // 2. Corps de l'email en HTML
    $message = "
    <html>
    <head>
      <title>Nouvelle Candidature</title>
    </head>
    <body>
      <h2>Candidature Reçue !</h2>
      <p>Cher employeur,</p>
      <p>Une nouvelle candidature a été soumise pour l'offre d'emploi : <strong>" . htmlspecialchars($offre_titre) . "</strong>.</p>
      <p>Détails du candidat :</p>
      <ul>
        <li><strong>Nom :</strong> " . htmlspecialchars($candidat_nom) . "</li>
      </ul>
      <p>Veuillez vous connecter à votre tableau de bord pour examiner le CV et la lettre de motivation.</p>
      <p><a href=\"http://votresite.com/index.php?page=dashboard\">Accéder à mon tableau de bord</a></p>
      <p>Cordialement,<br>L'équipe de Votre Plateforme</p>
    </body>
    </html>
    ";

    // 3. Envoi de l'email via SMTP
    $success = send_email_via_smtp($employeur_email, $subject, $message);
    
    if (!$success) {
        error_log("SMTP ERROR: Échec de l'envoi de notification à l'employeur " . $employeur_email);
    }

    return $success;
}


//************************************************************ */
// SIMULATION DE L'ENVOIE D'EMAIL A L'ENPLOYEUR
// includes/functions.php - Ajouter cette fonction

/**
 * Envoie une notification par e-mail à l'employeur lorsqu'une nouvelle candidature est soumise.
 * NOTE: Cette fonction utilise la fonction mail() de PHP. Pour un environnement de production,
 * il est fortement recommandé d'utiliser une librairie professionnelle (ex: PHPMailer).
 * * @param string $employeur_email L'adresse email de l'employeur.
 * @param string $offre_titre Le titre de l'offre concernée.
 * @param string $candidat_nom Le nom du candidat.
 * @return bool Retourne true si l'envoi semble réussi (selon mail()), false sinon.
 */
/* function send_new_application_notification($employeur_email, $offre_titre, $candidat_nom) {
    // 1. Définition des variables de l'email
    $subject = "Nouvelle Candidature pour: " . $offre_titre;
    $headers = "From: no-reply@votreplateforme.com\r\n";
    $headers .= "Reply-To: no-reply@votreplateforme.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // 2. Corps de l'email en HTML
    $message = "
    <html>
    <head>
      <title>Nouvelle Candidature</title>
    </head>
    <body>
      <h2>Candidature Reçue !</h2>
      <p>Cher employeur,</p>
      <p>Une nouvelle candidature a été soumise pour l'offre d'emploi : <strong>" . htmlspecialchars($offre_titre) . "</strong>.</p>
      <p>Détails du candidat :</p>
      <ul>
        <li><strong>Nom :</strong> " . htmlspecialchars($candidat_nom) . "</li>
      </ul>
      <p>Veuillez vous connecter à votre tableau de bord pour examiner le CV et la lettre de motivation.</p>
      <p><a href=\"http://votresite.com/index.php?page=dashboard\">Accéder à mon tableau de bord</a></p>
      <p>Cordialement,<br>L'équipe de Votre Plateforme</p>
    </body>
    </html>
    ";

    // 3. Envoi de l'email
    // La fonction mail() retourne TRUE si l'envoi est accepté par PHP, pas si l'email arrive.
    $success = mail($employeur_email, $subject, $message, $headers);
    
    if (!$success) {
        error_log("MAIL ERROR: Échec de l'envoi de notification à " . $employeur_email);
    }

    return $success;
} */
//************************************************************ */


// SEND NOTIFICATION TO CANDIDATE VIA EMAIL USING SMTP
// includes/functions.php - MODIFICATION de la fonction existante

function send_candidate_status_update($candidat_email, $offre_titre, $nouveau_statut, $nom_entreprise) {
    // ... (Logique switch/case existante pour définir $subject, $message_body et $action_message) ...
    
    // Adapter le message en fonction du statut
    $subject = "Mise à jour de votre candidature pour l'offre : " . $offre_titre;
    $message_body = "";
    $action_message = "";

    switch ($nouveau_statut) {
        // ... (Contenu des cases 'examiné', 'accepté', 'refusé' - non répété ici) ...
        case 'examiné':
            $message_body = "Votre candidature pour le poste de **" . htmlspecialchars($offre_titre) . "** chez " . htmlspecialchars($nom_entreprise) . " a été **examinée** par l'employeur. Ils vous recontacteront pour la suite du processus.";
            $action_message = "Merci de votre patience !";
            break;
        case 'accepté':
            $message_body = "Félicitations ! Votre candidature pour le poste de **" . htmlspecialchars($offre_titre) . "** chez " . htmlspecialchars($nom_entreprise) . " a été **acceptée**. L'employeur vous contactera directement pour organiser les prochaines étapes.";
            $action_message = "C'est une excellente nouvelle !";
            break;
        case 'refusé':
            $message_body = "Nous vous remercions de l'intérêt que vous portez à l'offre **" . htmlspecialchars($offre_titre) . "** chez " . htmlspecialchars($nom_entreprise) . ". Malheureusement, votre candidature n'a pas été retenue pour la suite du processus pour le moment.";
            $action_message = "Nous vous souhaitons bonne chance dans vos recherches futures.";
            break;
        case 'en attente':
        default:
            return true; 
    }

    $html_message = "
    <html>
    <head><title>$subject</title></head>
    <body>
      <h2>Mise à jour de Statut</h2>
      <p>Cher candidat,</p>
      <p>$message_body</p>
      <p><strong>Statut actuel : " . ucfirst($nouveau_statut) . "</strong></p>
      <p>" . $action_message . "</p>
      <p><a href=\"http://votresite.com/index.php?page=dashboard\">Voir votre tableau de bord Candidat</a></p>
      <p>Cordialement,<br>L'équipe de Votre Plateforme</p>
    </body>
    </html>
    ";

    // 3. Envoi de l'email via SMTP
    $success = send_email_via_smtp($candidat_email, $subject, $html_message);
    
    if (!$success) {
        error_log("SMTP ERROR: Échec de l'envoi de notification de statut à " . $candidat_email);
    }
    return $success;
}


//*********************************************************** */
// SIMULATION DE L'ENVOIE D'EMAIL AU CANDIDAT
// includes/functions.php - Ajouter cette fonction

/**
 * Envoie une notification par e-mail au candidat suite à une mise à jour de statut.
 * @param string $candidat_email L'adresse email du candidat.
 * @param string $offre_titre Le titre de l'offre concernée.
 * @param string $nouveau_statut Le nouveau statut ('examiné', 'accepté', 'refusé', etc.).
 * @param string $nom_entreprise Le nom de l'entreprise.
 * @return bool Résultat de l'envoi de mail.
 */
/* function send_candidate_status_update($candidat_email, $offre_titre, $nouveau_statut, $nom_entreprise) {
    // Adapter le message en fonction du statut
    $subject = "Mise à jour de votre candidature pour l'offre : " . $offre_titre;
    $message_body = "";
    $action_message = "";

    switch ($nouveau_statut) {
        case 'examiné':
            $message_body = "Votre candidature pour le poste de **" . htmlspecialchars($offre_titre) . "** chez " . htmlspecialchars($nom_entreprise) . " a été **examinée** par l'employeur. Ils vous recontacteront pour la suite du processus.";
            $action_message = "Merci de votre patience !";
            break;
        case 'accepté':
            $message_body = "Félicitations ! Votre candidature pour le poste de **" . htmlspecialchars($offre_titre) . "** chez " . htmlspecialchars($nom_entreprise) . " a été **acceptée**. L'employeur vous contactera directement pour organiser les prochaines étapes.";
            $action_message = "C'est une excellente nouvelle !";
            break;
        case 'refusé':
            $message_body = "Nous vous remercions de l'intérêt que vous portez à l'offre **" . htmlspecialchars($offre_titre) . "** chez " . htmlspecialchars($nom_entreprise) . ". Malheureusement, votre candidature n'a pas été retenue pour la suite du processus pour le moment.";
            $action_message = "Nous vous souhaitons bonne chance dans vos recherches futures.";
            break;
        case 'en attente':
        default:
            // Ne pas envoyer de mail pour 'en attente' ou statut inconnu (ou utiliser un message générique)
            return true; 
    }

    $headers = "From: no-reply@votreplateforme.com\r\n";
    $headers .= "Reply-To: no-reply@votreplateforme.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $html_message = "
    <html>
    <head><title>$subject</title></head>
    <body>
      <h2>Mise à jour de Statut</h2>
      <p>Cher candidat,</p>
      <p>$message_body</p>
      <p><strong>Statut actuel : " . ucfirst($nouveau_statut) . "</strong></p>
      <p>" . $action_message . "</p>
      <p><a href=\"http://votresite.com/index.php?page=dashboard\">Voir votre tableau de bord Candidat</a></p>
      <p>Cordialement,<br>L'équipe de Votre Plateforme</p>
    </body>
    </html>
    ";

    $success = mail($candidat_email, $subject, $html_message, $headers);
    
    if (!$success) {
        error_log("MAIL ERROR: Échec de l'envoi de notification de statut à " . $candidat_email);
    }
    return $success;
} */
//*********************************************************** */
   

// RECUPERER LES INFOS DU CANDIDAT POUR L'ENVOIE D'EMAIL
// includes/functions.php - Ajouter cette fonction

/**
 * Récupère l'email du candidat, le titre de l'offre et le nom de l'entreprise pour la notification.
 */
function get_notification_details($pdo, $candidature_id) {
    $sql = "SELECT 
                u.email AS candidat_email, 
                o.titre AS offre_titre,
                eu.nom AS nom_entreprise
            FROM candidatures c
            JOIN utilisateurs u ON c.candidat_id = u.id
            JOIN offres_emploi o ON c.offre_id = o.id
            JOIN utilisateurs eu ON o.employeur_id = eu.id -- Jointure pour le nom de l'employeur/entreprise
            WHERE c.id = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$candidature_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("DB Error fetching notification details: " . $e->getMessage()); 
        return false;
    }
}


//SEND EMAIL VIA SMTP
// includes/functions.php - Ajouter cette fonction pour PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Configure et envoie un email via SMTP en utilisant PHPMailer.
 * @param string $to_email Le destinataire.
 * @param string $subject Le sujet de l'email.
 * @param string $body Le contenu HTML de l'email.
 * @return bool Résultat de l'envoi.
 */
function send_email_via_smtp($to_email, $subject, $body) {
    // 1. Charger les classes PHPMailer (ajustez le chemin si nécessaire)
    require_once 'vendor/autoload.php'; 
    require_once 'includes/smtp_config.php';
    /* require_once 'includes/phpmailer/Exception.php';
    require_once 'includes/phpmailer/PHPMailer.php';
    require_once 'includes/phpmailer/SMTP.php';
    require_once 'includes/smtp_config.php'; */ // Charger les constantes SMTP

    $mail = new PHPMailer(true);

    try {
        // Paramètres du serveur
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Décommentez pour le débogage
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        // Destinataires et Expéditeur
        $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
        $mail->addAddress($to_email);

        // Contenu
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body); // Version texte brute

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Email sending failed to $to_email. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}



// DATA FOR ADMIN DASHBOARD
// includes/functions.php - Statistiques pour le Tableau de Bord Admin
function get_total_counts($pdo) {
    $counts = [];
    $counts['utilisateurs'] = (int)$pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
    $counts['employeurs'] = (int)$pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role='employeur'")->fetchColumn();
    $counts['candidats'] = (int)$pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role='candidat'")->fetchColumn();
    $counts['offres'] = (int)$pdo->query("SELECT COUNT(*) FROM offres_emploi")->fetchColumn();
    $counts['candidatures'] = (int)$pdo->query("SELECT COUNT(*) FROM candidatures")->fetchColumn();
    return $counts;
}


function get_latest_jobs($pdo, $limit = 5) {
    $sql = "SELECT id, titre, date_publication FROM offres_emploi ORDER BY date_publication DESC LIMIT ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}


function get_latest_users($pdo, $limit = 5) {
    $sql = "SELECT id, nom, email, role, date_inscription FROM utilisateurs ORDER BY date_inscription DESC LIMIT ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}


// includes/functions.php - Ajouter ces fonctions pour l'Administration

/**
 * Récupère tous les utilisateurs du système pour le panneau d'administration.
 * @param PDO $pdo L'objet de connexion PDO.
 * @return array Tableau des utilisateurs.
 */
function get_all_users($pdo) {
    $sql = "SELECT 
                id, nom, email, role, date_inscription, 
                (SELECT COUNT(*) FROM candidatures WHERE candidat_id = u.id) AS total_candidatures,
                (SELECT COUNT(*) FROM offres_emploi WHERE employeur_id = u.id) AS total_offres
            FROM utilisateurs u
            ORDER BY date_inscription DESC";
    try {
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("DB Error fetching all users for admin: " . $e->getMessage()); 
        return [];
    }
}

/**
 * Supprime un utilisateur et son contenu associé (offres, candidatures, fichiers).
 * NOTE: Cela nécessite des contraintes ON DELETE CASCADE sur la DB.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param int $user_id L'ID de l'utilisateur à supprimer.
 * @return array Résultat de l'opération.
 */
function admin_delete_user($pdo, $user_id) {
    // Mesure de sécurité: Ne pas s'autodétruire si l'administrateur essaie de supprimer son propre compte
    if ($user_id == ($_SESSION['user_id'] ?? 0)) {
        return ['success' => false, 'message' => "Vous ne pouvez pas supprimer votre propre compte administrateur en étant connecté."];
    }
    
    // NOTE: La suppression du fichier photo de profil et des CVs/fichiers d'offres n'est pas gérée ici 
    // et devrait l'être via une logique manuelle ou un script de nettoyage (comme clean_orphaned_files.php)
    
    $sql = "DELETE FROM utilisateurs WHERE id = ?";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => "L'utilisateur (ID: $user_id) et ses données associées ont été supprimés."];
        } else {
            return ['success' => false, 'message' => "Utilisateur non trouvé ou impossible à supprimer."];
        }
    } catch (PDOException $e) {
        error_log("DB Error deleting user (Admin): " . $e->getMessage()); 
        return ['success' => false, 'message' => "Erreur DB lors de la suppression de l'utilisateur."];
    }
}

/**
 * Met à jour le rôle ou l'email d'un utilisateur par l'administrateur.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param int $user_id L'ID de l'utilisateur.
 * @param string $new_role Le nouveau rôle.
 * @param string $new_email Le nouvel email.
 * @return array Résultat de l'opération.
 */
function admin_update_user($pdo, $user_id, $new_role, $new_email) {
    $allowed_roles = ['candidat', 'employeur', 'admin'];
    if (!in_array($new_role, $allowed_roles) || !filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
         return ['success' => false, 'message' => "Rôle ou format d'email invalide."];
    }
    
    // Vérification d'unicité de l'email (hors l'utilisateur actuel)
    $sql_check = "SELECT id FROM utilisateurs WHERE email = ? AND id != ?";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$new_email, $user_id]);
    if ($stmt_check->rowCount() > 0) {
        return ['success' => false, 'message' => 'Cet email est déjà utilisé par un autre compte.'];
    }
    
    $sql = "UPDATE utilisateurs SET role = ?, email = ? WHERE id = ?";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$new_role, $new_email, $user_id]);
        return ['success' => true, 'message' => "L'utilisateur (ID: $user_id) a été mis à jour."];
    } catch (PDOException $e) {
        error_log("DB Error updating user (Admin): " . $e->getMessage()); 
        return ['success' => false, 'message' => "Erreur DB lors de la mise à jour de l'utilisateur."];
    }
}


// MANAGE JOBS BY ADMIN 
// includes/functions.php - Ajouter ces fonctions pour la gestion des offres par l'Admin

/**
 * Récupère toutes les offres du système, y compris le nom de l'entreprise.
 * @param PDO $pdo L'objet de connexion PDO.
 * @return array Tableau des offres d'emploi.
 */
function get_all_jobs_for_admin($pdo) {
    $sql = "SELECT 
                o.id, o.titre, o.lieu, o.type_contrat, o.date_publication, o.est_actif,
                u.nom AS nom_entreprise, 
                (SELECT COUNT(*) FROM candidatures c WHERE c.offre_id = o.id) AS total_candidatures
            FROM offres_emploi o
            JOIN utilisateurs u ON o.employeur_id = u.id
            ORDER BY o.date_publication DESC";
    try {
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("DB Error fetching all jobs for admin: " . $e->getMessage()); 
        return [];
    }
}

/**
 * Supprime n'importe quelle offre d'emploi du système (Admin-level).
 * @param PDO $pdo L'objet de connexion PDO.
 * @param int $job_id L'ID de l'offre à supprimer.
 * @return array Résultat de l'opération.
 */
function admin_delete_job($pdo, $job_id) {
    // La suppression de l'offre (et des candidatures liées via ON DELETE CASCADE)
    $sql = "DELETE FROM offres_emploi WHERE id = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$job_id]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => "L'offre d'emploi (ID: $job_id) a été supprimée."];
        } else {
             return ['success' => false, 'message' => 'Offre non trouvée ou déjà supprimée.'];
        }
        
    } catch (PDOException $e) {
        error_log("DB Error in job deletion (Admin): " . $e->getMessage()); 
        return ['success' => false, 'message' => 'Une erreur est survenue lors de la suppression de l\'offre.'];
    }
}

/**
 * Active/désactive une offre d'emploi (Admin-level).
 * @param PDO $pdo L'objet de connexion PDO.
 * @param int $job_id L'ID de l'offre.
 * @param bool $is_active L'état désiré.
 * @return array Résultat de l'opération.
 */
function admin_toggle_job_status($pdo, $job_id, $is_active) {
    $sql = "UPDATE offres_emploi SET est_actif = ? WHERE id = ?";
    $status_text = $is_active ? 'activée' : 'désactivée';
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$is_active ? 1 : 0, $job_id]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => "L'offre (ID: $job_id) a été **$status_text**."];
        } else {
            return ['success' => false, 'message' => 'Offre non trouvée.'];
        }
    } catch (PDOException $e) {
        error_log("DB Error in job status toggle (Admin): " . $e->getMessage()); 
        return ['success' => false, 'message' => 'Erreur DB lors de la mise à jour du statut.'];
    }
}


// MANAGE SETTINGS
// includes/functions.php - Ajouter ces fonctions pour les paramètres Admin

/**
 * Récupère tous les paramètres du système.
 * @param PDO $pdo L'objet de connexion PDO.
 * @return array Tableau des paramètres (clé => [valeur, description]).
 */
function get_all_settings($pdo) {
    $sql = "SELECT setting_key, setting_value, description FROM settings ORDER BY setting_key";
    try {
        $stmt = $pdo->query($sql);
        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = [
                'value' => $row['setting_value'],
                'description' => $row['description']
            ];
        }
        return $settings;
    } catch (PDOException $e) {
        error_log("DB Error fetching all settings: " . $e->getMessage()); 
        return [];
    }
}

/**
 * Met à jour un paramètre spécifique.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param string $key La clé du paramètre à modifier.
 * @param string $value La nouvelle valeur.
 * @return array Résultat de l'opération.
 */
function admin_update_setting($pdo, $key, $value) {
    $sql = "UPDATE settings SET setting_value = ? WHERE setting_key = ?";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$value, $key]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => "Le paramètre '**$key**' a été mis à jour."];
        } else {
            return ['success' => false, 'message' => "Paramètre non trouvé ou aucune modification effectuée."];
        }
    } catch (PDOException $e) {
        error_log("DB Error updating setting: " . $e->getMessage()); 
        return ['success' => false, 'message' => "Erreur DB lors de la mise à jour du paramètre."];
    }
}


// MANAGE SETTINGS
// includes/functions.php - Ajouter cette fonction

/**
 * Charge tous les paramètres du site à partir de la base de données et les retourne.
 * @param PDO $pdo L'objet de connexion PDO.
 * @return array Tableau associatif des paramètres (key => value).
 */
function load_site_settings($pdo) {
    $sql = "SELECT setting_key, setting_value FROM settings";
    try {
        $stmt = $pdo->query($sql);
        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Stocker la valeur directement (ex: ['site_name' => 'Ma Plateforme'])
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    } catch (PDOException $e) {
        error_log("DB Error loading site settings: " . $e->getMessage()); 
        // Retourner un minimum par défaut en cas d'échec DB
        return [
            'site_name' => 'Plateforme par Défaut',
            'maintenance_mode' => '1' // Sécurité : bloquer si on ne peut pas lire le mode maintenance
        ];
    }
}


// COUNT ACTIVE JOBS FOR VERIFY LIMITATION 
// includes/functions.php - Ajouter cette fonction

/**
 * Compte le nombre d'offres d'emploi actives publiées par un employeur donné.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param int $employeur_id L'ID de l'employeur.
 * @return int Le nombre d'offres publiées.
 */
function count_employer_active_jobs($pdo, $employeur_id) {
    $sql = "SELECT COUNT(*) FROM offres_emploi WHERE employeur_id = ? AND est_actif = 1";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$employeur_id]);
        return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("DB Error counting employer jobs: " . $e->getMessage());
        return 9999; // Retourner un grand nombre pour bloquer la publication en cas d'erreur DB
    }
}


//********************************************************************** */
// includes/functions.php - Gestion des Alertes d'Emploi

/**
 * Récupère toutes les alertes créées par un candidat donné.
 */
function get_user_alerts($pdo, $user_id) {
    $sql = "SELECT id, mots_cles, lieu, type_contrat, date_creation 
            FROM alertes_emploi 
            WHERE user_id = ? 
            ORDER BY date_creation DESC";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("DB Error fetching user alerts: " . $e->getMessage());
        return [];
    }
}

/**
 * Crée une nouvelle alerte d'emploi pour un candidat.
 */
function create_job_alert($pdo, $user_id, $mots_cles, $lieu, $type_contrat) {
    $sql = "INSERT INTO alertes_emploi (user_id, mots_cles, lieu, type_contrat) 
            VALUES (?, ?, ?, ?)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $mots_cles, $lieu, $type_contrat]);
        return ['success' => true, 'message' => 'L\'alerte a été créée avec succès.'];
    } catch (PDOException $e) {
        error_log("DB Error creating job alert: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erreur lors de la création de l\'alerte.'];
    }
}

/**
 * Supprime une alerte, en s'assurant que seul le propriétaire peut le faire.
 */
function delete_job_alert($pdo, $alert_id, $user_id) {
    $sql = "DELETE FROM alertes_emploi WHERE id = ? AND user_id = ?";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$alert_id, $user_id]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'L\'alerte a été supprimée.'];
        } else {
            return ['success' => false, 'message' => 'Alerte non trouvée ou vous n\'avez pas la permission de la supprimer.'];
        }
    } catch (PDOException $e) {
        error_log("DB Error deleting job alert: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erreur DB lors de la suppression de l\'alerte.'];
    }
}


//********************************************************************* */
// includes/functions.php - Ajouter cette fonction

/**
 * Met à jour une alerte d'emploi, en s'assurant que seul le propriétaire peut le faire.
 */
function update_job_alert($pdo, $alert_id, $user_id, $mots_cles, $lieu, $type_contrat) {
    $sql = "UPDATE alertes_emploi 
            SET mots_cles = ?, lieu = ?, type_contrat = ?
            WHERE id = ? AND user_id = ?";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$mots_cles, $lieu, $type_contrat, $alert_id, $user_id]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'L\'alerte a été modifiée avec succès.'];
        } else {
            return ['success' => false, 'message' => 'Alerte non trouvée, non modifiée, ou vous n\'avez pas la permission.'];
        }
    } catch (PDOException $e) {
        error_log("DB Error updating job alert: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erreur DB lors de la modification de l\'alerte.'];
    }
}



//******************************************************************** */
// includes/functions.php - Ajouter cette fonction
/**
 * Récupère le top N des mots-clés les plus recherchés dans les alertes.
 */
function get_top_job_alert_keywords($pdo, $limit = 10) {
    $sql = "SELECT mots_cles, COUNT(id) AS count
            FROM alertes_emploi
            GROUP BY mots_cles
            ORDER BY count DESC
            LIMIT ?";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("DB Error fetching top alert keywords: " . $e->getMessage());
        return [];
    }
}


//******************************************************************** */
// includes/functions.php - Gestion des profils d'entreprise

/**
 * Récupère le profil détaillé de l'entreprise (ou un profil vide si non trouvé).
 */
function get_company_profile($pdo, $employeur_id) {
    $sql = "SELECT description, secteur_activite, adresse_siege, telephone, site_web 
            FROM profils_entreprise 
            WHERE employeur_id = ?";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$employeur_id]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$profile) {
            // Retourne un profil vide si l'entrée n'existe pas encore
            return [
                'description' => '',
                'secteur_activite' => '',
                'adresse_siege' => '',
                'telephone' => '',
                'site_web' => ''
            ];
        }
        return $profile;
    } catch (PDOException $e) {
        error_log("DB Error fetching company profile: " . $e->getMessage());
        return null;
    }
}

/**
 * Insère ou met à jour le profil détaillé de l'entreprise.
 * Utilise ON DUPLICATE KEY UPDATE (pour MySQL)
 */
function save_company_profile($pdo, $employeur_id, $data) {
    $sql = "INSERT INTO profils_entreprise (employeur_id, description, secteur_activite, adresse_siege, telephone, site_web)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            description = VALUES(description),
            secteur_activite = VALUES(secteur_activite),
            adresse_siege = VALUES(adresse_siege),
            telephone = VALUES(telephone),
            site_web = VALUES(site_web)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $employeur_id,
            $data['description'],
            $data['secteur_activite'],
            $data['adresse_siege'],
            $data['telephone'],
            $data['site_web']
        ]);
        return ['success' => true, 'message' => 'Le profil de l\'entreprise a été mis à jour avec succès.'];
    } catch (PDOException $e) {
        error_log("DB Error saving company profile: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erreur DB lors de la sauvegarde du profil.'];
    }
}


//******************************************************************** */
// includes/functions.php - Récupération du profil public

/**
 * Récupère le profil complet de l'entreprise pour l'affichage public.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param int $employeur_id L'ID de l'entreprise/employeur.
 * @return array Tableau fusionné des données du profil.
 */
function get_public_company_profile($pdo, $employeur_id) {
    // 1. Récupérer les informations de base de l'utilisateur
    $sql_user = "SELECT nom, email FROM utilisateurs WHERE id = ? AND role = 'employeur'";
    $stmt_user = $pdo->prepare($sql_user);
    $stmt_user->execute([$employeur_id]);
    $user_data = $stmt_user->fetch(PDO::FETCH_ASSOC);

    if (!$user_data) {
        return null; // Utilisateur non trouvé ou n'est pas un employeur
    }

    // 2. Récupérer les informations détaillées du profil
    $sql_profile = "SELECT description, secteur_activite, adresse_siege, telephone, site_web 
                    FROM profils_entreprise 
                    WHERE employeur_id = ?";
    $stmt_profile = $pdo->prepare($sql_profile);
    $stmt_profile->execute([$employeur_id]);
    $profile_data = $stmt_profile->fetch(PDO::FETCH_ASSOC);

    // 3. Fusionner les données (utiliser les données par défaut si le profil détaillé n'existe pas)
    $merged_data = array_merge($user_data, $profile_data ?: [
        'description' => 'Pas de description fournie pour le moment.',
        'secteur_activite' => 'Non spécifié',
        'adresse_siege' => 'Non spécifié',
        'telephone' => 'Non spécifié',
        'site_web' => ''
    ]);

    $merged_data['id'] = $employeur_id;
    return $merged_data;
}


//********************************************************************** */
// includes/functions.php - Récupération de toutes les candidatures pour un employeur

/**
 * Récupère toutes les candidatures soumises aux offres d'un employeur donné.
 * Joint les données du candidat, de l'offre et de la candidature.
 * * @param PDO $pdo L'objet de connexion PDO.
 * @param int $employeur_id L'ID de l'employeur.
 * @return array Liste des candidatures détaillées.
 */
function get_all_employer_applications($pdo, $employeur_id) {
    $sql = "SELECT 
                c.id AS candidature_id, 
                c.statut AS statut_candidature, 
                c.date_candidature,
                o.titre AS titre_offre,
                o.id AS offre_id,
                u.nom AS nom_candidat,
                u.email AS email_candidat,
                u.id AS candidat_id
            FROM candidatures c
            JOIN offres_emploi o ON c.offre_id = o.id
            JOIN utilisateurs u ON c.user_id = u.id
            WHERE o.employeur_id = ?
            ORDER BY c.date_candidature DESC";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$employeur_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("DB Error fetching all applications: " . $e->getMessage());
        return [];
    }
}


//*************************************************************************/
// includes/functions.php - Fonctions de Rapports pour l'Admin

/**
 * Récupère la liste des employeurs avec des filtres et statistiques de base.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param array $filters Filtres potentiels (ex: ['statut' => 'actif']).
 * @return array Liste des employeurs avec compte d'offres.
 */
function get_employer_report($pdo, $filters = []) {
    $sql = "SELECT 
                u.id, u.nom, u.email, u.date_inscription,
                (SELECT COUNT(o.id) FROM offres_emploi o WHERE o.employeur_id = u.id) AS total_offres,
                (SELECT COUNT(c.id) FROM candidatures c JOIN offres_emploi o ON c.offre_id = o.id WHERE o.employeur_id = u.id) AS total_candidatures_recues
            FROM utilisateurs u
            WHERE u.role = 'employeur' ";
    
    // Simplification : le statut 'actif' doit être géré si vous avez un champ de statut dans 'utilisateurs'
    // if (!empty($filters['statut'])) {
    //     $sql .= " AND u.statut = :statut"; 
    // }
    
    $sql .= " ORDER BY u.date_inscription DESC";
    
    try {
        $stmt = $pdo->prepare($sql);
        // Bind parameters if filters were implemented
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("DB Error fetching employer report: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère la liste des candidats avec des statistiques de base.
 * @param PDO $pdo L'objet de connexion PDO.
 * @param array $filters Filtres potentiels (ex: ['ville' => 'Paris']).
 * @return array Liste des candidats avec compte de candidatures déposées.
 */
function get_candidat_report($pdo, $filters = []) {
    $sql = "SELECT 
                u.id, u.nom, u.email, u.date_inscription,
                (SELECT COUNT(c.id) FROM candidatures c WHERE c.user_id = u.id) AS total_candidatures_deposees
                -- (Autres champs de profil/CV peuvent être joints ici si besoin)
            FROM utilisateurs u
            WHERE u.role = 'candidat'
            ORDER BY u.date_inscription DESC";

    // Simplification : les filtres sur les profils nécessiteraient un JOIN sur la table de profil
    
    try {
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("DB Error fetching candidat report: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère les candidatures avec statut 'Retenue' (ou 'Embauche Confirmée' si vous l'avez)
 * Simule les confirmations d'embauche.
 */
function get_hiring_confirmation_report($pdo) {
    $sql = "SELECT 
                c.id AS candidature_id, 
                c.date_candidature,
                o.titre AS offre_titre,
                u_emp.nom AS employeur_nom,
                u_cand.nom AS candidat_nom,
                u_cand.email AS candidat_email
            FROM candidatures c
            JOIN offres_emploi o ON c.offre_id = o.id
            JOIN utilisateurs u_emp ON o.employeur_id = u_emp.id
            JOIN utilisateurs u_cand ON c.user_id = u_cand.id
            WHERE c.statut = 'retenue' 
            ORDER BY c.date_candidature DESC";

    try {
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("DB Error fetching hiring confirmation report: " . $e->getMessage());
        return [];
    }
}



// includes/functions.php - Récupération des offres d'emploi par employeur

/**
 * Récupère la liste des offres d'emploi actives publiées par un employeur donné.
 *
 * @param PDO $pdo L'objet de connexion PDO.
 * @param int $employeur_id L'ID de l'entreprise/employeur.
 * @return array Tableau des offres d'emploi actives.
 */
function get_jobs_by_employer($pdo, $employeur_id) {
    $sql = "SELECT 
                id, 
                titre, 
                lieu, 
                type_contrat, 
                date_publication 
            FROM offres_emploi 
            WHERE employeur_id = ? 
            AND est_actif = 1 
            ORDER BY date_publication DESC";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$employeur_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("DB Error fetching active jobs by employer ID $employeur_id: " . $e->getMessage());
        return [];
    }
}
?>