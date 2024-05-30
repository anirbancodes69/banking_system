<?php

require_once ("dbcon.php");

// login.php
session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, username, password, status, is_admin FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id, $db_username, $db_password, $status, $is_admin);
    $stmt->fetch();

    if (password_verify($password, $db_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $db_username;
        $_SESSION['status'] = $status;
        $_SESSION['is_admin'] = $is_admin;
        if ($is_admin) {
            header("Location: admin.php");

        } else {
            header("Location: dashboard.php");
        }
        exit();
    } else {
        echo "Invalid username or password.";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html>

<body>
    <h2>Login</h2>
    <?php
    if (isset($_GET['message'])) {
        echo "<p style='color: green;'>" . htmlspecialchars($_GET['message']) . "</p>";
    }
    ?>
    <form method="post" action="login.php">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        <input type="submit" value="Login">
    </form>
    <a href="index.php">Don't have an account? Register here</a>
</body>

</html>