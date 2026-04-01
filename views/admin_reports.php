<?php
// views/admin_reports.php - Panneau de Rapports Administratifs

global $pdo;

// 1. VÉRIFICATION DE SÉCURITÉ
if (!is_role('admin')) {
    redirect_to('dashboard');
}

// 2. Récupération des Données pour les Rapports
$employers = get_employer_report($pdo);
$candidats = get_candidat_report($pdo);
$confirmations = get_hiring_confirmation_report($pdo);
?>
<div class="container mt-5">
    <header class="mb-4 d-flex justify-content-between align-items-center">
        <h1 class="display-5"><i class="fas fa-chart-bar text-info"></i> Tableau de Bord des Rapports</h1>
        
        <!-- <div>
            <a href="index.php?page=dashboard_admin" class="btn btn-sm btn-outline-secondary me-2 print-hide">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <button onclick="window.print()" class="btn btn-info btn-sm print-hide">
                <i class="fas fa-print"></i> Imprimer ce Rapport
            </button>
        </div> -->
        
    </header>
    <p class="lead">Générez et analysez les données clés sur l'activité des employeurs et des candidats.</p>
    <hr class="print-hide"> <div class="print-only" style="display: none;">
    <h1>Rapport Administrateur Généré par <?php echo htmlspecialchars($GLOBALS['settings']['site_name'] ?? 'Votre Plateforme'); ?></h1>
    <p>Date d'impression : <?php echo date('d/m/Y H:i:s'); ?></p>
</div>

<style>
/* À placer dans votre CSS */
@media print {
    .print-only {
        display: block !important;
        margin-bottom: 20px;
        text-align: center;
        border-bottom: 1px solid #ccc;
    }
}
</style>

<div class="container mt-5">
    <div class="card shadow-lg mb-5">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0"><i class="fas fa-trophy"></i> Rapport : Confirmations d'Embauche</h2>
            <a href="process_pdf_report.php?report=confirmations" class="btn btn-sm btn-light">
                <i class="fas fa-file-pdf"></i> Télécharger PDF
            </a>
        </div>
    </div>
    
    <div class="card shadow-lg mb-5">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0"><i class="fas fa-building"></i> Rapport : Employeurs</h2>
            <a href="process_pdf_report.php?report=employeurs" class="btn btn-sm btn-light">
                <i class="fas fa-file-pdf"></i> Télécharger PDF
            </a>
        </div>
    </div>

    <div class="card shadow-lg mb-5">
        <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0"><i class="fas fa-users"></i> Rapport : Candidats</h2>
            <a href="process_pdf_report.php?report=candidats" class="btn btn-sm btn-light">
                <i class="fas fa-file-pdf"></i> Télécharger PDF
            </a>
        </div>
    </div>
    <div class="card shadow-lg mb-5">
        <div class="card-header bg-info text-white">
            <h2 class="h4 mb-0"><i class="fas fa-trophy"></i> Rapport : Confirmations d'Embauche (Statut 'Retenue')</h2>
        </div>
        <div class="card-body p-0">
            <?php if (empty($confirmations)): ?>
                <div class="p-4 text-center text-muted">Aucune candidature marquée comme 'Retenue' pour le moment.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped m-0">
                        <thead>
                            <tr>
                                <th>Offre</th>
                                <th>Employeur</th>
                                <th>Candidat Retenu</th>
                                <th>Email Candidat</th>
                                <th>Date Candidature</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($confirmations as $conf): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($conf['offre_titre']); ?></td>
                                    <td><?php echo htmlspecialchars($conf['employeur_nom']); ?></td>
                                    <td>**<?php echo htmlspecialchars($conf['candidat_nom']); ?>**</td>
                                    <td><?php echo htmlspecialchars($conf['candidat_email']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($conf['date_candidature'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    ---

    <div class="card shadow-lg mb-5">
        <div class="card-header bg-primary text-white">
            <h2 class="h4 mb-0"><i class="fas fa-building"></i> Rapport : Employeurs (<?php echo count($employers); ?> inscrits)</h2>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped m-0">
                    <thead>
                        <tr>
                            <th>Nom Entreprise</th>
                            <th>Email</th>
                            <th>Date Inscription</th>
                            <th>Total Offres</th>
                            <th>Candidatures Reçues</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($employers)): ?>
                             <tr><td colspan="5" class="text-center text-muted">Aucun employeur trouvé.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($employers as $emp): ?>
                            <tr>
                                <td>**<?php echo htmlspecialchars($emp['nom']); ?>**</td>
                                <td><?php echo htmlspecialchars($emp['email']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($emp['date_inscription'])); ?></td>
                                <td><span class="badge bg-primary"><?php echo $emp['total_offres']; ?></span></td>
                                <td><span class="badge bg-secondary"><?php echo $emp['total_candidatures_recues']; ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    ---

    <div class="card shadow-lg mb-5">
        <div class="card-header bg-warning text-dark">
            <h2 class="h4 mb-0"><i class="fas fa-users"></i> Rapport : Candidats (<?php echo count($candidats); ?> inscrits)</h2>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped m-0">
                    <thead>
                        <tr>
                            <th>Nom Candidat</th>
                            <th>Email</th>
                            <th>Date Inscription</th>
                            <th>Candidatures Déposées</th>
                            <th>Confirmation Dépôt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($candidats)): ?>
                             <tr><td colspan="5" class="text-center text-muted">Aucun candidat trouvé.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($candidats as $cand): ?>
                            <tr>
                                <td>**<?php echo htmlspecialchars($cand['nom']); ?>**</td>
                                <td><?php echo htmlspecialchars($cand['email']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($cand['date_inscription'])); ?></td>
                                <td><span class="badge bg-primary"><?php echo $cand['total_candidatures_deposees']; ?></span></td>
                                <td>
                                    <?php if ($cand['total_candidatures_deposees'] > 0): ?>
                                        <span class="badge bg-success">Oui</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Non</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>