<?php
// department_dashboard.php
include 'includes/db.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manager') {
    header("Location: login.php");
    exit();
}

$manager_id = $_SESSION['user_id'];
$dept_id = $_SESSION['dept_id'];
$ward_no = $_SESSION['ward_no'];
$message = "";

// --- HANDLE STATUS UPDATE ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_issue_id'])) {
    $issue_id = $_POST['update_issue_id'];
    $new_status = $_POST['new_status'];
    
    $resolved_date = ($new_status == 'Resolved') ? date('Y-m-d H:i:s') : NULL;

    // Update the Issue Table
    $sql = "UPDATE issues SET status = ?, resolved_date = ? 
            WHERE issue_id = ? AND dept_id = ? AND ward_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiii", $new_status, $resolved_date, $issue_id, $dept_id, $ward_no);
    
    if ($stmt->execute()) {
        $message = "Status updated to $new_status.";

        // --- NOTIFICATION LOGIC ---
        $find_cit = $conn->prepare("SELECT citizen_id, title FROM issues WHERE issue_id = ?");
        $find_cit->bind_param("i", $issue_id);
        $find_cit->execute();
        $issue_data = $find_cit->get_result()->fetch_assoc();
        
        $cit_id = $issue_data['citizen_id'];
        $title_snippet = substr($issue_data['title'], 0, 20) . "...";

        if ($cit_id) {
            $notif_msg = "Update: Your issue '$title_snippet' is now $new_status.";
            $notif_sql = "INSERT INTO notifications (message, user_id, user_type) VALUES (?, ?, 'citizen')";
            $n_stmt = $conn->prepare($notif_sql);
            $n_stmt->bind_param("si", $notif_msg, $cit_id);
            $n_stmt->execute();
        }
    }
}

// --- FETCH ISSUES (Filtered by Dept, Ward, and Approval Status) ---
$sql = "SELECT issues.*, citizens.name AS citizen_name 
        FROM issues 
        JOIN citizens ON issues.citizen_id = citizens.citizen_id 
        WHERE issues.dept_id = ? 
          AND issues.ward_no = ? 
          AND issues.status != 'Pending Approval' 
        ORDER BY FIELD(status, 'In Progress', 'Pending', 'Resolved', 'Rejected'), date_reported DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $dept_id, $ward_no);
$stmt->execute();
$result = $stmt->get_result();

$page_title = "Ward $ward_no Manager Dashboard";
include 'includes/header.php';
?>

<div class="container">
    <h2>Ward <?php echo $ward_no; ?>: Manage Requests</h2>
    
    <?php if ($message): ?> 
        <p class="error-msg" style="background:#d4edda; color:#155724; border-color:#c3e6cb;"><?php echo $message; ?></p> 
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Issue Details</th>
                    <th>Citizen</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($row['title']); ?></strong><br>
                            <small><?php echo htmlspecialchars($row['description']); ?></small>
                            <?php if($row['photo_url']): ?>
                                <br><a href="<?php echo $row['photo_url']; ?>" target="_blank" style="font-size: 0.8rem;">View Evidence Photo</a>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['citizen_name']); ?></td>
                        <td class="status-<?php echo $row['status']; ?>"><?php echo $row['status']; ?></td>
                        <td>
                            <form method="POST" style="display:flex; gap:5px;">
                                <input type="hidden" name="update_issue_id" value="<?php echo $row['issue_id']; ?>">
                                <select name="new_status" style="padding:5px; width:auto; margin:0;">
                                    <option value="Pending" <?php if($row['status']=='Pending') echo 'selected';?>>Pending</option>
                                    <option value="In Progress" <?php if($row['status']=='In Progress') echo 'selected';?>>In Progress</option>
                                    <option value="Resolved" <?php if($row['status']=='Resolved') echo 'selected';?>>Resolved</option>
                                    <option value="Rejected" <?php if($row['status']=='Rejected') echo 'selected';?>>Reject</option>
                                </select>
                                <button type="submit" class="btn-primary" style="width:auto; padding:5px 10px; margin:0;">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align: center; padding: 20px; color: #666;">No active issues to display for Ward <?php echo $ward_no; ?>.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>