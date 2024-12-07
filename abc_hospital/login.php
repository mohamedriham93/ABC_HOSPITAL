<?php
session_start();
include('config.php'); 

$username = "";
$password = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $db_username, $db_password, $role);
        $stmt->fetch();

        if (password_verify($password, $db_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $db_username;
            $_SESSION['role'] = $role;

            if ($role === 'Doctor') {
                $sql = "SELECT name FROM doctors WHERE id = (SELECT doctor_id FROM users WHERE user_id = ?)";
                $stmt2 = $conn->prepare($sql);
                $stmt2->bind_param('i', $user_id);
                $stmt2->execute();
                $stmt2->bind_result($name);
                $stmt2->fetch();
                $_SESSION['first_name'] = $name; 
                $_SESSION['last_name'] = "";
                $stmt2->close();
            } elseif ($role === 'Patient') {
                $sql = "SELECT first_name, last_name FROM patients WHERE patient_id = (SELECT patient_id FROM users WHERE user_id = ?)";
                $stmt2 = $conn->prepare($sql);
                $stmt2->bind_param('i', $user_id);
                $stmt2->execute();
                $stmt2->bind_result($first_name, $last_name);
                $stmt2->fetch();
                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name'] = $last_name;
                $stmt2->close();
            } elseif ($role === 'Receptionist') {
                $sql = "SELECT first_name, last_name FROM receptionists WHERE receptionist_id = (SELECT receptionist_id FROM users WHERE user_id = ?)";
                $stmt2 = $conn->prepare($sql);
                $stmt2->bind_param('i', $user_id);
                $stmt2->execute();
                $stmt2->bind_result($first_name, $last_name);
                $stmt2->fetch();
                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name'] = $last_name;
                $stmt2->close();
            }

            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Invalid username.";
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
    <title>Login</title>
    <!-- <link rel="stylesheet" href="css/login.css"> -->
    <link rel="stylesheet" href="./css/Navigation_Style.css?version=1">
    <!-- <link rel="stylesheet" href="css/style.css"> -->
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/gh/alphardex/aqua.css@master/dist/aqua.min.css'>
    <link rel="stylesheet" href="./css/login_style.css?verion=1">

    <style>
        body {
            background-image: url("images/img3.jpg");
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            margin: 0%;
        }
    </style>
</head>

<body>
    <div class="nav">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="news.php">News</a></li>
            <?php if (!isset($_SESSION['role'])) { ?>
                <li><a href="appointments.php">Book Appointment</a></li>
            <?php } ?>
            <!-- <li><a href="#">Gallery</a></li> -->
            <!-- <li><a href="#">Contact Us</a></li> -->
            <li><a href="about-us.php">About Us</a></li>
        </ul>
    </div>


<div class="body">
    <form class="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <h1>Login</h1>

        <div class="form-input-material">
            <input class="form-control-material" type="text" id="username" name="username" placeholder=" " autocomplete="off" required>
            <label for="username">Username:</label>
        </div>
        <div class="form-input-material">
            <input class="form-control-material" type="password" id="password" name="password" placeholder=" " autocomplete="off" required>
            <label for="password">Password:</label>
        </div>
        <button class="btn btn-primary btn-ghost" type="submit" value="Login">Login</button>

        <a href="Patients/Patient_Registration.php" class="signup">SignUp Patient</a>

        <div class="error">
            <?php if (!empty($error)) {
                echo htmlspecialchars($error);
            } ?>
        </div>
    </form>
    </div>


</body>

</html>