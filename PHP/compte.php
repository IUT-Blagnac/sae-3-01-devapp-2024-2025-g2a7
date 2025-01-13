<?php
// Démarre la session pour vérifier si l'utilisateur est connecté
session_start();

// Vérifie si l'utilisateur est connecté
if (isset($_SESSION['Sutilisateur']) && $_SESSION['Sutilisateur'] === 'oui') {
    // Redirige vers la page des informations du compte si connecté
    header("Location: utilisateur.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte - E-commerce</title>
    <style>
        /* Style global */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc; /* Couleur de fond douce */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        /* Container principal centré */
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 40px 30px;
            text-align: center;
            width: 100%;
            max-width: 500px;
        }

        /* Style pour le titre */
        h1 {
            font-size: 24px;
            color: #000000; /* Titre en noir */
            margin-bottom: 20px;
        }

        /* Style pour le texte explicatif */
        p {
            font-size: 16px;
            color: #555;
            margin-bottom: 30px;
        }

        /* Style pour le bouton */
        button {
            background-color: #007BFF; /* Couleur bleue */
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        /* Effet de survol pour le bouton */
        button:hover {
            background-color: #0056b3; /* Couleur bleue plus foncée au survol */
        }

        /* Style pour le lien de connexion */
        a {
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Connectez-vous pour une meilleure expérience</h1>
    
    <!-- Bouton pour se connecter -->
    <a href="login.php">
        <button>Se connecter</button>
    </a>
</div>

</body>

</html>
