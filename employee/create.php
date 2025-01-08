<?php
// just debugging stuff
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../includes/db.php');
require_once('../includes/session_check.php');
include('../includes/sanitize.php');

// check if it's a POST request, aka if the form in the HTML is sent, and create an entry by filling the form
// photo is optional, but uses a flag to check if the photo does not violate any requirements/limits

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $formData = [];
    $fields = ['name', 'age', 'job_title', 'department'];
    $uploadOk = 1;

    foreach ($fields as $field) {
        if (empty($_POST[$field])) {
            $formData[$field] = '';
            $errors[$field] = ucfirst($field) . " is required";
        } else {
            $formData[$field] = sanitize_data($_POST[$field], $conn); // sanitize the input
        }
    }

    // based on the W3school tutorial on uploading files
    if (isset($_FILES['photo_path']) && $_FILES['photo_path']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../uploads/";
        $fileName = basename($_FILES['photo_path']['name']); // retrieve the basename of the uploaded file from the superglobal $_FILES
        $target_file = $target_dir . uniqid() . "_" . $fileName; // avoid filename conflicts by generating a unique id inside the file name
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION)); // obtain the file type of the image

        $check = getimagesize($_FILES["photo_path"]["tmp_name"]);
        if ($check === false) {
            $errors['photo_path'] = "Uploaded file is not an image.";
            $uploadOk = 0;
        }

        // checks if image is larger than 2MB
        if ($_FILES["photo_path"]["size"] > 2000000) {
            $errors['photo_path'] = "File is too large.";
            $uploadOk = 0;
        }
        
        // only accepts jpg, jpeg, and png files
        if (!$imageFileType === 'jpg' || !$imageFileType === 'jpeg' || !$imageFileType === 'png') {
            $errors['photo_path'] = "Only JPG, JPEG & PNG files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk === 1) {
            if (move_uploaded_file($_FILES["photo_path"]["tmp_name"], $target_file)) {
                $formData['photo_path'] = $target_file;
            } else {
                $errors['photo_path'] = "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        $formData['photo_path'] = ''; // set to empty string if no file is uploaded 
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO employees (name, age, job_title, department, photo_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sisss", $formData['name'], $formData['age'], $formData['job_title'], $formData['department'], $formData['photo_path']);

        // return to index page if the insertion is executed succesfully
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: index.php');
            exit();
        } else {
            echo "Error adding row: " . $stmt->error;
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
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet" />
</head>
<body>
    <nav>
        <span class="logo">Employee Directory</span>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="index.php?logout=true">Logout</a></li>
            </ul>
    </nav>

    <div class="center">
    <h1>Add Employee</h1>

    <?php
        if (!empty($errors)) {
            echo '<ul>';
            foreach ($errors as $error) {
                echo "<li>$error</li>";
            }
            echo '</ul>';
        }
    ?>
    
        <form action="" method="post" enctype="multipart/form-data">
            <label for="name">Name: </label>
            <input type="text" id="name" name="name">
            <label for="age">Age: </label>
            <input type="number" id="age" name="age">
            <label for="job_title">Job Title: </label>
            <input type="text" id="job_title" name="job_title">
            <label for="department">Department: </label>
            <input type="text" id="department" name="department">
            <label for="photo_path">Photo: </label>
            <input type="file" name="photo_path" id="photo_path">
            <input type="submit" value="Add Employee">
        </form>
    <div>
</body>
</html>