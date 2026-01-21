<?php
// Includes database connection and starts session
include 'includes/db.php'; 

$message = "";

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $national_id = trim($_POST['national_id']);
    $ward_no = intval($_POST['ward_no']);
    $address = trim($_POST['address']);

    // 1. Server-side Strict Validation for Citizenship (Format: 00-00-00-00000)
    if (!preg_match('/^[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{5}$/', $national_id)) {
        $message = "Invalid Citizenship format. Please use 00-00-00-00000.";
    } 
    // 2. Server-side Email Validation
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    }
    // 3. Server-side Password Length Check
    elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters long.";
    }
    else {
        // Check if email already exists
        $check = $conn->prepare("SELECT email FROM citizens WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        
        if ($check->get_result()->num_rows > 0) {
            $message = "This email is already registered!";
        } else {
            // Insert into Database
            $sql = "INSERT INTO citizens (name, email, password, national_id, ward_no, address) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            // Note: In a production app, use password_hash($password, PASSWORD_DEFAULT)
            $stmt->bind_param("ssssis", $name, $email, $password, $national_id, $ward_no, $address);

            if ($stmt->execute()) {
                header("Location: login.php?msg=registered");
                exit();
            } else {
                $message = "Registration failed. Please try again later.";
            }
        }
    }
}

$page_title = "Citizen Registration - Nepal Civic";
include 'includes/header.php';
?>

<div class="container">
    <div class="form-card" style="max-width: 600px; margin: 40px auto; border-top: 5px solid var(--nepal-blue);">
        <h2 style="text-align: center; color: var(--nepal-blue); margin-bottom: 10px;">Citizen Registration</h2>
        <p style="text-align: center; color: #666; margin-bottom: 25px;">Create an account to report and track community issues.</p>
        
        <?php if ($message): ?> 
            <p class="error-msg" style="color: var(--nepal-red); text-align: center; font-weight: bold; margin-bottom: 20px;">
                <?php echo $message; ?>
            </p> 
        <?php endif; ?>

        <form id="registrationForm" method="POST" action="register.php" novalidate>
            <div class="input-group">
                <label style="font-weight: bold; margin-bottom: 5px; display: block;">Full Name</label>
                <input type="text" name="name" placeholder="E.g. Sita Sigdel" required 
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                <span class="val-error" id="name-err" style="color: var(--nepal-red); font-size: 0.8rem; margin-top: 5px; display: block;"></span>
            </div>

            <div class="input-group" style="margin-top: 15px;">
                <label style="font-weight: bold; margin-bottom: 5px; display: block;">Email Address</label>
                <input type="email" name="email" placeholder="sitasig@mail.com" required 
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                <span class="val-error" id="email-err" style="color: var(--nepal-red); font-size: 0.8rem; margin-top: 5px; display: block;"></span>
            </div>

            <div class="input-group" style="margin-top: 15px;">
                <label style="font-weight: bold; margin-bottom: 5px; display: block;">Password</label>
                <input type="password" name="password" placeholder="Min 8 chars (letters + numbers)" required 
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                <span class="val-error" id="pass-err" style="color: var(--nepal-red); font-size: 0.8rem; margin-top: 5px; display: block;"></span>
            </div>

            <div class="input-group" style="margin-top: 15px;">
                <label style="font-weight: bold; margin-bottom: 5px; display: block;">Citizenship Number</label>
                <input type="text" name="national_id" placeholder="00-00-00-00000" 
                       pattern="[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{5}" 
                       title="Please follow the format: 00-00-00-00000" required 
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                <span class="val-error" id="id-err" style="color: var(--nepal-red); font-size: 0.8rem; margin-top: 5px; display: block;"></span>
                <small style="color: #888;">District-Office-Year-Serial</small>
            </div>

            <div style="display: flex; gap: 20px; margin-top: 15px;">
                <div class="input-group" style="flex: 1;">
                    <label style="font-weight: bold; margin-bottom: 5px; display: block;">Ward No.</label>
                    <input type="number" name="ward_no" min="1" max="32" required 
                           style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <div class="input-group" style="flex: 2;">
                    <label style="font-weight: bold; margin-bottom: 5px; display: block;">Address (Tole/Street)</label>
                    <input type="text" name="address" placeholder="e.g.Guras Tole" required 
                           style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                    <span class="val-error" id="addr-err" style="color: var(--nepal-red); font-size: 0.8rem; margin-top: 5px; display: block;"></span>
                </div>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 30px; font-weight: bold; font-size: 1rem;">
                Register Account
            </button>
        </form>

        <p style="text-align: center; margin-top: 20px;">
            Already have an account? <a href="login.php" style="color: var(--nepal-blue); font-weight: bold; text-decoration: none;">Login here</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>