<?php
include 'database_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars(trim($_POST['email'])); 

    $sql_entry = "SELECT * FROM users WHERE email = :email";
    $pdo_Statement1 = $pdo->prepare($sql_entry);
    $pdo_Statement1->execute([':email' => $email]);
    $user = $pdo_Statement1->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Token Generation
        $passrecover_token = bin2hex(random_bytes(16));
        $passrecover_expires = date('Y-m-d H:i:s', strtotime('+30 minutes'));  

     
        $sql_entry = "UPDATE users SET reset_token = :passrecover_token, passrecover_expires = :passrecover_expires WHERE email = :email";
        $pdo_Statement1 = $pdo->prepare($sql_entry);
        $pdo_Statement1->execute([
            ':passrecover_token' => $passrecover_token,
            ':passrecover_expires' => $passrecover_expires,
            ':email' => $email
        ]);

     
        $recovery_link = "http://localhost/reset_password.php?token=$passrecover_token";  

        mail($email, "Password Recovery", "Recover your password: $recovery_link");

        
        $email_sent = "Password recovery email has been sent to your email!";
    } else {
        $error = "Something went wrong, consider rechecking the email you entered.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Forgot Password </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/picocss/pico.min.css">
</head>
<body>
    <div class="container">
        <h1><strong> Forgot Password </strong></h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($email_sent)): ?>
            <div class="alert alert-success"><?php echo $email_sent; ?></div>
        <?php endif; ?>

        <form action="pass_forgotten.php" method="post">
            <label for="email"> Enter your UOB Email: </label>
            <input type="email" id="email" name="email" required>
            <button type="submit"> Send password recovery link to my email </button>
        </form>

    </div>

</body>

</html>
