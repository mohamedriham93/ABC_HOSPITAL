<?php
session_start();

if (!isset($_SESSION['role'])){
    header("Location: ./login.php");
    exit();
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to upload images.");
}

$user_id = $_SESSION['user_id'];

// Connect to the database
include ('config.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];

    // Validate image upload
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];

    // Allow only certain file formats (e.g., jpg, jpeg, png)
    $allowed = array('jpg', 'jpeg', 'png');
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (in_array($fileExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 5000000) { // 5MB max file size
                // Read the file content into a variable
                $imageContent = file_get_contents($fileTmpName);

                // Check if the image was read correctly
                if ($imageContent === false) {
                    die("Error: Failed to read the image file.");
                }

                // Check if the user already has an image and delete it
                $deleteSql = "DELETE FROM user_pictures WHERE user_id = ?";
                $deleteStmt = $conn->prepare($deleteSql);
                $deleteStmt->bind_param('i', $user_id);
                $deleteStmt->execute();
                $deleteStmt->close();

                // Store the new file in the database as BLOB
                $sql = "INSERT INTO user_pictures (user_id, picture, upload_date) VALUES (?, ?, NOW())";
                $stmt = $conn->prepare($sql);

                if ($stmt === false) {
                    die("Error preparing SQL statement: " . $conn->error);
                }

                // Bind the user ID (integer) and the image content (blob)
                $stmt->bind_param('ib', $user_id, $imageContent);

                // Send the binary image data using send_long_data
                $stmt->send_long_data(1, $imageContent);

                if ($stmt->execute()) {
                    echo "Image uploaded and stored in the database successfully!";
                } else {
                    echo "Error executing SQL: " . $stmt->error;
                }

                $stmt->close();
            } else {
                echo "Error: Your file is too large.";
            }
        } else {
            echo "Error: There was an error uploading your file.";
        }
    } else {
        echo "Error: You cannot upload files of this type.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Image</title>

    <link rel="stylesheet" href="./css/Navigation_Style.css?version = 1   " />
    <link rel="stylesheet" href="./css/style.css?version= 1">
    <link rel="stylesheet" href="./css/header.css?version=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="./css/index.css">
    <style>

        h2 {
            color: #333;
        }
        #imagePreviewContainer {
            display: none;
            margin-top: 20px;
        }
        #imagePreview {
            max-width: 100%;
            max-height: 400px;
            border: 2px solid #ccc;
            border-radius: 8px;
            padding: 10px;
        }
        input[type="file"] {
            margin: 10px 0;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<div class="header">
        <div class="nav">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="#">News</a></li>
               

                <li><a href="#">Gallery</a></li>
                <li><a href="#">Contact Us</a></li>
                <li><a href="about-us.php">About Us</a></li>

               
            </ul>
            <div class="header_right">
                <?php if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])): ?>
                    <?= htmlspecialchars($_SESSION['first_name']) . " " . htmlspecialchars($_SESSION['last_name']) ?>
                <?php elseif (isset($_SESSION['role']) && isset($_SESSION['username'])): ?>
                    <?= htmlspecialchars($_SESSION['username']) ?>
                <?php endif; ?>

                <a href="upload_image.php"><img class="profile" src="display_image.php" alt="Profile Picture" onerror="this.onerror=null; this.src='images/no-profile-picture-icon.png';"></a>
            </div>
        </div>
    </div>

    <div class="index_box">

   
    <h2>Upload Your Image</h2>
    <form action="upload_image.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="image" required accept="image/*" onchange="previewImage(event)">
        <div id="imagePreviewContainer">
            <h3>Image Preview:</h3>
            <img id="imagePreview" src="#" alt="Image Preview">
        </div>
        <button type="submit">Upload</button>
    </form>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imagePreview = document.getElementById('imagePreview');
                    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
                    imagePreview.src = e.target.result;
                    imagePreviewContainer.style.display = 'block'; // Show the preview container
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
     </div>
</body>
</html>
