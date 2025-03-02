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
/*else {
echo 'connection established' ;
}*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $name = $_POST['username'];
    $pass = $_POST['password'];

    
    $sql = "SELECT LOWER(CONVERT(VARCHAR(MAX), Parola, 2)) AS PasswordHash,CONVERT(VARCHAR(MAX),RolAdmin,2) AS Rol_Admin FROM Utilizatori WHERE Nume = ?";
    $params = array($name);
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    if ($row) {
        $hashedPassword = $row['PasswordHash'];
       
        if (hash('sha256', $pass) === $hashedPassword) {
            echo "Logare cu succes! Bine ai venit, $name.";
            $_SESSION['username']=$name;
            $_SESSION['Rol_Admin']=$row['Rol_Admin'];
            header("Location: home.php");
            exit();
        } else {
            $_SESSION['error'] = "Parolă invalidă.";
            header("Location: form.php?error=Parolă invalidă.") ;
            exit();
        }
    } else {
        $_SESSION['error'] = "Username invalid.";
        header("Location:  form.php?error=Username invalid.");
            exit();
    }
    sqlsrv_free_stmt($stmt);
}



sqlsrv_close($conn);


?>

