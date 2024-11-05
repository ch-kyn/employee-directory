<?php
require_once('../includes/db.php');
require_once('../includes/session_check.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>
</head>
<body>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="index.php?logout=true">Logout</a></li>
        </ul>
    </nav>
    
    <h1>Employee List<h1>
    <a href="create.php">Add Employee</a>
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
    // prepared statement selecting the whole 'employees' table
    $stmt = $conn->prepare("SELECT * FROM employees");
    $stmt->execute();
    $result = $stmt->get_result();

        // call 'fetch_assoc()', return a row, and display it with its corresponding data in the database to the interface
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['age']}</td>
                <td>{$row['job_title']}</td>
                <td>{$row['department']}</td>
                <td><img src='{$row['photo_path']}'></td> 
                <td><a href='edit.php?id={$row['id']}'>Edit</a> | <a href='delete.php?id={$row['id']}'>Delete</a></td>
                <tr>";
        };

    $stmt->close();
    $conn->close();
    ?>

</body>
</html>