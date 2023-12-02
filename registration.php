<?php
    require "connect_to_db.php";

    $user_is_already_registered = false;
    $registration_msg = "Rejestracja ukończona !";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $first_name = $_POST["first_name"];
        $last_name = $_POST["last_name"];
        $date_of_birth = $_POST["date_of_birth"];
        $email = $_POST["email"];
        $password = $_POST["password"];


        if (strlen($password) < 3){
            echo '<script>alert("Hasło zbyt krótkie!");</script>';
        }else {

          try { 
              $search_stmt = $conn->prepare(
                "SELECT email FROM users WHERE users.email=:email"
              );

              $search_stmt->bindParam(':email',
                                      $email);
              
              
              $res = $search_stmt->execute();
              $fetch_res = $search_stmt->fetch(PDO::FETCH_ASSOC);
              if ($res && gettype($fetch_res) == "boolean"){
                $insert_stmt = $conn->prepare(
                  "INSERT INTO users 
                  (first_name, last_name, 
                  email, password, date_of_birth) 
                  VALUES (:first_name, :last_name,
                 :email, :password, :date_of_birth)");

                $insert_stmt->bindParam(':first_name',
                                        $first_name);
                $insert_stmt->bindParam(':last_name',
                                        $last_name);
                $insert_stmt->bindParam(':email', $email);

                $hashed_password = password_hash($password,
                PASSWORD_DEFAULT);

                $insert_stmt->bindParam(':password', 
                $hashed_password);

                $insert_stmt->bindParam(':date_of_birth', 
                $date_of_birth);
                
                
                $insert_stmt->execute();
                
                $role = 'user';

                require "start_session.php";
                
                setSessionVariable('user_id',$conn->lastInsertId());
                setSessionVariable('email',$email);
                setSessionVariable('first_name',$first_name);
                setSessionVariable('last_name',$last_name);
                setSessionVariable('date_of_birth',$date_of_birth);
                setSessionVariable('role',$role);

                header("Location: my_account.php");
                exit();

              }else{
                $user_is_already_registered = true;
                $registration_msg = "Error: Taki e-mail jest już zarejestrowany ! Proszę użyć innego adresu e-mail.";
              }
          } catch (PDOException $e) {
              echo "Błąd: " . $e->getMessage();
          }
        }
    }

?>

<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0"
    />
    <link rel="stylesheet" href="styles/main.css" />
    <title>UKEN-awka</title>
  </head>
  <body>
    <div id="base_container">
      <div id="content">
        <header>
          <h1>Rejestracja</h1>
        </header>
        <?php //if ($registration_complete) echo "<p>Rejestracja ukończona !</p>"?>
        <?php if ($user_is_already_registered) echo "<p id='error_msg'>".$registration_msg."</p>"?>
        <form action=
        <?php echo htmlspecialchars($_SERVER["PHP_SELF"])?> method="post">

          <label for="first_name">Imię:</label>
          <input type="text" id="first_name" name="first_name" required />

          <label for="last_name">Nazwisko:</label>
          <input type="text" id="last_name" name="last_name" required />

          <label for="date_of_birth">Data urodzenia:</label>
          <input type="date" id="date_of_birth" name="date_of_birth" required />

          <label for="email">E-mail:</label>
          <input type="text" id="email" name="email" required />

          <label for="password">Hasło:</label>
          <input type="password" id="password" name="password" required />

          <input type="submit" value="Zarejestruj" />
        </form>

        <p>Już masz konto? <a href="login.php">Zaloguj się!</a></p>
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
    </div>
  </body>
</html>
