<?php
// feed.php
include 'includes/db.php';

// Filter Logic
$ward_filter = isset($_GET['ward']) ? intval($_GET['ward']) : 0;

if ($ward_filter > 0) {
    $sql = "SELECT issues.*, departments.name as dept_name FROM issues JOIN departments ON issues.dept_id = departments.dept_id WHERE ward_no = ? AND status != 'Pending Approval' ORDER BY date_reported DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ward_filter);
    $stmt->execute();
    $result = $stmt->get_result();
    $title_text = "Issues in Ward " . $ward_filter;
} else {
    $sql = "SELECT issues.*, departments.name as dept_name FROM issues JOIN departments ON issues.dept_id = departments.dept_id WHERE status != 'Pending Approval' ORDER BY date_reported DESC";
    $result = $conn->query($sql);
    $title_text = "All Community Issues";
}

$page_title = "Community Issues - Nepal Civic";
include 'includes/header.php';
?>

<div class="container">
    
    <div class="feed-controls">
        <h3><?php echo $title_text; ?></h3>
        
        <form method="GET" style="display:flex; gap:10px; align-items:center;">
            <input type="number" name="ward" placeholder="Ward No" style="width: 80px; margin:0; padding:5px;">
            <button type="submit" class="btn-primary" style="width:auto; padding:5px 15px;">Filter</button>
            <?php if($ward_filter > 0): ?>
                <a href="feed.php" style="margin-left: 10px;">Show All</a>
            <?php endif; ?>
        </form>

        <?php if(isset($_SESSION['user_id']) && $_SESSION['role'] === 'citizen'): ?>
            <a href="report.php" class="btn-report">Post New Issue</a>
        <?php elseif(!isset($_SESSION['user_id'])): ?>
            <a href="login.php" class="btn-primary" style="width: auto; padding: 8px 15px; background-color: #003893;">Login to Post</a>
        <?php endif; ?>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="issue-card card-<?php echo str_replace(' ', '', $row['status']); ?>">
                <div style="display:flex; justify-content:space-between; align-items: flex-start; gap: 20px;">
                    <div style="flex: 1;">
                        <div style="display:flex; justify-content:space-between;">
                            <h3 style="margin-bottom:5px;"><?php echo htmlspecialchars($row['title']); ?></h3>
                            <span class="status-<?php echo $row['status']; ?>"><?php echo $row['status']; ?></span>
                        </div>
                        
                        <div class="meta-info">
                            <span class="ward-badge">Ward <?php echo $row['ward_no']; ?></span> &bull; 
                            <?php echo htmlspecialchars($row['dept_name']); ?> &bull; 
                            <?php echo date("M d, Y", strtotime($row['date_reported'])); ?>
                        </div>

                        <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                        
                        <?php if($row['status'] == 'Resolved' && $row['resolved_date']): ?>
                            <p style="margin-top:10px; color:#15803d; font-size:0.9rem;">
                                <strong>✓ Resolved on:</strong> <?php echo date("M d, Y", strtotime($row['resolved_date'])); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <?php if($row['photo_url']): ?>
                    <div class="issue-photo-preview">
                        <a href="<?php echo $row['photo_url']; ?>" target="_blank">
                            <img src="<?php echo $row['photo_url']; ?>" alt="Issue Photo" style="width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center; padding:40px; color:#666;">No issues reported yet.</p>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>