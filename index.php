<!-- Main Page -->

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


$username = $_SESSION['username'];
$user_position = $_SESSION['user_position'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Welcome to UOB IT College Room Booking </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/picocss/pico.min.css">
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>
        <p>Your role: <?php echo htmlspecialchars($user_position); ?></p>

    
        
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
