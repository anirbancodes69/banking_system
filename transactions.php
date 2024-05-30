<?php

require_once ("dbcon.php");

// transactions.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}


$sql = "SELECT type, amount, date, approved FROM transactions WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>

<body>
    <h2>All Transactions</h2>
    <table border="1">
        <tr>
            <th>Type</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Approved</th>
        </tr>
        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['type'] . "</td>";
            echo "<td>" . $row['amount'] . "</td>";
            echo "<td>" . $row['date'] . "</td>";
            echo "<td>" . ($row['approved'] ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
    <a href="dashboard.php">Back to Dashboard</a>
</body>

</html>
<?php
$stmt->close();
$conn->close();