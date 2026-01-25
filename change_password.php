<?php
include "includes/db.php";

/* ======================
   START SESSION (SAFE)
====================== */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ======================
   STAFF ACCESS ONLY
====================== */
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

$staff_id = (int) $_SESSION['staff_id'];

/* ======================
   HANDLE PASSWORD UPDATE
====================== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (empty($_POST['password'])) {
        header("Location: change_password.php?error=empty");
        exit();
    }

    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    mysqli_query($conn, "
        UPDATE ward_staff 
        SET 
            password = '$new_password',
            first_login = 0,
            password_changed_at = NOW()
        WHERE staff_id = $staff_id
    ");

    header("Location: staff_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password | Nepal Civic</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-box">
        <h2>Change Password</h2>

        <form method="POST">
            <input
                type="password"
                name="password"
                placeholder="New Password"
                required
            >
            <button type="submit">Update Password</button>
        </form>
    </div>
</div>

</body>
</html>
