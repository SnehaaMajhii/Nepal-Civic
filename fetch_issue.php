<?php
session_start();
require "includes/db.php";

header('Content-Type: application/json');

/* ===============================
   COMMON SETUP
=============================== */
$action = $_GET['action'] ?? '';
$mode   = $_GET['mode'] ?? 'citizen'; // citizen | admin

$user_role = $_SESSION['user_role'] ?? '';

/* ===============================
   SECURITY CHECK
=============================== */
if ($mode === 'citizen' && $user_role !== 'citizen') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($mode === 'admin' && $user_role !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

/* =====================================================
   ACTION 1: FETCH ISSUE LIST (FILTER + PAGINATION)
===================================================== */
if ($action === 'list') {

    $page   = max(1, (int)($_GET['page'] ?? 1));
    $limit  = 10;
    $offset = ($page - 1) * $limit;

    $title      = $_GET['title'] ?? '';
    $status     = $_GET['status'] ?? '';
    $department = $_GET['department'] ?? '';
    $ward       = $_GET['ward'] ?? '';

    /* ---------- WHERE CLAUSE ---------- */
    $where = "WHERE 1";

    if ($mode === 'citizen') {
        $citizen_id = (int)$_SESSION['citizen_id'];
        $where .= " AND issue.citizen_id = $citizen_id";
    }

    if ($title !== '') {
        $title = mysqli_real_escape_string($conn, $title);
        $where .= " AND issue.title LIKE '%$title%'";
    }

    if ($status !== '') {
        $status = mysqli_real_escape_string($conn, $status);
        $where .= " AND issue.status = '$status'";
    }

    if ($department !== '') {
        $where .= " AND issue.department_id = " . (int)$department;
    }

    if ($ward !== '') {
        $where .= " AND issue.ward_id = " . (int)$ward;
    }

    /* ---------- TOTAL COUNT ---------- */
    $countRes = mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM issue
        JOIN department ON issue.department_id = department.department_id
        LEFT JOIN citizen ON issue.citizen_id = citizen.citizen_id
        LEFT JOIN ward ON issue.ward_id = ward.ward_id
        $where
    ");

    $countRow   = mysqli_fetch_assoc($countRes);
    $totalPages = ceil(($countRow['total'] ?? 0) / $limit);

    /* ---------- FETCH DATA ---------- */
    $q = mysqli_query($conn, "
        SELECT 
            issue.issue_id,
            issue.title,
            issue.status,
            issue.urgency_level,
            issue.expected_resolution_date,
            department.department_name,
            citizen.full_name,
            ward.ward_no
        FROM issue
        JOIN department ON issue.department_id = department.department_id
        LEFT JOIN citizen ON issue.citizen_id = citizen.citizen_id
        LEFT JOIN ward ON issue.ward_id = ward.ward_id
        $where
        ORDER BY issue.date_reported DESC
        LIMIT $limit OFFSET $offset
    ");

    $data = [];
    $sr = $offset + 1;

    while ($row = mysqli_fetch_assoc($q)) {
        $row['sr'] = $sr++;
        $row['ward_no'] = $row['ward_no'] ?? '-';
        $row['full_name'] = $row['full_name'] ?? '-';
        $data[] = $row;
    }

    echo json_encode([
        'data' => $data,
        'totalPages' => $totalPages,
        'currentPage' => $page
    ]);
    exit;
}

/* =====================================================
   ACTION 2: FETCH SINGLE ISSUE DETAILS (MODAL)
===================================================== */
if ($action === 'detail') {

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid issue ID']);
        exit;
    }

    $issue_id = (int)$_GET['id'];

    $where = "issue.issue_id = $issue_id";

    if ($mode === 'citizen') {
        $citizen_id = (int)$_SESSION['citizen_id'];
        $where .= " AND issue.citizen_id = $citizen_id";
    }

    $q = mysqli_query($conn, "
        SELECT 
            issue.issue_id,
            issue.title,
            issue.description,
            issue.status,
            issue.urgency_level,
            issue.photo_update,
            department.department_name,
            citizen.full_name,
            ward.ward_no,
            DATE_FORMAT(issue.date_reported, '%d %b %Y') AS date_reported,
            DATE_FORMAT(issue.expected_resolution_date, '%d %b %Y') AS expected_resolution_date
        FROM issue
        JOIN department ON issue.department_id = department.department_id
        LEFT JOIN citizen ON issue.citizen_id = citizen.citizen_id
        LEFT JOIN ward ON issue.ward_id = ward.ward_id
        WHERE $where
        LIMIT 1
    ");

    if (mysqli_num_rows($q) === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Issue not found']);
        exit;
    }

    $row = mysqli_fetch_assoc($q);
    $row['ward_no'] = $row['ward_no'] ?? '-';
    $row['full_name'] = $row['full_name'] ?? '-';

    echo json_encode($row);
    exit;
}

/* =====================================================
   ACTION: STAFF ISSUES (WARD) – LIST + FILTER + HISTORY
===================================================== */
if ($action === 'staff_list') {

    // Staff-only access
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'staff') {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    // Pagination
    $page   = max(1, (int)($_GET['page'] ?? 1));
    $limit  = 10;
    $offset = ($page - 1) * $limit;

    // Ward context
    $ward_id = (int)$_SESSION['ward_id'];

    // Filters
    $title      = $_GET['title'] ?? '';
    $status     = $_GET['status'] ?? '';
    $department = $_GET['department'] ?? '';

    // Dynamic WHERE clause
    $where = "WHERE issue.ward_id = $ward_id";

    if ($title !== '') {
        $title = mysqli_real_escape_string($conn, $title);
        $where .= " AND issue.title LIKE '%$title%'";
    }

    if ($status !== '') {
        $status = mysqli_real_escape_string($conn, $status);
        $where .= " AND issue.status = '$status'";
    }

    if ($department !== '') {
        $where .= " AND issue.department_id = " . (int)$department;
    }

    /* ---------- TOTAL COUNT ---------- */
    $countRes = mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM issue
        $where
    ");

    $total = mysqli_fetch_assoc($countRes)['total'] ?? 0;
    $totalPages = ceil($total / $limit);

    /* ---------- FETCH DATA ---------- */
    $q = mysqli_query($conn, "
        SELECT
            issue.issue_id,
            issue.title,
            issue.status,
            issue.urgency_level,
            issue.expected_resolution_date,
            DATE_FORMAT(issue.date_reported, '%d %b %Y') AS date_reported,
            citizen.full_name,
            department.department_name
        FROM issue
        JOIN citizen ON issue.citizen_id = citizen.citizen_id
        LEFT JOIN department ON issue.department_id = department.department_id
        $where
        ORDER BY issue.date_reported DESC
        LIMIT $limit OFFSET $offset
    ");

    $data = [];
    $sr = $offset + 1;

    while ($row = mysqli_fetch_assoc($q)) {
        $row['sr'] = $sr++;
        $data[] = $row;
    }

    echo json_encode([
        'data' => $data,
        'totalPages' => $totalPages,
        'currentPage' => $page
    ]);
    exit;
}



/* ===============================
   INVALID ACTION
=============================== */
http_response_code(400);
echo json_encode(['error' => 'Invalid action']);
