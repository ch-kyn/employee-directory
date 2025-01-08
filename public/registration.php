<?php
require_once('../includes/db.php');
require_once('../includes/sanitize.php');

session_start();

$errors = [];
$_SESSION['errors'] = '';
$_SESSION['success'] = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (empty($_POST['username'])) {
            $errors[] = "Username is required";
        } else {
            $username = sanitize_data($_POST['username'], $conn);
        }
    
        if (empty($_POST['password'])) {
            $errors[] = "Password is required";
        } else {
            $password = $_POST['password']; // don't sanitize the password
        }
    

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");

        if ($stmt === false) {
             die("Prepare failed: " . htmlspecialchars($conn->error));
        }
        
        // hash the password, and store the hashed version in the database
         $hashed_password = password_hash($password, PASSWORD_DEFAULT); 
         $stmt->bind_param("ss", $username, $hashed_password);
        
         try {
             $stmt->execute();
             $_SESSION['success'] = "Registration successful!";
             $stmt->close();
             $conn->close();
             header('location: login.php');
             exit();
         } catch (mysqli_sql_exception $e) {
             if ($e->getCode() === 1062) {
                $errors[] = "Username already exists. Please choose another one.";
             } else {
                $errors[] = "Error: " . htmlspecialchars($e->getMessage());
             }
         }
    }
}

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
    <title>Registration</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet" />
</head>
<body>
    <div class="center">
    <h1>Registration</h1>
    <form action="" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password">
        <input type="submit" value="Register">
    </form>
    <a href="login.php" class="m1">Back to login</a>

    <!-- echo errors to the UI to inform the user of errors-->
    <?php
    if (!empty($errors)) {
        echo '<ul class="errors">';
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo '</ul>';
    }
    ?>
    </div>
</body>
</html>