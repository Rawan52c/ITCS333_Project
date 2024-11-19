<?php
session_start();
include 'database_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@uob\.edu\.bh$/', $email)) {
        $error = "Failed to validate your email. Try Again, use your University of Bahrain (UOB) Email Address.";
    } else {
        $sql_entry = "SELECT * FROM users WHERE email = :email";
        $pdo_Statement1 = $pdo->prepare($sql_entry);
        $pdo_Statement1->execute([':email' => $email]);
        $user = $pdo_Statement1->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['user_name'];  
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_position'] = $user['user_position'];  

            header("Location: index.php");
            exit();
        } else {
            $error = "Sorry, something went wrong. Try Again in a few minutes";
        }
    }
}
?>
