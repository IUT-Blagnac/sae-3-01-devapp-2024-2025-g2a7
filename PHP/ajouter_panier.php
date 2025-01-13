<?php
session_start();
include("./include/Connect.inc.php");


// Vérification que l'ID de l'article est présent
if (!isset($_GET['id'])) {
    die("Erreur : Aucun article spécifié.");
}

$idArticle = intval($_GET['id']);

// Vérification si l'utilisateur est connecté
if (isset($_SESSION['idUtilisateur'])) {
    // Cas où l'utilisateur est connecté
    $idUtilisateur = $_SESSION['idUtilisateur'];    

    // Récupérer le panier avec l'ID le plus élevé pour cet utilisateur
    $stmt = $conn->prepare("SELECT MAX(idPanier) AS maxId FROM Panier WHERE idUtilisateur = ?");
    $stmt->execute([$idUtilisateur]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $idPanier = $result['maxId'];

    if (!$idPanier) {
        // Si aucun panier n'existe, en créer un nouveau
        $stmt = $conn->query("SELECT MAX(idPanier) AS maxId FROM Panier");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $idPanier = $result['maxId'] + 1;

        $stmt = $conn->prepare("INSERT INTO Panier (idPanier, montant, nbArticle, idUtilisateur) VALUES (?, 0, 0, ?)");
        $stmt->execute([$idPanier, $idUtilisateur]);
    }

    // Vérifier si l'article existe déjà dans le panier
    $stmt = $conn->prepare("SELECT quantité FROM DétailPanier WHERE idPanier = ? AND idArticle = ?");
    $stmt->execute([$idPanier, $idArticle]);
    $articleInPanier = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($articleInPanier) {
        // Si l'article existe déjà, mettre à jour la quantité
        $stmt = $conn->prepare("UPDATE DétailPanier SET quantité = quantité + 1 WHERE idPanier = ? AND idArticle = ?");
        $stmt->execute([$idPanier, $idArticle]);
    } else {
        // Ajouter l'article avec une quantité de 1
        $stmt = $conn->prepare("INSERT INTO DétailPanier (idPanier, idArticle, quantité) VALUES (?, ?, 1)");
        $stmt->execute([$idPanier, $idArticle]);
    }

    header("Location: panier.php?success=1");
    exit;

} else {
    // Cas où l'utilisateur n'est pas connecté
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    if (isset($_SESSION['panier'][$idArticle])) {
        // Si l'article est déjà dans le panier, augmenter la quantité
        $_SESSION['panier'][$idArticle]['quantité'] += 1;
    } else {
        // Ajouter l'article avec une quantité de 1
        $stmt = $conn->prepare("SELECT idArticle, nomArticle, prix FROM Article WHERE idArticle = ?");
        $stmt->execute([$idArticle]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$article) {
            die("Erreur : Article non trouvé.");
        }

        $_SESSION['panier'][$idArticle] = [
            'idArticle' => $article['idArticle'],
            'nomArticle' => $article['nomArticle'],
            'prix' => $article['prix'],
            'quantité' => 1
        ];
    }

    header("Location: panier.php?success=1");
    exit;
}
?>
