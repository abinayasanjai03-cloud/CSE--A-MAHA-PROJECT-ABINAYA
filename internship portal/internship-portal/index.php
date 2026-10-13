<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>InternLink | College Internship Portal</title>

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

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            color: #dbeafe;
            background: #0f172a;
            overflow-x: hidden;
        }

        .background-glow {
            position: fixed;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: rgba(37,99,235,0.16);
            filter: blur(100px);
            pointer-events: none;
            z-index: -1;
        }

        .glow-one {
            top: -180px;
            left: -150px;
        }

        .glow-two {
            right: -180px;
            top: 500px;
        }

        .navbar {
            height: 78px;
            padding: 0 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;

            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;

            background: rgba(15,23,42,0.72);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .logo {
            text-decoration: none;
            color: white;
            font-size: 27px;
            font-weight: 750;
            letter-spacing: -0.5px;
        }

        .logo span {
            color: #38bdf8;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-links a {
            text-decoration: none;
            color: #cbd5e1;
            padding: 10px 15px;
            border-radius: 10px;
            transition: 0.3s;
            font-size: 14px;
        }

        .nav-links a:hover {
            color: #38bdf8;
            background: rgba(56,189,248,0.08);
        }

        .login-btn {
            border: 1px solid rgba(56,189,248,0.25);
        }

        .register-btn {
            background: #38bdf8;
            color: #082f49 !important;
            font-weight: 700;
        }

        .register-btn:hover {
            background: #7dd3fc !important;
            color: #082f49 !important;
            transform: translateY(-2px);
        }

        .hero {
            min-height: 100vh;
            padding: 150px 7% 80px;

            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            align-items: center;
            gap: 60px;

            background:
                radial-gradient(
                    circle at 75% 40%,
                    rgba(37,99,235,0.18),
                    transparent 35%
                ),
                linear-gradient(
                    135deg,
                    #0f172a,
                    #111c35,
                    #172554
                );

            position: relative;
        }

        .hero-content {
            max-width: 700px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;

            padding: 8px 14px;
            border-radius: 30px;

            background: rgba(56,189,248,0.09);
            border: 1px solid rgba(56,189,248,0.2);

            color: #7dd3fc;
            font-size: 13px;
            margin-bottom: 22px;

            animation: fadeUp 0.7s ease;
        }

        .badge i {
            font-size: 11px;
        }

        .hero h1 {
            font-size: clamp(44px, 6vw, 76px);
            line-height: 1.04;
            color: white;
            letter-spacing: -2px;
            margin-bottom: 22px;

            animation: fadeUp 0.8s ease;
        }

        .hero h1 span {
            color: #38bdf8;
        }

        .hero-description {
            max-width: 620px;
            color: #94a3b8;
            font-size: 18px;
            line-height: 1.7;
            margin-bottom: 32px;

            animation: fadeUp 1s ease;
        }

        .hero-buttons {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;

            animation: fadeUp 1.1s ease;
        }

        .primary-btn,
        .secondary-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;

            padding: 14px 22px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 650;

            transition: 0.3s;
        }

        .primary-btn {
            background: #38bdf8;
            color: #082f49;
        }

        .primary-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(56,189,248,0.2);
        }

        .secondary-btn {
            color: #dbeafe;
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(255,255,255,0.05);
        }

        .secondary-btn:hover {
            background: rgba(255,255,255,0.09);
            transform: translateY(-3px);
        }

        .hero-visual {
            position: relative;
            min-height: 450px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dashboard-card {
            width: 390px;
            max-width: 100%;

            padding: 25px;

            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 24px;

            backdrop-filter: blur(18px);

            box-shadow:
                0 25px 70px rgba(0,0,0,0.35);

            transform: rotate(2deg);
            animation: floating 4s ease-in-out infinite;
        }

        .dash-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .dash-title {
            color: white;
            font-size: 17px;
            font-weight: 650;
        }

        .online {
            width: 10px;
            height: 10px;
            background: #22c55e;
            border-radius: 50%;
            box-shadow: 0 0 12px #22c55e;
        }

        .stat-box {
            padding: 20px;
            border-radius: 16px;
            background: rgba(15,23,42,0.55);
            margin-bottom: 14px;
        }

        .stat-label {
            color: #64748b;
            font-size: 12px;
            margin-bottom: 7px;
        }

        .stat-value {
            color: white;
            font-size: 29px;
            font-weight: 700;
        }

        .progress {
            height: 7px;
            border-radius: 10px;
            background: rgba(255,255,255,0.08);
            margin-top: 13px;
            overflow: hidden;
        }

        .progress span {
            display: block;
            width: 72%;
            height: 100%;
            background: #38bdf8;
            border-radius: inherit;
        }

        .floating-card {
            position: absolute;
            padding: 15px 18px;
            border-radius: 15px;

            background: rgba(15,23,42,0.8);
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(15px);

            box-shadow: 0 15px 35px rgba(0,0,0,0.3);

            animation: floating 4s ease-in-out infinite;
        }

        .floating-card i {
            color: #38bdf8;
            margin-right: 7px;
        }

        .floating-one {
            top: 55px;
            left: 0;
            animation-delay: 0.5s;
        }

        .floating-two {
            right: 0;
            bottom: 70px;
            animation-delay: 1s;
        }

        .section {
            padding: 100px 7%;
            background: #0f172a;
        }

        .section-heading {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 55px;
        }

        .section-heading span {
            color: #38bdf8;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .section-heading h2 {
            color: white;
            font-size: 38px;
            margin: 12px 0;
        }

        .section-heading p {
            color: #64748b;
            line-height: 1.7;
        }

        .role-grid {
            max-width: 1100px;
            margin: auto;

            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        .role-card {
            padding: 35px;
            border-radius: 22px;

            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);

            transition: 0.35s;
        }

        .role-card:hover {
            transform: translateY(-7px);
            border-color: rgba(56,189,248,0.3);
            background: rgba(255,255,255,0.07);
        }

        .role-icon {
            width: 58px;
            height: 58px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 16px;

            background: rgba(56,189,248,0.1);
            color: #38bdf8;

            font-size: 23px;
            margin-bottom: 20px;
        }

        .role-card h3 {
            color: white;
            font-size: 23px;
            margin-bottom: 12px;
        }

        .role-card p {
            color: #94a3b8;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .role-list {
            list-style: none;
        }

        .role-list li {
            color: #cbd5e1;
            margin: 10px 0;
            font-size: 14px;
        }

        .role-list i {
            color: #38bdf8;
            margin-right: 8px;
        }

        .features {
            max-width: 1150px;
            margin: auto;

            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .feature {
            padding: 28px;

            border-radius: 18px;

            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.07);

            transition: 0.3s;
        }

        .feature:hover {
            transform: translateY(-5px);
            border-color: rgba(56,189,248,0.25);
        }

        .feature-icon {
            width: 48px;
            height: 48px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: rgba(56,189,248,0.1);
            color: #38bdf8;

            border-radius: 13px;
            margin-bottom: 18px;
        }

        .feature h3 {
            color: white;
            font-size: 17px;
            margin-bottom: 8px;
        }

        .feature p {
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
        }

        .workflow {
            max-width: 1050px;
            margin: auto;

            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 10px;
        }

        .step {
            text-align: center;
            position: relative;
        }

        .step-number {
            width: 52px;
            height: 52px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin: auto auto 15px;

            border-radius: 50%;

            background: rgba(56,189,248,0.1);
            border: 1px solid rgba(56,189,248,0.25);

            color: #38bdf8;
            font-weight: 700;
        }

        .step h4 {
            color: white;
            font-size: 14px;
        }

        .step p {
            color: #64748b;
            font-size: 12px;
            margin-top: 5px;
        }

        .stats {
            padding: 70px 7%;

            background:
                linear-gradient(
                    135deg,
                    #172554,
                    #0f172a
                );

            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            text-align: center;
        }

        .stat-item h2 {
            color: #38bdf8;
            font-size: 38px;
        }

        .stat-item p {
            color: #64748b;
            margin-top: 5px;
        }

        .cta {
            padding: 100px 7%;
            text-align: center;
            background: #0f172a;
        }

        .cta-box {
            max-width: 900px;
            margin: auto;
            padding: 65px 30px;

            border-radius: 25px;

            background:
                linear-gradient(
                    135deg,
                    rgba(37,99,235,0.25),
                    rgba(56,189,248,0.08)
                );

            border: 1px solid rgba(56,189,248,0.18);
        }

        .cta h2 {
            color: white;
            font-size: 40px;
            margin-bottom: 14px;
        }

        .cta p {
            color: #94a3b8;
            max-width: 600px;
            margin: 0 auto 28px;
            line-height: 1.7;
        }

        footer {
            padding: 30px 7%;

            display: flex;
            justify-content: space-between;
            align-items: center;

            border-top: 1px solid rgba(255,255,255,0.07);

            background: #0b1120;
        }

        footer p {
            color: #64748b;
            font-size: 13px;
        }

        footer strong {
            color: #dbeafe;
        }

        .footer-links {
            display: flex;
            gap: 18px;
        }

        .footer-links a {
            color: #64748b;
            text-decoration: none;
            font-size: 13px;
        }

        .footer-links a:hover {
            color: #38bdf8;
        }

        @keyframes fadeUp {

            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }

        }

        @keyframes floating {

            0%, 100% {
                transform: translateY(0) rotate(2deg);
            }

            50% {
                transform: translateY(-12px) rotate(2deg);
            }

        }

        @media (max-width: 900px) {

            .hero {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .hero-description {
                margin-left: auto;
                margin-right: auto;
            }

            .hero-buttons {
                justify-content: center;
            }

            .hero-visual {
                min-height: 400px;
            }

            .role-grid {
                grid-template-columns: 1fr;
            }

            .features {
                grid-template-columns: repeat(2, 1fr);
            }

            .workflow {
                grid-template-columns: repeat(3, 1fr);
                row-gap: 35px;
            }

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 600px) {

            .navbar {
                padding: 0 5%;
            }

            .nav-links a:not(.register-btn) {
                display: none;
            }

            .hero {
                padding-left: 5%;
                padding-right: 5%;
            }

            .hero h1 {
                font-size: 45px;
            }

            .hero-description {
                font-size: 16px;
            }

            .dashboard-card {
                width: 320px;
            }

            .floating-one {
                left: -10px;
            }

            .floating-two {
                right: -10px;
            }

            .section {
                padding: 75px 5%;
            }

            .section-heading h2 {
                font-size: 30px;
            }

            .features {
                grid-template-columns: 1fr;
            }

            .workflow {
                grid-template-columns: repeat(2, 1fr);
            }

            .stats {
                grid-template-columns: 1fr 1fr;
                padding-left: 5%;
                padding-right: 5%;
            }

            .stat-item h2 {
                font-size: 30px;
            }

            .cta h2 {
                font-size: 30px;
            }

            footer {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

        }

    </style>

</head>

<body>

<div class="background-glow glow-one"></div>
<div class="background-glow glow-two"></div>

<nav class="navbar">

    <a href="index.php" class="logo">
        Intern<span>Link</span>
    </a>

    <div class="nav-links">

        <a href="#features">
            Features
        </a>

        <a href="#how-it-works">
            How It Works
        </a>

        <a href="login.php" class="login-btn">
            Login
        </a>

        <a href="register.php" class="register-btn">
            Register
        </a>

    </div>

</nav>

<section class="hero">

    <div class="hero-content">

        <div class="badge">
            <i class="fa-solid fa-circle-check"></i>
            College Internship Application Portal
        </div>

        <h1>
            Connect.
            <span>Apply.</span>
            Grow.
        </h1>

        <p class="hero-description">
            InternLink makes internship discovery and recruitment
            simple for students and companies — from finding the
            right opportunity to tracking every application.
        </p>

        <div class="hero-buttons">

            <a href="register.php" class="primary-btn">
                Get Started
                <i class="fa-solid fa-arrow-right"></i>
            </a>

            <a href="#how-it-works" class="secondary-btn">
                <i class="fa-solid fa-play"></i>
                Explore Platform
            </a>

        </div>

    </div>

    <div class="hero-visual">

        <div class="floating-card floating-one">
            <i class="fa-solid fa-circle-check"></i>
            Application Submitted
        </div>

        <div class="dashboard-card">

            <div class="dash-header">

                <div class="dash-title">
                    Application Overview
                </div>

                <div class="online"></div>

            </div>

            <div class="stat-box">

                <div class="stat-label">
                    Applications
                </div>

                <div class="stat-value">
                    08
                </div>

                <div class="progress">
                    <span></span>
                </div>

            </div>

            <div class="stat-box">

                <div class="stat-label">
                    Current Status
                </div>

                <div class="stat-value" style="font-size:22px;">
                    Shortlisted
                </div>

            </div>

            <div class="stat-box">

                <div class="stat-label">
                    Next Step
                </div>

                <div class="stat-value" style="font-size:19px;">
                    Interview
                </div>

            </div>

        </div>

        <div class="floating-card floating-two">
            <i class="fa-solid fa-briefcase"></i>
            New Internship
        </div>

    </div>

</section>

<section class="section">

    <div class="section-heading">

        <span>Built For Everyone</span>

        <h2>
            One Platform. Two Powerful Experiences.
        </h2>

        <p>
            InternLink brings students and companies together
            through a simple internship management experience.
        </p>

    </div>

    <div class="role-grid">

        <div class="role-card">

            <div class="role-icon">
                <i class="fa-solid fa-user-graduate"></i>
            </div>

            <h3>
                For Students
            </h3>

            <p>
                Discover opportunities, build your profile,
                apply for internships and track your application
                journey from one place.
            </p>

            <ul class="role-list">

                <li>
                    <i class="fa-solid fa-check"></i>
                    Create your profile
                </li>

                <li>
                    <i class="fa-solid fa-check"></i>
                    Search internships
                </li>

                <li>
                    <i class="fa-solid fa-check"></i>
                    Apply with your resume
                </li>

                <li>
                    <i class="fa-solid fa-check"></i>
                    Track application status
                </li>

            </ul>

        </div>

        <div class="role-card">

            <div class="role-icon">
                <i class="fa-solid fa-building"></i>
            </div>

            <h3>
                For Companies
            </h3>

            <p>
                Post internship opportunities, discover talented
                students and manage applications efficiently.
            </p>

            <ul class="role-list">

                <li>
                    <i class="fa-solid fa-check"></i>
                    Post internship opportunities
                </li>

                <li>
                    <i class="fa-solid fa-check"></i>
                    View student applications
                </li>

                <li>
                    <i class="fa-solid fa-check"></i>
                    Review candidate profiles
                </li>

                <li>
                    <i class="fa-solid fa-check"></i>
                    Shortlist or reject applicants
                </li>

            </ul>

        </div>

    </div>

</section>

<section class="section" id="features">

    <div class="section-heading">

        <span>Platform Features</span>

        <h2>
            Everything You Need
        </h2>

        <p>
            Designed to make the internship journey
            easier, clearer and more organized.
        </p>

    </div>

    <div class="features">

        <div class="feature">

            <div class="feature-icon">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>

            <h3>
                Internship Discovery
            </h3>

            <p>
                Search and explore internship opportunities
                based on your interests and skills.
            </p>

        </div>

        <div class="feature">

            <div class="feature-icon">
                <i class="fa-solid fa-user-pen"></i>
            </div>

            <h3>
                Student Profiles
            </h3>

            <p>
                Showcase your college, department, skills,
                resume and contact information.
            </p>

        </div>

        <div class="feature">

            <div class="feature-icon">
                <i class="fa-solid fa-paper-plane"></i>
            </div>

            <h3>
                Easy Applications
            </h3>

            <p>
                Apply for internships and keep your application
                information organized.
            </p>

        </div>

        <div class="feature">

            <div class="feature-icon">
                <i class="fa-solid fa-chart-line"></i>
            </div>

            <h3>
                Application Tracking
            </h3>

            <p>
                Follow your application from Applied to
                Interview, Selected or Rejected.
            </p>

        </div>

        <div class="feature">

            <div class="feature-icon">
                <i class="fa-solid fa-users"></i>
            </div>

            <h3>
                Applicant Management
            </h3>

            <p>
                Companies can review applicants and manage
                candidate progress efficiently.
            </p>

        </div>

        <div class="feature">

            <div class="feature-icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>

            <h3>
                Secure Authentication
            </h3>

            <p>
                Role-based login keeps student and company
                functionality separated.
            </p>

        </div>

    </div>

</section>

<section class="section" id="how-it-works">

    <div class="section-heading">

        <span>Application Journey</span>

        <h2>
            From Application to Outcome
        </h2>

        <p>
            Every application follows a clear and transparent
            status journey.
        </p>

    </div>

    <div class="workflow">

        <div class="step">

            <div class="step-number">
                01
            </div>

            <h4>
                Applied
            </h4>

            <p>
                Application submitted
            </p>

        </div>

        <div class="step">

            <div class="step-number">
                02
            </div>

            <h4>
                Review
            </h4>

            <p>
                Company reviews
            </p>

        </div>

        <div class="step">

            <div class="step-number">
                03
            </div>

            <h4>
                Shortlisted
            </h4>

            <p>
                Candidate selected
            </p>

        </div>

        <div class="step">

            <div class="step-number">
                04
            </div>

            <h4>
                Interview
            </h4>

            <p>
                Interview stage
            </p>

        </div>

        <div class="step">

            <div class="step-number">
                05
            </div>

            <h4>
                Selected
            </h4>

            <p>
                Internship secured
            </p>

        </div>

        <div class="step">

            <div class="step-number">
                06
            </div>

            <h4>
                Rejected
            </h4>

            <p>
                Application closed
            </p>

        </div>

    </div>

</section>

<section class="stats">

    <div class="stat-item">

        <h2>
            01
        </h2>

        <p>
            Unified Platform
        </p>

    </div>

    <div class="stat-item">

        <h2>
            02
        </h2>

        <p>
            User Roles
        </p>

    </div>

    <div class="stat-item">

        <h2>
            06
        </h2>

        <p>
            Application Stages
        </p>

    </div>

    <div class="stat-item">

        <h2>
            24/7
        </h2>

        <p>
            Access
        </p>

    </div>

</section>

<section class="cta">

    <div class="cta-box">

        <h2>
            Ready to Find Your Next Opportunity?
        </h2>

        <p>
            Join InternLink and make the internship journey
            simpler — whether you're looking for an opportunity
            or searching for talented students.
        </p>

        <a href="register.php" class="primary-btn">
            Create Your Account
            <i class="fa-solid fa-arrow-right"></i>
        </a>

    </div>

</section>

<footer>

    <p>
        © <?php echo date("Y"); ?>
        <strong>InternLink</strong>.
        College Internship Application Portal.
    </p>

    <div class="footer-links">

        <a href="login.php">
            Login
        </a>

        <a href="register.php">
            Register
        </a>

    </div>

</footer>

</body>

</html>