<?php
session_start();
include 'php/config.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: index.php'); // Redirect to login page if not logged in
    exit();
}

// Retrieve the username from the session
$username = $_SESSION['username'];

// Process file upload
if (isset($_POST['upload'])) {
    if (
        !isset($_FILES['userImage']) 
        || $_FILES['userImage']['error'] !== UPLOAD_ERR_OK 
        || empty($_FILES['userImage']['name'])
    ) {
        echo "<p style='color:red;'>No file selected or file upload error!</p>";
    } else {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
 
        // Generate unique filename to prevent overwrites
        $fileName = date('Y_m_d_H_i_') . basename($_FILES['userImage']['name']);
        $targetFile = $uploadDir . $fileName;
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['userImage']['type'];

        if (!in_array($fileType, $allowedTypes)) {
            echo "<p style='color:red;'>Only JPG, PNG, or GIF files are allowed.</p>";
        } else {
            if (move_uploaded_file($_FILES['userImage']['tmp_name'], $targetFile)) {
                try {
                    $stmt = $conn->prepare("INSERT INTO photos (user_id, filename) VALUES (:user_id, :filename)");
                    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                    $stmt->bindParam(':filename', $fileName, PDO::PARAM_STR);
                    $stmt->execute();
                    echo "<p style='color:green;'>File uploaded successfully!</p>";
                } catch (PDOException $e) {
                    echo "<p style='color:red;'>Database Error: " . $e->getMessage() . "</p>";
                }
            } else {
                echo "<p style='color:red;'>Error moving the uploaded file.</p>";
            }
        }
    }
}

// Fetch all photos
$query = "
    SELECT photos.*, users.username 
    FROM photos 
    JOIN users ON photos.user_id = users.id 
    ORDER BY photos.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload a Photo & Latest Photos</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
<nav class="navbar">
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="activity.php">Activity</a></li>
            <li><a href="addNew.php">Add New</a></li>
            <li><a href="profile.php"> <?php echo htmlspecialchars($username); ?></a></li>
        </ul>
    </nav>
<div class="upload-container">
    <h1>Upload a Photo</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="userImage" accept="image/*">
        <br><br>
        <button type="submit" name="upload">Upload</button>
    </form>
    <!-- <p><a href="profile.php">Go to Profile</a></p> -->
</div>

<h2>Latest Photos</h2>
<div class="photo-gallery">
    <?php if (!empty($photos)): ?>
        <?php foreach ($photos as $photo): ?>
            <div class="photo-item">
                <div class="photo-header">
                    <div class="user-avatar">
                        <?php echo substr(htmlspecialchars($photo['username']), 0, 1); ?>
                    </div>
                    <div class="user-info">
                        <a href="view_user.php?id=<?php echo htmlspecialchars($photo['user_id']); ?>" class="username">
                            <?php echo htmlspecialchars($photo['username']); ?>
                        </a>
                        <div class="timestamp"><?php echo date('F j, Y \a\t g:i a', strtotime($photo['created_at'])); ?></div>
                    </div>
                    <div class="post-options">...</div>
                </div>
                
                <div class="photo-content">
                    <img src="uploads/<?php echo htmlspecialchars($photo['filename']); ?>" alt="User Photo">
                </div>
                
                <div class="photo-footer">
                    <div class="engagement-info">
                        <div class="like-count">
                            <div class="like-icon"><i>👍</i></div>
                            <span>42</span>
                        </div>
                        <div class="comment-share-count">
                            <span>5 comments • 2 shares</span>
                        </div>
                    </div>
                    
                    <div class="action-bar">
                        <div class="action-button">
                            <i>👍</i> Like
                        </div>
                        <div class="action-button">
                            <i>💬</i> Comment
                        </div>
                        <div class="action-button">
                            <i>↗️</i> Share
                        </div>
                    </div>
                    
                    <div class="photo-caption">
                        <a href="view_user.php?id=<?php echo htmlspecialchars($photo['user_id']); ?>" class="username">
                            <?php echo htmlspecialchars($photo['username']); ?>
                        </a>
                        shared a beautiful plant photo
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No photos available.</p>
    <?php endif; ?>
</div>
</body>
</html>
