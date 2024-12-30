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
    <div class="search-results">
        <?php if (count($results) > 0) { ?>
            <?php foreach ($results as $result) { ?>
                <div class="observation">
                    <img src="<?php echo $result['photo_path']; ?>" alt="Observation Photo" style="max-width: 200px; height: auto;">
                    <p><strong>Species:</strong> <?php echo htmlspecialchars($result['species']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($result['location']); ?></p>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>No results found for "<?php echo $query; ?>"</p>
        <?php } ?>
    </div>
    
</body>
</html>
