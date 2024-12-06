<?php
session_start();
include 'database_connect.php';

// Handle POST request when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/picocss/pico.min.css">
</head>
<body>
    <div class="container">
        <h2>User Login</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="login.php" method="post">
            <div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="Use your UOB email">
            </div>
            
            <div>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" minlength="12" maxlength="16" required>
            </div>

            <input type="submit" value="Login">
        </form>

        <p>If you are a new user: <a href="registration.php">Register Now!</a>.</p>
    </div>

</body>
</html>
