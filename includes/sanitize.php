<?php
function sanitize_data($data, $conn) {
    $data = strip_tags($data);
    $data = htmlentities($data, ENT_QUOTES, 'UTF-8');
    $data = mysqli_real_escape_string($conn, $data);
    $data = stripslashes($data);
    return $data;
}
?>