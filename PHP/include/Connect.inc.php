<?php
try {
    $user = 'R2024MYSAE3003';
    $pass = 'PJ99R3ai53Bumw';
    
    // Add PDO options for UTF-8
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ];
    
    $conn = new PDO(
        'mysql:host=localhost;dbname=R2024MYSAE3003;charset=utf8mb4',
        $user,
        $pass,
        $options
    );
    
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage() . "<br>";
    die();
}
