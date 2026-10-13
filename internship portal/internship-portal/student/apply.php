<?php

session_start();
include("../config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: internships.php");
    exit();
}

$internship_id = intval($_GET['id']);

$message = "";
$message_type = "";
$application_submitted = false;

$internship_query = mysqli_query(
    $conn,
    "SELECT
        internships.id,
        internships.title,
        internships.description,
        internships.skills_required,
        internships.location,
        internships.stipend,
        internships.duration,
        internships.deadline,
        internships.created_at,
        companies.company_name
     FROM internships
     JOIN companies
        ON internships.company_id = companies.id
     WHERE internships.id='$internship_id'
     LIMIT 1"
);

if (!$internship_query || mysqli_num_rows($internship_query) == 0) {
    die("Internship not found.");
}

$internship = mysqli_fetch_assoc($internship_query);

$student_query = mysqli_query(
    $conn,
    "SELECT id, college, department, phone, skills, resume
     FROM students
     WHERE user_id='$user_id'
     LIMIT 1"
);

$student = null;

if ($student_query && mysqli_num_rows($student_query) > 0) {
    $student = mysqli_fetch_assoc($student_query);
}

if (isset($_POST['apply'])) {

    if (!$student) {

        $message = "Please complete your student profile before applying.";
        $message_type = "warning";

    } else {

        $student_id = $student['id'];

        $existing_query = mysqli_query(
            $conn,
            "SELECT id, status
             FROM applications
             WHERE internship_id='$internship_id'
             AND student_id='$student_id'
             LIMIT 1"
        );

        if ($existing_query && mysqli_num_rows($existing_query) > 0) {

            $existing = mysqli_fetch_assoc($existing_query);

            $message = "You have already applied for this internship.";
            $message_type = "info";

        } else {

            $insert = mysqli_query(
                $conn,
                "INSERT INTO applications
                (internship_id, student_id, status)
                VALUES
                ('$internship_id', '$student_id', 'Applied')"
            );

            if ($insert) {

                $application_submitted = true;
                $message = "Your application has been submitted successfully.";
                $message_type = "success";

            } else {

                $message = "Unable to submit your application. Please try again.";
                $message_type = "error";
            }
        }
    }
}

$deadline_passed = false;

