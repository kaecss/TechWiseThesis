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

$stmt = $conn->prepare("SELECT id, username, profile_pic FROM user_form WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$user_id = $row['id'];
$username = $row['username']; 
$profilePic = $row['profile_pic'] ? $row['profile_pic'] : 'default-profile.png';
$stmt->close();


function getUploadedFiles($user_id, $conn) {
    $uploadedFiles = array();
    $stmt = $conn->prepare("SELECT file_id, file_name, file_path, file_category, upload_date FROM user_files WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $uploadedFiles[] = $row;
    }
    $stmt->close();
    return $uploadedFiles;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdfFile'])) {
    $uploadDir = "uploads/user_$user_id/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = basename($_FILES['pdfFile']['name']);
    $uploadPath = $uploadDir . $fileName;

    $fileExtension = pathinfo($uploadPath, PATHINFO_EXTENSION);
    $allowedExtensions = array("pdf", "docx");

    if (in_array($fileExtension, $allowedExtensions)) {
        if (!file_exists($uploadPath)) {
           if (move_uploaded_file($_FILES['pdfFile']['tmp_name'], $uploadPath)) {
    $category = $_POST['category']; 

    $stmt = $conn->prepare("INSERT INTO user_files (user_id, file_name, file_path, file_category, upload_date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("isss", $user_id, $fileName, $uploadPath, $category);

    if ($stmt->execute()) {
        $file_id = $stmt->insert_id; 
        $stmt->close();
        echo json_encode(array('status' => 'success', 'fileName' => $fileName, 'filePath' => $uploadPath, 'file_id' => $file_id, 'file_category' => $category));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Failed to insert file into database.'));
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Failed to move uploaded file.'));
}

        } else {
            echo json_encode(array('status' => 'error', 'message' => 'File already exists.'));
        }
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Only PDF and DOCX files are allowed.'));
    }
    exit();
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile" class="user-icon" id="userIcon">
            <span class="username"><?php echo htmlspecialchars($username); ?></span>
        </div>
        <ul>
            <li><a href="#" class="active"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="user-acc.php"><i class="fas fa-cog"></i> Account Settings</a></li>
			<li><a href="login.php" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Sign Out</a></li>

            <hr>
            <li><a href="User-Library.php"><i class="fas fa-book"></i> Library</a></li>
        </ul>
    </div>
    <div class="header-actions">
        <input type="text" id="searchInput" class="search-bar" placeholder="Search...">
        <button type="button" onclick="searchFiles()" class="search-button"><i class="fas fa-search"></i></button>
    </div>
    <div class="upload-form">
       <button class="upload-button" onclick="showUploadModal()"><i class="fas fa-upload"></i> Upload File</button>

    </div>

   <div id="uploadModal" style="display: none;">
    <div id="uploadArea" class="upload-area" onclick="triggerFileInput()" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleFileDrop(event)">
        <p>Click here to select a file<br>or drag a file to upload</p>
    </div>
    <form id="uploadForm">
        <input type="file" id="fileInput" style="display: none;">
        <input type="text" id="descriptionInput" name="description" placeholder="Description">
        <select id="categorySelect" name="category">
            <option value="Science">Scientific Studies</option>
            <option value="Business">Market Research</option>
            <option value="Literature">Literature Reviews</option>
            <option value="Others">Others</option>
        </select>
    </form>
</div>




    <div class="main-content">
        <div class="slider">
            <p>Upload Files:</p>
            <button class="nav-btn prev">&#10094;</button>
         <div class="content-container" id="uploadedFilesContainer">
    <?php foreach (getUploadedFiles($user_id, $conn) as $file) : ?>
    <div class="content-item" id="file_<?php echo $file['file_id']; ?>">
        <div class="content-details">
            <div class="file-header">
                <a href="view.php?file_id=<?php echo $file['file_id']; ?>" class="file-name"><?php echo $file['file_name']; ?></a>
            </div>
			<div class="author">
				<span>Author: <?php echo htmlspecialchars($username); ?></span>
			 </div>
			<div class="file-category">
				<span class="file-category">Category: <?php echo $file['file_category']; ?></span>
			</div>
			<div class="file-upload-date">
				<span>Uploaded on: <?php echo date('M d, Y ', strtotime($file['upload_date'])); ?></span>
			</div>
				<button class="delete-button" onclick="deleteFile('<?php echo $file['file_id']; ?>')"><i class="fas fa-trash"></i></button>
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
		function deleteFile(file_id) {
			if (confirm("Are you sure you want to delete this file?")) {
				fetch('Delete-F.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded',
					},
					body: 'file_id=' + file_id,
				})
				.then(response => response.json())
				.then(data => {
					if (data.status === 'success') {
						var fileElement = document.getElementById("file_" + file_id);
						if (fileElement) {
							fileElement.remove();
							Swal.fire({
								icon: 'success',
								title: 'File Deleted!',
								text: 'The file has been successfully deleted.'
							});
						} else {
							alert("File element not found.");
						}
					} else {
						alert(data.message);
					}
				})
				.catch(error => {
					console.error('Error:', error);
					alert('Error deleting the file.');
				});
			}
		}

		function openFileRecently(fileName) {
    var recentlyOpenedFiles = JSON.parse(localStorage.getItem('recentlyOpenedFiles')) || [];

    if (!recentlyOpenedFiles.includes(fileName)) {
        recentlyOpenedFiles.push(fileName);
    }
    if (recentlyOpenedFiles.length > 5) {
        recentlyOpenedFiles.shift(); 
    }
    
    var userId = <?php echo json_encode($user_id); ?>;
    var recentlyOpenedKey = 'recentlyOpenedFiles_' + userId;
    localStorage.setItem(recentlyOpenedKey, JSON.stringify(recentlyOpenedFiles));

    displayRecentlyOpenedFiles();
}

function displayRecentlyOpenedFiles() {
    var userId = <?php echo json_encode($user_id); ?>;
    var recentlyOpenedKey = 'recentlyOpenedFiles_' + userId;
    var recentlyOpenedFiles = JSON.parse(localStorage.getItem(recentlyOpenedKey)) || [];

    const recentlyOpenedContainer = document.getElementById("recentlyOpenedFilesContainer");
    recentlyOpenedContainer.innerHTML = '';

    recentlyOpenedFiles.forEach(fileName => {
        var fileLink = document.createElement("div");
        fileLink.classList.add("content-item");
        var fileDetails = document.createElement("div");
        fileDetails.classList.add("content-details");
        var link = document.createElement("a");
        link.href = "uploads/" + fileName;
        link.target = "_blank";
        link.innerText = fileName;
        fileDetails.appendChild(link);
        fileLink.appendChild(fileDetails);
        recentlyOpenedContainer.appendChild(fileLink);
    });
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

        function showUploadModal() {
            Swal.fire({
                title: 'Upload File',
                html: document.getElementById('uploadModal').innerHTML,
                showCloseButton: true,
                showConfirmButton: false,
                customClass: {
                    container: 'upload-modal-container',
                    popup: 'upload-modal-popup',
                },
                didOpen: () => {
                    initFileUpload();
                }
            });
        }

        function triggerFileInput() {
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = '.pdf,.docx';
            fileInput.addEventListener('change', () => {
                const file = fileInput.files[0];
                handleFileSelection(file);
            });
            fileInput.click();
        }

        function initFileUpload() {
            const uploadArea = document.querySelector('.swal2-content .upload-area');

            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', () => {
                uploadArea.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                const file = e.dataTransfer.files[0];
                handleFileSelection(file);
            });

            uploadArea.addEventListener('click', () => {
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.accept = '.pdf,.docx';
                fileInput.addEventListener('change', () => {
                    const file = fileInput.files[0];
                    handleFileSelection(file);
                });
                fileInput.click();
            });
        }




        let selectedFile = null;
        function handleFileSelection(file) {
            selectedFile = file;
            if (file) {
                Swal.fire({
                    title: 'File Selected',
                    text: 'Please select a category for this file.',
                    showCancelButton: true,
                    cancelButtonText: 'Cancel',
                    html: document.getElementById('categorySelect').outerHTML,
                }).then((result) => {
                    if (result.isConfirmed) {
                        confirmUpload();
                    }
                });
            }
        }

        function confirmUpload() {
            const category = document.querySelector('.swal2-popup #categorySelect').value;

            if (!selectedFile) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No file selected.',
                });
                return;
            }

            Swal.fire({
                title: 'Confirm Upload',
                text: `Upload '${selectedFile.name}' to category '${category}'?`,
                showCancelButton: true,
                confirmButtonText: 'Yes, Upload',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    uploadFile(selectedFile, category);
                }
            });
        }

        function uploadFile(file, category) {
            Swal.fire({
                title: 'Uploading File...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    const formData = new FormData();
                    formData.append('pdfFile', file);
                    formData.append('category', category);

                    fetch('', {
                        method: 'POST',
                        body: formData,
                    }).then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'File Uploaded!',
                                text: data.fileName,
                            }).then(() => {
                                addFileToContainer(data.fileName, data.filePath, data.fileId);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: data.message,
                            });
                        }
                    }).catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!',
                        });
                    });
                },
            });
        }

        function addFileToContainer(fileName, filePath, fileId) {
            const contentContainer = document.querySelector(".content-container");
            const fileElement = document.createElement("div");
            fileElement.classList.add("content-item");
            fileElement.id = "file_" + fileId;
            const fileDetails = document.createElement("div");
            fileDetails.classList.add("content-details");
            const link = document.createElement("a");
            link.href = filePath;
            link.target = "_blank";
            link.innerText = fileName;
            fileDetails.appendChild(link);
            const deleteButton = document.createElement("button");
            deleteButton.classList.add("delete-button");
            deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
            deleteButton.onclick = function() {
                deleteFile(fileId);
            };
            fileDetails.appendChild(deleteButton);
            fileElement.appendChild(fileDetails);
            contentContainer.appendChild(fileElement);
        }
		function logout() {
    var userId = <?php echo json_encode($user_id); ?>;
    var recentlyOpenedKey = 'recentlyOpenedFiles_' + userId;
    localStorage.removeItem(recentlyOpenedKey);

    window.location.href = 'login.php';
}

		
		
		
		
    </script>
</body>
</html>