<?php
/*
 * A simple script to handle file uploads from Python code.
 * Make sure 'uploads' folder is writable by the web server.
 */

if (isset($_FILES['file'])) {
    $uploadDir = 'uploads/';  // Folder path where files will be saved
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filename = $_FILES['file']['name'];
    $tempPath = $_FILES['file']['tmp_name'];
    $destination = $uploadDir . $filename;

    // Move the uploaded file from the temporary path to the uploads folder
    if (move_uploaded_file($tempPath, $destination)) {
        echo "File uploaded successfully: " . $filename;
    } else {
        echo "Error moving uploaded file.";
    }
} else {
    echo "No file uploaded.";
}
?>
