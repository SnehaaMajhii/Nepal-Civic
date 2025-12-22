<?php
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Check Users table [cite: 85]
    $sql = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        // Verify hashed password 
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['fullname'];

            // Role-based redirection [cite: 96]
            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: citizen_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Nepal Civic - Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container" style="max-width: 450px; margin-top: 100px;">
        <div class="card">
            <h1>Nepal Civic Login</h1>
            <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <form method="POST" action="">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="example@mail.com">
                
                <label>Password</label>
                <input type="password" name="password" required placeholder="••••••••">
                
                <button type="submit" style="width:100%;">Sign In</button>
            </form>
            <p style="text-align:center;">New citizen? <a href="register.php">Create an Account</a></p>
        </div>
    </div>
</body>
</html>