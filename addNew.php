<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Photo</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <h1>Upload Your Photo</h1>

    <!-- Upload Area -->
    <div id="uploadArea" class="upload-area">
        <p>Drag & Drop your file here or <span id="fileLabel" class="file-label">Click to Upload</span></p>
        <input type="file" id="fileInput" accept="image/*" style="display: none;">
        <p id="fileName"></p>
    </div>

    <script src="scripts.js"></script>
</body>
</html>
