<?php require "connect_to_db.php"; require "start_session.php";
function getCommentsForPost($postId,$conn) {
  $commentsQuery = "SELECT c.*
  FROM comments c
  JOIN post_comments pc ON c.id = pc.comment_id
  WHERE pc.post_id = :post_id";
  $stmt = $conn->prepare($commentsQuery);
  $stmt->bindParam('post_id', $postId, PDO::PARAM_INT);
  $stmt->execute();

  
  $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $res;
}?>

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
          <a href="add_friend.php">Dodaj znajomego</a>
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
            if (isset($_POST["new_comment"]) &&
            isset($_POST["dodaj_komentarz"]))
          {
              $comment = $_POST["new_comment"];
              $comment_postID = $_POST["post_id"];
              $stmt = $conn->prepare("INSERT INTO comments (content, user_id, post_id, comment_date) VALUES (:content, :author, :post_id,NOW())");
          
              $stmt->bindParam(':content', $comment);
              $stmt->bindParam(':author',  $_SESSION['user_id']);
              $stmt->bindParam(':post_id', $comment_postID);
          
              $stmt->execute();
              $commentID = $conn->lastInsertId();
          
              $stmt = $conn->prepare("INSERT INTO post_comments (post_id, comment_id) VALUES (:post_id, :comment_id)");
              $stmt->bindParam(':post_id', $comment_postID);
              $stmt->bindParam(':comment_id', $commentID);
              $stmt->execute();
          
          }
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

                  
                /*if ($res && $posts) {
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
                }*/
                if ($res && $posts) {
                  echo "<div class='posts'>";
                  foreach ($posts as $post) {
                      echo "<div class='post'>";
                      echo "<div><strong>Post ID:</strong> " . $post['id'] . "</div>";
                      echo "<div><strong>Tytuł:</strong> " . $post['title'] . "</div>";
                      echo "<div><strong>Treść:</strong> " . $post['content'] . "</div>";
                      echo "<div><strong>Czas utworzenia:</strong> " . $post['created_at'] . "</div>";
              
                      // Pobierz komentarze dla danego posta
                      $comments = getCommentsForPost($post['id'],$conn);
                      if ($comments) {
                          echo "<div><strong>Komentarze:</strong></div>";
                          echo "<div class='comments'>";
                          foreach ($comments as $comment) {
                              echo "<div class='comment'>";
                              echo "<div><strong>Komentarz ID:</strong> " . $comment['id'] . "</div>";
                              $commentsQuery = "SELECT users.first_name, users.last_name FROM users WHERE users.id=:id_user";
                              $stmt = $conn->prepare($commentsQuery);
                              $stmt->bindParam('id_user', $comment['user_id'], PDO::PARAM_INT);
                              $stmt->execute();
      
                              $res = $stmt->fetch();
                              echo "<div><strong>Autor:</strong> " . $res['first_name']." ".$res['last_name']. "</div>";
                              
                              echo "<div><strong>Treść:</strong> " . $comment['content'] . "</div>";
                              echo "<div><strong>Czas utworzenia:</strong> " . $comment['comment_date'] . "</div>";
                              echo "</div>";
                          }
                          echo "</div>";
                      } else {
                          echo "<div><em>Brak komentarzy</em></div>";
                      }
                      echo "<form method='post' action=". 
                      htmlspecialchars($_SERVER['PHP_SELF']).">";
                      echo "<label for='new_comment'>Nowy komentarz:</label>";
                      echo "<br>
                            <input type='hidden' name='post_id' value='" . $post["id"] . "'>
                            <textarea id='new_comment' name='new_comment' rows='1' cols='1' require></textarea>
                            <br>";
                      echo "<button type='submit' name='dodaj_komentarz'>Dodaj komentarz</button>";
                      echo "</form>";
                      echo "</div>"; 
                  }
                  echo "</div>"; 
                } else {
                    echo "<h3>Brak postów</h3>";
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
