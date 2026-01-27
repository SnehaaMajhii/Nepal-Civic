<?php
// =======================================
// AUTOMATIC ISSUE REMINDER SCRIPT
// (Run via CRON or manually)
// =======================================

include "includes/db.php";

$sentCount = 0;
$today = date('Y-m-d');

// ---------------------------------------
// Fetch issues overdue or due today
// AND reminder not sent today
// ---------------------------------------
$issueQ = mysqli_query($conn, "
    SELECT 
        issue.issue_id,
        issue.title,
        issue.citizen_id,
        issue.ward_id,
        issue.last_reminder_sent_at
    FROM issue
    WHERE
        expected_resolution_date IS NOT NULL
        AND expected_resolution_date < CURDATE()
        AND status IN ('pending','assigned')
        AND (
            last_reminder_sent_at IS NULL
            OR DATE(last_reminder_sent_at) < CURDATE()
        )
");

    while ($issue = mysqli_fetch_assoc($issueQ)) {

    $issue_id   = (int)$issue['issue_id'];
    $citizen_id = (int)$issue['citizen_id'];
    $ward_id    = (int)$issue['ward_id'];
    $title      = mysqli_real_escape_string($conn, $issue['title']);

    // ---------------------------------------
    // Notify Citizen
    // ---------------------------------------
    mysqli_query($conn, "
        INSERT INTO notification
        (message, citizen_id, is_read, date_sent)
        VALUES
        (
            'Reminder: Your issue \"$title\" is overdue.',
            $citizen_id,
            0,
            NOW()
        )
    ");

    // Notify ADMIN
    mysqli_query($conn, "
    INSERT INTO notification
    (message, admin_id, is_read, date_sent)
    VALUES
    (
        'Reminder sent by citizen for Issue #$issue_id.',
        admin_id,
        0,
        NOW()
    )
    ");

    // Notify STAFF of same ward (optional but recommended)
    mysqli_query($conn, "
    INSERT INTO notification
    (message, staff_id, is_read, date_sent)
    SELECT
        'Reminder sent by citizen for Issue #$issue_id.',
        staff_id,
        0,
        NOW()
    FROM ward_staff
    WHERE ward_id = $ward_id
    ");

    }

    // ---------------------------------------
    // Update reminder timestamp (LIMIT 1/DAY)
    // ---------------------------------------
    mysqli_query($conn, "
        UPDATE issue
        SET last_reminder_sent_at = NOW()
        WHERE issue_id = $issue_id
    ");

    $sentCount++;


echo "Reminder job executed successfully. Reminders sent today: $sentCount";
