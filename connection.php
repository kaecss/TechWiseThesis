<?php
// connection.php
$con = mysqli_connect('localhost', 'root', '', 'techwisethesis');

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
