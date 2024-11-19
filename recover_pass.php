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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Recovery</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/picocss/pico.min.css">
</head>
<body>
    <div class="container">
        <h1><strong>Password Recovery</strong></h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (isset($user) && !isset($success)): ?>
           <form action="recover_pass.php?token=<?php echo htmlspecialchars($token); ?>" method="post">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>

                <label for="pass_confirmation">Confirm Your New Password</label>
                <input type="password" id="pass_confirmation" name="pass_confirmation" required>

                <button type="submit">Recover Password!</button>
            </form>
        <?php endif; ?>

    </div>

</body>
</html>
