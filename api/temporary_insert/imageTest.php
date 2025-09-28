<?php
// Check if the request method is POST

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if a file was uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Specify the upload directory
        $uploadDir = 'uploads/';

        // Generate a unique filename for the uploaded file
        $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
        $uploadPath = $uploadDir . $fileName;

        // Move the uploaded file to the specified directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            // File upload successful
            $response = [
                'status' => 'success',
                'message' => 'File uploaded successfully',
                'file_path' => $uploadPath
            ];
        } else {
            // Failed to move uploaded file
            $response = [
                'status' => 'error',
                'message' => 'Failed to move uploaded file'
            ];
        }
    } else {
        // No file uploaded or an error occurred
        $response = [
            'status' => 'error',
            'message' => 'No file uploaded or an error occurred'
        ];
    }
} else {
    // Invalid request method
    $response = [
        'status' => 'error',
        'message' => 'Invalid request method'
    ];
}

// Output JSON response
header('Content-Type: application/json');
echo json_encode($response);

?>