/* =========================
   HELPER FUNCTIONS
========================= */
function $(id) {
    return document.getElementById(id);
}

/* =========================
   FORM VALIDATION
========================= */
document.addEventListener("DOMContentLoaded", () => {

    /* LOGIN FORM */
    const loginForm = $("loginForm");
    if (loginForm) {
        loginForm.addEventListener("submit", function (e) {
            const email = $("email");
            const password = $("password");

            if (!email.value || !password.value) {
                alert("Please fill in all fields.");
                e.preventDefault();
            }
        });
    }

    /* REGISTER FORM */
    const registerForm = $("registerForm");
    if (registerForm) {
        registerForm.addEventListener("submit", function (e) {
            const email = $("email");
            const password = $("password");
            const ward = $("ward_id");

            if (!email.value || !password.value || !ward.value) {
                alert("All required fields must be filled.");
                e.preventDefault();
            }

            if (password.value.length < 6) {
                alert("Password must be at least 6 characters.");
                e.preventDefault();
            }
        });
    }

    /* REPORT ISSUE FORM */
    const issueForm = $("issueForm");
    if (issueForm) {
        issueForm.addEventListener("submit", function (e) {
            const title = $("title");
            const department = $("department_id");
            const description = $("description");

            if (!title.value || !department.value || !description.value) {
                alert("Please complete all issue details.");
                e.preventDefault();
            }
        });
    }
});

/* =========================
   SIDEBAR ACTIVE LINK (FIXED)
========================= */
document.addEventListener("DOMContentLoaded", () => {
    const links = document.querySelectorAll(".sidebar a");
    const params = new URLSearchParams(window.location.search);
    const currentPage = params.get("page") || "dashboard";

    links.forEach(link => {
        // FIX: Ignore the logout link specifically
        if (link.href.includes("logout.php")) return;

        const linkURL = new URL(link.href, window.location.origin);
        const linkPage = linkURL.searchParams.get("page") || "dashboard";

        if (linkPage === currentPage) {
            link.classList.add("active");
        }
    });
});

/* =========================
   SIMPLE HOVER EFFECTS
========================= */
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".issue-card").forEach(card => {
        card.addEventListener("mouseenter", () => {
            card.style.transform = "translateY(-3px)";
        });

        card.addEventListener("mouseleave", () => {
            card.style.transform = "translateY(0)";
        });
    });
});

/* =========================
   CHART FUNCTIONS (ADMIN) - FIXED
========================= */

/* BAR CHART (Department / Ward) */
function drawBarChart(canvasId, dataArr) {
    const canvas = document.getElementById(canvasId);
    // Check if dataArr is valid and has items
    if (!canvas || !dataArr || dataArr.length === 0) return;

    // Use the array directly
    const data = dataArr;

    const ctx = canvas.getContext("2d");
    // Ensure scaling is correct
    canvas.width = 380;
    canvas.height = Math.max(260, data.length * 60);


    // Calculate max value for dynamic height scaling
    const maxValue = Math.max(...data.map(d => d.value), 1);
    const barWidth = 28;
    const gap = 18;

    ctx.clearRect(0, 0, canvas.width, canvas.height);

    data.forEach((item, index) => {
        // Calculate dynamic height
        const barHeight = (item.value / maxValue) * 150;
        
        const x = 40 + index * (barWidth + gap);
        const y = canvas.height - barHeight - 35;

        // Draw Bar
        ctx.fillStyle = "#0b3c91";
        ctx.fillRect(x, y, barWidth, barHeight);

        // Draw Label and Value
        ctx.fillStyle = "#333";
        ctx.font = "11px Arial";
        
        // item.label comes directly from the PHP array now
        wrapText(ctx, item.label, x + barWidth / 2, canvas.height - 12, barWidth + 40, 12);
        ctx.fillText(item.value, x + 6, y - 6);
    });
}
// Helper function to wrap text within a given width
function wrapText(ctx, text, x, y, maxWidth, lineHeight) {
    const words = text.split(" ");
    let line = "";
    let lines = [];

    words.forEach(word => {
        const testLine = line + word + " ";
        if (ctx.measureText(testLine).width > maxWidth && line !== "") {
            lines.push(line);
            line = word + " ";
        } else {
            line = testLine;
        }
    });
    lines.push(line);

    lines.forEach((l, i) => {
        ctx.textAlign = "center";
        ctx.fillText(l, x, y + (i * lineHeight));
    });
}


/* PIE CHART (Status) */
function drawPieChart(canvasId, dataArr) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || !dataArr || dataArr.length === 0) return;

    // Use the array directly
    const data = dataArr;

    const ctx = canvas.getContext("2d");
    canvas.width = 220;
    canvas.height = 220;

    const total = data.reduce((sum, d) => sum + d.value, 0);
    let startAngle = 0;

    // Colors: Pending(orange), Assigned(blue), Resolved(green), Rejected(red)
    const colors = ["orange", "#0b3c91", "green", "red"];

    data.forEach((item, index) => {
        // Avoid dividing by zero
        if (total === 0) return;

        const sliceAngle = (item.value / total) * Math.PI * 2;

        ctx.beginPath();
        // Center (110,110) Radius (90)
        ctx.moveTo(110, 110);
        ctx.arc(110, 110, 90, startAngle, startAngle + sliceAngle);
        ctx.closePath();

        ctx.fillStyle = colors[index % colors.length];
        ctx.fill();

        startAngle += sliceAngle;
    });
}

/* =========================
   INIT CHARTS
========================= */
document.addEventListener("DOMContentLoaded", () => {
    // Check if the variables exist (they are defined in the PHP footer)
    if (typeof statusData !== "undefined") {
        drawPieChart("statusChart", statusData);
    }

    if (typeof deptData !== "undefined") {
        drawBarChart("deptChart", deptData);
    }

    if (typeof wardData !== "undefined") {
        drawBarChart("wardChart", wardData);
    }
});