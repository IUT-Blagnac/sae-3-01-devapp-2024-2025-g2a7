<?php
session_start();
include("./include/Connect.inc.php");

// Vérifie si l'utilisateur est connecté
$loggedIn = isset($_SESSION['idUtilisateur']);
$idUtilisateur = $loggedIn ? $_SESSION['idUtilisateur'] : null;
$idArticle = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Si l'article n'est pas trouvé, redirige vers le panier
if ($idArticle <= 0) {
    header('Location: panier.php');
    exit();
}

if ($loggedIn) {
    // Suppression de l'article dans la base de données
    $stmt = $conn->prepare("
        DELETE dp
        FROM DétailPanier dp
        JOIN Panier p ON dp.idPanier = p.idPanier
        WHERE dp.idArticle = ? AND p.idUtilisateur = ?
    ");
    $stmt->execute([$idArticle, $idUtilisateur]);
} else {
    // Utilisateur non connecté : suppression uniquement en session
    if (isset($_SESSION['panier'][$idArticle])) {
        unset($_SESSION['panier'][$idArticle]);
    }
}

// Redirige vers le panier après suppression
header('Location: panier.php');
exit();
?>
