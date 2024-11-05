<?php
require_once('../includes/db.php');
require_once('../includes/session_check.php');
include('../includes/sanitize.php');

$errors = []; // collect custom errors

// GET request through query string to the specific id ...
if (!isset($_GET['id'])) {
    header('location: index.php');
    exit();
}

$id = $_GET['id'];

// prepared statement to find row that matches ID that was passed to URL
$stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// return to 'Employee List' when you enter a non-existing ID to the URL
if ($result->num_rows === 0) {
    header('location: index.php');
    exit();
}

// initialize formData with existing values if the row is found
$formData = [
    'name' => $row['name'],
    'age' => $row['age'],
    'job_title' => $row['job_title'],
    'department' => $row['department']
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // check and overwrite only if POST data is present
    foreach ($formData as $key => $value) {
        if (!empty($_POST[$key])) {
            $formData[$key] = sanitize_data($_POST[$key], $conn); // sanitize input
        } else {
            $errors[] = ucfirst($key) . " is required.";
        }
    }

    if (empty($errors)) {
        // handle file upload exactly like in 'create.php'
        if (isset($_FILES['photo_path']) && $_FILES['photo_path']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "../uploads/";
            $fileName = basename($_FILES['photo_path']['name']);
            $target_file = $target_dir . uniqid() . "_" . $fileName;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["photo_path"]["tmp_name"]);
            if ($check === false) {
                $errors['photo_path'] = "Uploaded file is not an image.";
            }

            if ($_FILES["photo_path"]["size"] > 2000000) {
                $errors['photo_path'] = "File is too large.";
            }

            if (!in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
                $errors['photo_path'] = "Only JPG, JPEG & PNG files are allowed.";
            }

            if (empty($errors)) {
                if (move_uploaded_file($_FILES["photo_path"]["tmp_name"], $target_file)) {
                    $formData['photo_path'] = $target_file;
                } else {
                    $errors['photo_path'] = "Sorry, there was an error uploading your file.";
                }
            }
        }

        // Prepare update statement
        $stmt = $conn->prepare("UPDATE employees SET name = ?, age = ?, job_title = ?, department = ?, photo_path = ? WHERE id = ?");
        $stmt->bind_param("sisssi", $formData['name'], $formData['age'], $formData['job_title'], $formData['department'], $formData['photo_path'], $id);
        
        // Execute and check if successful
        if ($stmt->execute()) {
            header('location: index.php');
            exit();
        } else {
            $errors[] = "Error updating employee: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee</title>
</head>
<body>
    <h2>Edit Employee</h2>

    <?php
    // cisplay errors if they exist
    if (!empty($errors)) {
        echo '<ul>';
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo '</ul>';
    }
    ?>

    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $row['id'];?>">
        <label for="name">Name: </label><br>
        <input type="text" id="name" name="name" value="<?php echo $formData['name'];?>"><br>
        <label for="age">Age: </label><br>
        <input type="number" id="age" name="age" value="<?php echo $formData['age'];?>"><br>
        <label for="job_title">Job Title: </label><br>
        <input type="text" id="job_title" name="job_title" value="<?php echo $formData['job_title'];?>"><br>
        <label for="department">Department: </label><br>
        <input type="text" id="department" name="department" value="<?php echo $formData['department'];?>"><br>
        <label for="photo_path">Photo: </label><br>
        <input type="file" name="photo_path" id="photo_path"><br>
        <input type="submit" value="Update Employee"><br>
    </form>
</body>
</html>
