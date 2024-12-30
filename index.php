<?php
session_start();
include 'php/config.php';
if (isset($_SESSION['user_id'])) {
    header('Location: view_user.php?id=' . $_SESSION['user_id']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        try {
            // Prepare the SQL query using PDO
            $query = $conn->prepare("SELECT * FROM users WHERE username = :username");
            $query->bindValue(':username', $username, PDO::PARAM_STR);
            $query->execute();

            $user = $query->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['Password'])) {
                // Store the username in the session
                $_SESSION['username'] = $user['Username'];
                header('Location: home.php'); // Redirect to Home after login
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . htmlspecialchars($e->getMessage());
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <nav class="navbar">
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="activity.php">Activity</a></li>
            <li><a href="addNew.php">Add New</a></li>
            <li><a href="profile.php">
                <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Profile'; ?>
            </a></li>
        </ul>
    </nav>
<div class="container">
    <div class="box form-box">
        <header>Login</header>
        <?php if (isset($error)): ?>
            <div class="message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form action="index.php" method="POST">
            <div class="field input">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="Enter your username" required>
            </div>
            <div class="field input">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
            </div>
            <div class="field">
                <button type="submit" class="btn submit">Login</button>
            </div>
        </form>
        <div class="links">
            <p>Don't have an account? <a href="register.php">Register</a></p>
        </div>
    </div>
</div>
</body>
</html>
