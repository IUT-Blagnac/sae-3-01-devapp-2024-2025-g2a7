<?php
session_start();
include("./include/Connect.inc.php");

if (!isset($_SESSION['nom']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: panier.php');
    exit;
}

$tableName = "D\xc3\xa9tailPanier";
$colonne = "quantit\xc3\xa9";

$stmt = $conn->prepare("
    SELECT p.idPanier, dp.idArticle, a.nomArticle, a.prix, dp.{$colonne} as quantite
    FROM `{$tableName}` dp 
    JOIN Article a ON dp.idArticle = a.idArticle 
    JOIN Panier p ON dp.idPanier = p.idPanier 
    WHERE p.idUtilisateur = ?
");
$stmt->execute([$_SESSION['idUtilisateur']]);
$panierItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalMontant = 0;
foreach ($panierItems as $item) {
    $totalMontant += $item['prix'] * $item['quantite'];
}

$giftWrapPrice = 2.70;
$deliveryStandard = 4.90;
$deliveryExpress = 9.90;
$giftWrapChecked = isset($_SESSION['gift_wrap']) ? $_SESSION['gift_wrap'] : false;
$selectedDelivery = isset($_SESSION['delivery']) ? $_SESSION['delivery'] : 'Standard';

if ($giftWrapChecked) {
    $totalMontant += $giftWrapPrice;
}
$totalMontant += ($selectedDelivery === 'Express') ? $deliveryExpress : $deliveryStandard;

try {
    $conn->beginTransaction();

    // Récupérer le dernier idCommande
    $stmt = $conn->query("SELECT MAX(idCommande) AS maxId FROM Commande");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $idCommande = $result['maxId'] + 1;

    // Récupérer idAdresse de l'utilisateur
    $stmt = $conn->prepare("SELECT idAdresse FROM Adresse WHERE idUtilisateur = ?");
    $stmt->execute([$_SESSION['idUtilisateur']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $idAdresse = $result['idAdresse'];

    // Insert commande
    $stmt = $conn->prepare("
        INSERT INTO Commande (idCommande, dateCommande, idPanier) 
        VALUES (?, DATE_FORMAT(NOW(), '%Y-%m-%d'), ?)
    ");
    $stmt->execute([$idCommande, $_SESSION['idPanier']]);

    // // Insert paiement
    // $stmt = $conn->prepare("
    //     INSERT INTO Paiement (typeP, date, montantTotal, idCommande, idCarte) 
    //     VALUES ('CB', NOW(), ?, ?, NULL)
    // ");
    // $stmt->execute([$totalMontant, $idCommande]);


    // Récupérer le dernier idLivraison
    $stmt = $conn->query("SELECT MAX(idLivraison) AS maxId FROM Livraison");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $idLivraison = $result['maxId'] + 1;

    // Insert livraison
    $stmt = $conn->prepare("
        INSERT INTO Livraison (idLivraison, typeL, MontantLiv, idCommande, idAdresse) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $montantLivraison = ($selectedDelivery === 'express' ? $deliveryExpress : $deliveryStandard);
    $stmt->execute([
        $idLivraison,
        $selectedDelivery, 
        $montantLivraison,
        $idCommande,
        $idAdresse
    ]);

    // Récupérer le dernier idPanier
    $stmt = $conn->query("SELECT MAX(idPanier) AS maxId FROM Panier");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $newPanierId = $result['maxId'] + 1;

    // Créer un nouveau panier
    $stmt = $conn->prepare("
        INSERT INTO Panier (idPanier, montant, nbArticle, idUtilisateur) 
        VALUES (?, 0, 0, ?)
    ");
    $stmt->execute([$newPanierId, $_SESSION['idUtilisateur']]);

    // Commit transaction
    $conn->commit();

    // Réinitialiser l'ID du panier dans la session
    $_SESSION['idPanier'] = $newPanierId;

    // Redirection vers la page de confirmation
    header('Location: confirmCommande.php');
    exit();

} catch(PDOException $e) {
    $conn->rollBack();
    error_log("Erreur transaction: " . $e->getMessage());
    throw new Exception("Erreur lors de l'enregistrement de la commande: " . $e->getMessage());
} catch(Exception $e) {
    $conn->rollBack();
    error_log("Erreur: " . $e->getMessage());
    throw new Exception("Erreur: " . $e->getMessage());
}
