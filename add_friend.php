<?php require "connect_to_db.php"; require "start_session.php";?>

<script>

  if ( window.history.replaceState ) {
    window.history.replaceState( null, null, window.location.href );
  }

</script>

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
  <body class="registration-page">
    <div id="base_container">
      <div id="content">
        <header>
          <h1>Wyszukaj znajomego i go dodaj!</h1>
          <a href="my_friends.php">Przyjaciele</a>
          <a href="add_friend.php">Dodaj znajomego</a>
          <a href="my_account.php">Moje konto</a>
          <a href="logout.php">Wyloguj</a>
          <div id="account_info">
            <?php
            // Sprawdzenie, czy użytkownik jest zalogowany
            if (isset($_SESSION['email'])) {
                echo '<p>Zalogowano jako: ' . $_SESSION['email'] . '</p>';
            }
            ?>
          </div>
        </header>
        <?php 
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST["dodaj_do_przyjaciol"])) {
                    $friendId = $_POST["friend_id"];
                    

                    $addFriendQuery = "
                                    INSERT INTO friends (user_id, friend_id)
                                    VALUES (:userId, :friendId), (:friendId, :userId);
                                    ";

                    $addFriendStatement = $conn->prepare($addFriendQuery);
                    $user_id = getSessionVariable("user_id");
                    $addFriendStatement->bindParam(':userId', $user_id, PDO::PARAM_INT);
                    $addFriendStatement->bindParam(':friendId', $friendId, PDO::PARAM_INT);
                    $addFriendResult = $addFriendStatement->execute();

                    if ($addFriendResult){
                        echo "<h1>Udało się ! Przyjaciel dodany.</h1>";
                    }else{
                        echo "<h1 id='error_msg'>Nie udało się dodać przyjaciela...</h1>";
                    }

                }
            }


            $sql = "SELECT id, first_name, last_name FROM users 
                    WHERE id != :user_id 
                    AND id NOT IN (SELECT friend_id FROM friends WHERE user_id = :user_id)
                    AND id NOT IN (SELECT user_id FROM friends WHERE friend_id = :user_id)";
    
            $stmt = $conn->prepare($sql);
            $user_id = getSessionVariable("user_id");
            $stmt->bindParam(":user_id",$user_id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<form method='post'>";
                    echo "<p>" . $row["first_name"] . " " . $row["last_name"] . " 
                        <button type='submit' name='dodaj_do_przyjaciol'>Dodaj do przyjaciół</button>";
                    echo "<input type='hidden' name='friend_id' value='" . $row["id"] . "'></p>";
                    echo "</form>";
                }
            } else {
                echo "Brak użytkowników.";
            }

        ?>
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
