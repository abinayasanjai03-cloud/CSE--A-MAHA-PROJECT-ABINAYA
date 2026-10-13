<?php

session_start();
include("config/database.php");

$message = "";
$message_type = "";

if (isset($_GET['registered']) && $_GET['registered'] == "1") {
    $message = "Account created successfully. Please login.";
    $message_type = "success";
}

if (isset($_POST['login'])) {

    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";

    if (empty($email) || empty($password)) {

        $message = "Please enter your email and password.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } else {

        $email_safe = mysqli_real_escape_string($conn, $email);

        $query = mysqli_query(
            $conn,
            "SELECT id, name, email, password, role
             FROM users
             WHERE email='$email_safe'
             LIMIT 1"
        );

        if (!$query) {

            $message = "Something went wrong. Please try again.";
            $message_type = "error";

        } elseif (mysqli_num_rows($query) == 0) {

            $message = "No account found with this email.";
            $message_type = "error";

        } else {

            $user = mysqli_fetch_assoc($query);

            if (password_verify($password, $user['password'])) {

                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === "student") {

                    header("Location: student/dashboard.php");
                    exit();

                } elseif ($user['role'] === "company") {

                    header("Location: company/dashboard.php");
                    exit();

                } else {

                    session_unset();
                    session_destroy();

                    $message = "Invalid account role.";
                    $message_type = "error";
                }

            } else {

                $message = "Incorrect password.";
                $message_type = "error";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | InternLink</title>

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

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 30px 20px;

            background:
                radial-gradient(
                    circle at 15% 20%,
                    rgba(37,99,235,0.25),
                    transparent 30%
                ),
                radial-gradient(
                    circle at 85% 80%,
                    rgba(56,189,248,0.15),
                    transparent 30%
                ),
                linear-gradient(
                    135deg,
                    #0f172a,
                    #1e293b,
                    #172554
                );
        }

        .background-circle {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(80px);
            z-index: -1;
        }

        .circle-one {
            width: 300px;
            height: 300px;
            background: rgba(37,99,235,0.13);
            top: -100px;
            left: -100px;
        }

        .circle-two {
            width: 350px;
            height: 350px;
            background: rgba(56,189,248,0.08);
            bottom: -150px;
            right: -100px;
        }

        .page {
            width: 100%;
            max-width: 1000px;

            display: grid;
            grid-template-columns: 1fr 1fr;

            background: rgba(255,255,255,0.06);

            border: 1px solid rgba(255,255,255,0.1);

            border-radius: 28px;

            overflow: hidden;

            backdrop-filter: blur(20px);

            box-shadow:
                0 30px 80px rgba(0,0,0,0.35);
        }

        .left {
            padding: 55px 45px;

            display: flex;
            flex-direction: column;
            justify-content: center;

            background:
                linear-gradient(
                    145deg,
                    rgba(37,99,235,0.24),
                    rgba(15,23,42,0.15)
                );

            border-right: 1px solid rgba(255,255,255,0.08);
        }

        .logo {
            color: white;
            text-decoration: none;

            font-size: 28px;
            font-weight: 750;

            margin-bottom: 55px;
        }

        .logo span {
            color: #38bdf8;
        }

        .left h1 {
            color: white;

            font-size: 43px;
            line-height: 1.1;

            margin-bottom: 18px;
        }

        .left h1 span {
            color: #38bdf8;
        }

        .left > p {
            color: #94a3b8;

            line-height: 1.7;
            font-size: 15px;

            max-width: 390px;

            margin-bottom: 35px;
        }

        .features {
            display: grid;
            gap: 15px;
        }

        .feature {
            display: flex;
            align-items: center;
            gap: 12px;

            color: #cbd5e1;

            font-size: 14px;
        }

        .feature-icon {
            width: 32px;
            height: 32px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 9px;

            background: rgba(56,189,248,0.1);

            color: #38bdf8;

            font-size: 13px;
        }

        .right {
            padding: 55px 45px;

            display: flex;
            flex-direction: column;
            justify-content: center;

            background: rgba(15,23,42,0.35);
        }

        .form-heading {
            margin-bottom: 28px;
        }

        .form-heading h2 {
            color: white;
            font-size: 30px;
            margin-bottom: 7px;
        }

        .form-heading p {
            color: #64748b;
            font-size: 14px;
        }

        .message {
            padding: 13px 15px;

            border-radius: 11px;

            margin-bottom: 20px;

            font-size: 14px;
        }

        .message.success {
            color: #86efac;

            background: rgba(34,197,94,0.1);

            border: 1px solid rgba(34,197,94,0.25);
        }

        .message.error {
            color: #fca5a5;

            background: rgba(239,68,68,0.1);

            border: 1px solid rgba(239,68,68,0.25);
        }

        .field {
            margin-bottom: 19px;
        }

        .field label {
            display: block;

            color: #cbd5e1;

            font-size: 13px;

            margin-bottom: 7px;
        }

        .input-box {
            position: relative;
        }

        .input-box > i {
            position: absolute;

            left: 14px;
            top: 50%;

            transform: translateY(-50%);

            color: #64748b;

            font-size: 14px;
        }

        .input-box input {
            width: 100%;
            height: 50px;

            padding: 0 44px;

            color: #dbeafe;

            background: rgba(255,255,255,0.05);

            border: 1px solid rgba(255,255,255,0.1);

            border-radius: 11px;

            outline: none;

            font-family: inherit;

            transition: 0.3s;
        }

        .input-box input::placeholder {
            color: #475569;
        }

        .input-box input:focus {
            border-color: #38bdf8;

            box-shadow:
                0 0 0 3px rgba(56,189,248,0.08);
        }

        .password-toggle {
            position: absolute;

            right: 14px;
            top: 50%;

            transform: translateY(-50%);

            color: #64748b;

            cursor: pointer;

            font-size: 14px;
        }

        .password-toggle:hover {
            color: #38bdf8;
        }

        .options {
            display: flex;
            justify-content: flex-end;

            margin-top: -5px;
            margin-bottom: 22px;
        }

        .forgot {
            color: #38bdf8;

            text-decoration: none;

            font-size: 13px;
        }

        .forgot:hover {
            text-decoration: underline;
        }

        .login-btn {
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

        .login-btn:hover {
            background: #7dd3fc;

            transform: translateY(-2px);

            box-shadow:
                0 12px 25px rgba(56,189,248,0.18);
        }

        .register-text {
            text-align: center;

            color: #64748b;

            font-size: 14px;

            margin-top: 23px;
        }

        .register-text a {
            color: #38bdf8;

            text-decoration: none;

            font-weight: 600;
        }

        .register-text a:hover {
            text-decoration: underline;
        }

        .back-home {
            display: block;

            text-align: center;

            margin-top: 18px;

            color: #475569;

            font-size: 13px;

            text-decoration: none;
        }

        .back-home:hover {
            color: #38bdf8;
        }

        @media (max-width: 800px) {

            .page {
                grid-template-columns: 1fr;
                max-width: 520px;
            }

            .left {
                display: none;
            }

            .right {
                padding: 45px 35px;
            }

        }

        @media (max-width: 500px) {

            body {
                padding: 20px 12px;
            }

            .right {
                padding: 35px 22px;
            }

            .form-heading h2 {
                font-size: 26px;
            }

        }

    </style>

</head>

<body>

<div class="background-circle circle-one"></div>
<div class="background-circle circle-two"></div>

<div class="page">

    <div class="left">

        <a href="index.php" class="logo">
            Intern<span>Link</span>
        </a>

        <h1>
            Welcome
            <span>Back.</span>
        </h1>

        <p>
            Continue your internship journey. Find opportunities,
            manage applications and connect with the right people.
        </p>

        <div class="features">

            <div class="feature">

                <div class="feature-icon">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>

                Discover internship opportunities

            </div>

            <div class="feature">

                <div class="feature-icon">
                    <i class="fa-solid fa-paper-plane"></i>
                </div>

                Apply and track your applications

            </div>

            <div class="feature">

                <div class="feature-icon">
                    <i class="fa-solid fa-building"></i>
                </div>

                Manage internship recruitment

            </div>

            <div class="feature">

                <div class="feature-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>

                Secure role-based access

            </div>

        </div>

    </div>

    <div class="right">

        <div class="form-heading">

            <h2>
                Sign In
            </h2>

            <p>
                Enter your credentials to access InternLink.
            </p>

        </div>

        <?php if ($message != ""): ?>

            <div class="message <?php echo $message_type; ?>">

                <i class="fa-solid
                    <?php
                    echo $message_type === "success"
                        ? "fa-circle-check"
                        : "fa-circle-exclamation";
                    ?>">
                </i>

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="field">

                <label>
                    Email Address
                </label>

                <div class="input-box">

                    <i class="fa-solid fa-envelope"></i>

                    <input
                        type="email"
                        name="email"
                        placeholder="you@example.com"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        required
                    >

                </div>

            </div>

            <div class="field">

                <label>
                    Password
                </label>

                <div class="input-box">

                    <i class="fa-solid fa-lock"></i>

                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Enter your password"
                        required
                    >

                    <span
                        class="password-toggle"
                        onclick="togglePassword()"
                    >
                        <i class="fa-solid fa-eye"></i>
                    </span>

                </div>

            </div>

            <div class="options">

                <a href="forgot-password.php" class="forgot">
                    Forgot Password?
                </a>

            </div>

            <button
                type="submit"
                name="login"
                class="login-btn"
            >
                <i class="fa-solid fa-right-to-bracket"></i>
                Sign In
            </button>

        </form>

        <div class="register-text">

            Don't have an account?

            <a href="register.php">
                Create one
            </a>

        </div>

        <a href="index.php" class="back-home">
            <i class="fa-solid fa-arrow-left"></i>
            Back to Home
        </a>

    </div>

</div>

<script>

function togglePassword() {

    const password = document.getElementById("password");
    const icon = document.querySelector(".password-toggle i");

    if (password.type === "password") {

        password.type = "text";

        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");

    } else {

        password.type = "password";

        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");

    }

}

</script>

</body>

</html>