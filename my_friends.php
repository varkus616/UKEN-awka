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
          <h1>Twoi przyjaciele</h1>
          <a href="my_friends.php">Przyjaciele</a>
          <a href="my_account.php">Moje konto</a>
          <a href="logout.php">Wyloguj</a>
        </header>

            <?php 

                $friendsQuery = "
                                SELECT users.first_name, users.last_name
                                FROM friends
                                JOIN users ON friends.friend_id = users.id
                                WHERE friends.user_id = :userId
                                ";

                $friendsStatement = $conn->prepare($friendsQuery);
                $friendsStatement->bindParam(':userId', getSessionVariable("user_id"), PDO::PARAM_INT);

                $friendsStatement->execute();

                $friends = $friendsStatement->fetchAll(PDO::FETCH_ASSOC);

            
                echo '<table border="1">';
                echo '<tr><th>Name</th><th>Surname</th></tr>';

                foreach ($friends as $friend) {
                    echo '<tr>';
                    echo '<td>' . $friend['first_name'] . '</td>';
                    echo '<td>' . $friend['last_name'] . '</td>';
                    echo '</tr>';
                }

                echo '</table>';


                

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
