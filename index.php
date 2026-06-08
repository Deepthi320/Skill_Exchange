<?php include 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skill Exchange Platform</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="logo">SkillSwap</a>
        <div class="menu-toggle" id="mobile-menu">
            <span></span><span></span><span></span>
        </div>
        <ul class="nav-links" id="nav-list">
            <li><a href="index.php" class="active">Home</a></li>
            <li><a href="browse_skills.php">Browse Skills</a></li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="logout.php" class="nav-btn">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php" class="nav-btn">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <section class="hero">
        <h1>Share Your Skills, Learn for Free</h1>
        <p>Join our campus peer-to-peer network. Teach what you love, learn what you need.</p>
        <?php if(!isset($_SESSION['user_id'])): ?>
            <a href="register.php" class="btn">Get Started Now</a>
        <?php else: ?>
            <a href="dashboard.php" class="btn">Go to Dashboard</a>
        <?php endif; ?>
    </section>

    <div class="container">
        <h2 style="text-align: center; margin-top: 2rem;">Explore What You Can Learn</h2>
        <div class="grid-3">
            <?php
            $query = "SELECT skills.*, users.name FROM skills JOIN users ON skills.user_id = users.user_id LIMIT 3";
            $result = mysqli_query($conn, $query);
            while($row = mysqli_fetch_assoc($result)) {
                $levelClass = strtolower($row['skill_level']);
                echo "
                <div class='card'>
                    <div>
                        <span class='badge badge-{$levelClass}'>{$row['skill_level']}</span>
                        <h3>{$row['skill_name']}</h3>
                        <p style='color: var(--text-muted); font-size: 0.9rem; margin-bottom:1rem;'>Instructor: {$row['name']}</p>
                        <p>".substr($row['description'], 0, 100)."...</p>
                    </div>
                    <a href='browse_skills.php' class='btn' style='margin-top:1.5rem; text-align:center;'>Learn More</a>
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