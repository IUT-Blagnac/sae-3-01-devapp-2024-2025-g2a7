<?php
    session_start();
    include "include/header.php";
?>


<body style="display: flex; flex-direction: column; min-height: 100vh; margin: 0;">
    <main class="main-content py-5" style="flex-grow: 1; background-color: #bbebfb;">
        <div class="container">
            <!-- image mascotte brickolo -->
            <img src="images/personnageBrickolo.png" alt="perso brickolo" 
                class="mascotte-img"
                style="position: absolute; top: 60%; left: 25%; 
                    width: 15vw; 
                    max-width: 100%; 
                    height: auto; 
                    max-height: 100%; 
                    transform: translate(-50%, -50%);">

            <!-- carrousel des coups de coeur -->
            <h2 class="text-center mb-4">NOS COUPS DE COEUR !</h2>
            <div id="carouselCoups" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active" data-bs-interval="2500">
                        <div class="card mx-auto" style="max-width: 400px;">
                            <img src="images/articles/prod4.jpg" class="card-img-top" alt="Caserne de Pompiers Brickolo Town™">
                            <div class="card-body">
                                <h5 class="card-title">Caserne de Pompiers Brickolo Town&trade;</h5>
                                <p class="card-text">Prix : 39,99 &euro;</p>
                                <p class="card-text">Caserne de pompiers avec camions et figurines.</p>
                                <a href="produit.php?id=4" class="btn btn-primary">Details</a>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item" data-bs-interval="2500">
                        <div class="card mx-auto" style="max-width: 400px;">
                            <img src="images/articles/prod30.jpg" class="card-img-top" alt="Petite Ferme Kidz™">
                            <div class="card-body">
                                <h5 class="card-title">Petite Ferme Kidz&trade;</h5>
                                <p class="card-text">Prix : 14,99 &euro;</p>
                                <p class="card-text">Créez une ferme avec animaux, tracteur et grange.</p>
                                <a href="produit.php?id=30" class="btn btn-primary">Details</a>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item" data-bs-interval="2500">
                        <div class="card mx-auto" style="max-width: 400px;">
                            <img src="images/articles/prod58.jpg" class="card-img-top" alt="Stade de Football Brickolo™">
                            <div class="card-body">
                                <h5 class="card-title">Stade de Football Brickolo&trade;</h5>
                                <p class="card-text">Prix : 49,99 &euro;</p>
                                <p class="card-text">Terrain, buts et tribunes pour recréer un match.</p>
                                <a href="produit.php?id=58" class="btn btn-primary">Details</a>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselCoups" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Précédent</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselCoups" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Suivant</span>
                </button>
            </div>
        </div>
    </main>

<?php
    include "include/footer.php";
?>
</body>
