<?php
// Start session if needed


// Database configuration
$host = "localhost";     // Database host
$username = "root";      // Database username
$password = "";          // Database password
$database = "knitte_simats";       // Database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
// else {
//     echo "Connected successfully!";
// }
?>