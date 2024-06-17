<?php
session_start();
include 'config.php'; 

$login_errors = []; 
$signup_errors = []; 

if (isset($_POST['submit']) && isset($_POST['email']) && isset($_POST['password'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    $select = "SELECT * FROM user_form WHERE email = '$email'";
    $result = mysqli_query($con, $select);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['user_type'] = $row['user_type'];

            if ($row['user_type'] == 'admin') {
                header("location: admin_page.php");
                exit;
            } elseif ($row['user_type'] == 'user') {
                header('location: user_page.php');
                exit;
            }
        } else {
            $login_errors[] = 'Email not found!';
        }
    } else {
        $login_errors[] = 'Incorrect email or password!';
    }
}

function usernameExists($con, $username) {
    $stmt = $con->prepare("SELECT * FROM user_form WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}
function emailExists($con, $email) {
    $stmt = $con->prepare("SELECT * FROM user_form WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}
if (isset($_POST['signup'])) {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($con, $_POST['confirm_password']);
    $question = mysqli_real_escape_string($con, $_POST['question']);
    $answer = mysqli_real_escape_string($con, $_POST['answer']);
    $user_type = 'user';
    if (empty($username)) {
        $signup_errors['username'] = 'Username is required';
    } elseif (usernameExists($con, $username)) {
        $signup_errors['username'] = 'Username already exists';
    }

    if (empty($email)) {
        $signup_errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $signup_errors['email'] = 'Invalid email format.';
    } elseif (emailExists($con, $email)) {
        $signup_errors['email'] = 'Email already exists';
    }

    if (empty($password)) {
        $signup_errors['password'] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $signup_errors['password'] = 'Password must be at least 6 characters long.';
    }

    if (empty($confirm_password)) {
        $signup_errors['confirm_password'] = 'Confirm Password is required';
    } elseif ($password !== $confirm_password) {
        $signup_errors['confirm_password'] = 'Passwords do not match';
    }

    if (empty($question)) {
        $signup_errors['question'] = 'Please select a security question.';
    }

    if (empty($answer)) {
        $signup_errors['answer'] = 'Answer is required';
    }
    if (empty($signup_errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $con->prepare("INSERT INTO user_form (username, email, password, question, answer, user_type) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $email, $hashed_password, $question, $answer, $user_type);

        if ($stmt->execute()) {
            $_SESSION['account_created'] = true;
            header("Location: {$_SERVER['PHP_SELF']}");
            exit();
        } else {
            $signup_errors['db_error'] = 'Database error: ' . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>USER Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="userlogin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>
<body>
<video id="video-background" autoplay muted loop>
        <source src="v1.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
<div class="company-info">
    <a href="admin-login.html">
        <img src="logo.jpg" alt="Company Logo" class="company-logo">
    </a>
    <h1 class="company-name">TechWiseThesis</h1>
</div>
<div class="container">
    <div class="login-wrap">
        <div class="login-html">
            <a href="main.php" class="exit-button"><i class="fa fa-times"></i></a>
            <input id="tab-1" type="radio" name="tab" class="log-in" checked><label for="tab-1" class="tab">Log In</label>
           <input id="tab-2" type="radio" name="tab" class="sign-in"<?php if(isset($signup_errors) && !empty($signup_errors)) { echo ' checked'; } ?>>
				<label for="tab-2" class="tab">Sign Up</label>
            <div class="login-form">
                <div class="log-in-htm">
                    <?php
                    if(isset($_SESSION['account_login']) && $_SESSION['account_login'] == true){
                        unset($_SESSION['account_login']);
                        echo '<script>
                        window.onload = function() {
                            alert("User account has been successfully logged in!");
                            window.location.href = "user_page.php";
                        }
                        </script>';
                    }
                    ?>
                    <form action="" method="POST">
                        <div class="group">
                            <label for="login-username" class="label">Email</label>
                            <input id="login-username" name="email" type="email" class="input" required placeholder="Enter your email">
                        </div>
                        <div class="group">
                            <label for="login-password" class="label">Password</label>
                            <input id="login-password" name="password" type="password" class="input" data-type="password" required placeholder="Enter your password">
                        </div>
                        <div class="group">
                            <input type="submit" class="button" value="Log In" name="submit">
                        </div>
                        <?php
                        if(isset($login_errors) && !empty($login_errors)) {
                            foreach($login_errors as $error) {
                                echo '<span class="error-msg">'.$error.'</span><br>';
                            }
                        }
                        ?>
                    </form>
                    <div class="hr"></div>
                    <div class="foot-lnk">
                        <a href="forgot_pass.php">Forgot Password?</a>
                    </div>
                </div>
				
                <div class="sign-in-htm">
                    <form action="" method="post">
                        <div class="group">
                            <label for="signup-username" class="label">Username</label>
                            <input id="signup-username" name="username" type="text" class="input" required placeholder="Enter your username">
                            <?php if(isset($signup_errors['username'])) { ?>
                                <span class="error-msg"><?php echo $signup_errors['username']; ?></span>
                            <?php } ?>
                        </div>
                        <div class="group">
                            <label for="signup-email" class="label">Email</label>
                            <input id="signup-email" name="email" type="email" class="input" required placeholder="Enter your email">
                            <?php if(isset($signup_errors['email'])) { ?>
                                <span class="error-msg"><?php echo $signup_errors['email']; ?></span>
                            <?php } ?>
                        </div>
                        <div class="group">
                            <label for="signup-password" class="label">Password</label>
                            <input id="signup-password" name="password" type="password" class="input" required placeholder="Enter your password">
                            <?php if(isset($signup_errors['password'])) { ?>
                                <span class="error-msg"><?php echo $signup_errors['password']; ?></span>
                            <?php } ?>
                        </div>
                        <div class="group">
                            <label for="signup-confirm-password" class="label">Confirm Password</label>
                            <input id="signup-confirm-password" name="confirm_password" type="password" class="input" required placeholder="Confirm your password">
                            <?php if(isset($signup_errors['confirm_password'])) { ?>
                                <span class="error-msg"><?php echo $signup_errors['confirm_password']; ?></span>
                            <?php } ?>
                        </div>
                        <div class="group">
                            <label for="signup-question" class="label">Security Question</label>
                            <select id="signup-question" name="question" class="input" required>
                                <option value="" disabled selected>Select a security question:</option>
                                <option value="q1" <?php echo (isset($_POST['question']) && $_POST['question'] === 'q1') ? 'selected' : ''; ?>>What is your mother's maiden name?</option>
                                <option value="q2" <?php echo (isset($_POST['question']) && $_POST['question'] === 'q2') ? 'selected' : ''; ?>>What city were you born in?</option>
                                <option value="q3" <?php echo (isset($_POST['question']) && $_POST['question'] === 'q3') ? 'selected' : ''; ?>>What is your favorite pet's name?</option>
                            </select>
                            <?php if(isset($signup_errors['question'])) { ?>
                                <span class="error-msg"><?php echo $signup_errors['question']; ?></span>
                            <?php } ?>
                        </div>
                        <div class="group">
                            <label for="signup-answer" class="label">Answer</label>
                            <input id="signup-answer" name="answer" type="text" class="input" required placeholder="Enter your answer">
                            <?php if(isset($signup_errors['answer'])) { ?>
                                <span class="error-msg"><?php echo $signup_errors['answer']; ?></span>
                            <?php } ?>
                        </div>
                        <div class="group">
                            <input type="submit" class="button" name="signup" value="Sign Up">
                        </div>
                        <?php
                        if(isset($signup_errors['db_error'])) {
                            echo '<span class="error-msg">' . $signup_errors['db_error'] . '</span>';
                        }
                        ?>
                    </form>
                    <div class="hr"></div>
                    <div class="foot-lnk">
                        <a href="userlogin.php">Already have an Account?</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
if(isset($_SESSION['account_created'])) {
    echo '<script>
            window.onload = function() {
                swal("Thank you!", "User account was successfully created!", "success");
            }
          </script>';
    unset($_SESSION['account_created']);
}
?>

</body>
</html>
