<?php
$host = "localhost";     
$user = "root";          
$pass = "";             
$dbname = "trainup";    

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// echo "Connected successfully!";
?>

