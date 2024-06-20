<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

$uploadDir = 'uploads/user_$user_id/' . $_SESSION['user_id'] . '/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$fileName = $_FILES['pdfFile']['name'];
$filePath = $uploadDir . $fileName;

if (move_uploaded_file($_FILES['pdfFile']['tmp_name'], $filePath)) {
    echo json_encode(['status' => 'success', 'fileName' => $fileName, 'filePath' => $filePath]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error uploading file']);
}
?>
