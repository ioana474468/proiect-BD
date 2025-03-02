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


$actor = isset($_GET['actor']) ? $_GET['actor'] : '';

// Query pentru preluarea filmelor
$sql = "SELECT A.Nume AS Nume, A.Prenume AS Prenume, A.DataNasterii AS DataNasterii, A.Nationalitate AS Nationalitate, A.Biografie AS Biografie, A.Poza AS Poza, COUNT(FA.ActorID) AS NrFilme
 FROM Actori A JOIN FilmeActori FA ON (A.ActorID=FA.ActorID)
 GROUP BY Nume, Prenume, DataNasterii, Nationalitate, 
                 Biografie, Poza";

// Adaugă condiția de categorie dacă este selectată
$params = [];
if ($actor) {
    $sql = "SELECT A.Nume AS Nume, A.Prenume AS Prenume, A.DataNasterii AS DataNasterii, A.Nationalitate AS Nationalitate, A.Biografie AS Biografie, A.Poza AS Poza, COUNT(FA.ActorID) AS NrFilme
 FROM Actori A JOIN FilmeActori FA ON (A.ActorID=FA.ActorID)
 WHERE Nume LIKE ?
 GROUP BY Nume, Prenume, DataNasterii, Nationalitate, 
                 Biografie, Poza";
    $params[] = $actor . '%';
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
    <title>Actori</title>
    
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

    <h2> Actori </h2>

    <form method="GET" action="">
        <label for="nume">Nume Actor:</label>
        <input type="text" id="actor" name="actor">
        <button type="submit">Filtrează</button>
    </form>

    <h2>Lista actori</h2>
    <?php if (sqlsrv_has_rows($stmt)): ?>
        <table>
            <thead>
                <tr>
                    <th>Nume</th>
                    <th>Data Nasterii</th>
                    <th>Nationalitate</th>
                    <th>Biografie</th>
                    <th>Nr Filme</th>
                    <th>Poza</th>

                </tr>
            </thead>
            <tbody>
                <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                    <tr>
                        <td> <?= htmlspecialchars($row['Prenume'])?> <?= htmlspecialchars($row['Nume'])?> </td>
                        <td><?= $row['DataNasterii']->format('Y-m-d') ?></td>
                        <td><?= htmlspecialchars($row['Nationalitate']) ?></td>
                        <td><?= htmlspecialchars($row['Biografie']) ?></td>
                        <td><?= $row['NrFilme'] ?></td>
                        <td><img src="<?= htmlspecialchars($row['Poza'])?>" width=80 height=100></td>
                        
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nu există regizorul.</p>
    <?php endif; ?>


    

</body>
</html>