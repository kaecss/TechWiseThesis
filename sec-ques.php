<?php 
session_start();
include 'connection.php';

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
                    <div class="form-group">
                        <input class="form-control button" type="submit" name="check-email" value="Continue">
                    </div>

                    <br>
                    <hr>
                    <div class="aw">
                        <a href="forgot_pass.php">Use Email instead</a>
                    </div>
                
            </div>
        </div>
    </div>
    
</body>
</html>