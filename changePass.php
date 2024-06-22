<?php
session_start();

// connection.php
$conn = mysqli_connect('localhost', 'root', '', 'techwisethesis');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit'])) {
        $un = $_SESSION['username'];
        $pw = $_POST['password'];

        // Hash the password
        $hashed_password = password_hash($pw, PASSWORD_DEFAULT);

        // Use prepared statement to update password securely
        $stmt = $conn->prepare("UPDATE user_form SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $hashed_password, $un);

        if ($stmt->execute()) {
            // Password updated successfully
            header("Location: login.php");
            exit();
        } else {
            // Handle error if update fails
            echo "Error updating password: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="forgot-pas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .aw {
            text-align: center;
            color: #fff;
            margin-top: 10px;
            font-weight: bold;
        }
        .aw a {
            display: inline-block; 
        }
        .aw a:hover {
            color: #A90F0D;
            cursor: pointer;
        }

        .aw label:hover {
            color: #BA7E3B;
            cursor: pointer;
        }
    </style>
</head>
<body>

<video id="video-background" autoplay muted loop>
    <source src="v1.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>

<div class="login-wrap">
    <div class="login-html">
        <a href="login.php">
            <button class="exit-button"><i class="fa fa-times"></i></button>
        </a>
        <input id="tab-1" type="radio" name="tab" class="log-in" checked><label for="tab-1" class="tab">Forgot Password</label>
        <div class="login-form">
            <div class="log-in-htm">
                <div class="group">
                    <form action="changePass.php" method="POST">
                        <label class="label">Welcome, <?php echo $_SESSION['username']; ?>!</label>
                        <input name="password" type="password" class="input" required placeholder="Enter New password">
                        <br>
                        <input class="form-control button" type="submit" name="submit" value="Continue">
                    </form>
                    
                    <br>
                    <hr>
                    <div class="aw">
                        <a href="login.php">Go back to Log in Page</a>
                    </div>
                
                </div>
            </div>
        </div>
    </div>
</div>
    
</body>
</html>
