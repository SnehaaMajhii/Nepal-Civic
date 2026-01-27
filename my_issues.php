<?php
// ===============================
// MY ISSUES – CITIZEN VIEW
// ===============================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "includes/db.php";

$citizen_id = (int) $_SESSION['citizen_id'];
?>

<h2>My Reported Issues</h2>

<!-- ===============================
     ISSUE FILTERS
=============================== -->
<div class="issue-filters">
    <input
        type="text"
        id="searchTitle"
        placeholder="Search by title"
    >

    <select id="filterDepartment">
        <option value="">All Departments</option>
        <?php
        $deptQ = mysqli_query($conn, "SELECT department_id, department_name FROM department");
        while ($d = mysqli_fetch_assoc($deptQ)) {
            echo "<option value='{$d['department_id']}'>{$d['department_name']}</option>";
        }
        ?>
    </select>
    <select id="filterWard">
        <option value="">All Wards</option>
        <?php
        $wardQ = mysqli_query($conn, "SELECT DISTINCT ward_id FROM issue ORDER BY ward_id");
        while ($w = mysqli_fetch_assoc($wardQ)) {
            echo "<option value='{$w['ward_id']}'>Ward {$w['ward_id']}</option>";
        }
        ?>
    </select>


    <select id="filterStatus">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="assigned">Assigned</option>
        <option value="resolved">Resolved</option>
        <option value="rejected">Rejected</option>
    </select>
</div>

<!-- ===============================
     ISSUE TABLE
=============================== -->
<div class="table-card">
    <table class="styled-table issue-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Department</th>
                <th>Ward</th>
                <th>Status</th>
                <th>Expected Resolution</th>
            </tr>
        </thead>

        <!-- IMPORTANT: tbody is EMPTY (AJAX FILLS THIS) -->
        <tbody id="issuesTableBody">
            <tr>
                <td colspan="6" style="text-align:center; padding:20px;">
                    Loading issues...
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- ===============================
     PAGINATION
=============================== -->
<div id="pagination" class="pagination"></div>

<!-- ===============================
     ISSUE DETAIL MODAL
=============================== -->
<div id="issueModal" class="modal-overlay">
    <div class="modal-box">
        <span class="modal-close">&times;</span>

        <h2 id="m_title"></h2>

        <p><b>Department:</b> <span id="m_department"></span></p>
        <p><b>Status:</b> <span id="m_status"></span></p>
        <p><b>Ward:</b> <span id="m_ward"></span></p>
        <p><b>Date Reported:</b> <span id="m_reported"></span></p>
        <p><b>Expected Resolution:</b> <span id="m_expected"></span></p>

        <p id="m_description" class="modal-desc"></p>

        <img
            id="m_image"
            class="modal-image"
            alt="Issue Image"
            style="display:none;"
        >
    </div>
</div>
