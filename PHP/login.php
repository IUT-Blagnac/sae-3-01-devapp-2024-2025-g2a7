<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de connexion</title>
    <style>
        /* Style global */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Prend toute la hauteur de l'écran */
        }

        /* Conteneur principal pour centrer le formulaire */
        .form-container {
            width: 100%;
            max-width: 400px; /* Limite la largeur du formulaire */
            background: #fff;
            padding: 30px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Titre de la page */
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        /* Champs de saisie */
        input[type="email"],
        input[type="password"],
        input[type="submit"] {
            width: 95%;
            padding: 12px;
            margin: 10px auto 20px auto;
            border-radius: 4px;
            display: block; /* Permet le centrage */
            border: 1px solid #ccc;
            font-size: 16px;
        }

        /* Style pour le bouton de soumission */
        input[type="submit"] {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Message d'erreur */
        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Alignement de la case "Se souvenir de moi" */
        .checkbox-container {
            text-align: left;
        }

        /* Style pour le lien d'inscription */
        .signup-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .signup-link a {
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <!-- Conteneur pour centrer le formulaire -->
    <div class="form-container">
        <!-- Titre de la page -->
        <h2>Veuillez entrer l'identifiant et le mot de passe pour accéder aux données :</h2>
        
        <!-- Vérifie si un message d'erreur est passé en paramètre GET dans l'URL -->
        <?php
        if (isset($_GET['msgErreur'])) {
            echo "<p class='error-message'>" . htmlentities($_GET['msgErreur']) . "</p>";
        }
        ?>

        <!-- Formulaire de connexion -->
        <form action="TraitConnexion.php" method="POST">
            <!-- Champ Login -->
            <?php
            if (isset($_COOKIE['Cutilisateur'])) {
                echo '<input type="email" name="login" value="' . htmlentities($_COOKIE['Cutilisateur']) . '" required />';
            } else {
                echo '<input type="email" name="login" placeholder="Entrez votre identifiant" required />';
            }
            ?>
            
            <!-- Champ Mot de passe -->
            <input type="password" name="PWD" placeholder="Entrez votre mot de passe" required />

            <!-- Case "Se souvenir de moi" -->
            <div class="checkbox-container">
                <input type="checkbox" name="seSouvenirMoi" /> Se souvenir de moi
            </div>

            <!-- Bouton de soumission -->
            <input type="submit" name="Entrer" value="Se connecter" />
        </form>

        <!-- Lien vers la page d'inscription -->
        <div class="signup-link">
            <p>Pas encore inscrit ? <a href="signUp.php">Créer un compte</a></p>
        </div>
    </div>
</body>


</html>
