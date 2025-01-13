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

// Récupérer toutes les catégories pour le champ de sélection des catégories à supprimer
$allCategoriesQuery = "SELECT idCat, nomCat FROM Catégorie";
$allCategoriesStmt = $conn->prepare($allCategoriesQuery);
$allCategoriesStmt->execute();
$allCategories = $allCategoriesStmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire d'ajout de catégorie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete'])) {
    if (isset($_POST['nom_categorie']) && !empty($_POST['nom_categorie'])) {
        $nomCategorie = $_POST['nom_categorie'];
        $description = isset($_POST['description']) ? $_POST['description'] : null;
        $isSubCategory = isset($_POST['is_sub_category']) ? 1 : 0;
        $parentCategoryId = isset($_POST['parent_category']) ? $_POST['parent_category'] : null;

        $insertQuery = "INSERT INTO Catégorie (nomCat, description, idCatPere) VALUES (?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->execute([$nomCategorie, $description, $parentCategoryId]);
        $_SESSION['message'] = "Nouvelle catégorie ajoutée avec succès.";

        // Redirection après l'ajout pour éviter la soumission en double
        header('Location: administrateur.php');
        exit();
    } else {
        $_SESSION['message'] = "Veuillez entrer un nom de catégorie.";
        header('Location: administrateur.php');
        exit();
    }
}

// Traitement du formulaire de suppression de catégorie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    if (isset($_POST['nom_categorie']) && !empty($_POST['nom_categorie'])) {
        $nomCategorie = $_POST['nom_categorie'];
        $deleteQuery = "DELETE FROM Catégorie WHERE nomCat = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->execute([$nomCategorie]);
        $_SESSION['message'] = "Catégorie supprimée avec succès.";

        // Redirection après la suppression pour éviter la soumission en double
        header('Location: administrateur.php');
        exit();
    } else {
        $_SESSION['message'] = "Veuillez entrer un nom de catégorie.";
        header('Location: administrateur.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Administrateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        function toggleParentCategory() {
            var isSubCategory = document.getElementById('is_sub_category').checked;
            var parentCategoryDiv = document.getElementById('parent_category_div');
            if (isSubCategory) {
                parentCategoryDiv.style.display = 'block';
            } else {
                parentCategoryDiv.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <?php include 'include/header.php'; ?>

    <div class="container mt-5">
        <div class="admin-header text-center p-3 mb-4">
            <h1>Espace Administrateur</h1>
        </div>

        <?php
        if (isset($_SESSION['message'])) {
            echo "<div class='alert alert-success'>{$_SESSION['message']}</div>";
            unset($_SESSION['message']);
        }
        ?>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h2>Ajouter une nouvelle catégorie</h2>
                    </div>
                    <div class="card-body">
                        <form class="admin-form" method="POST" action="administrateur.php">
                            <div class="mb-3">
                                <label for="nom_categorie" class="form-label">Nom de la catégorie :</label>
                                <input type="text" id="nom_categorie" name="nom_categorie" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description (optionnelle) :</label>
                                <textarea id="description" name="description" class="form-control"></textarea>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="is_sub_category" name="is_sub_category" onclick="toggleParentCategory()">
                                <label class="form-check-label" for="is_sub_category">Est-ce une sous-catégorie ?</label>
                            </div>
                            <div class="mb-3" id="parent_category_div" style="display: none;">
                                <label for="parent_category" class="form-label">Catégorie parente :</label>
                                <select id="parent_category" name="parent_category" class="form-select">
                                    <option value="">Sélectionner une catégorie</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['idCat']; ?>"><?php echo $category['nomCat']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="admin-buttons">
                                <button type="submit" class="btn btn-brickolo">Ajouter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h2>Supprimer une catégorie</h2>
                    </div>
                    <div class="card-body">
                        <form class="admin-form" method="POST" action="administrateur.php">
                            <div class="mb-3">
                                <label for="nom_categorie" class="form-label">Nom de la catégorie :</label>
                                <select id="nom_categorie" name="nom_categorie" class="form-select" required>
                                    <option value="">Sélectionner une catégorie</option>
                                    <?php foreach ($allCategories as $category): ?>
                                        <option value="<?php echo $category['nomCat']; ?>"><?php echo $category['nomCat']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="admin-buttons">
                                <button type="submit" name="delete" class="btn btn-brickolo btn-brickolo-secondary">Supprimer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h2>Gestion des produits</h2>
                    </div>
                    <div class="card-body">
                        <div class="admin-links">
                            <a href="gestion_produits.php" class="btn btn-brickolo admin-link">Gérer les produits</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h2>Gestion des utilisateurs</h2>
                    </div>
                    <div class="card-body">
                        <div class="admin-links">
                            <a href="gestion_utilisateurs.php" class="btn btn-brickolo admin-link">Gérer les utilisateurs</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>