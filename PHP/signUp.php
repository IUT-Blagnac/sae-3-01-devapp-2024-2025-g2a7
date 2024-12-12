<?php
// Inclut le fichier pour la connexion à la base de données
require_once 'include/Connect.inc.php';

// Démarre la session pour pouvoir utiliser des variables de session
session_start();

// Initialisation des variables
$error = '';

// Gère la soumission du formulaire d'inscription
if (!empty($_POST['signUp'])) {
    // Récupère et nettoie les données du formulaire
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    // Validation des champs
    if (empty($prenom) || empty($nom) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse email invalide.";
    } elseif ($password !== $confirmPassword) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        try {
            // Vérifie si l'email existe déjà dans la base de données
            $sql = "SELECT idUtilisateur FROM Utilisateur WHERE email = :email";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->fetch()) {
                $error = "Cet email est déjà utilisé.";
            } else {
                // Génération du prochain ID utilisateur
                $sqlId = "SELECT COALESCE(MAX(idUtilisateur), 0) + 1 AS nextId FROM Utilisateur";
                $stmtId = $conn->query($sqlId);
                $nextId = $stmtId->fetchColumn();

                // Hachage du mot de passe
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                // Insère le nouvel utilisateur dans la base de données
                $sqlInsert = "INSERT INTO Utilisateur (idUtilisateur, prenom, nom, email, mdp) VALUES (:id, :prenom, :nom, :email, :password)";
                $stmtInsert = $conn->prepare($sqlInsert);
                $stmtInsert->bindParam(':id', $nextId);
                $stmtInsert->bindParam(':prenom', $prenom);
                $stmtInsert->bindParam(':nom', $nom);
                $stmtInsert->bindParam(':email', $email);
                $stmtInsert->bindParam(':password', $hashedPassword);

                if ($stmtInsert->execute()) {
                    // Redirige vers la page de connexion avec un message de succès
                    header("Location: login.php?msgSucces=Inscription réussie ! Vous pouvez maintenant vous connecter.");
                    exit();
                } else {
                    $error = "Une erreur est survenue lors de l'inscription. Veuillez réessayer.";
                }
            }
        } catch (PDOException $e) {
            $error = "Erreur de base de données : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            width: 100%;
            max-width: 400px;
            background: #fff;
            padding: 30px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="submit"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

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

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 20px;
        }

        .success-message {
            color: green;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Inscription</h2>
        <?php if (!empty($error)): ?>
            <p class='error-message'><?php echo htmlentities($error); ?></p>
        <?php endif; ?>

        <!-- Formulaire d'inscription -->
        <form action="signUp.php" method="POST">
            <input type="text" name="prenom" placeholder="Prénom" required />
            <input type="text" name="nom" placeholder="Nom" required />
            <input type="email" name="email" placeholder="Email" required />
            <input type="password" name="password" placeholder="Mot de passe" required />
            <input type="password" name="confirmPassword" placeholder="Confirmez le mot de passe" required />
            <input type="submit" name="signUp" value="S'inscrire" />
        </form>

        <!-- Lien vers la page de connexion -->
        <p style="text-align: center;">Déjà inscrit ? <a href="login.php">Connectez-vous ici</a>.</p>
    </div>
</body>

</html>
