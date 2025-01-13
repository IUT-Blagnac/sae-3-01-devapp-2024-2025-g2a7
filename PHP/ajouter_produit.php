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

// Ajouter un nouveau produit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomArticle = $_POST['nomArticle'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $poids = $_POST['poids'];
    $dimension = $_POST['dimension'];
    $nbPiece = $_POST['nbPiece'];
    $trancheAge = $_POST['trancheAge'];
    $couleur = $_POST['couleur'];
    $nouveaute = isset($_POST['nouveaute']) ? 1 : 0;
    $idCatégorie = $_POST['idCatégorie'];
    $seuil_min = $_POST['seuil_min'];
    $seuil_max = $_POST['seuil_max'];
    $quantite = $_POST['quantite'];

    // Expressions régulières pour valider les champs
    $regexDimension = '/^\d+(\s*[x*]\s*\d+)*$/'; // Exemple: 10x20x30 ou 10*20*30
    $regexNumeric = '/^\d+$/'; // Chiffres uniquement
    $regexTrancheAge = '/^\d+\+$/'; // Exemple: 9+

    // Validation des champs
    if (!preg_match($regexDimension, $dimension)) {
        $message = "Format de dimension invalide. Utilisez le format 10x20x30 ou 10*20*30.";
        $messageType = "danger";
    } elseif (!preg_match($regexNumeric, $seuil_min) || !preg_match($regexNumeric, $seuil_max) || !preg_match($regexNumeric, $quantite)) {
        $message = "Les seuils et la quantité doivent être des chiffres.";
        $messageType = "danger";
    } elseif (!preg_match($regexTrancheAge, $trancheAge)) {
        $message = "Format de tranche d'âge invalide. Utilisez le format nombre+ (ex: 9+).";
        $messageType = "danger";
    } else {
        try {
            // Commencer la transaction
            $conn->beginTransaction();

            // Insérer le stock dans la base de données
            $insertStockQuery = "INSERT INTO Stock (seuil_min, seuil_max, quantite) VALUES (?, ?, ?)";
            $insertStockStmt = $conn->prepare($insertStockQuery);
            $insertStockStmt->execute([$seuil_min, $seuil_max, $quantite]);
            $idStock = $conn->lastInsertId();

            // Insérer l'article dans la base de données
            $insertArticleQuery = "INSERT INTO Article (nomArticle, description, prix, poids, dimension, nbPièce, trancheAge, couleur, nouveaute, idCatégorie, idStock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $insertArticleStmt = $conn->prepare($insertArticleQuery);
            $insertArticleStmt->execute([$nomArticle, $description, $prix, $poids, $dimension, $nbPiece, $trancheAge, $couleur, $nouveaute, $idCatégorie, $idStock]);

            // Valider la transaction
            $conn->commit();
            $message = "Produit ajouté avec succès.";
            $messageType = "success";
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $conn->rollBack();
            $message = "Erreur lors de l'ajout du produit : " . $e->getMessage();
            $messageType = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Produit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'include/header.php'; ?>

    <div class="container mt-5">
        <h1>Ajouter Produit</h1>

        <?php if ($message) { echo "<div class='alert alert-$messageType'>$message</div>"; } ?>

        <form method="POST" action="ajouter_produit.php">
            <div class="mb-3">
                <label for="nomArticle" class="form-label">Nom du produit :</label>
                <input type="text" class="form-control" id="nomArticle" name="nomArticle" placeholder="Ex: Jouet en bois" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description :</label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Ex: Un jouet en bois pour enfants" required></textarea>
            </div>
            <div class="mb-3">
                <label for="prix" class="form-label">Prix :</label>
                <input type="number" class="form-control" id="prix" name="prix" step="0.01" placeholder="Ex: 19.99" required>
            </div>
            <div class="mb-3">
                <label for="poids" class="form-label">Poids :</label>
                <input type="number" class="form-control" id="poids" name="poids" step="0.001" placeholder="Ex: 0.5" required>
            </div>
            <div class="mb-3">
                <label for="dimension" class="form-label">Dimension :</label>
                <input type="text" class="form-control" id="dimension" name="dimension" placeholder="Ex: 10x20x30 ou 10*20*30" required>
            </div>
            <div class="mb-3">
                <label for="nbPiece" class="form-label">Nombre de pièces :</label>
                <input type="number" class="form-control" id="nbPiece" name="nbPiece" placeholder="Ex: 5" required>
            </div>
            <div class="mb-3">
                <label for="trancheAge" class="form-label">Tranche d'âge :</label>
                <input type="text" class="form-control" id="trancheAge" name="trancheAge" placeholder="Ex: 9+" required>
            </div>
            <div class="mb-3">
                <label for="couleur" class="form-label">Couleur :</label>
                <input type="text" class="form-control" id="couleur" name="couleur" placeholder="Ex: Rouge" required>
            </div>
            <div class="mb-3">
                <label for="nouveaute" class="form-label">Nouveauté :</label>
                <input type="checkbox" class="form-check-input" id="nouveaute" name="nouveaute">
            </div>
            <div class="mb-3">
                <label for="idCatégorie" class="form-label">Catégorie :</label>
                <select class="form-select" id="idCatégorie" name="idCatégorie" required>
                    <!-- Remplir les options de catégorie dynamiquement -->
                    <?php
                    $categoriesQuery = "SELECT idCat, nomCat FROM Catégorie";
                    $categoriesStmt = $conn->query($categoriesQuery);
                    while ($row = $categoriesStmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['idCat']}'>{$row['nomCat']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="seuil_min" class="form-label">Seuil Min :</label>
                <input type="number" class="form-control" id="seuil_min" name="seuil_min" placeholder="Ex: 10" required>
            </div>
            <div class="mb-3">
                <label for="seuil_max" class="form-label">Seuil Max :</label>
                <input type="number" class="form-control" id="seuil_max" name="seuil_max" placeholder="Ex: 100" required>
            </div>
            <div class="mb-3">
                <label for="quantite" class="form-label">Quantité :</label>
                <input type="number" class="form-control" id="quantite" name="quantite" placeholder="Ex: 50" required>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>