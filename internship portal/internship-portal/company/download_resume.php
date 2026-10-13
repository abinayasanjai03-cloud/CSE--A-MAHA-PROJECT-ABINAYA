<?php

session_start();
include("../config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid application.");
}

$application_id = intval($_GET['id']);

$company_query = mysqli_query(
    $conn,
    "SELECT id
     FROM companies
     WHERE user_id='$user_id'
     LIMIT 1"
);

if (!$company_query || mysqli_num_rows($company_query) == 0) {
    die("Company profile not found.");
}

$company = mysqli_fetch_assoc($company_query);
$company_id = $company['id'];

$query = mysqli_query(
    $conn,
    "SELECT students.resume
     FROM applications
     JOIN students
        ON applications.student_id = students.id
     JOIN internships
        ON applications.internship_id = internships.id
     WHERE applications.id='$application_id'
     AND internships.company_id='$company_id'
     LIMIT 1"
);

if (!$query || mysqli_num_rows($query) == 0) {
    die("Resume not found or you are not authorized to access it.");
}

$row = mysqli_fetch_assoc($query);

if (empty($row['resume'])) {
    die("This student has not uploaded a resume.");
}

$filename = basename($row['resume']);
$file = __DIR__ . "/../uploads/" . $filename;

if (!file_exists($file)) {
    die("Resume file does not exist on the server.");
}

$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

$mime_types = [
    "pdf"  => "application/pdf",
    "doc"  => "application/msword",
    "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
];

$content_type = $mime_types[$extension] ?? "application/octet-stream";

header("Content-Type: " . $content_type);
header("Content-Length: " . filesize($file));
header(
    'Content-Disposition: attachment; filename="' .
    basename($filename) .
    '"'
);

readfile($file);
exit();

?>