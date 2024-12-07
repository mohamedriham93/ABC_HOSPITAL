<?php
session_start();
include('../config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch all patients
$result = $conn->query("SELECT * FROM patients");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Patients - ABC Hospital</title>
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
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="manage_patients.php">Manage Patients</a></li>
            <li><a href="manage_doctors.php">Manage Doctors</a></li>
            <li><a href="manage_receptionists.php">Manage Receptionists</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>

    <header>
        <br>
        <br><br><br>
        <h1>Manage Patients</h1>
    </header>

    <main>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Email</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['patient_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['age']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td>
                            <a href="edit_patient.php?id=<?php echo $row['patient_id']; ?>">
                                <div class="container">
                                    <img class="image1" src="../images/icons/Edit.jpg" alt="Edit">
                                    <div class="overlay">
                                        <div class="text">Edit</div>
                                    </div>
                                </div>
                            </a>
                        </td>
                        <td>
                            <a href="delete_patient.php?id=<?php echo $row['patient_id']; ?>">
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

        <p><a href="add_patient2.php">Add New Patient</a></p>
    </main>

    <footer>
        <p>&copy; 2024 ABC Hospital</p>
    </footer>
</body>

</html>