

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
    padding-top: 70px;
    padding-bottom: 20px;
    box-sizing: border-box;
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
    margin: 0 auto;
}

.main-content {
    margin-top: 60px; 
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
    margin-top: 20px;
    padding: 10px;
    border-top: 1px solid #ccc;
}
.comments-list p {
    margin: 5px 0;
}
</style>
</head>
<body>
<header>
    <div class="header-left">
        <button class="button" onclick="goBack()">
            <i class="fas fa-arrow-left"></i>
        </button>
        <button class="button" id="downloadButton">
            <i class="fas fa-download"></i> Download
        </button>
        <button class="button" id="openInButton">
            <i class="fas fa-folder-open"></i> Open In
        </button>
        <button class="button" id="saveAsButton">
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
        <div class="main-content">
        </div>
    </div>
</div>

<div class="comment-section" id="commentSection">
    <h2 id="fileTitle">File Title</h2>

    <form id="commentForm">
        <input type="text" id="commentInput" class="comment-input" placeholder="Add your comment...">
        <button type="button" class="comment-submit" onclick="addComment()">Submit</button>
    </form>

    <div class="comments-list" id="commentsList">
    </div>

    <div class="close-button" onclick="toggleCommentSection()">Close</div>
</div>

<script>
function toggleCommentSection() {
    var commentSection = document.getElementById('commentSection');
    var mainContainer = document.getElementById('mainContainer');
    commentSection.classList.toggle('open');
    mainContainer.classList.toggle('adjusted');
}

function addComment() {
    var commentInput = document.getElementById('commentInput').value;
    var commentsList = document.getElementById('commentsList');

    var newComment = document.createElement('p');
    newComment.textContent = commentInput;
    commentsList.appendChild(newComment);

    document.getElementById('commentInput').value = '';
}

document.getElementById('downloadButton').addEventListener('click', function() {
  
    var blob = new Blob(['File content or URL here'], { type: 'text/plain' });
    var url = URL.createObjectURL(blob);

    var a = document.createElement('a');
    a.href = url;
    a.download = 'example.txt';
    document.body.appendChild(a);
    a.click();

    setTimeout(function() {
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }, 0);
});

document.getElementById('openInButton').addEventListener('click', function() {
    alert('Open In action clicked');
});

document.getElementById('saveAsButton').addEventListener('click', function() {
    alert('Save As action clicked');
});

function setFileTitle(title) {
    document.getElementById('fileTitle').textContent = title;
}

setFileTitle("Sample File Name"); 

function goBack() {
    window.location.href = 'User-Home.php';
}




</script>

</body>
</html>
