<?php
include 'config.php';

// Get specialization from query parameters
$specialization = isset($_GET['specialization']) ? $_GET['specialization'] : null;

if (!$specialization) {
    echo "Specialization not specified!";
    exit;
}

// Query to fetch doctors with their pictures for the selected specialization
$query = "
    SELECT 
        s.specialization_name, 
        s.experience, 
        d.id AS doctor_id, 
        d.name, 
        d.gender, 
        d.age, 
        d.schedule,
        up.picture
    FROM 
        specialization s
    JOIN 
        doctors d ON s.doctor_id = d.id
    JOIN 
        users u ON d.id = u.doctor_id
    LEFT JOIN 
        user_pictures up ON u.user_id = up.user_id
    WHERE 
        s.specialization_name = ?
    ORDER BY 
        d.name";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $specialization);
$stmt->execute();
$result = $stmt->get_result();

$doctors = [];
while ($row = mysqli_fetch_assoc($result)) {
    $doctors[] = [
        'id' => $row['doctor_id'],
        'name' => $row['name'],
        'experience' => $row['experience'],
        'gender' => $row['gender'],
        'age' => $row['age'],
        'schedule' => $row['schedule'],
        'picture' => $row['picture'] ? 'data:image/jpeg;base64,' . base64_encode($row['picture']) : null
    ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        body {
            background-color: #f9f9f9;
            /* padding: 20px; */
        }
        .doctors-container {
            max-width: 800px;
            margin: auto;
        }
        table {
            background-color: rgb(0, 0, 0, 0.2);
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            backdrop-filter: blur(5px);
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: rgb(59, 59, 59, 0.5);
            color: white;
            
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .book-btn {
            background-color: black;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .book-btn:hover {
            background-color: #0056b3;
        }
        img {
            max-width: 150px;
            max-height: 150px;
            border-radius: 10%;
        }
        .action_colm {
            width: 200px;
        }
    </style>
    <title>Doctors for <?php echo htmlspecialchars($specialization); ?></title>
</head>

<body>
    <h2>Doctors Specializing in <?php echo htmlspecialchars($specialization); ?></h2>
    <div class="doctors-container">
        <?php if (count($doctors) > 0): ?>
            <table>
                <tr>
                    <th>Picture</th>
                    <th>Doctor Name</th>
                    <th>Gender</th>
                    <th>Age</th>
                    <th>Experience (Years)</th>
                    <th>Schedule</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($doctors as $doctor): ?>
                    <tr>
                        <td>
                            <?php if ($doctor['picture']): ?>
                                <img src="<?php echo $doctor['picture']; ?>" alt="Doctor Picture">
                            <?php else: ?>
                                <span>No Image</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($doctor['name']); ?></td>
                        <td><?php echo htmlspecialchars($doctor['gender']); ?></td>
                        <td><?php echo htmlspecialchars($doctor['age']); ?></td>
                        <td><?php echo htmlspecialchars($doctor['experience']); ?></td>
                        <td><?php echo htmlspecialchars($doctor['schedule']); ?></td>
                        <td class="action_colm">
                            <a 
                                class="book-btn" 
                                href="./patients/book_appointments.php?doctor_id=<?php echo $doctor['id']; ?>">
                                Book Appointment
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No doctors available for this specialization.</p>
        <?php endif; ?>
    </div>
    <div style="text-align: center; margin-top: 20px;">
        <a href="specializations.php">
            <button style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
                Back to Specializations
            </button>
        </a>
    </div>
</body>

</html>
