<?php
include 'includes/db.php';

// 1. SESSION CHECK
if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { 
    header("Location: login.php"); exit(); 
}

$msg = ""; $success_msg = ""; $error_msg = "";

// --- HELPER: AUDIT LOG FUNCTION ---
function log_action($conn, $issue_id, $action_by, $role, $type, $desc) {
    $stmt = $conn->prepare("INSERT INTO issue_logs (issue_id, action_by, user_role, action_type, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $issue_id, $action_by, $role, $type, $desc);
    $stmt->execute();
    $stmt->close();
}

// --- 2. REGISTER WARD MEMBER (Chairperson, Secretary, etc.) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_member'])) {
    $name = trim($_POST['m_name']); 
    $email = trim($_POST['m_email']);
    $pass = password_hash($_POST['m_pass'], PASSWORD_DEFAULT); // Use password_hash() in production
    $designation = $_POST['m_designation'];
    $ward_id = intval($_POST['ward_id']);

    $check = $conn->query("SELECT * FROM ward_members WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $error_msg = "Error: Email is already registered.";
    } else {
        $stmt = $conn->prepare("INSERT INTO ward_members (full_name, email, password, designation, ward_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $name, $email, $pass, $designation, $ward_id);
        
        if($stmt->execute()) {
            $msg = "Success: $designation added to the Ward!";
        } else { $msg = "Database Error."; }
        $stmt->close();
    }
}

// --- 3. PROCESS ISSUE (APPROVE/REJECT) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['process_issue'])) {
    $id = intval($_POST['issue_id']);
    $decision = $_POST['decision'];
    
    if ($decision === 'approve') {
        // Approved -> Assign Ward + Set Deadline
        $new_status = 'Forwarded';
        $assigned_ward = intval($_POST['assigned_ward_id']);
        $exp_date = $_POST['expected_date'];
        $reason = null;
        
        $sql = "UPDATE issues SET status=?, assigned_ward_id=?, expected_resolution_date=?, rejection_reason=NULL WHERE issue_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisi", $new_status, $assigned_ward, $exp_date, $id);
        
        $log_desc = "Approved & Forwarded to Ward $assigned_ward. Deadline: $exp_date";
        
    } else {
        // Rejected -> Save Reason
        $new_status = 'Rejected';
        $reason = trim($_POST['rejection_reason']);
        
        $sql = "UPDATE issues SET status=?, rejection_reason=? WHERE issue_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $new_status, $reason, $id);
        
        $log_desc = "Rejected. Reason: $reason";
    }

    if ($stmt->execute()) {
        $success_msg = "Issue processed successfully.";
        
        // A. Log the Action
        log_action($conn, $id, 'Admin', 'Admin', 'Review', $log_desc);

        // B. Notify Citizen
        $res = $conn->query("SELECT citizen_id FROM issues WHERE issue_id = $id");
        if ($row_c = $res->fetch_assoc()) {
            $note = ($decision === 'approve') 
                ? "Your report has been forwarded to Ward $assigned_ward. Est. Completion: $exp_date" 
                : "Your report was rejected. Reason: $reason";
            $conn->query("INSERT INTO notifications (message, user_id, user_type) VALUES ('$note', {$row_c['citizen_id']}, 'citizen')");
        }
    } else {
        $error_msg = "Database Error: " . $conn->error;
    }
    $stmt->close();
}

// Fetch Data for Dropdowns & Tables
$wards_result = $conn->query("SELECT * FROM wards ORDER BY ward_no ASC");
// Fetch Pending Issues
$new_reqs = $conn->query("SELECT i.*, d.name as dname FROM issues i JOIN departments d ON i.dept_id = d.dept_id WHERE status = 'Pending Approval' ORDER BY issue_id DESC");

$page_title = "Admin Panel";
include 'includes/header.php';
?>

