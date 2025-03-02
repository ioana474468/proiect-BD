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

// Preia detalii despre utilizatorul conectat
$user = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleshome.css?v=1.0">
    <title>Pagina profil</title>
    
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

    <h1>Bun venit, <?php echo $_SESSION['username']; if($_SESSION['Rol_Admin']==1) echo "(Admin)"; ?>!</h1>
    <h2>Detaliile tale:</h2>
    <?php
			$sql = "SELECT Nume, Email, DataNasterii, Tara, DataInregistrarii
				FROM Utilizatori 
                WHERE Nume=?";

            $params = array($user);
            $stmt = sqlsrv_query($conn, $sql, $params);
            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }
        
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
			
		?>

<?php if ($row): ?>
        <table >
            <thead>
                <tr>
                <th>Câmp</th>
                <th>Valoare</th>
                </tr>
            </thead>
            <tr>
                <td>Nume</td>
                <td><?= htmlspecialchars($row['Nume']) ?></td>
            </tr>
            <tr>
                <td>Email</td>
                <td><?= htmlspecialchars($row['Email']) ?></td>
            </tr>
            <tr>
                <td>Data Nașterii</td>
                <td><?= $row['DataNasterii'] ? $row['DataNasterii']->format('Y-m-d') : 'Nespecificată' ?></td>
            </tr>
            <tr>
                <td>Țara</td>
                <td><?= htmlspecialchars($row['Tara']) ?></td>
            </tr>
            <tr>
                <td>Data Înregistrării</td>
                <td><?= $row['DataInregistrarii'] ? $row['DataInregistrarii']->format('Y-m-d H:i:s') : 'Nespecificată' ?></td>
            </tr>
        </table>
        <?php else: ?>
        <p>Nu s-au găsit date pentru utilizatorul specificat.</p>
    <?php endif; ?>

    <h2>Adaugă un film în lista ta</h2>
    <form method="POST" action="">
        <label for="film">Caută film:</label>
        <input type="text" id="film" name="film" required>
        <button type="submit">Adaugă în listă</button>
    </form>

    <?php $username = $_SESSION['username'];
$sql = "SELECT UtilizatorID FROM Utilizatori WHERE Nume = ?";
$params = array($username);
$stmtAdd = sqlsrv_query($conn, $sql, $params);

if ($stmtAdd === false) {
    die(print_r(sqlsrv_errors(), true));
}

$row = sqlsrv_fetch_array($stmtAdd, SQLSRV_FETCH_ASSOC);
if (!$row) {
    echo "Eroare: utilizatorul nu a fost găsit.";
    exit;
}

$utilizatorID = $row['UtilizatorID'];

// Preluarea filmului introdus de utilizator
$film = isset($_POST['film']) ? trim(htmlspecialchars($_POST['film'])) : '';

