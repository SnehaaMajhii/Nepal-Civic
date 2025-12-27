<?php
// report.php
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'citizen') {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $dept_id = $_POST['dept_id'];
    $priority = $_POST['priority'];
    $citizen_id = $_SESSION['user_id'];
    
    // --- IMAGE UPLOAD LOGIC ---
    $photo_url = NULL;
    if (isset($_FILES['issue_image']) && $_FILES['issue_image']['error'] == 0) {
        $target_dir = "uploads/";
        // Create a unique filename using timestamp
        $file_name = time() . "_" . basename($_FILES["issue_image"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed = ['jpg', 'png', 'jpeg', 'gif'];
        if (in_array($imageFileType, $allowed)) {
            if (move_uploaded_file($_FILES["issue_image"]["tmp_name"], $target_file)) {
                $photo_url = $target_file;
            } else {
                $message = "Error uploading file.";
            }
        } else {
            $message = "Only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }

    if (empty($message) && !empty($title)) {
        // Fetch Ward automatically
        $ward_query = $conn->prepare("SELECT ward_no FROM citizens WHERE citizen_id = ?");
        $ward_query->bind_param("i", $citizen_id);
        $ward_query->execute();
        $ward_no = $ward_query->get_result()->fetch_assoc()['ward_no'];

        // Set status to 'Pending Approval' for Admin moderation
        $sql = "INSERT INTO issues (title, description, dept_id, priority, citizen_id, ward_no, status, photo_url) VALUES (?, ?, ?, ?, ?, ?, 'Pending Approval', ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisiis", $title, $description, $dept_id, $priority, $citizen_id, $ward_no, $photo_url);

        if ($stmt->execute()) {
            header("Location: citizen_dashboard.php");
            exit();
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

$dept_result = $conn->query("SELECT * FROM departments ORDER BY name ASC");
$page_title = "Report Issue - Nepal Civic";
include 'includes/header.php';
?>

<div class="container">
    <div class="form-card">
        <h2>Report Community Issue</h2>
        <?php if ($message): ?> <p class="error-msg"><?php echo $message; ?></p> <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <label>Title</label>
            <input type="text" name="title" required>

            <label>Category</label>
            <select name="dept_id" required>
                <option value="">Select Department...</option>
                <?php while ($row = $dept_result->fetch_assoc()): ?>
                    <option value="<?php echo $row['dept_id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                <?php endwhile; ?>
            </select>

            <label>Priority</label>
            <select name="priority">
                <option value="Low">Low</option>
                <option value="Medium" selected>Medium</option>
                <option value="High">High</option>
            </select>

            <label>Description</label>
            <textarea name="description" rows="4" required></textarea>

            <label>Upload Photo (Optional)</label>
            <input type="file" name="issue_image" accept="image/*">
            <small style="color: #666; display: block; margin-bottom: 20px;">Provide a clear photo to help the Admin approve your request.</small>

            <button type="submit" class="btn-primary">Submit Official Report</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>