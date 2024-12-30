<?php
// upload_observation.php
include 'php/config.php'; // Include your database connection file
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect data from the form
    $species = htmlspecialchars($_POST['species']);
    $location = htmlspecialchars($_POST['location']);
    $latitude = htmlspecialchars($_POST['latitude']);
    $longitude = htmlspecialchars($_POST['longitude']);
    $tags = htmlspecialchars($_POST['tags']); // New input field
    $user_id = $_SESSION['user_id'];

    // Handle photo upload
    $upload_dir = 'uploads/';
    $file_name = basename($_FILES['photo']['name']);
    $upload_file = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_file)) {
        // Save observation to the database
        $query = "INSERT INTO observations (user_id, photo_path, species, location, latitude, longitude) 
                  VALUES (:user_id, :photo_path, :species, :location, :latitude, :longitude)";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':photo_path', $upload_file, PDO::PARAM_STR);
        $stmt->bindValue(':species', $species, PDO::PARAM_STR);
        $stmt->bindValue(':location', $location, PDO::PARAM_STR);
        $stmt->bindValue(':latitude', $latitude, PDO::PARAM_STR);
        $stmt->bindValue(':longitude', $longitude, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $observation_id = $conn->lastInsertId(); // Get the observation ID

            // Handle tags
            if (!empty($tags)) {
                $tags_array = explode(',', $tags); // Split tags by commas
                foreach ($tags_array as $tag) {
                    $tag = trim($tag); // Remove extra spaces
                    $tag_query = "INSERT INTO tags (observation_id, tag) VALUES (:observation_id, :tag)";
                    $tag_stmt = $conn->prepare($tag_query);
                    $tag_stmt->bindValue(':observation_id', $observation_id, PDO::PARAM_INT);
                    $tag_stmt->bindValue(':tag', $tag, PDO::PARAM_STR);
                    $tag_stmt->execute();
                }
            }

            echo "Observation and tags uploaded successfully!";
        } else {
            echo "Failed to save observation.";
        }
    } else {
        echo "Failed to upload photo.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Observation</title>
</head>
<body>
    <h1>Upload Observation</h1>
    <form action="upload_observation.php" method="POST" enctype="multipart/form-data">
        <label for="photo">Upload Photo:</label>
        <input type="file" name="photo" id="photo" required><br><br>

        <label for="species">Species Name:</label>
        <input type="text" name="species" id="species" required><br><br>

        <label for="location">Location:</label>
        <input type="text" name="location" id="location"><br><br>

        <label for="latitude">Latitude:</label>
        <input type="text" name="latitude" id="latitude"><br><br>

        <label for="longitude">Longitude:</label>
        <input type="text" name="longitude" id="longitude"><br><br>

        <label for="tags">Tags (comma-separated):</label>
        <input type="text" name="tags" id="tags" placeholder="e.g., wildlife, nature">
        <br><br>

        <button type="submit">Upload Observation</button>
    </form>
</body>
</html>
