<?php
include "include/header.php";

$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($productId > 0) {
    $query = $conn->prepare("SELECT * FROM Article WHERE idArticle = :id");
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

        $imagePath = "images/articles/prod{$idArticle}.jpg";
    } else {
        echo "<p class='text-danger'>Produit introuvable.</p>";
        exit; 
    }
} else {
    echo "<p class='text-danger'>Aucun produit sélectionné.</p>";
    exit;
}
?>


<style>
/* style unique à cette page*/
@import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap');


 h2,h3 {
    font-family: 'Open Sans', sans-serif;
}


body {
    background-color: #bbebfb; /
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

.purchase-info button {
    background-color: #e95321;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s, transform 0.3s, font-size 0.3s; 
}

.purchase-info button:hover {
    background-color: red;
    color: white; 
    transform: scale(1.1);
    font-size: 18px; 
}

</style>
 <body>
<div class="main-content" style="flex-grow: 1; background-color: #bbebfb;"></div>

 <div class="card-wrapper">
  <div class="card">
    <div class="product-imgs">
      <div class="img-display">
      <img src="images/articles/prod<?php echo $idArticle; ?>.jpg" class="card-img-top" alt="<?php echo htmlspecialchars($nomArticle); ?>">
      </div>
    </div>
    <!-- card right -->
    <div class="product-content">
      <h2 class="product-title"><?php echo $nomArticle; ?></h2>
      <div class="product-rating">
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
        <i class="fas fa-star-half-alt"></i>
        <span>4.7 (3)</span>
      </div>

      <div class="product-price">
        <p class="price">Prix: <span><?php echo $prix; ?> </span></p>
      </div>

      <div class="product-detail">
        <h3> Description : </h3>
        <p><?php echo $description; ?></p>
        
        <ul>
          <li>Couleur: <span><?php echo $couleur; ?></span></li>
          <li>Poids: <span><?php echo $poids; ?></span></li>
          <li>Dimensions: <span><?php echo $dimension; ?></span></li>
          <li>Tranche d'âge: <span><?php echo $trancheAge; ?></span></li>
        </ul>
      </div>

      <div class="purchase-info">
        <input type="number" min="0" value="1">
        <button type="button" class="btn">
         Ajouter au panier <i class="fas fa-shopping-cart"></i>
        </button>
      </div>

      
    </div>
  </div>
</div>
</div>

</body>
<?php
include "include/footer.php";
?>
