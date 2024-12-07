<?php
session_start();
include('config2.php');


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$first_name = "";
$last_name = "";
$age = 0;
$gender = "";
$email = "";
$phone_number = "";
$address = "";
$date_of_birth = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $date_of_birth = $_POST['date_of_birth']; // This should be in YYYY-MM-DD format
    $role = 'Patient';

    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("INSERT INTO patients (first_name, last_name, age, gender, email, phone_number, address, date_of_birth) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisssss", $first_name, $last_name, $age, $gender, $email, $phone_number, $address, $date_of_birth);

        if (!$stmt->execute()) {
            throw new Exception("Failed to insert into patients table");
        }

        $patient_id = $stmt->insert_id;

        $insert_user_stmt = $conn->prepare("INSERT INTO users (username, password, role, patient_id) VALUES (?, ?, ?, ?)");
        $insert_user_stmt->bind_param("sssi", $username, $hashed_password, $role, $patient_id);

        if (!$insert_user_stmt->execute()) {
            throw new Exception("Failed to insert into users table");
        }

        $conn->commit();
        echo "Data inserted successfully into both tables.";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Failed to insert: " . $e->getMessage();
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration</title>
    <!-- <link rel="stylesheet" href="../css/form_style.css?version=1 " /> -->
    <link rel="stylesheet" href="../css/style.css?version=1" />
    <link rel="stylesheet" href="../css/Navigation_Style.css" />
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/gh/alphardex/aqua.css@master/dist/aqua.min.css'>
    <link rel="stylesheet" href="../css/login_style.css?version=1.0" />


    <style>
        body {
            float: unset;
        }

        form {
            margin-top: 7cap;
            /* margin-left: 30%; */
            position: relative;
        }

        .login-form {
            background: rgba(0, 0, 0, 0.4);

        }
    </style>

</head>

<body>
    <div class="nav">
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="#">News</a></li>
            <?php if (!isset($_SESSION['role'])) { ?>
                <li><a href="../appointments.php">Book Appointment</a></li>
            <?php } ?>
            <li><a href="#">Gallery</a></li>
            <li><a href="#">Contact Us</a></li>
            <li><a href="#">About Us</a></li>
        </ul>
    </div>


    <div class="body">

        <form class="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <h1>Patient Registration</h1>

            <div class="form-input-material">
                <input class="form-control-material" type="text" id="first_name" name="first_name" placeholder=" " autocomplete="off" required>
                <label for="first_name">First Name:</label>
            </div>

            <div class="form-input-material">
                <input class="form-control-material" type="text" id="last_name" name="last_name" placeholder=" " autocomplete="off" required>
                <label for="last_name">Last Name:</label>
            </div>

            <div class="form-input-material">
                <input class="form-control-material" type="number" id="age" name="age" placeholder=" " autocomplete="off" required>
                <label for="age">Age:</label>
            </div>

            <div class="form-input-material">
                <select class="form-control-material" id="gender" name="gender" placeholder=" " autocomplete="off" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
                <label for="gender">Gender:</label>
            </div>

            <div class="form-input-material">
                <input class="form-control-material" type="email" id="email" name="email" placeholder=" " autocomplete="off" required>
                <label for="email">Email:</label>
            </div>

            <div class="form-input-material">
                <input class="form-control-material" type="text" id="phone_number" name="phone_number" placeholder=" " autocomplete="off" required>
                <label for="phone_number">Phone Number:</label>
            </div>

            <div class="form-input-material">
                <input class="form-control-material" type="text" id="address" name="address" placeholder=" " autocomplete="off" required>
                <label for="address">Address:</label>
            </div>

            <div class="form-input-material">
                <input class="form-control-material" type="date" id="date_of_birth" name="date_of_birth" placeholder=" " autocomplete="off" required>
                <label for="date_of_birth">Date of Birth:</label>
            </div>

            <!-- <input type="submit" value="Register Patient"> -->
            <br>
            <br>
            <!-- Username and Password fields -->
            <div class="form-input-material">
                <input class="form-control-material" type="text" name="username" placeholder=" " autocomplete="off" required>
                <label for="username">Username:</label>
            </div>

            <div class="form-input-material">
                <input class="form-control-material" type="password" name="password" placeholder=" " autocomplete="off" required>
                <label for="password">Password:</label>
            </div>

            <div class="form-input-material">
                <input class="form-control-material" type="password" name="confirm_password" placeholder=" " autocomplete="off" required>
                <label for="confirm_password">Confirm Password:</label>
            </div>

            <button type="submit" value="Register" class="btn btn-primary btn-ghost">Submit</button>

            <p style="color :chartreuse">Already have an Account ?</p>
            <a href="../login.php">Login</a>
        </form>
    </div>

</body>

</html>