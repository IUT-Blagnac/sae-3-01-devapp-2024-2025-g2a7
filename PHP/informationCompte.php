<?php
session_start();

// Inclure le fichier de connexion à la base de données
require 'include/Connect.inc.php';

// Initialiser les variables utilisateur
$nom = $prenom = $civilite = $email = $telephone = $numAdr = $rue = $codePostal = $ville = $pays = $complement = "";

// Récupérer les informations de l'utilisateur connecté
if (isset($_SESSION['nom'])) {
    $login = $_SESSION['nom'];

    // Si le formulaire est soumis, mettre à jour les informations de l'utilisateur
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validation des champs avec des expressions régulières
        if (!preg_match("/^[a-zA-ZÀ-ÿ '-]+$/", $_POST['nom'])) {
            $_SESSION['message'] = "Nom invalide.";
            $_SESSION['message_type'] = "danger";
            header('Location: informationCompte.php');
            exit();
        }
        if (!preg_match("/^[a-zA-ZÀ-ÿ '-]+$/", $_POST['prenom'])) {
            $_SESSION['message'] = "Prénom invalide.";
            $_SESSION['message_type'] = "danger";
            header('Location: informationCompte.php');
            exit();
        }
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['message'] = "Email invalide.";
            $_SESSION['message_type'] = "danger";
            header('Location: informationCompte.php');
            exit();
        }
        if (!preg_match("/^[0-9]{10}$/", $_POST['telephone'])) {
            $_SESSION['message'] = "Téléphone invalide.";
            $_SESSION['message_type'] = "danger";
            header('Location: informationCompte.php');
            exit();
        }
        if (!preg_match("/^[0-9]+$/", $_POST['numAdr'])) {
            $_SESSION['message'] = "Numéro d'adresse invalide.";
            $_SESSION['message_type'] = "danger";
            header('Location: informationCompte.php');
            exit();
        }
        if (!preg_match("/^[a-zA-ZÀ-ÿ0-9 '-]+$/", $_POST['rue'])) {
            $_SESSION['message'] = "Rue invalide.";
            $_SESSION['message_type'] = "danger";
            header('Location: informationCompte.php');
            exit();
        }
        if (!preg_match("/^[0-9]{5}$/", $_POST['codePostal'])) {
            $_SESSION['message'] = "Code postal invalide.";
            $_SESSION['message_type'] = "danger";
            header('Location: informationCompte.php');
            exit();
        }
        if (!preg_match("/^[a-zA-ZÀ-ÿ '-]+$/", $_POST['ville'])) {
            $_SESSION['message'] = "Ville invalide.";
            $_SESSION['message_type'] = "danger";
            header('Location: informationCompte.php');
            exit();
        }
        if (!preg_match("/^[a-zA-ZÀ-ÿ '-]+$/", $_POST['pays'])) {
            $_SESSION['message'] = "Pays invalide.";
            $_SESSION['message_type'] = "danger";
            header('Location: informationCompte.php');
            exit();
        }
        if (!preg_match("/^[a-zA-ZÀ-ÿ0-9 '-]*$/", $_POST['complement'])) {
            $_SESSION['message'] = "Complément d'adresse invalide.";
            $_SESSION['message_type'] = "danger";
            header('Location: informationCompte.php');
            exit();
        }

        $nom = htmlspecialchars($_POST['nom']);
        $prenom = htmlspecialchars($_POST['prenom']);
        $civilite = htmlspecialchars($_POST['civilite']);
        $email = htmlspecialchars($_POST['email']);
        $telephone = htmlspecialchars($_POST['telephone']);
        $numAdr = htmlspecialchars($_POST['numAdr']);
        $rue = htmlspecialchars($_POST['rue']);
        $codePostal = htmlspecialchars($_POST['codePostal']);
        $ville = htmlspecialchars($_POST['ville']);
        $pays = htmlspecialchars($_POST['pays']);
        $complement = htmlspecialchars($_POST['complement']);

        // Mettre à jour les informations de l'utilisateur
        $updateUserQuery = "
        UPDATE Utilisateur
        SET nom = ?, prenom = ?, Civilite = ?, email = ?, numTel = ?
        WHERE email = ?";
        $stmt = $conn->prepare($updateUserQuery);
        $stmt->execute([$nom, $prenom, $civilite, $email, $telephone, $login]);

        // Récupérer l'idUtilisateur
        $stmt = $conn->prepare("SELECT idUtilisateur FROM Utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $idUtilisateur = $user['idUtilisateur'];

        // Vérifier si une adresse existe déjà pour cet utilisateur
        $stmt = $conn->prepare("SELECT COUNT(*) FROM Adresse WHERE idUtilisateur = ?");
        $stmt->execute([$idUtilisateur]);
        $adresseExists = $stmt->fetchColumn();

        if ($adresseExists) {
            // Mettre à jour l'adresse existante
            $updateAddressQuery = "
            UPDATE Adresse
            SET numAdr = ?, rue = ?, codePostal = ?, ville = ?, pays = ?, complement = ?
            WHERE idUtilisateur = ?";
            $stmt = $conn->prepare($updateAddressQuery);
            $stmt->execute([$numAdr, $rue, $codePostal, $ville, $pays, $complement, $idUtilisateur]);
        } else {
            // Insérer une nouvelle adresse
            $insertAddressQuery = "
            INSERT INTO Adresse (idUtilisateur, numAdr, rue, codePostal, ville, pays, complement)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertAddressQuery);
            $stmt->execute([$idUtilisateur, $numAdr, $rue, $codePostal, $ville, $pays, $complement]);
        }

        // Mettre à jour la session avec le nouvel email
        $_SESSION['nom'] = $email;

        // Définir un message de confirmation
        $_SESSION['message'] = "Les modifications ont été enregistrées avec succès.";
        $_SESSION['message_type'] = "success";

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
        $nom = htmlspecialchars($result['nom']);
        $prenom = htmlspecialchars($result['prenom']);
        $civilite = htmlspecialchars($result['Civilite']);
        $email = htmlspecialchars($result['email']);
        $telephone = htmlspecialchars($result['numTel']);
        $numAdr = htmlspecialchars($result['numAdr']);
        $rue = htmlspecialchars($result['rue']);
        $codePostal = htmlspecialchars($result['codePostal']);
        $ville = htmlspecialchars($result['ville']);
        $pays = htmlspecialchars($result['pays']);
        $complement = htmlspecialchars($result['complement']);
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

