<?php
// citizen_dashboard.php
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'citizen') {
    header("Location: login.php");
    exit();
}

$citizen_id = $_SESSION['user_id'];
$citizen_name = $_SESSION['name'];

$sql = "SELECT issues.*, departments.name as dept_name 
        FROM issues 
        JOIN departments ON issues.dept_id = departments.dept_id 
        WHERE citizen_id = ? 
        ORDER BY date_reported DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $citizen_id);
$stmt->execute();
$result = $stmt->get_result();

// VIEW
$page_title = "My Dashboard - Nepal Civic";
include 'includes/header.php';
?>

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2>My Reported Issues</h2>
        <a href="report.php" class="btn-report">Report New Issue</a>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Priority</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['dept_name']); ?></td>
                        <td><?php echo date("M d, Y", strtotime($row['date_reported'])); ?></td>
                        <td class="status-<?php echo $row['status']; ?>"><?php echo $row['status']; ?></td>
                        <td><?php echo $row['priority']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You have not reported any issues yet.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>