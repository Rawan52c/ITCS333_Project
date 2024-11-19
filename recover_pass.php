<?php
include 'database_connect.php';

if (isset($_GET['token'])) {
    $token = htmlspecialchars($_GET['token']);

    $sql_entry = "SELECT * FROM users WHERE recover_token = :token AND passrecovery_expires > NOW()";
    $pdo_Statement1 = $pdo->prepare($sql_entry);
    $pdo_Statement1->execute([':token' => $token]);
    $user = $pdo_Statement1->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_password = $_POST['new_password'];
            $pass_confirmation = $_POST['pass_confirmation'];

            if ($new_password === $pass_confirmation) {
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

                $sql_latest = "UPDATE users SET password = :password, recover_token = NULL, passrecovery_expires = NULL WHERE recover_token = :token";
                $statement_latest = $pdo->prepare($sql_latest);
                $statement_latest->execute([
                    ':password' => $password_hash,
                    ':token' => $token
                ]);

                $success = "Your password has been updated! Use <a href='login.php'>login</a> to log in with your new password.";
            } else {
                $error = "Passwords do not match.";
            }
        }
    } else {
        $error = "Token expired.";
    }
} else {
    $error = "Token not found.";
}
?>


