<?php
include('config.php');

if (isset($_GET['specialization'])) {
    $specialization = $_GET['specialization'];

    // Fetch doctors based on specialization
    $query = "SELECT d.id, d.name, s.specialization_name
              FROM doctors d
              JOIN specialization s ON d.id = s.doctor_id
              WHERE s.specialization_name = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $specialization);
    $stmt->execute();
    $result = $stmt->get_result();

    $doctors = [];
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }

    echo json_encode($doctors);
}
?>
