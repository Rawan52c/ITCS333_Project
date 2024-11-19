<?php
$host = 'localhost';
$username = 'root';
$database_name = 'IT_College_Room_Booking';


try {
    $pdo = new PDO("mysql:host=$host;database_name=$database_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection Unsuccessful: " . htmlspecialchars($e->getMessage());
}
?> 