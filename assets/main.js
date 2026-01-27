/* =========================
   HELPER
========================= */
function $(id) {
    return document.getElementById(id);
}

/* =====================================
   FRONTEND VALIDATION (UX ONLY)
   Does NOT replace PHP validation
===================================== */

document.addEventListener("DOMContentLoaded", () => {

    const registerForm = document.getElementById("registerForm");
    if (!registerForm) return;

    registerForm.addEventListener("submit", (e) => {

        const fullName  = document.querySelector("input[name='full_name']").value.trim();
        const email     = document.querySelector("input[name='email']").value.trim();
        const password  = document.querySelector("input[name='password']").value;
        const national  = document.querySelector("input[name='national_id']").value.trim();
        const address   = document.querySelector("input[name='address']").value.trim();
        const ward      = document.querySelector("select[name='ward_id']").value;

        const frontImg  = document.querySelector("input[name='citizenship_front']").files.length;
        const backImg   = document.querySelector("input[name='citizenship_back']").files.length;

        /* =========================
           REQUIRED CHECK
        ========================= */
        if (
            !fullName || !email || !password ||
            !national || !address || !ward ||
            !frontImg || !backImg
        ) {
            alert("All fields are required.");
            e.preventDefault();
            return;
        }

        /* =========================
           EMAIL FORMAT
        ========================= */
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            alert("Please enter a valid email address.");
            e.preventDefault();
            return;
        }

        /* =========================
           PASSWORD STRENGTH
        ========================= */
        const passwordPattern =
            /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&]).{8,}$/;

        if (!passwordPattern.test(password)) {
            alert("Password must be at least 8 characters and include a letter, number, and symbol.");
            e.preventDefault();
            return;
        }

        /* =========================
           FILE TYPE CHECK
        ========================= */
        const allowedTypes = ["image/jpeg", "image/png", "image/jpg"];

        const frontType = document.querySelector("input[name='citizenship_front']").files[0].type;
        const backType  = document.querySelector("input[name='citizenship_back']").files[0].type;

        if (!allowedTypes.includes(frontType) || !allowedTypes.includes(backType)) {
            alert("Only JPG and PNG images are allowed.");
            e.preventDefault();
            return;
        }

        // ✅ If JS passes → PHP will validate again
    });
});


/* =========================
   SIDEBAR ACTIVE LINK
========================= */
document.addEventListener("DOMContentLoaded", () => {
    const links = document.querySelectorAll(".sidebar a");
    const params = new URLSearchParams(window.location.search);
    const currentPage = params.get("page") || "dashboard";

    links.forEach(link => {
        if (link.href.includes("logout.php")) return;

        const linkPage = new URL(link.href).searchParams.get("page") || "dashboard";
        if (linkPage === currentPage) link.classList.add("active");
    });
});

/* =========================
   SIMPLE HOVER EFFECT
========================= */
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".issue-card").forEach(card => {
        card.onmouseenter = () => card.style.transform = "translateY(-3px)";
        card.onmouseleave = () => card.style.transform = "translateY(0)";
    });
});

/* =========================
   CHART FUNCTIONS
========================= */
function drawBarChart(canvasId, dataArr) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || !dataArr || dataArr.length === 0) return;

    const ctx = canvas.getContext("2d");
    canvas.width = 700;
    canvas.height = 280;

    const maxValue = Math.max(...dataArr.map(d => d.value), 1);
    const barWidth = 60;
    const gap = 60;
    const totalWidth = dataArr.length * barWidth + (dataArr.length - 1) * gap;
    const startX = (canvas.width - totalWidth) / 2;
    const baseY = canvas.height - 50;

    ctx.clearRect(0, 0, canvas.width, canvas.height);

    dataArr.forEach((item, i) => {
        const h = (item.value / maxValue) * 150;
        const x = startX + i * (barWidth + gap);
        const y = baseY - h;

        ctx.fillStyle = "#0b3c91";
        ctx.fillRect(x, y, barWidth, h);
        ctx.fillStyle = "#000";
        ctx.textAlign = "center";
        ctx.fillText(item.value, x + barWidth / 2, y - 8);
        ctx.fillText(item.label, x + barWidth / 2, baseY + 18);
    });
}

function drawPieChart(canvasId, dataArr) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || !dataArr || dataArr.length === 0) return;

    const ctx = canvas.getContext("2d");
    canvas.width = 360;
    canvas.height = 360;

    const total = dataArr.reduce((s, d) => s + d.value, 0);
    let angle = -Math.PI / 2;
    const colors = ["#fb8c00", "#1e88e5", "#43a047", "#e53935"];

    dataArr.forEach((d, i) => {
        const slice = (d.value / total) * Math.PI * 2;
        ctx.beginPath();
        ctx.moveTo(180, 180);
        ctx.arc(180, 180, 140, angle, angle + slice);
        ctx.closePath();
        ctx.fillStyle = colors[i % colors.length];
        ctx.fill();
        angle += slice;
    });
}

