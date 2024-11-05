<?php
require_once('../includes/db.php');
require_once('../includes/session_check.php');
include('../includes/sanitize.php');

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
            $formData[$field] = sanitzie_data($_POST[$field], $conn); // sanitize the inpu
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
</head>
<body>
    <h2>Add Employee</h2>

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
        <label for="name">Name: </label><br>
        <input type="text" id="name" name="name"><br>
        <label for="age">Age: </label><br>
        <input type="number" id="age" name="age"><br>
        <label for="job_title">Job Title: </label><br>
        <input type="text" id="job_title" name="job_title"><br>
        <label for="department">Department: </label><br>
        <input type="text" id="department" name="department"><br>
        <label for="photo_path">Photo: </label><br>
        <input type="file" name="photo_path" id="photo_path"><br>
        <input type="submit" value="Add Employee"><br>
    </form>
</body>
</html>