<?php
// match row with written username, and confirm if the password written in the login form is the same as the (retrieved hashed) password
$stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
$stmt->bind_param("s", $username);

try {
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
}

$result = $stmt->get_result();

// access the result of the query, check if inputs matches only one record, and turn the result to an associative array to access the
// 'username' and 'password' column
if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $hashed_password = $row['password'];

    if (password_verify($password, $hashed_password)) {
       
        session_regenerate_id(true); // regenerate session ID for security
        
        $_SESSION['username'] = $row['username'];
        $_SESSION['isloggedin'] = true;

        echo "Logged in successfully";
    } else {
        echo "Invalid password";
    }
} else {
    echo "Username not found"; // if non-match username, echo error message
}

$stmt->close();
?>