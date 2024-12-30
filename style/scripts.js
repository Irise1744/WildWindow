document.addEventListener("DOMContentLoaded", function () {
    const uploadArea = document.getElementById("uploadArea");
    const fileInput = document.getElementById("fileInput");
    const fileLabel = document.getElementById("fileLabel");
    const fileNameDisplay = document.getElementById("fileName");

    // Handle click to trigger file input
    uploadArea.addEventListener("click", () => {
        fileInput.click();
    });

    fileLabel.addEventListener("click", (event) => {
        event.stopPropagation(); // Prevents uploadArea click event
        fileInput.click();
    });

    // Handle file selection
    fileInput.addEventListener("change", () => {
        if (fileInput.files.length > 0) {
            fileNameDisplay.textContent = `Selected File: ${fileInput.files[0].name}`;
        }
    });

    // Handle drag and drop
    uploadArea.addEventListener("dragover", (event) => {
        event.preventDefault();
        uploadArea.style.backgroundColor = "#c8e6c9";
    });

    uploadArea.addEventListener("dragleave", () => {
        uploadArea.style.backgroundColor = "";
    });

    uploadArea.addEventListener("drop", (event) => {
        event.preventDefault();
        uploadArea.style.backgroundColor = "";
        
        const droppedFiles = event.dataTransfer.files;
        if (droppedFiles.length > 0) {
            fileInput.files = droppedFiles; // Assign files to the input
            fileNameDisplay.textContent = `Selected File: ${droppedFiles[0].name}`;
        }
    });
});
