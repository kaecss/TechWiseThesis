<?php
session_start();
include 'connection.php';

$login_errors = []; 
$signup_errors = []; 

if (isset($_POST['submit']) && isset($_POST['email_or_username']) && isset($_POST['password'])) {
    $email_or_username = mysqli_real_escape_string($con, $_POST['email_or_username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    // Check if the input is an email or username
    if (filter_var($email_or_username, FILTER_VALIDATE_EMAIL)) {
        // Query by email
        $select = "SELECT * FROM user_form WHERE email = ?";
        $stmt = $con->prepare($select);
        $stmt->bind_param("s", $email_or_username);
    } else {
        // Query by username
        $select = "SELECT * FROM user_form WHERE username = ?";
        $stmt = $con->prepare($select);
        $stmt->bind_param("s", $email_or_username);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['user_type'] = $row['user_type'];

            // Redirect to appropriate page
            if ($row['user_type'] == 'admin') {
                header("location: admin_page.php");
                exit;
            } elseif ($row['user_type'] == 'user') {
                header('location: user_page.php');
                exit;
            }
        } else {
            $login_errors[] = 'Incorrect password!';
        }
    } else {
        $login_errors[] = 'Email or username not found!';
    }
}

// Function to check if username exists in database
function usernameExists($con, $username) {
    $stmt = $con->prepare("SELECT * FROM user_form WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Function to check if email exists in database
function emailExists($con, $email) {
    $stmt = $con->prepare("SELECT * FROM user_form WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Signup
if (isset($_POST['signup'])) {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($con, $_POST['confirm_password']);
    $question1 = mysqli_real_escape_string($con, $_POST['question1']);
    $answer1 = mysqli_real_escape_string($con, $_POST['answer1']);
    $question2 = mysqli_real_escape_string($con, $_POST['question2']);
    $answer2 = mysqli_real_escape_string($con, $_POST['answer2']);
    $user_type = 'user'; // Assuming default user type is 'user'

    // Validate inputs
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

    if (empty($question1)) {
        $signup_errors['question1'] = 'Please select a security question.';
    }

    if (empty($answer1)) {
        $signup_errors['answer1'] = 'Answer is required';
    }

    if (empty($question2)) {
        $signup_errors['question2'] = 'Please select a security question.';
    }

    if (empty($answer2)) {
        $signup_errors['answer2'] = 'Answer is required';
    }

    // If no errors, proceed to insert into database
    if (empty($signup_errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert into database using prepared statement
        $stmt = $con->prepare("INSERT INTO user_form (username, email, password, question1, answer1, question2, answer2, user_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $username, $email, $hashed_password, $question1, $answer1, $question2, $answer2, $user_type);

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
    <link rel="stylesheet" href="user-login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
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

<div class="company-info">
    <a href="admin-login.php">
        <img src="image/logo2.png" alt="Company Logo" class="company-logo">
    </a>
</div>
<div class="container">
    <!--//////////LOG IN////////////////-->
    <div class="login-wrap">
        <div class="login-html">
            <a href="main.html" class="exit-button"><i class="fa fa-times"></i></a>
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
                            <label class="label">Email or Username</label>
                            <input name="email_or_username" type="text" class="input" required placeholder="Enter your email or username">
                        </div>
                        <div class="group">
                            <label for="login-password" class="label">Password</label>
                            <input id="login-password" name="password" type="password" class="input" required placeholder="Enter your password">
                        </div>
                        <div class="group">
                            <input type="submit" class="button" value="Log In" name="submit">
                        </div>

                        <?php
                        if(isset($login_errors) && !empty($login_errors)) {
                            foreach($login_errors as $error) {
                                echo '<div class="error">' . htmlspecialchars($error) . '</div>';
                            }
                        }
                        ?>

                    <br>
                    <hr>
                    <div class="aw">
                        <a href="forgot_pass.php">Forgot your password?</a>
                    </div>
                    </form>
                </div>
                <div class="sign-in-htm">
                    <?php
                    if(isset($_SESSION['account_created']) && $_SESSION['account_created'] == true){
                        unset($_SESSION['account_created']);
                        echo '<script>
                        window.onload = function() {
                            alert("User account has been successfully created!");
                        }
                        </script>';
                    }
                    ?>
                    <form action="" method="POST">
                        <div class="group">
                            <label class="label">Username</label>
                            <input name="username" type="text" class="input" required placeholder="Create your username">
                            <?php if(isset($signup_errors['username'])) { echo '<div class="error">' . htmlspecialchars($signup_errors['username']) . '</div>'; } ?>
                        </div>
                        <div class="group">
                            <label class="label">Email</label>
                            <input name="email" type="email" class="input" required placeholder="Enter your email">
                            <?php if(isset($signup_errors['email'])) { echo '<div class="error">' . htmlspecialchars($signup_errors['email']) . '</div>'; } ?>
                        </div>
                        <div class="group">
                            <label class="label">Password</label>
                            <input name="password" type="password" class="input" required placeholder="Create your password">
                            <?php if(isset($signup_errors['password'])) { echo '<div class="error">' . htmlspecialchars($signup_errors['password']) . '</div>'; } ?>
                        </div>
                        <div class="group">
                            <label class="label">Confirm Password</label>
                            <input name="confirm_password" type="password" class="input" required placeholder="Confirm your password">
                            <?php if(isset($signup_errors['confirm_password'])) { echo '<div class="error">' . htmlspecialchars($signup_errors['confirm_password']) . '</div>'; } ?>
                        </div>
                        <div class="group">
                            <label class="label">Security Question 1</label>
                            <select name="question1" class="input" required>
                                <option value="">Select a question...</option>
                                <option value="q1">What is your mother's maiden name?</option>
                                <option value="q2">What is the name of your first pet?</option>
                                
                            </select>
                            <?php if(isset($signup_errors['question1'])) { echo '<div class="error">' . htmlspecialchars($signup_errors['question1']) . '</div>'; } ?>
                        </div>
                        <div class="group">
                            <label class="label">Answer 1</label>
                            <input name="answer1" type="text" class="input" required placeholder="Answer to the question">
                            <?php if(isset($signup_errors['answer1'])) { echo '<div class="error">' . htmlspecialchars($signup_errors['answer1']) . '</div>'; } ?>
                        </div>
                        <div class="group">
                            <label class="label">Security Question 2</label>
                            <select name="question2" class="input" required>
                                <option value="">Select a question...</option>
                                <option value="q3">Where did you grow up?</option>
                                <option value="q4">What is your favorite book?</option>
                                
                            </select>
                            <?php if(isset($signup_errors['question2'])) { echo '<div class="error">' . htmlspecialchars($signup_errors['question2']) . '</div>'; } ?>
                        </div>
                        <div class="group">
                            <label class="label">Answer 2</label>
                            <input name="answer2" type="text" class="input" required placeholder="Answer to the question">
                            <?php if(isset($signup_errors['answer2'])) { echo '<div class="error">' . htmlspecialchars($signup_errors['answer2']) . '</div>'; } ?>
                        </div>
                        <br>
                        <div class="group">
                            <input type="submit" class="button" value="Sign Up" name="signup">
                        </div>

                        <?php
                        if(isset($signup_errors) && !empty($signup_errors)) {
                            foreach($signup_errors as $error) {
                                echo '<div class="error">' . htmlspecialchars($error) . '</div>';
                            }
                        }
                        ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>