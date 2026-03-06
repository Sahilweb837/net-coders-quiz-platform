<?php
// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "net_coders";


// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
