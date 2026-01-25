<?php
include "includes/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name  = $_POST['full_name'];
    $email      = $_POST['email'];
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nationalID = $_POST['national_id'];
    $address    = $_POST['address'];
    $ward_id    = $_POST['ward_id'];

    /* FILE UPLOADS */
    $front = $_FILES['citizenship_front']['name'];
    $back  = $_FILES['citizenship_back']['name'];

    move_uploaded_file($_FILES['citizenship_front']['tmp_name'], "uploads/".$front);
    move_uploaded_file($_FILES['citizenship_back']['tmp_name'], "uploads/".$back);

    mysqli_query($conn, "
        INSERT INTO citizen
        (national_id, full_name, email, password, address, citizenship_front, citizenship_back, ward_id)
        VALUES
        ('$nationalID', '$full_name', '$email', '$password', '$address', '$front', '$back', '$ward_id')
    ");

    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Citizen Registration | Nepal Civic</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-box">
        <h2>Citizen Registration</h2>

        <form method="POST" id="registerForm" enctype="multipart/form-data">

            <input type="text" name="full_name" placeholder="Full Name" required>

            <input type="email" name="email" id="email" placeholder="Email" required>

            <input type="password" name="password" id="password" placeholder="Password" required>

            <input type="text" name="national_id" placeholder="Citizenship Number" required>

            <!-- ADDRESS -->
            <input type="text" name="address" placeholder="Address (Tole / Street)" required>

            <!-- WARD -->
            <select name="ward_id" id="ward_id" required>
                <option value="">Select Ward</option>
                <?php
                $wards = mysqli_query($conn, "SELECT * FROM ward");
                while ($w = mysqli_fetch_assoc($wards)) {
                    echo "<option value='{$w['ward_id']}'>Ward {$w['ward_no']}</option>";
                }
                ?>
            </select>

            <!-- FILE UPLOADS -->
            <label style="font-size:13px; color:#555;">
                Citizenship Front Image (clear photo)
            </label>
            <input type="file" name="citizenship_front" accept="image/*" required>

            <label style="font-size:13px; color:#555;">
                Citizenship Back Image (clear photo)
            </label>
            <input type="file" name="citizenship_back" accept="image/*" required>

            <button type="submit">Register</button>
        </form>

        <!-- LINKS -->
        <div style="margin-top:15px; text-align:center; font-size:14px;">
            <p>
                Already registered?
                <a href="login.php" style="color:#0b3c91; font-weight:bold;">
                    Login here
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
