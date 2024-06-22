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
    $file_name = pathinfo($file['file_name'], PATHINFO_FILENAME) . ".pdf";
    $file_path = $file['file_path'];

    // Use a library like Dompdf to convert the file to PDF
    require 'vendor/autoload.php';
    use Dompdf\Dompdf;

    $dompdf = new Dompdf();
    $dompdf->loadHtml(file_get_contents($file_path));
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $output = $dompdf->output();
    file_put_contents($file_name, $output);

    header('Content-Description: File Transfer');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_name));
    readfile($file_name);
    unlink($file_name); // Delete the file after download
    exit();
} else {
    echo "File not found.";
    exit();
}

$stmt->close();
$conn->close();
?>
