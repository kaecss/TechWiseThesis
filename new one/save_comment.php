<?php
session_start();

if (!isset($_SESSION['username'])) {
    die(json_encode(['success' => false, 'message' => 'User not logged in.']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file_id = $_POST['file_id'];
    $comment = $_POST['comment'];

    $conn = new mysqli("localhost", "root", "", "techwisethesis");
    if ($conn->connect_error) {
        die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
    }

    $stmt_user = $conn->prepare("SELECT id FROM user_form WHERE username = ?");
    $stmt_user->bind_param("s", $_SESSION['username']);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($result_user->num_rows > 0) {
        $user = $result_user->fetch_assoc();
        $user_id = $user['id'];
    } else {
        die(json_encode(['success' => false, 'message' => 'User not found.']));
    }

    $stmt_user->close();

    $stmt = $conn->prepare("INSERT INTO file_comments (file_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $file_id, $user_id, $comment);
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        echo json_encode(['success' => true]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save comment.']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}
