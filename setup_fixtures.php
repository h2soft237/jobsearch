<?php
// setup_fixtures.php - Script de génération de données de test (Fixtures)

// --- 1. CONFIGURATION ---
require 'vendor/autoload.php';
require_once 'includes/db.php';
require_once 'includes/functions.php'; // Pour is_role, connect_db, etc.

use Faker\Factory;

// Connexion à la base de données
$pdo = connect_db();
$faker = Factory::create('fr_FR'); // Utiliser des données françaises

// Nombre de données à créer
$NUM_EMPLOYEURS = 5;
$NUM_CANDIDATS = 10;
$OFFRES_PAR_EMPLOYEUR = 5;
$CANDIDATURES_PAR_OFFRE = 3;

echo "--- Démarrage de la génération des fixtures ---\n";

// --- 2. FONCTION UTILITAIRE : Exécution SQL et Affichage ---
function execute_sql($pdo, $sql, $params = [], $message = "Exécution réussie.") {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo "✅ " . $message . "\n";
        return true;
    } catch (PDOException $e) {
        echo "❌ ERREUR DB : " . $e->getMessage() . " | SQL: " . $sql . "\n";
        return false;
    }
}

// --- 3. NETTOYAGE PRÉALABLE (OPTIONNEL MAIS RECOMMANDÉ) ---
echo "\n--- 🧹 Nettoyage des tables existantes... ---\n";
// Supprimer les données, mais conserver les tables. Attention à l'ordre des FK !
execute_sql($pdo, "SET FOREIGN_KEY_CHECKS=0");
execute_sql($pdo, "TRUNCATE TABLE candidatures");
execute_sql($pdo, "TRUNCATE TABLE offres_emploi");
execute_sql($pdo, "TRUNCATE TABLE profils_entreprise");
execute_sql($pdo, "TRUNCATE TABLE alertes_emploi");
execute_sql($pdo, "DELETE FROM utilisateurs WHERE id > 3"); // Conserver l'Admin de base si id=1
execute_sql($pdo, "SET FOREIGN_KEY_CHECKS=1");

// --- 4. CRÉATION DES UTILISATEURS DE TEST ---

$employeur_ids = [];
$candidat_ids = [];

// A. Employeurs
echo "\n--- 🏭 Création de $NUM_EMPLOYEURS Employeurs... ---\n";
for ($i = 0; $i < $NUM_EMPLOYEURS; $i++) {
    $company_name = $faker->company;
    $email = $faker->unique()->companyEmail;
    $password_hash = password_hash('password', PASSWORD_DEFAULT); // Mot de passe facile pour le test
    $date_inscription = $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s');
    
    $sql_user = "INSERT INTO utilisateurs (nom, email, mot_de_passe, role, date_inscription) 
                 VALUES (?, ?, ?, 'employeur', ?)";
    if (execute_sql($pdo, $sql_user, [$company_name, $email, $password_hash, $date_inscription], "Employeur créé: $company_name")) {
        $employeur_id = $pdo->lastInsertId();
        $employeur_ids[] = $employeur_id;

        // Créer un profil d'entreprise détaillé
        $sql_profile = "INSERT INTO profils_entreprise (employeur_id, description, secteur_activite, adresse_siege, telephone, site_web)
                        VALUES (?, ?, ?, ?, ?, ?)";
        $profile_data = [
            $employeur_id,
            $faker->paragraphs(3, true),
            $faker->jobTitle,
            $faker->address,
            $faker->phoneNumber,
            $faker->url
        ];
        execute_sql($pdo, $sql_profile, $profile_data, "Profil entreprise créé pour $company_name.");
    }
}

