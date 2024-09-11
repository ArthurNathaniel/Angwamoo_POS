<?php
include 'db.php';

// Retrieve data sent via POST request
$data = json_decode(file_get_contents("php://input"), true);

// Extract order data
$cashierName = $data['cashierName'];
$orderDate = $data['orderDate'];
$selectedPaymentMethod = $data['selectedPaymentMethod'];
$total = $data['total'];
$amountGiven = isset($data['amountGiven']) ? $data['amountGiven'] : null; // Retrieve amount given
$balance = isset($data['balance']) ? $data['balance'] : null; // Retrieve balance
$orderItems = $data['orderItems'];

foreach ($orderItems as $item) {
    $orderId = $item['orderId'];
    $foodName = $item['foodName'];
    $price = $item['price'];
    $quantity = $item['quantity'];

    // Insert order into database with placeholders and prepared statements
    $sql = "INSERT INTO orders (order_id, food_name, price, quantity, date, cashier_name, payment_method, total, amount_given, balance)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isdisssddd", $orderId, $foodName, $price, $quantity, $orderDate, $cashierName, $selectedPaymentMethod, $total, $amountGiven, $balance);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

echo "Order processed successfully!";
?>
