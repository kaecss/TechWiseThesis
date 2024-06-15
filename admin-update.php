<?php
session_start();

if (!isset($_SESSION['usernamead'])) {
    header("Location: admin-login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "techwisethesis");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['usernamead'];

$update_messages = [];

if (isset($_POST['new_username']) && $_POST['new_username'] !== $username) {
    $new_username = $_POST['new_username'];
    $stmt = $conn->prepare("UPDATE admins SET usernamead = ? WHERE usernamead = ?");
    $stmt->bind_param("ss", $new_username, $username);
    $stmt->execute();
    $_SESSION['usernamead'] = $new_username; // Update session variable
    $update_messages[] = "Username updated successfully";
    $stmt->close();
}


if (isset($_POST['new_email'])) {
    $new_email = $_POST['new_email'];
    $stmt = $conn->prepare("UPDATE admins SET emailad = ? WHERE usernamead = ?");
    $stmt->bind_param("ss", $new_email, $username);
    $stmt->execute();
    if ($stmt->affected_rows > 0) { // Check if at least one row was affected
        $update_messages[] = "Email updated successfully";
    }
    $stmt->close();
}


if (isset($_POST['new_password']) && !empty($_POST['new_password'])) {
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE admins SET passwordad = ? WHERE usernamead = ?");
    $stmt->bind_param("ss", $new_password, $username);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $update_messages[] = "Password updated successfully";
    }
    $stmt->close();
}


// Handle file upload for profile picture
if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] === UPLOAD_ERR_OK) {
    $target_dir = "image/";
    $target_file = $target_dir . basename($_FILES["profilePic"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check file size
    if ($_FILES["profilePic"]["size"] > 500000) {
        $update_messages[] = "Sorry, your file is too large.";
    } elseif (!in_array($imageFileType, array("jpg", "jpeg", "png", "gif"))) {
        $update_messages[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    } elseif (move_uploaded_file($_FILES["profilePic"]["tmp_name"], $target_file)) {
        // Update profile_pic path in database
        $new_profile_pic = $target_file;
        $stmt = $conn->prepare("UPDATE admins SET profile_pic = ? WHERE usernamead = ?");
        $stmt->bind_param("ss", $new_profile_pic, $username);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $update_messages[] = "Profile picture updated successfully";
        } else {
            $update_messages[] = "Profile picture update failed.";
        }
        $stmt->close();
    } else {
        $update_messages[] = "Sorry, there was an error uploading your file.";
    }
}

$conn->close();

// Construct the final update status message based on the changes made
if (!empty($update_messages)) {
    $_SESSION['update_status'] = implode(" and ", $update_messages);
} else {
    $_SESSION['update_status'] = "No changes made.";
}

// Redirect back to admin account page with update status message
header("Location: admin_page.php");
exit();
?>
