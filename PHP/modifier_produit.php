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

// Vérifier si l'ID du produit est passé en paramètre
if (!isset($_GET['id'])) {
    echo "ID de produit manquant.";
    exit();
}

$idArticle = $_GET['id'];

// Récupérer les détails du produit
$query = "SELECT * FROM Article WHERE idArticle = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$idArticle]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    echo "Produit non trouvé.";
    exit();
}

// Récupérer les informations de stock
$idStock = $article['idStock'];
$query = "SELECT * FROM Stock WHERE idStock = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$idStock]);
$stock = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les catégories
$query = "SELECT idCat, nomCat FROM Catégorie";
$catStmt = $conn->prepare($query);
$catStmt->execute();
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// Mettre à jour les détails du produit et du stock
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

    try {
        // Commencer la transaction
        $conn->beginTransaction();

        // Mettre à jour les détails du produit
        $updateArticleQuery = "UPDATE Article SET nomArticle = ?, description = ?, prix = ?, poids = ?, dimension = ?, nbPièce = ?, trancheAge = ?, couleur = ?, nouveaute = ?, idCatégorie = ? WHERE idArticle = ?";
        $updateArticleStmt = $conn->prepare($updateArticleQuery);
        $updateArticleStmt->execute([$nomArticle, $description, $prix, $poids, $dimension, $nbPiece, $trancheAge, $couleur, $nouveaute, $idCatégorie, $idArticle]);

        // Mettre à jour les informations de stock
        $updateStockQuery = "UPDATE Stock SET seuil_min = ?, seuil_max = ?, quantite = ? WHERE idStock = ?";
        $updateStockStmt = $conn->prepare($updateStockQuery);
        $updateStockStmt->execute([$seuil_min, $seuil_max, $quantite, $idStock]);

        // Valider la transaction
        $conn->commit();

        $message = "Produit et stock mis à jour avec succès.";
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $conn->rollBack();
        $message = "Erreur lors de la mise à jour du produit : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Produit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'include/header.php'; ?>

    <div class="container mt-5">
        <h1>Modifier Produit</h1>

        <?php if (isset($message)) { echo "<div class='alert alert-success'>$message</div>"; } ?>

        <form method="POST" action="modifier_produit.php?id=<?php echo $idArticle; ?>">
            <div class="mb-3">
                <label for="nomArticle" class="form-label">Nom du produit :</label>
                <input type="text" class="form-control" id="nomArticle" name="nomArticle" value="<?php echo htmlspecialchars($article['nomArticle']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description :</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($article['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="prix" class="form-label">Prix :</label>
                <input type="number" class="form-control" id="prix" name="prix" step="0.01" value="<?php echo htmlspecialchars($article['prix']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="poids" class="form-label">Poids :</label>
                <input type="number" class="form-control" id="poids" name="poids" step="0.001" value="<?php echo htmlspecialchars($article['poids']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="dimension" class="form-label">Dimension :</label>
                <input type="text" class="form-control" id="dimension" name="dimension" value="<?php echo htmlspecialchars($article['dimension']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="nbPiece" class="form-label">Nombre de pièces :</label>
                <input type="number" class="form-control" id="nbPiece" name="nbPiece" value="<?php echo htmlspecialchars($article['nbPièce']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="trancheAge" class="form-label">Tranche d'âge :</label>
                <input type="text" class="form-control" id="trancheAge" name="trancheAge" value="<?php echo htmlspecialchars($article['trancheAge']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="couleur" class="form-label">Couleur :</label>
                <input type="text" class="form-control" id="couleur" name="couleur" value="<?php echo htmlspecialchars($article['couleur']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="nouveaute" class="form-label">Nouveauté :</label>
                <input type="checkbox" class="form-check-input" id="nouveaute" name="nouveaute" <?php echo $article['nouveaute'] ? 'checked' : ''; ?>>
            </div>
            <div class="mb-3">
                <label for="idCatégorie" class="form-label">Catégorie :</label>
                <select class="form-select" id="idCatégorie" name="idCatégorie" required>
                    <?php
                    foreach ($categories as $category) {
                        $selected = $category['idCat'] == $article['idCatégorie'] ? 'selected' : '';
                        echo "<option value='{$category['idCat']}' $selected>{$category['nomCat']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="seuil_min" class="form-label">Seuil Min :</label>
                <input type="number" class="form-control" id="seuil_min" name="seuil_min" value="<?php echo htmlspecialchars($stock['seuil_min']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="seuil_max" class="form-label">Seuil Max :</label>
                <input type="number" class="form-control" id="seuil_max" name="seuil_max" value="<?php echo htmlspecialchars($stock['seuil_max']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="quantite" class="form-label">Quantité :</label>
                <input type="number" class="form-control" id="quantite" name="quantite" value="<?php echo htmlspecialchars($stock['quantite']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>