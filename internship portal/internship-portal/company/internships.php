<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$user_query = mysqli_query($conn, "SELECT name FROM users WHERE id='$user_id'");
$user_data = mysqli_fetch_assoc($user_query);

$company_query = mysqli_query($conn, "SELECT id, company_name FROM companies WHERE user_id='$user_id'");

if (mysqli_num_rows($company_query) > 0) {
    $company = mysqli_fetch_assoc($company_query);
    $company_id = $company['id'];
    $company_name = $company['company_name'];
} else {
    $company_id = 0;
    $company_name = $user_data['name'] ?? "Company";
}

$message = "";
$message_type = "";

if (isset($_POST['delete'])) {

    $internship_id = intval($_POST['internship_id']);

    $check = mysqli_query(
        $conn,
        "SELECT id FROM internships
         WHERE id='$internship_id'
         AND company_id='$company_id'"
    );

    if ($check && mysqli_num_rows($check) > 0) {

        $delete = mysqli_query(
            $conn,
            "DELETE FROM internships
             WHERE id='$internship_id'
             AND company_id='$company_id'"
        );

        if ($delete) {
            $message = "Internship deleted successfully.";
            $message_type = "success";
        } else {
            $message = "Unable to delete the internship.";
            $message_type = "error";
        }

    } else {
        $message = "Invalid internship.";
        $message_type = "error";
    }
}

$internships = [];

