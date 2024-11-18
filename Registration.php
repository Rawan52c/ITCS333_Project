<?php
include 'database_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = $_POST['user_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $pass_confirmation = $_POST['pass_confirmation'];
    $user_position = $_POST['user_position']; 
    
    // Email Validation

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@uob\.edu\.bh$/', $email)) {
        $error = "Failed to Validate your email. Try Again, using your University of Bahrain (UOB) Email Address";

    } elseif ($password !== $password_confirmation) {
        $error = "Incorrect. Make sure you are using the same password for confirmation!.";
    } else {
        
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql_entry = "INSERT INTO users (user_name, email, password, user_position) VALUES (:user_name, :email, :password, :user_position)";
        $pdo_Statement1 = $pdo->prepare($sql);
        $pdo_Statement1->execute([
            ':user_name' => $user_name,
            ':email' => $email,
            ':password' => $password_hash,
            ':user_position' => $user_position
        ]);
        
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Registration </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/picocss/pico.min.css">
</head>
<body>
    <div class="container">
        <h1> <strong> User Registration </strong> </h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?> </div>
        <?php endif; ?>

        <form action="registration.php" method="post">
            <label for="user_name">Username</label>
            <input type="text" id="user_name" name="user_name" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="pass_confirmation"> Password Confirmation </label>
            <input type="password" id="pass_confirmation" name="pass_confirmation" required>

            <label for="user_position">User Position</label>
            <select id="user_position" name="user_position" required>
                <option value="Dean">Dean</option>
                <option value="HOD">HOD</option>
                <option value="IT Specialist">IT Specialist</option>
                <option value="Faculty-Member" selected>Faculty-Member</option>

            </select>

            <button type="submit">Register</button>
            
        </form>
    </div>
</body>
</html>
