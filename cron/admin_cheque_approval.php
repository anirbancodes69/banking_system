<?php
require_once ("../dbcon.php");

$conn->begin_transaction();

try {
    // Fetch a pending task
    $sql = "SELECT * FROM queue WHERE status = 'pending' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $task = $result->fetch_assoc();
        $queue_id = $task['id'];
        $transaction_id = $task['transaction_id'];

        // Mark task as processing
        echo "processing";
        $sql = "UPDATE queue SET status = 'processing' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $queue_id);
        $stmt->execute();

        // Approve the cheque
        echo "approved";
        $sql = "UPDATE transactions SET approved = TRUE WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $transaction_id);
        $stmt->execute();

        // Mark task as completed
        echo "completed";

        $sql = "UPDATE queue SET status = 'completed' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $queue_id);
        $stmt->execute();

        $stmt->close();
        $conn->commit();
    } else {
        // No pending tasks, sleep for a while
        $conn->commit();
        sleep(5);
    }
} catch (Exception $e) {
    $conn->rollback();
    echo "Error: " . $e->getMessage();
    sleep(5);
}

// Close the connection outside the loop
$conn->close();
?>