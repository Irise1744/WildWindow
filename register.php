<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <nav class="navbar">
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="activity.html">Activity</a></li>
            <li><a href="addNew.html">Add New</a></li>
            <li><a href="profile.html">Profile</a></li>
        </ul>
    </nav> 
    
    <div class="container">
        <div class="box form-box">
        
        <?php
        include("php/config.php");

        if (isset($_POST['submit'])) {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $age = (int)$_POST['age'];
            $password = trim($_POST['password']);

            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            try {
                // Verify the unique email
                $verify_query = $conn->prepare("SELECT Email FROM users WHERE Email = :email");
                $verify_query->bindValue(':email', $email, PDO::PARAM_STR);
                $verify_query->execute();

                if ($verify_query->rowCount() > 0) {
                    echo "<div class='message'>
                    <p>This email is already in use. Try another one!</p>
                  </div><br>";
            
                    echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
                } else {
                    // Insert new user
                    $insert_query = $conn->prepare("INSERT INTO users (Username, Email, Age, Password) VALUES (:username, :email, :age, :password)");
                    $insert_query->bindValue(':username', $username, PDO::PARAM_STR);
                    $insert_query->bindValue(':email', $email, PDO::PARAM_STR);
                    $insert_query->bindValue(':age', $age, PDO::PARAM_INT);
                    $insert_query->bindValue(':password', $hashed_password, PDO::PARAM_STR);

                    if ($insert_query->execute()) {
                        echo '<div class="message2">
                            <p>Registration Successful!</p>
                          </div><br>';
                        echo "<a href='index.php'><button class='btn'>Login Now</button>";
                    } else {
                        echo "<div class='message'>
                            <p>Error occurred while registering. Please try again.</p>
                          </div><br>";
                    }
                }
            } catch (PDOException $e) {
                echo "<div class='message'>
                    <p>Error: " . htmlspecialchars($e->getMessage()) . "</p>
                  </div><br>";
            }
        } else {
        ?>

            <header>Sign Up</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>

                <div class="field input">
                    <label for="age">Age</label>
                    <input type="number" name="age" id="age" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Sign Up" required>
                </div>

                <div class="links">
                    Already a member? <a href="index.php">Sign In</a>
                </div>
            </form>
        </div>
        <?php } ?>
    </div>
</body>
</html>
