<?php
// INCLUDED inside citizen_dashboard.php

$citizen_id = (int) $_SESSION['citizen_id'];

$issues = mysqli_query($conn, "
    SELECT 
        issue.*,
        department.department_name
    FROM issue
    JOIN department ON issue.department_id = department.department_id
    WHERE issue.citizen_id = $citizen_id
    ORDER BY issue.date_reported DESC
");
?>

<h2>My Reported Issues</h2>

<?php if (mysqli_num_rows($issues) === 0) { ?>
    <div class="empty-state">
        You have not reported any issues yet.
    </div>
<?php } ?>

<?php while ($row = mysqli_fetch_assoc($issues)) { ?>

    <?php
        // -----------------------------
        // Reminder visibility logic
        // -----------------------------
        $canSendReminder = false;

        if (
            !empty($row['expected_resolution_date']) &&
            in_array($row['status'], ['pending', 'assigned']) &&
            strtotime($row['expected_resolution_date']) < strtotime(date('Y-m-d')) &&
            (
                empty($row['last_reminder_sent_at']) ||
                date('Y-m-d', strtotime($row['last_reminder_sent_at'])) < date('Y-m-d')
            )
        ) {
            $canSendReminder = true;
        }
    ?>

    <div class="issue-card">

        <h3><?= htmlspecialchars($row['title']); ?></h3>

        <p>
            <b>Department:</b>
            <?= htmlspecialchars($row['department_name']); ?>
        </p>

        <p>
            <b>Status:</b>
            <span class="status-<?= $row['status']; ?>">
                <?= ucfirst($row['status']); ?>
            </span>
        </p>

        <?php if (!empty($row['expected_resolution_date'])) { ?>
            <p>
                <b>Expected Resolution:</b>
                <?= date("d M Y", strtotime($row['expected_resolution_date'])); ?>
            </p>
        <?php } ?>

        <p><?= nl2br(htmlspecialchars($row['description'])); ?></p>

        <!-- ================= REMINDER BUTTON ================= -->
        <?php if ($canSendReminder) { ?>
            <div class="issue-actions">
                <a href="send_reminder.php?issue_id=<?= $row['issue_id']; ?>">
                    <button>Send Reminder</button>
                </a>
            </div>
        <?php } ?>

    </div>

<?php } ?>
