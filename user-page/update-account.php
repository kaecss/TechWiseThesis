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

if (isset($_POST['new_username']) && $_POST['new_username'] !== $username) {
    $new_username = $_POST['new_username'];
    $update_query = "UPDATE user_form SET username = ? WHERE username = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ss", $new_username, $username);
    $stmt->execute();
    $_SESSION['username'] = $new_username; 
    $update_messages[] = "Username updated successfully";
    $stmt->close();
}

if (isset($_POST['new_email'])) {
    $new_email = $_POST['new_email'];
    $update_query = "UPDATE user_form SET email = ? WHERE username = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ss", $new_email, $username);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $update_messages[] = "Email updated successfully";
    }
    $stmt->close();
}

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

if (!empty($update_messages)) {
    $_SESSION['update_status'] = implode(" and ", $update_messages);
} else {
    $_SESSION['update_status'] = "No changes made.";
}

header("Location: user-acc.php");
exit();
?>
