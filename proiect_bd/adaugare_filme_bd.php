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
"TrustServerCertificate" => true,
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Preia datele din formular
    $numeRegizor = $_POST['numeRegizor'];
    $numeCategorie = $_POST['numeCategorie'];
    $titlu = $_POST['titlu'];
    $dataLansarii = $_POST['dataLansarii'];
    $durata = $_POST['durata'];
    $descriereFilm = $_POST['descriereFilm'];
    $poster = $_POST['poster'];
    $premiuOscar = isset($_POST['premiuOscar']) ? 1 : 0; // Verifică dacă premiul Oscar este selectat

    // Căutăm ID-ul regizorului în baza de date
    $sqlRegizor = "SELECT RegizorID FROM Regizori WHERE Nume = ?";
    $stmtRegizor = sqlsrv_query($conn, $sqlRegizor, array($numeRegizor));
    if ($stmtRegizor === false || sqlsrv_has_rows($stmtRegizor) === false) {
        die("Eroare: Regizorul nu a fost găsit.");
    }
    $rowRegizor = sqlsrv_fetch_array($stmtRegizor, SQLSRV_FETCH_ASSOC);
    $regizorID = $rowRegizor['RegizorID'];

    // Căutăm ID-ul categoriei în baza de date
    $sqlCategorie = "SELECT CategorieFilmID FROM CategoriiFilme WHERE Nume = ?";
    $stmtCategorie = sqlsrv_query($conn, $sqlCategorie, array($numeCategorie));
    if ($stmtCategorie === false || sqlsrv_has_rows($stmtCategorie) === false) {
        die("Eroare: Categoria nu a fost găsită.");
    }
    $rowCategorie = sqlsrv_fetch_array($stmtCategorie, SQLSRV_FETCH_ASSOC);
    $categorieFilmID = $rowCategorie['CategorieFilmID'];

    // Inserăm filmul în baza de date
    $sql = "INSERT INTO Filme (RegizorID, CategorieFilmID, Titlu, DataLansarii, Durata, DescriereFilm, Poster, PremiuOscar)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $params = array($regizorID, $categorieFilmID, $titlu, $dataLansarii, $durata, $descriereFilm, $poster, $premiuOscar);

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));  // Dacă apare o eroare la inserare
    } else {
        echo "Filmul a fost adăugat cu succes!";
    }
}


?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleshome.css?v=1.0">
    <title>Pagina speciala admin adaugare film</title>
    
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

    
    <h2>Adaugă un film la baza de date</h2>
    
    <form method="POST" action="">
        <label for="numeRegizor">Regizor:</label>
        <input type="text" id="numeRegizor" name="numeRegizor" required><br><br>

        <label for="numeCategorie">Categorie Film:</label>
        <select name="numeCategorie" id="numeCategorie">
            <option value="Acțiune"> Acțiune</option>
            <option value="Comedie"> Comedie </option>
            <option value="Science Fiction" >Science Fiction</option>
            <option value="Horror" >Horror</option>
            <option value="Animație" >Animație</option>
            <option value="Romantic" >Romantic</option>
        </select>

        <label for="titlu">Titlu Film:</label>
        <input type="text" id="titlu" name="titlu" maxlength="50" required><br><br>

        <label for="dataLansarii">Data Lansării:</label>
        <input type="date" id="dataLansarii" name="dataLansarii" required><br><br>

        <label for="durata">Durata (minute):</label>
        <input type="text" id="durata" name="durata" required><br><br>

        <label for="descriereFilm">Descriere Film:</label>
        <textarea id="descriereFilm" name="descriereFilm" rows="4" cols="50"></textarea><br><br>

        <label for="poster">Link Poster:</label>
        <input type="text" id="poster" name="poster" maxlength="255"><br><br>

        <label for="premiuOscar">Premiu Oscar:</label>
        <input type="checkbox" id="premiuOscar" name="premiuOscar" value="1"><br><br>

        <button type="submit">Adaugă Film</button>
    </form>




</body>
</html>