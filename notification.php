<?php
// notification.php
include 'includes/db.php';

// 1. Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role']; // 'citizen' or 'manager'

// 2. Mark all as Read (Since user is viewing them now)
$update_sql = "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND user_type = ?";
$stmt = $conn->prepare($update_sql);
$stmt->bind_param("is", $user_id, $role);
$stmt->execute();

// 3. Fetch Notifications
$sql = "SELECT * FROM notifications WHERE user_id = ? AND user_type = ? ORDER BY date_sent DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $role);
$stmt->execute();
$result = $stmt->get_result();

$page_title = "My Notifications";
include 'includes/header.php';
?>

<div class="container">
    <div class="form-card" style="border-top: 5px solid #C41E3A;"> <h2>My Notifications</h2>
        
        <?php if ($result->num_rows > 0): ?>
            <ul style="list-style: none; padding: 0;">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li style="
                        border-bottom: 1px solid #eee; 
                        padding: 15px 0; 
                        display: flex; 
                        justify-content: space-between; 
                        align-items: center;
                        <?php echo ($row['is_read'] == 0) ? 'background-color: #f9f9f9; font-weight: bold;' : ''; ?>
                    ">
                        <div>
                            <span style="font-size: 1.1rem; color: #333;">
                                <?php echo htmlspecialchars($row['message']); ?>
                            </span>
                            <br>
                            <small style="color: #888;">
                                <?php echo date("M d, Y - h:i A", strtotime($row['date_sent'])); ?>
                            </small>
                        </div>
                        
                        <?php if ($row['is_read'] == 0): ?>
                            <span style="background: #C41E3A; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.8rem;">New</span>
                        <?php endif; ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p style="text-align: center; color: #666; padding: 20px;">You have no new notifications.</p>
        <?php endif; ?>
        
        <div style="text-align: center; margin-top: 20px;">
            <?php if ($role == 'citizen'): ?>
                <a href="citizen_dashboard.php">Back to Dashboard</a>
            <?php else: ?>
                <a href="department_dashboard.php">Back to Dashboard</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>