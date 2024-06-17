<?php require_once "UserDataController.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="forgot-pas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

<video id="video-background" autoplay muted loop>
        <source src="v1.mp4" type="video/mp4">
        Your browser does not support the video tag.
</video>

    <div class="login-wrap">
        <div class="login-html">
            <a href="login.php">
                <button class="exit-button"><i class="fa fa-xmark"></i></button>
            </a>
            <input id="tab-1" type="radio" name="tab" class="log-in" checked><label for="tab-1" class="tab">Forgot Password</label>
            <div class="login-form">
                <div class="log-in-htm">
                   <form action="UserDataController.php" method="post" autocomplete>
					<div class="group">
						<label for="email" class="label">Enter your email address</label><br>
                        <input id="email" name="email" type=" email" class="input" required placeholder="Enter your email address">
                    <?php
                        if(count($errors) > 0){
                            ?>
                            <div class="alert alert-danger text-center">
                                <?php 
                                    foreach($errors as $error){
                                        echo $error;
                                    }
                                ?>
                            </div>
                            <?php
                        }
                    ?>
                    <br>
                    <div class="form-group">
                        <input class="form-control button" type="submit" name="check-email" value="Continue">
                    </div>
                </form>
            </div>
        </div>
    </div>
    
</body>
</html>