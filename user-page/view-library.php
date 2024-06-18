<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$file_id = $_GET['file_id'];

$conn = new mysqli("localhost", "root", "", "techwisethesis");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT file_name, file_path FROM user_files WHERE file_id = ?");
$stmt->bind_param("i", $file_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $file = $result->fetch_assoc();
    $file_name = $file['file_name'];
    $file_path = $file['file_path'];
} else {
    echo "File not found.";
    exit();
}

$stmt->close();

$stmt_comments = $conn->prepare("
    SELECT fc.comment, u.username, u.profile_pic
    FROM file_comments fc
    JOIN user_form u ON fc.user_id = u.id
    WHERE fc.file_id = ?
");
$stmt_comments->bind_param("i", $file_id);
$stmt_comments->execute();
$result_comments = $stmt_comments->get_result();

$comments = [];
while ($row = $result_comments->fetch_assoc()) {
    $comment = htmlspecialchars($row['comment']);
    $username = htmlspecialchars($row['username']);
	    $profile_pic = htmlspecialchars($row['profile_pic']);
    $comments[] = [
        'comment' => $comment,
        'username' => $username,
		  'profile_pic' => $profile_pic
    ];
}

$stmt_comments->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
<title>View the file</title>
<style>
html, body {
    font-family: 'Times New Roman', Times, serif;
    margin: 0;
    padding: 0;
    height: 100%;
    display: flex;
    flex-direction: column;
}
::-webkit-scrollbar {
   display: none;
}

header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background-color: #734122;
    color: #ffffff;
    padding: 10px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    z-index: 1000; 
}

.header-left {
    display: flex;
    align-items: center;
}

