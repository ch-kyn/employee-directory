<?php
// sanitize user input by passing it to multiple built-in functions in a specific order
// to remove HTML/php tags and backslashes, convert special characters to HTML entities,
// and escape special characters used in SQL queries to protect against XSS attacks/SQL injection

function sanitize_data($data, $conn) {
    $data = strip_tags($data);
    $data = htmlentities($data, ENT_QUOTES, 'UTF-8');
    $data = mysqli_real_escape_string($conn, $data);
    $data = stripslashes($data);
    return $data;
}
?>