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

    // PHP Backend Validation (Security Layer)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } elseif (strlen($password) < 8) {
        $message = "Password too short.";
    } else {
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
                header("Location: login.php?msg=registered");
                exit();
            } else {
                $message = "Error: " . $conn->error;
            }
        }
    }
}

$page_title = "Citizen Registration - Nepal Civic";
include 'includes/header.php';
?>

<div class="container">
    <div class="form-box" style="max-width: 600px; margin: 40px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; color: #003893;">Citizen Registration</h2>
        <p style="text-align: center; color: #666; margin-bottom: 20px;">Create an account to report issues in your ward.</p>
        
        <?php if ($message): ?> <p class="error-msg" style="color: red; text-align: center;"><?php echo $message; ?></p> <?php endif; ?>

        <form id="registrationForm" method="POST" action="register.php" novalidate>
            <div class="input-group">
                <label>Full Name</label>
                <input type="text" name="name" required>
                <span class="val-error" id="name-err"></span>
            </div>

            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
                <span class="val-error" id="email-err"></span>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required>
                <span class="val-error" id="pass-err"></span>
            </div>

            <div style="display: flex; gap: 20px;">
                <div class="input-group" style="flex: 1;">
                    <label>National ID (Citizenship)</label>
                    <input type="text" name="national_id" placeholder="e.g. 12-34-56-789" required>
                    <span class="val-error" id="id-err"></span>
                </div>
                <div class="input-group" style="flex: 1;">
                    <label>Ward Number</label>
                    <input type="number" name="ward_no" min="1" max="32" required>
                    <span class="val-error" id="ward-err"></span>
                </div>
            </div>

            <div class="input-group">
                <label>Address (Tole/Street)</label>
                <input type="text" name="address" required>
                <span class="val-error" id="addr-err"></span>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Register Account</button>
        </form>
        <p style="text-align: center; margin-top: 15px;">
            Already registered? <a href="login.php">Login here</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>