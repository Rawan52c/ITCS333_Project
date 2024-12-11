<?php
include 'database_connect.php';

// Handle POST request when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = htmlspecialchars(trim($_POST['user_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];
    $pass_confirmation = $_POST['pass_confirmation'];


    // Validate email format and domain
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@uob\.edu\.bh$/', $email)) {
        $error = "Failed to validate your email. Try again using your University of Bahrain (UOB) Email Address.";
    }
    // Check if passwords match
    elseif ($password !== $pass_confirmation) {
        $error = "Passwords do not match. Please confirm your password correctly.";
    } else {
        // Hash the password for secure storage
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $sql_entry = "INSERT INTO users (user_name, email, password, role) VALUES (:user_name, :email, :password, 'user')";
        $pdo_Statement1 = $pdo->prepare($sql_entry);
        $pdo_Statement1->execute([
            ':user_name' => $user_name,
            ':email' => $email,
            ':password' => $password_hash,
        ]);

        // Redirect to the login page after successful registration
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
    <title>Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/picocss/pico.min.css">
    <link rel="stylesheet" href="styles.css">
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form');
            const passwordInput = document.getElementById('password');
            const passConfirmInput = document.getElementById('pass_confirmation');
            const emailInput = document.getElementById('email');

            form.addEventListener('submit', (event) => {
                // Password confirmation validation
                if (passwordInput.value !== passConfirmInput.value) {
                    event.preventDefault();
                    alert('Passwords do not match.');
                }

                // Email domain validation
                const emailRegex = /@uob\.edu\.bh$/;
                if (!emailRegex.test(emailInput.value)) {
                    event.preventDefault();
                    alert('Please use a valid UOB email address.');
                }
            });
        });
    </script>
</head>

<body>
    <main class="container">
    <header class="text-center">
        <h1> <strong>User Registration</strong></h1>
        <nav class="navlist">
            <ul>
                <li> <a href = "login.php" class="badge secondary">Go Back</a></li>
            </ul>
        </nav>
    </header>

        <!-- Display error message if set -->
        <?php if (isset($error)): ?>
            <article class="alert error"><?php echo $error; ?></article>
        <?php endif; ?>

        <!-- Registration form -->
        <form action="registration.php" method="post">
            <div>
                <label for="user_name">Username</label>
                <input type="text" id="user_name" name="user_name" placeholder="Enter your username" required>
            </div>

            <div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your UOB email" required>
            </div>

            <div>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" minlength="12" maxlength="16" placeholder="Don't use easy passwords!" required>
            </div>

            <div>
                <label for="pass_confirmation">Password Confirmation</label>
                <input type="password" id="pass_confirmation" name="pass_confirmation" minlength="12" maxlength="16" required>
            </div>
            <button type="submit" role="button">Register</button>
        </form>
    </main>
</body>

</html>