if ($film) {
    // Cod pentru căutare și inserare a filmului
    $sql = "SELECT FilmID FROM Filme WHERE Titlu = ?";
    $params = array($film);
    $stmtAdd = sqlsrv_query($conn, $sql, $params);

    if ($stmtAdd === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmtAdd, SQLSRV_FETCH_ASSOC);
    if (!$row) {
        echo "Eroare: filmul nu a fost găsit.";
    } else {
        $filmID = $row['FilmID'];

        // Verifică dacă filmul este deja în lista utilizatorului
        $sql = "SELECT * FROM UtilizatoriFilme WHERE UtilizatorID = ? AND FilmID = ?";
        $params = array($utilizatorID, $filmID);
        $stmtAdd = sqlsrv_query($conn, $sql, $params);

        if ($stmtAdd === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_fetch_array($stmtAdd, SQLSRV_FETCH_ASSOC)) {
            echo "Film deja adăugat în lista ta!";
        } else {
            // Inserează filmul în lista de vizionare
            $sql = "INSERT INTO UtilizatoriFilme (UtilizatorID, FilmID, DataAdaugarii)
                    VALUES (?, ?, GETDATE())";
            $params = array($utilizatorID, $filmID);

            $stmtAdd = sqlsrv_query($conn, $sql, $params);
            if ($stmtAdd === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            echo "Film adăugat cu succes în lista ta!";
        }
    }
}
?>




<h2>Șterge un film din lista ta</h2>
    <form method="POST" action="">
        <label for="filmSters">Caută film:</label>
        <input type="text" id="filmSters" name="filmSters" required>
        <button type="submit">Sterge din listă</button>
    </form>

    <?php $username = $_SESSION['username'];
$sql = "SELECT UtilizatorID FROM Utilizatori WHERE Nume = ?";
$params = array($username);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
if (!$row) {
    echo "Eroare: utilizatorul nu a fost găsit.";
    exit;
}

$utilizatorID = $row['UtilizatorID'];

// Preluarea filmului introdus de utilizator
$film = isset($_POST['filmSters']) ? trim(htmlspecialchars($_POST['filmSters'])) : '';

if ($film) {
    // Cod pentru căutare și stergere a filmului
    $sql = "SELECT FilmID FROM Filme WHERE Titlu = ?";
    $params = array($film);
    $stmtDel = sqlsrv_query($conn, $sql, $params);

    if ($stmtDel === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmtDel, SQLSRV_FETCH_ASSOC);
    if (!$row) {
        echo "Eroare: filmul nu a fost găsit.";
    } else {
        $filmID = $row['FilmID'];

        
            // Sterge
            $sql = "DELETE FROM UtilizatoriFilme WHERE UtilizatorID = ? AND FilmID = ?";
            $params = array($utilizatorID, $filmID);

            $stmtDel = sqlsrv_query($conn, $sql, $params);
            if ($stmtDel === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            echo "Film sters cu succes din lista ta!";
        }
    }

?>

<h2>Modifică statusul filmului</h2>
<form method="POST" action="">
    <label for="filmUp">Nume Film:</label>
    <input type="text" id="filmUp" name="filmUp">

    <label for="status">Status Vizionare:</label>
    <select name="status" id="status">
        <option value="1">Vizionat</option>
        <option value="0">Nevizionat</option>
    </select>
    <select name="preferat" id="status">
        <option value="1">Favorite</option>
        <option value="0">-</option>
    </select>

    <button type="submit" name="updateStatus">Modifică Status</button>
</form>

<?php
$film = isset($_POST['filmUp']) ? trim(htmlspecialchars($_POST['filmUp'])) : '';
$status = isset($_POST['status']) ? $_POST['status'] : null;
$favorit= isset($_POST['preferat'])? $_POST['preferat'] : null;

if ($film) {
    // Cod pentru căutare și updatare a filmului
    $sql = "SELECT FilmID FROM Filme WHERE Titlu = ?";
    $params = array($film);
    $stmtUp = sqlsrv_query($conn, $sql, $params);

    if ($stmtUp === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmtUp, SQLSRV_FETCH_ASSOC);
    if (!$row) {
        echo "Eroare: filmul nu a fost găsit.";
    } else {
        $filmID = $row['FilmID'];
            // Updateaza
            $sql = "UPDATE UtilizatoriFilme SET StatusVizionare = ?, Favorit = ? WHERE UtilizatorID = ? AND FilmID = ?";
            $binaryStatus = ($status == "1") ? 0x01 : 0x00;
            $binaryFavorite= ($favorit == "1") ? 0x01 : 0x00;
            $params = array($binaryStatus,$binaryFavorite,$utilizatorID, $filmID);

            $stmtUp = sqlsrv_query($conn, $sql, $params);
            if ($stmtUp === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            echo "Film updatat cu succes din lista ta!";
        }
    }
?>



    
<h2>Listă filme: </h2>

<?php
// Asigură-te că sesiunea este activă și că utilizatorul este conectat
if (!isset($_SESSION['username'])) {
    echo "<p>Eroare: utilizatorul nu este conectat.</p>";
    exit;
}

$user = $_SESSION['username']; // Preia numele utilizatorului din sesiune

// Obține lista de filme pentru utilizator
$sql = "SELECT F.Titlu AS Titlu, 
               UF.Favorit AS Favorit, 
               UF.DataAdaugarii AS DataAdaugarii, 
               UF.StatusVizionare AS StatusVizionare, 
               R.Rating AS Rating
        FROM Utilizatori U 
        JOIN UtilizatoriFilme UF ON U.UtilizatorID = UF.UtilizatorID
        JOIN Filme F ON UF.FilmID = F.FilmID
        LEFT JOIN Reviewuri R ON F.FilmID = R.FilmID AND U.UtilizatorID = R.UtilizatorID
        WHERE U.Nume = ?";

$params = array($user);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Verifică dacă sunt rânduri în rezultat
if (sqlsrv_has_rows($stmt)): ?>
    <table>
        <thead>
            <tr>
                <th>Film</th>
                <th>Favorit</th>
                <th>Data Adăugării</th>
                <th>Status Vizionare</th>
                <th>Rating</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['Titlu']) ?></td>
                    <td><?= bin2hex($row['Favorit']) === '01' ? 'Da' : 'Nu' ?></td>
                    <td><?= $row['DataAdaugarii'] ? $row['DataAdaugarii']->format('Y-m-d') : 'Nespecificată' ?></td>
                    <td><?= bin2hex($row['StatusVizionare']) === '01' ? 'Vizionat' : 'Nevizionat' ?></td>
                    <td><?= $row['Rating'] ? $row['Rating'] : '-' ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Nu s-au găsit filme în lista ta.</p>
<?php endif; ?>

<?php
// Încheie query-ul pentru a elibera resursele
sqlsrv_free_stmt($stmt);
?>





</body>
</html>