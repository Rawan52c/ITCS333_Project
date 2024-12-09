<?php
$host = 'localhost';
$username = 'root';
$password='';
$database_name = 'IT_College_Room_Booking';


try {
    $pdo = new PDO("mysql:host=$host;dbname=$database_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection Unsuccessful: " ($e->getMessage());
}
?> 
