<?php
try {
  $user = 'R2024MYSAE3003'; // Nom d'utilisateur pour se connecter à la base de données
  $pass = 'PJ99R3ai53Bumw'; // Mot de passe associé à cet utilisateur
  // Création d'une instance PDO pour se connecter à la base de données MySQL
  $conn = new PDO('mysql:host=localhost;dbname=R2024MYSAE3003;charset=UTF8', $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch (PDOException $e) {
  echo "Erreur: " . $e->getMessage() . "<br>"; // Affiche un message d'erreur détaillé
  die(); // Stoppe l'exécution du script après l'affichage du message d'erreur
}
