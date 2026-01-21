<?php
include 'includes/db.php';

// Security: Only Ward Officers (Managers) allowed
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ward_member') {
    header("Location: login.php");
    exit();
}

$ward_no = $_SESSION['ward_no'];
$success_msg = "";

// --- HANDLE STATUS UPDATES ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $issue_id = intval($_POST['issue_id']);
    $new_status = $_POST['new_status'];

    // 1. Update the issue status in the database
    $stmt = $conn->prepare("UPDATE issues SET status = ? WHERE issue_id = ? AND ward_no = ?");
    $stmt->bind_param("sii", $new_status, $issue_id, $ward_no);
    
    if ($stmt->execute()) {
        // 2. Fetch Citizen ID to send them a notification alert
        $res = $conn->query("SELECT citizen_id, title FROM issues WHERE issue_id = $issue_id");
        $issue_data = $res->fetch_assoc();
        $c_id = $issue_data['citizen_id'];
        $title = $issue_data['title'];
        
        $msg = "Ward Update: Your report on $title is now $new_status.";
        $conn->query("INSERT INTO notifications (message, user_id, user_type) VALUES ('$msg', $c_id, 'citizen')");
        
        $success_msg = "Status updated and citizen notified!";
    }
}

// --- FETCH ALL WARD ISSUES ---
$sql = "SELECT issues.*, departments.name as dept_name, citizens.name as c_name 
        FROM issues 
        JOIN departments ON issues.dept_id = departments.dept_id 
        JOIN citizens ON issues.citizen_id = citizens.citizen_id 
        WHERE issues.ward_no = ? AND issues.status != 'Pending Approval'
        ORDER BY issues.date_reported DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ward_no);
$stmt->execute();
$result = $stmt->get_result();

$page_title = "Ward $ward_no Officer Dashboard";
include 'includes/header.php';
?>

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Ward <?php echo $ward_no; ?> Oversight</h2>
        <?php if($success_msg): ?> <span style="color: green; font-weight: bold;"><?php echo $success_msg; ?></span> <?php endif; ?>
    </div>

    <div class="form-card">
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Issue Details</th>
                    <th>Citizen</th>
                    <th>Status</th>
                    <th>Update</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['dept_name']); ?></strong></td>
                    <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['c_name']); ?></td>
                    <td><span class="status-<?php echo str_replace(' ', '', $row['status']); ?>"><?php echo $row['status']; ?></span></td>
                    <td>
                        <form method="POST" style="display:flex; gap:5px;">
                            <input type="hidden" name="issue_id" value="<?php echo $row['issue_id']; ?>">
                            <select name="new_status" style="padding: 5px;">
                                <option value="Pending" <?php if($row['status']=='Pending') echo 'selected';?>>Received</option>
                                <option value="In Progress" <?php if($row['status']=='In Progress') echo 'selected';?>>In Progress</option>
                                <option value="Resolved" <?php if($row['status']=='Resolved') echo 'selected';?>>Resolved</option>
                            </select>
                            <button type="submit" name="update_status" class="btn-primary" style="padding: 5px 10px;">Save</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>