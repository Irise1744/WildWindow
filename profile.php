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
                    <img src="<?php echo htmlspecialchars($photo['filename']); ?>" alt="User Photo" style="width: 200px; height: auto;">
                    <small>Uploaded on: <?php echo htmlspecialchars($photo['created_at']); ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No photos uploaded yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
