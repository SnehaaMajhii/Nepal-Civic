<?php
/* ======================
   1. ERROR REPORTING & DB
====================== */
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "includes/db.php";

/* ======================
   2. SESSION START (FIXED)
====================== */
// This prevents the "Notice: session_start() already active" error
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ======================
   3. ACCESS & METHOD CHECK
====================== */
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'citizen') {
    die("Access Denied: Please log in as a citizen.");
}

// If you see the "Lifecycle Error" from your screenshot, 
// it's because you are visiting the URL directly. You must submit the form.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Lifecycle Error: This page only accepts POST submissions. Go back to your dashboard and submit the form.");
}

$citizen_id = (int) $_SESSION['citizen_id'];

/* ======================
   4. GET & CLEAN FORM DATA
====================== */
$title        = mysqli_real_escape_string($conn, $_POST['title'] ?? '');
$description  = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
$department   = (int) ($_POST['department_id'] ?? 0);
$ward_id      = (int) ($_POST['ward_id'] ?? 0);
$urgency      = mysqli_real_escape_string($conn, $_POST['urgency_level'] ?? 'low');

// Basic Validation
if (empty($title) || empty($description) || $department === 0 || $ward_id === 0) {
    header("Location: citizen_dashboard.php?page=report&error=missing_fields");
    exit();
}

/* ======================
   5. IMAGE UPLOAD
====================== */
$photo_update = NULL;
if (!empty($_FILES['photo_update']['name'])) {
    $uploadDir = "uploads/issues/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $ext = strtolower(pathinfo($_FILES['photo_update']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp'];
    if (in_array($ext, $allowed)) {
        $photo_update = time() . "_" . uniqid() . "." . $ext;
        move_uploaded_file($_FILES['photo_update']['tmp_name'], $uploadDir . $photo_update);
    }
}

/* ======================
   6. INSERT ISSUE
====================== */
$query_issue = "INSERT INTO issue (
    title, citizen_id, ward_id, department_id, description, 
    status, urgency_level, photo_update, date_reported
) VALUES (
    '$title', $citizen_id, $ward_id, $department, '$description', 
    'pending', '$urgency', " . ($photo_update ? "'$photo_update'" : "NULL") . ", NOW()
)";

if (!mysqli_query($conn, $query_issue)) {
    die("CRITICAL DB ERROR (Issue): " . mysqli_error($conn));
}

// Capture the REAL ID of the issue we just created
$new_issue_id = mysqli_insert_id($conn);

/* ======================
   7. INSERT LOG (THE FIX)
====================== */
$query_log = "INSERT INTO issue_logs (
    issue_id, action_by, user_role, action_type, description, created_at
) VALUES (
    $new_issue_id, $citizen_id, 'citizen', 'Reported', 'Issue reported by citizen', NOW()
)";

if (!mysqli_query($conn, $query_log)) {
    // If the log fails, we see exactly why here
    die("LOGGING ERROR: " . mysqli_error($conn) . " | Query: " . $query_log);
}

/* ======================
   8. NOTIFY ADMIN
====================== */
mysqli_query($conn, "INSERT INTO notification (message, is_read, created_at) VALUES ('New issue reported by a citizen.', 0, NOW())");

/* ======================
   9. REDIRECT
====================== */
// Using a meta refresh as a backup if headers fail
echo "Issue Reported Successfully! Redirecting...";
echo "<meta http-equiv='refresh' content='1;url=citizen_dashboard.php?success=issue_reported'>";
exit();