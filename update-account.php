<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateBtn'])) {
    $newUsername = $_POST['new_username'];
    $newPassword = $_POST['new_password'];

    // Validate inputs (example: check if fields are not empty)
    if (empty($newUsername) || empty($newPassword)) {
        $_SESSION['update_status'] = "Username or password cannot be empty.";
        header("Location: user-acc.php");
        exit();
    }

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Database connection
    $conn = new mysqli("localhost", "root", "", "techwisethesis");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare update statement
    $stmt_update = $conn->prepare("UPDATE user_form SET username = ?, password = ? WHERE username = ?");
    $stmt_update->bind_param("sss", $newUsername, $hashedPassword, $_SESSION['username']);

    // Execute update
    if ($stmt_update->execute()) {
        // Check if any rows were affected by the update
        if ($stmt_update->affected_rows > 0) {
            $_SESSION['username'] = $newUsername;
            $_SESSION['update_status'] = "Account details updated successfully.";
        } else {
            $_SESSION['update_status'] = "Error: No changes made.";
        }
    } else {
        $_SESSION['update_status'] = "Error updating account details: " . $stmt_update->error;
    }

    // Close statement and connection for update
    $stmt_update->close();
    $conn->close();

    // Redirect to user account page
    header("Location: user-acc.php");
    exit();
}
?>
