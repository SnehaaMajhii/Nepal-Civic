<?php
include "includes/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    /* -------- ADMIN LOGIN -------- */
    $adminQ = mysqli_query($conn, "SELECT * FROM admin WHERE email='$email'");
    if (mysqli_num_rows($adminQ) === 1) {
        $admin = mysqli_fetch_assoc($adminQ);
        if (password_verify($password, $admin['password'])) {
            $_SESSION['user_role'] = 'admin';
            $_SESSION['admin_id']  = $admin['admin_id'];
            header("Location: admin_dashboard.php");
            exit();
        }
    }

    /* -------- CITIZEN LOGIN -------- */
    $citizenQ = mysqli_query(
        $conn,
        "SELECT * FROM citizen WHERE email='$email'"
    );

    if (mysqli_num_rows($citizenQ) === 1) {

        $citizen = mysqli_fetch_assoc($citizenQ);

        if ($citizen['is_active'] == 0) {
            $error = "Your account has been deactivated due to unnecessary activities.";
        } 
        elseif (password_verify($password, $citizen['password'])) {

            $_SESSION['user_role']  = 'citizen';
            $_SESSION['citizen_id'] = $citizen['citizen_id'];
            $_SESSION['ward_id']    = $citizen['ward_id'];

            header("Location: citizen_dashboard.php");
            exit();
        } 
        else {
            $error = "Invalid email or password.";
        }
    }   
    else {
            $error = "Invalid email or password.";
    }


    /* -------- STAFF LOGIN -------- */
    $staffQ = mysqli_query($conn, "SELECT * FROM ward_staff WHERE email='$email'");
    if (mysqli_num_rows($staffQ) === 1) {
        $staff = mysqli_fetch_assoc($staffQ);
        if (password_verify($password, $staff['password'])) {

            $_SESSION['user_role'] = 'staff';
            $_SESSION['staff_id']  = $staff['staff_id'];
            $_SESSION['ward_id']   = $staff['ward_id'];

            /* 🔒 FORCE PASSWORD CHANGE ON FIRST LOGIN */
            if ((int)$staff['first_login'] === 1) {
                header("Location: change_password.php");
                exit();
            }

            header("Location: staff_dashboard.php");
            exit();
        }
    }

    $error = "Invalid login credentials";
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Login | Nepal Civic</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-box">
        <h2>Login</h2>

        <?php if (!empty($error)) { ?>
            <p style="color:red; text-align:center; margin-bottom:10px;">
                <?php echo $error; ?>
            </p>
        <?php } ?>

        <form method="POST" id="loginForm">
            <input type="email" name="email" id="email" placeholder="Email" required>
            <input type="password" name="password" id="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <!-- EXTRA LINKS -->
        <div style="margin-top:15px; text-align:center; font-size:14px;">
            <p>
                New citizen?
                <a href="register.php" style="color:#0b3c91; font-weight:bold;">
                    Register here
                </a>
            </p>

            <p style="margin-top:8px;">
                <a href="index.php" style="color:#555;">
                    ← Back to Home
                </a>
            </p>
        </div>
    </div>
</div>

<script src="assets/main.js"></script>
</body>
</html>
