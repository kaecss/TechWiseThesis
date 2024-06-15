<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fileName'])) {
    $fileName = $_POST['fileName'];
    $uploadDir = "";
    $filePath = $uploadDir . $fileName;
    
    if (file_exists($filePath)) {
        unlink($filePath);
    } else {
        echo "File not found.";
        exit();
    }

    $conn = new mysqli("localhost", "root", "", "techwisethesis");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("DELETE FROM user_files WHERE file_name = ?");
    $stmt->bind_param("s", $fileName);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "File deleted successfully.";
    } else {
        echo "File not found in the database.";
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>
