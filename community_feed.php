<?php
include "includes/db.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fetch all issues (public)
$issues = mysqli_query($conn, "
    SELECT issue.*, department.department_name, ward.ward_no,
    (SELECT COUNT(*) FROM urgency_vote WHERE urgency_vote.issue_id = issue.issue_id) AS votes
    FROM issue
    JOIN department ON issue.department_id = department.department_id
    JOIN ward ON issue.ward_id = ward.ward_id
    ORDER BY issue.date_reported DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Community Feed | Nepal Civic</title>
    <?php
        $wardStats = mysqli_query($conn, "
        SELECT ward_id, COUNT(*) AS total 
        FROM issue GROUP BY ward_id
        ");
    ?>

    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="feed-container">
    
    <h2 style="text-align:center;">Community Issues</h2>
    <p style="text-align:center;">Publicly reported issues for transparency and accountability</p>
    <p><a href="index.php">Back to Home</a></p>
    <br>

    <div class="feed-grid">

        <?php while ($row = mysqli_fetch_assoc($issues)) { ?>

            <div class="feed-card">

                <?php if (!empty($row['photo_update'])) { ?>
                    <img src="uploads/issues/<?php echo htmlspecialchars($row['photo_update']); ?>" alt="Issue Image">
                <?php } else { ?>
                    <img src="assets/no-image.png" alt="No Image">
                <?php } ?>


                <div class="feed-card-body">
                    <h3><?php echo $row['title']; ?></h3>

                    <span class="status-badge">
                        <?php echo ucfirst($row['status']); ?>
                    </span>

                    <p><b>Ward:</b> <?php echo $row['ward_no']; ?></p>
                    <p><b>Department:</b> <?php echo $row['department_name']; ?></p>
                    <p><b>Votes:</b> <?php echo $row['votes']; ?></p>

                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'citizen') { ?>
                        <a href="vote_issue.php?id=<?php echo $row['issue_id']; ?>">
                            <button>Vote Issue</button>
                        </a>
                    <?php } ?>

                </div>
            </div>

        <?php } ?>

    </div>
</div>

</body>
</html>
