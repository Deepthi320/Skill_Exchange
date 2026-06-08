<?php 
include 'config/db.php'; 

// Handle Skill Request Submission
$message = "";
if (isset($_POST['request_skill'])) {
    if (!isset($_SESSION['user_id'])) {
        $message = "<div class='alert alert-danger'>Please log in to request a skill exchange!</div>";
    } else {
        $learner_id = $_SESSION['user_id'];
        $skill_id = mysqli_real_escape_string($conn, $_POST['skill_id']);
        
        // Prevent requesting your own skill
        $check_own = mysqli_query($conn, "SELECT user_id FROM skills WHERE skill_id = '$skill_id'");
        $skill_owner = mysqli_fetch_assoc($check_own)['user_id'];
        
        if ($learner_id == $skill_owner) {
            $message = "<div class='alert alert-danger'>You cannot request your own skill!</div>";
        } else {
            // Check if already requested
            $check_dup = mysqli_query($conn, "SELECT * FROM requests WHERE learner_id = '$learner_id' AND skill_id = '$skill_id'");
            if (mysqli_num_rows($check_dup) > 0) {
                $message = "<div class='alert alert-warning'>You have already submitted a request for this skill.</div>";
            } else {
                $insert = mysqli_query($conn, "INSERT INTO requests (learner_id, skill_id, status) VALUES ('$learner_id', '$skill_id', 'Pending')");
                if ($insert) {
                    $message = "<div class='alert alert-success'>Exchange request submitted successfully! Tracking via dashboard.</div>";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Skills - SkillSwap</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="logo">SkillSwap</a>
        <div class="menu-toggle" id="mobile-menu"><span></span><span></span><span></span></div>
        <ul class="nav-links" id="nav-list">
            <li><a href="index.php">Home</a></li>
            <li><a href="browse_skills.php" class="active">Browse Skills</a></li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="logout.php" class="nav-btn">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php" class="nav-btn">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="container">
        <h2 style="margin-bottom: 1rem;">Available Skills for Exchange</h2>
        
        <?php echo $message; ?>

        <div class="grid-3">
            <?php
            // Fetch all skills along with user names
            $query = "SELECT skills.*, users.name FROM skills JOIN users ON skills.user_id = users.user_id ORDER BY skills.created_at DESC";
            $result = mysqli_query($conn, $query);

            if(mysqli_num_rows($result) == 0) {
                echo "<p>No skills posted yet.</p>";
            }

            while($row = mysqli_fetch_assoc($result)) {
                $levelClass = strtolower($row['skill_level']);
                echo "
                <div class='card'>
                    <div>
                        <span class='badge badge-{$levelClass}'>{$row['skill_level']}</span>
                        <h3>{$row['skill_name']}</h3>
                        <p style='color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1rem;'>Instructor: {$row['name']}</p>
                        <p>{$row['description']}</p>
                    </div>
                    <form method='POST' style='margin-top: 1.5rem;'>
                        <input type='hidden' name='skill_id' value='{$row['skill_id']}'>
                        <button type='submit' name='request_skill' class='btn' style='width: 100%; text-align: center;'>Request Exchange</button>
                    </form>
                </div>";
            }
            ?>
        </div>
    </div>

    <script>
        document.getElementById('mobile-menu').addEventListener('click', function() {
            document.getElementById('nav-list').classList.toggle('active');
        });
    </script>
</body>
</html>