<?php require "connect_to_db.php"; require "start_session.php";?>
<script>

if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}

</script>
<?php 
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
  <body>
    <div id="base_container" >
      <div id="content">
        <header id="account_header">
            <h1>Mój profil</h1>
            <h2><a href="my_friends.php">Znajomi</a></h2>
            <h2><a href="add_friend.php">Dodaj znajomego</a></h2>
            <a href="logout.php">Wyloguj</a>
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
                      echo "</div>";
                  }
                  echo "</div>";
              }
          }else {
            echo "<h3>Brak postów</h3>";
          }
        ?>
        
        <!-- Admin 
        <?php #if ($_SESSION['role'] == 'admin'): ?>
          <h2>Panel administratora</h2>
          admin cos robi 
          <p>Admin</p>
        <?php #endif; ?>-->

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
