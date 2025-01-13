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

// Supprimer l'article
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $deleteQuery = "DELETE FROM Article WHERE idArticle = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->execute([$id]);

    $_SESSION['message'] = "Produit supprimé avec succès.";
    header('Location: gestion_produits.php');
    exit();
}

// Récupérer les articles
$query = "SELECT * FROM Article";
$stmt = $conn->prepare($query);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Produits</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php include 'include/header.php'; ?>

    <div class="container mt-5">
        <div class="admin-header text-center p-3 mb-4">
            <h1>Gestion des Produits</h1>
        </div>
        <div class="d-flex justify-content-end mb-3">
            <a href="ajouter_produit.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Ajouter un produit</a>
        </div>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Prix</th>
                    <th>Catégorie</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($article['idArticle']); ?></td>
                        <td><?php echo htmlspecialchars($article['nomArticle']); ?></td>
                        <td><?php echo htmlspecialchars($article['description']); ?></td>
                        <td><?php echo htmlspecialchars($article['prix']); ?></td>
                        <td><?php echo htmlspecialchars($article['idCatégorie']); ?></td>
                        <td>
                            <a href="modifier_produit.php?id=<?php echo $article['idArticle']; ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i> Modifier</a>
                            <form method="post" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">
                                <input type="hidden" name="id" value="<?php echo $article['idArticle']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i> Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>