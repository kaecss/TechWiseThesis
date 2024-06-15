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

$stmt = $conn->prepare("SELECT id, profile_pic FROM user_form WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$id = $row['id']; // Retrieve user_id
$profilePic = $row['profile_pic'] ? $row['profile_pic'] : 'image/default-profile.jpg';

$stmt->close();

function getUploadedFiles($id, $conn) {
    $uploadedFiles = array();
    $stmt = $conn->prepare("SELECT file_name FROM user_files WHERE user_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $uploadedFiles[] = $row['file_name'];
    }
    $stmt->close();
    return $uploadedFiles;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdfFile'])) {
    $uploadDir = "uploads/user_$id/"; 
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $fileName = basename($_FILES['pdfFile']['name']);
    $uploadPath = $uploadDir . $fileName;
    
    $fileExtension = pathinfo($uploadPath, PATHINFO_EXTENSION);
    $allowedExtensions = array("pdf", "docs", "docx"); 

    if (in_array($fileExtension, $allowedExtensions)) {
        if (!file_exists($uploadPath)) {
            move_uploaded_file($_FILES['pdfFile']['tmp_name'], $uploadPath);
            $stmt = $conn->prepare("INSERT INTO user_files (user_id, file_name, file_path) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user_id, $fileName, $uploadPath);
            $stmt->execute();
            $stmt->close();
            
            echo "File uploaded successfully.";
            echo '<meta http-equiv="refresh" content="0">';
            exit();
        } else {
            echo "File already exists.";
        }
    } else {
        echo "Only PDF and DOCX files are allowed.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User-Home</title>
    <link rel="stylesheet" href="user-home.css">
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
            <li><a href="#" class="active"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="user-acc.php"><i class="fas fa-cog"></i> Account Settings</a></li>
            <li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Sign Out</a></li>
            <hr>
            <li><a href="user-Library.php"><i class="fas fa-book"></i> Library</a></li>
        </ul>
    </div>
    <div class="header-actions">
        <input type="text" id="searchInput" class="search-bar" placeholder="Search...">
        <button type="button" onclick="searchFiles()" class="search-button"><i class="fas fa-search"></i></button>
    </div>
    <div class="upload-form">
        <form action="" method="post" enctype="multipart/form-data">
            <input type="file" name="pdfFile" accept="all files">
            <input type="submit" class="upload-button" value="Upload">
        </form>
    </div>
    <div class="main-content">
        <div class="slider">
            <p>Upload Files:</p>
            <button class="nav-btn prev">&#10094;</button>
           <div class="content-container">
            <?php foreach (getUploadedFiles($id, $conn) as $file) : ?>
                <div class="content-item" id="file_<?php echo $file; ?>">
                    <div class="content-details">
                        <a href="view-thesis.php?file=<?php echo urlencode($file); ?>" target="_blank"><?php echo $file; ?></a>

                        <button class="delete-button" onclick="deleteFile('<?php echo $file; ?>')"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
            <button class="nav-btn next">&#10095;</button>
        </div>
        <div class="slider">
            <p>Recently:</p>
            <button class="nav-btn prev">&#10094;</button>
            <div class="content-container" id="recentlyOpenedFilesContainer"></div>
            <button class="nav-btn next">&#10095;</button>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const sliders = document.querySelectorAll(".slider");

            sliders.forEach(function(slider) {
                const contentContainer = slider.querySelector(".content-container");
                const prevButton = slider.querySelector(".prev");
                const nextButton = slider.querySelector(".next");

                const scrollAmount = 300;

                prevButton.addEventListener("click", function () {
                    contentContainer.scrollLeft -= scrollAmount;
                });

                nextButton.addEventListener("click", function () {
                    contentContainer.scrollLeft += scrollAmount;
                });
            });

            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', function() {
                searchFiles();
            });

            attachEventListeners();
        });

        function uploadFile(event) {
            const file = event.target.files[0];
            if (file) {
                alert(`File uploaded: ${file.name}`);
            }
        }

        function searchFiles() {
            var input, filter, ul, li, a, i, txtValue;
            input = document.getElementById('searchInput');
            filter = input.value.toUpperCase();
            ul = document.querySelector('.content-container');
            li = ul.querySelectorAll('.content-item');
            for (i = 0; i < li.length; i++) {
                a = li[i].getElementsByClassName('content-details')[0];
                txtValue = a.textContent || a.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }
            }
        }

        function deleteFile(fileName) {
            if (confirm("Are you sure you want to delete this file?")) {
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var fileElement = document.getElementById("file_" + fileName);
                        if (fileElement) {
                            fileElement.remove();
                        }
                    }
                };
                xhr.open("POST", "delete-file.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.send("fileName=" + fileName);
            }
        }

        function openFileRecently(fileName) {
            var recentlyOpenedContainer = document.getElementById("recentlyOpenedFilesContainer");
            var fileLink = document.createElement("div");
            fileLink.classList.add("content-item");
            var fileDetails = document.createElement("div");
            fileDetails.classList.add("content-details");
            var link = document.createElement("a");
            link.href = "uploads/" + fileName;
            link.target = "_blank";
            link.innerText = fileName;
            fileDetails.appendChild(link);
            var deleteButton = document.createElement("button");
            deleteButton.classList.add("delete-button");
            deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
            deleteButton.onclick = function() {
                deleteFile(fileName);
            };
            fileDetails.appendChild(deleteButton);
            fileLink.appendChild(fileDetails);
            recentlyOpenedContainer.appendChild(fileLink);
        }

        function attachEventListeners() {
            var fileLinks = document.querySelectorAll(".content-item a");
            fileLinks.forEach(function(link) {
                link.addEventListener("click", function(event) {
                    var fileName = event.target.innerText;
                    openFileRecently(fileName);
                });
            });
        }
    </script>
</body>
</html>
