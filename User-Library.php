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
$profilePic = $row['profile_pic'] ? $row['profile_pic'] : 'image/default-profile.jpg';
$stmt->close();

date_default_timezone_set('UTC');

function getAllUploadedFiles($conn) {
    $uploadedFiles = array();
    $query = "SELECT uf.file_id, uf.file_name, uf.file_path, uf.file_category, uf.upload_date, uf.user_id, u.username AS uploader_username, u.profile_pic AS uploader_profile_pic
              FROM user_files uf 
              JOIN user_form u ON uf.user_id = u.id
              ORDER BY uf.upload_date DESC"; 

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $uploadTime = strtotime($row['upload_date']);
            $currentTime = time();
            $timeDifference = $currentTime - $uploadTime;

            if ($timeDifference < 60) {
                $timeAgo = ($timeDifference > 0 ? $timeDifference : 1) . "s ago";
            } elseif ($timeDifference < 3600) {
                $minutes = round($timeDifference / 60);
                $timeAgo = ($minutes > 0 ? $minutes : 1) . "m ago";
            } else {
                $hours = round($timeDifference / 3600);
                $timeAgo = ($hours > 0 ? $hours : 1) . "h ago";
            }

            $row['time_ago'] = $timeAgo;
            $uploadedFiles[] = $row;
        }
    }

    return $uploadedFiles;
}

function getCategories($conn) {
    $categories = array();
    $query = "SELECT DISTINCT file_category FROM user_files";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row['file_category'];
        }
    }

    return $categories;
}

$uploadedFiles = getAllUploadedFiles($conn);
$categories = getCategories($conn);

$conn->close();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User-Library</title>
    <link rel="stylesheet" href="User_library.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
</head>
<body>
    <div class="side-panel">
        <div class="company-name">
            <a href="#">
                <img src="image/logo2.png" alt="Company Logo" class="company-logo">
            </a>
            <!-- <h2>TechWiseThesis</h2> -->
        </div>
        <div class="user-info"> 
            <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile" class="user-icon" id="userIcon">
            <span class="username"><?php echo htmlspecialchars($username); ?></span>
        </div>
        <ul>
            <li><a href="user_page.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="#" class="active"><i class="fas fa-book"></i> Library</a></li>
            <li><a href="user-acc.php"><i class="fas fa-cog"></i> Account Settings</a></li>
            <hr>
            <li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Sign Out</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <span class="library-label">Library</span>
            <div class="search-bar">
                <input type="text" id="searchInput" class="search-input" placeholder="Search...">
                <select id="categoryDropdown" class="category-dropdown">
					<option value="">All Categories</option>
					<option value="Science">Scientific Studies</option>
					<option value="Business">Market Research</option>
					<option value="Literature">Literature Reviews</option>
					<option value="Engineering">Engineering Analysis</option> 
					<option value="Others">Others</option>
					<?php foreach ($categories as $category) : ?>
						<option value="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></option>
					<?php endforeach; ?>
				</select>
                <button id="searchButton" class="search-button"><i class="fas fa-search"></i></button>
            </div>
        </div>
        <div class="sort-dropdown">
            <label for="sort-by" class="sort-label">Sort by:</label>
            <select id="sort-by">
	<option value="title">Title (A-Z)</option>
    <option value="time">Time</option>
