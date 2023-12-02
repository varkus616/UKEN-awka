<?php
session_start();


function setSessionVariable($key, $value) {
    $_SESSION[$key] = $value;
}

function getSessionVariable($key) {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
}

?>
