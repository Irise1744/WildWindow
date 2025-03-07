<?php
include 'php/config.php';
session_start();

// Get the observation ID from the URL
$observation_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch observation details
$query = "SELECT * FROM observations WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':id', $observation_id, PDO::PARAM_INT);
$stmt->execute();
$observation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$observation) {
    echo "Observation not found.";
    exit;
}

// Fetch comments for the observation
$query = "
    SELECT comments.comment, comments.created_at, users.username
    FROM comments
    JOIN users ON comments.user_id = users.id
    WHERE comments.observation_id = :observation_id
    ORDER BY comments.created_at DESC
";
$stmt = $conn->prepare($query);
$stmt->bindValue(':observation_id', $observation_id, PDO::PARAM_INT);
$stmt->execute();
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Observation Details</title>
</head>
<body>
    <div class="photo-gallery">
        <div class="photo-item">
            <div class="photo-header">
                <div class="user-avatar">
                    <i>🌿</i>
                </div>
                <div class="user-info">
                    <span class="username"><?php echo htmlspecialchars($observation['species']); ?></span>
                    <div class="timestamp"><?php echo date('F j, Y \a\t g:i a', strtotime($observation['created_at'])); ?></div>
                </div>
                <div class="post-options">...</div>
            </div>
            
            <div class="photo-content">
                <img src="uploads/<?php echo $observation['photo_path']; ?>" alt="Observation Photo">
            </div>
            
            <div class="photo-footer">
                <div class="engagement-info">
                    <div class="like-count">
                        <div class="like-icon"><i>👍</i></div>
                        <span>31</span>
                    </div>
                    <div class="comment-share-count">
                        <span><?php echo count($comments); ?> comments • 2 shares</span>
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
                    <strong>Species:</strong> <?php echo htmlspecialchars($observation['species']); ?><br>
                    <strong>Location:</strong> <?php echo htmlspecialchars($observation['location']); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Comment Form -->
    <h2>Leave a Comment</h2>
    <form action="post_comment.php" method="POST">
        <input type="hidden" name="observation_id" value="<?php echo $observation_id; ?>">
        <label for="comment">Comment:</label><br>
        <textarea name="comment" id="comment" rows="4" required></textarea><br>
        <button type="submit">Post Comment</button>
    </form>

    <!-- Comments Section -->
    <h2>Comments</h2>
    <div class="comments-section">
        <?php if (count($comments) > 0): ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <p><strong><?php echo htmlspecialchars($comment['username']); ?>:</strong></p>
                    <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                    <p><small><?php echo $comment['created_at']; ?></small></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No comments yet. Be the first to comment!</p>
        <?php endif; ?>
    </div>
</body>
</html>
