<?php
session_start();
include "include/header.php";

// Vérification des filtres dans l'URL
$filters = false;
if (isset($_GET['availability']) || isset($_GET['min-price']) || isset($_GET['max-price']) || isset($_GET['weight']) || isset($_GET['dimension']) || isset($_GET['nbPièces']) || isset($_GET['age-group']) || isset($_GET['color']) || isset($_GET['is-new']) || isset($_GET['nomArticle'])) {
    $filters = true;
}

$sql = "SELECT a.idCatégorie, c.idCat, c.nomCat, a.idArticle, a.nomArticle, a.prix, a.description, a.poids, a.dimension, a.nbPièce, a.trancheAge, a.couleur, a.nouveaute, s.quantite 
        FROM Article a
        INNER JOIN Catégorie c ON a.idCatégorie = c.idCat
        INNER JOIN Stock s ON a.idStock = s.idStock 
        WHERE 1";


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

    if (isset($_GET['weight']) && $_GET['weight'] !== '') {
        $weight = max(0, floatval($_GET['weight']));
        $sql .= " AND poids <= $weight";
    }

    if (isset($_GET['dimension']) && $_GET['dimension'] !== '') {
        $dimension = $_GET['dimension'];
        $sql .= " AND dimension = '$dimension'";
    }

    if (isset($_GET['nbPièces']) && $_GET['nbPièces'] !== '') {
        $nbPièces = max(0, intval($_GET['nbPièces']));
        $sql .= " AND nbPièce >= $nbPièces";
    }

    if (isset($_GET['nomArticle']) && $_GET['nomArticle'] !== '') {
        $nomArticle = htmlspecialchars($_GET['nomArticle']);
        $sql .= " AND nomArticle LIKE '%$nomArticle%'";
    }

    // Gestion des tranches d'âge
    if (isset($_GET['age-group']) && !empty($_GET['age-group'])) {
        $ageGroups = array_map('htmlspecialchars', $_GET['age-group']);
        $ageGroups = implode("', '", $ageGroups); // Convertit en format SQL acceptable
        $sql .= " AND trancheAge IN ('$ageGroups')";
    }

    if (isset($_GET['color']) && $_GET['color'] !== '') {
        $color = htmlspecialchars($_GET['color']);
        $sql .= " AND couleur IN ('$color')";
    }
    if (isset($_GET['categorie']) && $_GET['categorie'] !== '') {
        $cat = htmlspecialchars($_GET['categorie']);
        $sql .= " AND c.nomCat = '$cat'";
    }

    if (isset($_GET['orderBy']) && $_GET['orderBy'] == '1') {
        $sql .= " ORDER BY Prix";
    }
    else{
      $sql .= " ORDER BY Prix DESC ";
    }
}




$stmt = $conn->prepare($sql);
$stmt->execute();
?>

