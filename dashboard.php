<?php
// dashboard.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', 'Mysqlisbest@1', 'banking_system_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Calculate total balance
$sql = "SELECT SUM(amount) as balance FROM transactions WHERE user_id = ? and type = 'credit'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($c_balance);
$stmt->fetch();
$stmt->close();

$sql = "SELECT SUM(amount) as balance FROM transactions WHERE user_id = ? and type = 'withdraw'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($d_balance);
$stmt->fetch();
$stmt->close();

$balance = $c_balance - $d_balance;

$sql = "SELECT status FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($status);
$stmt->fetch();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html>

<body>
    <h2>Dashboard</h2>
    <?php
    if ($status == 'pending') {
        echo "<p>Your application is under process.</p>";
    } else if ($status == 'approved') {
        echo "<p>Welcome, " . $_SESSION['username'] . "</p>";
        echo "<p>Total Balance: " . $balance . "</p>";

        echo "<ul>";
        echo "<li><a href='add_credit.php'>Add Credit to Account</a></li>";
        echo "<li><a href='withdraw.php'>Withdraw from Account</a></li>";
        echo "<li><a href='send_cheques.php'>Send Cheques</a></li>";
        echo "<li><a href='transactions.php'>View All Transactions</a></li>";
        echo "</ul>";
    }
    ?>
    <a href="logout.php">Logout</a>
</body>

</html>