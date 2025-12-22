<?php
include 'includes/db.php';
// Role-Based Access Control: Security check (SRS 5.5 & 96)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=Unauthorized Access");
    exit();
}

// Update Status or Department (SRS 3.2.2)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_issue'])) {
    $issue_id = $_POST['issue_id'];
    $new_status = $_POST['status'];
    
    $update_query = "UPDATE issues SET status='$new_status' WHERE id='$issue_id'";
    mysqli_query($conn, $update_query);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Nepal Civic</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="header-content">
            <h2>Nepal Civic Admin</h2>
            <span>Welcome, Admin</span>
        </div>
        <a href="logout.php"><button class="danger">Logout</button></a>
    </header>

    <div class="container">
        [cite_start]<h3>Civic Issue Management [cite: 2]</h3>
        
        <div class="grid">
            <?php
            $counts = mysqli_query($conn, "SELECT status, COUNT(*) as total FROM issues GROUP BY status");
            while($c = mysqli_fetch_assoc($counts)) {
                echo "<div class='card'><h4>{$c['status']}</h4><p>{$c['total']} Issues</p></div>";
            }
            ?>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Tracking ID</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Manage</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $issues = mysqli_query($conn, "SELECT * FROM issues ORDER BY created_at DESC");
                while($row = mysqli_fetch_assoc($issues)):
                ?>
                <tr>
                    <td><strong><?php echo $row['tracking_id']; ?></strong> [cite: 29]</td>
                    <td><?php echo $row['category']; ?></td>
                    <td><?php echo $row['location']; ?></td>
                    <td><span class="status <?php echo str_replace(' ', '-', $row['status']); ?>"><?php echo $row['status']; ?></span></td>
                    <td>
                        <form method="POST" style="display:flex; gap:5px;">
                            <input type="hidden" name="issue_id" value="<?php echo $row['id']; ?>">
                            <select name="status">
                                <option value="Pending" <?php if($row['status']=='Pending') echo 'selected'; ?>>Pending</option>
                                <option value="In Progress" <?php if($row['status']=='In Progress') echo 'selected'; ?>>In Progress</option>
                                <option value="Resolved" <?php if($row['status']=='Resolved') echo 'selected'; ?>>Resolved</option>
                            </select>
                            <button type="submit" name="update_issue" style="padding:5px 10px;">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>