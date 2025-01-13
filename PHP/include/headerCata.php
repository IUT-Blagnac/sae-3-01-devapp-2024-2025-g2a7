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
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brickolo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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

        .btn-catalog {
            background-color: #417ba0;
            color: white;
            border: 2px solid white;
            transition: all 0.3s ease;
        }

        .btn-catalog:hover {
            background-color: #e95321;
            color: white;
            transform: scale(1.05);
            box-shadow: 0 0 10px rgba(233, 83, 33, 0.5);
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
    <a href="catalogue.php" class="btn btn-catalog btn-lg">Catalogue</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel" style="background-color: #417ba0;">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title text-white" id="offcanvasNavbarLabel">Menu</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    
    <div class="d-flex flex-column flex-lg-row gap-3 align-items-center">
      <a href="panier.php">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="white" class="bi bi-bag" viewBox="0 0 16 16">
          <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>
        </svg>
      </a>
      <a href="compte.php">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="white" class="bi bi-person" viewBox="0 0 16 16">
          <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
        </svg>
      </a>
      <?php if (isset($_SESSION['nom'])): ?>
        <a href="logout.php">
          <i class="bi bi-box-arrow-right text-white" style="font-size: 40px;"></i>
        </a>
      <?php endif; ?>
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
