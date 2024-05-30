<?php
// admin_transactions.php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', 'Mysqlisbest@1', 'banking_system_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['transaction_id'])) {
    $transaction_id = $_POST['transaction_id'];
    $sql = "UPDATE transactions SET approved = TRUE WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $transaction_id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_transactions.php");

}

$sql = "SELECT t.id, u.username, t.type, t.amount, t.date, t.approved 
        FROM transactions t 
        JOIN users u ON t.user_id = u.id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>

<body>
    <h2>Admin - All Transactions</h2>
    <table border="1">
        <tr>
            <th>Transaction ID</th>
            <th>Username</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Approved</th>
            <th>Action</th>
        </tr>
        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['username'] . "</td>";
            echo "<td>" . $row['type'] . "</td>";
            echo "<td>" . $row['amount'] . "</td>";
            echo "<td>" . $row['date'] . "</td>";
            echo "<td>" . ($row['approved'] ? 'Yes' : 'No') . "</td>";
            echo "<td>";
            if ($row['type'] == 'cheque' && !$row['approved']) {
                echo "<form method='post' action='admin_transactions.php'>";
                echo "<button type='submit' name='transaction_id' value='" . $row['id'] . "'>Approve</button>";
                echo "</form>";
            }
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>
    <a href="admin.php">Back to Admin Dashboard</a>
</body>

</html>
<?php
$conn->close();
?>