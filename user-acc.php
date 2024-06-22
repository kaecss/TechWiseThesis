<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);

$conn = new mysqli("localhost", "root", "", "techwisethesis");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT password, email, profile_pic FROM user_form WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    exit("User not found.");
}

$hashedPassword = $row['password'];
$profilePic = $row['profile_pic'] ? htmlspecialchars($row['profile_pic']) : 'image/default-profile.jpg';
$email = htmlspecialchars($row['email']);

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Account Settings</title>
    <link rel="stylesheet" href="user-acco.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  
</head>
<body>
    <div class="side-panel">
        <div class="company-name">
            <a href="#">
                <img src="logo.png" alt="Company Logo" class="company-logo">
            </a>
            <h2>TechWiseThesis</h2>
        </div>
        <div class="user-info">
            <img src="<?php echo $profilePic; ?>" alt="Profile" class="user-icon" id="userIcon">
            <span class="username"><?php echo $username; ?></span>
        </div>
        <ul>
            <li><a href="user_page.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="#" class="active"><i class="fas fa-cog"></i> Account Settings</a></li>
            <li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Sign Out</a></li>
            <hr>
            <li><a href="User-Library.php"><i class="fas fa-book"></i> Library</a></li>
        </ul>
    </div>
    <div class="main-content">
        <form action="update-account.php" method="post" enctype="multipart/form-data" id="accountForm">
            <div class="profile-upload">
                <div class="profile-pic-container">
                    <img src="<?php echo $profilePic; ?>" alt="Profile" class="profile-pic" id="profilePic">
                    <input type="file" id="fileInput" name="fileInput" accept=".jpeg, .jpg, .png, .gif" onchange="loadFile(event)" style="display:none;">
                </div>
                <div class="upload-btn-container">
                    <button type="button" onclick="document.getElementById('fileInput').click()" class="upload-btn" style="display:none;">Upload Image</button>
                </div>
            </div>
            <div class="account-details">
                <h3>Account Details</h3>
                <div class="username-container">
                    <label>Username:</label>
                    <span id="display_username"><?php echo $username; ?></span>
                    <input type="text" id="new_username" name="new_username" value="<?php echo $username; ?>" placeholder="New Username" style="display:none;">
                </div>
                <div class="password-container">
                    <label>Password:</label>
                    <span id="display_password">********</span>
                    <input type="password" id="new_password" name="new_password" placeholder="New Password" style="display:none;">
                </div>
				<span id="passwordError" class="error-message"></span>
                <div class="email-container">
                    <label>Email:</label>
                    <span id="display_email"><?php echo $email; ?></span>
                    <input type="email" id="new_email" name="new_email" value="<?php echo $email; ?>" placeholder="New Email" style="display:none;">
					<span id="passwordError" class="error-message"></span> 
                </div>
				
                <div class="button-container">
                    <button type="submit" name="updateBtn" id="updateBtn" style="display: none;">Save</button>
                    <button type="button" name="editBtn" id="editBtn">Edit</button>
                </div>
            </div>
        </form>
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
                notification.textContent = "<?php echo htmlspecialchars($_SESSION['update_status']); ?>";
                notification.style.display = 'block';
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 3000);
                <?php unset($_SESSION['update_status']); ?>
            <?php endif; ?>
        });

        document.getElementById('editBtn').addEventListener('click', function() {
            var displayElements = document.querySelectorAll('#display_username, #display_password, #display_email');
            var inputElements = document.querySelectorAll('#new_username, #new_password, #new_email');
            var uploadBtn = document.querySelector('.upload-btn');
            
            displayElements.forEach(function(element) {
                element.style.display = 'none';
            });

            inputElements.forEach(function(element) {
                element.style.display = 'inline-block';
            });

            uploadBtn.style.display = 'inline-block';

            var updateBtn = document.getElementById('updateBtn');
            updateBtn.style.display = 'inline-block';

            var editBtn = document.getElementById('editBtn');
            editBtn.style.display = 'none';
        });

        document.getElementById('accountForm').addEventListener('submit', function(event) {
			var newPassword = document.getElementById('new_password').value;
			var newEmail = document.getElementById('new_email').value;
			var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
			var passwordError = "";
			var emailError = "";

			if (newPassword && newPassword.length < 8) {
				passwordError = "Password must be at least 8 characters long.";
			}

			if (newEmail && !emailPattern.test(newEmail)) {
				emailError = "Please enter a valid email address.";
			}

			var passwordErrorElement = document.getElementById('passwordError');
		passwordErrorElement.textContent = passwordError; // Update error message


			if (passwordError || emailError) {
				event.preventDefault();
				notification.textContent = passwordError + " " + emailError;
				notification.style.display = 'none';
				setTimeout(() => {
					notification.style.display = 'none';
				}, 5000);
			}
			});

    </script>
</body>
</html>
