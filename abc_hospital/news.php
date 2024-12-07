<?php
session_start();

$loginMessage = '';
if (!isset($_SESSION['role'])) {
    // $loginMessage = "You are not logged in. Please login to continue.";
} else {
    $role = $_SESSION['role'];
}
require 'config.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABC Hospital News</title>
    <link rel="stylesheet" href="./css/Navigation_Style.css?version = 1   " />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            background-size: cover;
        }
        header {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 1rem 0;
        }
        .news-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .news-item {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .news-item:last-child {
            border-bottom: none;
        }
        .news-title {
            font-size: 1.5rem;
            color: #34495e;
        }
        .news-description {
            font-size: 1rem;
            margin: 10px 0;
            color: #7f8c8d;
        }
        .read-more {
            color: #2980b9;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
        }
        .read-more:hover {
            text-decoration: underline;
        }
        /* Modal Styles */
        #full-details {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            max-width: 500px;
            width: 90%;
        }
        #full-details h2 {
            font-size: 1.5rem;
            margin: 0 0 10px;
        }
        #full-details p {
            margin: 0 0 10px;
        }
        #close-details {
            display: block;
            text-align: right;
            color: #2980b9;
            cursor: pointer;
        }
        #close-details:hover {
            text-decoration: underline;
        }
        /* Overlay */
        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="nav">
            <ul>
                <li><a href="index.php">Home</a></li>
                <!-- <li><a href="news.html">News</a></li> -->
                <?php if (!isset($role)) { ?>
                    <li><a href="appointments.php">Book Appointment</a></li>
                <?php } ?>

                <!-- <li><a href="#">Gallery</a></li> -->
                <!-- <li><a href="#">Contact Us</a></li> -->
                <li><a href="about-us.php">About Us</a></li>

                <?php if (isset($role)) {
                    switch ($role) {
                        case 'Admin': ?>
                            <li><a href="admins/admin_dashboard.php">Dashboard</a></li>
                            <li><a href="Admins/admin_dashboard.php">Manage Users</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        <?php break;
                        case 'Doctor': ?>
                            <li><a href="doctors/doctor_dashboard.php">Dashboard</a></li>
                            <li><a href="doctors/view_appointments.php">View Appointments</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        <?php break;
                        case 'Receptionist': ?>
                            <li><a href="./receptionists/receptionist_dashboard.php">Dashboard</a></li>
                            <li><a href="./receptionists/view_appointments.php">Manage Appointments</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        <?php break;
                        case 'Patient': ?>
                            <li><a href="specializations.php">Doctor Channeling</a></li>
                            <li><a href="Patients/patient_dashboard.php">Dashboard</a></li>
                            <li><a href="Patients/book_appointments.php">Book Appointment</a></li>
                            <li><a href="logout.php">Logout</a></li>

                    <?php break;
                    }
                } else { ?>
                    <li><a href="specializations.php">Doctor Channeling</a></li>
                    <li><a href="login.php">Login</a></li>
                <?php } ?>
            </ul>
            <div class="header_right">
                <?php if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])): ?>
                    <?= htmlspecialchars($_SESSION['first_name']) . " " . htmlspecialchars($_SESSION['last_name']) ?>
                <?php elseif (isset($_SESSION['role']) && isset($_SESSION['username'])): ?>
                    <?= htmlspecialchars($_SESSION['username']) ?>
                <?php endif; ?>

                <a href="upload_image.php"><img class="profile" src="display_image.php" alt="Profile Picture" onerror="this.onerror=null; this.src='images/no-profile-picture-icon.png';"></a>
            </div>
        </div>
    </div>

    <header>
        <h1>ABC Hospital - Latest News & Updates</h1>
    </header>
    <div class="news-container">
        <!-- News Item 1 -->
        <div class="news-item">
            <h2 class="news-title">Free Health Checkup Camp</h2>
            <p class="news-description">ABC Hospital is organizing a free health checkup camp on December 15th. Avail free consultations with our specialists.</p>
            <span class="read-more" data-details="Join us for a free health checkup camp, where our specialists will provide detailed consultations and screenings for general health issues. Date: December 15th, Location: ABC Hospital, Main Lobby.">Read More</span>
        </div>
        <!-- News Item 2 -->
        <div class="news-item">
            <h2 class="news-title">COVID-19 Booster Shots Available</h2>
            <p class="news-description">We are now administering COVID-19 booster shots. Book your appointment online or visit us directly.</p>
            <span class="read-more" data-details="Get your COVID-19 booster shot at ABC Hospital. Walk-ins are welcome, or you can book your appointment via our website or helpline. Stay protected and keep your loved ones safe.">Read More</span>
        </div>
        <!-- News Item 3 -->
        <div class="news-item">
            <h2 class="news-title">New Cardiology Department Launched</h2>
            <p class="news-description">ABC Hospital proudly announces the opening of our state-of-the-art Cardiology Department. Equipped with advanced technology.</p>
            <span class="read-more" data-details="Our new Cardiology Department offers the latest in cardiac care technology, including advanced diagnostics, minimally invasive procedures, and a dedicated team of cardiologists. Now open for appointments.">Read More</span>
        </div>
        <!-- News Item 4 -->
        <div class="news-item">
            <h2 class="news-title">Diabetes Awareness Workshop</h2>
            <p class="news-description">Join our Diabetes Awareness Workshop on November 28th to learn about prevention and management. Open to all.</p>
            <span class="read-more" data-details="This free workshop focuses on raising awareness about diabetes, prevention strategies, and management tips. Expert speakers and Q&A sessions included. Date: November 28th, Time: 10 AM to 2 PM, Location: ABC Hospital Auditorium.">Read More</span>
        </div>
    </div>

    <!-- Overlay -->
    <div id="overlay"></div>

    <!-- Full Details Modal -->
    <div id="full-details">
        <span id="close-details">Close</span>
        <h2 id="details-title"></h2>
        <p id="details-content"></p>
    </div>

    <script>
        const readMoreLinks = document.querySelectorAll('.read-more');
        const fullDetails = document.getElementById('full-details');
        const detailsTitle = document.getElementById('details-title');
        const detailsContent = document.getElementById('details-content');
        const closeDetails = document.getElementById('close-details');
        const overlay = document.getElementById('overlay');

        readMoreLinks.forEach(link => {
            link.addEventListener('click', () => {
                const title = link.parentElement.querySelector('.news-title').textContent;
                const details = link.getAttribute('data-details');

                detailsTitle.textContent = title;
                detailsContent.textContent = details;

                fullDetails.style.display = 'block';
                overlay.style.display = 'block';
            });
        });

        closeDetails.addEventListener('click', () => {
            fullDetails.style.display = 'none';
            overlay.style.display = 'none';
        });

        overlay.addEventListener('click', () => {
            fullDetails.style.display = 'none';
            overlay.style.display = 'none';
        });
    </script>
</body>
</html>
