<?php
require_once('../db/db_connection.php');
include('../includes/sanitize.php');

session_start();

$username = '';
$password = '';
$errors = []; // initialize empty array using its shorthand method

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $errors[] = "All the fields required";
    } else {
        $username = $_POST['username'];
        $password = $_POST['password']; // hassh the retrieved password
    }

    if (empty($errors)) {
        $_SESSION['username'] = sanitize_data($username, $conn); // sanitize input using a sanitize function in oblig3/includes/sanitize
        $_SESSION['password'] = $password; // not necessary to sanitize password
    
        include('../db/auth/verify_user.php');
        $conn->close();    
    }
}

// checks if session variable 'isloggedin' is set, which is 'true' if correct login credentials in 'verify_user.php'
// redirects to ...
if (isset($_SESSION['isloggedin'])) {
    if ($_SESSION['isloggedin']) {
        header('Location: employees.php');
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
</head>
<body>
    
    <h1>Login</h1>
    <form action="" method="post">
        <label for="username">Username: </label><br>
        <input type="text" id="username" name="username"><br>
        <label for="password">Password: </label><br>
        <input type="password" id="password" name="password"><br>
        <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="submit" value="Login">
    </form>
</body>
</html>