<?php
// file that starts session, return user to the login page $_SESSION['isloggedin'] is not set,
// and destroys session and return user to login page if $_GET['logout'] is set and equals 'true'
// 'logout' query parameter is added to the current URL when clicking on the logout link

session_start();

if (!isset($_SESSION['isloggedin'])) {
    header('location: ../public/login.php');
    exit();
}

if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    session_unset();
    session_destroy();
    header('location: ../public/login.php');
    exit();
}
?>