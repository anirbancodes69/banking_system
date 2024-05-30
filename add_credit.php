<?php

require_once ("dbcon.php");

// add_credit.php
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

    $sql = "INSERT INTO transactions (user_id, type, amount, approved) VALUES (?, 'credit', ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idi", $_SESSION['user_id'], $amount, $approved);

    if ($stmt->execute()) {
        header("Location: add_credit.php");
    } else {
        echo "Error: " . $stmt->error;
    }

}

?>
<!DOCTYPE html>
<html>

<body>
    <p>Available Balance: <?= $balance; ?></p>
    <h2>Add Credit</h2>
    <form method="post" action="add_credit.php">
        Amount: <input type="number" step="0.01" name="amount" required><br>
        <input type="submit" value="Add Credit">
    </form>
    <a href="dashboard.php">Back to Dashboard</a>
</body>

</html>