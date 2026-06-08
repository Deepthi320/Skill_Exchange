<?php 
include 'config/db.php'; 

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "All fields are required!";
    } else {
        $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Verify password hash
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['name'];
                
                // Send to user dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Incorrect password!";
            }
        } else {
            $error = "No account found with that email!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SkillSwap</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="logo">SkillSwap</a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="browse_skills.php">Browse Skills</a></li>
            <li><a href="login.php" class="active">Login</a></li>
            <li><a href="register.php" class="nav-btn">Register</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="form-card">
            <h2>Welcome Back</h2>
            
            <?php if(!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="enter your registered email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="enter your password" required>
                </div>
                <button type="submit" name="login" class="btn" style="width: 100%;">Login</button>
            </form>
            <p style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem;">
                Don't have an account? <a href="register.php" style="color: var(--primary-color);">Register here</a>
            </p>
        </div>
    </div>

</body>
</html>