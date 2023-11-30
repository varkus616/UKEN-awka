<?php session_start();?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, 
    initial-scale=1.0"
    />
    <link rel="stylesheet" href="styles/main.css" />
    <title>UKEN-awka</title>
  </head>
  <body>
    <div id="base_container">
      <div id="content">
        <header>
            <h1>Mój profil</h1>
            <a href="logout.php">Wyloguj</a>
        </header>
        
        <p>Imie:<?php echo$_SESSION['first_name']?></p>
        <p>Nazwisko:<?php echo$_SESSION['last_name']?></p>
        <p>E-mail:<?php echo$_SESSION['email']?></p>
        <p>Data urodzenia:<?php echo$_SESSION['date_of_birth']?></p>
        
      </div>
      <footer>
        <a href="#">Informacje</a>
        <a href="#">Centrum Pomocy</a>
        <a href="#">Zasady użytkowania</a>
        <a href="#">Polityka prywatności</a>
        <a href="#">Polityka dotycząca plików cookie</a>
        <a href="#">Dostępność</a>
        <a href="#">Informacja o reklamach</a>
        <a href="#">Deweloperzy</a>
        <a href="#">Ustawienia</a>
        <span>© 2023 UKEN-awka Corp.</span>
      </footer>
      
  </body>
</html>
