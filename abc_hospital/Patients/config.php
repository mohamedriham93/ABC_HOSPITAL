<?php
$mysqli = new mysqli("localhost", "root", "", "abc_hospital");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>
