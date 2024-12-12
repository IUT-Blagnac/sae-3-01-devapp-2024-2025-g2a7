<?php
    session_start();
    include "include/header.php";
?>

<body style="display: flex; flex-direction: column; min-height: 100vh; margin: 0;">

    <div class="main-content" style="flex-grow: 1; background-color: #bbebfb; padding: 20px; position: relative;">

    <!-- image mascotte brickolo -->
        <img src="images/personnageBrickolo.png" alt="perso brickolo" 
             style="position: absolute; top: 50%; left: 25%; 
                    width: 15vw; 
                    max-width: 100%; 
                    height: auto; 
                    max-height: 100%; 
                    transform: translate(-50%, -50%);">
    </div>

    <!-- cadre des coups de coeur -->
    <div class="right-frame">
    <h2>NOS COUPS DE COEUR !</h2>
    <div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <img src="images/articles/prod4.jpg" class="card-img-top" alt="Caserne de Pompiers Brickolo Town™">
                <div class="card-body">
                    <h5 class="card-title">Caserne de Pompiers Brickolo Town™</h5>
                    <p class="card-text">Prix : 39.99 €</p>
                    <p class="card-text">Caserne de pompiers avec camions et figurines.</p>
                    <a href="produit.php?id=4" class="btn btn-primary">Details</a>
                    <a href="ajouter_panier.php?id=4" class="btn btn-primary">Ajouter au panier</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <img src="images/articles/prod1.jpg" class="card-img-top" alt="Station de Police Brickolo Town™">
                <div class="card-body">
                    <h5 class="card-title">Station de Police Brickolo Town™</h5>
                    <p class="card-text">Prix : 29.99 €</p>
                    <p class="card-text">Station complète avec véhicules et figurines.</p>
                    <a href="produit.php?id=1" class="btn btn-primary">Details</a>
                    <a href="ajouter_panier.php?id=1" class="btn btn-primary">Ajouter au panier</a>
                </div>
            </div>
        </div>
    </div>
</div>

</div>




    </body>

<?php
    include "include/footer.php";
?>




