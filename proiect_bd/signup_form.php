<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleslogin.css">
    <title>Signup Page</title>
    
</head>
<body>
<div class="login-div">
        <div class="logo">
        <img class="logo" src="poze/vintage.jpg">
        </div>
    <h2 class="title">FilmeBune.com</h2>


    <form method="POST" action="signup.php">

    <?php
if (isset($_SESSION['error'])) {
    echo "<p style='color: red;'>" . htmlspecialchars($_SESSION['error']) . "</p>";
    unset($_SESSION['error']); 
}
?>
    <label for="nume">Nume:</label>
        <input type="text" id="nume" name="nume" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="dataNasterii">Data Nașterii:</label>
        <input type="date" id="dataNasterii" name="dataNasterii"><br><br>

        <label for="tara">Țara:</label>
        <input type="text" id="tara" name="tara"><br><br>

        <label for="parola">Parolă:</label>
        <input type="password" id="parola" name="parola" required><br><br>


        <button type="submit">Sign Up</button>

    </form>
</div>
    <footer>
        <p>&copy; 2024 FilmeBune.com. Toate drepturile rezervate.</p>
    </footer>
</body>
</html>
