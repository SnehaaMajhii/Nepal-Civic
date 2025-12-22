<?php 
include 'includes/db.php'; 
if(!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
$uid = $_SESSION['user_id'];

if(isset($_POST['submit_issue'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $cat = $_POST['category'];
    $loc = mysqli_real_escape_string($conn, $_POST['location']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $tid = "NC-" . rand(1000, 9999);
    
    mysqli_query($conn, "INSERT INTO issues (tracking_id, title, category, location, description, citizen_id) 
                         VALUES ('$tid', '$title', '$cat', '$loc', '$desc', '$uid')");
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h2>Nepal Civic Portal</h2>
        <a href="logout.php"><button class="danger">Logout</button></a>
    </header>
    <div class="container">
        <div class="grid">
            <div class="card">
                <h3>Report New Issue</h3>
                <form method="POST">
                    <input type="text" name="title" placeholder="What is the issue?" required>
                    <select name="category" required>
                        <option value="Sanitation">Sanitation</option>
                        <option value="Road">Road</option>
                        <option value="Water">Water</option>
                        <option value="Electricity">Electricity</option>
                    </select>
                    <div style="display:flex; gap:5px;">
                        <input type="text" id="locInp" name="location" placeholder="Location/Ward" required>
                        <button type="button" id="getLoc" style="background:#1e3a8a">GPS</button>
                    </div>
                    <textarea name="description" placeholder="Provide details..."></textarea>
                    <button type="submit" name="submit_issue">Submit Complaint</button>
                </form>
            </div>
            <div class="card" style="flex: 2;">
                <h3>Your Tracking History</h3>
                <table>
                    <tr><th>ID</th><th>Issue</th><th>Status</th></tr>
                    <?php 
                    $res = mysqli_query($conn, "SELECT * FROM issues WHERE citizen_id=$uid");
                    while($r = mysqli_fetch_assoc($res)) {
                        echo "<tr><td>{$r['tracking_id']}</td><td>{$r['title']}</td>
                              <td><span class='status {$r['status']}'>{$r['status']}</span></td></tr>";
                    } ?>
                </table>
            </div>
        </div>
    </div>
    <script src="assets/js/main.js"></script>
</body>
</html>