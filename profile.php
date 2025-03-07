<?php
session_start();
include 'php/config.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user photos
$query = "SELECT * FROM photos WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <h1>Welcome to Your Profile</h1>
    <h2>Your Photos</h2>
    <div class="photo-gallery">
        <?php if (!empty($photos)): ?>
            <?php foreach ($photos as $photo): ?>
                <div class="photo-item">
                    <div class="photo-header">
                        <div class="user-avatar">
                            <?php echo substr($_SESSION['username'], 0, 1); ?>
                        </div>
                        <div class="user-info">
                            <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
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
                                <span>38</span>
                            </div>
                            <div class="comment-share-count">
                                <span>3 comments • 1 share</span>
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
                            <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            shared a beautiful plant photo
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No photos uploaded yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