// B. Candidats
echo "\n--- 🧑‍💻 Création de $NUM_CANDIDATS Candidats... ---\n";
for ($i = 0; $i < $NUM_CANDIDATS; $i++) {
    $name = $faker->firstName . ' ' . $faker->lastName;
    $email = $faker->unique()->email;
    $password_hash = password_hash('password', PASSWORD_DEFAULT);
    $date_inscription = $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s');
    
    $sql_user = "INSERT INTO utilisateurs (nom, email, mot_de_passe, role, date_inscription) 
                 VALUES (?, ?, ?, 'candidat', ?)";
    if (execute_sql($pdo, $sql_user, [$name, $email, $password_hash, $date_inscription], "Candidat créé: $name")) {
        $candidat_ids[] = $pdo->lastInsertId();
    }
}


// --- 5. CRÉATION DES OFFRES ET CANDIDATURES ---

$offre_ids = [];
$statuts_candidature = ['en attente', 'en revue', 'retenue', 'rejetee'];
$types_contrat = ['CDI', 'CDD', 'Stage', 'Intérim'];

echo "\n--- 💼 Création des Offres et des Candidatures... ---\n";

foreach ($employeur_ids as $employeur_id) {
    for ($j = 0; $j < $OFFRES_PAR_EMPLOYEUR; $j++) {
        
        // A. Création de l'Offre
        $offre_titre = $faker->catchPhrase;
        $offre_description = $faker->paragraphs(5, true);
        $offre_lieu = $faker->city;
        $offre_contrat = $faker->randomElement($types_contrat);
        $date_publication = $faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d H:i:s');
        $est_actif = $faker->boolean(80); // 80% des offres sont actives

        $sql_offre = "INSERT INTO offres_emploi (employeur_id, titre, description, lieu, type_contrat, date_publication, est_actif)
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        if (execute_sql($pdo, $sql_offre, [
            $employeur_id, $offre_titre, $offre_description, $offre_lieu, $offre_contrat, $date_publication, $est_actif
        ], "Offre créée: $offre_titre")) {
            $offre_id = $pdo->lastInsertId();
            $offre_ids[] = $offre_id;

            // B. Création des Candidatures pour cette Offre
            $candidats_pour_offre = $faker->randomElements($candidat_ids, $CANDIDATURES_PAR_OFFRE, false); // Candidats uniques
            
            foreach ($candidats_pour_offre as $candidat_id) {
                $statut = $faker->randomElement($statuts_candidature);
                $date_candidature = $faker->dateTimeBetween($date_publication, 'now')->format('Y-m-d H:i:s');

                $sql_candidature = "INSERT INTO candidatures (offre_id, candidat_id, statut, date_candidature)
                                    VALUES (?, ?, ?, ?)";
                execute_sql($pdo, $sql_candidature, [
                    $offre_id, $candidat_id, $statut, $date_candidature
                ], "Candidature créée: ID Offre $offre_id / ID Candidat $candidat_id ($statut)");
            }
        }
    }
}

// --- 6. CRÉATION DES ALERTES (Optionnel) ---
echo "\n--- 🔔 Création des Alertes Aléatoires... ---\n";
foreach ($candidat_ids as $candidat_id) {
    if ($faker->boolean(50)) { // 50% des candidats ont une alerte
        $sql_alerte = "INSERT INTO alertes_emploi (user_id, mots_cles, lieu, type_contrat) VALUES (?, ?, ?, ?)";
        $alerte_data = [
            $candidat_id,
            $faker->randomElement(['Développeur', 'Marketing', 'Commercial', 'Comptable', 'RH']),
            $faker->randomElement(['Paris', 'Lyon', 'Télétravail', null]),
            $faker->randomElement(['CDI', 'CDD', null])
        ];
        execute_sql($pdo, $sql_alerte, $alerte_data, "Alerte créée pour Candidat ID $candidat_id.");
    }
}

echo "\n--- ✅ Génération de fixtures terminée ! ---\n";
echo "Total Utilisateurs créés : " . ($NUM_EMPLOYEURS + $NUM_CANDIDATS) . "\n";
echo "Total Offres créées : " . count($offre_ids) . "\n";
echo "Total Candidatures créées : " . (count($offre_ids) * $CANDIDATURES_PAR_OFFRE) . "\n";
?>