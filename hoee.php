<?php
// home.php

require_once 'C:\xampp\htdocs\Wildwindow\php\config.php';  // Include the database connection

// Example user ID (if you have a login system, fetch this dynamically)
$userId = 1;

// Handle file upload
if (isset($_POST['upload'])) {
    // Folder where images get stored
    $uploadDir = 'uploads/';

    // Create the folder if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Get the uploaded file info
    $fileName = basename($_FILES['userImage']['name']);
    $targetFile = $uploadDir . $fileName;
    $fileType = $_FILES['userImage']['type'];

    // Allowed mime types
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

    if (!in_array($fileType, $allowedTypes)) {
        echo "<p style='color:red;'>Only JPG, PNG, or GIF files are allowed.</p>";
    } else {
        // Move the file from temp dir to uploads folder
        if (move_uploaded_file($_FILES['userImage']['tmp_name'], $targetFile)) {
            echo "<p style='color:green;'>File uploaded successfully!</p>";

            // OPTIONAL: Resize large images on server side
            list($width, $height) = getimagesize($targetFile);
            $maxDim = 800;
            if ($width > $maxDim || $height > $maxDim) {
                $ratio = $width / $height;
                if ($ratio > 1) {
                    $newWidth  = $maxDim;
                    $newHeight = $maxDim / $ratio;
                } else {
                    $newWidth  = $maxDim * $ratio;
                    $newHeight = $maxDim;
                }
                $src = imagecreatefromstring(file_get_contents($targetFile));
                $dst = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagejpeg($dst, $targetFile, 80);
                imagedestroy($src);
                imagedestroy($dst);
            }

            // Insert file info into the database
            $stmt = $conn->prepare("INSERT INTO photos (user_id, filename) VALUES (:user_id, :filename)");
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':filename', $fileName);
            $stmt->execute();
        } else {
            echo "<p style='color:red;'>Error uploading your file.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Upload a Photo</title>
    <link rel="stylesheet" href="style\style.css">
</head>
<body>
<nav class="navbar">
    <ul>
        <li><a href="home.php">Home</a></li>
        <li><a href="activity.php">Activity</a></li>
        <li><a href="addNew.php">Add New</a></li>
        <li><a href="profile.php">Profile</a></li>
    </ul>
</nav>
    <div class="upload-container">
        <h1>Upload a Photo</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="file" name="userImage" accept="image/*" required>
            <br>
            <button type="submit" name="upload">Upload</button>
        </form>
        <p><a href="profile.php">Go to Profile</a></p>
    </div>

</body>
</html>
