<?php
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $national_id = mysqli_real_escape_string($conn, $_POST['national_id']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure hashing 

    $sql = "INSERT INTO users (fullname, email, password_hash, national_id, role) 
            VALUES ('$fullname', '$email', '$password', '$national_id', 'citizen')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=Success! Please login.");
        exit();
    } else {
        $error = "Registration failed. ID or Email might already exist.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - Nepal Civic</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container" style="max-width: 500px;">
        <div class="card">
            <h2>Citizen Registration</h2>
            [cite_start]<p>Please use your official National ID/Citizenship Number [cite: 19]</p>
            <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <form method="POST" action="">
                <input type="text" name="fullname" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="text" name="national_id" placeholder="Citizenship / National ID" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" style="width:100%;">Register</button>
            </form>
            <p style="text-align:center;">Already registered? <a href="index.php">Login here</a></p>
        </div>
    </div>
    <script src="assets/js/main.js"></script>
</body>
</html>