<?php
session_start();
require 'include/Connect.inc.php';

if (isset($_SESSION['nom'])) {
    $login = $_SESSION['nom'];

    // Récupérer les commandes et les détails des articles liés à l'utilisateur
    $query = "
        SELECT c.idCommande, c.dateCommande, p.montant, p.nbArticle, a.idArticle, a.nomArticle, dp.quantité, l.statut
        FROM Commande c
        JOIN Panier p ON c.idPanier = p.idPanier
        JOIN Utilisateur u ON p.idUtilisateur = u.idUtilisateur
        JOIN DétailPanier dp ON p.idPanier = dp.idPanier
        JOIN Article a ON dp.idArticle = a.idArticle
        JOIN Livraison l ON c.idCommande = l.idCommande
        WHERE u.email = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute([$login]);
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Commandes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'include/header.php'; ?>

    <div class="container mt-5">
        <h2>Mes Commandes</h2>

        <?php
        if (count($commandes) > 0) {
            $currentCommande = null;
            foreach ($commandes as $commande) {
                if ($currentCommande !== $commande['idCommande']) {
                    if ($currentCommande !== null) {
                        echo '</tbody></table>';
                    }
                    $currentCommande = $commande['idCommande'];
                    echo '<h3>Commande n°' . htmlspecialchars($commande['idCommande']) . ' - Date : ' . htmlspecialchars($commande['dateCommande']) . ' - Statut : ' . htmlspecialchars($commande['statut']) . '</h3>';
                    echo '<table class="table table-striped">';
                    echo '<thead><tr><th>Article</th><th>Quantité</th></tr></thead>';
                    echo '<tbody>';
                }
                echo '<tr>';
                echo '<td><a href="produit.php?id=' . htmlspecialchars($commande['idArticle']) . '">' . htmlspecialchars($commande['nomArticle']) . '</a></td>';
                echo '<td>' . htmlspecialchars($commande['quantité']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<div class="alert alert-info">Aucune commande trouvée pour cet utilisateur.</div>';
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
<footer>
    <?php include 'include/footer.php'; ?>
</footer>
</html>