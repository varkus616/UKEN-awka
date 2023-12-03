
<?php
$servername = "localhost"; 
$username = "root"; 
$password = "123"; 
$dbname = "uken-awka"; 

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",
    $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, 
                        PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES UTF8");
}catch(PDOException $e){
    die( "Connection failed: ".$e->getMessage());
}
?>