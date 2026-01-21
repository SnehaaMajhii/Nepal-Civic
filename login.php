<?php
// Start Session
session_start();
include 'includes/db.php';

$error = "";

// Check if user is already logged in, redirect them based on role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } elseif ($_SESSION['role'] === 'ward_member') {
        header("Location: ward_dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

// HANDLE LOGIN FORM SUBMISSION
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $admin_email = 'admin@nepalcivic.com';
    $admin_hash  = '$2y$10$loXhnq3AlP/3j3xJcKbkTOf1BYEZLF2xa4DS.jbLEHMjJZJ6dEfVS'; 

if ($email === $admin_email && password_verify($password, $admin_hash)) {
    $_SESSION['user_id'] = 0;
    $_SESSION['username'] = 'Super Admin';
    $_SESSION['role'] = 'admin';
    header("Location: admin_dashboard.php");
    exit();
}


    // 2. CHECK WARD MEMBERS (Chairperson, Secretary, etc.)
    // We join with 'wards' table to store the Ward Number in the session
    $stmt = $conn->prepare("SELECT wm.*, w.ward_no FROM ward_members wm JOIN wards w ON wm.ward_id = w.ward_id WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Verify Password (Plain text for now. In production, use password_verify)
        if ($password === $row['password']) {
            $_SESSION['user_id'] = $row['member_id'];
            $_SESSION['username'] = $row['full_name'];
            $_SESSION['role'] = 'ward_member'; 
            $_SESSION['designation'] = $row['designation'];
            $_SESSION['ward_no'] = $row['ward_no'];
            
            header("Location: ward_dashboard.php"); // Redirect to Ward Panel
            exit();
        } 
    }
    $stmt->close();

    // 3. CHECK CITIZENS (Regular Users)
    $stmt = $conn->prepare("SELECT * FROM citizens WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
    if ($password === $row['password']) {
        $_SESSION['user_id'] = $row['citizen_id'];
        $_SESSION['username'] = $row['name'];
        $_SESSION['role'] = 'citizen';

        header("Location: citizen_dashboard.php");
        exit();}
    }

    $stmt->close();

    // If we reach here, no match was found
    $error = "Invalid email or password.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Nepal Civic</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        /* Specific styles for the centered login page */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #e9ecef;
        }
        .login-wrapper {
            width: 100%;
            max-width: 400px;
        }
        .form-card {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .logo-area {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo-area h2 {
            border: none;
            color: var(--nepal-blue);
            font-size: 24px;
            margin-bottom: 5px;
        }
        .home-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #666;
            text-decoration: none;
            font-size: 14px;
        }
        .home-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="form-card">
        <div class="logo-area">
            <h2>Nepal Civic</h2>
            <p style="color:#777;">Secure Login Portal</p>
        </div>

        <?php if($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php" style="display:flex; flex-direction:column; gap:15px;">
            <div>
                <label style="font-weight:600; font-size:14px; color:#555;">Email Address</label>
                <input type="email" name="email" required placeholder="name@example.com">
            </div>

            <div>
                <label style="font-weight:600; font-size:14px; color:#555;">Password</label>
                <input type="password" name="password" required placeholder="Enter your password">
            </div>

            <button type="submit" class="btn-primary" style="width:100%; padding:12px; margin-top:10px;">Login</button>
        </form>

        <div style="text-align:center; margin-top:20px; font-size:14px;">
            Don't have an account? <a href="register.php" style="color:var(--nepal-blue); font-weight:bold;">Register here</a>
        </div>
    </div>

    <a href="index.php" class="home-link">&larr; Back to Home</a>
</div>

<script src="assets/main.js"></script>

</body>
</html>