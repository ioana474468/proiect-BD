<?php
session_start();

if(isset($_SESSION['username'])) {
    ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleshome.css">
    <title>Pagina Principala</title>
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

    <h2> Bine ai venit, <?php echo $_SESSION['username']; if($_SESSION['Rol_Admin']==1) echo "(Admin)"; ?>!


    <a class="logout" href="logout.php">Log out</a></h2>
    <div class="continut">
    <p> Bine ai venit în comunitatea FilmeBune.com, platforma ta dedicată pasionaților de filme! 
        Descoperă, discută și împărtășește păreri despre filme care te-au marcat. Aici poți:</p>
<ul>
<li>Lăsa recenzii și ratinguri pentru filmele pe care le-ai vizionat.</li>
<li>Explora opiniile comunității despre cele mai populare producții.</li>
<li>Găsi recomandări personalizate bazate pe gusturile tale.</li>
<li>Interacționa cu alți cinefili și să faci schimb de idei despre poveștile care te-au impresionat.</li></ul> 
<p>Indiferent că ești în căutarea următorului film de vizionat sau vrei să-ți exprimi părerea despre 
o capodoperă sau un eșec, FilmeBune.com este locul ideal pentru tine. 
Transformă-ți pasiunea pentru filme într-o experiență interactivă!</p>
</div>

    <img class="rola" src="poze/rola_film.jpg">

    <footer>
        <p>&copy; 2024 FilmeBune.com. Toate drepturile rezervate.</p>
    </footer>
    </body>
</html>
<?php
}
else{
    header("Location: form.php");
    exit();
}
    

