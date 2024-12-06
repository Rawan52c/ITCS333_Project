<?php
session_start();
include 'database_connect.php';

// Handle login POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // If it's a login attempt
    if (isset($_POST['login_email']) && isset($_POST['login_password'])) {
        $email = $_POST['login_email'];
        $password = $_POST['login_password'];

        // Validate email (check if it's a UOB email)
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@uob\.edu\.bh$/', $email)) {
            $error = "Failed to validate your email. Try Again, use your University of Bahrain (UOB) Email Address.";
        } else {
            // Prepare and execute the SQL query to check user credentials
            $sql_entry = "SELECT * FROM users WHERE email = :email";
            $pdo_Statement1 = $pdo->prepare($sql_entry);
            $pdo_Statement1->execute([':email' => $email]);
            $user = $pdo_Statement1->fetch(PDO::FETCH_ASSOC);

            // Check if user exists and password matches
            if ($user && password_verify($password, $user['password'])) {
                // Regenerate session ID for security
                session_regenerate_id(true);

                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['user_name'];  
                $_SESSION['email'] = $user['email'];
                $_SESSION['user_position'] = $user['user_position'];  

                // Redirect to home page after successful login
                header("Location: index.php");
                exit();
            } else {
                // Error message if credentials are incorrect
                $error = "Sorry, something went wrong. Try Again in a few minutes.";
            }
        }
    }

    // If it's a password recovery attempt
    elseif (isset($_POST['recover_email'])) {
        $email = htmlspecialchars(trim($_POST['recover_email'])); 

        $sql_entry = "SELECT * FROM users WHERE email = :email";
        $pdo_Statement1 = $pdo->prepare($sql_entry);
        $pdo_Statement1->execute([':email' => $email]);
        $user = $pdo_Statement1->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Token Generation
            $passrecover_token = bin2hex(random_bytes(16));
            $passrecover_expires = date('Y-m-d H:i:s', strtotime('+30 minutes'));  

            // Update the user with the recovery token and expiration
            $sql_entry = "UPDATE users SET reset_token = :passrecover_token, passrecover_expires = :passrecover_expires WHERE email = :email";
            $pdo_Statement1 = $pdo->prepare($sql_entry);
            $pdo_Statement1->execute([
                ':passrecover_token' => $passrecover_token,
                ':passrecover_expires' => $passrecover_expires,
                ':email' => $email
            ]);

            // Send recovery email
            $recovery_link = "http://localhost/reset_password.php?token=$passrecover_token";  
            mail($email, "Password Recovery", "Recover your password: $recovery_link");

            $email_sent = "Password recovery email has been sent to your email!";
        } else {
            $error = "Something went wrong, consider rechecking the email you entered.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Password Recovery</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/picocss/pico.min.css">
</head>
<body>
    <div class="container">
        <h2>User Login</h2>

        <!-- Display error or success messages -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($email_sent)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($email_sent); ?></div>
        <?php endif; ?>

        <!-- Login form -->
        <form action="login.php" method="post" id="loginForm">
            <div>
                <label for="login_email">Email</label>
                <input type="email" id="login_email" name="login_email" required placeholder="Use your UOB email">
            </div>
            <div>
                <label for="login_password">Password</label>
                <input type="password" id="login_password" name="login_password" minlength="12" maxlength="16" required>
            </div>
            <input type="submit" value="Login">
        </form>

        <!-- Forgot password link -->
        <p>Forgot your password? <a href="#" id="forgotPasswordLink">Click here</a></p>

        <!-- Password recovery form (hidden by default) -->
        <form action="login.php" method="post" id="passwordRecoveryForm" style="display: none;">
            <div>
                <label for="recover_email">Enter your UOB Email:</label>
                <input type="email" id="recover_email" name="recover_email" required>
            </div>
            <button type="submit">Send password recovery link</button>
        </form>

        <!-- Register link -->
        <p>If you are a new user: <a href="registration.php">Register Now!</a>.</p>
    </div>

    <script>
        // Toggle between login and password recovery forms
        document.getElementById('forgotPasswordLink').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('passwordRecoveryForm').style.display = 'block';
        });
    </script>
</body>
</html>
