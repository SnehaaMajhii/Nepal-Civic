<?php
// register.php
include 'includes/db.php'; 

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $national_id = trim($_POST['national_id']);
    $ward_no = intval($_POST['ward_no']);
    $address = trim($_POST['address']);

    $check = $conn->prepare("SELECT email FROM citizens WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        $message = "Email already registered!";
    } else {
        $sql = "INSERT INTO citizens (name, email, password, national_id, ward_no, address) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssis", $name, $email, $password, $national_id, $ward_no, $address);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

// VIEW
$page_title = "Register - Nepal Civic";
include 'includes/header.php';
?>

<div class="container">
    <div class="form-box" style="max-width: 600px;">
        <h2 style="text-align: center;">Citizen Registration</h2>
        <?php if ($message): ?> <p class="error-msg"><?php echo $message; ?></p> <?php endif; ?>

        <form method="POST" action="">
            <label>Full Name</label>
            <input type="text" name="name" required>

            <label>Email Address</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <div style="display: flex; gap: 20px;">
                <div style="flex: 1;">
                    <label>National ID No.</label>
                    <input type="text" name="national_id" required>
                </div>
                <div style="flex: 1;">
                    <label>Ward Number</label>
                    <input type="number" name="ward_no" placeholder="e.g. 5" min="1" max="32" required>
                </div>
            </div>

            <label>Address (Tole/Street)</label>
            <input type="text" name="address" placeholder="e.g. Main Road" required>

            <button type="submit" class="btn-primary">Register</button>
        </form>
        <p style="text-align: center; margin-top: 15px;">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>