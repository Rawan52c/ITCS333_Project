<?php 
include 'database_connect.php';  

if ($_SERVER['REQUEST_METHOD'] === 'POST') {     
    $user_name = htmlspecialchars(trim($_POST['user_name']));     
    $email = htmlspecialchars(trim($_POST['email']));     
    $password = $_POST['password'];     
    $pass_confirmation = $_POST['pass_confirmation'];     
    $user_position = htmlspecialchars(trim($_POST['user_position']));           

    // Email Validation      
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@uob\.edu\.bh$/', $email)) {         
        $error = "Failed to Validate your email. Try Again, using your University of Bahrain (UOB) Email Address";      
    } elseif ($password !== $pass_confirmation) {         
        $error = "Incorrect. Make sure you are using the same password for confirmation!.";     
    } else {                  
        $password_hash = password_hash($password, PASSWORD_DEFAULT);          
        
        $sql_entry = "INSERT INTO users (user_name, email, password, user_position) VALUES (:user_name, :email, :password, :user_position)";         
        $pdo_Statement1 = $pdo->prepare($sql_entry);         
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