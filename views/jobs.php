
<?php
// views/jobs.php - Mise à jour pour le filtrage et la pagination

// Connexion à la base de données
global $pdo; 

// --- Configuration de la Pagination ---
$jobs_per_page = 10;
$current_page = (int)($_GET['p'] ?? 1);
$current_page = max(1, $current_page); // S'assurer que la page est au moins 1

// --- Récupération des Paramètres de Filtrage ---
$keyword = trim($_GET['keyword'] ?? '');
$lieu = trim($_GET['lieu'] ?? '');
$type_contrat = trim($_GET['type_contrat'] ?? 'all');

// --- Calcul de l'Offset et du Nombre Total ---
$offset = ($current_page - 1) * $jobs_per_page;
$total_jobs = get_active_jobs_count($pdo, $keyword, $lieu, $type_contrat);
$total_pages = ceil($total_jobs / $jobs_per_page);

// --- Récupération des Offres (avec filtres et pagination) ---
$jobs = get_active_jobs($pdo, $keyword, $lieu, $type_contrat, $jobs_per_page, $offset);

// Options pour le type de contrat (Doit correspondre à l'ENUM dans la DB)
$contrat_options = ['CDI', 'CDD', 'Intérim', 'Stage', 'Freelance'];

// Préparer les paramètres de l'URL pour la pagination
$query_params = http_build_query([
    'page' => 'jobs',
    'keyword' => $keyword,
    'lieu' => $lieu,
    'type_contrat' => $type_contrat
]);
?>

<div class="container mt-5">
    <h1 class="mb-4">Toutes les Offres d'Emploi</h1>

    <div class="card p-4 mb-4 shadow-sm">
        <form action="index.php" method="GET" class="row g-3">
            <input type="hidden" name="page" value="jobs">
            
            <div class="col-md-6">
                <label for="keyword" class="form-label visually-hidden">Mot-clé</label>
                <input type="text" name="keyword" id="keyword" class="form-control" 
                       placeholder="Titre ou description..." 
                       value="<?php echo htmlspecialchars($keyword); ?>">
            </div>
            
            <div class="col-md-3">
                <label for="lieu" class="form-label visually-hidden">Lieu</label>
                <input type="text" name="lieu" id="lieu" class="form-control" 
                       placeholder="Ville ou région..." 
                       value="<?php echo htmlspecialchars($lieu); ?>">
            </div>
            
            <div class="col-md-3">
                <label for="type_contrat" class="form-label visually-hidden">Contrat</label>
                <select name="type_contrat" id="type_contrat" class="form-select">
                    <option value="all">Tous Contrats</option>
                    <?php foreach ($contrat_options as $option): ?>
                        <option value="<?php echo $option; ?>" 
                            <?php echo ($type_contrat === $option) ? 'selected' : ''; ?>>
                            <?php echo $option; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-12">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Filtrer et Rechercher
                </button>
            </div>
        </form>
    </div>
    
    <h2 class="h4">
        <?php echo $total_jobs; ?> Offres correspondant aux critères
    </h2>
    
    <hr>
    
    <div class="row">
        <?php if (empty($jobs)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center" role="alert">
                    Aucune offre d'emploi active ne correspond à vos filtres.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($jobs as $job): 
                $excerpt = substr(strip_tags($job['description']), 0, 150) . '...';
            ?>
                <div class="col-lg-12 mb-4">
                    <div class="card shadow-sm border-start border-primary border-5">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4 class="card-title text-primary"><?php echo htmlspecialchars($job['titre']); ?></h4>
                                    <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($job['nom_entreprise']); ?></h6>
                                    
                                    <div class="job-meta mb-3">
                                        <span class="badge bg-secondary me-2"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['lieu']); ?></span>
                                        <span class="badge bg-info me-2"><i class="fas fa-clock"></i> <?php echo htmlspecialchars($job['type_contrat']); ?></span>
                                        <?php if (!empty($job['salaire'])): ?>
                                            <span class="badge bg-warning text-dark"><i class="fas fa-money-bill-wave"></i> <?php echo htmlspecialchars($job['salaire']); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <p class="card-text"><?php echo $excerpt; ?></p>
                                </div>
                                
                                <div class="col-md-4 text-md-end">
                                    <p class="text-muted small">Publié le <?php echo date('d/m/Y', strtotime($job['date_publication'])); ?></p>
                                    <a href="index.php?page=job_details&id=<?php echo $job['id']; ?>" class="btn btn-primary btn-lg">Voir l'offre</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            
            <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="index.php?<?php echo $query_params; ?>&p=<?php echo $current_page - 1; ?>" aria-label="Précédent">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo ($i === $current_page) ? 'active' : ''; ?>">
                    <a class="page-link" href="index.php?<?php echo $query_params; ?>&p=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="index.php?<?php echo $query_params; ?>&p=<?php echo $current_page + 1; ?>" aria-label="Suivant">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
    
</div>

