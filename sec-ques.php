<?php
session_start();
include 'connection.php';

$host = 'localhost'; 
$dbname = 'techwisethesis'; 
$username = 'root'; 
$password = ''; 


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if (isset($_POST['submit'])) {
    $username = $_POST['email_or_username'];
    $question1 = $_POST['question1'];
    $answer1 = $_POST['answer1'];
    $question2 = $_POST['question2'];
    $answer2 = $_POST['answer2'];

    
    $query = "SELECT * FROM user_form WHERE username = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        
        if ($user['question1'] === $question1 && $user['answer1'] === $answer1 &&
            $user['question2'] === $question2 && $user['answer2'] === $answer2) {
            $_SESSION['username'];
            header("Location: changePass.php");
            exit();
        } else {
            // palagyan nung pop up
            $error_message = "Answers do not match. Please try again.";
        }
    } else {
        // palagyan nung pop up
        $error_message = "User not found. Please check your username.";
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
            <button class="exit-button"><i class="fa fa-xmark"></i></button>
        </a>
        <input id="tab-1" type="radio" name="tab" class="log-in" checked><label for="tab-1" class="tab">Forgot Password</label>
        <div class="login-form">
            <div class="log-in-htm">
                <div class="group">
                    <form action="sec-ques.php" method="POST">
                        <label class="label">Username</label>
                        <input name="email_or_username" type="text" class="input" required placeholder="Enter username">
                        <label class="label">Security Question 1</label>
                        <select name="question1" class="input" required>
                            <option value="">Select a question...</option>
                            <option value="q1">What is your mother's maiden name?</option>
                            <option value="q2">What is the name of your first pet?</option>
                        </select>
                        <label class="label">Answer 1</label>
                        <input name="answer1" type="text" class="input" required placeholder="Answer to the question">
                        <label class="label">Security Question 2</label>
                        <select name="question2" class="input" required>
                            <option value="">Select a question...</option>
                            <option value="q3">Where did you grow up?</option>
                            <option value="q4">What is your favorite book?</option>
                        </select>
                        <label class="label">Answer 2</label>
                        <input name="answer2" type="text" class="input" required placeholder="Answer to the question">
                        <br>
                        <input class="form-control button" type="submit" name="submit" value="Continue">
                    </form>
                    
                    <br>
                    <hr>
                    <div class="aw">
                        <a href="forgot_pass.php">Use email instead</a>
                    </div>
                
                </div>
            </div>
        </div>
    </div>
</div>
    
</body>
</html>