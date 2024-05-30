<?php

require_once ("dbcon.php");

// send_cheques.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = $_POST['amount'];
    $recipient = $_POST['recipient'];

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

    if ($balance >= $amount) {
        $sql = "INSERT INTO transactions (user_id, type, amount, approved) VALUES (?, 'cheque', ?, FALSE)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("id", $_SESSION['user_id'], $amount);

        if ($stmt->execute()) {
            echo "Cheque request sent to $recipient successfully and is pending admin approval!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Insufficient balance.";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html>

<body>
    <h2>Send Cheques</h2>
    <form method="post" action="send_cheques.php">
        Recipient: <input type="text" name="recipient" required><br>
        Amount: <input type="number" step="0.01" name="amount" required><br>
        <input type="submit" value="Send Cheque">
    </form>
    <a href="dashboard.php">Back to Dashboard</a>
</body>

</html>