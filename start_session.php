<?php
session_start();
#if (session_status() == PHP_SESSION_NONE){
#    session_start();
#}


function setSessionVariable($key, $value) {
    $_SESSION[$key] = $value;
}

function getSessionVariable($key) {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
}

#$userData = null;
#function startSession($userData) {

    /*$_SESSION['email'] = $userData['email'];
    $_SESSION['user_id'] = $userData['user_id'];
    $_SESSION['first_name'] = $userData['first_name'];
    $_SESSION['last_name'] = $userData['last_name'];
    $_SESSION['date_of_birth'] = $userData['date_of_birth'];

    if ($userData['role'] === 'admin') {
        $_SESSION['role'] = 'admin';
    }
    */

#}

?>
