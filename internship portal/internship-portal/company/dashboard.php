<?php

session_start();
include("../config/database.php");


if(!isset($_SESSION['user_id']))
{
    header("Location: ../login.php");
    exit();
}


$user_id = $_SESSION['user_id'];


/* =========================================
   GET REGISTERED USER NAME
========================================= */

$user_query = mysqli_query(
    $conn,
    "SELECT name
     FROM users
     WHERE id='$user_id'"
);


if(!$user_query)
{
    die("Database Error: " . mysqli_error($conn));
}


$user = mysqli_fetch_assoc($user_query);

$registered_name = $user['name'] ?? "Company";


/* =========================================
   GET COMPANY DETAILS
========================================= */

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


if(mysqli_num_rows($company_query) > 0)
{
    $company = mysqli_fetch_assoc($company_query);

    $company_id = $company['id'];

    $company_name = $company['company_name'];
}
else
{
    /*
       If company profile hasn't been created yet,
       use the registered name from users table.
    */

    $company_id = 0;

    $company_name = $registered_name;
}


/* =========================================
   INITIAL STATISTICS
========================================= */

$total_internships = 0;

$total_applicants = 0;

$shortlisted = 0;

$selected = 0;


/* =========================================
   TOTAL INTERNSHIPS
========================================= */

if($company_id > 0)
{
    $query = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM internships
         WHERE company_id='$company_id'"
    );


    if($query)
    {
        $row = mysqli_fetch_assoc($query);

        $total_internships = $row['total'];
    }
}


/* =========================================
   TOTAL APPLICANTS
========================================= */

if($company_id > 0)
{
    $query = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total

         FROM applications

         JOIN internships
         ON applications.internship_id = internships.id

         WHERE internships.company_id='$company_id'"
    );


    if($query)
    {
        $row = mysqli_fetch_assoc($query);

        $total_applicants = $row['total'];
    }
}


/* =========================================
   SHORTLISTED
========================================= */

if($company_id > 0)
{
    $query = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total

         FROM applications

         JOIN internships
         ON applications.internship_id = internships.id

         WHERE internships.company_id='$company_id'

         AND applications.status='Shortlisted'"
    );


    if($query)
    {
        $row = mysqli_fetch_assoc($query);

        $shortlisted = $row['total'];
    }
}


/* =========================================
   SELECTED
========================================= */