</select>

        </div>
        <div class="content-container">
            <?php foreach ($uploadedFiles as $file) : ?>
                <div class="content-item" id="file_<?php echo $file['file_id']; ?>" data-category="<?php echo htmlspecialchars($file['file_category']); ?>">
                    <div class="content-details">
                        <div class="epilogue-title">
                        <a href="view.php?file_id=<?php echo $file['file_id']; ?>" class="file-name">
                            <?php echo pathinfo($file['file_name'], PATHINFO_FILENAME); ?>
                        </a>
                        </div>
                        <div class="file-category">
                            <span class="file-category">Category: <?php echo $file['file_category']; ?></span>
                        </div>
                        <p class="card-text">
                            <span class="badge badge-info">100 views</span>
                            <span class="badge badge-success">100 likes</span>
                            <span class="badge badge-warning">20 comments</span>
                        </p>
                        <div class="author-profile d-flex align-items-center">
                            <img src="<?php echo htmlspecialchars($file['uploader_profile_pic']); ?>" alt="Uploader Profile Picture" class="profile-pic" width="30" height="30">
                            <div class="author-info">
                                <div>
                                    <span class="card-text1"><?php echo htmlspecialchars($file['uploader_username']); ?></span>
                                </div>
                                <div>
                                    <span class="card-text2">Posted <?php echo $file['time_ago']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
			
			
			<div class="content-item">
			    <a href="thesis3.html"><img src="image/t3.png" alt="Photo Title"></a>
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
			   <a href="thesis4.html"><img src="image/t4.png" alt="Photo Title"></a>
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
			<a href="thesis5.html"><img src="image/t5.png" alt="Photo Title"></a>
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
			 <a href="thesis6.html"><img src="image/b3.png" alt="Photo Title"></a>
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
			 <a href="thesis6.html"><img src="image/b3.png" alt="Photo Title"></a>
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
        <div class="no-results-container">
            <p class="no-results-message">No files match your search.</p>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchButton = document.getElementById('searchButton');
            const searchInput = document.getElementById('searchInput');
            const categoryDropdown = document.getElementById('categoryDropdown');
            const contentItems = document.querySelectorAll('.content-item');
            const noResultsContainer = document.querySelector('.no-results-container');
            const sortDropdown = document.getElementById('sort-by');

            searchButton.addEventListener('click', filterContent);
            searchInput.addEventListener('input',filterContent );
            categoryDropdown.addEventListener('change', filterContent);
            categoryDropdown.addEventListener('input', filterContent); 

            sortDropdown.addEventListener('change', sortContent);
			
            function filterContent() {
                const searchTerm = searchInput.value.trim().toLowerCase();
                const selectedCategory = categoryDropdown.value.toLowerCase();

                contentItems.forEach(item => {
                    const title = item.querySelector('.file-name').textContent.toLowerCase();
                    const category = item.dataset.category.toLowerCase();
                    
                    const titleMatches = title.includes(searchTerm);
                    const categoryMatches = selectedCategory === '' || category.includes(selectedCategory);

                    if (titleMatches && categoryMatches) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });

                const anyMatchFound = Array.from(contentItems).some(item => {
                    return item.style.display === 'flex';
                });

                noResultsContainer.style.display = anyMatchFound ? 'none' : 'block';
            }

            function sortContent() {
                const sortBy = sortDropdown.value;

                if (sortBy === 'title') {
                    sortItemsByTitle();
                } else if (sortBy === 'time') {
                    sortItemsByTime();
                }
            }

          function sortItemsByTitle() {
    const sortedItems = Array.from(contentItems).sort((a, b) => {
        const titleA = a.querySelector('.file-name').textContent.trim().toLowerCase();
        const titleB = b.querySelector('.file-name').textContent.trim().toLowerCase();
        
        // Compare based on the first letter of the title
        const firstLetterA = titleA.charAt(0);
        const firstLetterB = titleB.charAt(0);
        
        return firstLetterA.localeCompare(firstLetterB);
    });

    updateContent(sortedItems);
}


            function sortItemsByTime() {
                const sortedItems = Array.from(contentItems).sort((a, b) => {
                    const timeA = getTimeInHours(a.querySelector('.card-text2').textContent);
                    const timeB = getTimeInHours(b.querySelector('.card-text2').textContent);
                    return timeA - timeB;
                });

                updateContent(sortedItems);
            }

            function getTimeInHours(timeText) {
                const hoursIndex = timeText.indexOf('h');
                return parseInt(timeText.slice(7, hoursIndex).trim());
            }

            function updateContent(sortedItems) {
                contentItems.forEach(item => {
                    item.parentNode.removeChild(item);
                });

                sortedItems.forEach(item => {
                    document.querySelector('.content-container').appendChild(item);
                });
            }
        });
    </script>
</body>
</html>
