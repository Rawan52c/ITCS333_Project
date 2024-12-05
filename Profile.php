<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Welcome to UOB IT College Room Booking </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/picocss/pico.min.css">
</head>
<body>
    <?php 
    echo($_SESSION['user_id']);
    $userID =  $_SESSION['user_id'];
    $userName = $_SESSION['user_name'] ;
    $userEmail = $_SESSION['email'] ;
    $userPostion = $_SESSION['user_position'] ;
    $ID = explode("@", $userEmail);
    ?>
    <div class="ProfileContainer">
        <div class="ProfileImage"></div>
        <form method="post" action="">
        <div class="UserDetails">
          
                <div class="UserName"> 
                    <label > User Name </label>
                    <input type ="text" value="<?php echo($userName)?>">

                </div>
               
            <div class="ProfileEmail"> 
                    <label > Email </label>
                    <input type ="email" value="<?php echo($userEmail)?>" >

                </div>
                <div class="ProfileID"> 
                    <label > ID </label>
                    <input type ="number" value="<?php echo($userID[0])?>">

                </div>

                <button type="submit"> Save </button>
            </form>
        </div>
        
    </div>
      
   
</body>
</html>