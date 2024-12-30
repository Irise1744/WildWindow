<?php
// Fetch observations with geolocation
include 'php/config.php';
session_start();

$sql = "
    SELECT id, species, location, latitude, longitude, photo_path
    FROM observations
    WHERE latitude IS NOT NULL AND longitude IS NOT NULL
";
$stmt = $conn->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Observation Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body>
    <h1>Explore Observations on the Map</h1>

    <!-- Map Container -->
    <div id="map" style="height: 500px; width: 100%;"></div>

    <script>
        // Initialize the map
        var map = L.map('map').setView([0, 0], 2);

        // Add the OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Add markers for observations
        <?php foreach ($results as $observation) { ?>
            L.marker([<?php echo $observation['latitude']; ?>, <?php echo $observation['longitude']; ?>])
                .addTo(map)
                .bindPopup(
                    `<strong><?php echo htmlspecialchars($observation['species']); ?></strong><br>
                     <?php echo htmlspecialchars($observation['location']); ?><br>
                     <img src="<?php echo $observation['photo_path']; ?>" alt="Observation Photo" style="width: 100px; height: auto;">`
                );
        <?php } ?>
    </script>
</body>
</html>

