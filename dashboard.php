<?php
include 'config/db.php';

// Redirect to login if user isn't authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$current_user = $_SESSION['user_id'];
$message = "";

// 1. Handle New Skill Posting Form Submission
if (isset($_POST['add_skill'])) {
    $s_name = mysqli_real_escape_string($conn, $_POST['skill_name']);
    $s_desc = mysqli_real_escape_string($conn, $_POST['description']);
    $s_level = mysqli_real_escape_string($conn, $_POST['skill_level']);
    
    if(!empty($s_name) && !empty($s_desc)) {
        $insert = mysqli_query($conn, "INSERT INTO skills (user_id, skill_name, description, skill_level) VALUES ('$current_user', '$s_name', '$s_desc', '$s_level')");
        if ($insert) {
            $message = "<div class='alert alert-success'>Your new skill has been published successfully!</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Please fill out all fields to add a skill.</div>";
    }
}

// 2. Handle Accepting or Rejecting Requests from other users
if (isset($_POST['update_status'])) {
    $req_id = mysqli_real_escape_string($conn, $_POST['request_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $update = mysqli_query($conn, "UPDATE requests SET status = '$new_status' WHERE request_id = '$req_id'");
    if($update) {
        $message = "<div class='alert alert-success'>Request updated to '{$new_status}'!</div>";
    }
}

// 3. Fetch Statistics Metrics
$skills_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM skills WHERE user_id = '$current_user'"))['total'];
$incoming_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM requests JOIN skills ON requests.skill_id = skills.skill_id WHERE skills.user_id = '$current_user'"))['total'];
$outgoing_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM requests WHERE learner_id = '$current_user'"))['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - SkillSwap</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="logo">SkillSwap</a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="browse_skills.php">Browse Skills</a></li>
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <li><a href="logout.php" class="nav-btn">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['nav_name'] ?? $_SESSION['user_name']); ?>!</h2>
        <p style="color: var(--text-muted); margin-bottom: 2rem;">Manage your skills and active match updates here.</p>

        <?php echo $message; ?>

        <div class="dash-stats">
            <div class="stat-card">
                <h4>My Listed Skills</h4>
                <p><?php echo $skills_count; ?></p>
            </div>
            <div class="stat-card" style="border-left-color: var(--warning);">
                <h4>Incoming Proposals</h4>
                <p><?php echo $incoming_count; ?></p>
            </div>
            <div class="stat-card" style="border-left-color: var(--success);">
                <h4>Sent Requests</h4>
                <p><?php echo $outgoing_count; ?></p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-top: 2rem;">
            
            <div>
                <h3>Incoming Exchange Requests (People wanting to learn from you)</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Learner Name</th>
                                <th>Requested Skill</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $inc_query = "SELECT requests.*, users.name as learner, skills.skill_name FROM requests 
                                          JOIN skills ON requests.skill_id = skills.skill_id 
                                          JOIN users ON requests.learner_id = users.user_id 
                                          WHERE skills.user_id = '$current_user'";
                            $inc_result = mysqli_query($conn, $inc_query);
                            if(mysqli_num_rows($inc_result) == 0) echo "<tr><td colspan='4'>No incoming requests yet.</td></tr>";
                            while($row = mysqli_fetch_assoc($inc_result)) {
                                $statusClass = strtolower($row['status']);
                                echo "<tr>
                                    <td>{$row['learner']}</td>
                                    <td>{$row['skill_name']}</td>
                                    <td><span class='badge badge-{$statusClass}'>{$row['status']}</span></td>
                                    <td>";
                                if($row['status'] == 'Pending') {
                                    echo "
                                    <form method='POST' style='display:inline;'>
                                        <input type='hidden' name='request_id' value='{$row['request_id']}'>
                                        <button type='submit' name='update_status' value='1' class='btn-sm btn-success' style='border:none; cursor:pointer;'>
                                            <input type='hidden' name='status' value='Accepted'>Accept
                                        </button>
                                        <button type='submit' name='update_status' value='1' class='btn-sm btn-danger' style='border:none; cursor:pointer;'>
                                            <input type='hidden' name='status' value='Rejected'>Reject
                                        </button>
                                    </form>";
                                } else {
                                    echo "<span style='color:var(--text-muted); font-size:0.85rem;'>Decision Complete</span>";
                                }
                                echo "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <h3 style="margin-top: 3rem;">Your Sent Requests (Skills you want to acquire)</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Skill Requested</th>
                                <th>Teacher Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $out_query = "SELECT requests.*, skills.skill_name, users.name as teacher FROM requests 
                                          JOIN skills ON requests.skill_id = skills.skill_id 
                                          JOIN users ON skills.user_id = users.user_id 
                                          WHERE requests.learner_id = '$current_user'";
                            $out_result = mysqli_query($conn, $out_query);
                            if(mysqli_num_rows($out_result) == 0) echo "<tr><td colspan='3'>You haven't requested any exchanges yet.</td></tr>";
                            while($row = mysqli_fetch_assoc($out_result)) {
                                $statusClass = strtolower($row['status']);
                                echo "<tr>
                                    <td>{$row['skill_name']}</td>
                                    <td>{$row['teacher']}</td>
                                    <td><span class='badge badge-{$statusClass}'>{$row['status']}</span></td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <div style="background: white; padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border);">
                    <h3>Offer a New Skill</h3>
                    <form method="POST" action="dashboard.php" style="margin-top:1rem;">
                        <div class="form-group">
                            <label style="font-size:0.9rem;">Skill Name</label>
                            <input type="text" name="skill_name" placeholder="e.g. Node.js, Photography" required>
                        </div>
                        <div class="form-group">
                            <label style="font-size:0.9rem;">Skill Level</label>
                            <select name="skill_level">
                                <option value="Beginner">Beginner</option>
                                <option value="Intermediate">Intermediate</option>
                                <option value="Expert">Expert</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label style="font-size:0.9rem;">Brief Description</label>
                            <textarea name="description" rows="4" placeholder="What can you teach your peer?" required></textarea>
                        </div>
                        <button type="submit" name="add_skill" class="btn" style="width: 100%;">Publish Skill</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</body>
</html>