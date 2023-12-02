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
  <body>
    <div id="base_container">
      <div id="content">
        <header>
          <h1>Wyszukaj znajomego i go dodaj!</h1>
          <a href="my_friends.php">Przyjaciele</a>
          <a href="my_account.php">Moje konto</a>
          <a href="logout.php">Wyloguj</a>
        </header>

        <form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?> method="post">
          <label for="first_name">Imie:</label>
          <input type="text" id="first_name" name="first_name" required />

          <label for="last_name">Nazwisko:</label>
          <input type="text" id="last_name" name="last_name" required />

          <input type="submit" value="Wyszukaj" />
        </form>
        <?php 
            if ($_SERVER["REQUEST_METHOD"] == "POST" &&
                isset($_POST['first_name']) &&
                isset($_POST['last_name']))
            {
                $searchFirstName = $_POST['first_name'];
                $searchLastName = $_POST['last_name'];
                $searchQuery = "
                                SELECT *
                                FROM users
                                WHERE first_name LIKE :firstName
                                AND last_name LIKE :lastName
                            ";

                $searchStatement = $conn->prepare($searchQuery);
                $searchStatement->bindParam(':firstName', $searchFirstName, PDO::PARAM_STR);
                $searchStatement->bindParam(':lastName', $searchLastName, PDO::PARAM_STR);
                $searchStatement->execute();

                $searchResults = $searchStatement->fetchAll(PDO::FETCH_ASSOC);
                if ($searchResults){
                    
                    $addFriendQuery = "
                                    INSERT INTO friends (user_id, friend_id)
                                    VALUES (:userId, :friendId)
                                    ";

                    #foreach ($searchResults as $result) {
                    #    foreach ($result as $key => $value) {
                    #        echo $key . ": " . $value . "<br>";
                    #    }
                    #    echo "<hr>";}

                    $friendId = $searchResults[0]['id'];
                    $addFriendStatement = $conn->prepare($addFriendQuery);
                    $addFriendStatement->bindParam(':userId', getSessionVariable("user_id"), PDO::PARAM_INT);
                    $addFriendStatement->bindParam(':friendId', $friendId, PDO::PARAM_INT);
                    $addFriendResult = $addFriendStatement->execute();

                    if ($addFriendResult){
                        echo "<h1>Udało się ! Przyjaciel dodany.</h1>";
                    }else{
                        echo "<h1 id='error_msg'>Nie udało się dodać przyjaciela...</h1>";
                    }

                }else {
                    echo "<h1 id='error_msg'>Nie ma nikogo takiego!</h1>";
                }
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
