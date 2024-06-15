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
    <title>Create a New Password</title>
    <link rel="stylesheet" href="reset-code.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <style>
        .error-msg {
            color: #734122; /* Set the color for error messages */
        }
    </style>
</head>
<body>
<div class="container">
    <div class="company-name">
        <a href="user-login.html">
            <img src="image/logo.png" alt="Company Logo" class="company-logo">
        </a>
        <h1>TechWiseThesis</h1>
    </div>


    <div class="login-wrap">
        <div class="login-html">
            <a href="forgot_pass.html">
                <button class="exit-button"><i class="fa fa-xmark"></i></button>
            </a>
            <input id="tab-1" type="radio" name="tab" class="log-in" checked><label for="tab-1" class="tab">New Password</label>

            <div class="login-form">
                <div class="log-in-htm">
                    <form action="new-password.php" method="post" autocomplete="off">
                        <div class="group">
                            <label for="password" class="label">Create New Password</label>
                            <input id="password" name="password" type="password" class="input" placeholder="Enter your new password" required>
                            <?php if(isset($errors['password'])) { ?>
                                <span class="error-msg"><?php echo $errors['password']; ?></span>
                            <?php } ?>
                        </div>

                        <div class="group">
                            <label for="cpassword" class="label">Confirm New Password</label>
                            <input id="cpassword" name="cpassword" type="password" class="input" placeholder="Confirm your new password" required>

                            <?php if(isset($errors['password'])) { ?>
                                <span class="error-msg"><?php echo $errors['password']; ?></span>
                            <?php } ?>

                        </div>
                        <div class="group">
                            <input type="submit" class="button" name="change-password" value="Reset Password">
                        </div>

                        <?php
                        if(isset($errors['db_error'])) {
                            echo '<span class="error-msg">' . $errors['db_error'] . '</span>';
                        }
                        ?>
                        <br>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// SweetAlert for success message
document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($_SESSION['password_changed']) && $_SESSION['password_changed'] === true): ?>
        Swal.fire({
            title: "Congrats!",
            text: "Your password has been changed. You can now login to your account!",
            icon: "success"
        }).then(function() {
            window.location = "login.php";
        });
        <?php unset($_SESSION['password_changed']); ?>
    <?php endif; ?>
});
</script>

</body>
</html>
