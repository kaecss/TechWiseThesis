<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profilePic'])) {
    $uploadDir = 'uploads/';
    $fileName = $username . '_' . basename($_FILES['profilePic']['name']);
    $uploadPath = $uploadDir . $fileName;
    
    if (move_uploaded_file($_FILES['profilePic']['tmp_name'], $uploadPath)) {
        $conn = new mysqli("localhost", "root", "", "techwisethesis");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("UPDATE user_form SET profile_pic = ? WHERE username = ?");
        $stmt->bind_param("ss", $uploadPath, $username);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        $_SESSION['profile_pic'] = $uploadPath;
        echo "Profile picture updated successfully.";
    } else {
        echo "There was an error uploading the file.";
    }
}
?>