if($company_id > 0)
{
    $query = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total

         FROM applications

         JOIN internships
         ON applications.internship_id = internships.id

         WHERE internships.company_id='$company_id'

         AND applications.status='Selected'"
    );


    if($query)
    {
        $row = mysqli_fetch_assoc($query);

        $selected = $row['total'];
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Company Dashboard - InternLink</title>


<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


<style>

/* =========================================
   RESET
========================================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;

    font-family:'Segoe UI',sans-serif;
}


/* =========================================
   BODY
========================================= */

body{

    min-height:100vh;

    background:
    linear-gradient(
        135deg,
        #0f172a,
        #1e293b,
        #2563eb
    );

    color:white;

    overflow-x:hidden;
}


/* =========================================
   BACKGROUND GLOW
========================================= */

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


/* =========================================
   NAVBAR
========================================= */

nav{

    height:75px;

    display:flex;

    justify-content:space-between;

    align-items:center;

    padding:0 7%;

    background:
    rgba(255,255,255,.08);

    backdrop-filter:blur(15px);

    border-bottom:
    1px solid rgba(255,255,255,.1);

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


.nav-right{

    display:flex;

    align-items:center;

    gap:15px;
}


.company-name{

    color:#dbeafe;

    font-size:14px;

    font-weight:500;
}


.company-name i{

    color:#38bdf8;

    margin-right:5px;
}


.logout-btn{

    text-decoration:none;

    color:white;

    padding:9px 17px;

    border:
    1px solid rgba(255,255,255,.4);

    border-radius:25px;

    transition:.3s;
}


.logout-btn:hover{

    background:#38bdf8;

    color:#0f172a;

    transform:translateY(-2px);
}


/* =========================================
   MAIN CONTAINER
========================================= */

.container{

    width:90%;

    max-width:1200px;

    margin:45px auto;
}


/* =========================================
   WELCOME SECTION
========================================= */

.welcome-section{

    margin-bottom:35px;

    animation:slideUp .8s ease;
}


.welcome-section h1{

    font-size:38px;

    margin-bottom:8px;
}


.welcome-section h1 span{

    color:#38bdf8;
}


.welcome-section p{

    color:#dbeafe;

    font-size:16px;
}


/* =========================================
   STATISTICS
========================================= */

.stats-grid{

    display:grid;

    grid-template-columns:
    repeat(4,1fr);

    gap:20px;

    margin-bottom:40px;
}


.stat-card{

    padding:25px;

    background:
    rgba(255,255,255,.08);

    backdrop-filter:blur(15px);

    border:
    1px solid rgba(255,255,255,.1);

    border-radius:20px;

    display:flex;

    align-items:center;

    gap:18px;

    transition:.35s;

    animation:fadeIn .7s ease;
}


.stat-card:hover{

    transform:translateY(-7px);

    background:
    rgba(255,255,255,.12);
}


.stat-icon{

    width:55px;

    height:55px;

    display:flex;

    justify-content:center;

    align-items:center;

    border-radius:16px;

    background:
    rgba(56,189,248,.15);

    color:#38bdf8;

    font-size:22px;

    flex-shrink:0;
}


.stat-card h3{

    font-size:28px;

    margin-bottom:3px;
}


.stat-card p{

    color:#bfdbfe;

    font-size:13px;
}


/* =========================================
   SECTION TITLE
========================================= */

.section-title{

    margin-bottom:20px;

    font-size:25px;
}


/* =========================================
   ACTION GRID
========================================= */

.action-grid{

    display:grid;

    grid-template-columns:
    repeat(3,1fr);

    gap:22px;

    margin-bottom:40px;
}


.action-card{

    text-decoration:none;

    color:white;

    padding:30px;

    background:
    rgba(255,255,255,.08);

    backdrop-filter:blur(15px);

    border:
    1px solid rgba(255,255,255,.1);

    border-radius:22px;

    transition:.35s;

    animation:fadeIn .8s ease;
}


.action-card:hover{

    transform:translateY(-8px);

    background:
    rgba(255,255,255,.13);

    box-shadow:
    0 15px 35px rgba(0,0,0,.25);
}


.action-icon{

    width:60px;

    height:60px;

    display:flex;

    justify-content:center;

    align-items:center;

    border-radius:17px;

    background:
    rgba(56,189,248,.15);

    color:#38bdf8;

    font-size:25px;

    margin-bottom:18px;
}


.action-card h3{

    font-size:20px;

    margin-bottom:8px;
}


.action-card p{

    color:#dbeafe;

    font-size:14px;

    line-height:1.5;

    margin-bottom:15px;
}


.action-link{

    color:#38bdf8;

    font-size:14px;

    font-weight:bold;
}


/* =========================================
   INFO CARD
========================================= */

.info-card{

    padding:30px;

    background:
    rgba(255,255,255,.07);

    backdrop-filter:blur(15px);

    border:
    1px solid rgba(255,255,255,.1);

    border-radius:22px;

    margin-bottom:30px;
}


.info-card h2{

    margin-bottom:15px;

    font-size:23px;
}


.info-card h2 i{

    color:#38bdf8;

    margin-right:8px;
}


.info-card p{

    color:#dbeafe;

    line-height:1.7;

    font-size:15px;
}


.info-card strong{

    color:#38bdf8;
}


/* =========================================
   FOOTER
========================================= */

footer{

    text-align:center;

    padding:30px;

    margin-top:50px;

    color:#dbeafe;

    background:
    rgba(255,255,255,.05);
}


/* =========================================
   ANIMATIONS
========================================= */

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


/* =========================================
   RESPONSIVE
========================================= */

@media(max-width:900px){

    .stats-grid{

        grid-template-columns:
        repeat(2,1fr);
    }


    .action-grid{

        grid-template-columns:
        repeat(2,1fr);
    }

}


@media(max-width:600px){

    nav{

        padding:0 5%;
    }


    .logo{

        font-size:24px;
    }


    .company-name{

        display:none;
    }


    .container{

        width:92%;

        margin:30px auto;
    }


    .welcome-section h1{

        font-size:30px;
    }


    .stats-grid{

        grid-template-columns:1fr;
    }


    .action-grid{

        grid-template-columns:1fr;
    }

}

</style>

</head>


<body>


<!-- =========================================
     NAVBAR
========================================= -->

<nav>

    <div class="logo">

        Intern<span>Link</span>

    </div>


    <div class="nav-right">

        <div class="company-name">

            <i class="fa-solid fa-building"></i>

            <?= htmlspecialchars($company_name) ?>

        </div>


        <a
            href="../logout.php"
            class="logout-btn">

            <i class="fa-solid fa-right-from-bracket"></i>

            Logout

        </a>

    </div>

</nav>



<!-- =========================================
     MAIN
========================================= -->

<div class="container">


    <!-- WELCOME -->

    <div class="welcome-section">

        <h1>

            Welcome,

            <span>
                <?= htmlspecialchars($company_name) ?>
            </span>

        </h1>


        <p>

            Manage your internship opportunities
            and discover talented students.

        </p>

    </div>



    <!-- =====================================
         STATISTICS
    ====================================== -->

    <div class="stats-grid">


        <!-- INTERNSHIPS -->

        <div class="stat-card">

            <div class="stat-icon">

                <i class="fa-solid fa-briefcase"></i>

            </div>


            <div>

                <h3>
                    <?= $total_internships ?>
                </h3>

                <p>
                    Internships Posted
                </p>

            </div>

        </div>



        <!-- APPLICANTS -->

        <div class="stat-card">

            <div class="stat-icon">

                <i class="fa-solid fa-users"></i>

            </div>


            <div>

                <h3>
                    <?= $total_applicants ?>
                </h3>

                <p>
                    Total Applicants
                </p>

            </div>

        </div>



        <!-- SHORTLISTED -->

        <div class="stat-card">

            <div class="stat-icon">

                <i class="fa-solid fa-star"></i>

            </div>


            <div>

                <h3>
                    <?= $shortlisted ?>
                </h3>

                <p>
                    Shortlisted
                </p>

            </div>

        </div>



        <!-- SELECTED -->

        <div class="stat-card">

            <div class="stat-icon">

                <i class="fa-solid fa-circle-check"></i>

            </div>


            <div>

                <h3>
                    <?= $selected ?>
                </h3>

                <p>
                    Selected
                </p>

            </div>

        </div>

    </div>



    <!-- =====================================
         QUICK ACTIONS
    ====================================== -->

    <h2 class="section-title">

        Quick Actions

    </h2>


    <div class="action-grid">


        <!-- ADD INTERNSHIP -->

        <a
            href="add_internship.php"
            class="action-card">

            <div class="action-icon">

                <i class="fa-solid fa-plus"></i>

            </div>


            <h3>
                Add Internship
            </h3>


            <p>

                Create a new internship opportunity
                and attract talented students.

            </p>


            <div class="action-link">

                Create Internship

                <i class="fa-solid fa-arrow-right"></i>

            </div>

        </a>



        <!-- MANAGE INTERNSHIPS -->

        <a
            href="internships.php"
            class="action-card">

            <div class="action-icon">

                <i class="fa-solid fa-briefcase"></i>

            </div>


            <h3>
                Manage Internships
            </h3>


            <p>

                View and manage all internship
                opportunities posted by your company.

            </p>


            <div class="action-link">

                View Internships

                <i class="fa-solid fa-arrow-right"></i>

            </div>

        </a>



        <!-- APPLICANTS -->

        <a
            href="applicants.php"
            class="action-card">

            <div class="action-icon">

                <i class="fa-solid fa-user-group"></i>

            </div>


            <h3>
                View Applicants
            </h3>


            <p>

                Review student applications,
                shortlist candidates and manage
                application status.

            </p>


            <div class="action-link">

                View Applicants

                <i class="fa-solid fa-arrow-right"></i>

            </div>

        </a>

    </div>



    <!-- =====================================
         INFORMATION
    ====================================== -->

    <div class="info-card">

        <h2>

            <i class="fa-solid fa-lightbulb"></i>

            Company Portal

        </h2>


        <p>

            Use the company portal to publish internship
            opportunities, review applications and manage
            candidates throughout the recruitment process.

            Application statuses can be updated from

            <strong>Applied</strong>

            to

            <strong>Under Review</strong>,

            <strong>Shortlisted</strong>,

            <strong>Interview</strong>,

            <strong>Selected</strong>

            or

            <strong>Rejected</strong>.

        </p>

    </div>


</div>



<!-- =========================================
     FOOTER
========================================= -->

<footer>

    © 2026 InternLink Portal |
    Company Internship Management System

</footer>


</body>

</html>