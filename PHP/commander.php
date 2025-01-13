<?php
session_start();
include("./include/Connect.inc.php");

if (!isset($_SESSION['nom']) || !isset($_GET['panier'])) {
    header('Location: panier.php');
    exit;
}

$idPanier = $_GET['panier'];
$totalMontant = 0;
$giftWrapPrice = 2.70;
$deliveryStandard = 4.90;
$deliveryExpress = 9.90;
$giftWrapChecked = isset($_SESSION['gift_wrap']) ? $_SESSION['gift_wrap'] : false;
$selectedDelivery = isset($_SESSION['delivery']) ? $_SESSION['delivery'] : 'standard';

$tableName = "D\xc3\xa9tailPanier";
$colonne = "quantit\xc3\xa9";
$stmt = $conn->prepare("
    SELECT dp.idArticle, a.nomArticle, a.prix, ad.ville, ad.codePostal, ad.rue, dp.{$colonne} as quantite
    FROM {$tableName} dp 
    JOIN Article a ON dp.idArticle = a.idArticle 
    JOIN Panier p ON dp.idPanier = p.idPanier 
    JOIN Adresse ad ON p.idUtilisateur = ad.idUtilisateur
    WHERE dp.idPanier = ?
");
$stmt->execute([$idPanier]);
$panierItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($panierItems as $item) {
    $totalMontant += $item['prix'] * $item['quantite'];
}

if ($giftWrapChecked) {
    $totalMontant += $giftWrapPrice;
}
$totalMontant += ($selectedDelivery === 'express') ? $deliveryExpress : $deliveryStandard;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement - Brickolo</title>
    <?php include("./include/header.php"); ?>
    <link rel="stylesheet" href="styleCommande.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4 order-md-2 mb-4">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-primary">R&eacute;sum&eacute; de votre commande</span>
                    <span class="badge bg-primary rounded-pill"><?= count($panierItems) ?></span>
                </h4>
                <ul class="list-group mb-3">
                    <?php foreach ($panierItems as $item): ?>
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div>
                                <h6 class="my-0"><?= htmlspecialchars($item['nomArticle']) ?></h6>
                                <small class="text-muted">Quantit&eacute; : <?= $item['quantite'] ?></small>
                            </div>
                            <span class="text-muted"><?= number_format($item['prix'] * $item['quantite'], 2) ?> &euro;</span>
                        </li>
                    <?php endforeach; ?>

                    <?php if ($giftWrapChecked): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Emballage cadeau</span>
                            <span><?= number_format($giftWrapPrice, 2) ?> &euro;</span>
                        </li>
                    <?php endif; ?>

                    <li class="list-group-item d-flex justify-content-between">
                        <span>Livraison (<?= $selectedDelivery === 'express' ? 'Express' : 'Standard' ?>)</span>
                        <span><?= number_format($selectedDelivery === 'express' ? $deliveryExpress : $deliveryStandard, 2) ?> &euro;</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        <span>Total</span>
                        <strong><?= number_format($totalMontant, 2) ?> &euro;</strong>
                    </li>
                </ul>
            </div>

            <!-- Formulaire de paiement -->
            <div class="col-md-8 order-md-1">
                <h4 class="mb-3">Informations de paiement</h4>
                <form class="needs-validation" method="POST" action="paiement.php">
                    <!-- Adresse de livraison -->
                    <div class="mb-3">
                        <label for="address">Adresse</label>
                        <input type="text" 
                               class="form-control" 
                               id="address" 
                               name="address" 
                               value="<?= (!empty($panierItems) && isset($panierItems[0]['rue']) && $panierItems[0]['rue'] !== null) ? htmlspecialchars($panierItems[0]['rue']) : '' ?>" 
                               placeholder="Votre adresse"
                               required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city">Ville</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="city" 
                                   name="city" 
                                   value="<?= (!empty($panierItems) && isset($panierItems[0]['ville']) && $panierItems[0]['ville'] !== null) ? htmlspecialchars($panierItems[0]['ville']) : '' ?>" 
                                   placeholder="Votre ville"
                                   required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="postal">Code postal</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="postal" 
                                   name="postal" 
                                   value="<?= (!empty($panierItems) && isset($panierItems[0]['codePostal']) && $panierItems[0]['codePostal'] !== null) ? htmlspecialchars($panierItems[0]['codePostal']) : '' ?>" 
                                   placeholder="Code postal"
                                   required>
                        </div>
                    </div>

                    <hr class="mb-4">

                    <!-- M�thode de paiement -->
                    <h4 class="mb-3">Paiement</h4>
                    <div class="d-block my-3">
                        <div class="custom-control custom-radio">
                            <input type="radio" name="paymentMethod" class="custom-control-input" id="credit" checked required>
                            <label class="custom-control-label" for="credit">Carte de cr&eacute;dit</label>
                        </div>
                    </div>

                    <!-- Informations carte -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cc-name">Nom sur la carte</label>
                            <input type="text" class="form-control" id="cc-name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cc-number">Num&eacute;ro de carte</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="cc-number" 
                                   pattern="[0-9]{16}"
                                   maxlength="16"
                                   placeholder="1234567890123456"
                                   title="Le num&eacute;ro de carte doit contenir 16 chiffres."
                                   required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="cc-expiration">Expiration</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="cc-expiration" 
                                   pattern="(0[1-9]|1[0-2])\/([0-9]{2})"
                                   maxlength="5"
                                   placeholder="MM/YY"
                                   title="La date d'expiration doit �tre au format MM/YY."
                                   required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="cc-cvv">CVV</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="cc-cvv" 
                                   pattern="[0-9]{3}"
                                   maxlength="3"
                                   placeholder="123"
                                   title="Le CVV doit contenir 3 chiffres."
                                   required>
                        </div>
                    </div>
                    
                    <hr class="mb-4">
                    <button class="btn btn-primary btn-lg btn-block" type="submit">Confirmer la commande</button>
                </form>
            </div>
        </div>
    </div>
    <br/>
    <?php include "include/footer.php"; ?>
</body>
</html>