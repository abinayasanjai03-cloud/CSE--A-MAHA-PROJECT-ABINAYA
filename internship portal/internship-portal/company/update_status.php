<?php

session_start();

include("../config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    header("Location: applicants.php");
    exit();
}

$application_id = intval($_GET['id']);
$status = $_GET['status'];

$allowed_statuses = [
    "Applied",
    "Under Review",
    "Shortlisted",
    "Interview",
    "Selected",
    "Rejected"
];

if (!in_array($status, $allowed_statuses)) {
    header("Location: applicants.php");
    exit();
}

$company_query = mysqli_query(
    $conn,
    "SELECT id
     FROM companies
     WHERE user_id='$user_id'"
);

if (!$company_query || mysqli_num_rows($company_query) == 0) {
    die("Company profile not found.");
}

$company = mysqli_fetch_assoc($company_query);
$company_id = $company['id'];

$check_query = mysqli_query(
    $conn,
    "SELECT applications.id
     FROM applications
     JOIN internships
     ON applications.internship_id = internships.id
     WHERE applications.id='$application_id'
     AND internships.company_id='$company_id'"
);

if (!$check_query || mysqli_num_rows($check_query) == 0) {
    die("You are not authorized to update this application.");
}

$status_safe = mysqli_real_escape_string($conn, $status);

$update = mysqli_query(
    $conn,
    "UPDATE applications
     SET status='$status_safe'
     WHERE id='$application_id'"
);

if (!$update) {
    die("Unable to update application status.");
}

header("Location: applicants.php");
exit();

?>