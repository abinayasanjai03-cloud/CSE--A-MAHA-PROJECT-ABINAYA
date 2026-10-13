<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$company_query = mysqli_query(
    $conn,
    "SELECT id, company_name
     FROM companies
     WHERE user_id='$user_id'"
);

if (!$company_query || mysqli_num_rows($company_query) == 0) {
    die("Company profile not found.");
}

$company = mysqli_fetch_assoc($company_query);
$company_id = $company['id'];
$company_name = $company['company_name'];

$filter = "";

if (isset($_GET['internship_id']) && intval($_GET['internship_id']) > 0) {
    $internship_id = intval($_GET['internship_id']);

    $filter = "AND internships.id='$internship_id'";
}

$sql = "
    SELECT
        applications.id AS application_id,
        applications.status,
        applications.applied_at,

        users.name AS student_name,
        users.email AS student_email,

        students.college,
        students.department,
        students.phone,
        students.skills,
        students.resume,

        internships.id AS internship_id,
        internships.title AS internship_title,
        internships.location,
        internships.stipend

    FROM applications

    JOIN students
        ON applications.student_id = students.id

    JOIN users
        ON students.user_id = users.id

    JOIN internships
        ON applications.internship_id = internships.id

    WHERE internships.company_id='$company_id'
    $filter

    ORDER BY applications.applied_at DESC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database error: " . mysqli_error($conn));
}

$applications = [];

while ($row = mysqli_fetch_assoc($result)) {
    $applications[] = $row;
}

