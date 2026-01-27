<?php
session_start();
include "includes/db.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

// Data Collection
    $full_name  = trim($_POST['full_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $nationalID = trim($_POST['national_id'] ?? '');
    $address    = trim($_POST['address'] ?? '');
    $ward_id    = (int) ($_POST['ward_id'] ?? 0);

// Validation
    if (
        !$full_name || !$email || !$password ||
        !$nationalID || !$address || !$ward_id
    ) {
        $error = "All fields are required.";
    }


    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    }


    elseif (
        !preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)
    ) {
        $error = "Password must be at least 8 characters and include a letter, number, and symbol.";
    }


    else {
        $safeNationalID = mysqli_real_escape_string($conn, $nationalID);

        $check = mysqli_query($conn, "
            SELECT citizen_id FROM citizen
            WHERE national_id = '$safeNationalID'
            LIMIT 1
        ");

        if (mysqli_num_rows($check) > 0) {
            $error = "Citizenship number already registered.";
        }
    }

    if (!$error) {

        if (
            !isset($_FILES['citizenship_front']) ||
            !isset($_FILES['citizenship_back'])
        ) {
            $error = "Citizenship images are required.";
        } else {

            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

            if (
                !in_array($_FILES['citizenship_front']['type'], $allowedTypes) ||
                !in_array($_FILES['citizenship_back']['type'], $allowedTypes)
            ) {
                $error = "Only JPG and PNG images are allowed.";
            }
        }
    }

// Procedure
    if (!$error) {

        //Safe filenames 
        $frontName = uniqid("front_") . "." . pathinfo($_FILES['citizenship_front']['name'], PATHINFO_EXTENSION);
        $backName  = uniqid("back_")  . "." . pathinfo($_FILES['citizenship_back']['name'], PATHINFO_EXTENSION);

        move_uploaded_file($_FILES['citizenship_front']['tmp_name'], "uploads/$frontName");
        move_uploaded_file($_FILES['citizenship_back']['tmp_name'], "uploads/$backName");

        //Password hash
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        //Insert
        mysqli_query($conn, "
            INSERT INTO citizen
            (national_id, full_name, email, password, address, citizenship_front, citizenship_back, ward_id)
            VALUES (
                '".mysqli_real_escape_string($conn, $nationalID)."',
                '".mysqli_real_escape_string($conn, $full_name)."',
                '".mysqli_real_escape_string($conn, $email)."',
                '$hashedPassword',
                '".mysqli_real_escape_string($conn, $address)."',
                '$frontName',
                '$backName',
                $ward_id
            )
        ");

        header("Location: login.php");
        exit();
    }
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

        <?php if ($error): ?>
            <div style="color:red; margin-bottom:10px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <input type="text" name="full_name" placeholder="Full Name" required>

            <input type="email" name="email" placeholder="Email" required>

            <input type="password" name="password" placeholder="Password" required>

            <input type="text" name="national_id" placeholder="Citizenship Number" required>

            <input type="text" name="address" placeholder="Address (Tole / Street)" required>

            <select name="ward_id" required>
                <option value="">Select Ward</option>
                <?php
                $wards = mysqli_query($conn, "SELECT * FROM ward");
                while ($w = mysqli_fetch_assoc($wards)) {
                    echo "<option value='{$w['ward_id']}'>Ward {$w['ward_no']}</option>";
                }
                ?>
            </select>

            <label>Citizenship Front Image</label>
            <input type="file" name="citizenship_front" accept="image/*" required>

            <label>Citizenship Back Image</label>
            <input type="file" name="citizenship_back" accept="image/*" required>

            <button type="submit">Register</button>
        </form>

        <div style="margin-top:15px; text-align:center;">
            Already registered? <a href="login.php">Login</a>
        </div>
    </div>
</div>

</body>
</html>
