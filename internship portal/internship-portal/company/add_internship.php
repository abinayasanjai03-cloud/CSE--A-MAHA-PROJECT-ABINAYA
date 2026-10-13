<?php

session_start();
include("../config/database.php");

if(!isset($_SESSION['user_id']))
{
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$message = "";
$error = "";

$user_query = mysqli_query(
    $conn,
    "SELECT name FROM users WHERE id='$user_id'"
);

if(!$user_query)
{
    die("Database Error: " . mysqli_error($conn));
}

$user = mysqli_fetch_assoc($user_query);

$user_name = $user['name'] ?? "Company";

$company_query = mysqli_query(
    $conn,
    "SELECT id, company_name
     FROM companies
     WHERE user_id='$user_id'"
);

if(!$company_query)
{
    die("Database Error: " . mysqli_error($conn));
}

if(mysqli_num_rows($company_query) == 0)
{
    $company_name = $user_name;

    $insert_company = mysqli_query(
        $conn,
        "INSERT INTO companies
        (user_id, company_name)
        VALUES
        ('$user_id', '$company_name')"
    );

    if(!$insert_company)
    {
        die("Company Creation Error: " . mysqli_error($conn));
    }

    $company_id = mysqli_insert_id($conn);
}
else
{
    $company = mysqli_fetch_assoc($company_query);

    $company_id = $company['id'];
    $company_name = $company['company_name'];
}

if(isset($_POST['add']))
{
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $skills = trim($_POST['skills']);
    $location = trim($_POST['location']);
    $stipend = trim($_POST['stipend']);
    $duration = trim($_POST['duration']);
    $deadline = $_POST['deadline'];

    if(
        empty($title) ||
        empty($description) ||
        empty($skills) ||
        empty($location) ||
        empty($stipend) ||
        empty($duration) ||
        empty($deadline)
    )
    {
        $error = "Please fill in all fields.";
    }
    else
    {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO internships
            (
                company_id,
                title,
                description,
                skills_required,
                location,
                stipend,
                duration,
                deadline
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "isssssss",
            $company_id,
            $title,
            $description,
            $skills,
            $location,
            $stipend,
            $duration,
            $deadline
        );

        if(mysqli_stmt_execute($stmt))
        {
            $message = "Internship added successfully!";

            $title = "";
            $description = "";
            $skills = "";
            $location = "";
            $stipend = "";
            $duration = "";
            $deadline = "";
        }
        else
        {
            $error = "Failed to add internship: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Add Internship - InternLink</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    min-height:100vh;
    background:linear-gradient(135deg,#0f172a,#1e293b,#2563eb);
    color:white;
    overflow-x:hidden;
}

body::before{
    content:'';
    position:fixed;
    width:350px;
    height:350px;
    background:#38bdf8;
    border-radius:50%;
    top:-150px;
    left:-150px;
    filter:blur(140px);
    opacity:.35;
    z-index:-1;
}

body::after{
    content:'';
    position:fixed;
    width:400px;
    height:400px;
    background:#2563eb;
    border-radius:50%;
    bottom:-180px;
    right:-180px;
    filter:blur(140px);
    opacity:.4;
    z-index:-1;
}

nav{
    height:75px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:0 7%;
    background:rgba(255,255,255,.08);
    backdrop-filter:blur(15px);
    border-bottom:1px solid rgba(255,255,255,.1);
    position:sticky;
    top:0;
    z-index:100;
}

.logo{
    font-size:28px;
    font-weight:bold;
}

.logo span{
    color:#38bdf8;
}

.nav-links{
    display:flex;
    gap:12px;
}

.nav-btn{
    text-decoration:none;
    color:white;
    padding:9px 17px;
    border:1px solid rgba(255,255,255,.4);
    border-radius:25px;
    transition:.3s;
}

.nav-btn:hover{
    background:#38bdf8;
    color:#0f172a;
    transform:translateY(-2px);
}

.container{
    width:90%;
    max-width:850px;
    margin:50px auto;
}

.page-header{
    text-align:center;
    margin-bottom:35px;
    animation:slideUp .7s ease;
}

.page-icon{
    width:70px;
    height:70px;
    margin:0 auto 15px;
    display:flex;
    justify-content:center;
    align-items:center;
    border-radius:20px;
    background:rgba(56,189,248,.15);
    color:#38bdf8;
    font-size:30px;
}

.page-header h1{
    font-size:36px;
    margin-bottom:8px;
}

.page-header h1 span{
    color:#38bdf8;
}

.page-header p{
    color:#dbeafe;
}

.form-card{
    background:rgba(255,255,255,.08);
    backdrop-filter:blur(18px);
    border:1px solid rgba(255,255,255,.12);
    border-radius:25px;
    padding:35px;
    box-shadow:0 15px 40px rgba(0,0,0,.25);
    animation:fadeIn .8s ease;
}

.company-info{
    display:flex;
    align-items:center;
    gap:15px;
    padding:15px;
    margin-bottom:25px;
    border-radius:15px;
    background:rgba(56,189,248,.08);
    border:1px solid rgba(56,189,248,.15);
}

.company-icon{
    width:45px;
    height:45px;
    display:flex;
    justify-content:center;
    align-items:center;
    border-radius:12px;
    background:rgba(56,189,248,.15);
    color:#38bdf8;
}

.company-info small{
    color:#94a3b8;
    display:block;
    margin-bottom:3px;
}

.company-info strong{
    color:white;
}

.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
}

.form-group{
    margin-bottom:5px;
}

.full-width{
    grid-column:1 / -1;
}

label{
    display:block;
    margin-bottom:8px;
    color:#dbeafe;
    font-size:14px;
    font-weight:600;
}

label i{
    color:#38bdf8;
    margin-right:6px;
}

input,
textarea{
    width:100%;
    padding:14px 16px;
    border:none;
    outline:none;
    border-radius:12px;
    background:rgba(255,255,255,.1);
    border:1px solid rgba(255,255,255,.08);
    color:white;
    font-size:15px;
    transition:.3s;
}

input::placeholder,
textarea::placeholder{
    color:#94a3b8;
}

input:focus,
textarea:focus{
    border-color:#38bdf8;
    background:rgba(255,255,255,.13);
    box-shadow:0 0 0 3px rgba(56,189,248,.1);
}

textarea{
    min-height:130px;
    resize:vertical;
}

input[type="date"]{
    color-scheme:dark;
}

.success{
    padding:13px 16px;
    margin-bottom:20px;
    border-radius:12px;
    background:rgba(34,197,94,.12);
    border:1px solid rgba(34,197,94,.3);
    color:#86efac;
    text-align:center;
}

.error{
    padding:13px 16px;
    margin-bottom:20px;
    border-radius:12px;
    background:rgba(239,68,68,.12);
    border:1px solid rgba(239,68,68,.3);
    color:#fca5a5;
    text-align:center;
}

.button-area{
    margin-top:25px;
    display:flex;
    gap:12px;
}

.btn{
    flex:1;
    padding:14px;
    border:none;
    border-radius:12px;
    background:#38bdf8;
    color:#0f172a;
    font-size:16px;
    font-weight:bold;
    cursor:pointer;
    transition:.3s;
}

.btn:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 25px rgba(56,189,248,.3);
}

.cancel-btn{
    flex:0 0 auto;
    padding:14px 22px;
    border-radius:12px;
    border:1px solid rgba(255,255,255,.3);
    color:white;
    text-decoration:none;
    display:flex;
    align-items:center;
    transition:.3s;
}

.cancel-btn:hover{
    background:rgba(255,255,255,.1);
}

footer{
    text-align:center;
    padding:30px;
    margin-top:40px;
    color:#dbeafe;
    background:rgba(255,255,255,.05);
}

@keyframes slideUp{
    from{
        opacity:0;
        transform:translateY(35px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(20px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

@media(max-width:650px){

    .form-grid{
        grid-template-columns:1fr;
    }

    .full-width{
        grid-column:auto;
    }

    .container{
        width:92%;
        margin:30px auto;
    }

    .form-card{
        padding:25px;
    }

    .page-header h1{
        font-size:30px;
    }

    .button-area{
        flex-direction:column;
    }

    .cancel-btn{
        justify-content:center;
    }

    .nav-btn{
        padding:8px 12px;
        font-size:13px;
    }
}

</style>

</head>

<body>

<nav>

    <div class="logo">
        Intern<span>Link</span>
    </div>

    <div class="nav-links">

        <a href="dashboard.php" class="nav-btn">
            <i class="fa-solid fa-house"></i>
            Dashboard
        </a>

        <a href="../logout.php" class="nav-btn">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
        </a>

    </div>

</nav>

<div class="container">

    <div class="page-header">

        <div class="page-icon">
            <i class="fa-solid fa-briefcase"></i>
        </div>

        <h1>
            Add <span>Internship</span>
        </h1>

        <p>
            Create a new internship opportunity for students.
        </p>

    </div>

    <div class="form-card">

        <div class="company-info">

            <div class="company-icon">
                <i class="fa-solid fa-building"></i>
            </div>

            <div>

                <small>Posting as</small>

                <strong>
                    <?= htmlspecialchars($company_name) ?>
                </strong>

            </div>

        </div>

        <?php if(!empty($message)){ ?>

            <div class="success">
                <i class="fa-solid fa-circle-check"></i>
                <?= htmlspecialchars($message) ?>
            </div>

        <?php } ?>

        <?php if(!empty($error)){ ?>

            <div class="error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?= htmlspecialchars($error) ?>
            </div>

        <?php } ?>

        <form method="POST">

            <div class="form-grid">

                <div class="form-group full-width">

                    <label>
                        <i class="fa-solid fa-heading"></i>
                        Internship Title
                    </label>

                    <input
                        type="text"
                        name="title"
                        placeholder="Example: Web Development Intern"
                        value="<?= htmlspecialchars($title ?? '') ?>"
                        required>

                </div>

                <div class="form-group full-width">

                    <label>
                        <i class="fa-solid fa-align-left"></i>
                        Internship Description
                    </label>

                    <textarea
                        name="description"
                        placeholder="Describe the internship, responsibilities and what students will learn..."
                        required><?= htmlspecialchars($description ?? '') ?></textarea>

                </div>

                <div class="form-group full-width">

                    <label>
                        <i class="fa-solid fa-code"></i>
                        Skills Required
                    </label>

                    <input
                        type="text"
                        name="skills"
                        placeholder="Example: PHP, MySQL, HTML, CSS, JavaScript"
                        value="<?= htmlspecialchars($skills ?? '') ?>"
                        required>

                </div>

                <div class="form-group">

                    <label>
                        <i class="fa-solid fa-location-dot"></i>
                        Location
                    </label>

                    <input
                        type="text"
                        name="location"
                        placeholder="Example: Chennai"
                        value="<?= htmlspecialchars($location ?? '') ?>"
                        required>

                </div>

                <div class="form-group">

                    <label>
                        <i class="fa-solid fa-indian-rupee-sign"></i>
                        Stipend
                    </label>

                    <input
                        type="text"
                        name="stipend"
                        placeholder="Example: ₹10,000 / month"
                        value="<?= htmlspecialchars($stipend ?? '') ?>"
                        required>

                </div>

                <div class="form-group">

                    <label>
                        <i class="fa-solid fa-clock"></i>
                        Duration
                    </label>

                    <input
                        type="text"
                        name="duration"
                        placeholder="Example: 3 Months"
                        value="<?= htmlspecialchars($duration ?? '') ?>"
                        required>

                </div>

                <div class="form-group">

                    <label>
                        <i class="fa-solid fa-calendar-days"></i>
                        Application Deadline
                    </label>

                    <input
                        type="date"
                        name="deadline"
                        value="<?= htmlspecialchars($deadline ?? '') ?>"
                        required>

                </div>

            </div>

            <div class="button-area">

                <a href="dashboard.php" class="cancel-btn">
                    <i class="fa-solid fa-arrow-left"></i>
                    &nbsp; Cancel
                </a>

                <button
                    type="submit"
                    name="add"
                    class="btn">

                    <i class="fa-solid fa-plus"></i>
                    &nbsp; Add Internship

                </button>

            </div>

        </form>

    </div>

</div>

<footer>
    © 2026 InternLink Portal | Internship Management System
</footer>

</body>
</html>