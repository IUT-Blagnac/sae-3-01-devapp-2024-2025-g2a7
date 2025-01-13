<?php
ob_start(); 
session_start();
require 'include/Connect.inc.php';
require 'include/header.php'; 

$idArticle = isset($_GET['id']) ? intval($_GET['id']) : 0;
$idUtilisateur = $_SESSION['idUtilisateur'] ?? null;

if (!$idUtilisateur) {
    header('Location: login.php?error=Vous devez être connecté pour ajouter un avis.');
    exit;
}
// Récupérer le dernier idAvis et l'incrémenter
$queryLastId = $conn->query("SELECT MAX(idAvis) AS lastId FROM Avis");
$lastIdRow = $queryLastId->fetch(PDO::FETCH_ASSOC);
$newIdAvis = ($lastIdRow['lastId'] ?? 0) + 1; // Si NULL, commence à 1

// Enregistrement de l'avis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'] ?? '';
    $commentaire = $_POST['commentaire'] ?? '';
    $note = intval($_POST['note']) ?? 0;

    if ($note < 1 || $note > 5 || empty($titre) || empty($commentaire)) {
        echo "<p style='color: red;'>Veuillez remplir tous les champs correctement.</p>";
    } else {
        $queryInsertAvis = $conn->prepare("
            INSERT INTO Avis (idAvis, note, commentaire, date, titre, idUtilisateur, idArticle)
            VALUES (:idAvis, :note, :commentaire, NOW(), :titre, :idUtilisateur, :idArticle)
        ");
        $queryInsertAvis->execute([
            'idAvis' => $newIdAvis,
            'note' => $note,
            'commentaire' => $commentaire,
            'titre' => $titre,
            'idUtilisateur' => $idUtilisateur,
            'idArticle' => $idArticle
        ]);
        header ('Location: produit.php?id=' .$idArticle); 

        echo "<p style='color: green;'>Votre avis a été ajouté avec succès !</p>";
        exit; 
    }
}
ob_end_flush();
?>

<style>
    .container-avis {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 20px;
    }

    .container-avis form {
        max-width: 600px;
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .container-avis form h2 {
        margin-bottom: 20px;
        color: #333;
    }

    .container-avis form label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
        color: #555;
    }

    .container-avis form input,
    .container-avis form textarea,
    .container-avis form select {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .container-avis form textarea {
        height: 100px;
        resize: none;
    }

    .container-avis form button {
        width: 100%;
        background-color: #007bff;
        color: #fff;
        padding: 10px;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .container-avis form button:hover {
        background-color: #0056b3;
    }
</style>

<body>
<div class="container-avis">
    <form method="POST" action="">
        <h2>Ajouter un Avis</h2>
        <div>
            <label for="titre">Titre de l'avis :</label>
            <input type="text" id="titre" name="titre" required>
        </div>
        <div>
            <label for="commentaire">Commentaire :</label>
            <textarea id="commentaire" name="commentaire" required></textarea>
        </div>
        <div>
            <label for="note">Note (1 à 5) :</label>
            <select id="note" name="note" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div><br/>
        <button type="submit">Valider l'avis</button>
    </form>
</div>

</body>
<?php
include "include/footer.php";
?>
