<?php
// home.php

// 1. Include the database connection file
//    Make sure db.php is in the same directory, or adjust the path if needed.
require_once 'C:\xampp\htdocs\Wildwindow\php\config.php';

// Example user ID—if you have an actual login system, retrieve it from session.
$userId = 1;

// 2. Handle form submission for file upload.
if (isset($_POST['upload'])) {
    // Define the folder where images will be saved
    $uploadDir = 'uploads/';

    // Create the uploads folder if it doesn’t exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Get the uploaded file info
    $fileName  = basename($_FILES['userImage']['name']);
    $targetFile = $uploadDir . $fileName;
    $fileType  = $_FILES['userImage']['type'];

    // Allowed mime types
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

    // Check if the file type is valid
    if (!in_array($fileType, $allowedTypes)) {
        echo "<p style='color:red;'>Only JPG, PNG, or GIF files are allowed.</p>";
    } else {
        // Attempt to move the file from temporary directory to uploads folder
        if (move_uploaded_file($_FILES['userImage']['tmp_name'], $targetFile)) {
            echo "<p style='color:green;'>File uploaded successfully!</p>";

            // (Optional) Resize large images on the server side
            list($width, $height) = getimagesize($targetFile);
            $maxDim = 800; // max dimension (width or height)
            if ($width > $maxDim || $height > $maxDim) {
                // Calculate new dimensions
                $ratio = $width / $height;
                if ($ratio > 1) {
                    $newWidth  = $maxDim;
                    $newHeight = $maxDim / $ratio;
                } else {
                    $newWidth  = $maxDim * $ratio;
                    $newHeight = $maxDim;
                }
                // Create image resources
                $src = imagecreatefromstring(file_get_contents($targetFile));
                $dst = imagecreatetruecolor($newWidth, $newHeight);
                // Copy and resize
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                // Overwrite the original file as JPEG (80% quality)
                imagejpeg($dst, $targetFile, 80);
                // Free memory
                imagedestroy($src);
                imagedestroy($dst);
            }

            // 3. Insert file info into the database
            try {
                $stmt = $conn->prepare("INSERT INTO photos (user_id, filename) VALUES (:user_id, :filename)");
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->bindParam(':filename', $fileName, PDO::PARAM_STR);
                $stmt->execute();
            } catch (PDOException $e) {
                echo "<p style='color:red;'>Database Error: " . $e->getMessage() . "</p>";
            }

        } else {
            echo "<p style='color:red;'>There was an error uploading your file.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Upload a Photo</title>
    <!-- 4. Link to your separate CSS file -->
    <link rel="stylesheet" href="style/style.css">
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
            <br><br>
            <button type="submit" name="upload">Upload</button>
        </form>
        <!-- Link to profile page to see the gallery -->
        <p><a href="profile.php">Go to Profile</a></p>
    </div>
</body>
</html>
