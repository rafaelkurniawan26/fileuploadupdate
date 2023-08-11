<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $file = $_FILES["file"];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    // Check if file is a valid JPEG or PNG image
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    if (!in_array($file['type'], $allowedTypes)) {
        die("Invalid file type. Only JPEG and PNG images are allowed.");
    }

    // Create a new database connection
    $conn=mysqli_connect("localhost", "root", "", "fileupload");
    if (!$conn){die("Connection failed: " . mysqli_connect_error()); }

    // Prepare and execute the SQL query with prepared statement
    $insertQuery = "INSERT INTO uploads (email, filename) VALUES (?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ss", $email, $targetFile);

    // Generate a unique filename to prevent overwriting
    $targetDir = 'uploads/';
    $targetFile = $targetDir . uniqid() . '_' . basename($file['name']);

    // Move the uploaded file to the target directory
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        // Execute the prepared statement
        if ($stmt->execute()) {
            echo "File uploaded and data stored successfully.";
        } else {
            echo "Error storing data in the database.";
        }
    } else {
        echo "Error uploading file.";
    }

    // Close the prepared statement and database connection
    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
</head>
<body>
<h1>File Upload</h1>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>
        
        <label for="file">Upload Image (JPEG or PNG only):</label>
        <input type="file" id="file" name="file" accept=".jpeg, .jpg, .png" required><br>
        
        <button type="submit">Upload</button>
    </form>
</body>
</html>