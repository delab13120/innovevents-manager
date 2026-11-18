<?php
include 'header.php';
$erreur = "";

// Fonction de journalisation NoSQL (Format JSON prêt pour MongoDB)
function enregistrerLogNoSQL($type, $userId, $details) {
    $log = [
        'horodatage' => date('c'),
        'type_action' => $type,
        'id_utilisateur' => $userId,
        'details' => $details
    ];
    file_put_contents('logs_nosql.json', json_encode($log, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $mdp = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND actif = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($mdp, $user['password'])) {
        $_SESSION['user'] = $user;
        enregistrerLogNoSQL('connexion_reussie', $user['id'], ['ip' => $_SERVER['REMOTE_ADDR'], 'role' => $user['role']]);
        echo '<script>window.location.href="dashboard.php";</script>';
        exit;
    } else {
        enregistrerLogNoSQL('connexion_echouee', null, ['email_tente' => $email, 'ip' => $_SERVER['REMOTE_ADDR']]);
        $erreur = "Identifiants incorrects.";
    }
}
?>
<h1>Connexion à votre espace</h1>
<?php if ($erreur): ?><div class="alert alert-danger"><?= $erreur ?></div><?php endif; ?>

<form method="POST" class="bg-light p-4 rounded border">
    <label class="form-label">Adresse Email</label>
    <input type="email" name="email" class="form-control" required placeholder="Ex: chloe@innovevents.com">
    <label class="form-label">Mot de passe</label>
    <input type="password" name="password" class="form-control" required placeholder="Mot de passe initial : password">
    <button type="submit" class="btn btn-primary">Se connecter</button>
    <a href="register.php" class="btn btn-outline-primary">Créer un compte</a>
</form>
<?php include 'footer.php'; ?>
