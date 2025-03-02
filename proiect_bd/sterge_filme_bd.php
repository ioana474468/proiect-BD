<?php
session_start(); // Inițializează sesiunea

$servername = "DESKTOP-OEU6GV8\SQLEXPRESS";
$database = "BDFilme";
$uid = "";
$password = "";

$connection = [
    "Database" => $database,
    "Uid" => $uid,
    "PWD" => $password,
    "Encrypt" => true,
    "TrustServerCertificate" => true,
    "CharacterSet" => "UTF-8",
];

$conn = sqlsrv_connect($servername, $connection);
if (!$conn) {
    die(print_r(sqlsrv_errors(), true));
}

// Verifică dacă utilizatorul este conectat
if (!isset($_SESSION['username'])) {
    echo "Trebuie să te conectezi pentru a accesa această pagină.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Preia titlul filmului din formular
    $titlu = $_POST['titlu'];

    // Căutăm filmul în baza de date folosind titlul
    
    // Ștergem filmul din baza de date
    $sqlDelete = "DELETE FROM Filme WHERE Titlu = ?";
    $stmtDelete = sqlsrv_query($conn, $sqlDelete, array($titlu));

    if ($stmtDelete === false) {
        die(print_r(sqlsrv_errors(), true));  // Dacă apare o eroare la ștergere
    } else {
        echo "Filmul a fost șters cu succes!";
    }
}

?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleshome.css?v=1.0">
    <title>Pagina speciala admin sterge film</title>
</head>
<body>
<header>
    <h1><img class="logo" src="poze/vintage.jpg"> FilmeBune.com</h1>
    <p>Descoperă cele mai noi și populare filme</p>

    <nav>
        <a href="home.php">Acasă</a>
        <a href="actori.php">Actori</a>
        <a href="filme.php">Filme</a>
        <a href="regizori.php">Regizori</a>
        <a href="utilizatori.php">Utilizatori</a>
        <a href="reviewuri.php">Reviewuri</a>
        <a href="comentarii.php">Comentarii</a>
        <a href="profil.php">Profil</a>
        <a href="metrici.php">Metrici</a>
    </nav>
</header>

<h2>Șterge un film din baza de date</h2>

<form method="POST" action="">
    <label for="titlu">Titlu Film (pentru ștergere):</label>
    <input type="text" id="titlu" name="titlu" required><br><br>

    <?php if (isset($filmData)): ?>
        <h3>Filmul găsit:</h3>
        <p><strong>Titlu:</strong> <?= htmlspecialchars($filmData['Titlu']) ?></p>
    <?php endif; ?>

    <button type="submit">Șterge Filmul</button>
</form>

</body>
</html>