function statusClass($status)
{
    switch ($status) {
        case "Applied":
            return "applied";

        case "Under Review":
            return "review";

        case "Shortlisted":
            return "shortlisted";

        case "Interview":
            return "interview";

        case "Selected":
            return "selected";

        case "Rejected":
            return "rejected";

        default:
            return "";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Applicants | InternLink</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            color: #dbeafe;
            background:
                linear-gradient(
                    135deg,
                    #0f172a,
                    #1e293b,
                    #2563eb
                );
            padding-bottom: 50px;
        }

        .navbar {
            height: 75px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 7%;
            background: rgba(15, 23, 42, 0.72);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            color: white;
            text-decoration: none;
            font-size: 26px;
            font-weight: 700;
        }

        .logo span {
            color: #38bdf8;
        }

        .nav-links {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .nav-links a {
            color: #dbeafe;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 10px;
            transition: 0.3s;
        }

        .nav-links a:hover {
            color: #38bdf8;
            background: rgba(56,189,248,0.1);
        }

        .logout {
            border: 1px solid rgba(248,113,113,0.35);
        }

        .logout:hover {
            color: #f87171 !important;
            background: rgba(248,113,113,0.1) !important;
        }

        .container {
            width: 88%;
            max-width: 1250px;
            margin: 45px auto;
        }

        .header {
            margin-bottom: 30px;
        }

        .header h1 {
            color: white;
            font-size: 34px;
            margin-bottom: 8px;
        }

        .header p {
            color: #94a3b8;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 18px;
            color: #7dd3fc;
            text-decoration: none;
            font-size: 14px;
        }

        .back-btn:hover {
            color: #38bdf8;
        }

        .empty {
            text-align: center;
            padding: 70px 20px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            backdrop-filter: blur(15px);
        }

        .empty i {
            font-size: 55px;
            color: #38bdf8;
            margin-bottom: 20px;
        }

        .empty h2 {
            color: white;
            margin-bottom: 10px;
        }

        .empty p {
            color: #94a3b8;
        }

        .applicant-list {
            display: grid;
            gap: 22px;
        }

        .applicant-card {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 25px;
            backdrop-filter: blur(15px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            transition: 0.3s;
        }

        .applicant-card:hover {
            border-color: rgba(56,189,248,0.3);
            transform: translateY(-2px);
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 22px;
        }

        .student-info {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .avatar {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(56,189,248,0.14);
            color: #38bdf8;
            font-size: 22px;
        }

        .student-info h2 {
            color: white;
            font-size: 21px;
            margin-bottom: 5px;
        }

        .student-info p {
            color: #94a3b8;
            font-size: 14px;
        }

        .status {
            padding: 8px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }

        .status.applied {
            background: rgba(59,130,246,0.12);
            color: #93c5fd;
        }

        .status.review {
            background: rgba(234,179,8,0.12);
            color: #fde047;
        }

        .status.shortlisted {
            background: rgba(56,189,248,0.12);
            color: #7dd3fc;
        }

        .status.interview {
            background: rgba(168,85,247,0.12);
            color: #d8b4fe;
        }

        .status.selected {
            background: rgba(34,197,94,0.12);
            color: #86efac;
        }

        .status.rejected {
            background: rgba(239,68,68,0.12);
            color: #fca5a5;
        }

        .internship-box {
            padding: 15px;
            border-radius: 13px;
            background: rgba(15,23,42,0.35);
            border: 1px solid rgba(255,255,255,0.06);
            margin-bottom: 20px;
        }

        .internship-box h3 {
            color: #7dd3fc;
            font-size: 16px;
            margin-bottom: 8px;
        }

        .internship-details {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            color: #94a3b8;
            font-size: 13px;
        }

        .internship-details span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 20px;
        }

        .detail {
            background: rgba(15,23,42,0.28);
            padding: 13px;
            border-radius: 11px;
        }

        .detail-label {
            display: block;
            color: #64748b;
            font-size: 11px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .detail-value {
            color: #dbeafe;
            font-size: 13px;
            word-break: break-word;
        }

        .skills {
            margin-bottom: 20px;
        }

        .skills-label {
            color: #64748b;
            font-size: 11px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .skills-value {
            color: #bae6fd;
            font-size: 14px;
        }

        .bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            border-top: 1px solid rgba(255,255,255,0.08);
            padding-top: 18px;
        }

        .applied-date {
            color: #64748b;
            font-size: 12px;
        }

        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .action {
            padding: 9px 13px;
            border-radius: 9px;
            text-decoration: none;
            font-size: 13px;
            transition: 0.3s;
        }

        .review-btn {
            color: #fde047;
            background: rgba(234,179,8,0.08);
            border: 1px solid rgba(234,179,8,0.2);
        }

        .shortlist-btn {
            color: #7dd3fc;
            background: rgba(56,189,248,0.08);
            border: 1px solid rgba(56,189,248,0.2);
        }

        .interview-btn {
            color: #d8b4fe;
            background: rgba(168,85,247,0.08);
            border: 1px solid rgba(168,85,247,0.2);
        }

        .select-btn {
            color: #86efac;
            background: rgba(34,197,94,0.08);
            border: 1px solid rgba(34,197,94,0.2);
        }

        .reject-btn {
            color: #fca5a5;
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.2);
        }

        .action:hover {
            transform: translateY(-2px);
        }

        .resume-btn {
            color: #dbeafe;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
        }

        @media (max-width: 800px) {

            .navbar {
                padding: 0 5%;
            }

            .nav-links a:not(.logout) {
                display: none;
            }

            .container {
                width: 92%;
                margin-top: 30px;
            }

            .top {
                flex-direction: column;
            }

            .details-grid {
                grid-template-columns: 1fr;
            }

            .bottom {
                flex-direction: column;
                align-items: flex-start;
            }

            .actions {
                width: 100%;
            }
        }

    </style>

</head>

<body>

<nav class="navbar">

    <a href="dashboard.php" class="logo">
        Intern<span>Link</span>
    </a>

    <div class="nav-links">

        <a href="dashboard.php">
            <i class="fa-solid fa-chart-line"></i>
            Dashboard
        </a>

        <a href="internships.php">
            <i class="fa-solid fa-briefcase"></i>
            Internships
        </a>

        <a href="add_internship.php">
            <i class="fa-solid fa-plus"></i>
            Add Internship
        </a>

        <a href="../logout.php" class="logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
        </a>

    </div>

</nav>

<div class="container">

    <div class="header">

        <h1>
            <i class="fa-solid fa-users"></i>
            Applicants
        </h1>

        <p>
            View and manage students who applied for your internships.
        </p>

        <?php if (isset($_GET['internship_id'])): ?>

            <a href="internships.php" class="back-btn">
                <i class="fa-solid fa-arrow-left"></i>
                Back to My Internships
            </a>

        <?php endif; ?>

    </div>

    <?php if (count($applications) == 0): ?>

        <div class="empty">

            <i class="fa-solid fa-user-group"></i>

            <h2>No Applicants Yet</h2>

            <p>
                Students who apply for your internships will appear here.
            </p>

        </div>

    <?php else: ?>

        <div class="applicant-list">

            <?php foreach ($applications as $row): ?>

                <div class="applicant-card">

                    <div class="top">

                        <div class="student-info">

                            <div class="avatar">
                                <i class="fa-solid fa-user"></i>
                            </div>

                            <div>
                                <h2>
                                    <?php echo htmlspecialchars($row['student_name']); ?>
                                </h2>

                                <p>
                                    <?php echo htmlspecialchars($row['student_email']); ?>
                                </p>
                            </div>

                        </div>

                        <div class="status <?php echo statusClass($row['status']); ?>">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </div>

                    </div>

                    <div class="internship-box">

                        <h3>
                            <i class="fa-solid fa-briefcase"></i>
                            <?php echo htmlspecialchars($row['internship_title']); ?>
                        </h3>

                        <div class="internship-details">

                            <span>
                                <i class="fa-solid fa-location-dot"></i>
                                <?php echo htmlspecialchars($row['location'] ?? 'Not specified'); ?>
                            </span>

                            <span>
                                <i class="fa-solid fa-money-bill"></i>
                                <?php echo htmlspecialchars($row['stipend'] ?? 'Not specified'); ?>
                            </span>

                        </div>

                    </div>

                    <div class="details-grid">

                        <div class="detail">

                            <span class="detail-label">
                                College
                            </span>

                            <span class="detail-value">
                                <?php echo htmlspecialchars($row['college'] ?? 'Not provided'); ?>
                            </span>

                        </div>

                        <div class="detail">

                            <span class="detail-label">
                                Department
                            </span>

                            <span class="detail-value">
                                <?php echo htmlspecialchars($row['department'] ?? 'Not provided'); ?>
                            </span>

                        </div>

                        <div class="detail">

                            <span class="detail-label">
                                Phone
                            </span>

                            <span class="detail-value">
                                <?php echo htmlspecialchars($row['phone'] ?? 'Not provided'); ?>
                            </span>

                        </div>

                    </div>

                    <div class="skills">

                        <div class="skills-label">
                            Skills
                        </div>

                        <div class="skills-value">
                            <?php echo htmlspecialchars($row['skills'] ?? 'No skills added'); ?>
                        </div>

                    </div>

                    <div class="bottom">

                        <div class="applied-date">

                            <i class="fa-regular fa-calendar"></i>

                            Applied on

                            <?php
                            echo date(
                                "d M Y, h:i A",
                                strtotime($row['applied_at'])
                            );
                            ?>

                        </div>

                        <div class="actions">

                            <?php if (!empty($row['resume'])): ?>

                            <a
                            href="download_resume.php?id=<?php echo $row['application_id']; ?>"class="action resume-btn">
                            <i class="fa-solid fa-file"></i>
                            Resume
                            </a>

                            <?php endif; ?>

                            <?php if ($row['status'] == 'Applied'): ?>

                                <a
                                    href="update_status.php?id=<?php echo $row['application_id']; ?>&status=Under%20Review"
                                    class="action review-btn"
                                >
                                    Review
                                </a>

                            <?php endif; ?>

                            <?php if ($row['status'] == 'Applied' || $row['status'] == 'Under Review'): ?>

                                <a
                                    href="update_status.php?id=<?php echo $row['application_id']; ?>&status=Shortlisted"
                                    class="action shortlist-btn"
                                >
                                    Shortlist
                                </a>

                                <a
                                    href="update_status.php?id=<?php echo $row['application_id']; ?>&status=Rejected"
                                    class="action reject-btn"
                                >
                                    Reject
                                </a>

                            <?php endif; ?>

                            <?php if ($row['status'] == 'Shortlisted'): ?>

                                <a
                                    href="update_status.php?id=<?php echo $row['application_id']; ?>&status=Interview"
                                    class="action interview-btn"
                                >
                                    Interview
                                </a>

                                <a
                                    href="update_status.php?id=<?php echo $row['application_id']; ?>&status=Rejected"
                                    class="action reject-btn"
                                >
                                    Reject
                                </a>

                            <?php endif; ?>

                            <?php if ($row['status'] == 'Interview'): ?>

                                <a
                                    href="update_status.php?id=<?php echo $row['application_id']; ?>&status=Selected"
                                    class="action select-btn"
                                >
                                    Select
                                </a>

                                <a
                                    href="update_status.php?id=<?php echo $row['application_id']; ?>&status=Rejected"
                                    class="action reject-btn"
                                >
                                    Reject
                                </a>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

</body>
</html>