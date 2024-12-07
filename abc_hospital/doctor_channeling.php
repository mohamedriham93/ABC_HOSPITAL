<?php
// Start the session to access user_id from session
session_start();

// Connect to the database
include('config.php');

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT picture FROM user_pictures WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($imageContent);
    $stmt->fetch();

    if ($imageContent) {
        header("Content-Type: image/jpeg");
        echo $imageContent;
        exit; 
    } else {
        $message = "No image found for this user.";
    }

    $stmt->close();
} else {
    $message = "User ID not found in session.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Uploaded Image</title>
</head>
<body>
    <h2>Uploaded Image</h2>
    <?php if (isset($message)): ?>
        <p><?php echo $message; ?></p>
    <?php else: ?>
        <img src="view_image.php" alt="User Image">
    <?php endif; ?>
</body>
</html>
