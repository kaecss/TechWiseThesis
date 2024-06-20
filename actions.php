<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_GET['user_id'];

$conn = new mysqli("localhost", "root", "", "techwisethesis");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT file_name, file_path FROM user_files WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $file = $result->fetch_assoc();
    $file_name = $file['file_name'];
    $file_path = $file['file_path'];
} else {
    echo "File not found.";
    exit();
}

$stmt->close();
$conn->close();

$action = $_GET['action'];

if ($action == 'download') {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));
    flush(); 
    readfile($file_path);
    exit();
} elseif ($action == 'openin') {
    if (pathinfo($file_path, PATHINFO_EXTENSION) == "pdf") {
        header("Location: " . $file_path);
        exit();
    } else {
        echo "Cannot open file in browser. Unsupported file type.";
        exit();
    }
} elseif ($action == 'saveas') {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));
    flush(); 
    readfile($file_path);
    exit();
} else {
    echo "Invalid action.";
    exit();
}
?>
