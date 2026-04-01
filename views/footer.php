<?php
// views/footer.php
// Inclut la fin de la balise <main>, le pied de page et les scripts JS.
?>
</main>

<footer class="footer mt-auto py-3">
    
    <div class="container text-center">
        <span class="text-muted">&copy; <?php echo date('Y'); ?> Job Board PHP Procédural. Tous droits réservés.</span>
        <p>Contactez-nous : <a href="mailto:<?php echo htmlspecialchars($GLOBALS['settings']['contact_email'] ?? ''); ?>">
        <?php echo htmlspecialchars($GLOBALS['settings']['contact_email'] ?? 'Contact par défaut'); ?>
        </a></p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>