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


$categorie = isset($_GET['categorie']) ? $_GET['categorie'] : '';

// Query pentru preluarea filmelor
$sql = "SELECT F.Titlu AS Titlu , F.DataLansarii AS DataLansarii, F.Durata AS Durata, CF.Nume AS CategorieFilm, F.DescriereFilm AS DescriereFilm, F.Poster AS Poster
        FROM Filme F JOIN CategoriiFilme CF ON (F.CategorieFilmID=CF.CategorieFilmID)";

// Adaugă condiția de categorie dacă este selectată
$params = [];
if ($categorie) {
    $sql = "SELECT F.Titlu AS Titlu , F.DataLansarii AS DataLansarii, F.Durata AS Durata, CF.Nume AS CategorieFilm, F.DescriereFilm AS DescriereFilm, F.Poster AS Poster
        FROM Filme F JOIN CategoriiFilme CF ON (F.CategorieFilmID=CF.CategorieFilmID)
         WHERE CF.Nume = ?";
    $params[] = $categorie;
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
    <title>Filme</title>
    
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

    <h2> Filme </h2>
<?php
    if (isset($_SESSION['Rol_Admin']) && $_SESSION['Rol_Admin'] == 1) { 
        echo '<div class="admin-link">
        <a class="file_admin" href="adaugare_filme_bd.php">Adaugare Filme in BD pentru Admin</a> <br>
        <a class="file_admin" href="modificare_filme_bd.php">Modificare Filme in BD pentru Admin</a> <br>
        <a class="file_admin" href="sterge_filme_bd.php">Sterge Filme pentru in BD Admin</a> <br>
      </div>';
    
}
    ?>

    <form method="GET" action="">
        <label for="categorie">Alege categoria:</label>
        <select name="categorie" id="categorie">
            <option value="">Toate</option>
            <option value="Acțiune" <?= $categorie === 'Acțiune' ? 'selected' : '' ?>>Acțiune</option>
            <option value="Comedie" <?= $categorie === 'Comedie' ? 'selected' : '' ?>>Comedie</option>
            <option value="Science Fiction" <?= $categorie === 'Science Fiction' ? 'selected' : '' ?>>Science Fiction</option>
            <option value="Horror" <?= $categorie === 'Horror' ? 'selected' : '' ?>>Horror</option>
            <option value="Animație" <?= $categorie === 'Animație' ? 'selected' : '' ?>>Animație</option>
            <option value="Romantic" <?= $categorie === 'Romantic' ? 'selected' : '' ?>>Romantic</option>
        </select>
        <button type="submit">Filtrează</button>
    </form>

    <h2>Lista filme</h2>
    <?php if (sqlsrv_has_rows($stmt)): ?>
        <table>
            <thead>
                <tr>
                    <th>Titlu</th>
                    <th>Data Lansării</th>
                    <th>Durată</th>
                    <th>Categorie</th>
                    <th>Descriere Film</th>
                    <th>Poster</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Titlu']) ?></td>
                        <td><?= $row['DataLansarii']->format('Y-m-d') ?></td>
                        <td><?= $row['Durata'] ?></td>
                        <td><?= htmlspecialchars($row['CategorieFilm']) ?></td>
                        <td><?= htmlspecialchars($row['DescriereFilm']) ?></td>
                        <td><img src="<?= htmlspecialchars($row['Poster'])?>" width=80 height=100></td>
                        
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nu există filme în categoria selectată.</p>
    <?php endif; ?>


    

</body>
</html>