<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add</title>
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

    <!-- add enctype="multipart/form-data" to allow file uploads--->
    <form method="post" enctype="multipart/form-data">
        <label for="name">Name:  </label><br>
        <input type="text" id="name" name="name"><br>
        <label for="age">Age: </label><br>
        <input type="int" id="age" name="age"><br>
        <label for="jobTitle">Job Title: </label><br>
        <input type="text" id="jobTitle" name="jobTitle"><br>
        <label for="department">Department: </label><br>
        <input type="text" id="department" name="department"><br>
        <input type="file" name="fileToUpload" id="fileToUpload"><br>
        <input type="submit" name="Add Employee"><br>
    </form>
</body>
</html>