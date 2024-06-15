<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$username = $_SESSION['username'];

$conn = new mysqli("localhost", "root", "", "techwisethesis");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT password, profile_pic FROM user_form WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$hashedPassword = $row['password']; 
$profilePic = $row['profile_pic'] ? $row['profile_pic'] : 'image/default-profile.jpg';

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Account Settings</title>
    <link rel="stylesheet" href="user-acc.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="side-panel">
        <div class="company-name">
            <a href="#">
                <img src="image/logo.png" alt="Company Logo" class="company-logo">
            </a>
            <h2>TechWiseThesis</h2>
        </div>
        <div class="user-info">
            <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile" class="user-icon" id="userIcon">
            <span class="username"><?php echo htmlspecialchars($username); ?></span>
        </div>
        <ul>
            <li><a href="user_page.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="#" class="active"><i class="fas fa-cog"></i> Account Settings</a></li>
            <li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Sign Out</a></li>
            <hr>
            <li><a href="user-Library.php"><i class="fas fa-book"></i> Library</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="profile-upload">
            <div class="profile-pic-container">
                <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile" class="profile-pic" id="profilePic">
                <input type="file" id="fileInput" accept=".jpeg, .jpg, .png, .gif" onchange="loadFile(event)">
            </div>
        </div>
         <button onclick="document.getElementById('fileInput').click()" class="upload-btn">Upload Image</button>
        
        <div class="account-details">
            <h3>Account Details</h3>
            <form action="update-account.php" method="post" enctype="multipart/form-data">
                <div class="username-container">
                    <label for="new_username">Username:</label>
                    <input type="text" id="new_username" name="new_username" value="<?php echo htmlspecialchars($username); ?>" placeholder="New Username">
                </div>
                <div class="password-container">
                    <label for="new_password">New Password:</label>
                    <input type="text" id="new_password" name="new_password" placeholder="New Password">
                </div>
                <button type="submit" name="updateBtn">Update</button>
            </form>
        </div>
    </div>

    <div class="notification" id="notification"></div>

    <script>
        const loadFile = event => {
            const image = document.getElementById('profilePic');
            const userIcon = document.getElementById('userIcon');
            const file = event.target.files[0];
            const url = URL.createObjectURL(file);

            image.src = url;
            userIcon.src = url;

            image.onload = () => {
                URL.revokeObjectURL(image.src);
            };
            userIcon.onload = () => {
                URL.revokeObjectURL(userIcon.src);
            };
            const formData = new FormData();
            formData.append('profilePic', file);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'upload-profile-pic.php', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
               
                }
            };
            xhr.send(formData);
        };

        document.addEventListener('DOMContentLoaded', () => {
            <?php if (isset($_SESSION['update_status'])): ?>
                const notification = document.getElementById('notification');
                notification.textContent = "<?php echo $_SESSION['update_status']; ?>";
                notification.style.display = 'block';
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 3000);

                <?php unset($_SESSION['update_status']); ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>
