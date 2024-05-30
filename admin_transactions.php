<?php
require_once ("dbcon.php");

session_start();

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

function enqueue($transaction_id, $conn)
{
    $sql = "INSERT INTO queue (transaction_id, status) VALUES (?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $transaction_id);
    $stmt->execute();
    $stmt->close();
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['transaction_id'])) {
    $transaction_id = $_POST['transaction_id'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Enqueue the task
        enqueue($transaction_id, $conn);

        // Commit the transaction
        $conn->commit();
        echo "Cheque approval enqueued successfully!";
    } catch (Exception $e) {
        // Rollback the transaction on failure
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }

    header("Location: admin_transactions.php");
    exit();
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