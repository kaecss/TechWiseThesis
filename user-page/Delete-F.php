<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

$conn = new mysqli("localhost", "root", "", "techwisethesis");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file_id'])) {
    $file_id = $_POST['file_id'];

    $stmt = $conn->prepare("SELECT file_path FROM user_files WHERE file_id = ?");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($file_path);
        $stmt->fetch();
        
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        $stmt = $conn->prepare("DELETE FROM user_files WHERE file_id = ?");
        $stmt->bind_param("i", $file_id);
        $stmt->execute();
        $stmt->close();

        echo json_encode(array('status' => 'success'));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'File not found in database.'));
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Invalid request.'));
}

$conn->close();
?>
