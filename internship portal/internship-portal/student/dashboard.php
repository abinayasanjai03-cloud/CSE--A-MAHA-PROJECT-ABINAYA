<?php
session_start();

if(!isset($_SESSION['user_id']))
{
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student Dashboard - InternLink</title>

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

/* Background Glow */

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

/* Navbar */

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

.nav-right{
    display:flex;
    align-items:center;
    gap:15px;
}

.student-label{
    color:#dbeafe;
    font-size:14px;
}

.logout{
    text-decoration:none;
    color:white;
    padding:9px 18px;
    border:1px solid rgba(255,255,255,.5);
    border-radius:25px;
    transition:.3s;
}

.logout:hover{
    background:#38bdf8;
    color:#0f172a;
    transform:translateY(-2px);
}

/* Main */

.container{
    width:85%;
    max-width:1200px;
    margin:50px auto;
}

/* Welcome */

.welcome{
    background:rgba(255,255,255,.08);
    backdrop-filter:blur(15px);
    border:1px solid rgba(255,255,255,.1);
    border-radius:25px;
    padding:40px;
    margin-bottom:30px;
    box-shadow:0 15px 40px rgba(0,0,0,.25);
    animation:slideUp .8s ease;
}

.welcome h1{
    font-size:38px;
    margin-bottom:10px;
}

.welcome h1 span{
    color:#38bdf8;
}

.welcome p{
    color:#dbeafe;
    font-size:16px;
}

/* Statistics */

.stats{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:22px;
    margin-bottom:35px;
}

.stat-card{
    background:rgba(255,255,255,.08);
    backdrop-filter:blur(15px);
    border:1px solid rgba(255,255,255,.1);
    border-radius:20px;
    padding:25px;
    display:flex;
    align-items:center;
    gap:20px;
    transition:.3s;
    animation:fadeIn 1s ease;
}

.stat-card:hover{
    transform:translateY(-7px);
    box-shadow:0 12px 30px rgba(0,0,0,.25);
}

.stat-icon{
    width:55px;
    height:55px;
    display:flex;
    justify-content:center;
    align-items:center;
    border-radius:15px;
    background:rgba(56,189,248,.15);
    color:#38bdf8;
    font-size:24px;
}

.stat-info h3{
    font-size:25px;
}

.stat-info p{
    color:#dbeafe;
    font-size:14px;
    margin-top:3px;
}

/* Section Heading */

.section-title{
    margin-bottom:20px;
}

.section-title h2{
    font-size:28px;
}

.section-title p{
    color:#dbeafe;
    margin-top:5px;
}

/* Quick Actions */

.actions{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:25px;
}

.action-card{
    text-decoration:none;
    color:white;
    background:rgba(255,255,255,.08);
    backdrop-filter:blur(15px);
    border:1px solid rgba(255,255,255,.1);
    border-radius:22px;
    padding:30px;
    transition:.35s;
    animation:slideUp 1s ease;
}

.action-card:hover{
    transform:translateY(-10px);
    background:rgba(255,255,255,.13);
    box-shadow:0 15px 35px rgba(0,0,0,.3);
}

.action-icon{
    width:60px;
    height:60px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#38bdf8;
    color:#0f172a;
    border-radius:17px;
    font-size:25px;
    margin-bottom:20px;
}

.action-card h3{
    font-size:21px;
    margin-bottom:8px;
}

.action-card p{
    color:#dbeafe;
    font-size:14px;
    line-height:1.6;
}

.arrow{
    display:block;
    margin-top:20px;
    color:#38bdf8;
    font-weight:bold;
}

/* Footer */

footer{
    text-align:center;
    padding:30px;
    margin-top:60px;
    color:#dbeafe;
    background:rgba(255,255,255,.05);
}

/* Animations */

@keyframes slideUp{

    from{
        opacity:0;
        transform:translateY(40px);
    }

    to{
        opacity:1;
        transform:translateY(0);
    }

}

@keyframes fadeIn{

    from{
        opacity:0;
    }

    to{
        opacity:1;
    }

}

/* Responsive */

@media(max-width:850px){

    .stats{
        grid-template-columns:1fr;
    }

    .actions{
        grid-template-columns:1fr;
    }

    .welcome h1{
        font-size:30px;
    }

}

@media(max-width:600px){

    nav{
        padding:0 5%;
    }

    .student-label{
        display:none;
    }

    .container{
        width:90%;
        margin:30px auto;
    }

    .welcome{
        padding:30px 22px;
    }

    .welcome h1{
        font-size:27px;
    }

}

</style>

</head>

<body>

<!-- Navbar -->

<nav>

    <div class="logo">
        Intern<span>Link</span>
    </div>

    <div class="nav-right">

        <span class="student-label">
            <i class="fa-solid fa-user-graduate"></i>
            Student
        </span>

        <a href="../logout.php" class="logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
        </a>

    </div>

</nav>


<!-- Main Container -->

<div class="container">

    <!-- Welcome -->

    <section class="welcome">

        <h1>
            Welcome to <span>InternLink</span> 👋
        </h1>

        <p>
            Find the right internship, build your skills,
            and take the next step toward your career.
        </p>

    </section>


    <!-- Statistics -->

    <div class="stats">

        <div class="stat-card">

            <div class="stat-icon">
                <i class="fa-solid fa-briefcase"></i>
            </div>

            <div class="stat-info">
                <h3>Explore</h3>
                <p>Internship Opportunities</p>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                <i class="fa-solid fa-user"></i>
            </div>

            <div class="stat-info">
                <h3>Profile</h3>
                <p>Manage Your Profile</p>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                <i class="fa-solid fa-file-lines"></i>
            </div>

            <div class="stat-info">
                <h3>Applications</h3>
                <p>Track Your Applications</p>
            </div>

        </div>

    </div>


    <!-- Quick Actions -->

    <div class="section-title">

        <h2>Student Portal</h2>

        <p>
            Choose an option to continue
        </p>

    </div>


    <div class="actions">

        <!-- Profile -->

        <a href="profile.php" class="action-card">

            <div class="action-icon">
                <i class="fa-solid fa-user-graduate"></i>
            </div>

            <h3>My Profile</h3>

            <p>
                Create and manage your student profile,
                skills and personal information.
            </p>

            <span class="arrow">
                Manage Profile →
            </span>

        </a>


        <!-- Internships -->

        <a href="internships.php" class="action-card">

            <div class="action-icon">
                <i class="fa-solid fa-briefcase"></i>
            </div>

            <h3>Browse Internships</h3>

            <p>
                Search and explore internship opportunities
                offered by companies.
            </p>

            <span class="arrow">
                Find Internships →
            </span>

        </a>


        <!-- Applications -->

        <a href="applications.php" class="action-card">

            <div class="action-icon">
                <i class="fa-solid fa-chart-line"></i>
            </div>

            <h3>Track Applications</h3>

            <p>
                View your applications and check their
                current status and progress.
            </p>

            <span class="arrow">
                View Applications →
            </span>

        </a>

    </div>

</div>


<footer>

    © 2026 InternLink Portal | Student Dashboard

</footer>

</body>
</html>