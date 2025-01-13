<?php
    session_start();
    include "include/header.php";
?>

<body style="display: flex; flex-direction: column; min-height: 100vh; margin: 0;">
    <main class="main-content py-5" style="flex-grow: 1; background-color: #bbebfb;">
        <div class="container">
            <!-- Présentation de l'entreprise Brickolo -->
            <h2 class="text-center mb-4">À propos de Brickolo</h2>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <p style="text-align: justify;">
                        Fondée en 1924 à Blagnac par <strong>Wilhelm Schmidt</strong>, Brickolo a commencé par concevoir des jouets en bois et des marionnettes pour enfants. 
                        L'entreprise s'est rapidement développée pour devenir un acteur majeur dans la fabrication de briques de jeu en plastique, offrant créativité et amusement aux enfants du monde entier.
                    </p>
                    <p style="text-align: justify;">
                        Depuis 2021, <strong>Anna Müller</strong> dirige l'entreprise, apportant une vision innovante axée sur la durabilité et la réduction de l'empreinte écologique. 
                        Grâce à ces réformes, Brickolo continue de répondre aux attentes de ses clients tout en respectant l'environnement.
                    </p>
                    <p style="text-align: justify;">
                        En 2023, Brickolo a enregistré un chiffre d'affaires d'environ <strong>7,8 millions d'euros</strong>, illustrant sa popularité mondiale et sa capacité d'innovation.
                    </p>
                </div>
            </div>
        </div>
    </main>

<?php
    include "include/footer.php";
?>
</body>