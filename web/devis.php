<?php
include 'header.php';
$message_confirmation = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO prospects (entreprise, nom_prenom, email, telephone, lieu, type_evenement, date_souhaitee, nb_participants, besoin, statut) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'a_contacter')");
    $stmt->execute([
        htmlspecialchars($_POST['entreprise']),
        htmlspecialchars($_POST['nom_prenom']),
        htmlspecialchars($_POST['email']),
        htmlspecialchars($_POST['telephone']),
        htmlspecialchars($_POST['lieu']),
        htmlspecialchars($_POST['type_evenement']),
        $_POST['date_souhaitee'],
        (int)$_POST['nb_participants'],
        htmlspecialchars($_POST['besoin'])
    ]);
    $message_confirmation = "Merci pour votre demande. Chloé vous recontactera dans les plus brefs délais pour discuter de votre projet.";
}
?>

<h1>Demande de devis</h1>
<p class="text-muted">Tous les champs sont obligatoires pour étudier votre projet.</p>

<?php if ($message_confirmation): ?>
    <div class="alert alert-success"><?= $message_confirmation ?></div>
<?php endif; ?>

<form method="POST" class="row g-3 bg-light p-4 rounded border">
    <div class="col-md-6">
        <label for="entreprise" class="form-label">Nom de l'entreprise</label>
        <input type="text" class="form-control" id="entreprise" name="entreprise" required>
    </div>
    <div class="col-md-6">
        <label for="nom_prenom" class="form-label">Nom et Prénom</label>
        <input type="text" class="form-control" id="nom_prenom" name="nom_prenom" required>
    </div>
    <div class="col-md-6">
        <label for="email" class="form-label">Email professionnel</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="col-md-6">
        <label for="telephone" class="form-label">Numéro de téléphone</label>
        <input type="tel" class="form-control" id="telephone" name="telephone" required>
    </div>
    <div class="col-md-6">
        <label for="lieu" class="form-label">Lieu envisagé</label>
        <input type="text" class="form-control" id="lieu" name="lieu" required>
    </div>
    <div class="col-md-6">
        <label for="type_evenement" class="form-label">Type d'événement</label>
        <select class="form-select" id="type_evenement" name="type_evenement" required>
            <option value="Séminaire">Séminaire</option>
            <option value="Conférence">Conférence</option>
            <option value="Soirée d'entreprise">Soirée d'entreprise</option>
            <option value="Autre">Autre</option>
        </select>
    </div>
    <div class="col-md-6">
        <label for="date_souhaitee" class="form-label">Date souhaitée</label>
        <input type="date" class="form-control" id="date_souhaitee" name="date_souhaitee" required>
    </div>
    <div class="col-md-6">
        <label for="nb_participants" class="form-label">Nombre de participants estimé</label>
        <input type="number" class="form-control" id="nb_participants" name="nb_participants" min="1" required>
    </div>
    <div class="col-12">
        <label for="besoin" class="form-label">Votre besoin en quelques mots</label>
        <textarea class="form-control" id="besoin" name="besoin" rows="4" required></textarea>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary">Envoyer ma demande</button>
    </div>
</form>

<?php include 'footer.php'; ?>
