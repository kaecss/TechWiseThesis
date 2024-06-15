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

$stmt = $conn->prepare("SELECT profile_pic FROM user_form WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$profilePicURL = $row['profile_pic'];

$stmt->close();
$conn->close();

function getUploadedFiles() {
    $uploadDir = "uploads/";
    $uploadedFiles = scandir($uploadDir);
    $uploadedFiles = array_diff($uploadedFiles, array('.', '..'));
    return $uploadedFiles;
}

if (isset($_GET['file'])) {
    $fileName = $_GET['file'];
    $filePath = "uploads/" . $fileName;

    if (file_exists($filePath)) {
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>';
        echo '<div id="pdfViewer" style="padding: 20px;"></div>'; // Added padding here
        
        // Notification element
        echo '<div id="notification" class="notification" style="display: none;"></div>';
        
        echo '<div id="header" class="fixed-header" style="background-color: #333; padding: 10px;">
                <a href="user-home.php"><img src="back_icon.png" alt="Back" style="width: 30px; height: 30px;"></a>';
        // Display the user's profile picture
        echo '<div style="float: right; margin-right: 20px;">';
        echo '<img src="' . $profilePicURL . '" alt="Profile Picture" style="width: 50px; height: 50px; border-radius: 50%;">';
        echo '</div>';
        // Share button
        echo '<button onclick="generateShareableLink()" class="button" style="float: right; margin-right: 10px;"><i class="fas fa-share-alt"></i> Share</button>';
        echo '<button onclick="saveAsPDF()" class="button" style="float: right;"><i class="fas fa-save"></i> Save As</button>';
        // Download button with notification
        echo '<a onclick="downloadPDF()" class="button" style="float: right; cursor: pointer;"><i class="fas fa-download"></i> Download</a>';
        echo '<button onclick="toggleCommentsPanel()" class="button" style="float: right;"><i class="fas fa-comments"></i> Comments</button>';
        echo '</div>';
        echo '<script>
            pdfjsLib.getDocument("' . $filePath . '").promise.then(function(pdf) {
                pdf.getPage(1).then(function(page) {
                    var scale = 1.5;
                    var viewport = page.getViewport({ scale: scale });

                    var canvas = document.createElement("canvas");
                    var context = canvas.getContext("2d");
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    var renderContext = {
                        canvasContext: context,
                        viewport: viewport
                    };
                    page.render(renderContext).promise.then(function() {
                        document.getElementById("pdfViewer").appendChild(canvas);
                    });
                });
            });
            
            function saveAsPDF() {
                // Implement logic to save PDF
            }
            
            function downloadPDF() {
                document.getElementById("notification").textContent = "File download started!";
                document.getElementById("notification").style.display = "block";
                setTimeout(function() {
                    document.getElementById("notification").style.display = "none";
                }, 3000);
            }

            function toggleCommentsPanel() {
                var commentsPanel = document.getElementById("commentsPanel");
                commentsPanel.classList.toggle("show");
            }

            function generateShareableLink() {
                var baseUrl = window.location.href.split("?")[0];
                var shareableLink = baseUrl + "?file=' . $fileName . '";
                prompt("Copy this link to share:", shareableLink);
            }
        </script>';

        // Add HTML code for comments panel
        echo '<div id="commentsPanel" class="commentsPanel">
                <h2>Comments</h2>
                <textarea id="commentText" rows="4" cols="30"></textarea>
                <button onclick="saveComment()">Add Comment</button>
                <ul id="commentList"></ul>
              </div>';

        // JavaScript code for saving comments
        echo '<script>
            function saveComment() {
                var commentText = document.getElementById("commentText").value;
                var commentList = document.getElementById("commentList");
                var listItem = document.createElement("li");
                listItem.textContent = commentText;
                commentList.appendChild(listItem);
                document.getElementById("commentText").value = "";
            }
        </script>';

        // CSS for positioning
        echo '<style>
            #header {
                width: 100%;
                color: white;
            }

            .fixed-header {
                position: fixed;
                top: 0;
                left: 0;
                z-index: 1000;
            }
            
            .button {
                background-color: #4CAF50; /* Green */
                border: none;
                color: white;
                padding: 10px 20px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                margin: 4px 2px;
                transition-duration: 0.4s;
                cursor: pointer;
                border-radius: 4px;
            }
            
            .button:hover {
                background-color: #45a049;
            }

            /* Style the comments panel */
            .commentsPanel {
                position: fixed;
                right: 0;
                top: 70px;
                height: 100%;
                width: 0;
                z-index: 1;
                background-color: #f1f1f1;
                overflow-x: hidden;
                padding-top: 60px;
                transition: 0.5s;
            }

            .commentsPanel.show {
                width: 250px;
            }

            /* Notification style */
            .notification {
                position: fixed;
                top: 0;
                left: 50%;
                transform: translateX(-50%);
                background-color: #4CAF50;
                color: white;
                padding: 10px 20px;
                font-size: 16px;
                z-index: 1000;
            }
        </style>';
    } else {
        echo "File not found.";
    }
} else {
}
?>
