<?php
include "includes/session.php";
include "includes/db.php";

// Citizen only
if ($_SESSION['user_role'] !== 'citizen') {
    header("Location: login.php");
    exit();
}

$issue_id   = (int) $_GET['id'];
$citizen_id = $_SESSION['citizen_id'];

// Prevent duplicate vote
$check = mysqli_query($conn, "
    SELECT * FROM urgency_vote 
    WHERE issue_id=$issue_id AND citizen_id=$citizen_id
");

if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "
        INSERT INTO urgency_vote (issue_id, citizen_id)
        VALUES ($issue_id, $citizen_id)
    ");
}

header("Location: community_feed.php");
exit();
