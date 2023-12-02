<script>

if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}

</script>
<?php
    require "connect_to_db.php";
    $error_msg = "";
    $error = false;
    if ($_SERVER["REQUEST_METHOD"] == "POST" &&
        isset($_POST['e-mail']) &&
        isset($_POST['password'])) {

        $email = $_POST["e-mail"];
        $password = $_POST["password"];
    
      try {
        $search_stmt = $conn->prepare(
          "SELECT * FROM users WHERE users.email=:email"
        );

        
        $search_stmt->bindParam(':email',
                                $email);
        
        $res = $search_stmt->execute();
        $fetch_res = $search_stmt->fetch(PDO::FETCH_ASSOC);
        

        if ($res && $fetch_res){
            $hashed_password = $fetch_res['password'];
            if (password_verify($password, $hashed_password)){

              $role = 'user';

              if (
                  ($email === 'wiktor.sioła@student.up.kraków.pl' 
                  && password_verify($password, $fetch_res['password'])) 
                  ||
                  ($email === 'viktor.siropol@student.up.kraków.pl' 
                  && password_verify($password, $fetch_res['password']))
                )
                $role == 'admin';
            

              require "start_session.php";

              setSessionVariable('user_id', $fetch_res['id']);
              setSessionVariable('email',$email);
              setSessionVariable('first_name',$fetch_res['first_name']);
              setSessionVariable('last_name',$fetch_res['last_name']);
              setSessionVariable('date_of_birth',$fetch_res['date_of_birth']);
              setSessionVariable('role',$role);

              header("Location: my_account.php");
              exit();
              
          }else {
              $error_msg = "Logowanie niepoprawne. Proszę znowu spróbować.";
              $error = true;
          }
        }
      }catch(PDOException $e){
        echo "Błąd: " . $e->getMessage();
      }
    }
?>
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
          <h1>Logowanie</h1>
        </header>
        <?php if ($error) echo "<p id='error_msg'>".$error_msg."</p>";?>
        <form action="login.php" method="post">
          <label for="e-mail">E-mail:</label>
          <input type="text" id="e-mail" name="e-mail" required />

          <label for="password">Hasło:</label>
          <input type="password" id="password" name="password" required />

          <input type="submit" value="Zaloguj" />
        </form>
          <p>Nie masz konta ? <a href="registration.php">Zarejestruj się !</a></p>
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