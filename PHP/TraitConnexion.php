<?php
// Inclut le fichier pour la connexion à la base de données
include("./include/Connect.inc.php");

// Démarre la session pour pouvoir utiliser des variables de session
session_start();

// Vérifie si le formulaire a été soumis et si les champs 'login' et 'PWD' sont remplis
if (!empty($_POST['Entrer']) && !empty($_POST['login']) && !empty($_POST['PWD'])) {
    // Nettoie les entrées pour éviter les attaques de type injection SQL
    $login = trim($_POST['login']);
    $password = trim($_POST['PWD']);

    // Prépare la requête SQL pour chercher l'utilisateur
    $sql = "SELECT * FROM Utilisateur WHERE email = :login";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':login', $login);
    $stmt->execute();

    // Récupère l'utilisateur correspondant
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si l'email ne correspond à aucun utilisateur
    if (!$user) {
        header("Location: signUp.php?msgErreur=Aucun compte trouvé pour cet email. Veuillez vous inscrire.");
        exit();
    }

    // Vérifie si le mot de passe est correct
    if ($password === $user['mdp']) {
        // Si les identifiants sont corrects, crée une variable de session
        $_SESSION['Sutilisateur'] = 'oui';
        $_SESSION['nom'] = htmlentities($user['email']);

        // Si l'utilisateur a coché "Se souvenir de moi", un cookie est créé
        if (isset($_POST['seSouvenirMoi'])) {
            setcookie("Cutilisateur", $user['email'], time() + 60 * 5);
        }

        // Redirige vers la page d'accueil
        header("Location: index.php");
        exit();
    } else {
        // Mot de passe incorrect
        header("Location: login.php?msgErreur=Erreur de connexion ! Login ou mot de passe incorrect ... Recommencez");
        exit();
    }
} else {
    // Si les champs sont vides
    header("Location: login.php?msgErreur=Erreur de connexion... Veuillez remplir tous les champs");
    exit();
}