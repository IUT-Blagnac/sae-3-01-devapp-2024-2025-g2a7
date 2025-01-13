<?php
session_start();
include "include/header.php";
?>

<body style="display: flex; flex-direction: column; min-height: 100vh; margin: 0;">
    <main class="main-content py-5" style="flex-grow: 1; background-color: #bbebfb;">
        <div class="container text-center">
            <h2 class="mb-4">Votre commande a bien &eacute;t&eacute; effectu&eacute;e</h2>
            <a href="index.php" class="btn btn-primary">Retour &agrave; l'accueil</a>
        </div>
    </main>

<?php
include "include/footer.php";
?>
</body>