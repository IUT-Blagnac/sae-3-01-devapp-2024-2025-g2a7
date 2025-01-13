<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['gift_wrap'] = isset($_POST['gift_wrap']);
    $_SESSION['delivery'] = $_POST['delivery'] ?? 'standard';
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

include("./include/Connect.inc.php");

$loggedIn = isset($_SESSION['idUtilisateur']);
$panierItems = [];
$totalMontant = 0;
$giftWrapPrice = 2.70;
$deliveryStandard = 4.90;
$deliveryExpress = 9.90;
$giftWrapChecked = isset($_SESSION['gift_wrap']) ? $_SESSION['gift_wrap'] : false;
$selectedDelivery = isset($_SESSION['delivery']) ? $_SESSION['delivery'] : 'standard';

if ($loggedIn) {
    $idUtilisateur = $_SESSION['idUtilisateur'];
    $stmt = $conn->prepare("CALL GetPanierDetails(?)");
    $stmt->execute([$idUtilisateur]);
    $panierItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($panierItems)) {
        $_SESSION['idPanier'] = $panierItems[0]['idPanier'];
    }
} else {
    $panierItems = isset($_SESSION['panier']) ? $_SESSION['panier'] : [];
}

// Calculate total
foreach ($panierItems as $item) {
    $totalMontant += $item['prix'] * $item['quantité'];
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre Panier</title>
    <?php
        include("./include/header.php"); 
    ?>
    <link rel="stylesheet" href="stylePanier.css">
    
</head>
<body>
    <div id="page-panier">
    <div class="container">
        <h1>Votre Panier</h1>

        <?php if (empty($panierItems)): ?>
            <p>Votre panier est vide.</p>
            <a href="catalogue.php" class="btn">Explorer nos produits</a>
        <?php else: ?>
            <div class="panier-items">
                <?php foreach ($panierItems as $item): ?>
                    <div class="panier-item">
                    <a href="produit.php?id=<?= $item['idArticle'] ?>">
                        <img src="images/articles/prod<?php echo $item['idArticle']; ?>.jpg" 
                            alt="<?= htmlspecialchars($item['nomArticle']) ?>" 
                            class="item-image">
                        </a>
                        <div class="item-details">
                            <a href="produit.php?id=<?= $item['idArticle'] ?>" class="item-link">
                                <p class="item-name"><?= htmlspecialchars($item['nomArticle']) ?></p>
                            </a>
                            <p class="item-price"><?= number_format($item['prix'], 2) ?> €</p>
                            <p class="item-quantity">Quantité: <?= $item['quantité'] ?></p>
                            <p class="item-subtotal">Sous-total: <?= number_format($item['prix'] * $item['quantité'], 2) ?> €</p>
                        </div>
                        <div class="item-actions">
                            <?php
                            // Récupérer la quantité en stock pour cet article
                            $stmt = $conn->prepare("SELECT s.quantite FROM Stock s JOIN Article a ON s.idStock = a.idStock WHERE a.idArticle = ?");
                            $stmt->execute([$item['idArticle']]);
                            $stock = $stmt->fetch(PDO::FETCH_ASSOC);
                            $quantiteDisponible = $stock ? $stock['quantite'] : 0;
                            ?>

                            <div class="quantity-actions">
                                <?php if ($item['quantité'] < $quantiteDisponible): ?>
                                    <a href="modifierQuantite.php?id=<?= $item['idArticle'] ?>&action=augmenter" class="btn-action">+</a>
                                <?php else: ?>
                                    <button class="btn-action" disabled>+</button>
                                <?php endif; ?>
                                <a href="modifierQuantite.php?id=<?= $item['idArticle'] ?>&action=diminuer" class="btn-action">-</a>
                            </div>
                            <a href="supprimerArticle.php?id=<?= $item['idArticle'] ?>" class="btn-action delete">Supprimer</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="gift-wrap mb-3">
                <form method="POST">
                    <div class="form-check">
                        <input type="checkbox" name="gift_wrap" class="form-check-input" id="giftWrap" 
                               value="1"
                               <?php echo $giftWrapChecked ? 'checked' : ''; ?> 
                               onchange="this.form.submit()">
                        <label class="form-check-label" for="giftWrap">
                            Emballage cadeau (+2,70€)
                        </label>
                    </div>
                </form>
            </div>
            
            
            <div class="delivery-options mb-3">
                <form method="POST">
                    <div class="form-check">
                        <input type="radio" name="delivery" value="standard" class="form-check-input" id="deliveryStandard" 
                               <?php echo $selectedDelivery === 'standard' ? 'checked' : ''; ?> 
                               onchange="this.form.submit()">
                        <label class="form-check-label" for="deliveryStandard">
                            Livraison standard (4,90€)
                        </label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="delivery" value="express" class="form-check-input" id="deliveryExpress" 
                               <?php echo $selectedDelivery === 'express' ? 'checked' : ''; ?> 
                               onchange="this.form.submit()">
                        <label class="form-check-label" for="deliveryExpress">
                            Livraison express (9,90€)
                        </label>
                    </div>
                </form>
            </div>

            <div class="panier-summary">
                <p>Total: <strong><?= number_format($totalMontant, 2) ?> €</strong></p>
                <div class="actions">
                    <a href="catalogue.php" class="btn">Continuer vos achats</a>
                    <?php if (isset($_SESSION['nom']) && !empty($panierItems)): ?>
                        <a href="commander.php?panier=<?= $_SESSION['idPanier'] ?>" class="btn">Passer la commande</a>
                    <?php elseif (!isset($_SESSION['nom'])): ?>
                        <p class="text-danger">Connectez-vous pour commander</p>
                    <?php elseif ($totalArticles == 0): ?>
                        <p class="text-danger">Votre panier est vide</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
<footer>
<?php
include "include/footer.php";
?>
</footer> 
</html>
