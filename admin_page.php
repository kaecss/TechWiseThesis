<?php
session_start();

if (!isset($_SESSION['usernamead'])) {
    header("Location: admin-login.php");
    exit();
}

$username = $_SESSION['usernamead'];

$conn = new mysqli("localhost", "root", "", "techwisethesis");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT emailad, profile_pic FROM admins WHERE usernamead = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$email = $row['emailad'];
$profilePic = $row['profile_pic'] ? $row['profile_pic'] : 'image/default-profile.jpg';

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Account</title>
    <link rel="stylesheet" href="user-acc.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .profile-pic-container {
            position: relative;
            display: inline-block;
        }

        .upload-btn {
            position: absolute;
            bottom: -50px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #734122;
            color: white;
            padding: 8px 15px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            font-size: 14px;
        }
    </style>
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
            <li><a href="Dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="userlist.php" class="active"><i class="fas fa-home"></i> User</a></li>
            <li><a href="adminrecords.php" class="active"><i class="fas fa-home"></i> Record</a></li>
            <hr>
            <li><a href="admin_page.php"><i class="fas fa-cog"></i> Account Settings</a></li>
            <li><a href="admin-login.php"><i class="fas fa-sign-out-alt"></i> Sign Out</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="profile-upload">
            <div class="profile-pic-container">
                <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile" class="profile-pic" id="profilePic">
                <button onclick="document.getElementById('fileInput').click()" class="upload-btn">Upload Image</button>
                <input type="file" id="fileInput" accept="image/*" onchange="loadFile(event)" style="display:none;">

            </div>
        </div>
        
        <div class="account-details">
            <h3>Account Details</h3>

            <form action="admin-update.php" method="post" enctype="multipart/form-data">
                <div class="username-container">
                    <label for="new_username">Username:</label>
                    <input type="text" id="new_username" name="new_username" value="<?php echo htmlspecialchars($username); ?>" placeholder="New Username">
                </div>

                <div class="password-container">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" placeholder="New Password">
                </div>

                <div class="password-container">
                    <label for="new_email">New Email:</label>
                    <input type="email" id="new_email" name="new_email" value="<?php echo htmlspecialchars($email); ?>" placeholder="New Email">
                </div>

                <button type="submit" name="updateBtn">Update</button>
            </form>
        </div>
    </div>

    <div class="notification" id="notification"></div>

    <script>
        const loadFile = event => {
            const image = document.getElementById('profilePic');
            const file = event.target.files[0];
            const url = URL.createObjectURL(file);

            image.src = url;

            image.onload = () => {
                URL.revokeObjectURL(image.src);
            };
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