// Inclure le fichier de header qui contient l'importation de style.css
require 'include/header.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informations du Compte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-5">
    <h2>Mon Compte</h2>
    
    <?php
    if (isset($_SESSION['message'])) {
        $alertType = $_SESSION['message_type'] == 'success' ? 'alert-success' : 'alert-danger';
        echo '<div class="alert ' . $alertType . '">' . htmlspecialchars($_SESSION['message']) . '</div>';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
    ?>
    
    <form class="row g-3" action="informationCompte.php" method="post">
        <div class="col-md-6">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $nom; ?>" required>
        </div>
        
        <div class="col-md-6">
            <label for="prenom" class="form-label">Prénom</label>
            <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo $prenom; ?>" required>
        </div>
        
        <div class="col-md-6">
            <label for="civilite" class="form-label">Civilité</label>
            <select class="form-select" id="civilite" name="civilite" required>
                <option value="H" <?php echo $civilite == 'H' ? 'selected' : ''; ?>>Homme</option>
                <option value="F" <?php echo $civilite == 'F' ? 'selected' : ''; ?>>Femme</option>
                <option value="A" <?php echo $civilite == 'A' ? 'selected' : ''; ?>>Autre</option>
            </select>
        </div>
                
        <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
        </div>
        
        <div class="col-md-6">
            <label for="telephone" class="form-label">Téléphone</label>
            <input type="text" class="form-control" id="telephone" name="telephone" value="<?php echo $telephone; ?>" required>
        </div>
        
        <div class="col-md-6">
            <label for="numAdr" class="form-label">Numéro d'adresse</label>
            <input type="text" class="form-control" id="numAdr" name="numAdr" value="<?php echo $numAdr; ?>" required>
        </div>
        
        <div class="col-md-6">
            <label for="rue" class="form-label">Rue</label>
            <input type="text" class="form-control" id="rue" name="rue" value="<?php echo $rue; ?>" required>
        </div>
        
        <div class="col-md-6">
            <label for="codePostal" class="form-label">Code Postal</label>
            <input type="text" class="form-control" id="codePostal" name="codePostal" value="<?php echo $codePostal; ?>" required>
        </div>
        
        <div class="col-md-6">
            <label for="ville" class="form-label">Ville</label>
            <input type="text" class="form-control" id="ville" name="ville" value="<?php echo $ville; ?>" required>
        </div>
        
        <div class="col-md-6">
            <label for="pays" class="form-label">Pays</label>
            <input type="text" class="form-control" id="pays" name="pays" value="<?php echo $pays; ?>" required>
        </div>
        
        <div class="col-md-6">
            <label for="complement" class="form-label">Complément</label>
            <input type="text" class="form-control" id="complement" name="complement" value="<?php echo $complement; ?>">
        </div>
        
        <div class="col-12 d-flex justify-content-between mt-4">
            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
            <a href="panier.php" class="btn btn-secondary">Accéder au panier</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>