if ($company_id > 0) {

    $sql = "
        SELECT 
            internships.*,
            COUNT(applications.id) AS applicant_count
        FROM internships
        LEFT JOIN applications
            ON internships.id = applications.internship_id
        WHERE internships.company_id='$company_id'
        GROUP BY internships.id
        ORDER BY internships.created_at DESC
    ";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $internships[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Internships | InternLink</title>

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
            font-size: 26px;
            font-weight: 700;
            color: white;
            text-decoration: none;
        }

        .logo span {
            color: #38bdf8;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-links a {
            text-decoration: none;
            color: #dbeafe;
            padding: 10px 16px;
            border-radius: 10px;
            transition: 0.3s;
        }

        .nav-links a:hover {
            background: rgba(56,189,248,0.12);
            color: #38bdf8;
        }

        .logout {
            border: 1px solid rgba(248,113,113,0.4);
            color: #fecaca !important;
        }

        .logout:hover {
            background: rgba(248,113,113,0.12) !important;
            color: #f87171 !important;
        }

        .container {
            width: 86%;
            max-width: 1250px;
            margin: 45px auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 34px;
            color: white;
            margin-bottom: 7px;
        }

        .page-header p {
            color: #94a3b8;
            font-size: 15px;
        }

        .add-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 20px;
            border-radius: 12px;
            background: #38bdf8;
            color: #082f49;
            text-decoration: none;
            font-weight: 700;
            transition: 0.3s;
        }

        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(56,189,248,0.25);
        }

        .message {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 25px;
            backdrop-filter: blur(10px);
        }

        .success {
            background: rgba(34,197,94,0.12);
            border: 1px solid rgba(34,197,94,0.3);
            color: #86efac;
        }

        .error {
            background: rgba(239,68,68,0.12);
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
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
            margin-bottom: 25px;
        }

        .internship-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(330px, 1fr));
            gap: 25px;
        }

        .card {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 25px;
            backdrop-filter: blur(15px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            transition: 0.3s;
            position: relative;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            border-color: rgba(56,189,248,0.35);
            box-shadow: 0 20px 40px rgba(0,0,0,0.28);
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 18px;
        }

        .card-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            background: rgba(56,189,248,0.12);
            color: #38bdf8;
            font-size: 20px;
        }

        .applicants {
            padding: 7px 11px;
            border-radius: 20px;
            background: rgba(56,189,248,0.1);
            color: #7dd3fc;
            font-size: 13px;
            white-space: nowrap;
        }

        .card h2 {
            color: white;
            font-size: 22px;
            margin-bottom: 12px;
        }

        .description {
            color: #94a3b8;
            font-size: 14px;
            line-height: 1.6;
            min-height: 65px;
            margin-bottom: 20px;
        }

        .details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 20px;
        }

        .detail {
            background: rgba(15,23,42,0.35);
            border-radius: 10px;
            padding: 11px;
        }

        .detail-label {
            display: block;
            color: #64748b;
            font-size: 11px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .detail-value {
            color: #dbeafe;
            font-size: 13px;
            word-break: break-word;
        }

        .skills {
            margin-bottom: 20px;
        }

        .skills-title {
            color: #64748b;
            font-size: 11px;
            text-transform: uppercase;
            margin-bottom: 7px;
        }

        .skills-text {
            color: #bae6fd;
            font-size: 14px;
        }

        .deadline {
            color: #fbbf24;
        }

        .card-actions {
            display: flex;
            gap: 10px;
            border-top: 1px solid rgba(255,255,255,0.08);
            padding-top: 18px;
        }

        .view-btn {
            flex: 1;
            text-align: center;
            padding: 11px;
            border-radius: 10px;
            text-decoration: none;
            background: rgba(56,189,248,0.1);
            border: 1px solid rgba(56,189,248,0.25);
            color: #7dd3fc;
            transition: 0.3s;
        }

        .view-btn:hover {
            background: rgba(56,189,248,0.18);
        }

        .delete-btn {
            padding: 11px 15px;
            border-radius: 10px;
            border: 1px solid rgba(248,113,113,0.3);
            background: rgba(248,113,113,0.08);
            color: #fca5a5;
            cursor: pointer;
            transition: 0.3s;
        }

        .delete-btn:hover {
            background: rgba(248,113,113,0.18);
            color: #f87171;
        }

        @media (max-width: 700px) {

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

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-header h1 {
                font-size: 28px;
            }

            .internship-grid {
                grid-template-columns: 1fr;
            }

            .details {
                grid-template-columns: 1fr;
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

        <a href="add_internship.php">
            <i class="fa-solid fa-plus"></i>
            Add Internship
        </a>

        <a href="applicants.php">
            <i class="fa-solid fa-users"></i>
            Applicants
        </a>

        <a href="../logout.php" class="logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
        </a>
    </div>

</nav>

<div class="container">

    <div class="page-header">

        <div>
            <h1>My Internships</h1>
            <p>
                Manage the internship opportunities posted by
                <?php echo htmlspecialchars($company_name); ?>
            </p>
        </div>

        <a href="add_internship.php" class="add-btn">
            <i class="fa-solid fa-plus"></i>
            Post Internship
        </a>

    </div>

    <?php if ($message != ""): ?>

        <div class="message <?php echo $message_type; ?>">
            <i class="fa-solid
                <?php echo $message_type == 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>">
            </i>

            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>

    <?php if (count($internships) == 0): ?>

        <div class="empty">

            <i class="fa-solid fa-briefcase"></i>

            <h2>No Internships Posted Yet</h2>

            <p>
                Start attracting talented students by posting your first internship opportunity.
            </p>

            <a href="add_internship.php" class="add-btn">
                <i class="fa-solid fa-plus"></i>
                Add Your First Internship
            </a>

        </div>

    <?php else: ?>

        <div class="internship-grid">

            <?php foreach ($internships as $internship): ?>

                <div class="card">

                    <div class="card-top">

                        <div class="card-icon">
                            <i class="fa-solid fa-briefcase"></i>
                        </div>

                        <div class="applicants">
                            <i class="fa-solid fa-users"></i>
                            <?php echo $internship['applicant_count']; ?>
                            Applicants
                        </div>

                    </div>

                    <h2>
                        <?php echo htmlspecialchars($internship['title']); ?>
                    </h2>

                    <p class="description">
                        <?php
                        $description = $internship['description'] ?? '';

                        if (strlen($description) > 150) {
                            echo htmlspecialchars(substr($description, 0, 150)) . "...";
                        } else {
                            echo htmlspecialchars($description);
                        }
                        ?>
                    </p>

                    <div class="skills">

                        <div class="skills-title">
                            Required Skills
                        </div>

                        <div class="skills-text">
                            <?php echo htmlspecialchars($internship['skills_required'] ?? 'Not specified'); ?>
                        </div>

                    </div>

                    <div class="details">

                        <div class="detail">
                            <span class="detail-label">
                                <i class="fa-solid fa-location-dot"></i>
                                Location
                            </span>

                            <span class="detail-value">
                                <?php echo htmlspecialchars($internship['location'] ?? 'Not specified'); ?>
                            </span>
                        </div>

                        <div class="detail">
                            <span class="detail-label">
                                <i class="fa-solid fa-money-bill"></i>
                                Stipend
                            </span>

                            <span class="detail-value">
                                <?php echo htmlspecialchars($internship['stipend'] ?? 'Not specified'); ?>
                            </span>
                        </div>

                        <div class="detail">
                            <span class="detail-label">
                                <i class="fa-solid fa-clock"></i>
                                Duration
                            </span>

                            <span class="detail-value">
                                <?php echo htmlspecialchars($internship['duration'] ?? 'Not specified'); ?>
                            </span>
                        </div>

                        <div class="detail">
                            <span class="detail-label">
                                <i class="fa-solid fa-calendar"></i>
                                Deadline
                            </span>

                            <span class="detail-value deadline">
                                <?php
                                if (!empty($internship['deadline'])) {
                                    echo date(
                                        "d M Y",
                                        strtotime($internship['deadline'])
                                    );
                                } else {
                                    echo "No deadline";
                                }
                                ?>
                            </span>
                        </div>

                    </div>

                    <div class="card-actions">

                        <a
                            href="applicants.php?internship_id=<?php echo $internship['id']; ?>"
                            class="view-btn"
                        >
                            <i class="fa-solid fa-users"></i>
                            View Applicants
                        </a>

                        <form method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this internship?');">

                            <input
                                type="hidden"
                                name="internship_id"
                                value="<?php echo $internship['id']; ?>"
                            >

                            <button
                                type="submit"
                                name="delete"
                                class="delete-btn"
                                title="Delete Internship"
                            >
                                <i class="fa-solid fa-trash"></i>
                            </button>

                        </form>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

</body>
</html>