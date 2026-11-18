<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include 'header.php';

if (!isset($_SESSION['user'])) {
    echo '<script>window.location.href="login.php";</script>';
    exit;
}

$user = $_SESSION['user'];
$notif = "";

if ($user['role'] === 'admin' && isset($_POST['creer_devis'])) {
    $ht = (float)$_POST['montant_ht'];
    $tva = 20.00;
    $ttc = $ht * (1 + $tva / 100);
    $stmt = $pdo->prepare("INSERT INTO devis (evenement_id, client_id, montant_ht, tva, montant_ttc, statut) VALUES (?, 3, ?, ?, ?, 'etude_client')");
    $stmt->execute([$_POST['evenement_id'], $ht, $tva, $ttc]);
    $notif = '<div class="alert alert-success">Devis généré (TTC : ' . number_format($ttc, 2) . ' €) et envoyé au client !</div>';
}

if ($user['role'] === 'client' && isset($_POST['action_devis'])) {
    $stmt = $pdo->prepare("UPDATE devis SET statut = ?, motif_modification = ? WHERE id = ?");
    $stmt->execute([$_POST['action_devis'], htmlspecialchars($_POST['motif'] ?? ''), $_POST['devis_id']]);
    $notif = '<div class="alert alert-success">Votre réponse sur le devis a bien été enregistrée et transmise à Chloé.</div>';
}

if (($user['role'] === 'admin' || $user['role'] === 'employe') && isset($_POST['ajouter_note'])) {
    $stmt = $pdo->prepare("INSERT INTO notes (evenement_id, auteur_id, contenu) VALUES (1, ?, ?)");
    $stmt->execute([$user['id'], htmlspecialchars($_POST['contenu'])]);
    $notif = '<div class="alert alert-success">Note ajoutée avec succès.</div>';
}
?>

<h1>Tableau de bord — Bienvenue <?= htmlspecialchars($user['prenom']) ?> (Rôle : <?= strtoupper($user['role']) ?>)</h1>
<?= $notif ?>

<?php if ($user['role'] === 'admin'): ?>
    <div class="row mb-4">
        <div class="col-md-4"><div class="card"><h3>Clients actifs</h3><p class="display-4">1</p></div></div>
        <div class="col-md-4"><div class="card"><h3>Évènements brouillon</h3><p class="display-4">1</p></div></div>
        <div class="col-md-4"><div class="card"><h3>Prochains évènements</h3><p>Séminaire Innovation 2026</p></div></div>
    </div>

    <div class="bg-light p-4 rounded border mb-4">
        <h2>Demandes de devis reçues (Prospects)</h2>
        <?php $prospects = $pdo->query("SELECT * FROM prospects ORDER BY id DESC")->fetchAll(); ?>
        <?php foreach ($prospects as $p): ?>
            <div class="card mb-2">
                <strong><?= htmlspecialchars($p['entreprise']) ?></strong> — <?= htmlspecialchars($p['nom_prenom']) ?> (<?= htmlspecialchars($p['type_evenement']) ?>) | Statut : <span class="badge"><?= $p['statut'] ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="bg-light p-4 rounded border mb-4">
        <h2>Créer un devis et générer le PDF (Calcul automatique HT / TVA 20% / TTC)</h2>
        <form method="POST">
            <label class="form-label">Évènement concerné</label>
            <select name="evenement_id" class="form-select">
                <option value="1">Séminaire Innovation 2026 (Sophie Durand)</option>
                <option value="2">Gala Annuel Luxe (Sophie Durand)</option>
            </select>
            <label class="form-label">Prestation globale - Montant HT (€)</label>
            <input type="number" step="0.01" name="montant_ht" class="form-control" placeholder="Ex: 5000" required>
            <button type="submit" name="creer_devis" class="btn btn-primary">Générer le devis PDF et envoyer</button>
        </form>
    </div>
<?php endif; ?>

<?php if ($user['role'] === 'admin' || $user['role'] === 'employe'): ?>
    <div class="bg-light p-4 rounded border mb-4">
        <h2>Dernières notes sur les évènements</h2>
        <form method="POST" class="mb-3">
            <input type="text" name="contenu" class="form-control" placeholder="Écrire une note rapide sur l'évènement..." required>
            <button type="submit" name="ajouter_note" class="btn btn-dark">Ajouter la note</button>
        </form>
        <?php $notes = $pdo->query("SELECT n.*, u.prenom FROM notes n JOIN users u ON n.auteur_id = u.id ORDER BY n.id DESC LIMIT 5")->fetchAll(); ?>
        <?php foreach ($notes as $n): ?>
            <div class="card mb-2"><small><strong><?= htmlspecialchars($n['prenom']) ?> :</strong> <?= htmlspecialchars($n['contenu']) ?></small></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($user['role'] === 'client'): ?>
    <div class="bg-light p-4 rounded border mb-4">
        <h2>Mes Devis à valider</h2>
        <?php $mesDevis = $pdo->query("SELECT d.*, e.titre FROM devis d JOIN evenements e ON d.evenement_id = e.id")->fetchAll(); ?>
        <?php foreach ($mesDevis as $d): ?>
            <div class="card mb-3">
                <h3><?= htmlspecialchars($d['titre']) ?></h3>
                <p>Montant HT : <?= $d['montant_ht'] ?> € | TVA : <?= $d['tva'] ?>% | <strong>Total TTC : <?= $d['montant_ttc'] ?> €</strong></p>
                <p>Statut actuel : <span class="badge"><?= $d['statut'] ?></span></p>
                <form method="POST" class="mt-2">
                    <input type="hidden" name="devis_id" value="<?= $d['id'] ?>">
                    <input type="text" name="motif" class="form-control" placeholder="Motif si demande de modification...">
                    <button type="submit" name="action_devis" value="accepte" class="btn btn-primary">Accepter le devis</button>
                    <button type="submit" name="action_devis" value="modification" class="btn btn-dark">Demander modification</button>
                    <button type="submit" name="action_devis" value="refuse" class="btn btn-outline-primary">Refuser</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?>
