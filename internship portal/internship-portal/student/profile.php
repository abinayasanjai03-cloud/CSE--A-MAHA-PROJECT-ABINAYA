<?php

session_start();
include("../config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$success = "";
$error = "";

$upload_dir = "../uploads/";

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$profile_query = mysqli_query(
    $conn,
    "SELECT id, college, department, phone, skills, resume
     FROM students
     WHERE user_id='$user_id'
     LIMIT 1"
);

$profile = null;

if ($profile_query && mysqli_num_rows($profile_query) > 0) {
    $profile = mysqli_fetch_assoc($profile_query);
}

if (isset($_POST['save'])) {

    $college = trim($_POST['college'] ?? "");
    $department = trim($_POST['department'] ?? "");
    $phone = trim($_POST['phone'] ?? "");
    $skills = trim($_POST['skills'] ?? "");

    if (
        empty($college) ||
        empty($department) ||
        empty($skills)
    ) {

        $error = "Please fill in all required fields.";

    } else {

        $resume_name = $profile['resume'] ?? "";

        if (
            isset($_FILES['resume']) &&
            $_FILES['resume']['error'] !== UPLOAD_ERR_NO_FILE
        ) {

            if ($_FILES['resume']['error'] !== UPLOAD_ERR_OK) {

                $error = "There was a problem uploading your resume.";

            } else {

                $original_name = $_FILES['resume']['name'];
                $tmp_name = $_FILES['resume']['tmp_name'];

                $extension = strtolower(
                    pathinfo($original_name, PATHINFO_EXTENSION)
                );

                $allowed_extensions = [
                    "pdf",
                    "doc",
                    "docx"
                ];

                if (!in_array($extension, $allowed_extensions)) {

                    $error = "Only PDF, DOC and DOCX files are allowed.";

                } elseif ($_FILES['resume']['size'] > 5 * 1024 * 1024) {

                    $error = "Resume file must be smaller than 5 MB.";

                } else {

                    $new_filename =
                        "resume_" .
                        $user_id .
                        "_" .
                        time() .
                        "." .
                        $extension;

                    $destination = $upload_dir . $new_filename;

                    if (move_uploaded_file($tmp_name, $destination)) {

                        if (
                            !empty($resume_name) &&
                            file_exists($upload_dir . $resume_name)
                        ) {
                            unlink($upload_dir . $resume_name);
                        }

                        $resume_name = $new_filename;

                    } else {

                        $error = "Unable to save the resume file.";

                    }
                }
            }
        }

        if ($error === "") {

            $college_safe = mysqli_real_escape_string($conn, $college);
            $department_safe = mysqli_real_escape_string($conn, $department);
            $phone_safe = mysqli_real_escape_string($conn, $phone);
            $skills_safe = mysqli_real_escape_string($conn, $skills);
            $resume_safe = mysqli_real_escape_string($conn, $resume_name);

            if ($profile) {

                $update = mysqli_query(
                    $conn,
                    "UPDATE students
                     SET
                        college='$college_safe',
                        department='$department_safe',
                        phone='$phone_safe',
                        skills='$skills_safe',
                        resume='$resume_safe'
                     WHERE user_id='$user_id'"
                );

                if ($update) {
                    $success = "Profile updated successfully.";
                } else {
                    $error = "Unable to update your profile.";
                }

            } else {

                $insert = mysqli_query(
                    $conn,
                    "INSERT INTO students
                    (user_id, college, department, phone, skills, resume)
                    VALUES
                    ('$user_id',
                     '$college_safe',
                     '$department_safe',
                     '$phone_safe',
                     '$skills_safe',
                     '$resume_safe')"
                );

                if ($insert) {
                    $success = "Profile created successfully.";
                } else {
                    $error = "Unable to create your profile.";
                }
            }

            $profile_query = mysqli_query(
                $conn,
                "SELECT id, college, department, phone, skills, resume
                 FROM students
                 WHERE user_id='$user_id'
                 LIMIT 1"
            );

            if ($profile_query && mysqli_num_rows($profile_query) > 0) {
                $profile = mysqli_fetch_assoc($profile_query);
            }
        }
    }
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

    <title>My Profile | InternLink</title>

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
                    rgba(37,99,235,0.22),
                    transparent 30%
                ),
                radial-gradient(
                    circle at 90% 90%,
                    rgba(56,189,248,0.12),
                    transparent 30%
                ),
                linear-gradient(
                    135deg,
                    #0f172a,
                    #1e293b,
                    #172554
                );

            padding-bottom: 50px;
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

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;

            padding: 10px 16px;

            color: #dbeafe;
            text-decoration: none;

            border: 1px solid rgba(255,255,255,0.18);

            border-radius: 10px;

            transition: 0.3s;
        }

        .back-btn:hover {
            color: #38bdf8;
            border-color: rgba(56,189,248,0.35);
            background: rgba(56,189,248,0.08);
        }

        .container {
            width: 90%;
            max-width: 850px;

            margin: 45px auto;
        }

        .profile-card {
            background: rgba(255,255,255,0.07);

            border: 1px solid rgba(255,255,255,0.1);

            border-radius: 24px;

            padding: 38px;

            backdrop-filter: blur(18px);

            box-shadow:
                0 20px 55px rgba(0,0,0,0.3);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .profile-icon {
            width: 75px;
            height: 75px;

            margin: 0 auto 16px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background: rgba(56,189,248,0.1);

            border: 1px solid rgba(56,189,248,0.18);

            color: #38bdf8;

            font-size: 30px;
        }

        .profile-header h1 {
            color: white;
            font-size: 30px;
            margin-bottom: 8px;
        }

        .profile-header p {
            color: #94a3b8;
            font-size: 14px;
            line-height: 1.6;
        }

        .message {
            display: flex;
            align-items: center;
            gap: 10px;

            padding: 13px 15px;

            border-radius: 11px;

            margin-bottom: 22px;

            font-size: 14px;
        }

        .success {
            color: #86efac;
            background: rgba(34,197,94,0.09);
            border: 1px solid rgba(34,197,94,0.25);
        }

        .error {
            color: #fca5a5;
            background: rgba(239,68,68,0.09);
            border: 1px solid rgba(239,68,68,0.25);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .form-group {
            margin-bottom: 5px;
        }

        .full {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;

            color: #cbd5e1;

            font-size: 13px;
            font-weight: 600;

            margin-bottom: 8px;
        }

        .input-box {
            position: relative;
        }

        .input-box > i {
            position: absolute;

            left: 15px;
            top: 50%;

            transform: translateY(-50%);

            color: #38bdf8;

            font-size: 14px;
        }

        input,
        textarea {
            width: 100%;

            border: 1px solid rgba(255,255,255,0.1);

            outline: none;

            border-radius: 11px;

            background: rgba(255,255,255,0.05);

            color: #dbeafe;

            font-family: inherit;
            font-size: 14px;

            transition: 0.3s;
        }

        input {
            height: 48px;
            padding: 0 15px 0 43px;
        }

        textarea {
            min-height: 125px;

            padding: 14px;

            resize: vertical;
        }

        input::placeholder,
        textarea::placeholder {
            color: #64748b;
        }

        input:focus,
        textarea:focus {
            border-color: #38bdf8;

            box-shadow:
                0 0 0 3px rgba(56,189,248,0.08);
        }

        .file-box {
            padding: 24px;

            text-align: center;

            border: 2px dashed rgba(56,189,248,0.35);

            border-radius: 15px;

            background: rgba(56,189,248,0.04);

            transition: 0.3s;
        }

        .file-box:hover {
            border-color: #38bdf8;
            background: rgba(56,189,248,0.08);
        }

        .file-icon {
            font-size: 30px;

            color: #38bdf8;

            margin-bottom: 9px;
        }

        .file-box h3 {
            color: #dbeafe;

            font-size: 15px;

            margin-bottom: 5px;
        }

        .file-box p {
            color: #64748b;

            font-size: 12px;

            margin-bottom: 15px;
        }

        .file-box input {
            height: auto;

            padding: 0;

            border: none;

            background: transparent;

            box-shadow: none;

            color: #94a3b8;

            cursor: pointer;
        }

        .file-box input:focus {
            box-shadow: none;
        }

        .current-file {
            margin-top: 13px;

            padding: 10px 12px;

            border-radius: 9px;

            background: rgba(34,197,94,0.07);

            border: 1px solid rgba(34,197,94,0.15);

            color: #86efac;

            font-size: 12px;
        }

        .submit-btn {
            width: 100%;

            height: 50px;

            margin-top: 25px;

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

        .submit-btn:hover {
            background: #7dd3fc;

            transform: translateY(-2px);

            box-shadow:
                0 12px 25px rgba(56,189,248,0.18);
        }

        .required {
            color: #f87171;
        }

        footer {
            text-align: center;

            color: #475569;

            font-size: 12px;

            margin-top: 30px;
        }

        @media (max-width: 650px) {

            .navbar {
                padding: 0 5%;
            }

            .container {
                width: 92%;
                margin-top: 30px;
            }

            .profile-card {
                padding: 25px 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .full {
                grid-column: auto;
            }

            .profile-header h1 {
                font-size: 25px;
            }

        }

    </style>

</head>

<body>

<nav class="navbar">

    <a href="dashboard.php" class="logo">
        Intern<span>Link</span>
    </a>

    <a href="dashboard.php" class="back-btn">
        <i class="fa-solid fa-arrow-left"></i>
        Dashboard
    </a>

</nav>

<div class="container">

    <div class="profile-card">

        <div class="profile-header">

            <div class="profile-icon">
                <i class="fa-solid fa-user-graduate"></i>
            </div>

            <h1>
                <?php echo $profile ? "Update Your Profile" : "Create Your Profile"; ?>
            </h1>

            <p>
                Add your academic details, skills and resume
                to make your profile internship-ready.
            </p>

        </div>

        <?php if ($success !== ""): ?>

            <div class="message success">
                <i class="fa-solid fa-circle-check"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>

        <?php endif; ?>

        <?php if ($error !== ""): ?>

            <div class="message error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>

        <form
            method="POST"
            enctype="multipart/form-data"
        >

            <div class="form-grid">

                <div class="form-group">

                    <label>
                        <i class="fa-solid fa-building-columns"></i>
                        College / University
                        <span class="required">*</span>
                    </label>

                    <div class="input-box">

                        <i class="fa-solid fa-school"></i>

                        <input
                            type="text"
                            name="college"
                            placeholder="Enter your college"
                            value="<?php
                                echo htmlspecialchars(
                                    $profile['college'] ?? ''
                                );
                            ?>"
                            required
                        >

                    </div>

                </div>

                <div class="form-group">

                    <label>
                        <i class="fa-solid fa-graduation-cap"></i>
                        Department
                        <span class="required">*</span>
                    </label>

                    <div class="input-box">

                        <i class="fa-solid fa-code"></i>

                        <input
                            type="text"
                            name="department"
                            placeholder="Example: Computer Science"
                            value="<?php
                                echo htmlspecialchars(
                                    $profile['department'] ?? ''
                                );
                            ?>"
                            required
                        >

                    </div>

                </div>

                <div class="form-group">

                    <label>
                        <i class="fa-solid fa-phone"></i>
                        Phone
                    </label>

                    <div class="input-box">

                        <i class="fa-solid fa-phone"></i>

                        <input
                            type="tel"
                            name="phone"
                            placeholder="Enter your phone number"
                            value="<?php
                                echo htmlspecialchars(
                                    $profile['phone'] ?? ''
                                );
                            ?>"
                        >

                    </div>

                </div>

                <div class="form-group">

                    <label>
                        <i class="fa-solid fa-lightbulb"></i>
                        Skills
                        <span class="required">*</span>
                    </label>

                    <div class="input-box">

                        <i class="fa-solid fa-star"></i>

                        <input
                            type="text"
                            name="skills"
                            placeholder="Java, Python, HTML, SQL..."
                            value="<?php
                                echo htmlspecialchars(
                                    $profile['skills'] ?? ''
                                );
                            ?>"
                            required
                        >

                    </div>

                </div>

                <div class="form-group full">

                    <label>
                        <i class="fa-solid fa-file-lines"></i>
                        Resume
                        <span class="required">*</span>
                    </label>

                    <div class="file-box">

                        <div class="file-icon">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>

                        <h3>
                            Upload Your Resume
                        </h3>

                        <p>
                            PDF, DOC or DOCX • Maximum 5 MB
                        </p>

                        <input
                            type="file"
                            name="resume"
                            accept=".pdf,.doc,.docx"
                            <?php echo $profile ? '' : 'required'; ?>
                        >

                        <?php if (!empty($profile['resume'])): ?>

                            <div class="current-file">

                                <i class="fa-solid fa-circle-check"></i>

                                Current resume:
                                <?php
                                echo htmlspecialchars(
                                    $profile['resume']
                                );
                                ?>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

            <button
                type="submit"
                name="save"
                class="submit-btn"
            >

                <i class="fa-solid fa-floppy-disk"></i>

                <?php
                echo $profile
                    ? "Update Profile"
                    : "Save Profile";
                ?>

            </button>

        </form>

    </div>

    <footer>
        © 2026 InternLink Portal
    </footer>

</div>

</body>
</html>