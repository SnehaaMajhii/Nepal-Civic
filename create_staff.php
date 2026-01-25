<?php
include "includes/db.php";

/* ======================
   ADMIN ACCESS ONLY
====================== */
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

/* ======================
   FETCH WARDS
====================== */
$wards = mysqli_query($conn, "SELECT * FROM ward");

$success = "";
$tempPassword = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name  = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $desig = mysqli_real_escape_string($conn, $_POST['designation']);
    $ward  = (int) $_POST['ward_id'];

    /* ======================
       GENERATE ONE-TIME PASSWORD
    ====================== */
    $tempPassword = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 8);
    $hashedPass   = password_hash($tempPassword, PASSWORD_DEFAULT);

    /* ======================
       INSERT STAFF
    ====================== */
    mysqli_query($conn, "
        INSERT INTO ward_staff
        (
            full_name,
            email,
            password,
            designation,
            ward_id,
            first_login,
            password_changed_at
        )
        VALUES
        (
            '$name',
            '$email',
            '$hashedPass',
            '$desig',
            $ward,
            1,
            NULL
        )
    ");

    $success = "Ward staff created successfully.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Ward Staff | Nepal Civic</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-box">
        <h2>Create Ward Staff</h2>

        <?php if ($success) { ?>
            <p style="color:green;text-align:center;">
                <?= $success ?>
            </p>

            <p style="color:#c4161c;text-align:center;font-weight:bold;margin-top:8px;">
                Temporary Password: <?= htmlspecialchars($tempPassword) ?>
            </p>

            <p style="font-size:13px;text-align:center;color:#555;">
                ⚠️ Ask staff to change password on first login.
            </p>
        <?php } ?>

        <form method="POST">

            <input type="text" name="full_name" placeholder="Full Name" required>

            <input type="email" name="email" placeholder="Email Address" required>

            <input type="text" name="designation" placeholder="Designation" required>

            <label>Select Ward</label>
            <select name="ward_id" required>
                <option value="">-- Select Ward --</option>
                <?php while ($w = mysqli_fetch_assoc($wards)) { ?>
                    <option value="<?= $w['ward_id'] ?>">
                        Ward <?= $w['ward_no'] ?> - <?= htmlspecialchars($w['location']) ?>
                    </option>
                <?php } ?>
            </select>

            <button type="submit">Create Staff</button>
        </form>

        <p style="margin-top:10px;text-align:center;">
            <a href="admin_dashboard.php">Back to Dashboard</a>
        </p>
    </div>
</div>

</body>
</html>
