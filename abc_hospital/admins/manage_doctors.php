<?php
session_start();
include('../config.php');

// Ensure the user is an Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch doctor details along with specialization from the specialization table
$query = "
    SELECT doctors.id, doctors.name, doctors.email, GROUP_CONCAT(specialization.specialization_name SEPARATOR ', ') AS specialization
    FROM doctors
    LEFT JOIN specialization ON doctors.id = specialization.doctor_id
    GROUP BY doctors.id
";
$result = $conn->query($query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors - ABC Hospital</title>
    <link rel="stylesheet" href="../css/dashboard_style.css?version=1">
    <link rel="stylesheet" href="../css/form_style.css?version=1">
    <link rel="stylesheet" href="../css/Dashboard_Navigation.css?version=1">
    <link rel="stylesheet" href="../css/icons.css">

    <style>
        body {
            background-image: url("../images/img8.jpg");
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: 100% 150%;
        }
        .p-box {
            padding: 1cap;
            background-color: aquamarine;
            margin-top: 2cap;
            margin: 2cap;
            text-decoration: none;
            color: black;
        }

        :hover.p-box {
            background-color: black;
            color: antiquewhite;
        }

       
    </style>
</head>

<body>
    <div class="nav">
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="manage_patients.php">Manage Patients</a></li>
            <!-- <li><a href="manage_doctors.php">Manage Doctors</a></li> -->
            <li><a href="manage_receptionists.php">Manage Receptionists</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>

    <header>
        <br><br><br><br>
        <h1>Manage Doctors</h1>
    </header>

    <main>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Specialization</th>
                    <th>Email</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($doctor = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $doctor['id']; ?></td>
                        <td><?php echo htmlspecialchars($doctor['name']); ?></td>
                        <td><?php echo htmlspecialchars($doctor['specialization']); ?></td>
                        <td><?php echo htmlspecialchars($doctor['email']); ?></td>
                        <td>
                            <a href="edit_doctor.php?id=<?php echo $doctor['id']; ?>">
                                <div class="container">
                                    <img class="image1" src="../images/icons/Edit.jpg" alt="Edit">
                                    <div class="overlay">
                                        <div class="text">Edit</div>
                                    </div>
                                </div>
                            </a>
                        </td>
                        <td>
                            <a href="delete_doctor.php?id=<?php echo $doctor['id']; ?>"><div class="container">
                                    <img class="image" src="../images/icons/delete-16.ico" alt="Delete">
                                    <div class="overlay">
                                        <div class="text">Delete</div>
                                    </div>
                                </div></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <br><br><br>
        <a class="p-box" href="add_doctor.php">Add Doctor</a>
    </main>

    <footer>
        <p>&copy; 2024 ABC Hospital</p>
    </footer>
</body>

</html>