/* =========================
   INIT CHARTS
========================= */
document.addEventListener("DOMContentLoaded", () => {
    if (typeof statusData !== "undefined") drawPieChart("statusChart", statusData);
    if (typeof deptData !== "undefined") drawBarChart("deptChart", deptData);
    if (typeof wardData !== "undefined") drawBarChart("wardChart", wardData);
});

/* =====================================================
   ISSUE LIST + FILTER + PAGINATION (ADMIN & CITIZEN)
===================================================== */

function initIssueTable(tableBody, pagination) {

    const MODE = window.APP_ROLE || "citizen";
    const isAdmin = MODE === "admin";

    function loadIssues(page = 1) {

        const title =
            document.getElementById("filterTitle")?.value ||
            document.getElementById("searchTitle")?.value ||
            "";

        const department = document.getElementById("filterDepartment")?.value || "";
        const status = document.getElementById("filterStatus")?.value || "";
        const ward = document.getElementById("filterWard")?.value || "";

        fetch(`fetch_issue.php?action=list&mode=${MODE}&page=${page}&title=${title}&department=${department}&status=${status}&ward=${ward}`)
            .then(res => res.json())
            .then(res => {
                renderTable(res.data || []);
                renderPagination(res.totalPages || 0, res.currentPage || 1);
            });
    }

    function renderTable(data) {
        tableBody.innerHTML = "";

        if (!data.length) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="${isAdmin ? 8 : 6}" style="text-align:center;">
                        No issues found
                    </td>
                </tr>`;
            pagination.innerHTML = "";
            return;
        }

        data.forEach(row => {
            tableBody.innerHTML += isAdmin ? `
            <tr class="issue-row" data-id="${row.issue_id}">
                <td>${row.sr}</td>
                <td>${row.title}</td>
                <td>${row.full_name || "-"}</td>
                <td>Ward ${row.ward_no}</td>
                <td>${row.department_name}</td>
                <td class="status-${row.status}">${row.status}</td>
                <td>${row.urgency_level || "-"}</td>
                <td>
                    ${row.status === "pending" ? `
                        <a href="approve_issue.php?id=${row.issue_id}"><button>Approve</button></a>
                        <a href="reject_issue.php?id=${row.issue_id}"><button>Reject</button></a>
                    ` : ""}
                    <a href="generate_report.php?issue_id=${row.issue_id}">
                        <button>PDF</button>
                    </a>
                </td>
            </tr>` : `
            <tr class="issue-row" data-id="${row.issue_id}">
                <td>${row.sr}</td>
                <td>${row.title}</td>
                <td>${row.department_name}</td>
                <td>Ward ${row.ward_no}</td>
                <td class="status-${row.status}">${row.status}</td>
                <td>${row.expected_resolution_date || "-"}</td>
            </tr>`;
        });

        bindRowClicks();
    }

    function renderPagination(total, current) {
        pagination.innerHTML = "";
        for (let i = 1; i <= total; i++) {
            const btn = document.createElement("button");
            btn.textContent = i;
            if (i === current) btn.classList.add("active");
            btn.onclick = () => loadIssues(i);
            pagination.appendChild(btn);
        }
    }

   function bindRowClicks() {
    document.querySelectorAll(".issue-row").forEach(row => {
        row.onclick = e => {

            // 🚫 Ignore clicks on buttons or links
            if (e.target.closest("button") || e.target.closest("a")) return;

            openModal(row.dataset.id);
        };
    });
}


    function openModal(id) {
        fetch(`fetch_issue.php?action=detail&mode=${MODE}&id=${id}`)
            .then(res => res.json())
            .then(d => {
                $("m_title").textContent = d.title;
                $("m_department").textContent = d.department_name;
                $("m_status").textContent = d.status;
                $("m_urgency") && ($("m_urgency").textContent = d.urgency_level || "-");
                $("m_ward").textContent = "Ward " + d.ward_no;
                $("m_reported").textContent = d.date_reported;
                $("m_expected").textContent = d.expected_resolution_date || "-";
                $("m_description").textContent = d.description;

                const img = $("m_image");
                if (d.photo_update) {
                    img.src = "uploads/issues/" + d.photo_update;
                    img.style.display = "block";
                } else {
                    img.style.display = "none";
                }

                $("issueModal").style.display = "flex";
            });
    }

    document.getElementById("filterTitle")?.addEventListener("keyup", () => loadIssues(1));
    document.getElementById("searchTitle")?.addEventListener("keyup", () => loadIssues(1));
    document.getElementById("filterDepartment")?.addEventListener("change", () => loadIssues(1));
    document.getElementById("filterStatus")?.addEventListener("change", () => loadIssues(1));
    document.getElementById("filterWard")?.addEventListener("change", () => loadIssues(1));

    loadIssues();
}
document.addEventListener("click", function (e) {

    // Close by ID (admin)
    if (e.target.id === "closeModal") {
        const modal = document.getElementById("issueModal");
        if (modal) modal.style.display = "none";
    }

    // Close by class (citizen)
    if (e.target.classList.contains("close")) {
        const modal = document.getElementById("issueModal");
        if (modal) modal.style.display = "none";
    }

    // Click outside modal
    if (e.target.id === "issueModal") {
        e.target.style.display = "none";
    }

});


/* =========================
   WAIT FOR TABLE (ADMIN FIX)
========================= */
document.addEventListener("DOMContentLoaded", () => {

    function waitForIssueTable() {
        const tableBody =
            document.getElementById("issueTableBody") ||
            document.getElementById("issuesTableBody");
        const pagination = document.getElementById("pagination");

        if (!tableBody || !pagination) {
            setTimeout(waitForIssueTable, 50);
            return;
        }

        initIssueTable(tableBody, pagination);
    }

    waitForIssueTable();
});


/* =====================================================
   STAFF ASSIGNED ISSUES – TABLE
===================================================== */
document.addEventListener("DOMContentLoaded", () => {

    const tbody = document.getElementById("staffIssueTableBody");
    const pagination = document.getElementById("pagination");

    if (!tbody || !pagination) return;

    function loadStaffIssues(page = 1) {
        const title = document.getElementById("staffSearchTitle")?.value || "";
        const department = document.getElementById("staffFilterDepartment")?.value || "";
        const statusFilter = document.getElementById("staffFilterStatus")?.value || "";

        fetch(
            `/Nepal-Civic/fetch_issue.php?action=staff_list&mode=staff&page=${page}` +
            `&title=${encodeURIComponent(title)}` +
            `&department=${encodeURIComponent(department)}` +
            `&status=${encodeURIComponent(statusFilter)}`
        )
        .then(res => res.json())
        .then(res => {
            tbody.innerHTML = "";
            pagination.innerHTML = "";

            if (!res.data || res.data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" style="text-align:center;">
                            No issues found
                        </td>
                    </tr>`;
                return;
            }

            res.data.forEach(row => {
                tbody.innerHTML += `
                <tr class="staff-issue-row" data-id="${row.issue_id}">
                    <td>${row.sr}</td>
                    <td>${row.title}</td>
                    <td>${row.full_name}</td>
                    <td>${row.department_name || "-"}</td>
                    <td>
                        <span class="urgency-${row.urgency_level}">
                            ${row.urgency_level}
                        </span>
                    </td>
                    <td>${row.expected_resolution_date || "-"}</td>
                    <td>${row.date_reported}</td>
                    <td>
                        ${row.status === "assigned" ? `
                            <a href="resolve_issue.php?id=${row.issue_id}">
                                <button>Resolve</button>
                            </a>
                        ` : `<span class="status-resolved">Resolved</span>`}
                    </td>
                </tr>`;
            });

            renderPagination(res.totalPages, res.currentPage);
            bindStaffRowClicks();
        })
        .catch(err => console.error("Staff issue load failed:", err));
    }

    function renderPagination(total, current) {
        pagination.innerHTML = "";
        for (let i = 1; i <= total; i++) {
            const btn = document.createElement("button");
            btn.textContent = i;
            if (i === current) btn.classList.add("active");
            btn.onclick = () => loadStaffIssues(i);
            pagination.appendChild(btn);
        }
    }

    function bindStaffRowClicks() {
        document.querySelectorAll(".staff-issue-row").forEach(row => {
            row.onclick = e => {
                if (e.target.closest("button") || e.target.closest("a")) return;
                openStaffIssueModal(row.dataset.id);
            };
        });
    }

    function openStaffIssueModal(issueId) {
        fetch(`/Nepal-Civic/fetch_issue.php?action=detail&mode=staff&id=${issueId}`)
            .then(res => res.json())
            .then(d => {
                document.getElementById("m_title").textContent = d.title;
                document.getElementById("m_department").textContent = d.department_name;
                document.getElementById("m_status").textContent = d.status;
                document.getElementById("m_urgency").textContent = d.urgency_level || "-";
                document.getElementById("m_ward").textContent = "Ward " + d.ward_no;
                document.getElementById("m_reported").textContent = d.date_reported;
                document.getElementById("m_expected").textContent =
                    d.expected_resolution_date || "-";
                document.getElementById("m_description").textContent = d.description;

                const img = document.getElementById("m_image");
                if (d.photo_update) {
                    img.src = "uploads/issues/" + d.photo_update;
                    img.style.display = "block";
                } else {
                    img.style.display = "none";
                }

                document.getElementById("issueModal").style.display = "flex";
            });
    }

    document.querySelector(".modal-close")?.addEventListener("click", () => {
        document.getElementById("issueModal").style.display = "none";
    });

    window.addEventListener("click", e => {
        const modal = document.getElementById("issueModal");
        if (e.target === modal) modal.style.display = "none";
    });

    document.getElementById("staffSearchTitle")
        ?.addEventListener("keyup", () => loadStaffIssues(1));

    document.getElementById("staffFilterDepartment")
        ?.addEventListener("change", () => loadStaffIssues(1));

    document.getElementById("staffFilterStatus")
        ?.addEventListener("change", () => loadStaffIssues(1));

    loadStaffIssues();
});

