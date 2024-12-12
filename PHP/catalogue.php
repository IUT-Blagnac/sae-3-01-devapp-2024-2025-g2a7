<?php
include "include/header.php";


// Vérification des filtres dans l'URL
$filters = false;
if (isset($_GET['availability']) || isset($_GET['min-price']) || isset($_GET['max-price']) || isset($_GET['rating'])) {
    $filters = true;
}

$sql = "SELECT idArticle, nomArticle, prix, description FROM Article WHERE 1"; 


if ($filters) {
    if (isset($_GET['availability']) && $_GET['availability'] != 'any') {
        $availability = $_GET['availability'];
        $sql .= $availability == 'available' ? " AND disponible = 1" : " AND disponible = 0";
    }

    if (isset($_GET['min-price']) && isset($_GET['max-price'])) {
        $min_price = intval($_GET['min-price']);
        $max_price = intval($_GET['max-price']);
        $sql .= " AND prix BETWEEN $min_price AND $max_price";
    }

    if (isset($_GET['rating'])) {
        $rating = intval($_GET['rating']);
        $sql .= " AND rating >= $rating";
    }
}

$stmt = $conn->prepare($sql); 
$stmt->execute(); 

?>

<body style="display: flex; flex-direction: column; min-height: 100vh; margin: 0;">
    <div class="main-content" style="flex-grow: 1; background-color: #bbebfb; padding: 20px; position: relative;">
    
        <!-- Formulaire de filtres -->
        <form method="GET" class="filter-bar">
            <div class="filter-item">
                <label for="availability">Disponibilité :</label>
                <select id="availability" name="availability">
                    <option value="any" <?php echo !isset($_GET['availability']) || $_GET['availability'] == 'any' ? 'selected' : ''; ?>>Tous</option>
                    <option value="available" <?php echo isset($_GET['availability']) && $_GET['availability'] == 'available' ? 'selected' : ''; ?>>Disponible</option>
                    <option value="unavailable" <?php echo isset($_GET['availability']) && $_GET['availability'] == 'unavailable' ? 'selected' : ''; ?>>Indisponible</option>
                </select>
            </div>
            <div class="filter-item">
                <label for="price-range">Fourchette de prix :</label>
                <div class="price-range">
                    <input type="number" id="min-price" name="min-price" min="0" max="500" step="10" value="<?php echo isset($_GET['min-price']) ? $_GET['min-price'] : 0; ?>" />
                    <span>-</span>
                    <input type="number" id="max-price" name="max-price" min="0" max="500" step="10" value="<?php echo isset($_GET['max-price']) ? $_GET['max-price'] : 500; ?>" />
                </div>
            </div>
            <div class="filter-item">
                <label for="min-rating">Commentaires client :</label>
                <div class="star-rating">
                    <input type="radio" id="star5" name="rating" value="5" <?php echo isset($_GET['rating']) && $_GET['rating'] == '5' ? 'checked' : ''; ?> />
                    <label for="star5" class="star">&#9733;</label>

                    <input type="radio" id="star4" name="rating" value="4" <?php echo isset($_GET['rating']) && $_GET['rating'] == '4' ? 'checked' : ''; ?> />
                    <label for="star4" class="star">&#9733;</label>

                    <input type="radio" id="star3" name="rating" value="3" <?php echo isset($_GET['rating']) && $_GET['rating'] == '3' ? 'checked' : ''; ?> />
                    <label for="star3" class="star">&#9733;</label>

                    <input type="radio" id="star2" name="rating" value="2" <?php echo isset($_GET['rating']) && $_GET['rating'] == '2' ? 'checked' : ''; ?> />
                    <label for="star2" class="star">&#9733;</label>

                    <input type="radio" id="star1" name="rating" value="1" <?php echo isset($_GET['rating']) && $_GET['rating'] == '1' ? 'checked' : ''; ?> />
                    <label for="star1" class="star">&#9733;</label>
                </div>
            </div>
            <button type="submit">Filtrer</button>
        </form>

        <div class="container">
            <div class="row">
                <?php
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC); 
                if (count($products) > 0) {
                    foreach ($products as $row) {
                        $idArticle = $row['idArticle'];
                        $nomArticle = $row['nomArticle'];
                        $prix = $row['prix']; 
                        $description = isset($row['description']) ? $row['description'] : 'Pas de description';
                        ?>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                            <img src="images/articles/prod<?php echo $idArticle; ?>.jpg" class="card-img-top" alt="<?php echo htmlspecialchars($nomArticle); ?>">
                              <div class="card-body">
                                    <h5 class="card-title"><?php echo $nomArticle; ?></h5>
                                    <p class="card-text">Prix : <?php echo $prix; ?> €</p>
                                    <p class="card-text"><?php echo $description; ?></p>
                                    <a href="produit.php?id=<?php echo $idArticle; ?>" class="btn btn-primary">Details</a>
                                    <a href="ajouter_panier.php?id=<?php echo $idArticle; ?>" class="btn btn-primary">Ajouter au panier</a>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "Aucun produit trouvé.";
                }
                ?>
            </div>
        </div>
    </div>
</body>



<?php
include "include/footer.php";
?>
