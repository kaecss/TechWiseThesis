<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $subject = $_POST["subject"];
    $message = $_POST["message"];

    $mail = new PHPMailer(true);

    try {
        //Server settings
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      
        $mail->isSMTP();                                            
        $mail->Host       = 'smtp.gmail.com';                    
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = 'techwisethesis@gmail.com';                     
        $mail->Password   = 'ldmstxjgeqeyuves';                               
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         
        $mail->Port       = 587;                                    

        //Recipients
        $mail->setFrom($email, $name);
        $mail->addAddress('techwisethesis@gmail.com', 'TechWiseThesis');    
        $mail->addReplyTo($email, $name);

        // Content
        $mail->isHTML(true);                                  
        $mail->Subject = $subject;
        $mail->Body    = "Message from: $name <br> Email: $email <br><br>" . nl2br($message);
        $mail->AltBody = wordwrap($message, 70, "\r\n");
        $mail->send();

        // Set session variable to indicate success
        $_SESSION['email_sent'] = true;
    } catch (Exception $e) {
        // Set session variable to indicate failure
        $_SESSION['email_sent'] = false;
    }

    // Redirect back to the contact form
    header('Location: contact.php');
    exit();
}
?>
