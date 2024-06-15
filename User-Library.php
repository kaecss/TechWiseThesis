<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

$conn = new mysqli("localhost", "root", "", "techwisethesis");

if ($conn->connect_error) {
    die("Connection failed: s" . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT profile_pic FROM user_form WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$profilePic = $row['profile_pic'] ? $row['profile_pic'] : 'image/default-profile.jpg';

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User-Library</title>
    <link rel="stylesheet" href="user-library.css">
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
			<li><a href="user-acc.php"><i class="fas fa-cog"></i> Account Settings</a></li>
			<li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Sign Out</a></li>
			<hr>
			<li><a href="#" class="active"><i class="fas fa-book"></i> Library</a></li>
		</ul>
	</div>

    <div class="main-content">
       <div class="header">
			<span class="library-label">Library</span>
			<div class="search-bar">
				<input type="text" id="searchInput" placeholder="Search...">
                <button onclick="searchFiles()"><i class="fas fa-search"></i></button>
           
			</div>
		</div>
		<div class="sort-dropdown">
    <label for="sort-by" class="sort-label">Sort by:</label>
    <select id="sort-by">
        <option value="date">Date</option>
        <option value="time">Time</option>
    </select>
</div>

        <div class="content-container">
             <div class="content-item">
                <a href="thesis1.html"><img src="image/t1.png" alt="Photo Title"></a>
                <div class="content-details">
                    <a href="thesis1.html" class="epilogue-title">Factors Concerning Animal Growth</a>
                    <p class="card-text">
                        <span class="badge badge-info">100 views</span>
                        <span class="badge badge-success">100 likes</span>
                        <span class="badge badge-warning">20 comments</span>
                    </p>
                    <div class="author-profile d-flex align-items-center">
                        <a href="thesis1.html"><img src="image/p1.jpg" alt="Author" width="40" height="40"></a>
                        <div class="author-info">
                            <div>
                                <p class="card-text1"><a href="thesis1.html">Vince Datu</a></p>
                            </div>
                            <div>
                                <p class="card-text2">Posted 1h ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			
			
			  <div class="content-item">
                <a href="thesis2.html"><img src="image/t2.png" alt="Photo Title"></a>
                <div class="content-details">
                    <a href="thesis2.html" class="epilogue-title">Are Technology Improvements Contractionary?</a>
                    <p class="card-text">
                        <span class="badge badge-info">200 views</span>
                        <span class="badge badge-success">500 likes</span>
                        <span class="badge badge-warning">50 comments</span>
                    </p>
                    <div class="author-profile d-flex align-items-center">
                        <a href="thesis2.html"><img src="image/p2.jpg" alt="Author" width="40" height="40"></a>
                        <div class="author-info">
                            <div>
                                <p class="card-text1"><a href="thesis2.html">Sharla Sonaliza</a></p>
                            </div>
                            <div>
                                <p class="card-text2">Posted 11h ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
			 <div class="content-item">
                <a href="thesis3.html"><img src="image/t3.png" alt="Photo Title"></a>
                <div class="content-details">
                    <a href="thesis3.html" class="epilogue-title">"Economic Impacts of Artificial Intelligence: An In-depth Analysis"</a>
                    <p class="card-text">
                        <span class="badge badge-info">150 views</span>
                        <span class="badge badge-success">250 likes</span>
                        <span class="badge badge-warning">70 comments</span>
                    </p>
                    <div class="author-profile d-flex align-items-center">
                        <a href="thesis3.html"><img src="image/p3.jpg" alt="Author" width="40" height="40"></a>
                        <div class="author-info">
                            <div>
                                <p class="card-text1"><a href="thesis3.html">Myles Maralit</a></p>
                            </div>
                            <div>
                                <p class="card-text2">Posted 7h ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        

            <div class="content-item">
                <a href="thesis4.html"><img src="image/t4.png" alt="Photo Title"></a>
                <div class="content-details">
                    <a href="thesis4.html" class="epilogue-title">“Exploring its Socio-Economic Impact and Environmental Implications"</a>
                    <p class="card-text">
                        <span class="badge badge-info">300 views</span>
                        <span class="badge badge-success">300 likes</span>
                        <span class="badge badge-warning">80 comments</span>
                    </p>
                    <div class="author-profile d-flex align-items-center">
                        <a href="thesis4.html"><img src="image/p4.jpg" class="rounded-circle" alt="Author" width="40" height="40"></a>
                        <div class="author-info">
                            <div>
                                <p class="card-text1"><a href="thesis4.html">Harvey Valentin</a></p>
                            </div>
                            <div>
                                <p class="card-text2">Posted 6h ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        

            <div class="content-item">
                <a href="thesis5.html"><img src="image/t5.png" alt="Photo Title"></a>
                <div class="content-details">
                    <a href="thesis5.html" class="epilogue-title">"Redefining Education: Exploring Innovations, Equity, and Economic Impact"</a>
                    <p class="card-text">
                        <span class="badge badge-info">400 views</span>
                        <span class="badge badge-success">200 likes</span>
                        <span class="badge badge-warning">40 comments</span>
                    </p>
                    <div class="author-profile d-flex align-items-center">
                        <a href="thesis5.html"><img src="image/p5.jpg" class="rounded-circle" alt="Author" width="40" height="40"></a>
                        <div class="author-info">
                            <div>
                                <p class="card-text1"><a href="thesis5.html">Geraldine Valdez</a></p>
                            </div>
                            <div>
                                <p class="card-text2">Posted 10h ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        

            <div class="content-item">
                <a href="thesis6.html"><img src="imaeg/b3.png" alt="Photo Title"></a>
                <div class="content-details">
                    <a href="thesis6.html" class="epilogue-title">Factors Concerning Animal Growth</a>
                    <p class="card-text">
                        <span class="badge badge-info">500 views</span>
                        <span class="badge badge-success">600 likes</span>
                        <span class="badge badge-warning">70 comments</span>
                    </p>
                    <div class="author-profile d-flex align-items-center">
                        <a href="thesis6.html"><img src="image/p6.jpg" class="rounded-circle" alt="Author" width="40" height="40"></a>
                        <div class="author-info">
                            <div>
                                <p class="card-text1"><a href="thesis6.html">Aethel Mae Udtuhan</a></p>
                            </div>
                            <div>
                                <p class="card-text2">Posted 5h ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
	<script>
        document.addEventListener("DOMContentLoaded", function () {
            attachSearchListener();
        });

        function attachSearchListener() {
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', function() {
                searchFiles();
            });
        }

        function searchFiles() {
            var input, filter, contentItems, i, txtValue;
            input = document.getElementById('searchInput');
            filter = input.value.toUpperCase();
            contentItems = document.querySelectorAll('.content-item');
            for (i = 0; i < contentItems.length; i++) {
                txtValue = contentItems[i].textContent || contentItems[i].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    contentItems[i].style.display = "";
                } else {
                    contentItems[i].style.display = "none";
                }
            }
        }
    </script>
</body>
</html>
