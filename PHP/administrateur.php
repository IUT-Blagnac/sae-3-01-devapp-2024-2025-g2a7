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

// Traitement du formulaire d'ajout de catégorie
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nom_categorie']) && !empty($_POST['nom_categorie'])) {
        $nomCategorie = $_POST['nom_categorie'];
        $insertQuery = "INSERT INTO Categorie (nom) VALUES (?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->execute([$nomCategorie]);
        $message = "Nouvelle catégorie ajoutée avec succès.";
    } else {
        $message = "Veuillez entrer un nom de catégorie.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Administrateur</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'include/header.php'; ?>

    <div class="container">
        <h1>Espace Administrateur</h1>

        <?php if (isset($message)) { echo "<p>$message</p>"; } ?>

        <h2>Ajouter une nouvelle catégorie</h2>
        <form method="POST" action="administrateur.php">
            <label for="nom_categorie">Nom de la catégorie :</label>
            <input type="text" id="nom_categorie" name="nom_categorie" required>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>

        <!-- Autres fonctionnalités administratives -->
        <h2>Gestion des produits</h2>
        <ul>
            <li><a href="ajouter_produit.php">Ajouter un produit</a></li>
            <li><a href="modifier_produit.php">Modifier un produit</a></li>
            <li><a href="supprimer_produit.php">Supprimer un produit</a></li>
        </ul>

        <h2>Gestion des utilisateurs</h2>
        <ul>
            <li><a href="liste_utilisateurs.php">Liste des utilisateurs</a></li>
            <li><a href="modifier_utilisateur.php">Modifier un utilisateur</a></li>
        </ul>

        <h2>Statistiques</h2>
        <ul>
            <li><a href="statistiques_ventes.php">Statistiques des ventes</a></li>
            <li><a href="statistiques_utilisateurs.php">Statistiques des utilisateurs</a></li>
        </ul>
    </div>
</body>
</html>