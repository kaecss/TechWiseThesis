<?php
session_start();
require 'vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

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
    $file_name = pathinfo($file['file_name'], PATHINFO_FILENAME) . ".docx";
    $file_path = $file['file_path'];

    if (!file_exists($file_path)) {
        die("File not found on server.");
    }

    $phpWord = new PhpWord();
    $section = $phpWord->addSection();
    $section->addText(file_get_contents($file_path));
    $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save($file_name);

    // Download the generated file
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_name));
    readfile($file_name);

    // Delete the temporary file after download
    unlink($file_name);
    exit();
} else {
    echo "File not found in database.";
}

$stmt->close();
$conn->close();
?>
