<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="contact.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>
<body>
<div class="head">
  <header>
    <div class="company-name">
        <a href="#">
           <img src="image/logo2.png" alt="Company Logo" class="company-logo">
        </a>
        <!-- <h2>TechWiseThesis</h2> -->
    </div>
    <div class="register">
      <a href="login.php" >
        <i class="fas fa-user"></i>
      </a>
    </div>
  </header>

  <nav class="navigation">
      <a href="main.html">Home</a>
      <a href="Library.html">Library</a>
      <a href="login.php">Profile</a>
      <a href="main.html#our-teams">Team</a>
      <a href="#" class="active">Contact Us</a>
  </nav>
  
    <div class="main-content">
	<br>
      <h1>Contact Us</h1>
    </div>
	<div class="info">
    <div class="contact-info">
          <div class="contact-icon">
              <i class="fas fa-map-marker-alt" style="font-size: 24px;"></i>
          </div>
          <div class="contact-details">
              <p>123 Main Street, City, Country</p>
          </div>
      </div>
      <div class="contact-info">
          <div class="contact-icon">
              <i class="fas fa-phone" style="font-size: 24px;"></i>
          </div>
          <div class="contact-details">
              <p>(+1) 123 456 7890</p>
          </div>
      </div>
      <div class="contact-info">
          <div class="contact-icon">
              <i class="fas fa-envelope" style="font-size: 24px;"></i>
          </div>
          <div class="contact-details">
              <p>techwisethesis@gmail.com</p>
          </div>
      </div>
	  </div>
    </div>
	     <main>
    <div class="contact-wrapper">
      <img src="image/5.png" alt="Contact Image" class="contact-image">
    
      <div class="contact-form">
        <?php
        session_start();
        if (isset($_SESSION['email_sent'])) {
            if ($_SESSION['email_sent'] == true) {
                echo '<script>
                        window.onload = function() {
                            swal("Thank you!", "Your email was successfully sent!", "success");
                        }
                      </script>';
            } else {
                echo '<script>
                        window.onload = function() {
                            swal("Oops!", "Something went wrong. Please try again.", "error");
                        }
                      </script>';
            }
            unset($_SESSION['email_sent']);
        }
        ?>
        <form id="contact-form" method="post" action="send-email.php">
          <label for="name">Name</label>
          <input type="text" name="name" id="name" required>
          
          <label for="email">Email</label>
          <input type="email" name="email" id="email" required>
          
          <label for="subject">Subject</label>
          <input type="text" name="subject" id="subject" required>
          
          <label for="message">Message</label>
          <textarea name="message" id="message" required></textarea>
          
          <button type="submit">Send Message</button>
        </form>
      </div>
    </div>
  </main>
  
  <footer>
    <div class="container">
      <div class="footer-content">
        <div class="company-info">
          <p class="company-name">TechWiseThesis</p>
          <div class="icon-button">
            <div class="icon-text">
              <i class="fas fa-map-marker-alt"></i>
              <p>&nbsp;123 Main Street, City, Country</p>
            </div>
            <div class="icon-text">
              <i class="fas fa-phone"></i>
              <p>&nbsp;(+1) 123 456 7890</p>
            </div>
            <div class="icon-text">
              <i class="fas fa-envelope"></i>
              <p>&nbsp;techwisethesis@email.com</p>
            </div>
          </div>
        </div>
        <p class="footer-text">Discover a premier digital consumer brand offering comprehensive travel information and <br>
        inspiration. With a rich heritage of over 30 years in print and two decades online, this <br>
        platform provides accurate content on top global destinations. From cities and airports to <br>
        resorts and attractions, our dedicated editorial team updates the portal daily, attracting <br>
        millions of unique users monthly.</p>
      </div>
      <p class="rights-reserved">&copy; 2024 TechWiseThesis. All rights reserved.</p>
    </div>
  </footer>
</body>
</html>