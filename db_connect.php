<?php
$servername = "localhost";
$username = "root"; // Change if using a different username
$password = ""; // Change if you set a password
$dbname = "back2me";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>