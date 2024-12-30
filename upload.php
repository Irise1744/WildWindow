<?php
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Directory to store uploads
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
    }

    // File and title inputs
    $photo = $_FILES['photo'];
    $title = htmlspecialchars($_POST['title']);

    // Validate the file upload
    if ($photo['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($photo['tmp_name']);
        
        if (in_array($fileType, $allowedTypes)) {
            // Generate unique filename
            $fileName = uniqid() . '-' . basename($photo['name']);
            $uploadFile = $uploadDir . $fileName;

            // Move file to the uploads directory
            if (move_uploaded_file($photo['tmp_name'], $uploadFile)) {
                echo "File uploaded successfully as $fileName.<br>";
                echo "Title: $title";
            } else {
                echo "Error uploading the file.";
            }
        } else {
            echo "Invalid file type. Only JPG, PNG, and GIF are allowed.";
        }
    } else {
        echo "Error: " . $photo['error'];
    }
} else {
    echo "No file uploaded.";
}
?>
