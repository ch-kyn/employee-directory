<?php
// establish connection to database 'corax'
$servername = '127.0.0.1';
$username = 'root';
$password = '';
$db = 'corax';

$conn = new mysqli($servername, $username, $password, $db);

if ($conn->connect_error) {
    die("Connection failed: {$conn->connect_error}");
}
?>