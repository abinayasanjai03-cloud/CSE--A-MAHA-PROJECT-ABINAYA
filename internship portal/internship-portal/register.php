<?php

session_start();
include("config/database.php");

$message = "";
$message_type = "";

if (isset($_POST['register'])) {

    $firstname = trim($_POST['firstname'] ?? "");
    $lastname = trim($_POST['lastname'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";
    $confirm_password = $_POST['confirm_password'] ?? "";
    $role = $_POST['role'] ?? "";

    $name = $firstname . " " . $lastname;

    if (
        empty($firstname) ||
        empty($lastname) ||
        empty($email) ||
        empty($password) ||
        empty($confirm_password) ||
        empty($role)
    ) {
        $message = "Please fill in all fields.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } elseif (!in_array($role, ["student", "company"])) {

        $message = "Invalid account type selected.";
        $message_type = "error";

    } elseif (strlen($password) < 6) {

        $message = "Password must contain at least 6 characters.";
        $message_type = "error";

    } elseif ($password !== $confirm_password) {

        $message = "Passwords do not match.";
        $message_type = "error";

    } else {

        $email_safe = mysqli_real_escape_string($conn, $email);

        $check = mysqli_query(
            $conn,
            "SELECT id FROM users WHERE email='$email_safe'"
        );

        if (mysqli_num_rows($check) > 0) {

            $message = "An account with this email already exists.";
            $message_type = "error";

        } else {

            $password_hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $name_safe = mysqli_real_escape_string($conn, $name);
            $hash_safe = mysqli_real_escape_string($conn, $password_hash);

            $insert_user = mysqli_query(
                $conn,
                "INSERT INTO users
                (name, email, password, role)
                VALUES
                ('$name_safe', '$email_safe', '$hash_safe', '$role')"
            );

            if ($insert_user) {

                $user_id = mysqli_insert_id($conn);

                if ($role === "company") {

                    $company_name = $name_safe;

                    $insert_company = mysqli_query(
                        $conn,
                        "INSERT INTO companies
                        (user_id, company_name)
                        VALUES
                        ('$user_id', '$company_name')"
                    );

                    if (!$insert_company) {

                        mysqli_query(
                            $conn,
                            "DELETE FROM users WHERE id='$user_id'"
                        );

                        $message = "Unable to create company profile. Please try again.";
                        $message_type = "error";

                    } else {

                        header("Location: login.php?registered=1");
                        exit();
                    }

                } else {

                    header("Location: login.php?registered=1");
                    exit();
                }

            } else {

                $message = "Registration failed. Please try again.";
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

    <title>Create Account | InternLink</title>

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

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 40px 20px;
        }

        .page {
            width: 100%;
            max-width: 1050px;

            display: grid;
            grid-template-columns: 0.9fr 1.1fr;

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
                    rgba(37,99,235,0.25),
                    rgba(15,23,42,0.15)
                );

            border-right: 1px solid rgba(255,255,255,0.08);
        }

        .logo {
            color: white;
            text-decoration: none;
            font-size: 28px;
            font-weight: 750;
            margin-bottom: 45px;
        }

        .logo span {
            color: #38bdf8;
        }

        .left h1 {
            color: white;
            font-size: 42px;
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
            margin-bottom: 35px;
        }

        .benefits {
            list-style: none;
        }

        .benefits li {
            display: flex;
            align-items: center;
            gap: 12px;

            color: #cbd5e1;
            font-size: 14px;

            margin: 17px 0;
        }

        .benefits i {
            width: 28px;
            height: 28px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 8px;

            background: rgba(56,189,248,0.1);
            color: #38bdf8;

            font-size: 12px;
        }

        .right {
            padding: 45px;
            background: rgba(15,23,42,0.35);
        }

        .form-heading {
            margin-bottom: 28px;
        }

        .form-heading h2 {
            color: white;
            font-size: 28px;
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

        .message.error {
            color: #fca5a5;
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.25);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .field {
            margin-bottom: 17px;
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

        .input-box input,
        .input-box select {
            width: 100%;
            height: 48px;

            padding: 0 42px;

            color: #dbeafe;
            background: rgba(255,255,255,0.05);

            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 11px;

            outline: none;

            font-family: inherit;
            transition: 0.3s;
        }

        .input-box select {
            appearance: none;
            cursor: pointer;
        }

        .input-box select option {
            background: #172033;
            color: white;
        }

        .input-box input:focus,
        .input-box select:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56,189,248,0.08);
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

        .register-btn {
            width: 100%;
            height: 50px;

            margin-top: 5px;

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

        .register-btn:hover {
            background: #7dd3fc;
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(56,189,248,0.18);
        }

        .login-text {
            text-align: center;
            color: #64748b;
            font-size: 14px;
            margin-top: 22px;
        }

        .login-text a {
            color: #38bdf8;
            text-decoration: none;
            font-weight: 600;
        }

        .login-text a:hover {
            text-decoration: underline;
        }

        @media (max-width: 850px) {

            .page {
                grid-template-columns: 1fr;
                max-width: 600px;
            }

            .left {
                display: none;
            }

            .right {
                padding: 40px 30px;
            }

        }

        @media (max-width: 500px) {

            body {
                padding: 20px 12px;
            }

            .right {
                padding: 30px 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .form-heading h2 {
                font-size: 24px;
            }

        }

    </style>

</head>

<body>

<div class="page">

    <div class="left">

        <a href="index.php" class="logo">
            Intern<span>Link</span>
        </a>

        <h1>
            Start Your
            <span>Journey.</span>
        </h1>

        <p>
            Create your InternLink account and connect with
            internship opportunities or talented students.
        </p>

        <ul class="benefits">

            <li>
                <i class="fa-solid fa-user-graduate"></i>
                Discover internship opportunities
            </li>

            <li>
                <i class="fa-solid fa-file-circle-check"></i>
                Apply and track applications
            </li>

            <li>
                <i class="fa-solid fa-building"></i>
                Companies can post internships
            </li>

            <li>
                <i class="fa-solid fa-users"></i>
                Manage applicants efficiently
            </li>

        </ul>

    </div>

    <div class="right">

        <div class="form-heading">

            <h2>
                Create Account
            </h2>

            <p>
                Fill in your details to get started.
            </p>

        </div>

        <?php if ($message != ""): ?>

            <div class="message <?php echo $message_type; ?>">

                <i class="fa-solid fa-circle-exclamation"></i>

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="form-row">

                <div class="field">

                    <label>
                        First Name
                    </label>

                    <div class="input-box">

                        <i class="fa-solid fa-user"></i>

                        <input
                            type="text"
                            name="firstname"
                            placeholder="First name"
                            value="<?php echo htmlspecialchars($_POST['firstname'] ?? ''); ?>"
                            required
                        >

                    </div>

                </div>

                <div class="field">

                    <label>
                        Last Name
                    </label>

                    <div class="input-box">

                        <i class="fa-solid fa-user"></i>

                        <input
                            type="text"
                            name="lastname"
                            placeholder="Last name"
                            value="<?php echo htmlspecialchars($_POST['lastname'] ?? ''); ?>"
                            required
                        >

                    </div>

                </div>

            </div>

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
                    Account Type
                </label>

                <div class="input-box">

                    <i class="fa-solid fa-user-tag"></i>

                    <select name="role" required>

                        <option value="">
                            Select account type
                        </option>

                        <option
                            value="student"
                            <?php echo (($_POST['role'] ?? '') === 'student') ? 'selected' : ''; ?>
                        >
                            Student
                        </option>

                        <option
                            value="company"
                            <?php echo (($_POST['role'] ?? '') === 'company') ? 'selected' : ''; ?>
                        >
                            Company
                        </option>

                    </select>

                </div>

            </div>

            <div class="form-row">

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
                            placeholder="Minimum 6 characters"
                            required
                        >

                        <span
                            class="password-toggle"
                            onclick="togglePassword('password', this)"
                        >
                            <i class="fa-solid fa-eye"></i>
                        </span>

                    </div>

                </div>

                <div class="field">

                    <label>
                        Confirm Password
                    </label>

                    <div class="input-box">

                        <i class="fa-solid fa-lock"></i>

                        <input
                            type="password"
                            name="confirm_password"
                            id="confirm_password"
                            placeholder="Repeat password"
                            required
                        >

                        <span
                            class="password-toggle"
                            onclick="togglePassword('confirm_password', this)"
                        >
                            <i class="fa-solid fa-eye"></i>
                        </span>

                    </div>

                </div>

            </div>

            <button
                type="submit"
                name="register"
                class="register-btn"
            >
                <i class="fa-solid fa-user-plus"></i>
                Create Account
            </button>

        </form>

        <div class="login-text">

            Already have an account?

            <a href="login.php">
                Login here
            </a>

        </div>

    </div>

</div>

<script>

function togglePassword(id, element) {

    const input = document.getElementById(id);
    const icon = element.querySelector("i");

    if (input.type === "password") {

        input.type = "text";

        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");

    } else {

        input.type = "password";

        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");

    }

}

</script>

</body>
</html>