<?php
include 'php/config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Get the profile user ID from the URL
$followee_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch user details for the profile user
$query = "SELECT username, email FROM users WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':id', $followee_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit;
}

// Logged-in user's ID
$follower_id = $_SESSION['user_id'];

// Check if already following
$query = "SELECT * FROM followers WHERE follower_id = :follower_id AND followee_id = :followee_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':follower_id', $follower_id, PDO::PARAM_INT);
$stmt->bindValue(':followee_id', $followee_id, PDO::PARAM_INT);
$stmt->execute();
$is_following = $stmt->fetch(PDO::FETCH_ASSOC);

// Initialize filter variables
$species = isset($_GET['species']) ? trim($_GET['species']) : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$date = isset($_GET['date']) ? trim($_GET['date']) : '';

// Base query for observations
$query = "SELECT * FROM observations WHERE user_id = :user_id";

// Add filters if provided
if (!empty($species)) {
    $query .= " AND species LIKE :species";
}
if (!empty($location)) {
    $query .= " AND location LIKE :location";
}
if (!empty($date)) {
    $query .= " AND DATE(created_at) = :date";
}

$query .= " ORDER BY created_at DESC";
$stmt = $conn->prepare($query);

// Bind parameters
$stmt->bindValue(':user_id', $followee_id, PDO::PARAM_INT);
if (!empty($species)) {
    $stmt->bindValue(':species', "%$species%", PDO::PARAM_STR);
}
if (!empty($location)) {
    $stmt->bindValue(':location', "%$location%", PDO::PARAM_STR);
}
if (!empty($date)) {
    $stmt->bindValue(':date', $date, PDO::PARAM_STR);
}

$stmt->execute();
$observations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['username']); ?>'s Profile</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body>
    <h1><?php echo htmlspecialchars($user['username']); ?>'s Profile</h1>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>

    <!-- Follow/Unfollow Button -->
    <form action="toggle_follow.php" method="POST">
        <input type="hidden" name="followee_id" value="<?php echo $followee_id; ?>">
        <?php if ($is_following): ?>
            <button type="submit" name="action" value="unfollow">Unfollow</button>
        <?php else: ?>
            <button type="submit" name="action" value="follow">Follow</button>
        <?php endif; ?>
    </form>

    <!-- Filter Observations -->
    <h3>Filter Observations</h3>
    <form method="GET" action="view_user.php">
        <input type="hidden" name="id" value="<?php echo $followee_id; ?>">
        <label for="species">Species:</label>
        <input type="text" name="species" id="species" placeholder="Enter species name" value="<?php echo htmlspecialchars($species); ?>">
        <br><br>
        <label for="location">Location:</label>
        <input type="text" name="location" id="location" placeholder="Enter location" value="<?php echo htmlspecialchars($location); ?>">
        <br><br>
        <label for="date">Date:</label>
        <input type="date" name="date" id="date" value="<?php echo htmlspecialchars($date); ?>">
        <br><br>
        <button type="submit">Filter</button>
    </form>

    <!-- Observations Section -->
    <h3>Observations</h3>
    <div class="photo-gallery">
        <?php if (!empty($observations)): ?>
            <?php foreach ($observations as $observation): ?>
                <div class="photo-item">
                    <div class="photo-header">
                        <div class="user-avatar">
                            <?php echo substr(htmlspecialchars($user['username']), 0, 1); ?>
                        </div>
                        <div class="user-info">
                            <span class="username"><?php echo htmlspecialchars($user['username']); ?></span>
                            <div class="timestamp"><?php echo date('F j, Y \a\t g:i a', strtotime($observation['created_at'])); ?></div>
                        </div>
                        <div class="post-options">...</div>
                    </div>
                    
                    <div class="photo-content">
                        <img src="uploads/<?php echo htmlspecialchars($observation['photo_path']); ?>" alt="Observation Photo">
                    </div>
                    
                    <div class="photo-footer">
                        <div class="engagement-info">
                            <div class="like-count">
                                <div class="like-icon"><i>👍</i></div>
                                <span>27</span>
                            </div>
                            <div class="comment-share-count">
                                <span>4 comments • 3 shares</span>
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
                            <span class="username"><?php echo htmlspecialchars($user['username']); ?></span>
                            observed <strong><?php echo htmlspecialchars($observation['species']); ?></strong> 
                            at <?php echo htmlspecialchars($observation['location']); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No observations to display.</p>
        <?php endif; ?>
    </div>
</body>
</html>
