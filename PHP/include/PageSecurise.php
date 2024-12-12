<?php
    // Démarrer la session pour pouvoir accéder aux variables de session
    session_start();

    // Vérifier si l'utilisateur est authentifié en vérifiant la présence et la valeur de la variable de session 'SrazafinirinaMialisoa'
    // Si la variable n'existe pas ou si sa valeur est différente de "oui", cela signifie que l'utilisateur n'est pas authentifié
    if (!isset($_SESSION['Sutilisateur']) || $_SESSION['Sutilisateur'] != "oui") { 
        // Si l'utilisateur n'est pas authentifié, on redirige vers la page de connexion avec un message d'erreur dans l'URL
        header('Location: login.php?msgErreur=Accès non autorisé. Veuillez vous identifier'); 
        exit();
    }

?>