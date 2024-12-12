<?php

session_start();

// Inclure le fichier de connexion à la base de données
require 'include/Connect.inc.php';

// Inclure le fichier de header qui contient l'importation de style.css
require 'include/header.php';

// Initialiser les variables utilisateur
$nom = $prenom = $civilite = $email = $telephone = $numAdr = $rue = $codePostal = $ville = $pays = $complement = "";

// Récupérer les informations de l'utilisateur connecté
if (isset($_SESSION['nom'])) {
    $login = $_SESSION['nom'];
    
    // Si le formulaire est soumis, mettre à jour les informations de l'utilisateur
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $civilite = $_POST['civilite'];
        $email = $_POST['email'];
        $telephone = $_POST['telephone'];
        $numAdr = $_POST['numAdr'];
        $rue = $_POST['rue'];
        $codePostal = $_POST['codePostal'];
        $ville = $_POST['ville'];
        $pays = $_POST['pays'];
        $complement = $_POST['complement'];
        
        // Requête pour mettre à jour les informations de l'utilisateur
        $updateQuery = "
        UPDATE Utilisateur u
        LEFT JOIN Adresse a ON u.idUtilisateur = a.idUtilisateur
        SET u.nom = ?, u.prenom = ?, u.Civilite = ?, u.email = ?, u.numTel = ?, 
            a.numAdr = ?, a.rue = ?, a.codePostal = ?, a.ville = ?, a.pays = ?, a.complement = ?
        WHERE u.email = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->execute([$nom, $prenom, $civilite, $email, $telephone, $numAdr, $rue, $codePostal, $ville, $pays, $complement, $login]);
        
        // Mettre à jour la session avec le nouvel email
        $_SESSION['nom'] = $email;

        // Définir un message de confirmation
        $_SESSION['message'] = "Les modifications ont été enregistrées avec succès.";
        
        // Rediriger pour éviter la resoumission du formulaire
        header('Location: informationCompte.php');
        exit();
    }
    
    // Requête pour obtenir les informations de l'utilisateur
    $query = "
    SELECT 
        u.nom, u.prenom, u.Civilite, u.email, u.numTel, 
        a.numAdr, a.rue, a.codePostal, a.ville, a.pays, a.complement
    FROM Utilisateur u
    LEFT JOIN Adresse a ON u.idUtilisateur = a.idUtilisateur
    WHERE u.email = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$login]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        // Récupérer les données
        $nom = htmlentities($result['nom']);
        $prenom = htmlentities($result['prenom']);
        $civilite = htmlentities($result['Civilite']);
        $email = htmlentities($result['email']);
        $telephone = htmlentities($result['numTel']);
        $numAdr = htmlentities($result['numAdr']);
        $rue = htmlentities($result['rue']);
        $codePostal = htmlentities($result['codePostal']);
        $ville = htmlentities($result['ville']);
        $pays = htmlentities($result['pays']);
        $complement = htmlentities($result['complement']);
    } else {
        // Si l'utilisateur n'existe pas dans la base, déconnecter
        header('Location: login.php?msgErreur=Utilisateur non trouvé.');
        exit();
    }
} else {
    // Si aucun utilisateur connecté, rediriger
    header('Location: login.php?msgErreur=Veuillez vous connecter.');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informations du Compte</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        .account-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }

        .account-form .form-group {
            margin-bottom: 20px;
        }

        .account-form label {
            display: block;
            color: #444;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .account-form input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s ease;
        }

        .account-form input:focus {
            border-color: #e95321;
            outline: none;
        }

        .account-buttons {
            grid-column: 1 / -1;
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        .btn-brickolo {
            background-color: #e95321;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-brickolo:hover {
            background-color: #d64a1a;
        }

        .btn-brickolo-secondary {
            background-color: #4a90e2;
        }

        .btn-brickolo-secondary:hover {
            background-color: #357abd;
        }

        .message {
            color: green;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="frame account-frame">
    <div class="account-header">
        <h1>Mon Compte</h1>
    </div>
    
    <?php
    if (isset($_SESSION['message'])) {
        echo '<div class="message">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']);
    }
    ?>
    
    <form class="account-form" action="informationCompte.php" method="post">
        <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" value="<?php echo $nom; ?>">
        </div>
        
        <div class="form-group">
            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" value="<?php echo $prenom; ?>">
        </div>
        
        <div class="form-group">
            <label for="civilite">Civilité</label>
            <input type="text" id="civilite" name="civilite" value="<?php echo $civilite; ?>">
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo $email; ?>">
        </div>
        
        <div class="form-group">
            <label for="telephone">Téléphone</label>
            <input type="text" id="telephone" name="telephone" value="<?php echo $telephone; ?>">
        </div>
        
        <div class="form-group">
            <label for="numAdr">Numéro d'adresse</label>
            <input type="text" id="numAdr" name="numAdr" value="<?php echo $numAdr; ?>">
        </div>
        
        <div class="form-group">
            <label for="rue">Rue</label>
            <input type="text" id="rue" name="rue" value="<?php echo $rue; ?>">
        </div>
        
        <div class="form-group">
            <label for="codePostal">Code Postal</label>
            <input type="text" id="codePostal" name="codePostal" value="<?php echo $codePostal; ?>">
        </div>
        
        <div class="form-group">
            <label for="ville">Ville</label>
            <input type="text" id="ville" name="ville" value="<?php echo $ville; ?>">
        </div>
        
        <div class="form-group">
            <label for="pays">Pays</label>
            <input type="text" id="pays" name="pays" value="<?php echo $pays; ?>">
        </div>
        
        <div class="form-group">
            <label for="complement">Complément</label>
            <input type="text" id="complement" name="complement" value="<?php echo $complement; ?>">
        </div>
        
        <div class="account-buttons">
            <button type="submit" class="btn-brickolo">Enregistrer les modifications</button>
            <a href="panier.php" class="btn-brickolo btn-brickolo-secondary">Accéder au panier</a>
        </div>
    </form>
</div>
</body>
</html>