<?php
session_start();
include 'database_connect.php';

// Handle login and password recovery requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login_email'], $_POST['login_password'])) {
        $email = trim($_POST['login_email']);
        $password = trim($_POST['login_password']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@uob\.edu\.bh$/', $email)) {
            $error = "Please use a valid UOB email address.";
        } else {
            $query = "SELECT * FROM users WHERE email = :email";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['user_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] == 'admin'){
                    header("Location: admin.php");
                }
                else{
                    header("Location: index.php");
                }
                exit();
                
            } else {
                $error = "Invalid email or password.";
            }
        }
    } elseif (isset($_POST['recover_email'])) {
        $email = htmlspecialchars(trim($_POST['recover_email']));

        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $token = bin2hex(random_bytes(16));
            $expires = date('Y-m-d H:i:s', strtotime('+30 minutes'));

            $updateQuery = "UPDATE users SET reset_token = :token, passrecover_expires = :expires WHERE email = :email";
            $stmt = $pdo->prepare($updateQuery);
            $stmt->execute([
                ':token' => $token,
                ':expires' => $expires,
                ':email' => $email
            ]);

            $link = "http://localhost/reset_password.php?token=$token";
            mail($email, "Password Recovery", "Reset your password using this link: $link");

            $email_sent = "A password recovery link has been sent to your email.";
        } else {
            $error = "No account found with the provided email.";
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
    <link rel="stylesheet" href="styles.css">

   
</head>
<body>
    <main>
        <h2>Welcome</h2>
        <p class="text-center">Please log in or recover your password below.</p>

        <!-- Display messages -->
        <?php if (isset($error)): ?>
            <article class="alert error"><?= htmlspecialchars($error) ?></article>
        <?php elseif (isset($email_sent)): ?>
            <article class="alert success"><?= htmlspecialchars($email_sent) ?></article>
        <?php endif; ?>

        <!-- Login Form -->
        <form id="loginForm" action="login.php" method="post">
            <fieldset>
                <legend>Login</legend>
                <label for="login_email">
                    Email
                    <input type="email" id="login_email" name="login_email" required placeholder="Enter your UOB email">
                </label>
                <label for="login_password">
                    Password
                    <input type="password" id="login_password" name="login_password" minlength="12" maxlength="16" required>
                </label>
                <button type="submit" class="primary">Login</button>
            </fieldset>
        </form>

        <!-- Password Recovery Form -->
        <form id="passwordRecoveryForm" action="login.php" method="post" style="display: none;">
            <fieldset>
                <legend>Password Recovery</legend>
                <label for="recover_email">
                    Enter your UOB Email:
                    <input type="email" id="recover_email" name="recover_email" required placeholder="Enter your UOB email">
                </label>
                <button type="submit" class="primary">Send Recovery Link</button>
            </fieldset>
        </form>

        <!-- Additional Links -->
        <div class="form-footer">
            <p>
                <span id="toggleRecoveryForm" class="toggle-link">Forgot your password?</span>
            </p>
            <p>
                New user? <a href="registration.php">Register here</a>.
            </p>
        </div>
    </main>

    <script>
        // Toggle visibility of login and password recovery forms
        document.getElementById('toggleRecoveryForm').addEventListener('click', function() {
            const loginForm = document.getElementById('loginForm');
            const recoveryForm = document.getElementById('passwordRecoveryForm');
            if (loginForm.style.display === 'none') {
                loginForm.style.display = 'block';
                recoveryForm.style.display = 'none';
            } else {
                loginForm.style.display = 'none';
                recoveryForm.style.display = 'block';
            }
        });
    </script>
</body>
</html>
