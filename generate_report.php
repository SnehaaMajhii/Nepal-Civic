<?php
include "includes/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ======================
   ACCESS CONTROL
====================== */
if (!isset($_SESSION['user_role'])) {
    die("Unauthorized access");
}

/* ======================
   VALIDATE ISSUE ID
====================== */
if (!isset($_GET['issue_id']) || !is_numeric($_GET['issue_id'])) {
    die("Invalid issue ID");
}

$issue_id = (int) $_GET['issue_id'];

/* ======================
   FETCH ISSUE DETAILS
====================== */
$issueQ = mysqli_query($conn, "
    SELECT 
        issue.*,
        citizen.full_name AS citizen_name,
        department.department_name,
        ward.ward_no
    FROM issue
    JOIN citizen ON issue.citizen_id = citizen.citizen_id
    JOIN department ON issue.department_id = department.department_id
    JOIN ward ON issue.ward_id = ward.ward_id
    WHERE issue.issue_id = $issue_id
");

if (mysqli_num_rows($issueQ) === 0) {
    die("Issue not found");
}

$issue = mysqli_fetch_assoc($issueQ);

/* ======================
   FETCH ISSUE LOGS
====================== */
$logsQ = mysqli_query($conn, "
    SELECT user_role, action_type, description, created_at
    FROM issue_logs
    WHERE issue_id = $issue_id
    ORDER BY created_at ASC
");

/* ======================
   URGENCY FIX (TEXT)
====================== */
$urgency = 'N/A';
if (!empty($issue['urgency_level'])) {
    $urgency = ucfirst($issue['urgency_level']); // low → Low
}

/* ======================
   LOAD FPDF (ALREADY FIXED)
====================== */
require_once "fpdf/fpdf.php";

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 15);

/* ======================
   TITLE
====================== */
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Nepal Civic - Issue Report', 0, 1, 'C');
$pdf->Ln(6);

/* ======================
   ISSUE DETAILS
====================== */
$pdf->SetFont('Arial', '', 12);

$pdf->Cell(45, 8, 'Issue Title:', 0, 0);
$pdf->Cell(0, 8, $issue['title'], 0, 1);

$pdf->Cell(45, 8, 'Citizen:', 0, 0);
$pdf->Cell(0, 8, $issue['citizen_name'], 0, 1);

$pdf->Cell(45, 8, 'Ward:', 0, 0);
$pdf->Cell(0, 8, 'Ward ' . $issue['ward_no'], 0, 1);

$pdf->Cell(45, 8, 'Department:', 0, 0);
$pdf->Cell(0, 8, $issue['department_name'], 0, 1);

$pdf->Cell(45, 8, 'Status:', 0, 0);
$pdf->Cell(0, 8, ucfirst($issue['status']), 0, 1);

$pdf->Cell(45, 8, 'Urgency:', 0, 0);
$pdf->Cell(0, 8, $urgency, 0, 1);

$pdf->Ln(6);

/* ======================
   DESCRIPTION
====================== */
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'Description:', 0, 1);

$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(0, 8, $issue['description']);
$pdf->Ln(6);

/* ======================
   ISSUE TIMELINE
====================== */
$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(0, 10, 'Issue Activity Log', 0, 1);
$pdf->Ln(2);

$pdf->SetFont('Arial', '', 11);

if (mysqli_num_rows($logsQ) === 0) {
    $pdf->Cell(0, 8, 'No activity logs found.', 0, 1);
} else {
    while ($log = mysqli_fetch_assoc($logsQ)) {

        $logLine = 
            date("Y-m-d H:i", strtotime($log['created_at'])) .
            " | " .
            ucfirst($log['user_role']) .
            " | " .
            $log['action_type'] .
            " - " .
            $log['description'];

        $pdf->MultiCell(0, 7, $logLine);
        $pdf->Ln(1);
    }
}

/* ======================
   FOOTER
====================== */
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 8, 'Generated on ' . date("Y-m-d H:i:s"), 0, 1, 'R');

/* ======================
   OUTPUT
====================== */
$pdf->Output('I', 'issue_report_'.$issue_id.'.pdf');
exit();
