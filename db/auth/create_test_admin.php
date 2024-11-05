<?php
require_once('../db/db_connection.php');

// hardcoded login credentials for testing purposes
$test_username = 'Admin';
$test_password = '1234';
$hashed_password = password_hash($test_password, PASSWORD_DEFAULT); // hash the password

$stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");

$stmt->bind_param("ss", $test_username, $hashed_password);

$stmt->execute(); // execute prepared statement

$stmt->close();
$conn->close();
?>
