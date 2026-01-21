<?php
// citizen_dashboard.php
include 'includes/db.php';

// Security: If not a citizen, redirect to login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'citizen') {
    header("Location: login.php");
    exit();
}

$citizen_id = $_SESSION['user_id'];
$citizen_name = $_SESSION['username'];

// Fetch all issues reported by this specific citizen
$sql = "SELECT issues.*, departments.name as dept_name 
        FROM issues 
        JOIN departments ON issues.dept_id = departments.dept_id 
        WHERE citizen_id = ? 
        ORDER BY date_reported DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $citizen_id);
$stmt->execute();
$result = $stmt->get_result();

$page_title = "My Dashboard - Nepal Civic";
include 'includes/header.php';
?>

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin: 20px 0;">
        <h2>Welcome, <?php echo htmlspecialchars($citizen_name); ?></h2>
        <a href="report.php" class="btn-primary" style="width: auto; padding: 10px 20px;">+ Report New Issue</a>
    </div>

    <div class="form-card" style="border-top: 5px solid #003893;">
        <h3>My Reported Issues</h3>
        <p style="color: #666; margin-bottom: 20px;">Track the live status of your submissions below.</p>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Issue Details</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Date Reported</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($row['title']); ?></strong>
                                
                                <?php if($row['status'] === 'Rejected' && !empty($row['rejection_reason'])): ?>
                                    <div style="margin-top: 5px; padding: 5px; background: #fff5f5; border-left: 3px solid #C41E3A; font-size: 0.85rem; color: #C41E3A;">
                                        <strong>Rejection Reason:</strong> <?php echo htmlspecialchars($row['rejection_reason']); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if($row['status'] === 'Pending Approval'): ?>
                                    <br><small style="color: #d97706;">(Waiting for Admin to review your report)</small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['dept_name']); ?></td>
                            <td>
                                <span class="status-<?php echo str_replace(' ', '', $row['status']); ?>" 
                                      style="padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 0.85rem;">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td><?php echo date("M d, Y", strtotime($row['date_reported'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 40px;">
                <p style="color: #888;">You haven't reported any issues yet.</p>
                <a href="report.php" style="color: #003893; font-weight: bold;">Start by filing your first report.</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>