if (!empty($internship['deadline'])) {
    $deadline_passed = strtotime($internship['deadline']) < strtotime(date("Y-m-d"));
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Apply | <?php echo htmlspecialchars($internship['title']); ?> | InternLink
    </title>

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
            min-height: 100vh;

            font-family: 'Segoe UI', sans-serif;

            color: #dbeafe;

            background:
                radial-gradient(
                    circle at 10% 10%,
                    rgba(37,99,235,0.20),
                    transparent 30%
                ),
                radial-gradient(
                    circle at 90% 80%,
                    rgba(56,189,248,0.12),
                    transparent 30%
                ),
                linear-gradient(
                    135deg,
                    #0f172a,
                    #1e293b,
                    #172554
                );

            padding-bottom: 60px;
        }

        .navbar {
            height: 75px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 7%;

            background: rgba(15,23,42,0.72);

            backdrop-filter: blur(16px);

            border-bottom: 1px solid rgba(255,255,255,0.08);

            position: sticky;
            top: 0;

            z-index: 100;
        }

        .logo {
            color: white;

            text-decoration: none;

            font-size: 27px;

            font-weight: 750;
        }

        .logo span {
            color: #38bdf8;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 10px;
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

            background: rgba(56,189,248,0.08);
        }

        .logout {
            border: 1px solid rgba(248,113,113,0.3);
        }

        .logout:hover {
            color: #f87171 !important;

            background: rgba(248,113,113,0.08) !important;
        }

        .container {
            width: 88%;

            max-width: 1050px;

            margin: 45px auto;
        }

        .back {
            display: inline-flex;

            align-items: center;

            gap: 8px;

            color: #7dd3fc;

            text-decoration: none;

            font-size: 14px;

            margin-bottom: 25px;
        }

        .back:hover {
            color: #38bdf8;
        }

        .layout {
            display: grid;

            grid-template-columns: 1.5fr 0.8fr;

            gap: 25px;

            align-items: start;
        }

        .card {
            background: rgba(255,255,255,0.07);

            border: 1px solid rgba(255,255,255,0.1);

            border-radius: 22px;

            backdrop-filter: blur(16px);

            box-shadow:
                0 20px 50px rgba(0,0,0,0.25);
        }

        .main-card {
            padding: 32px;
        }

        .company {
            display: flex;

            align-items: center;

            gap: 12px;

            color: #94a3b8;

            font-size: 14px;

            margin-bottom: 18px;
        }

        .company-icon {
            width: 42px;
            height: 42px;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 12px;

            background: rgba(56,189,248,0.1);

            color: #38bdf8;
        }

        .title {
            color: white;

            font-size: 34px;

            line-height: 1.2;

            margin-bottom: 18px;
        }

        .description {
            color: #94a3b8;

            line-height: 1.75;

            font-size: 15px;

            margin-bottom: 28px;
        }

        .section-title {
            color: white;

            font-size: 18px;

            margin-bottom: 14px;
        }

        .skills {
            display: flex;

            flex-wrap: wrap;

            gap: 8px;

            margin-bottom: 28px;
        }

        .skill {
            padding: 7px 11px;

            border-radius: 20px;

            background: rgba(56,189,248,0.09);

            border: 1px solid rgba(56,189,248,0.16);

            color: #7dd3fc;

            font-size: 12px;
        }

        .side-card {
            padding: 25px;

            position: sticky;

            top: 100px;
        }

        .side-title {
            color: white;

            font-size: 19px;

            margin-bottom: 20px;
        }

        .detail {
            display: flex;

            align-items: flex-start;

            gap: 13px;

            padding: 14px 0;

            border-bottom: 1px solid rgba(255,255,255,0.07);
        }

        .detail:last-of-type {
            border-bottom: none;
        }

        .detail-icon {
            width: 36px;
            height: 36px;

            flex-shrink: 0;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 10px;

            background: rgba(56,189,248,0.08);

            color: #38bdf8;

            font-size: 13px;
        }

        .detail-label {
            color: #64748b;

            font-size: 11px;

            text-transform: uppercase;

            margin-bottom: 3px;
        }

        .detail-value {
            color: #dbeafe;

            font-size: 14px;
        }

        .deadline-warning {
            color: #fbbf24;
        }

        .deadline-danger {
            color: #f87171;
        }

        .apply-area {
            margin-top: 25px;

            padding-top: 20px;

            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .apply-btn {
            width: 100%;

            height: 50px;

            border: none;

            border-radius: 11px;

            background: #38bdf8;

            color: #082f49;

            font-family: inherit;

            font-size: 15px;

            font-weight: 700;

            cursor: pointer;

            transition: 0.3s;
        }

        .apply-btn:hover {
            background: #7dd3fc;

            transform: translateY(-2px);

            box-shadow:
                0 12px 25px rgba(56,189,248,0.18);
        }

        .apply-btn:disabled {
            background: #475569;

            color: #94a3b8;

            cursor: not-allowed;

            transform: none;

            box-shadow: none;
        }

        .profile-warning {
            padding: 18px;

            border-radius: 14px;

            background: rgba(234,179,8,0.08);

            border: 1px solid rgba(234,179,8,0.22);

            margin-bottom: 18px;
        }

        .profile-warning .warning-icon {
            color: #fbbf24;

            font-size: 20px;

            margin-bottom: 9px;
        }

        .profile-warning h3 {
            color: #fde68a;

            font-size: 16px;

            margin-bottom: 7px;
        }

        .profile-warning p {
            color: #a8a29e;

            font-size: 13px;

            line-height: 1.6;

            margin-bottom: 14px;
        }

        .profile-btn {
            display: inline-flex;

            align-items: center;
            justify-content: center;

            gap: 7px;

            width: 100%;

            padding: 11px;

            border-radius: 10px;

            background: rgba(251,191,36,0.1);

            border: 1px solid rgba(251,191,36,0.25);

            color: #fde68a;

            text-decoration: none;

            font-size: 13px;

            font-weight: 600;

            transition: 0.3s;
        }

        .profile-btn:hover {
            background: rgba(251,191,36,0.17);
        }

        .message {
            padding: 16px 18px;

            border-radius: 13px;

            margin-bottom: 22px;

            font-size: 14px;
        }

        .message.success {
            color: #86efac;

            background: rgba(34,197,94,0.09);

            border: 1px solid rgba(34,197,94,0.25);
        }

        .message.error {
            color: #fca5a5;

            background: rgba(239,68,68,0.09);

            border: 1px solid rgba(239,68,68,0.25);
        }

        .message.warning {
            color: #fde68a;

            background: rgba(234,179,8,0.08);

            border: 1px solid rgba(234,179,8,0.22);
        }

        .message.info {
            color: #7dd3fc;

            background: rgba(56,189,248,0.08);

            border: 1px solid rgba(56,189,248,0.2);
        }

        .success-box {
            text-align: center;

            padding: 35px 25px;
        }

        .success-icon {
            width: 70px;
            height: 70px;

            margin: 0 auto 20px;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background: rgba(34,197,94,0.1);

            color: #4ade80;

            font-size: 30px;

            border: 1px solid rgba(34,197,94,0.2);
        }

        .success-box h2 {
            color: white;

            font-size: 25px;

            margin-bottom: 10px;
        }

        .success-box p {
            color: #94a3b8;

            line-height: 1.6;

            font-size: 14px;

            margin-bottom: 22px;
        }

        .track-btn {
            display: inline-flex;

            align-items: center;
            justify-content: center;

            gap: 8px;

            padding: 12px 18px;

            border-radius: 10px;

            background: #38bdf8;

            color: #082f49;

            text-decoration: none;

            font-size: 14px;

            font-weight: 700;
        }

        .track-btn:hover {
            background: #7dd3fc;
        }

        .notice {
            margin-top: 15px;

            color: #64748b;

            text-align: center;

            font-size: 11px;

            line-height: 1.5;
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

            .layout {
                grid-template-columns: 1fr;
            }

            .side-card {
                position: static;
            }

            .title {
                font-size: 28px;
            }

        }

        @media (max-width: 500px) {

            .main-card,
            .side-card {
                padding: 22px;
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

        <a href="applications.php">
            <i class="fa-solid fa-file-lines"></i>
            Applications
        </a>

        <a href="../logout.php" class="logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
        </a>

    </div>

</nav>

<div class="container">

    <a href="internships.php" class="back">
        <i class="fa-solid fa-arrow-left"></i>
        Back to Internships
    </a>

    <?php if ($message != "" && !$application_submitted): ?>

        <div class="message <?php echo $message_type; ?>">

            <i class="fa-solid
                <?php
                if ($message_type === "warning") {
                    echo "fa-triangle-exclamation";
                } elseif ($message_type === "info") {
                    echo "fa-circle-info";
                } elseif ($message_type === "error") {
                    echo "fa-circle-exclamation";
                } else {
                    echo "fa-circle-check";
                }
                ?>">
            </i>

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>

    <?php if ($application_submitted): ?>

        <div class="card success-box">

            <div class="success-icon">
                <i class="fa-solid fa-check"></i>
            </div>

            <h2>
                Application Submitted!
            </h2>

            <p>
                Your application for
                <strong>
                    <?php echo htmlspecialchars($internship['title']); ?>
                </strong>
                has been successfully submitted.
            </p>

            <a href="applications.php" class="track-btn">
                <i class="fa-solid fa-chart-line"></i>
                Track Application
            </a>

        </div>

    <?php else: ?>

        <div class="layout">

            <div class="card main-card">

                <div class="company">

                    <div class="company-icon">
                        <i class="fa-solid fa-building"></i>
                    </div>

                    <div>
                        <div style="font-size:11px; color:#64748b;">
                            Internship offered by
                        </div>

                        <strong style="color:#dbeafe;">
                            <?php
                            echo htmlspecialchars(
                                $internship['company_name']
                            );
                            ?>
                        </strong>
                    </div>

                </div>

                <h1 class="title">
                    <?php
                    echo htmlspecialchars(
                        $internship['title']
                    );
                    ?>
                </h1>

                <p class="description">
                    <?php
                    echo nl2br(
                        htmlspecialchars(
                            $internship['description']
                        )
                    );
                    ?>
                </p>

                <h3 class="section-title">
                    <i
                        class="fa-solid fa-code"
                        style="color:#38bdf8;"
                    ></i>
                    Required Skills
                </h3>

                <div class="skills">

                    <?php

                    $skills = $internship['skills_required'] ?? '';

                    $skill_list = array_filter(
                        array_map(
                            'trim',
                            explode(',', $skills)
                        )
                    );

                    if (count($skill_list) > 0):

                        foreach ($skill_list as $skill):
                    ?>

                            <span class="skill">
                                <?php echo htmlspecialchars($skill); ?>
                            </span>

                    <?php
                        endforeach;

                    else:
                    ?>

                        <span class="skill">
                            Skills not specified
                        </span>

                    <?php endif; ?>

                </div>

            </div>

            <div class="card side-card">

                <h2 class="side-title">
                    Internship Details
                </h2>

                <div class="detail">

                    <div class="detail-icon">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>

                    <div>

                        <div class="detail-label">
                            Location
                        </div>

                        <div class="detail-value">
                            <?php
                            echo htmlspecialchars(
                                $internship['location']
                            );
                            ?>
                        </div>

                    </div>

                </div>

                <div class="detail">

                    <div class="detail-icon">
                        <i class="fa-solid fa-money-bill"></i>
                    </div>

                    <div>

                        <div class="detail-label">
                            Stipend
                        </div>

                        <div class="detail-value">
                            <?php
                            echo htmlspecialchars(
                                $internship['stipend']
                            );
                            ?>
                        </div>

                    </div>

                </div>

                <div class="detail">

                    <div class="detail-icon">
                        <i class="fa-solid fa-clock"></i>
                    </div>

                    <div>

                        <div class="detail-label">
                            Duration
                        </div>

                        <div class="detail-value">
                            <?php
                            echo htmlspecialchars(
                                $internship['duration']
                            );
                            ?>
                        </div>

                    </div>

                </div>

                <div class="detail">

                    <div class="detail-icon">
                        <i class="fa-solid fa-calendar"></i>
                    </div>

                    <div>

                        <div class="detail-label">
                            Application Deadline
                        </div>

                        <div
                            class="detail-value
                            <?php
                            echo $deadline_passed
                                ? 'deadline-danger'
                                : 'deadline-warning';
                            ?>"
                        >

                            <?php

                            if (!empty($internship['deadline'])) {

                                echo date(
                                    "d M Y",
                                    strtotime(
                                        $internship['deadline']
                                    )
                                );

                            } else {

                                echo "No deadline";

                            }

                            ?>

                        </div>

                    </div>

                </div>

                <div class="apply-area">

                    <?php if (!$student): ?>

                        <div class="profile-warning">

                            <div class="warning-icon">
                                <i class="fa-solid fa-user-pen"></i>
                            </div>

                            <h3>
                                Complete Your Profile
                            </h3>

                            <p>
                                You need to create your student profile
                                before applying. Add your college,
                                department, skills and resume so the
                                company can review your application.
                            </p>

                            <a
                                href="profile.php"
                                class="profile-btn"
                            >
                                <i class="fa-solid fa-arrow-right"></i>
                                Complete Profile
                            </a>

                        </div>

                    <?php elseif ($deadline_passed): ?>

                        <button
                            class="apply-btn"
                            disabled
                        >
                            <i class="fa-solid fa-lock"></i>
                            Application Closed
                        </button>

                        <div class="notice">
                            The application deadline has passed.
                        </div>

                    <?php else: ?>

                        <form method="POST">

                            <button
                                type="submit"
                                name="apply"
                                class="apply-btn"
                                onclick="return confirmApply();"
                            >
                                <i class="fa-solid fa-paper-plane"></i>
                                Apply for this Internship
                            </button>

                        </form>

                        <div class="notice">
                            By applying, your student profile and resume
                            may be reviewed by the company.
                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    <?php endif; ?>

</div>

<script>

function confirmApply() {

    return confirm(
        "Are you sure you want to apply for this internship?"
    );

}

</script>

</body>
</html>