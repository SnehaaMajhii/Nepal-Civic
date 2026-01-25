<?php
include "includes/db.php";

/* ======================
   CITIZEN ACCESS ONLY
====================== */
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'citizen') {
    header("Location: login.php");
    exit();
}

$citizen_id = $_SESSION['citizen_id'];

/* ======================
   FETCH CITIZEN & WARDS
====================== */
$citizen = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT ward_id FROM citizen WHERE citizen_id = $citizen_id
"));

$citizen_ward_id = $citizen['ward_id'];

$wards = mysqli_query($conn, "SELECT * FROM ward ORDER BY ward_no ASC");
$departments = mysqli_query($conn, "SELECT * FROM department");

/* ======================
   FORM SUBMIT
====================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title         = mysqli_real_escape_string($conn, $_POST['title']);
    $department_id = (int) $_POST['department_id'];
    $description   = mysqli_real_escape_string($conn, $_POST['description']);
    $urgency_level = $_POST['urgency_level']; // low | medium | high
    $ward_id       = (int) $_POST['ward_id'];

    /* ======================
       IMAGE UPLOAD
    ====================== */
    $photo_update = null;

    if (!empty($_FILES['photo_update']['name'])) {

        $folder = "uploads/issues/";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $ext = pathinfo($_FILES['photo_update']['name'], PATHINFO_EXTENSION);
        $photo_update = time() . "_" . rand(1000,9999) . "." . $ext;

        move_uploaded_file(
            $_FILES['photo_update']['tmp_name'],
            $folder . $photo_update
        );
    }

    /* ======================
       INSERT ISSUE
    ====================== */
    mysqli_query($conn, "
        INSERT INTO issue
        (title, citizen_id, ward_id, department_id, description, status, urgency_level, photo_update)
        VALUES
        (
            '$title',
            $citizen_id,
            $ward_id,
            $department_id,
            '$description',
            'pending',
            '$urgency_level',
            '$photo_update'
        )
    ");

    header("Location: citizen_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Report Issue | Nepal Civic</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="dashboard-wrapper">

<?php include "includes/sidebar.php"; ?>

<!-- <div class="main-content"> -->

<h2>Report New Issue</h2>

<form method="POST" enctype="multipart/form-data" class="auth-box" id="issueForm">

    <label>Issue Location (Ward)</label>
    <select name="ward_id" required>
        <?php while ($w = mysqli_fetch_assoc($wards)) { ?>
            <option value="<?= $w['ward_id'] ?>"
                <?= $w['ward_id'] == $citizen_ward_id ? 'selected' : '' ?>>
                Ward <?= $w['ward_no'] ?>
            </option>
        <?php } ?>
    </select>

    <label>Issue Title</label>
    <input type="text" name="title" required>

    <label>Department</label>
    <select name="department_id" required>
        <option value="">Select Department</option>
        <?php while ($d = mysqli_fetch_assoc($departments)) { ?>
            <option value="<?= $d['department_id'] ?>">
                <?= htmlspecialchars($d['department_name']) ?>
            </option>
        <?php } ?>
    </select>

    <label>Urgency Level</label>
    <select name="urgency_level" required>
        <option value="">Select urgency</option>
        <option value="low">Low</option>
        <option value="medium">Medium</option>
        <option value="high">High</option>
    </select>

    <label>Description</label>
    <textarea name="description" required></textarea>

    <label>Upload Issue Photo (optional)</label>
    <input type="file" name="photo_update" accept="image/*">

    <button type="submit">Submit Issue</button>

</form>

</div>
</div>

<script src="assets/main.js"></script>
</body>
</html>