.header-left .button {
    background-color: #996633;
    color: #ffffff;
    border: none;
    padding: 8px 12px;
    margin-right: 10px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.header-left .button:hover {
    background-color: #fff;
    color: #996633;
}

.main-container {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding-top: 200px;
    padding-bottom: 20px;
    box-sizing: border-box;
	   min-height: 100vh;
    transition: margin-right 0.3s ease; 
}


.main-container.adjusted {
    margin-right: 300px; 
}

.file-paper {
    background-color: #fdfdfd;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    padding: 20px;
    width: 80%;
    max-width: 800px;
	margin-top: 50px;
    margin: 0 auto;
	height: 600px; /* Adjust height as needed */
    position: relative;
}

.main-content {
    margin-top: 60px; 
}

.file-header {
	margin-bottom: 0px;
}
.file-viewer {
	width: 100%;
	height: 100%;
	border: none;
	position: absolute;
	top: 100px;
	left: 0;
}


.button , .share-button{
    background-color: #996633;
    color: #ffffff;
    border: none;
    padding: 8px 12px;
    margin-right: 10px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.button, .share-button:hover {
    background-color: #fff;
    color: #996633;
}
.share-button{
	  margin-right: 100px;
}
.comment-button {
    background-color: #996633;
    color: #ffffff;
    border: none;
    padding: 8px 12px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.comment-button:hover {
    background-color: #fff;
    color: #996633;
}

.comment-section {
    position: fixed; 
    top: 50px; 
    right: -300px; 
    height: 100%;
    width: 300px;
    background-color: #ffffff;
    box-shadow: -2px 0 5px rgba(0, 0, 0, 0.5);
    transition: right 0.3s ease; 
    z-index: 999;
    overflow-y: auto; 
}

.comment-section.open {
    right: 0; 
}

.close-button {
    position: absolute;
    top: 10px;
    right: 10px;
    cursor: pointer;
}

.comment-input {
    width: calc(100% - 20px);
    margin: 10px;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
}

.comment-submit {
    background-color: #996633;
    color: #ffffff;
    border: none;
    padding: 8px 12px;
    margin-top: 10px;
    margin-left: 10px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.comment-submit:hover {
    background-color: #fff;
    color: #996633;
}

.comments-list {
    margin-top: 10px;
    padding: 10px;
    border-top: 1px solid #ccc;
}

.comment {
    margin-bottom: 0;
    display: flex;
    align-items: flex-start;
}

.comment .user-info {
    display: flex;
    align-items: center;
}

.comment .user-info img {
    width: 40px; 
    height: 40px; 
    border-radius: 50%; 
    margin-right: 10px;
}

.comment .username {
    font-weight: bold;
    margin-right: 10px;
}

.comment-text {
    padding: 10px;
	margin-left: 50px;
}

hr {
    border: none;
    height: 1px; 
    background-color: #ccc; 
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
    margin: 10px 0; 
}

</style>
</head>
<body>
<header>
    <div class="header-left">
        <button class="button" onclick="goBack()">
            <i class="fas fa-arrow-left"></i>
        </button>
        <button class="button" onclick="performAction('download')">
    <i class="fas fa-download"></i> Download
</button>

<button class="button" onclick="performAction('openin')">
    <i class="fas fa-folder-open"></i> Open In
</button>

<button class="button" onclick="performAction('saveas')">
    <i class="fas fa-save"></i> Save As
</button>

    </div>
    <div class="header-right">
        <button class="comment-button" onclick="toggleCommentSection()">
            <i class="fas fa-comment"></i> 
        </button>
        <button class="share-button">
            <i class="fas fa-share-alt"></i> Share
        </button>
    </div>
</header>
<div class="main-container" id="mainContainer">
    <div class="file-paper">
        <div class="file-header">
            <h2><?php echo htmlspecialchars($file_name); ?></h2>
        </div>
        <?php
        $file_extension = pathinfo($file_path, PATHINFO_EXTENSION);
        if ($file_extension == "pdf") {
            echo '<object class="file-viewer" data="' . $file_path . '" type="application/pdf"></object>';
        } elseif ($file_extension == "docx") {
            echo '<iframe class="file-viewer" src="https://docs.google.com/gview?url=' . urlencode($file_path) . '&embedded=true"></iframe>';
        } else {
            echo '<p>This file type is not supported for direct viewing.</p>';
        }
        ?>
    </div>
</div>

    <div class="comment-section" id="commentSection">
        <h2 id="fileTitle"><?php echo htmlspecialchars($file_name); ?></h2>
        <form id="commentForm">
            <input type="hidden" id="fileId" value="<?php echo $file_id; ?>">
            <input type="text" id="commentInput" class="comment-input" placeholder="Add your comment...">
            <button type="button" class="comment-submit" onclick="addComment()">Submit</button>
        </form>
        <div class="comments-list" id="commentsList">
    <?php foreach ($comments as $comment): ?>
        <div class="comment">
            <div class="user-info">
                <img src="<?php echo $comment['profile_pic']; ?>" alt="Profile Picture">
                <span class="username"><?php echo $comment['username']; ?></span>
            </div>
            
        </div>
		<div class="comment-text">
                <span class="comment-content"><?php echo $comment['comment']; ?></span>
            </div>
			<hr>
    <?php endforeach; ?>
	
</div>

        <div class="close-button" onclick="toggleCommentSection()">Close</div>
    </div>
    <script>
      function addComment() {
    var commentInput = document.getElementById('commentInput').value;
    var fileId = document.getElementById('fileId').value;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'save_comment.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = xhr.responseText;
            try {
                var responseData = JSON.parse(response);
                if (responseData.success) {
                    var newComment = document.createElement('div');
                    newComment.className = 'comment';
                    newComment.innerHTML = '<p>' + commentInput + '</p>' +
                                           '<span class="username">Comment by: <?php echo htmlspecialchars($_SESSION['username']); ?></span>';
                    document.getElementById('commentsList').appendChild(newComment);
                    document.getElementById('commentInput').value = '';
                } else {
                    alert('Failed to save comment.');
                }
            } catch (e) {
                console.error('Error parsing JSON:', e);
                alert('Failed to save comment.');
            }
        } else {
            alert('Failed to save comment.');
        }
    };
    xhr.send('file_id=' + encodeURIComponent(fileId) + '&comment=' + encodeURIComponent(commentInput));
}


        function toggleCommentSection() {
            var commentSection = document.getElementById('commentSection');
            var mainContainer = document.getElementById('mainContainer');
            commentSection.classList.toggle('open');
            mainContainer.classList.toggle('adjusted');
        }
function goBack() {
    window.location.href = 'User-Library.php';
}
</script>
</body>
</html>