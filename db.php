<?php
$servername = "htrjmusgcr";
$username = "htrjmusgcr";
$password = "4Mw5cZjywY";
$dbname = "htrjmusgcr";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
