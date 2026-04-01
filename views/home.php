<?php 
// views/home.php

// Note : Dans une structure procédurale, vous pourriez définir ici
// une variable contenant des offres d'emploi récupérées dans index.php.

// Exemple : Simuler des données si la logique métier n'est pas encore en place
$offres_recentes = [
    ['titre' => 'Développeur PHP Junior', 'entreprise' => 'Tech Solutions', 'lieu' => 'Paris'],
    ['titre' => 'Chef de Projet IT', 'entreprise' => 'InnovCorp', 'lieu' => 'Lyon'],
    ['titre' => 'Stagiaire en Marketing Digital', 'entreprise' => 'DigitalPro', 'lieu' => 'Télétravail'],
];
?>

<div class="container main-content">
    <header class="hero text-center my-5 p-5 bg-light rounded">
        <h1>Trouvez l'Emploi de Vos Rêves</h1>
        <p class="lead">Des milliers d'offres d'emploi disponibles partout en France.</p>
        
        <form action="index.php" method="GET" class="d-flex justify-content-center mt-4">
            <input type="hidden" name="page" value="jobs">
            <input type="text" name="keyword" placeholder="Titre, Mots-clés, Lieu..." 
                   class="form-control me-2" style="max-width: 400px;" required>
            <button type="submit" class="btn btn-primary">Rechercher</button>
        </form>
    </header>

    <hr>

    <section class="recent-jobs">
        <h2>Offres d'Emploi Récentes</h2>
        <div class="row mt-4">
            <?php foreach ($offres_recentes as $offre): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($offre['titre']); ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($offre['entreprise']); ?></h6>
                            <p class="card-text"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($offre['lieu']); ?></p>
                            <a href="index.php?page=job_details&id=<?php echo $offre['id'] ?? '1'; ?>" class="btn btn-sm btn-outline-primary">Voir l'offre</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-3">
            <a href="index.php?page=jobs" class="btn btn-lg btn-success">Voir toutes les offres</a>
        </div>
    </section>

</div>

<?php 
// Afficher l'état de la connexion (pour test)
/* if (isset($pdo)) {
    echo '<p class="text-center mt-5 text-success">DEBUG: Connexion à la base de données réussie dans ' . basename(__FILE__) . '</p>';
} */
?>