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


// Query pentru preluarea comentariilor
$sql = "SELECT U.Nume, U.Tara, U.DataNasterii, U.DataInregistrarii , COUNT(UF.FilmID) AS Nr_filme_in_lista
FROM Utilizatori U JOIN UtilizatoriFilme UF ON (U.UtilizatorID=UF.UtilizatorID)
    GROUP BY U.Nume, U.Tara, U.DataNasterii, U.DataInregistrarii
    ORDER BY U.DataInregistrarii DESC";

// Adaugă condiția de categorie dacă este selectată
$util=isset($_GET['util'])?($_GET['util']):'';

if($util)
{
    $params[]='%'.$util.'%';
    $sql = "SELECT U.Nume, U.Tara, U.DataNasterii, U.DataInregistrarii , COUNT(UF.FilmID) AS Nr_filme_in_lista
FROM Utilizatori U JOIN UtilizatoriFilme UF ON (U.UtilizatorID=UF.UtilizatorID)
    WHERE U.Nume LIKE ?
    GROUP BY U.Nume, U.Tara, U.DataNasterii, U.DataInregistrarii
    ORDER BY U.DataInregistrarii DESC";
}
else {
    $params=[];
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
    <title>Utilizatori</title>
    
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

    <h2> Utilizatori </h2>

    <form method="GET" action="">
        <label for="util">Alege utilizatorul:</label>
        <input type='text' name="util" id="util">
        
        <button type="submit">Filtrează</button>
    </form>

    <h2>Listă utilizatori</h2>
    <?php if (sqlsrv_has_rows($stmt)): ?>
        <table>
            <thead>
                <tr>
                    <th>Utilizator</th>
                    <th>Țara</th>
                    <th>Data Nașterii</th>
                    <th>Data Înregistrării</th>
                    <th>Nr. de filme in listă</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Nume']) ?></td>
                        <td><?= htmlspecialchars($row['Tara']) ?></td>
                        <td><?= $row['DataNasterii']->format('Y-m-d') ?></td>
                        <td><?= $row['DataInregistrarii']->format('Y-m-d') ?></td>
                        <td><?= htmlspecialchars($row['Nr_filme_in_lista']) ?></td>
                        
                        
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nu există comentarii în categoria selectată.</p>
    <?php endif; ?>


    

</body>
</html>