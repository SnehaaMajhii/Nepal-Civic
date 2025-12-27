<?php
// admin_dashboard.php
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";

// --- MODERATION LOGIC ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['moderate_issue_id'])) {
    $issue_id = $_POST['moderate_issue_id'];
    $action = $_POST['action']; 
    $admin_id = $_SESSION['user_id'];

    $find_cit = $conn->prepare("SELECT citizen_id, title FROM issues WHERE issue_id = ?");
    $find_cit->bind_param("i", $issue_id);
    $find_cit->execute();
    $data = $find_cit->get_result()->fetch_assoc();
    $cit_id = $data['citizen_id'];
    $title_short = substr($data['title'], 0, 20) . "...";

    if ($action === 'approve') {
        $sql = "UPDATE issues SET status = 'Pending', admin_id = ? WHERE issue_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $admin_id, $issue_id);
        if ($stmt->execute()) {
            $message = "Issue #$issue_id Approved.";
            if ($cit_id) {
                $msg = "Report '$title_short' approved and assigned.";
                $conn->query("INSERT INTO notifications (message, user_id, user_type) VALUES ('$msg', $cit_id, 'citizen')");
            }
        }
    } elseif ($action === 'reject') {
        $reason = trim($_POST['rejection_reason']);
        $sql = "UPDATE issues SET status = 'Rejected', rejection_reason = ?, admin_id = ? WHERE issue_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $reason, $admin_id, $issue_id);
        if ($stmt->execute()) {
            $message = "Issue #$issue_id Rejected.";
            if ($cit_id) {
                $msg = "Report '$title_short' rejected. Reason: $reason";
                $conn->query("INSERT INTO notifications (message, user_id, user_type) VALUES ('$msg', $cit_id, 'citizen')");
            }
        }
    }
}

// --- MANAGEMENT LOGIC ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_dept_name'])) {
    $new_dept = trim($_POST['add_dept_name']);
    if (!empty($new_dept)) {
        $stmt = $conn->prepare("INSERT INTO departments (name) VALUES (?)");
        $stmt->bind_param("s", $new_dept);
        $stmt->execute();
        $message = "Department Added!";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_manager_email'])) {
    $m_name = trim($_POST['new_manager_name']);
    $m_email = trim($_POST['new_manager_email']);
    $m_pass = trim($_POST['new_manager_pass']);
    $m_dept = $_POST['new_manager_dept'];
    $m_ward = $_POST['new_manager_ward'];

    $stmt = $conn->prepare("INSERT INTO department_managers (name, email, password, dept_id, ward_no) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssii", $m_name, $m_email, $m_pass, $m_dept, $m_ward);
    if($stmt->execute()) $message = "Manager Created!";
}

// FETCH DATA
$new_requests = $conn->query("SELECT issues.*, departments.name as dept_name FROM issues JOIN departments ON issues.dept_id = departments.dept_id WHERE status = 'Pending Approval' ORDER BY date_reported ASC");
$all_issues = $conn->query("SELECT issues.*, departments.name as dept_name FROM issues JOIN departments ON issues.dept_id = departments.dept_id WHERE status != 'Pending Approval' ORDER BY date_reported DESC");
$dept_result = $conn->query("SELECT * FROM departments ORDER BY name ASC");

$page_title = "Admin Panel - Nepal Civic";
include 'includes/header.php';
?>

<div class="container">
    <h2>System Control Panel</h2>
    <?php if ($message): ?> <p class="error-msg"><?php echo $message; ?></p> <?php endif; ?>

    <div class="form-card" style="border-top: 5px solid #C41E3A; margin-bottom: 40px;">
        <h3 style="color: #C41E3A;">⚠️ New Requests (Pending Approval)</h3>
        <?php if ($new_requests->num_rows > 0): ?>
            <table>
                <thead><tr><th>ID</th><th>Details & Image</th><th>Action</th></tr></thead>
                <tbody>
                    <?php while ($row = $new_requests->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['issue_id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['title']); ?></strong><br>
                                <small>Ward <?php echo $row['ward_no']; ?> | <?php echo htmlspecialchars($row['dept_name']); ?></small>
                                <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                                <?php if($row['photo_url']): ?>
                                    <div style="margin-top: 10px;">
                                        <a href="<?php echo $row['photo_url']; ?>" target="_blank">
                                            <img src="<?php echo $row['photo_url']; ?>" style="max-width: 150px; border-radius: 4px; border: 1px solid #ddd;">
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="POST" style="margin-bottom: 5px;">
                                    <input type="hidden" name="moderate_issue_id" value="<?php echo $row['issue_id']; ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn-primary" style="background-color: #28a745;">✓ Approve</button>
                                </form>
                                <form method="POST" style="display:flex; gap:5px;">
                                    <input type="hidden" name="moderate_issue_id" value="<?php echo $row['issue_id']; ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <input type="text" name="rejection_reason" placeholder="Reason" required style="padding: 8px; flex:1; margin:0;">
                                    <button type="submit" class="btn-primary" style="background-color: #dc3545; width: auto; margin:0;">✕</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; color: #888;">No new requests.</p>
        <?php endif; ?>
    </div>

    <div style="display:flex; gap:20px; flex-wrap:wrap; margin-bottom:40px;">
        <div class="form-card" style="flex:1; margin:0; border-top: 5px solid #003893;">
            <h3>Add Department</h3>
            <form method="POST"><input type="text" name="add_dept_name" required><button type="submit">Add</button></form>
        </div>
        <div class="form-card" style="flex:1; margin:0; border-top: 5px solid #003893;">
            <h3>Hire Manager</h3>
            <form method="POST">
                <input type="text" name="new_manager_name" placeholder="Name" required>
                <input type="email" name="new_manager_email" placeholder="Email" required>
                <input type="password" name="new_manager_pass" placeholder="Password" required>
                <input type="number" name="new_manager_ward" placeholder="Ward" required>
                <select name="new_manager_dept" required>
                    <?php $dept_result->data_seek(0); while ($d = $dept_result->fetch_assoc()): ?>
                        <option value="<?php echo $d['dept_id']; ?>"><?php echo htmlspecialchars($d['name']); ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit">Create</button>
            </form>
        </div>
    </div>

    <h3>Issue History</h3>
    <table>
        <thead><tr><th>ID</th><th>Title</th><th>Ward</th><th>Status</th></tr></thead>
        <tbody>
            <?php while ($row = $all_issues->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $row['issue_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo $row['ward_no']; ?></td>
                    <td class="status-<?php echo $row['status']; ?>"><?php echo $row['status']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>