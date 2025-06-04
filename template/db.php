<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "municipal_db"; // Change to your database name

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
