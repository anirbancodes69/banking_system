<?php

require_once ("dbcon.php");


// register.php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    if ($password == 123456789) {
        // ADMIN USER
        $sql = "INSERT INTO users (username, password, status, is_admin) VALUES (?, ?, 'approved', TRUE)";
    } else {
        $sql = "INSERT INTO users (username, password, status, is_admin) VALUES (?, ?, 'pending', FALSE)";
    }


    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $hashed_password);

    if ($stmt->execute()) {
        // Redirect to login page with success message
        header("Location: login.php?message=Registration successful! Please log in.");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html>

<body>
    <h2>Register</h2>
    <form method="post" action="index.php">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        <input type="submit" value="Register">
    </form>
    <a href="login.php">Already have an account? Log in here</a>
</body>

</html>