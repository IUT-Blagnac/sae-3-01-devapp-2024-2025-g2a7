<?php
// Démarrer la session
session_start();

// Détruire toutes les variables de session
$_SESSION = array();

// Détruire la session
session_destroy();

// Redirection vers la page de connexion
header("Location: index.php?msgErreur=Vous êtes déconnecté.");
exit();