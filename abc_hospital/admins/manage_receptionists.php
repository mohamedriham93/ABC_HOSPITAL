<?php
session_start();
include('../config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch all receptionists
$result = $conn->query("SELECT * FROM receptionists");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Receptionists - ABC Hospital</title>
    <link rel="stylesheet" href="../css/dashboard_style.css?version=1">
    <link rel="stylesheet" href="../css/form_style.css?version=1">
    <link rel="stylesheet" href="../css/Dashboard_Navigation.css?version=1">
    <link rel="stylesheet" href="../css/icons.css?version=1">
    <style>
        body {
            background-image: url("../images/img8.jpg");
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: 100% 150%;
        }
    </style>
</head>

<body>
    <div class="nav">
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="manage_patients.php">Manage Patients</a></li>
            <li><a href="manage_doctors.php">Manage Doctors</a></li>
            <li><a href="manage_receptionists.php">Manage Receptionists</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>

    <header>
        <br><br><br><br>
        <h1>Manage Receptionists</h1>
    </header>

    <main>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($receptionist = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $receptionist['receptionist_id']; ?></td>
                        <td><?php echo htmlspecialchars($receptionist['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($receptionist['email']); ?></td>
                        <td>
                            <a href="edit_receptionist.php?id=<?php echo $receptionist['receptionist_id']; ?>">
                                <div class="container">
                                    <img class="image1" src="../images/icons/Edit.jpg" alt="Edit">
                                    <div class="overlay">
                                        <div class="text">Edit</div>

                                    </div>
                                </div>
                            </a>
                        </td>
                        <td>
                            <a href="delete_receptionist.php?receptionist_id=<?php echo $receptionist['receptionist_id']; ?>">
                                <div class="container">
                                    <img class="image" src="../images/icons/delete-16.ico" alt="Delete">
                                    <div class="overlay">
                                        <div class="text">Delete</div>

                                    </div>

                                </div>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <p><a href="add_receptionist.php">Add Receptionist</a></p>
    </main>

    <footer>
        <p>&copy; 2024 ABC Hospital</p>
    </footer>
</body>

</html>