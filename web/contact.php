<?php
include 'header.php';
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['username']) && empty($_POST['email'])) {
        $msg = '<div class="alert alert-danger">Veuillez renseigner un email ou un nom d\'utilisateur.</div>';
    } else {
        $msg = '<div class="alert alert-success">Votre demande a bien été transmise à contact@innovevents.com.</div>';
    }
}
?>
<h1>Contactez-nous</h1>
<?= $msg ?>
<form method="POST" class="bg-light p-4 rounded border">
    <div class="mb-3">
        <label for="username" class="form-label">Nom d'utilisateur (facultatif)</label>
        <input type="text" class="form-control" id="username" name="username">
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Adresse email (obligatoire si pas de nom d'utilisateur)</label>
        <input type="email" class="form-control" id="email" name="email">
    </div>
    <div class="mb-3">
        <label for="titre" class="form-label">Titre de la demande</label>
        <input type="text" class="form-control" id="titre" name="titre" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Envoyer</button>
</form>
<?php include 'footer.php'; ?>
