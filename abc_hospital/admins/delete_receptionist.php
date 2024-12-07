<?php
// Include database connection
include('../config.php');

// Check if the id is set in the URL
if (isset($_GET['receptionist_id'])) {
    $receptionist_id = $_GET['receptionist_id'];

    // Prepare SQL statements to delete from both tables
    $delete_user_query = "DELETE FROM users WHERE receptionist_id = ?";
    $delete_receptionist_query = "DELETE FROM receptionists WHERE receptionist_id = ?";

    // Begin transaction
    mysqli_begin_transaction($conn);

    try {
        // Delete from users table
        if ($stmt = mysqli_prepare($conn, $delete_user_query)) {
            mysqli_stmt_bind_param($stmt, 'i', $receptionist_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        // Delete from receptionists table
        if ($stmt = mysqli_prepare($conn, $delete_receptionist_query)) {
            mysqli_stmt_bind_param($stmt, 'i', $receptionist_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        // Commit transaction
        mysqli_commit($conn);

        // Redirect to the manage receptionists page after deletion
        header('Location: manage_receptionists.php');
        exit();
    } catch (Exception $e) {
        // Rollback in case of error
        // mysqli_roll_back($conn);
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Receptionist ID not specified.";
}

// Close the database connection
mysqli_close($conn);
?>
