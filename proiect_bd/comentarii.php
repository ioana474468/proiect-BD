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

$userAles=isset($_GET['userAles'])?$_GET['userAles']:'';
// Query pentru preluarea comentariilor
$sql = "SELECT U.Nume AS Nume, C.ContinutComentariu AS ContinutComentariu, C.DataPostarii AS DataPostarii
FROM Utilizatori U JOIN Comentarii C ON (U.UtilizatorID=C.UtilizatorID)";

// Adaugă condiția de categorie dacă este selectată
$params = [];
if ($user ) {
    $sql = "SELECT U.Nume AS Nume, C.ContinutComentariu AS ContinutComentariu, C.DataPostarii AS DataPostarii
FROM Utilizatori U JOIN Comentarii C ON (U.UtilizatorID=C.UtilizatorID)
WHERE Nume LIKE ?";
    $params[] = '%'.$userAles.'%';
}

// Execută query-ul
$stmt = sqlsrv_query($conn, $sql, $params);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}


$sql2= "SELECT F.Titlu, COUNT(C.ComentariuID) AS Nr_Comentarii
            FROM Filme F JOIN Reviewuri R ON (F.FilmID=R.FilmID)
                        JOIN Comentarii C ON (C.ReviewID=R.ReviewID)
            GROUP BY F.Titlu
            ORDER BY COUNT(C.ComentariuID) DESC";
$params=[];
$stmt2 = sqlsrv_query($conn, $sql2, $params);
if ($stmt2 === false) {
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

    <h2> Comentarii </h2>

    <form method="GET" action="">
        <label for="userAles">Alege userul:</label>
        <input type='text' name="userAles" id="userAles">
        
        <button type="submit">Filtrează</button>
    </form>

    <h2>Lista comentarii</h2>
    <?php if (sqlsrv_has_rows($stmt)): ?>
        <table>
            <thead>
                <tr>
                    <th>Utilizator</th>
                    <th>Continut</th>
                    <th>Data Postarii</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Nume']) ?></td>
                        <td><?= htmlspecialchars($row['ContinutComentariu']) ?></td>
                        <td><?= $row['DataPostarii']->format('Y-m-d') ?></td>
                        
                        
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nu există comentarii în categoria selectată.</p>
    <?php endif; ?>

    Nr com referitoare la un film:

    <?php if (sqlsrv_has_rows($stmt2)): ?>
        <table>
            <thead>
                <tr>
                    <th>Utilizator</th>
                    <th>Continut</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php while ($row = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Titlu']) ?></td>
                        <td><?= htmlspecialchars($row['Nr_Comentarii']) ?></td>
                        
                        
                        
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nu există comentarii în categoria selectată.</p>
    <?php endif; ?>


    

</body>
</html>