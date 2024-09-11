<?php
session_start();

if (!isset($_SESSION['username'])) {
    echo "Not authorized";
    exit();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $orderID = $_POST['order_id'];

    // Update order status in the database
    $sql = "UPDATE orders SET status='ready' WHERE order_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $orderID);

    if ($stmt->execute()) {
        echo "Order marked as ready";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
