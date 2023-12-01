<?php
session_start();

if (isset($_SESSION['user_id'])) {


} else {
    
    $email = isset($_SESSION['email']) ? $_SESSION['email'] : null;

    if ($email) {
        // Zapytanie SQL do pobrania danych użytkownika
        $user_query = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $user_query->bindParam(":email", $email);
        $user_query->execute();
        $user_data = $user_query->fetch(PDO::FETCH_ASSOC);

        if ($user_data) {
            $_SESSION['user_id'] = $user_data['id'];
            $_SESSION['first_name'] = $user_data['first_name'];
            $_SESSION['email'] = $user_data['email'];
            $_SESSION['last_name'] = $user_data['last_name'];
            $_SESSION['date_of_birth'] = $user_data['date_of_birth'];
        }
    }
}
?>
