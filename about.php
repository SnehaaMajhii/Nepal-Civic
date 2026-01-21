<?php
// about.php
include 'includes/db.php';
$page_title = "About Us - Nepal Civic";
include 'includes/header.php';
?>

<div class="container">
    <div class="form-card" style="border-top: 5px solid var(--nepal-red);">
        <h1 style="color: var(--nepal-red); text-align: center;">About Nepal Civic</h1>
        <p style="font-size: 1.1rem; line-height: 1.6; color: #444; text-align: center; max-width: 800px; margin: 0 auto 30px;">
            Nepal Civic is a digital bridge between citizens and their local municipal government. 
            We empower residents to report infrastructure issues directly to their Ward Officers 
            for a cleaner, safer, and better-managed community.
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 40px;">
            <div style="padding: 20px; border: 1px solid #eee; border-radius: 8px; text-align: center;">
                <h3 style="color: var(--nepal-blue);">The Citizen</h3>
                <p>Identifies local issues, uploads photo evidence, and tracks the status of their reports in real-time.</p>
            </div>

            <div style="padding: 20px; border: 1px solid #eee; border-radius: 8px; text-align: center;">
                <h3 style="color: var(--nepal-blue);">The Admin</h3>
                <p>Acts as the gatekeeper by moderating reports to prevent spam and assigning qualified Ward Officers to specific wards.</p>
            </div>

            <div style="padding: 20px; border: 1px solid #eee; border-radius: 8px; text-align: center;">
                <h3 style="color: var(--nepal-blue);">The Ward Officer</h3>
                <p>Oversees all approved issues within their assigned ward, manages the resolution process, and updates the public.</p>
            </div>
        </div>

        <div style="margin-top: 50px; background: var(--gray-bg); padding: 30px; border-radius: 8px; text-align: center;">
            <h2 style="color: var(--nepal-blue);">Our Vision</h2>
            <p style="font-style: italic; color: #666;">
                "To foster accountability and transparency in local governance through technology."
            </p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>