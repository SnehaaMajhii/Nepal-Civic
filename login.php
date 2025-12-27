<?php
// login.php
include 'includes/db.php'; 

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // 1. Check ADMINS
    $stmt = $conn->prepare("SELECT admin_id, name, password FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();
        if ($password === $row['password']) {
            $_SESSION['user_id'] = $row['admin_id'];
            $_SESSION['role'] = 'admin';
            $_SESSION['name'] = $row['name'];
            header("Location: admin_dashboard.php");
            exit();
        }
    }

    // 2. Check MANAGERS
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
            $_SESSION['ward_no'] = $row['ward_no']; // Save ward to session!
            header("Location: department_dashboard.php");
            exit();
        }
    }

    // 3. Check CITIZENS
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

    $error = "Invalid email or password.";
}

// VIEW
$page_title = "Login - Nepal Civic";
include 'includes/header.php';
?>

<div class="container">
    <div class="login-box">
        <h2 style="text-align: center;">Sign In</h2>
        <?php if($error): ?>
            <p class="error-msg"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form method="POST" action="">
            <label>Email</label>
            <input type="email" name="email" required>
            
            <label>Password</label>
            <input type="password" name="password" required>
            
            <button type="submit" class="btn-primary">Login</button>
        </form>
        <p style="text-align: center; margin-top: 15px;">
            New Citizen? <a href="register.php">Register Here</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>