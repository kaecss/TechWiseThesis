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

$stmt = $conn->prepare("SELECT admin_id, profile_pic FROM admins WHERE usernamead = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$profilePic = $row['profile_pic'] ? $row['profile_pic'] : 'image/default-profile.jpg';

$stmt->close();


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Records</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
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
    <div class="header-actions">
        <input type="text" id="searchInput" class="search-bar" placeholder="Search...">
        <button type="button" onclick="searchFiles()" class="search-button"><i class="fas fa-search"></i></button>
    </div>

    <br>
    <hr>
    <br>

    </div>
    <div class="dash-table">
            <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Title</th>
                    <th scope="col">Author</th>
                    <th scope="col">Date Uploaded</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">1</th>
                    <td>Mark</td>
                    <td>Otto</td>
                    <td>@mdo</td>
                </tr>
                <tr>
                    <th scope="row">2</th>
                    <td>Jacob</td>
                    <td>Thornton</td>
                    <td>@fat</td>
                </tr>
                <tr>
                    <th scope="row">3</th>
                    <td colspan="2">Larry the Bird</td>
                    <td>@twitter</td>
                </tr>
            </tbody>
            </table>
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
