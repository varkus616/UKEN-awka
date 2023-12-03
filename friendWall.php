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
          <h1>Twój przyjaciel</h1>
          <a href="my_friends.php">Przyjaciele</a>
          <a href="my_account.php">Moje konto</a>
          <a href="logout.php">Wyloguj</a>
        </header>

            <?php 
                if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['friend_id']) ){
                    setSessionVariable('friend_id', $_POST['friend_id']);
                  }
                $friend_id = getSessionVariable('friend_id');
                $friendQuery = "
                                SELECT first_name, last_name, date_of_birth, email
                                FROM users  
                                WHERE users.id = :friendID
                                ";

                $friendStatement = $conn->prepare($friendQuery);
                $friendStatement->bindParam(':friendID', $friend_id, PDO::PARAM_INT);

                $friendStatement->execute();

                $friend = $friendStatement->fetch(PDO::FETCH_ASSOC);

                echo "<p>Imie:".$friend['first_name']."</p>";
                echo "<p>Nazwisko:".$friend['last_name']."</p>";
                echo "<p>Data urodzenia:".$friend['date_of_birth']."</p>";

                ##########################################################

                $search_stmt = $conn->prepare(
                  "SELECT * FROM users WHERE users.email=:email"
                );
                $search_stmt->bindParam(":email", $friend['email']);
                $res = $search_stmt->execute();
                $fetch_res = $search_stmt->fetch(PDO::FETCH_ASSOC);
                if ($res &&
                    $fetch_res['number_of_posts'] != null &&
                    $fetch_res['number_of_posts'] > 0) {
                    $search_stmt = $conn->prepare(
                        "SELECT * FROM posts WHERE user_id=:user_id;"
                    );
                    $search_stmt->bindParam(":user_id", $fetch_res['id']);
                    $res = $search_stmt->execute();
                    $posts = $search_stmt->fetchAll(PDO::FETCH_ASSOC);

                  
                if ($res && $posts) {
                    echo "<div class='posts'>";
                    foreach ($posts as $post) {
                        echo "<div class='post'>";
                        echo "<div><strong>Post ID:</strong> " . $post['id'] . "</div>";
                        echo "<div><strong>Tytuł:</strong> " . $post['title'] . "</div>";
                        echo "<div><strong>Treść:</strong> " . $post['content'] . "</div>";
                        echo "<div><strong>Czas utworzenia:</strong> " . $post['created_at'] . "</div>";
                        echo "</div>";
                    }
                    echo "</div>";
                }
                }else {
                  echo "<h3>Brak postów</h3>";
                }
            ?>

        <p>Imie:<?php echo $_SESSION['first_name']?></p>
        <p>Nazwisko:<?php echo $_SESSION['last_name']?></p>
        <p>E-mail:<?php echo $_SESSION['email']?></p>
        <p>Data urodzenia:<?php echo $_SESSION['date_of_birth']?></p>
        <p>ID:<?php echo $_SESSION['user_id']?></p>


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
