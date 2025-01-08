<?php
require_once('../includes/db.php');
require_once('../includes/session_check.php');

// gets the ID sent with a GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    }

    // fetch the photo path from the database and delete from uploads/ using unlink
    $stmt = $conn->prepare("SELECT photo_path FROM employees WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($photo_path);
    $stmt->fetch();
    $stmt->close();
  
    // checks if the row has an image associated with it, and delete when it exist
    if (!empty($photo_path) && file_exists($photo_path)) {
        unlink($photo_path);
    }

    // use a SQL prepared statement to delete the row matching the id stored in the GET request
    $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header('location: index.php');
    exit();
}
?>
