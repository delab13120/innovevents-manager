<?php
include 'header.php';
$avis = $pdo->query("SELECT a.*, u.prenom, e.titre FROM avis a JOIN users u ON a.client_id = u.id JOIN evenements e ON a.evenement_id = e.id WHERE a.valide = 1")->fetchAll();
?>
<h1>Avis de nos clients</h1>
<div class="row g-3 mt-3">
    <?php foreach ($avis as $a): ?>
        <div class="col-md-6">
            <div class="card p-3">
                <h5><?= htmlspecialchars($a['titre']) ?> (<?= $a['note'] ?>/5)</h5>
                <p>"<?= htmlspecialchars($a['commentaire']) ?>"</p>
                <small class="text-muted">Par <?= htmlspecialchars($a['prenom']) ?></small>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php include 'footer.php'; ?>
