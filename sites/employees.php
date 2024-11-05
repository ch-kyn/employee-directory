<?php
require_once('../db/db_connection.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $formData = [];
    $fields = ['name', 'age', 'jobTitle', 'department', 'photoPath'];

    foreach ($fields as $field) {
        if (empty($_POST[$field])) {
            $validationErrors[$field] = ucfirst($field) . " is required";
        } else {
            $formData[$field] = $_POST[$field];
        }
    }   

    if (empty($errors)) {
        foreach ($requiredFields as $field) {
            $_SESSION[$field] = $formData[$field]; // assign values to $formData[key]
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee</title>
</head>
<body>
    <nav>
        <ul>
            <li><a href=""></a>Home</li>
            <li><a href="index.php?logout=true"></a>Logout</li>
        </ul>
    </nav>
    <?php
    /* foreach ($data as $row) {
        // echo rows
    } */
    ?>

    
    <h1>Employee List<h1>
    <a href="../db/crud/create.php">Add Employee</a>
    <span>List View</span>
    <span>Card View</span>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Age</th>
            <th>Job Title</th>
            <th>Department</th>
            <th>Actions</th>
        </tr>    
    <table>

    <?php
    require('../db/crud/read.php');
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['age']}</td>
                <td>{$row['job_title']}</td>
                <td>{$row['department']}</td>
                <td><a href='../db/crud/edit.php?id={$row['id']}'>Edit</a> | <a href='./db/crud/delete.php?id={$row['id']}'>Delete</a></td>
                <tr>";
        };

        $stmt->close();
        $conn->close();
    ?>
</body>
</html>