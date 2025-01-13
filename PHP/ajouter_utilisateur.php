<?php
session_start();
require 'include/Connect.inc.php';

// Vérifier que l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['nom'])) {
    header('Location: login.php');
    exit();
}

$login = $_SESSION['nom'];
$query = "SELECT rôle FROM Utilisateur WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$login]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result || $result['rôle'] !== 'Admin') {
    echo "Accès refusé. Cette page est réservée aux administrateurs.";
    exit();
}

$message = "";
$messageType = ""; // Variable pour stocker le type de message (success ou danger)

// Ajouter un nouvel utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $civilite = $_POST['civilite'];
    $email = $_POST['email'];
    $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
    $numTel = $_POST['numTel'];
    $role = $_POST['role'];

    // Expressions régulières pour valider les champs
    $regexEmail = '/^[\w\.-]+@([\w-]+\.)+[\w-]{2,4}$/'; // Email valide
    $regexTel = '/^\d{10}$/'; // Numéro de téléphone à 10 chiffres

    // Validation des champs
    if (!preg_match($regexEmail, $email)) {
        $message = "Format d'email invalide.";
        $messageType = "danger";
    } elseif (!preg_match($regexTel, $numTel)) {
        $message = "Format de numéro de téléphone invalide. Utilisez un numéro à 10 chiffres.";
        $messageType = "danger";
    } else {
        try {
            // Génération du prochain ID utilisateur
            $sqlId = "SELECT COALESCE(MAX(idUtilisateur), 0) + 1 AS nextId FROM Utilisateur";
            $stmtId = $conn->query($sqlId);
            $nextId = $stmtId->fetchColumn();

            // Insérer l'utilisateur dans la base de données
            $insertUserQuery = "INSERT INTO Utilisateur (idUtilisateur, nom, prenom, Civilite, email, mdp, numTel, rôle) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $insertUserStmt = $conn->prepare($insertUserQuery);
            $insertUserStmt->execute([$nextId, $nom, $prenom, $civilite, $email, $mdp, $numTel, $role]);

            $message = "Utilisateur ajouté avec succès.";
            $messageType = "success";
        } catch (Exception $e) {
            $message = "Erreur lors de l'ajout de l'utilisateur : " . $e->getMessage();
            $messageType = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Utilisateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'include/header.php'; ?>

    <div class="container mt-5">
        <h2>Ajouter Utilisateur</h2>

        <?php if ($message) { echo "<div class='alert alert-$messageType'>$message</div>"; } ?>

        <form method="post">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" placeholder="Ex: Dupont" required>
            </div>
            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Ex: Jean" required>
            </div>
            <div class="mb-3">
                <label for="civilite" class="form-label">Civilité</label>
                <select class="form-control" id="civilite" name="civilite" required>
                    <option value="H">Homme</option>
                    <option value="F">Femme</option>
                    <option value="A">Autre</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Ex: jean.dupont@example.com" required>
            </div>
            <div class="mb-3">
                <label for="mdp" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="mdp" name="mdp" required>
            </div>
            <div class="mb-3">
                <label for="numTel" class="form-label">Numéro de téléphone</label>
                <input type="text" class="form-control" id="numTel" name="numTel" placeholder="Ex: 0123456789" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Rôle</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="Client">Utilisateur</option>
                    <option value="Admin">Administrateur</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
<footer>
    <?php include 'include/footer.php'; ?>
</footer>
</html>