<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Welcome to UOB IT College Room Booking </title>
     <!-- Link to Pico CSS for styling -->
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
            overflow: hidden;
        }
        .ProfileImage img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Ensures the image fills the circle without distortion */
            border-radius: 50%; /* Makes the image round */
            display: block;
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

    session_start();
    require 'database_connect.php';

    $userID = $_SESSION['user_id'];
    $userName = $_SESSION['user_name'];
    $userEmail = $_SESSION['email'];
    $ID = explode("@", $userEmail);

    $query = "SELECT profile_image FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':user_id' => $userID]);
    $user = $stmt->fetch();

    $profileImage = isset($user['profile_image']) && !empty($user['profile_image']) 
        ? $user['profile_image'] 
        : 'uploads/default.jpeg';


    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
        
        // Ensure the uploads directory exists
        $uploadsDir = 'uploads/';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }
    
        // Handle profile image upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_image']['tmp_name'];
            $fileName = $_FILES['profile_image']['name'];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));


            // Validate file type
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($fileExt, $allowedExtensions)) {
                $newFileName = $userID . '_profile.' . $fileExt; // Unique file name
                $filePath = $uploadsDir . $newFileName;
    
                // Move the uploaded file to the uploads directory
                if (move_uploaded_file($fileTmpPath, $filePath)) {
                    // Save the file path to the database
                    $query = "UPDATE users SET profile_image = :profile_image WHERE id = :user_id";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute([
                        ':profile_image' => $filePath,
                        ':user_id' => $userID
                    ]);
                    $profileImage = $filePath;
                    echo "<p>Profile image updated successfully!</p>";
                } else {
                    echo "<p>Error moving the uploaded file.</p>";
                }
            } else {
                echo "Invalid file type. Please upload an image (jpg, jpeg, png, gif).";
            }
        }
    }
    
    ?>

    <div class="ProfileContainer">
        <div class="ProfileImage">
            <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Profile Image">
        </div>

        <form method="post" action="" enctype="multipart/form-data">
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
                <div>
                    <label for="profile_image">Profile Image</label>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*">
                </div>
                <button type="submit" name="save">Save</button>
            </div>
        </form>
    </div>
</body>
</html>
