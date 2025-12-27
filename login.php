<?php
// login.php
include 'includes/db.php'; 

// Check if user is already logged in, redirect them accordingly
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') header("Location: admin_dashboard.php");
    elseif ($_SESSION['role'] === 'manager') header("Location: department_dashboard.php");
    else header("Location: citizen_dashboard.php");
    exit();
}

$error = "";
$info_msg = "";

// Handle URL messages (from registration or logout)
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'registered') $info_msg = "Registration successful! You can now login.";
    if ($_GET['msg'] === 'loggedout') $info_msg = "You have been successfully logged out.";
}

// --- HANDLE LOGIN SUBMISSION ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        
        // 1. Check ADMINS Table
        $stmt = $conn->prepare("SELECT admin_id, name, password FROM admins WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 1) {
            $row = $res->fetch_assoc();
            if ($password === $row['password']) { // Use password_verify() if hashed
                $_SESSION['user_id'] = $row['admin_id'];
                $_SESSION['role'] = 'admin';
                $_SESSION['name'] = $row['name'];
                header("Location: admin_dashboard.php");
                exit();
            }
        }

        // 2. Check MANAGERS Table
        $stmt = $conn->prepare("SELECT manager_id, name, password, dept_id, ward_no FROM department_managers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 1) {
            $row = $res->fetch_assoc();
            if ($password === $row['password']) {
                $_SESSION['user_id'] = $row['manager_id'];
                $_SESSION['role'] = 'manager';
                $_SESSION['name'] = $row['name'];
                $_SESSION['dept_id'] = $row['dept_id'];
                $_SESSION['ward_no'] = $row['ward_no'];
                header("Location: department_dashboard.php");
                exit();
            }
        }

        // 3. Check CITIZENS Table
        $stmt = $conn->prepare("SELECT citizen_id, name, password FROM citizens WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 1) {
            $row = $res->fetch_assoc();
            if ($password === $row['password']) {
                $_SESSION['user_id'] = $row['citizen_id'];
                $_SESSION['role'] = 'citizen';
                $_SESSION['name'] = $row['name'];
                header("Location: citizen_dashboard.php");
                exit();
            }
        }

        $error = "Invalid email or password. Please try again.";
    } else {
        $error = "Please fill in all fields.";
    }
}

$page_title = "Login - Nepal Civic";
include 'includes/header.php';
?>

<div class="container">
    <div class="login-box" style="max-width: 400px; margin: 60px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; color: #003893; margin-bottom: 20px;">Sign In</h2>

        <?php if ($info_msg): ?>
            <p class="error-msg" style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; text-align: center; font-size: 0.9rem; margin-bottom: 15px;">
                <?php echo $info_msg; ?>
            </p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p class="error-msg" style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; text-align: center; font-size: 0.9rem; margin-bottom: 15px;">
                <?php echo $error; ?>
            </p>
        <?php endif; ?>
        
        <form method="POST" action="login.php">
            <div class="input-group" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Email Address</label>
                <input type="email" name="email" required placeholder="Enter your email" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            
            <div class="input-group" style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px;">Password</label>
                <input type="password" name="password" required placeholder="Enter your password" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%; padding: 12px; font-weight: bold;">Login</button>
        </form>

        <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">

        <p style="text-align: center; font-size: 0.9rem;">
            New Citizen? <a href="register.php" style="color: #003893; font-weight: bold; text-decoration: none;">Register Here</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>