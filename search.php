<?php
include 'php/config.php';
session_start();

// Fetch the search query from the GET request
$query = isset($_GET['query']) ? htmlspecialchars($_GET['query']) : '';

$sql = "
    SELECT DISTINCT observations.id, observations.photo_path, observations.species, observations.location
    FROM observations
    LEFT JOIN tags ON observations.id = tags.observation_id
    WHERE observations.species LIKE :query 
       OR observations.location LIKE :query 
       OR tags.tag LIKE :query
";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Observations</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

</head>
<body>
    <h1>Search Observations</h1>

    <!-- Search Form -->
    <form method="GET" action="search.php">
        <input type="text" name="query" placeholder="Search by tags, species, or location" value="<?php echo $query; ?>" required>
        <button type="submit">Search</button>
    </form>

    <!-- Search Results -->
    <div class="photo-gallery">
        <?php if (count($results) > 0) { ?>
            <?php foreach ($results as $result) { ?>
                <div class="photo-item">
                    <div class="photo-header">
                        <div class="user-avatar">
                            <i>🔍</i>
                        </div>
                        <div class="user-info">
                            <span class="username">Search Result</span>
                            <div class="timestamp">Found for: "<?php echo htmlspecialchars($query); ?>"</div>
                        </div>
                        <div class="post-options">...</div>
                    </div>
                    
                    <div class="photo-content">
                        <img src="uploads/<?php echo $result['photo_path']; ?>" alt="Observation Photo">
                    </div>
                    
                    <div class="photo-footer">
                        <div class="engagement-info">
                            <div class="like-count">
                                <div class="like-icon"><i>👍</i></div>
                                <span>15</span>
                            </div>
                            <div class="comment-share-count">
                                <span>2 comments • 1 share</span>
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
                            <strong>Species:</strong> <?php echo htmlspecialchars($result['species']); ?><br>
                            <strong>Location:</strong> <?php echo htmlspecialchars($result['location']); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>No results found for "<?php echo $query; ?>"</p>
        <?php } ?>
    </div>
    
</body>
</html>
