<?php
require_once ("dbcon.php");

// admin.php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $current_status = $_POST['current_status'];

    // Toggle the status
    $new_status = ($current_status == 'approved') ? 'pending' : 'approved';
    $sql = "UPDATE users SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $user_id);
    $stmt->execute();
    $stmt->close();
}

$sql = "SELECT id, username, status FROM users where is_admin = FALSE order by id desc";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>

<body>
    <h2>Admin Dashboard</h2>
    <h3>User Applications</h3>
    <form method="post" action="admin.php">
        <table border="1">
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['username'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "<td>";
                echo "<button type='submit' name='user_id' value='" . $row['id'] . "'>";
                echo ($row['status'] == 'approved') ? "Disapprove" : "Approve";
                echo "</button>";
                echo "<input type='hidden' name='current_status' value='" . $row['status'] . "'>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </form>
    <a href="admin_transactions.php">View All Transactions</a><br>
    <a href="logout.php">Logout</a>
</body>

</html>
<?php
$conn->close();
?>