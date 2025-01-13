<?php
session_start();
include("./include/Connect.inc.php");

// Vérifie si l'utilisateur est connecté
$loggedIn = isset($_SESSION['idUtilisateur']);
$idUtilisateur = $loggedIn ? $_SESSION['idUtilisateur'] : null;
$idArticle = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Si l'article n'est pas trouvé, redirige vers le panier
if ($idArticle <= 0 || !in_array($action, ['augmenter', 'diminuer'])) {
    header('Location: panier.php');
    exit();
}

if ($loggedIn) {
    // Modification de la quantité dans la base de données
    $stmt = $conn->prepare("
        SELECT dp.idArticle, dp.quantité, a.nbPièce
        FROM DétailPanier dp
        JOIN Article a ON dp.idArticle = a.idArticle
        WHERE dp.idArticle = ? AND dp.idPanier IN (SELECT idPanier FROM Panier WHERE idUtilisateur = ?)
    ");
    $stmt->execute([$idArticle, $idUtilisateur]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        $quantité = $item['quantité'];
        $stockDisponible = $item['nbPièce'];

        // Augmenter la quantité
        if ($action == 'augmenter' && $quantité < $stockDisponible) {
            $newQuantité = $quantité + 1;
            $stmt = $conn->prepare("UPDATE DétailPanier SET quantité = ? WHERE idArticle = ? AND idPanier IN (SELECT idPanier FROM Panier WHERE idUtilisateur = ?)");
            $stmt->execute([$newQuantité, $idArticle, $idUtilisateur]);
        }
        // Diminuer la quantité
        elseif ($action == 'diminuer' && $quantité > 1) {
            $newQuantité = $quantité - 1;
            $stmt = $conn->prepare("UPDATE DétailPanier SET quantité = ? WHERE idArticle = ? AND idPanier IN (SELECT idPanier FROM Panier WHERE idUtilisateur = ?)");
            $stmt->execute([$newQuantité, $idArticle, $idUtilisateur]);
        }
    }
} else {
    // Utilisateur non connecté : modification uniquement en session
    if (isset($_SESSION['panier'][$idArticle])) {
        $quantité = $_SESSION['panier'][$idArticle]['quantité'];
        $prix = $_SESSION['panier'][$idArticle]['prix'];

        if ($action == 'augmenter') {
            $_SESSION['panier'][$idArticle]['quantité'] = $quantité + 1;
        } elseif ($action == 'diminuer' && $quantité > 1) {
            $_SESSION['panier'][$idArticle]['quantité'] = $quantité - 1;
        }
    }
}

// Redirige vers le panier après modification
header('Location: panier.php');
exit();
?>
