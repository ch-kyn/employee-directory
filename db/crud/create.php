<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../db_connection.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $formData = [];
    $fields = ['name', 'age', 'jobTitle', 'department', 'photoPath'];
    $uploadOk = 1;

    foreach ($fields as $field) {
        if (empty($_POST[$field])) {
            $formData[$field] = '';
            $errors[$field] = ucfirst($field) . " is required";
        } else {
            $formData[$field] = $_POST[$field];
        }
    }   

    if (empty($errors)) {
        foreach ($fields as $field) {
            $_SESSION[$field] = $formData[$field]; // assign values to $formData[key]
        }
    }
  
      if (isset($_FILES['photoPath']) && $_FILES['photoPath']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../../uploads/";
        $target_file = $target_dir . basename($_FILES['photoPath']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION)); // check and return only the file extension
        $docFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION)); // check and return only the file extension
        
        $fileName = htmlspecialchars(basename($_FILES["photoPath"]["name"]));
    
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES["photoPath"]["tmp_name"], $target_file)) {
                echo "The file {$fileName} has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
      }}

if (empty($errors)) {
    $stmt = $conn->prepare("INSERT INTO employees (name, age, job_title, department, photo_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sisss", $formData['name'], $formData['age'], $formData['jobTitle'], $formData['department'], $formData['photoPath']);

    if ($stmt->execute()) {
        echo "Record inserted successfully";
    } else {
        echo "Error adding row: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    // Redirect to the list page after adding
    // header('Location: employees.php');
    // exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Entry</title>
</head>
<body>
    <!-- add enctype="multipart/form-data" to allow file uploads -->
    <form method="post" enctype="multipart/form-data">
        <label for="name">Name: </label><br>
        <input type="text" id="name" name="name"><br>
        <label for="age">Age: </label><br>
        <input type="number" id="age" name="age"><br>
        <label for="jobTitle">Job Title: </label><br>
        <input type="text" id="jobTitle" name="jobTitle"><br>
        <label for="department">Department: </label><br>
        <input type="text" id="department" name="department"><br>
        <label for="photoPath">Photo: </label><br>
        <input type="file" name="photoPath" id="photoPath"><br>
        <input type="submit" name="Add Employee"><br>
    </form>
</body>
</html>