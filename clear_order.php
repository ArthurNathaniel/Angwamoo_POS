<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $orderID = $_POST['order_id'];

    // Update the status of the order to 'cleared'
    $sql = "UPDATE orders SET status='cleared' WHERE order_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderID);
    if ($stmt->execute()) {
        // Redirect back to the ready orders page
        header("Location: ready_orders.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