<body style="display: flex; flex-direction: column; min-height: 100vh; margin: 0;">
    <div class="main-content" style="flex-grow: 1; background-color: #bbebfb; padding: 20px; position: relative;">
        <form method="GET" class="filter-bar" style="display: flex; flex-wrap: wrap; gap: 20px;">
            <?php
            $searchTerm = isset($_GET['search']) ? htmlentities($_GET['search']) : '';
            ?>

            <div class="filter-item" style="flex: 1 1 calc(33% - 20px); min-width: 250px;">
                <label for="nomArticle">Nom de l'article :</label>
                <input 
                    type="text" 
                    id="nomArticle" 
                    name="nomArticle" 
                    value="<?= isset($_GET['nomArticle']) ? htmlspecialchars($_GET['nomArticle']) : $searchTerm; ?>" 
                    style="width: 100%;"
                />
            </div>

            <div class="filter-item" style="flex: 1 1 calc(33% - 20px); min-width: 250px;">
                <label for="price-range">Fourchette de prix :</label>
                <div class="price-range" style="display: flex; align-items: center; gap: 10px;">
                    <input type="number" id="min-price" name="min-price" min="0" value="<?php echo isset($_GET['min-price']) ? $_GET['min-price'] : 0; ?>" style="flex: 1;" />
                    <span>-</span>
                    <input type="number" id="max-price" name="max-price" min="0" value="<?php echo isset($_GET['max-price']) ? $_GET['max-price'] : 500; ?>" style="flex: 1;" />
                </div>
            </div>

            <div class="filter-item" style="flex: 1 1 calc(33% - 20px); min-width: 250px;">
                <label for="weight">Poids max :</label>
                <input type="number" id="weight" name="weight" min="0" step="0.001" value="<?php echo isset($_GET['weight']) ? $_GET['weight'] : ''; ?>" style="width: 100%;" />
            </div>

            <div class="filter-item" style="flex: 1 1 calc(33% - 20px); min-width: 250px;">
                <label for="dimension">Dimensions (L x H x P) :</label>
                <input type="text" id="dimension" name="dimension" placeholder="ex : 30x40x20" value="<?php echo isset($_GET['dimension']) ? $_GET['dimension'] : ''; ?>" style="width: 100%;" />
            </div>

            <div class="filter-item" style="flex: 1 1 calc(33% - 20px); min-width: 250px;">
                <label for="nbPièces">Nombre de pièces min :</label>
                <input type="number" id="nbPièces" name="nbPièces" min="0" value="<?php echo isset($_GET['nbPièces']) ? $_GET['nbPièces'] : ''; ?>" style="width: 100%;" />
            </div>

            <div class="filter-item" style="flex: 1 1 calc(33% - 20px); min-width: 250px;">
                <label for="age-group">Tranche d'âge :</label>
                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                    <?php 
                    $ageGroups = ['8+' => '8+', '10+' => '10+', '6+' => '6+', '4+' => '4+', '12+' => '12+'];
                    foreach ($ageGroups as $id => $label): ?>
                        <div>
                            <input type="checkbox" id="age<?= $id; ?>" name="age-group[]" value="<?= $label; ?>" 
                                <?= isset($_GET['age-group']) && in_array($label, $_GET['age-group']) ? 'checked' : ''; ?> />
                            <label for="age<?= $id; ?>"><?= $label; ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="filter-item" style="flex: 1 1 calc(33% - 20px); min-width: 250px;">
                <label for="color">Couleur :</label>
                <select id="color" name="color" style="width: 100%;">
                    <option value="">Choisir une couleur</option>
                    <?php
                    $colors = ['Bleu', 'Gris', 'Jaune', 'Rouge', 'Multicolore', 'Vert', 'Blanc', 'Beige', 'Noir', 'Rose', 'Marron', 'Chêne', 'Bois', 'Argent', 'Or', 'Transparent', 'Chrome', 'Platine', 'Édition Spéciale'];
                    foreach ($colors as $color): ?>
                        <option value="<?= $color; ?>" <?= isset($_GET['color']) && $_GET['color'] == $color ? 'selected' : ''; ?>><?= $color; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-item" style="flex: 1 1 calc(33% - 20px); min-width: 250px;">
                <label for="is-new">Nouveauté:</label>
                <input type="checkbox" id="is-new" name="is-new" value="1" <?php echo isset($_GET['is-new']) && $_GET['is-new'] == '1' ? 'checked' : ''; ?> />
            </div>

            <div class="filter-item" style="flex: 1 1 calc(33% - 20px); min-width: 250px;">
                <label>Prix:</label>
                <input type="radio" id="orderBy" name="orderBy" value="1" <?php echo isset($_GET['orderBy']) && $_GET['orderBy'] == '1' ? 'checked' : ''; ?> />
                <label for="orderBy">Croissant</label>
                <input type="radio" id="orderByD" name="orderBy" value="0" <?php echo isset($_GET['orderBy']) && $_GET['orderBy'] == '0' ? 'checked' : ''; ?> />
                <label for="orderByD">Décroissant</label>
            </div>
            <div class="filter-item" style="flex: 1 1 calc(33% - 20px); min-width: 250px;">
                <label for="categorie">Catégorie :</label>
                <select id="categorie" name="categorie" style="width: 25%;">
                    <option value="">Choisir une catégorie</option>
                    <?php
                    $categories = [];
                    try {
                        $query = "SELECT nomCat FROM Catégorie";
                        $statement = $conn->prepare($query);
                        $statement->execute();
            
                        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                            $categories[] = htmlentities($row['nomCat']);
                        }
                    } catch (PDOException $e) {
                        $categories[] = "Erreur : " . htmlentities($e->getMessage());
                    }
            
                    foreach ($categories as $categorie): ?>
                        <option value="<?= $categorie; ?>" <?= isset($_GET['categorie']) && $_GET['categorie'] == $categorie ? 'selected' : ''; ?>>
                            <?= $categorie; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>




            
            


            <div class="filter-item" style="flex: 1 1 100%; text-align: center;">
                <button type="submit" style="padding: 10px 20px;">Rechercher</button>
            </div>
        </form>
    </div>
    <div class="main-content" style="flex-grow: 1; background-color: #bbebfb; padding: 20px;">

      <div class="container" >
              <div class="row">
                  <?php
                  $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                  if (count($products) > 0) {
                      foreach ($products as $row) {
                          $idArticle = $row['idArticle'];
                          $nomArticle = $row['nomArticle'];
                          $prix = $row['prix'];
                          $description = isset($row['description']) ? $row['description'] : 'Pas de description';
                          $quantite = $row['quantite'];
                          ?>
                          <div class="col-md-4 mb-4">
                              <div class="card">
                                  <a href="produit.php?id=<?php echo $idArticle; ?>">
                                      <img src="images/articles/prod<?php echo $idArticle; ?>.jpg"
                                           class="card-img-top"
                                           alt="<?php echo htmlspecialchars($nomArticle); ?>">
                                  </a>
                                  <div class="card-body">
                                      <a href="produit.php?id=<?php echo $idArticle; ?>">
                                          <h5 class="card-title"><?php echo htmlspecialchars($nomArticle); ?></h5>
                                      </a>
                                      <p class="card-text">Prix : <?php echo $prix; ?> €</p>
                                      <p class="card-text"><?php echo $description; ?></p>
                                      <a href="produit.php?id=<?php echo $idArticle; ?>" class="btn btn-primary">Détails</a>
                                      <a href="ajouter_panier.php?id=<?php echo $idArticle; ?>"
                                         class="btn btn-primary <?php echo $quantite == 0 ? 'disabled' : ''; ?> "
                                         <?php echo $quantite == 0 ? 'aria-disabled="true"' : ''; ?>>
                                          Ajouter au panier
                                      </a>
                                      <?php if ($quantite == 0): ?>
                                          <p class="text-danger mt-2">Rupture de stock</p>
                                      <?php endif; ?>
                                  </div>
                              </div>
                          </div>
                      <?php }
                  } else {
                      echo "Aucun produit trouvé.";
                  }
                  ?>
              </div>
          </div>
      </div>
      </div>
    
</body>
