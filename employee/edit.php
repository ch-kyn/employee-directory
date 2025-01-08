<?php
require_once('../includes/db.php');
require_once('../includes/session_check.php');
include('../includes/sanitize.php');


// edit the entry by pre-filling the form with the data retrieved from the matching ID passed to the URL
// and update it when changing the values in the form in the subsequent POST request

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
    'department' => $row['department'],
    'photo_path' => $row['photo_path']
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // check and overwrite only if POST data is present
    foreach ($formData as $key => $value) {
        if (!empty($_POST[$key])) {
            $formData[$key] = sanitize_data($_POST[$key], $conn); // sanitize input
        } elseif ($key !== 'photo_path') {
            $errors[] = ucfirst($key) . " is required.";
        }
    }

       // similar to in delete.php, fetch the photo path from the database and delete from uploads/ using unlink
       // but do this before uploading a new image to not store others' files on the server if not needed
       $stmt = $conn->prepare("SELECT photo_path FROM employees WHERE id = ?");
       $stmt->bind_param("i", $id);
       $stmt->execute();
       $stmt->bind_result($photo_path);
       $stmt->fetch();
       $stmt->close();

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
                // checks if the row has an image associated with it, and delete when it exist
                if (!empty($photo_path) && file_exists($photo_path)) {
                    unlink($photo_path);

                    if (move_uploaded_file($_FILES["photo_path"]["tmp_name"], $target_file)) {
                        $formData['photo_path'] = $target_file;
                    } else {
                        $errors['photo_path'] = "Sorry, there was an error uploading your file.";
                    }
                }
            }
        }

        // prepare update statement
        $stmt = $conn->prepare("UPDATE employees SET name = ?, age = ?, job_title = ?, department = ?, photo_path = ? WHERE id = ?");
        $stmt->bind_param("sisssi", $formData['name'], $formData['age'], $formData['job_title'], $formData['department'], $formData['photo_path'], $id);
        
        // execute and check if successful
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
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
    <h1>Edit Employee</h1>

    <?php
    // display errors if they exist
    if (!empty($errors)) {
        echo '<ul class="errors">';
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo '</ul>';
    }
    ?>

    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $row['id'];?>">
        <label for="name">Name: </label>
        <input type="text" id="name" name="name" value="<?php echo $formData['name'];?>">
        <label for="age">Age: </label>
        <input type="number" id="age" name="age" value="<?php echo $formData['age'];?>">
        <label for="job_title">Job Title: </label>
        <input type="text" id="job_title" name="job_title" value="<?php echo $formData['job_title'];?>">
        <label for="department">Department: </label>
        <input type="text" id="department" name="department" value="<?php echo $formData['department'];?>">
        <label for="photo_path">Photo: </label>
        <input type="file" name="photo_path" id="photo_path">
        <input type="submit" value="Update Employee">
    </form>
    </div>
</body>
</html>
