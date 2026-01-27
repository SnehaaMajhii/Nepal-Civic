<?php
session_start();
include "includes/db.php";

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Unauthorized");
}

if (
    !isset($_GET['type'], $_GET['id']) ||
    !is_numeric($_GET['id'])
) {
    die("Invalid request");
}

$type = $_GET['type'];
$id   = (int) $_GET['id'];

if ($type === 'citizen') {

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE citizen SET is_active = 0 WHERE citizen_id = ?"
    );

    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $id);

    if (!mysqli_stmt_execute($stmt)) {
        die("Execute failed: " . mysqli_stmt_error($stmt));
    }

    mysqli_stmt_close($stmt);

    header("Location: admin_dashboard.php?page=citizens");
    exit();
}

if ($type === 'staff') {
    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM ward_staff WHERE staff_id = ?"
    );
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: admin_dashboard.php?page=staff");
    exit();
}

die("Invalid type");