<div class="container">
    <h2 style="color: var(--nepal-red);">Admin Control Center</h2>

    <?php if($success_msg) echo "<p class='success-msg'>$success_msg</p>"; ?>
    <?php if($error_msg) echo "<p class='error-msg'>$error_msg</p>"; ?>

    <div class="form-card admin-card">
        <h3>Register Ward Member</h3>
        <p style="font-size:0.9em; color:#666;">Create accounts for Ward Chairpersons, Secretaries, etc.</p>
        <?php if($msg) echo "<p style='color:blue;'>$msg</p>"; ?>
        
        <form method="POST" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <select name="ward_id" required style="flex: 1;">
                <option value="" disabled selected>Select Ward</option>
                <?php 
                if ($wards_result->num_rows > 0) {
                    mysqli_data_seek($wards_result, 0); 
                    while($w = $wards_result->fetch_assoc()) { echo "<option value='".$w['ward_id']."'>Ward ".$w['ward_no']."</option>"; } 
                }
                ?>
            </select>
            <select name="m_designation" required style="flex: 1;">
                <option value="" disabled selected>Select Role</option>
                <option value="Ward Chairperson">Ward Chairperson</option>
                <option value="Ward Secretary">Ward Secretary</option>
                <option value="Ward Member">Ward Member</option>
            </select>
            <input type="text" name="m_name" placeholder="Full Name" required style="flex: 1;">
            <input type="email" name="m_email" placeholder="Email" required style="flex: 1;">
            <input type="password" name="m_pass" placeholder="Password" required style="flex: 1;">
            <button type="submit" name="add_member" class="btn-primary" style="flex: 0.5;">Add Member</button>
        </form>
    </div>

    <div class="form-card admin-card" style="margin-top: 20px;">
        <h3>Pending Reports</h3>
        <table>
            <thead><tr><th>Category</th><th>Title</th><th>ID</th><th>Action</th></tr></thead>
            <tbody>
                <?php 
                if ($new_reqs && $new_reqs->num_rows > 0) {
                    while($row = $new_reqs->fetch_assoc()): 
                        $modalId = "modal-" . $row['issue_id'];
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['dname']); ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo $row['issue_id']; ?></td>
                    <td><button class="btn-review" onclick="openModal('<?php echo $modalId; ?>')">Review</button></td>
                </tr>

                <div id="<?php echo $modalId; ?>" class="modal-overlay">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeModal('<?php echo $modalId; ?>')">&times;</span>
                        <h3>Review Report #<?php echo $row['issue_id']; ?></h3>
                        
                        <div class="modal-grid">
                            <div>
                                <h4>Evidence:</h4>
                                <?php if (!empty($row['image'])): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" class="evidence-img">
                                    <br><a href="uploads/<?php echo htmlspecialchars($row['image']); ?>" target="_blank" style="color:blue;">View Full Image</a>
                                <?php else: ?>
                                    <p style="color:#777;">No image attached.</p>
                                <?php endif; ?>
                                <h4>Description:</h4>
                                <p style="background:#f9f9f9; padding:10px;"><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                            </div>

                            <div>
                                <h4>Decision:</h4>
                                <form method="POST">
                                    <input type="hidden" name="issue_id" value="<?php echo $row['issue_id']; ?>">
                                    <input type="hidden" name="process_issue" value="1">
                                    
                                    <select name="decision" id="decision-<?php echo $row['issue_id']; ?>" onchange="toggleDecision('<?php echo $row['issue_id']; ?>')" style="margin-bottom:15px;">
                                        <option value="approve">Approve (Assign Ward)</option>
                                        <option value="reject">Reject</option>
                                    </select>

                                    <div id="assign-box-<?php echo $row['issue_id']; ?>">
                                        <label><strong>Assign to Ward:</strong></label>
                                        <select name="assigned_ward_id" required style="margin-bottom:10px;">
                                            <option value="" disabled selected>Choose Ward...</option>
                                            <?php 
                                            // Reuse ward list
                                            mysqli_data_seek($wards_result, 0);
                                            while($w = $wards_result->fetch_assoc()) { 
                                                echo "<option value='".$w['ward_id']."'>Ward ".$w['ward_no']."</option>"; 
                                            } 
                                            ?>
                                        </select>

                                        <label><strong>Expected Resolution Date:</strong></label>
                                        <input type="date" name="expected_date" required min="<?php echo date('Y-m-d'); ?>">
                                        <small style="color:#666;">This date will trigger system alerts.</small>
                                    </div>

                                    <div id="reason-box-<?php echo $row['issue_id']; ?>" style="display:none;">
                                        <label style="color:red;"><strong>Reason for Rejection:</strong></label>
                                        <textarea name="rejection_reason" rows="3"></textarea>
                                    </div>

                                    <button type="submit" class="btn-primary" style="margin-top:15px; width:100%;">Confirm Decision</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; } else { echo "<tr><td colspan='4'>No pending reports.</td></tr>"; } ?>
            </tbody>
        </table>
    </div>
</div>
<script src="assets/main.js"></script>
<?php include 'includes/footer.php'; ?>