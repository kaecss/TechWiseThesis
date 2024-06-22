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

$update_messages = [];

// Update profile picture if a file is uploaded
if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'] === UPLOAD_ERR_OK) {
    $profile_pic = $_FILES['fileInput']['name'];
    $profile_pic_tmp = $_FILES['fileInput']['tmp_name'];

    $profile_pic_dest = "uploads/" . basename($profile_pic);
    move_uploaded_file($profile_pic_tmp, $profile_pic_dest);

    $update_query = "UPDATE user_form SET profile_pic = ? WHERE username = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ss", $profile_pic_dest, $username);
    $stmt->execute();
    $stmt->close();

    $update_messages[] = "Profile picture updated successfully.";
} elseif ($_FILES['fileInput']['error'] !== UPLOAD_ERR_NO_FILE) {
    $_SESSION['update_status'] = "Error uploading profile picture.";
}

// Validate and update username if changed
if (isset($_POST['new_username']) && $_POST['new_username'] !== $username) {
    $new_username = $_POST['new_username'];

    // Check if the new username is already taken
    $check_username_query = "SELECT * FROM user_form WHERE username = ?";
    $stmt = $conn->prepare($check_username_query);
    $stmt->bind_param("s", $new_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $update_messages[] = "Username is already taken.";
    } else {
        // Update username
        $update_query = "UPDATE user_form SET username = ? WHERE username = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ss", $new_username, $username);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $_SESSION['username'] = $new_username;
            $update_messages[] = "Username updated successfully.";
        }
        $stmt->close();
    }
}

// Validate and update email if changed
if (isset($_POST['new_email']) && $_POST['new_email'] !== $_SESSION['email']) {
    $new_email = $_POST['new_email'];

    // Check if the new email is already taken
    $check_query = "SELECT COUNT(*) FROM user_form WHERE email = ? AND username <> ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ss", $new_email, $username);
    $check_stmt->execute();
    $check_stmt->bind_result($email_count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($email_count > 0) {
        // Email is already taken
        $update_messages[] = "Email is already taken. Please choose a different one.";
    } else {
        // Proceed with the update
        $update_query = "UPDATE user_form SET email = ? WHERE username = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ss", $new_email, $username);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $update_messages[] = "Email updated successfully";
        }
        $stmt->close();
    }
}

// Update password if changed
if (isset($_POST['new_password']) && !empty($_POST['new_password'])) {
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $update_query = "UPDATE user_form SET password = ? WHERE username = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ss", $new_password, $username);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $update_messages[] = "Password updated successfully";
    }
    $stmt->close();
}

$conn->close();

// Prepare update status message for session
if (!empty($update_messages)) {
    $_SESSION['update_status'] = implode(" and ", $update_messages);
} else {
    $_SESSION['update_status'] = "No changes made.";
}

header("Location: user-acc.php");
exit();
?>
