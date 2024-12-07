<?php
include('../config.php');

if (isset($_GET['doctor_id'])) {
    $doctor_id = mysqli_real_escape_string($conn, $_GET['doctor_id']);
    $query = "SELECT schedule FROM doctors WHERE id = '$doctor_id'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo htmlspecialchars($row['schedule']);
    } else {
        echo "No schedule available.";
    }
} else {
    echo "Invalid request.";
}

mysqli_close($conn);
?>
