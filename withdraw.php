<?php

require_once ("dbcon.php");

// withdraw.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

// Check if the user has enough balance
$sql = "SELECT SUM(amount) as balance FROM transactions WHERE user_id = ? and type = 'credit'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($c_balance);
$stmt->fetch();
$stmt->close();

$sql = "SELECT SUM(amount) as balance FROM transactions WHERE user_id = ? and type = 'withdraw' OR (type = 'cheque' and approved = TRUE)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($d_balance);
$stmt->fetch();
$stmt->close();

$balance = $c_balance - $d_balance;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $amount = $_POST['amount'];
    $approved = TRUE;

    if ($balance >= $amount) {
        $sql = "INSERT INTO transactions (user_id, type, amount, approved) VALUES (?, 'withdraw', ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("idi", $_SESSION['user_id'], $amount, $approved);

        if ($stmt->execute()) {
            header("Location: withdraw.php");

        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Insufficient balance.";
    }

}

$conn->close();

?>
<!DOCTYPE html>
<html>

<body>
    <p>Available Balance: <?= $balance; ?></p>
    <h2>Withdraw</h2>
    <form method="post" action="withdraw.php">
        Amount: <input type="number" step="0.01" name="amount" required><br>
        <input type="submit" value="Withdraw">
    </form>
    <a href="dashboard.php">Back to Dashboard</a>
</body>

</html>