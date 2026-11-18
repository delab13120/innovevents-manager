<?php
include 'header.php';

$sql = "SELECT * FROM evenements WHERE accord_client = 1 AND statut != 'brouillon' AND visible_public = 1";
$params = [];

if (!empty($_GET['type'])) {
    $sql .= " AND type_evenement = ?";
    $params[] = $_GET['type'];
}
if (!empty($_GET['theme'])) {
    $sql .= " AND theme LIKE ?";
    $params[] = "%" . $_GET['theme'] . "%";
}
if (!empty($_GET['date_debut'])) {
    $sql .= " AND date_debut >= ?";
    $params[] = $_GET['date_debut'];
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$evenements = $stmt->fetchAll();
?>

<h1>Nos Évènements Réalisés</h1>

<form method="GET" class="row g-3 my-4 bg-light p-3 rounded border">
    <div class="col-md-3">
        <label for="type" class="form-label">Type d'événement</label>
        <select name="type" id="type" class="form-select">
            <option value="">Tous</option>
            <option value="Séminaire">Séminaire</option>
            <option value="Conférence">Conférence</option>
            <option value="Soirée d'entreprise">Soirée d'entreprise</option>
        </select>
    </div>
    <div class="col-md-3">
        <label for="theme" class="form-label">Thème</label>
        <input type="text" name="theme" id="theme" class="form-control" placeholder="Ex: Tech, Luxe...">
    </div>
    <div class="col-md-3">
        <label for="date_debut" class="form-label">À partir du</label>
        <input type="date" name="date_debut" id="date_debut" class="form-control">
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-dark w-100">Filtrer</button>
    </div>
</form>

<div class="row g-4">
    <?php foreach ($evenements as $evt): ?>
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <span class="badge bg-secondary mb-2"><?= htmlspecialchars($evt['type_evenement']) ?> - <?= htmlspecialchars($evt['theme']) ?></span>
                    <h2 class="h4 card-title"><?= htmlspecialchars($evt['titre']) ?></h2>
                    <p class="card-text"><strong>Lieu :</strong> <?= htmlspecialchars($evt['lieu']) ?><br>
                    <strong>Du :</strong> <?= date('d/m/Y H:i', strtotime($evt['date_debut'])) ?> <strong>au</strong> <?= date('d/m/Y H:i', strtotime($evt['date_fin'])) ?></p>
                    <hr>
                    <section aria-label="Prestations et Devis">
                        <h3 class="h6">Prestations et Devis</h3>
                        <p class="small text-muted">Vous souhaitez organiser un événement similaire ?</p>
                        <a href="devis.php" class="btn btn-outline-primary btn-sm">Demander un devis personnalisé</a>
                    </section>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include 'footer.php'; ?>
