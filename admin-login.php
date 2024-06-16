<?php
session_start();
include 'connection.php';

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $un = $_POST['username'];
    $pw = $_POST['password'];

    
    if ($con) {
        // Prepare and execute the query using prepared statements
        $stmt = $con->prepare("SELECT usernamead, passwordad FROM admins WHERE usernamead = ?");
        $stmt->bind_param("s", $un);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if there is a row with matching username
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if ($pw == $row['passwordad']) {
                $_SESSION['usernamead'] = $un;
                header("location: Dashboard.php"); // Redirect to admin dashboard
                exit;
            } else {
                $error_message = "Incorrect username or password"; // Incorrect password
            }
        } else {
            $error_message = "Username not found!"; // No matching username
        }

        $stmt->close();
    } else {
        $error_message = "Database connection failed: " . mysqli_connect_error();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ADMIN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="userlogin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
<div class="container">
        <div class="company-name">
             <a href="#">
                <img src="image/logo.png" alt="Company Logo" class="company-logo">
            </a>
            <h1>TechWiseThesis</h1>
        </div>

        <!--//////////ADMIN LOG IN////////////////-->
    <div class="login-wrap">
        <div class="login-html">
            <a href="main.html">
                <button class="exit-button"><i class="fa fa-xmark"></i></button>
            </a>
            <input id="tab-1" type="radio" name="tab" class="log-in" checked><label for="tab-1" class="tab">Admin Log In</label>
            <input id="tab-2" type="radio" name="tab" class="sign-in"><label for="tab-2" class="tab"></label>
            <div class="login-form">
                <div class="log-in-htm">
                    <form action="admin-login.php" method="POST" id="login-form">
                        <div class="group">
                            <label for="login-username" class="label">Admin Username</label>
                            <input id="login-username" name="username" type="text" class="input" required>
                        </div>
                        <div class="group">
                            <label for="login-password" class="label">Password</label>
                            <input id="login-password" name="password" type="password" class="input" data-type="password" required>
                        </div>
                        <div class="group">
                            <input type="submit" class="button" value="Log In" name="submit">
                        </div>
                        <div id="error-message" class="error-message"><?php echo $error_message; ?></div>

                    </form>
                    <div class="hr"></div>
                    <div class="foot-lnk">
                        <a href="forgot_pass.php">Forgot Password?</a>
                    </div>
                </div>
               
            </div>
        </div>
    </div>
</div>


</body>
</html>
