<?php
// Start the session
session_start();

// Unset session variables
session_unset();

// Destroy the session
session_destroy();

// Optional: Set a message for successful logout
$message = "You have successfully logged out.";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@1.5.10/css/pico.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main class="container">
        <h2>Logout</h2>
        <p><?= isset($message) ? htmlspecialchars($message) : 'Logging out...' ?></p>
        <p>You will be redirected shortly. If not, click <a href="login.php">here</a>.</p>
    </main>
</body>
</html>

<?php
// Redirect to the login page after 2 seconds
header("Refresh: 2; url=login.php");
exit();
?>

