<?php
require_once('../includes/db.php');
include('../includes/sanitize.php');
include('../includes/test.php');

session_start();

$username = '';
$password = '';
$errors = []; // initialize empty array using its shorthand method

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $errors[] = "All fields are required";
    } else {
        $username = sanitize_data($_POST['username'], $conn); // sanitize input using a sanitize function in 'includes/sanitize.php 
        $password = $_POST['password'];
    }

    if (empty($errors)) {
        $_SESSION['username'] = $username;   
        include('../auth/verify_user.php');
        $conn->close();    
    }
}

// checks if session variable 'isloggedin' is set, which is 'true' if correct login credentials in 'verify_user.php'
// redirects to the index page if successfully logged in
if (isset($_SESSION['isloggedin'])) {
    if ($_SESSION['isloggedin']) {
        header('location: ../employee/index.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet" />
</head>
<body>
    <div class="center">
    <h1>Login</h1>
    <form action="" method="post">
        <label for="username">Username: </label>
        <input type="text" id="username" name="username">
        <label for="password">Password: </label>
        <input type="password" id="password" name="password">
        <input type="submit" value="Login">
    </form>
    <a href="registration.php" class="m1">Sign Up</a>

    <?php
    if (isset($_SESSION['errors'])) {
        echo $_SESSION['errors'];
    } 
    
    if (isset($_SESSION['success'])) {
        echo $_SESSION['success'];
    }
    ?>
    
    </div>
</body>
</html>