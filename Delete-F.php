<?php
session_start();

if (!isset($_SESSION['username'])) {
    die(json_encode(['status' => 'error', 'message' => 'User not logged in.']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file_id = $_POST['file_id'];

    $conn = new mysqli("localhost", "root", "", "techwisethesis");
    if ($conn->connect_error) {
        die(json_encode(['status' => 'error', 'message' => 'Database connection failed.']));
    }

    $stmt = $conn->prepare("SELECT file_path FROM user_files WHERE file_id = ?");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die(json_encode(['status' => 'error', 'message' => 'File not found.']));
    }

    $file = $result->fetch_assoc();
    $file_path = $file['file_path'];
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM file_comments WHERE file_id = ?");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM user_files WHERE file_id = ?");
    $stmt->bind_param("i", $file_id);
    if ($stmt->execute()) {
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        $stmt->close();
        $conn->close();
        echo json_encode(['status' => 'success', 'message' => 'File deleted successfully.']);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete file from database.']);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}
?>
