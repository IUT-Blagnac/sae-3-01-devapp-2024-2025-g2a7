<?php
session_start(); 
include "include/header.php";
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['idUtilisateur'])) {
    $isConnected = false;
} else {
    $isConnected = true;
    $idUtilisateur = $_SESSION['idUtilisateur']; // Récupère l'ID utilisateur depuis la session
}

$commande = false; 

if ($isConnected) {
    $queryCommande = $conn->prepare("
        SELECT c.idCommande 
        FROM Commande c
        INNER JOIN Panier p ON c.idPanier = p.idPanier
        INNER JOIN DétailPanier dp ON dp.idPanier = p.idPanier
        WHERE dp.idArticle = :idArticle AND p.idUtilisateur = :idUtilisateur
    ");
    $queryCommande->execute([
        'idArticle' => $productId,
        'idUtilisateur' => $idUtilisateur
    ]);

    $commande = $queryCommande->rowCount() > 0;

    // Vérifie si une consultation pour ce produit existe déjà
    $queryCheck = $conn->prepare("
        SELECT * FROM HistoriqueConsultations 
        WHERE idUtilisateur = :idUtilisateur AND idArticle = :idArticle
    ");
    $queryCheck->execute([
        'idUtilisateur' => $idUtilisateur,
        'idArticle' => $productId
    ]);

    if ($queryCheck->rowCount() > 0) {
        // Met à jour la date de consultation si elle existe déjà
        $queryUpdate = $conn->prepare("
            UPDATE HistoriqueConsultations 
            SET dateConsultation = NOW() 
            WHERE idUtilisateur = :idUtilisateur AND idArticle = :idArticle
        ");
        $queryUpdate->execute([
            'idUtilisateur' => $idUtilisateur,
            'idArticle' => $productId
        ]);
    } else {
        // Insère une nouvelle consultation si elle n'existe pas encore
        $queryInsert = $conn->prepare("
            INSERT INTO HistoriqueConsultations (idUtilisateur, idArticle, dateConsultation)
            VALUES (:idUtilisateur, :idArticle, NOW())
        ");
        $queryInsert->execute([
            'idUtilisateur' => $idUtilisateur,
            'idArticle' => $productId
        ]);
    }
}

if ($productId > 0) {
    $query = $conn->prepare("SELECT a.*, s.quantite FROM Article a INNER JOIN Stock s ON a.idStock = s.idStock  WHERE idArticle = :id");
    $query->execute(['id' => $productId]);
    $produit = $query->fetch(PDO::FETCH_ASSOC);

    if ($produit) {
        $idArticle = $produit['idArticle'];
        $nomArticle = $produit['nomArticle'];
        $description = $produit['description'];
        $prix = $produit['prix'];
        $poids = $produit['poids'];
        $dimension = $produit['dimension'];
        $trancheAge = $produit['trancheAge'];
        $couleur = $produit['couleur'];
        $quantite = $produit['quantite'];

        $imagePath = "images/articles/prod{$idArticle}.jpg";
    } else {
        echo "<p class='text-danger'>Produit introuvable.</p>";
        exit; 
    }
} else {
    echo "<p class='text-danger'>Aucun produit s�lectionn�.</p>";
    exit;
}


$queryAvis = $conn->prepare("
    SELECT a.*, u.prenom 
    FROM Avis a 
    LEFT JOIN Utilisateur u ON a.idUtilisateur = u.idUtilisateur 
    WHERE a.idArticle = :id
");
$queryAvis->execute(['id' => $productId]);
$avis = $queryAvis->fetchAll(PDO::FETCH_ASSOC);

// Calcul de la note moyenne du prod
$totalRating = 0;
$numberOfReviews = count($avis);

if ($numberOfReviews > 0) {
    foreach($avis as $review) {
        $totalRating += intval($review['note']);
    }
    $averageRating = round($totalRating / $numberOfReviews, 1);
} else {
    $averageRating = 0;
}
    
?>


<style>
/* style unique � cette page*/
@import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap');


 h2,h3 {
    font-family: 'Open Sans', sans-serif;
}

.main-content {
    min-height: 10vh;
    margin-bottom: 2rem;
}


body {
    background-color: #bbebfb; 
}

.card-wrapper {
    display: flex;
    justify-content: center;
    margin: 20px;
}

.card {
    display: flex;
    flex-direction: row;
    border: 1px solid #ddd;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 800px;
    background-color: white;
}

/* Image du produit */
.product-imgs {
    flex: 1;
    padding: 20px;
}

.img-display {
    max-width: 100%;
    text-align: center;
}

.product-imgs img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
}

/* Contenu du produit */
.product-content {
    flex: 2;
    padding: 20px;
    color: #333;
}

.product-title {
    font-size: 32px;
    color: #333;
    margin: 10px 0;
}

.product-rating {
    font-size: 20px;
    color: #ffcc00;
}

.product-price {
    font-size: 22px;
    font-weight: bold;
    margin-top: 10px;
}

.product-detail {
    margin-top: 20px;
}

.product-detail ul {
    list-style-type: none;
    padding: 0;
}

.product-detail ul li {
    margin: 8px 0;
}

.purchase-info {
    margin-top: 20px;
}

.purchase-info input {
    width: 60px;
    padding: 5px;
    font-size: 16px;
    border-radius: 10px;
    border: 1px solid #ccc;
}

.purchase-info .button {
    background-color: #e95321;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-size: 16px;
    text-decoration: none;
    transition: background-color 0.3s, transform 0.3s, font-size 0.3s;
}

.purchase-info .button:hover:not(.disabled) {
    background-color: red;
    color: white;
    transform: scale(1.1);
    font-size: 18px;
}

.purchase-info .button.disabled {
    background-color: #ccc;
    cursor: not-allowed;
    pointer-events: none;
}

</style>
 <body>
<div class="main-content" style="flex-grow: 1; background-color: #bbebfb;"></div>
<?php
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<p style='color: green; text-align: center;'>Votre avis a été ajouté avec succès !</p>";
}
?>

 <div class="card-wrapper">
  <div class="card">
    <div class="product-imgs">
      <div class="img-display">
      <img src="images/articles/prod<?php echo $idArticle; ?>.jpg" class="card-img-top" alt="<?php echo$nomArticle; ?>">
      
    </div>
    </div>
    <!-- card right -->
    <div class="product-content">
      <h2 class="product-title"><?php echo $nomArticle; ?></h2>
      <div class="product-rating">
        <?php 
        for($i = 1; $i <= 5; $i++) {
            if($i <= floor($averageRating)) {
                echo '<i class="bi bi-star-fill text-warning"></i>';
            } elseif($i - 0.5 <= $averageRating) {
                echo '<i class="bi bi-star-half text-warning"></i>';
            } else {
                echo '<i class="bi bi-star text-warning"></i>';
            }
        }
        ?>
        <span><?php echo $averageRating; ?> (<?php echo $numberOfReviews; ?>)</span>
      </div>

      <div class="product-price">
        <p class="price">Prix: <span><?php echo $prix; ?>&euro; </span></p>
      </div>

      <div class="product-detail">
        <h3> Description : </h3>
        <p><?php echo $description; ?></p>
        </div>
        <hr>
        <div class="caracteristiques">
        <ul>
          <li>Couleur: <span><?php echo $couleur; ?></span></li>
          <li>Poids: <span><?php echo $poids; ?></span> kg</li>
          <li>Dimensions: <span><?php echo $dimension; ?> (cm)</span></li>
          <li>Tranche d'&acirc;ge: <span><?php echo $trancheAge; ?></span></li>
        </ul>
      </div>
       <div class="purchase-info">
       <?php if ($quantite > 0): ?>
        <p class="text-success mb-2">En stock: <?php echo $quantite; ?> unité(s)</p>
        <br/>
        <a href="ajouter_panier.php?id=<?php echo $idArticle; ?>" 
           class="purchase-info button <?php echo $quantite == 0 ? 'disabled' : ''; ?>"
           <?php echo $quantite == 0 ? 'aria-disabled="true"' : ''; ?>>
            Ajouter au panier
        </a>
    <?php else: ?>
        <p class="text-danger mt-2">Rupture de stock</p>
    <?php endif; ?>

    <!-- Bouton Ajouter avis -->
    <a href="ajouter_avis.php?id=<?php echo $idArticle; ?>" 
        class="purchase-info button <?php echo !$commande ? 'disabled' : ''; ?>" 
        <?php echo !$commande ? 'aria-disabled="true" onclick="return false;"' : ''; ?> 
        style="margin-left: 10px;">
        Ajouter avis
    </a>
      </div></div>
  </div>
</div>
</div>


<!-- Zone avis --> 
<div class="container mt-5">
    <h2 class="text-start" style="font-weight: 400;">Avis Clients</h2>
    <div class="row">
        <?php if (!empty($avis)): ?>
            <div class="col-12">
                <?php foreach($avis as $review): ?>
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0"><?php echo$review['prenom']; ?></h5>
                                <div class="stars">
                                    <?php 
                                    $noteInt = intval($review['note']);
                                    for($i = 1; $i <= 5; $i++) {
                                        if($i <= $noteInt) {
                                            echo '<i class="bi bi-star-fill text-warning"></i>';
                                        } else {
                                            echo '<i class="bi bi-star text-warning"></i>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <h6 class="mt-2"><?php echo $review['titre']; ?></h6>
                            <p class="card-text mt-2"><?php echo $review['commentaire']; ?></p>
                            <?php if(isset($review['date'])): ?>
                                <small class="text-muted">Posté le <?php echo date('d/m/Y', strtotime($review['date'])); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="col-12">
                <p>Aucun avis pour ce produit.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
<?php
include "include/footer.php";
?>
