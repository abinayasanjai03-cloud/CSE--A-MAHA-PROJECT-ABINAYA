<?php

session_start();
include("../config/database.php");


/* =========================================
   CHECK LOGIN
========================================= */

if(!isset($_SESSION['user_id']))
{
    header("Location: ../login.php");
    exit();
}


$user_id = $_SESSION['user_id'];


/* =========================================
   GET APPLICATIONS
========================================= */

$sql = "
SELECT
    internships.title,
    internships.location,
    internships.stipend,
    applications.status,
    applications.applied_at

FROM applications

JOIN internships
ON applications.internship_id = internships.id

JOIN students
ON applications.student_id = students.id

WHERE students.user_id = '$user_id'

ORDER BY applications.applied_at DESC
";


$result = mysqli_query($conn, $sql);


if(!$result)
{
    die("Database Error: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>My Applications - InternLink</title>


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


.nav-links{

    display:flex;

    gap:12px;
}


.nav-btn{

    text-decoration:none;

    color:white;

    padding:9px 17px;

    border:
    1px solid rgba(255,255,255,.4);

    border-radius:25px;

    transition:.3s;
}


.nav-btn:hover{

    background:#38bdf8;

    color:#0f172a;

    transform:translateY(-2px);
}


/* =========================================
   MAIN CONTAINER
========================================= */

.container{

    width:90%;

    max-width:1100px;

    margin:50px auto;
}


/* =========================================
   PAGE HEADER
========================================= */

.page-header{

    text-align:center;

    margin-bottom:40px;

    animation:slideUp .8s ease;
}


.page-icon{

    width:70px;
    height:70px;

    display:flex;

    justify-content:center;

    align-items:center;

    margin:0 auto 15px;

    border-radius:20px;

    background:
    rgba(56,189,248,.15);

    color:#38bdf8;

    font-size:30px;
}


.page-header h1{

    font-size:38px;

    margin-bottom:8px;
}


.page-header h1 span{

    color:#38bdf8;
}


.page-header p{

    color:#dbeafe;

    font-size:16px;
}


/* =========================================
   APPLICATION GRID
========================================= */

.application-grid{

    display:grid;

    grid-template-columns:
    repeat(auto-fit,minmax(330px,1fr));

    gap:25px;
}


/* =========================================
   APPLICATION CARD
========================================= */

.application-card{

    background:
    rgba(255,255,255,.08);

    backdrop-filter:blur(18px);

    border:
    1px solid rgba(255,255,255,.12);

    border-radius:22px;

    padding:28px;

    box-shadow:
    0 12px 35px rgba(0,0,0,.22);

    transition:.35s;

    animation:fadeIn .7s ease;
}


.application-card:hover{

    transform:translateY(-7px);

    background:
    rgba(255,255,255,.12);

    box-shadow:
    0 18px 40px rgba(0,0,0,.3);
}


/* =========================================
   CARD HEADER
========================================= */

.card-header{

    display:flex;

    align-items:center;

    gap:15px;

    margin-bottom:20px;
}


.company-icon{

    width:55px;
    height:55px;

    display:flex;

    justify-content:center;

    align-items:center;

    border-radius:16px;

    background:
    rgba(56,189,248,.15);

    color:#38bdf8;

    font-size:23px;

    flex-shrink:0;
}


.card-header h2{

    font-size:21px;

    line-height:1.3;
}


.card-header p{

    color:#bfdbfe;

    font-size:13px;

    margin-top:4px;
}


/* =========================================
   DETAILS
========================================= */

.details{

    display:grid;

    grid-template-columns:1fr 1fr;

    gap:10px;

    margin-bottom:22px;
}


.detail{

    padding:13px;

    border-radius:12px;

    background:
    rgba(255,255,255,.05);

    color:#dbeafe;

    font-size:13px;
}


.detail i{

    color:#38bdf8;

    margin-right:7px;
}


/* =========================================
   STATUS SECTION
========================================= */

.status-section{

    border-top:
    1px solid rgba(255,255,255,.1);

    padding-top:20px;
}


.status-label{

    color:#bfdbfe;

    font-size:13px;

    margin-bottom:10px;
}


/* =========================================
   STATUS BADGES
========================================= */

.status{

    display:inline-flex;

    align-items:center;

    gap:8px;

    padding:9px 16px;

    border-radius:25px;

    font-size:13px;

    font-weight:bold;
}


/* Applied */

.status-applied{

    background:
    rgba(56,189,248,.15);

    color:#7dd3fc;

    border:
    1px solid rgba(56,189,248,.3);
}


/* Under Review */

.status-review{

    background:
    rgba(250,204,21,.12);

    color:#fde047;

    border:
    1px solid rgba(250,204,21,.3);
}


/* Shortlisted */

.status-shortlisted{

    background:
    rgba(168,85,247,.15);

    color:#d8b4fe;

    border:
    1px solid rgba(168,85,247,.3);
}


/* Interview */

.status-interview{

    background:
    rgba(249,115,22,.15);

    color:#fdba74;

    border:
    1px solid rgba(249,115,22,.3);
}


/* Selected */

.status-selected{

    background:
    rgba(34,197,94,.15);

    color:#86efac;

    border:
    1px solid rgba(34,197,94,.3);
}


/* Rejected */

.status-rejected{

    background:
    rgba(239,68,68,.15);

    color:#fca5a5;

    border:
    1px solid rgba(239,68,68,.3);
}


/* =========================================
   APPLICATION DATE
========================================= */

.applied-date{

    margin-top:15px;

    color:#94a3b8;

    font-size:12px;
}


/* =========================================
   PROGRESS TIMELINE
========================================= */

.timeline{

    display:flex;

    align-items:center;

    margin-top:22px;

    overflow:hidden;
}


.step{

    display:flex;

    flex-direction:column;

    align-items:center;

    flex:1;

    position:relative;

    min-width:0;
}


.step:not(:last-child)::after{

    content:'';

    position:absolute;

    top:9px;

    left:50%;

    width:100%;

    height:2px;

    background:
    rgba(255,255,255,.15);

    z-index:0;
}


.dot{

    width:19px;

    height:19px;

    border-radius:50%;

    background:#334155;

    border:
    3px solid #1e293b;

    position:relative;

    z-index:2;
}


.step.active .dot{

    background:#38bdf8;

    box-shadow:
    0 0 12px rgba(56,189,248,.6);
}


.step.selected .dot{

    background:#4ade80;

    box-shadow:
    0 0 12px rgba(74,222,128,.6);
}


.step.rejected .dot{

    background:#f87171;

    box-shadow:
    0 0 12px rgba(248,113,113,.6);
}


.step span{

    font-size:9px;

    color:#94a3b8;

    margin-top:7px;

    text-align:center;

    white-space:nowrap;
}


.step.active span{

    color:#7dd3fc;

}


/* =========================================
   NO APPLICATIONS
========================================= */

.no-applications{

    text-align:center;

    padding:70px 25px;

    background:
    rgba(255,255,255,.07);

    backdrop-filter:blur(15px);

    border:
    1px solid rgba(255,255,255,.1);

    border-radius:25px;

    animation:slideUp .8s ease;
}


.no-applications i{

    font-size:50px;

    color:#38bdf8;

    margin-bottom:20px;
}


.no-applications h2{

    margin-bottom:10px;
}


.no-applications p{

    color:#dbeafe;

    margin-bottom:25px;
}


.browse-btn{

    display:inline-block;

    text-decoration:none;

    background:#38bdf8;

    color:#0f172a;

    padding:12px 25px;

    border-radius:12px;

    font-weight:bold;

    transition:.3s;
}


.browse-btn:hover{

    transform:translateY(-3px);

    box-shadow:
    0 8px 20px rgba(56,189,248,.3);
}


/* =========================================
   FOOTER
========================================= */

footer{

    text-align:center;

    padding:30px;

    margin-top:60px;

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

@media(max-width:650px){

    nav{

        padding:0 5%;
    }


    .logo{

        font-size:24px;
    }


    .nav-btn{

        padding:8px 12px;

        font-size:13px;
    }


    .container{

        width:92%;

        margin:30px auto;
    }


    .page-header h1{

        font-size:30px;
    }


    .application-grid{

        grid-template-columns:1fr;
    }


    .details{

        grid-template-columns:1fr;
    }


    .timeline{

        margin-left:-5px;

        margin-right:-5px;
    }


    .step span{

        font-size:7px;
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


    <div class="nav-links">

        <a
            href="dashboard.php"
            class="nav-btn">

            <i class="fa-solid fa-house"></i>

            Dashboard

        </a>


        <a
            href="../logout.php"
            class="nav-btn">

            <i class="fa-solid fa-right-from-bracket"></i>

            Logout

        </a>

    </div>

</nav>



<!-- =========================================
     MAIN
========================================= -->

<div class="container">


    <!-- PAGE HEADER -->

    <div class="page-header">

        <div class="page-icon">

            <i class="fa-solid fa-chart-line"></i>

        </div>


        <h1>

            My <span>Applications</span>

        </h1>


        <p>

            Track the progress of your internship applications.

        </p>

    </div>



    <!-- APPLICATIONS -->

    <div class="application-grid">


    <?php

    if(mysqli_num_rows($result) > 0)
    {

        while($row=mysqli_fetch_assoc($result))
        {

            $status = $row['status'];


            /* Determine status class */

            $status_class = "status-applied";

            $status_icon = "fa-paper-plane";


            if($status == "Under Review")
            {
                $status_class = "status-review";
                $status_icon = "fa-eye";
            }
            elseif($status == "Shortlisted")
            {
                $status_class = "status-shortlisted";
                $status_icon = "fa-star";
            }
            elseif($status == "Interview")
            {
                $status_class = "status-interview";
                $status_icon = "fa-comments";
            }
            elseif($status == "Selected")
            {
                $status_class = "status-selected";
                $status_icon = "fa-circle-check";
            }
            elseif($status == "Rejected")
            {
                $status_class = "status-rejected";
                $status_icon = "fa-circle-xmark";
            }

    ?>


        <div class="application-card">


            <!-- Card Header -->

            <div class="card-header">

                <div class="company-icon">

                    <i class="fa-solid fa-building"></i>

                </div>


                <div>

                    <h2>

                        <?= htmlspecialchars(
                            $row['title']
                        ) ?>

                    </h2>


                    <p>

                        Internship Application

                    </p>

                </div>

            </div>



            <!-- Details -->

            <div class="details">


                <div class="detail">

                    <i class="fa-solid fa-location-dot"></i>

                    <?= htmlspecialchars(
                        $row['location']
                    ) ?>

                </div>


                <div class="detail">

                    <i class="fa-solid fa-money-bill-wave"></i>

                    <?= htmlspecialchars(
                        $row['stipend']
                    ) ?>

                </div>

            </div>



            <!-- Status -->

            <div class="status-section">

                <div class="status-label">

                    Current Application Status

                </div>


                <div class="status <?= $status_class ?>">

                    <i class="fa-solid <?= $status_icon ?>"></i>

                    <?= htmlspecialchars($status) ?>

                </div>



                <!-- Timeline -->

                <?php if($status != "Rejected"){ ?>

                <div class="timeline">


                    <div class="step active">

                        <div class="dot"></div>

                        <span>Applied</span>

                    </div>


                    <div class="step
                        <?= in_array(
                            $status,
                            ['Under Review',
                             'Shortlisted',
                             'Interview',
                             'Selected']
                        )
                        ? 'active'
                        : ''
                        ?>">

                        <div class="dot"></div>

                        <span>Review</span>

                    </div>


                    <div class="step
                        <?= in_array(
                            $status,
                            ['Shortlisted',
                             'Interview',
                             'Selected']
                        )
                        ? 'active'
                        : ''
                        ?>">

                        <div class="dot"></div>

                        <span>Shortlisted</span>

                    </div>


                    <div class="step
                        <?= in_array(
                            $status,
                            ['Interview',
                             'Selected']
                        )
                        ? 'active'
                        : ''
                        ?>">

                        <div class="dot"></div>

                        <span>Interview</span>

                    </div>


                    <div class="step
                        <?= $status == 'Selected'
                        ? 'selected'
                        : ''
                        ?>">

                        <div class="dot"></div>

                        <span>Selected</span>

                    </div>


                </div>

                <?php } else { ?>

                    <div class="timeline">


                        <div class="step active">

                            <div class="dot"></div>

                            <span>Applied</span>

                        </div>


                        <div class="step">

                            <div class="dot"></div>

                            <span>Review</span>

                        </div>


                        <div class="step rejected">

                            <div class="dot"></div>

                            <span>Rejected</span>

                        </div>


                    </div>

                <?php } ?>



                <!-- Applied Date -->

                <div class="applied-date">

                    <i class="fa-regular fa-calendar"></i>

                    Applied on:

                    <?= date(
                        'd M Y',
                        strtotime($row['applied_at'])
                    ) ?>

                </div>

            </div>


        </div>


    <?php

        }

    }
    else
    {

    ?>


        <!-- NO APPLICATIONS -->

        <div class="no-applications"
             style="grid-column:1/-1;">

            <i class="fa-solid fa-file-circle-xmark"></i>


            <h2>
                No Applications Yet
            </h2>


            <p>

                You haven't applied for any internships yet.
                Explore available opportunities and start
                your career journey.

            </p>


            <a
                href="internships.php"
                class="browse-btn">

                <i class="fa-solid fa-briefcase"></i>

                Browse Internships

            </a>

        </div>


    <?php

    }

    ?>


    </div>

</div>



<footer>

    © 2026 InternLink Portal |
    Internship Application Management System

</footer>


</body>

</html>