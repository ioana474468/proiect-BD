<?php

session_start();

$servername = "DESKTOP-OEU6GV8\SQLEXPRESS";
$database = "BDFilme";
$uid="";
$password="";

$connection=[
"Database" => $database,
"Uid"=>$uid,
"PWD"=>$password,
"Encrypt" => true, 
"TrustServerCertificate" => true 
];

$conn = sqlsrv_connect($servername,$connection);
if(!$conn){
    die(print_r(sqlsrv_errors(),true));
}

// Preluare date din formular
$nume = $_POST['nume'];
$email = $_POST['email'];
$dataNasterii = $_POST['dataNasterii'] ?: null; // Valoare NULL dacă nu este completată
$tara = $_POST['tara'] ?: null; // Valoare NULL dacă nu este completată
$parola = $_POST['parola'];

$sql2= "SELECT COUNT(*) AS Num FROM Utilizatori WHERE Nume = ?";
$params2 = array($nume);
$stmt2 = sqlsrv_query($conn, $sql2, $params2);
$row2 = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC);
if ($row2['Num'] > 0) {
    echo "Numele de utilizator este deja folosit.";
    $_SESSION['error'] = "Numele de utilizator este deja folosit.";
    header("Location: signup_form.php?error=Numele de utilizator este deja folosit.") ;
    exit;
}

$sql3= "SELECT COUNT(*) AS Num FROM Utilizatori WHERE Email = ?";
$params3 = array($email);
$stmt3 = sqlsrv_query($conn, $sql3, $params3);
$row3 = sqlsrv_fetch_array($stmt3, SQLSRV_FETCH_ASSOC);
if ($row3['Num'] > 0) {
    echo "Emailul este deja folosit.";
    $_SESSION['error']="Emailul este deja folosit.";
    header("Location: signup_form.php?error=Emailul este deja folosit.") ;
    exit;
}


// Convertire parolă în VARBINARY(32)
$hashedPassword = hash('sha256', $parola, true); // true returnează raw binary data

// Pregătire interogare SQL
$sql = "INSERT INTO Utilizatori (Nume, Email, DataNasterii, Tara, Parola, DataInregistrarii)
        VALUES (?, ?, ?, ?, CONVERT(VARBINARY(32), ?), GETDATE())";
$params = array($nume, $email, $dataNasterii, $tara, $hashedPassword);

// Executare interogare
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
    header("Location: form.php?error=Eroare.") ;
} else {
    echo "Cont creat cu succes!";
    header("Location: home.php");
}

// Închidere conexiune
sqlsrv_close($conn);
?>
