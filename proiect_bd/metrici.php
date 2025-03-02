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

if (!isset($_SESSION['username'])) {
    echo "Trebuie să te conectezi pentru a accesa această pagină.";
    exit;
}

$user = $_SESSION['username'];

$sql= "SELECT TOP 3 Titlu, RatingMediu
FROM (
    SELECT F.Titlu, AVG(R.Rating) AS RatingMediu
    FROM Filme F
    JOIN Reviewuri R ON F.FilmID = R.FilmID
    WHERE R.DataPostarii > DATEADD(MONTH, -6, GETDATE())
    GROUP BY F.Titlu
) AS Subquery
WHERE RatingMediu > 8.0
ORDER BY RatingMediu DESC;";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$sql2= "SELECT Titlu, Durata, DataLansarii,Poster
FROM Filme F
WHERE F.FilmID IN (
    SELECT UF.FilmID
    FROM UtilizatoriFilme UF
    GROUP BY UF.FilmID
    HAVING COUNT(UF.UtilizatorID) > ? 
)
ORDER BY Titlu";

$params[]=isset($_GET['numar']  ) ? $_GET['numar']   : 1;
$stmt2 = sqlsrv_query($conn, $sql2, $params);

if ($stmt2 === false) {
    die(print_r(sqlsrv_errors(), true));
}

$params=[];

$sql3= "SELECT F.Titlu, Durata, DataLansarii, AVG(R.Rating) AS MedieRating,Poster
FROM Filme F JOIN Reviewuri R ON (F.FilmID=R.FilmID)
WHERE F.FilmID IN (
    SELECT UF.FilmID
    FROM UtilizatoriFilme UF
    WHERE UF.StatusVizionare = 1
)
AND F.FilmID IN (
    SELECT R.FilmID
    FROM Reviewuri R
    GROUP BY R.FilmID
    HAVING AVG(R.Rating) > ?
)
GROUP BY F.Titlu, Durata, DataLansarii, Poster
ORDER BY AVG(R.Rating) DESC;";

$params[]=isset($_GET['rating_ales']  ) ? $_GET['rating_ales']  : 5;
$stmt3 = sqlsrv_query($conn, $sql3, $params);

if ($stmt3 === false) {
    die(print_r(sqlsrv_errors(), true));
}

$params=[];
$sql4="SELECT TOP 5 A.Prenume, A.Nume, COUNT(FA.FilmID) AS NumarFilme, A.Poza
FROM Actori A
JOIN FilmeActori FA ON A.ActorID = FA.ActorID
WHERE FA.FilmID IN (
    SELECT F.FilmID
    FROM Filme F
    WHERE F.CategorieFilmID = (SELECT CategorieFilmID FROM CategoriiFilme WHERE Nume = ?) 
)
GROUP BY A.Nume,A.Prenume,A.Poza
ORDER BY COUNT(FA.FilmID) DESC ";
$categorie = isset($_GET['categorie']) ? $_GET['categorie'] : '';
$params[]=$categorie;

$stmt4=sqlsrv_query($conn, $sql4, $params);
if ($stmt4 === false) {
    die(print_r(sqlsrv_errors(), true));
}

?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleshome.css?v=1.0">
    <title>Metrici</title>
    
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

    
    <h2>Top filme în funcție de rating în ultimele 6 luni</h2>
    <?php if (sqlsrv_has_rows($stmt)): ?>
        <table>
            <thead>
                <tr>
                    <th>Titlu</th>
                    <th>Rating Mediu</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Titlu']) ?></td>
                        <td><?= htmlspecialchars($row['RatingMediu']) ?></td>
         
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nu există filme in aceasta categorie.</p>
    <?php endif; ?>

    <h2>Filmele vizionate mai mult de un anumit număr de utilizatori</h2>
    <form method="GET" action="">
        <label for="numar">Alege numarul:</label>
        <input type='number' name="numar" id="numar">
        
        <button type="submit">Filtrează</button>
    </form>

    <?php if (sqlsrv_has_rows($stmt2)): ?>
        <table>
            <thead>
                <tr>
                    <th>Titlu</th>
                    <th>Durata</th>
                    <th>Data Lansarii</th>
                    <th>Poster</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php while ($row2 = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row2['Titlu']) ?></td>
                        <td><?= htmlspecialchars($row2['Durata']) ?></td>
                        <td><?= $row2['DataLansarii']->format('Y-m-d') ?></td>
                        <td><img src="<?= htmlspecialchars($row2['Poster'])?>" width=80 height=100> </td> 
         
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nu există filme in aceasta categorie.</p>
    <?php endif; ?>

    <h2>Filmele care au fost vizionate și care au primit recenzii pozitive de la utilizatori</h2>

    <form method="GET" action="">
        <label for="rating_ales">Alege ratingul de prag:</label>
        <input type='number' name="rating_ales" id="rating_ales">
        
        <button type="submit">Filtrează</button>
    </form>

    <?php if (sqlsrv_has_rows($stmt3)): ?>
        <table>
            <thead>
                <tr>
                    <th>Titlu</th>
                    <th>Durata</th>
                    <th>Data Lansarii</th>
                    <th>Rating mediu</th>
                    <th>Poster</th>

                </tr>
            </thead>
            <tbody>
                <?php while ($row3 = sqlsrv_fetch_array($stmt3, SQLSRV_FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row3['Titlu']) ?></td>
                        <td><?= htmlspecialchars($row3['Durata']) ?></td>
                        <td><?= $row3['DataLansarii']->format('Y-m-d') ?></td>
                        <td><?= htmlspecialchars($row3['MedieRating']) ?>
                        <td><img src="<?= htmlspecialchars($row3['Poster'])?>" width=80 height=100> </td> 

                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nu există filme in aceasta categorie.</p>
    <?php endif; ?>

    <h2>Actorii care au jucat in cele mai multe filme de o anumita categorie </h2>

    <form method="GET" action="">
        <label for="categorie">Alege categoria:</label>
        <select name="categorie" id="categorie">
            <option value="Acțiune" <?= $categorie === 'Acțiune' ? 'selected' : '' ?>>Acțiune</option>
            <option value="Comedie" <?= $categorie === 'Comedie' ? 'selected' : '' ?>>Comedie</option>
            <option value="Science Fiction" <?= $categorie === 'Science Fiction' ? 'selected' : '' ?>>Science Fiction</option>
            <option value="Horror" <?= $categorie === 'Horror' ? 'selected' : '' ?>>Horror</option>
            <option value="Animație" <?= $categorie === 'Animație' ? 'selected' : '' ?>>Animație</option>
            <option value="Romantic" <?= $categorie === 'Romantic' ? 'selected' : '' ?>>Romantic</option>
        </select>
        <button type="submit">Filtrează</button>
    </form>

    <?php if (sqlsrv_has_rows($stmt4)): ?>
        <table>
            <thead>
                <tr>
                    <th>Actor</th>
                    <th>Nr Filme</th>
                    <th>Poza</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php while ($row4 = sqlsrv_fetch_array($stmt4, SQLSRV_FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row4['Prenume'])?> <?= htmlspecialchars($row4['Nume']) ?></td>
                        <td><?= htmlspecialchars($row4['NumarFilme']) ?></td>
                        <td><img src="<?= htmlspecialchars($row4['Poza']) ?>" width=80 height=100></td>
         
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nu există actori in aceasta categorie.</p>
    <?php endif; ?>


    

</body>
</html>