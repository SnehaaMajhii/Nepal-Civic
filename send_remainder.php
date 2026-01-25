<?php
include "includes/db.php";

/* ======================
   FIND OVERDUE ISSUES
====================== */
$today = date('Y-m-d');

$issuesQ = mysqli_query($conn, "
    SELECT issue_id, citizen_id, expected_resolution_date
    FROM issue
    WHERE 
        status != 'resolved'
        AND expected_resolution_date IS NOT NULL
        AND expected_resolution_date < '$today'
        AND reminder_sent = 0
");

while ($row = mysqli_fetch_assoc($issuesQ)) {

    $issue_id   = (int) $row['issue_id'];
    $citizen_id = (int) $row['citizen_id'];
    $date       = $row['expected_resolution_date'];

    /* ======================
       SEND NOTIFICATION
    ====================== */
    mysqli_query($conn, "
        INSERT INTO notification
        (message, citizen_id, is_read, date_sent)
        VALUES
        (
            'Reminder: Your issue (ID: $issue_id) has passed the expected resolution date ($date).',
            $citizen_id,
            0,
            NOW()
        )
    ");

    /* ======================
       LOG REMINDER
    ====================== */
    mysqli_query($conn, "
        INSERT INTO issue_logs
        (issue_id, action_by, user_role, action_type, description, created_at)
        VALUES
        (
            $issue_id,
            0,
            'system',
            'Reminder',
            'Automatic reminder sent to citizen',
            NOW()
        )
    ");

    /* ======================
       MARK REMINDER SENT
    ====================== */
    mysqli_query($conn, "
        UPDATE issue
        SET reminder_sent = 1
        WHERE issue_id = $issue_id
    ");
}

echo "Reminder check completed.";