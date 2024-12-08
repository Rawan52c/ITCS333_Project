<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Welcome to UOB IT College Room Booking </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/picocss/pico.min.css">
    <style>
        .ProfileContainer {
            max-width: 600px;
            margin: 2rem auto;
        }
        .ProfileImage {
            width: 100px;
            height: 100px;
            background-color: #ddd;
            border-radius: 50%;
            margin: 1rem auto;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form');
            const nameInput = document.querySelector('input[name="user_name"]');
            const emailInput = document.querySelector('input[name="email"]');
            const idInput = document.querySelector('input[name="user_id"]');

            form.addEventListener('submit', (event) => {
                if (!nameInput.value.trim() || !emailInput.value.trim() || !idInput.value.trim()) {
                    event.preventDefault();
                    alert('All fields must be filled out before saving.');
                }
            });
        });
    </script>
</head>
<body>
    <?php 
    echo($_SESSION['user_id']);
    $userID = $_SESSION['user_id'];
    $userName = $_SESSION['user_name'];
    $userEmail = $_SESSION['email'];
    $userPosition = $_SESSION['user_position'];
    $ID = explode("@", $userEmail);
    ?>
    <div class="ProfileContainer">
        <div class="ProfileImage"></div>
        <form method="post" action="">
            <div class="UserDetails">
                <div>
                    <label for="user_name">User Name</label>
                    <input type="text" id="user_name" name="user_name" value="<?php echo htmlspecialchars($userName); ?>" required>
                </div>
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userEmail); ?>" required>
                </div>
                <div>
                    <label for="user_id">ID</label>
                    <input type="text" id="user_id" name="user_id" value="<?php echo htmlspecialchars($ID[0]); ?>" readonly>
                </div>
                <button type="submit">Save</button>
            </div>
        </form>
    </div>
</body>
</html>