<?php 
session_start();
require "connection.php";
require 'vendor/autoload.php'; // Include Composer's autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = "";
$username = "";
$errors = array();

function sendVerificationEmail($email, $code) {
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'techwisethesis@gmail.com'; // Gmail address
        $mail->Password   = 'ldmstxjgeqeyuves';  // Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use SSL
        $mail->Port       = 465;

        //Recipients
        $mail->setFrom('techwisethesis@gmail.com', 'TechWiseThesis');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification Code';
        $mail->Body    = "Your verification code is $code";
        $mail->AltBody = "Your verification code is $code";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log the error
        error_log("Error sending email: {$mail->ErrorInfo}");
        return false;
    }
}

// If user signup button is clicked
if (isset($_POST['signup'])) {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $cpassword = mysqli_real_escape_string($con, $_POST['cpassword']);
    $question = mysqli_real_escape_string($con, $_POST['question']);
    $answer = mysqli_real_escape_string($con, $_POST['answer']);
    
    if ($password !== $cpassword) {
        $errors['password'] = "Confirm password not matched!";
    }
    
    $email_check = "SELECT * FROM user_form WHERE email = '$email'";
    $res = mysqli_query($con, $email_check);
    
    if (mysqli_num_rows($res) > 0) {
        $errors['email'] = "Email that you have entered already exists!";
    }
    
    if (count($errors) === 0) {
        $encpass = password_hash($password, PASSWORD_BCRYPT);
        $user_type = 'user'; // Default user type
        
        $insert_data = "INSERT INTO user_form (username, email, password, question, answer, user_type)
                        VALUES ('$username', '$email', '$encpass', '$question', '$answer', '$user_type')";
        
        $data_check = mysqli_query($con, $insert_data);
        
        if ($data_check) {
            $info = "Registration successful! Please login.";
            $_SESSION['info'] = $info;
            header('location: login.php');
            exit();
        } else {
            $errors['db-error'] = "Failed while inserting data into database!";
        }
    }
}

// If user clicks continue button in forgot password form
if (isset($_POST['check-email'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $check_email = "SELECT * FROM user_form WHERE email='$email'";
    $run_sql = mysqli_query($con, $check_email);
    
    if (mysqli_num_rows($run_sql) > 0) {
        $code = rand(999999, 111111);
        $insert_code = "INSERT INTO forgotpass (email, code) VALUES ('$email', '$code') ON DUPLICATE KEY UPDATE code='$code'";
        $run_query = mysqli_query($con, $insert_code);
        
        if ($run_query) {
            if (sendVerificationEmail($email, $code)) {
                $info = "We've sent a password reset otp to your email - $email";
                $_SESSION['info'] = $info;
                $_SESSION['email'] = $email;
                header('location: reset-code.php');
                exit();
            } else {
                $errors['otp-error'] = "Failed while sending code!";
            }
        } else {
            $errors['db-error'] = "Something went wrong!";
        }
    } else {
        $errors['email'] = "This email address does not exist!";
    }
}

// If user clicks check reset otp button
if (isset($_POST['check-reset-otp'])) {
    $_SESSION['info'] = "";
    $otp_code = mysqli_real_escape_string($con, $_POST['otp']);
    $check_code = "SELECT * FROM forgotpass WHERE code = $otp_code";
    $code_res = mysqli_query($con, $check_code);
    
    if (mysqli_num_rows($code_res) > 0) {
        $fetch_data = mysqli_fetch_assoc($code_res);
        $email = $fetch_data['email'];
        $_SESSION['email'] = $email;
        $info = "Please create a new password that you don't use on any other site.";
        $_SESSION['info'] = $info;
        header('location: new-password.php');
        exit();
    } else {
        $errors['otp-error'] = "You've entered incorrect code!";
    }
}

// If user clicks change password button
if (isset($_POST['change-password'])) {
    $_SESSION['info'] = "";
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $cpassword = mysqli_real_escape_string($con, $_POST['cpassword']);
    
    if($password !== $cpassword) {
        $errors['password'] = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters long.";
    } else {
        $email = $_SESSION['email']; // getting this email using session
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        // Update password in the database using prepared statement
        $stmt = $con->prepare("UPDATE user_form SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);

        if($stmt->execute()) {
            $_SESSION['password_changed'] = true;
            header('Location: new-password.php');
            exit();
        } else {
            $errors['db_error'] = 'Database error: ' . $stmt->error;
        }
    }
}

// If login now button click
if (isset($_POST['login-now'])) {
    header('Location: login.php');
}
?>