<?php
// This file is INCLUDED inside citizen_dashboard.php

$citizen_id = $_SESSION['citizen_id'];

$issues = mysqli_query($conn, "
    SELECT issue.*, department.department_name
    FROM issue
    JOIN department ON issue.department_id = department.department_id
    WHERE issue.citizen_id = $citizen_id
    ORDER BY issue.date_reported DESC
");
?>

<h2>My Reported Issues</h2>

<?php if (mysqli_num_rows($issues) == 0) { ?>
    <p>You have not reported any issues yet.</p>
<?php } ?>

<?php while ($row = mysqli_fetch_assoc($issues)) { ?>
    <div class="issue-card">

        <h3><?php echo htmlspecialchars($row['title']); ?></h3>

        <p>
            <b>Department:</b>
            <?php echo htmlspecialchars($row['department_name']); ?>
        </p>

        <p>
            <b>Status:</b>
            <span class="status-<?php echo $row['status']; ?>">
                <?php echo ucfirst($row['status']); ?>
            </span>
        </p>

        <p><?php echo htmlspecialchars($row['description']); ?></p>

        <?php if ($row['status'] !== 'resolved') { ?>
            <a href="send_remainder.php?issue_id=<?php echo $row['issue_id']; ?>">
                <button>Send Remainder</button>
            </a>
        <?php } ?>

    </div>
<?php } ?>
