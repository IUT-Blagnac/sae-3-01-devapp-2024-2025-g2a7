<?php
// Inclure le fichier de connexion à la base de données
require 'include/Connect.inc.php';

// Vérifier si l'utilisateur est connecté
$role = '';
if (isset($_SESSION['nom'])) {
    $login = $_SESSION['nom'];
    // Requête pour obtenir le rôle de l'utilisateur
    $query = "SELECT rôle FROM Utilisateur WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$login]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result && isset($result['rôle'])) {
        $role = $result['rôle'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brickolo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3E%3Cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
        }
        .orange-band {
            background-color: #e95321;
            text-align: center;
            padding: 20px 0;
        }
        .orange-band h1 {
            margin: 0;
            color: white;
        }
        .nav-link.active {
    color: white; 
    text-decoration: none; 
    font-size: 25px; 
    font-family: 'Gill Sans', sans-serif; 
    font-weight: bold; 
    padding: 5px 10px; 
    transition: all 0.3s ease; 
}
    </style>
</head>
<body>
<header>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #417ba0; padding: 20px 0;">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">
        <img src="images/LogoBrickoloV2.png" alt="Logo Brickolo" style="height: 70px; border-radius: 15px; padding: 5px;">
    </a>
    <a class="nav-link active" aria-current="page" href="catalogue.php">Catalogue</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel" style="background-color: #417ba0;">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title text-white" id="offcanvasNavbarLabel">Menu</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    
    <form class="d-flex d-lg-none mx-auto mb-3" role="search" onsubmit="event.preventDefault();">
      <input class="form-control rounded-pill" style="width: 100%;" type="search" placeholder="Rechercher..." aria-label="Search" onkeydown="if(event.key === 'Enter'){ this.form.submit(); }">
    </form>
    
    <form class="d-none d-lg-flex mx-auto" role="search" onsubmit="event.preventDefault();">
      <input class="form-control rounded-pill" style="width: 500px;" type="search" placeholder="Rechercher..." aria-label="Search" onkeydown="if(event.key === 'Enter'){ this.form.submit(); }">
    </form>
    
    <div class="d-flex flex-column flex-lg-row gap-3 align-items-center">
      <a href="panier.php"><img src="images/logoPanier.png" alt="Logo Panier" style="height: 40px;"></a>
      <a href="compte.php"><img src="images/logoProfil.png" alt="Logo Profil" style="height: 40px;"></a>
      <a href="logout.php"><img src="images/logoLogout.png" alt="Logo Profil" style="height: 40px;"></a>
          <?php if ($role === 'Admin'): ?>
      <a href="administrateur.php"><button class="btn btn-warning">Admin</button></a>
          <?php endif; ?> 
    </div>
  </div>
</div>
  </div>
</nav>
<div class="orange-band" style="display: flex; align-items: center; justify-content: space-between; padding: 20px 0;">
    <h1 style="flex-grow: 1; text-align: center; margin: 0;">Brickolo</h1>
</div>
</header>

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>