<?php
session_start(); // Inițializează sesiunea

$servername = "DESKTOP-OEU6GV8\SQLEXPRESS";
$database = "BDFilme";
$uid="";
$password="";

$connection=[
"Database" => $database,
"Uid"=>$uid,
"PWD"=>$password,
"Encrypt" => true, 
"TrustServerCertificate" => true ,
"CharacterSet" => "UTF-8",
];

$conn = sqlsrv_connect($servername,$connection);
if(!$conn){
    die(print_r(sqlsrv_errors(),true));
}
/*else {
echo 'connection established' ;
}*/

// Verifică dacă utilizatorul este conectat
if (!isset($_SESSION['username'])) {
    echo "Trebuie să te conectezi pentru a accesa această pagină.";
    exit;
}

// Preia detalii despre utilizatorul conectat
$user = $_SESSION['username'];


$film = isset($_GET['film']) ? $_GET['film'] : '';

// Query pentru preluarea filmelor
$sql = "SELECT U.Nume AS Nume, F.Titlu AS Titlu, R.ContinutReview AS Review, R.Rating AS Rating, R.DataPostarii AS DataPostarii
FROM Reviewuri R JOIN Utilizatori U ON (R.UtilizatorID=U.UtilizatorID)
                JOIN Filme F ON (F.FilmID=R.FilmID)";

// Adaugă condiția de categorie dacă este selectată
$params = [];
if ($film ) {
    $sql = "SELECT U.Nume AS Nume, F.Titlu AS Titlu, R.ContinutReview AS Review, R.Rating AS Rating, R.DataPostarii AS DataPostarii
FROM Reviewuri R JOIN Utilizatori U ON (R.UtilizatorID=U.UtilizatorID)
                JOIN Filme F ON (F.FilmID=R.FilmID)
                WHERE F.Titlu LIKE ? ";
    $params[] = '%'.$film.'%';
}

// Execută query-ul
$stmt = sqlsrv_query($conn, $sql, $params);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleshome.css?v=1.0">
    <title>Reviewuri</title>
    
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

    <h2> Reviewuri </h2>

    <form method="GET" action="">
        <label for="film">Alege filmul:</label>
        <input type='text' name="film" id="film">
        
        <button type="submit">Filtrează</button>
    </form>

    <h2>Lista reviewuri</h2>
    <?php if (sqlsrv_has_rows($stmt)): ?>
        <table>
            <thead>
                <tr>
                    <th>Utilizator</th>
                    <th>Film</th>
                    <th>Continut</th>
                    <th>Rating</th>
                    <th>Data Postarii</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Nume']) ?></td>
                        <td><?= htmlspecialchars($row['Titlu']) ?></td>
                        <td><?= $row['Rating'] ?></td>
                        <td><?= htmlspecialchars($row['Review']) ?></td>
                        <td><?= $row['DataPostarii']->format('Y-m-d') ?></td>
                        
                        
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nu există reviewuri în categoria selectată.</p>
    <?php endif; ?>


    

</body>
</html>