<?php require "connect_to_db.php"; require "start_session.php";?>
<script>

if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}

</script>

<?php
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
    }

    if (isset($_POST["new_comment"]) &&
        isset($_POST["dodaj_komentarz"]))
        {
          $comment = $_POST["new_comment"];

        }

    $isAdmin = isset($_SESSION['role']) && getSessionVariable('role') === 'admin';
    if ($_SERVER["REQUEST_METHOD"] == "POST" && 
        isset($_POST["new_post_title"]) && 
        isset($_POST["new_post"])) {

      $title = $_POST["new_post_title"];
      $user_id = getSessionVariable('user_id');
      $content = $_POST["new_post"];
      $created_at = date('Y-m-d H:i:s'); 
          

      try {
          $insert_post_query = $conn->prepare("INSERT INTO posts (title, content, user_id, created_at) 
                                               VALUES (:title, :content, :user_id, :created_at)");
  
          $insert_post_query->bindParam(":title", $title);
          $insert_post_query->bindParam(":content", $content);
          $insert_post_query->bindParam(":user_id", $user_id);
          $insert_post_query->bindParam(":created_at", $created_at);
  
          $insert_post_query->execute();

          $update_counter_query = $conn->prepare("UPDATE users SET number_of_posts = number_of_posts + 1 
          WHERE id = :user_id");

          $update_counter_query->bindParam(":user_id", $user_id);

          $update_counter_query->execute();
  
      } catch (PDOException $e) {
          echo "Wystąpił problem podczas dodawania posta: " . $e->getMessage();
      }
  }

  
      // Usuwanie użytkownika
      if ($isAdmin && isset($_POST['delete_user'])) {
        $userToDelete = $_POST['delete_user'];
      
        try {
            $deleteUserQuery = $conn->prepare("DELETE FROM users WHERE id = :user_id");
            $deleteUserQuery->bindParam(":user_id", $userToDelete);
            $deleteUserQuery->execute();
      
            echo "Użytkownik został pomyślnie usunięty.";
        } catch (PDOException $e) {
            echo "Wystąpił problem podczas usuwania użytkownika: " . $e->getMessage();
        }
      }
  
      // Usuwanie posta
      if ($isAdmin && isset($_POST['delete_post'])) {
        $postToDelete = $_POST['delete_post'];
  
        try {
            $deletePostQuery = $conn->prepare("DELETE FROM posts WHERE id = :post_id");
            $deletePostQuery->bindParam(":post_id", $postToDelete);
            $deletePostQuery->execute();

  
            echo "Post został pomyślnie usunięty.";
        } catch (PDOException $e) {
            echo "Wystąpił problem podczas usuwania posta: " . $e->getMessage();
        }
      }
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

?>
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
  <body class="loggedin-page">
    <div id="base_container" >
      <div id="content">
      <header id="account_header">
          <h1>Mój profil</h1>
          <h2><a href="my_friends.php">Znajomi</a></h2>
          <h2><a href="add_friend.php">Dodaj znajomego</a></h2>
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
        
        <p>Imie:<?php echo $_SESSION['first_name']?></p>
        <p>Nazwisko:<?php echo $_SESSION['last_name']?></p>
        <p>E-mail:<?php echo $_SESSION['email']?></p>
        <p>Data urodzenia:<?php echo $_SESSION['date_of_birth']?></p>
        <p>ID:<?php echo $_SESSION['user_id']?></p>
        <form action=<?php 
        echo htmlspecialchars($_SERVER["PHP_SELF"])?> method="post">
          
          <label for="new_post_title">Tytuł</label>

          <br>
          <input type="text"  name="new_post_title" require>
          <br>

          <label for="new_post">Dodaj nowego posta</label>

          <br>
          <textarea id="new_post" name="new_post" rows="1" cols="1" require></textarea>
          <br>

          <input type="submit" value="Dodaj" name="submitbtn"/>

        </form>
        <?php
            $search_stmt = $conn->prepare(
              "SELECT * FROM users WHERE users.email=:email"
          );
          $search_stmt->bindParam(":email", $_SESSION['email']);
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
        
        <?php if ($isAdmin): ?>
          <!-- Formularz usuwania użytkownika -->
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
              <label for="delete_user">Usuń użytkownika o ID:</label>
              <input type="number" name="delete_user" required>
              <input type="submit" value="Usuń użytkownika">
          </form>

        <!-- Formularz usuwania posta -->
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="delete_post">Usuń post o ID:</label>
            <input type="number" name="delete_post" required>
            <input type="submit" value="Usuń post">
          </form>
        <?php endif; ?>

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
