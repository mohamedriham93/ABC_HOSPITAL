<?php
session_start();
include('../config.php'); // This file should contain the $conn (database connection)

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_POST['email'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert admin details into the `admin` table
    $stmt = $conn->prepare("INSERT INTO admin (username, password, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashed_password, $email);

    if ($stmt->execute()) {
        // Get the inserted admin's ID
        $admin_id = $stmt->insert_id;

        // Insert into the `users` table with the role 'Admin' and link to the admin ID
        $insert_user_stmt = $conn->prepare("INSERT INTO users (username, password, role, admin_id) VALUES (?, ?, ?, ?)");
        $role = 'Admin';
        $insert_user_stmt->bind_param("sssi", $username, $hashed_password, $role, $admin_id);

        if ($insert_user_stmt->execute()) {
            echo "Admin registered successfully.";
        } else {
            // Check for unique constraint violations (username already exists)
            if ($insert_user_stmt->errno == 1062) {
                echo "Error: Username already exists.";
            } else {
                echo "Failed to register user in the 'users' table.";
            }
        }

        $insert_user_stmt->close();
    } else {
        // Check for unique constraint violations (username or email already exists)
        if ($stmt->errno == 1062) {
            echo "Error: Username or Email already exists in the 'admin' table.";
        } else {
            echo "Failed to register admin.";
        }
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="../css/form_style.css">
</head>
<body>

<h2>Admin Registration</h2>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br><br>

    <label for="confirm_password">Confirm Password:</label>
    <input type="password" id="confirm_password" name="confirm_password" required><br><br>

    <input type="submit" value="Register">

    <a href="../login.php">Login</a>
</form>

</body>
</html>
