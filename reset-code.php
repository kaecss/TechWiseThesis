<?php require_once "UserDataController.php"; ?>
<?php 
$email = $_SESSION['email'];
if($email == false){
  header('Location: login.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code Verification</title>
    <link rel="stylesheet" href="resetcode.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

<video id="video-background" autoplay muted loop>
        <source src="v1.mp4" type="video/mp4">
        Your browser does not support the video tag.
</video>

    <div class="login-wrap">
        <div class="login-html">
            <a href="forgot_pass.php">
                <button class="exit-button"><i class="fa fa-xmark"></i></button>
            </a>
            <input id="tab-1" type="radio" name="tab" class="log-in" checked><label for="tab-1" class="tab">Code Verification</label>
            <div class="login-form">
                <div class="log-in-htm">
                    <form action="reset-code.php" method="post" autocomplete="off">
                    <?php 
                    if(isset($_SESSION['info'])){
                        ?>
                        <div class="alert alert-success text-center" style="padding: 0.4rem 0.4rem; color: white; text-align: center; font-size:20px">
                            <?php echo $_SESSION['info']; ?>
                        </div>
                        <?php
                    }
                    ?>

                    <?php
                    if(count($errors) > 0){
                        ?>
                        <div class="alert alert-danger text-center">
                            <?php
                            foreach($errors as $showerror){
                                echo $showerror;
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                         <div class="group">
                            <input id="code" name="otp" type="number" class="input" placeholder="Enter code" required>
                        </div>
                       
                        <div class="group">
                            <input type="submit" class="button" name="check-reset-otp" value="Submit">
                        </div>
                    </form>
                   
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
