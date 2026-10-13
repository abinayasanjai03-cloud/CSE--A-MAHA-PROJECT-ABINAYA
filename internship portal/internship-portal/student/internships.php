<?php

session_start();
include("../config/database.php");

if(!isset($_SESSION['user_id']))
{
    header("Location: ../login.php");
    exit();
}

$search = "";

if(isset($_GET['search']))
{
    $search = $_GET['search'];
}

$search_safe = $conn->real_escape_string($search);

$sql = "SELECT * FROM internships
        WHERE title LIKE '%$search_safe%'";

try
{
    $result = $conn->query($sql);
}
catch(mysqli_sql_exception $e)
{
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Browse Internships - InternLink</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

/* =========================
   RESET
========================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}


/* =========================
   BODY
========================= */

body{
    min-height:100vh;
    background:linear-gradient(
        135deg,
        #0f172a,
        #1e293b,
        #2563eb
    );
    color:white;
    overflow-x:hidden;
}


/* =========================
   BACKGROUND GLOW
========================= */

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


/* =========================
   NAVBAR
========================= */

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
    align-items:center;
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


/* =========================
   MAIN CONTAINER
========================= */

.container{
    width:88%;
    max-width:1200px;

    margin:45px auto;
}


/* =========================
   PAGE HEADER
========================= */

.page-header{
    text-align:center;

    margin-bottom:35px;

    animation:slideUp .8s ease;
}


.page-header .icon{
    width:70px;
    height:70px;

    display:flex;
    justify-content:center;
    align-items:center;

    margin:0 auto 15px;

    border-radius:20px;

    background:rgba(56,189,248,.15);

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


/* =========================
   SEARCH BOX
========================= */

.search-box{
    max-width:750px;

    margin:0 auto 45px;

    padding:8px;

    display:flex;

    background:rgba(255,255,255,.08);

    backdrop-filter:blur(15px);

    border:1px solid rgba(255,255,255,.12);

    border-radius:18px;

    box-shadow:0 10px 30px rgba(0,0,0,.2);
}


.search-box input{
    flex:1;

    border:none;

    outline:none;

    padding:14px 18px;

    background:transparent;

    color:white;

    font-size:16px;
}


.search-box input::placeholder{
    color:#bfdbfe;
}


.search-btn{
    border:none;

    padding:0 25px;

    border-radius:13px;

    background:#38bdf8;

    color:#0f172a;

    font-size:15px;

    font-weight:bold;

    cursor:pointer;

    transition:.3s;
}


.search-btn:hover{
    transform:scale(1.04);

    box-shadow:0 6px 20px rgba(56,189,248,.25);
}


/* =========================
   RESULTS HEADER
========================= */

.results-heading{
    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:20px;
}


.results-heading h2{
    font-size:25px;
}


.results-heading p{
    color:#dbeafe;

    font-size:14px;
}


/* =========================
   INTERNSHIP GRID
========================= */

.internship-grid{
    display:grid;

    grid-template-columns:
        repeat(auto-fit,minmax(300px,1fr));

    gap:25px;
}


/* =========================
   INTERNSHIP CARD
========================= */

.internship-card{
    background:rgba(255,255,255,.08);

    backdrop-filter:blur(15px);

    border:1px solid rgba(255,255,255,.1);

    border-radius:22px;

    padding:28px;

    transition:.35s;

    animation:fadeIn .7s ease;

    box-shadow:
        0 10px 30px rgba(0,0,0,.18);
}


.internship-card:hover{
    transform:translateY(-8px);

    background:rgba(255,255,255,.12);

    box-shadow:
        0 18px 35px rgba(0,0,0,.3);
}


/* =========================
   CARD HEADER
========================= */

.card-top{
    display:flex;

    align-items:center;

    gap:15px;

    margin-bottom:20px;
}


.company-icon{
    width:52px;
    height:52px;

    display:flex;

    justify-content:center;
    align-items:center;

    border-radius:15px;

    background:rgba(56,189,248,.15);

    color:#38bdf8;

    font-size:22px;

    flex-shrink:0;
}


.card-top h3{
    font-size:21px;

    line-height:1.3;
}


/* =========================
   DESCRIPTION
========================= */

.description{
    color:#dbeafe;

    line-height:1.6;

    font-size:14px;

    min-height:68px;

    margin-bottom:20px;
}


/* =========================
   DETAILS
========================= */

.details{
    display:flex;

    flex-direction:column;

    gap:10px;

    padding:15px;

    margin-bottom:20px;

    border-radius:14px;

    background:rgba(255,255,255,.05);
}


.detail{
    display:flex;

    align-items:center;

    gap:10px;

    color:#dbeafe;

    font-size:14px;
}


.detail i{
    color:#38bdf8;

    width:18px;
}


/* =========================
   APPLY BUTTON
========================= */

.apply-btn{
    display:block;

    text-align:center;

    text-decoration:none;

    padding:13px;

    border-radius:12px;

    background:#38bdf8;

    color:#0f172a;

    font-weight:bold;

    transition:.3s;
}


.apply-btn:hover{
    transform:translateY(-2px);

    box-shadow:
        0 8px 20px rgba(56,189,248,.3);
}


/* =========================
   NO RESULTS
========================= */

.no-results{
    grid-column:1/-1;

    text-align:center;

    padding:60px 20px;

    background:rgba(255,255,255,.07);

    border:1px solid rgba(255,255,255,.1);

    border-radius:20px;
}


.no-results i{
    font-size:45px;

    color:#38bdf8;

    margin-bottom:15px;
}


.no-results h3{
    margin-bottom:8px;

    font-size:22px;
}


.no-results p{
    color:#dbeafe;
}


/* =========================
   FOOTER
========================= */

footer{
    text-align:center;

    padding:30px;

    margin-top:60px;

    color:#dbeafe;

    background:rgba(255,255,255,.05);
}


/* =========================
   ANIMATIONS
========================= */

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


/* =========================
   RESPONSIVE
========================= */

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

    .search-box{
        flex-direction:column;

        gap:8px;

        padding:10px;
    }

    .search-btn{
        padding:13px;
    }

    .results-heading{
        flex-direction:column;

        align-items:flex-start;

        gap:5px;
    }

}

</style>

</head>


<body>


<!-- =========================
     NAVBAR
========================= -->

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


<!-- =========================
     MAIN
========================= -->

<div class="container">


    <!-- Page Header -->

    <div class="page-header">

        <div class="icon">

            <i class="fa-solid fa-briefcase"></i>

        </div>


        <h1>

            Find Your
            <span>Internship</span>

        </h1>


        <p>

            Explore opportunities and take the next step
            toward your career.

        </p>

    </div>



    <!-- Search -->

    <form method="GET" class="search-box">

        <input
            type="text"
            name="search"
            value="<?= htmlspecialchars($search) ?>"
            placeholder="Search internship by title...">


        <button
            type="submit"
            class="search-btn">

            <i class="fa-solid fa-magnifying-glass"></i>

            Search

        </button>

    </form>



    <!-- Results Heading -->

    <div class="results-heading">

        <h2>
            Available Internships
        </h2>


        <?php if($search != ""){ ?>

            <p>

                Results for:

                <strong>
                    <?= htmlspecialchars($search) ?>
                </strong>

            </p>

        <?php } ?>

    </div>



    <!-- Internship Cards -->

    <div class="internship-grid">


    <?php

    if(mysqli_num_rows($result) > 0)
    {

        while($row=mysqli_fetch_assoc($result))
        {

    ?>



        <div class="internship-card">


            <div class="card-top">

                <div class="company-icon">

                    <i class="fa-solid fa-building"></i>

                </div>


                <h3>

                    <?= htmlspecialchars($row['title']) ?>

                </h3>

            </div>



            <p class="description">

                <?= htmlspecialchars($row['description']) ?>

            </p>



            <div class="details">


                <div class="detail">

                    <i class="fa-solid fa-location-dot"></i>

                    <span>

                        <?= htmlspecialchars($row['location']) ?>

                    </span>

                </div>



                <div class="detail">

                    <i class="fa-solid fa-money-bill-wave"></i>

                    <span>

                        Stipend:
                        <?= htmlspecialchars($row['stipend']) ?>

                    </span>

                </div>


            </div>



            <a
                href="apply.php?id=<?= $row['id'] ?>"
                class="apply-btn">

                <i class="fa-solid fa-paper-plane"></i>

                Apply Now

            </a>


        </div>



    <?php

        }

    }
    else
    {

    ?>


        <div class="no-results">

            <i class="fa-solid fa-magnifying-glass"></i>

            <h3>
                No internships found
            </h3>

            <p>
                Try searching with a different internship title.
            </p>

        </div>


    <?php

    }

    ?>


    </div>

</div>



<!-- Footer -->

<footer>

    © 2026 InternLink Portal | Internship Opportunities

</footer>


</body>

</html>