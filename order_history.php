<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Fetch orders from the database
$sql = "SELECT * FROM orders ORDER BY id DESC"; // Assuming the ID column name is 'id'
$result = $conn->query($sql);

$groupedOrders = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orderID = $row['order_id'];
        if (!isset($groupedOrders[$orderID])) {
            $groupedOrders[$orderID] = [];
        }
        $groupedOrders[$orderID][] = $row;
    }
}

// Fetch cashier's name from session
$cashierName = $_SESSION['username'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/food.css">
    <script>
        function greetUser() {
            var currentTime = new Date();
            var currentHour = currentTime.getHours();
            var greeting;

            if (currentHour < 12) {
                greeting = "Good morning";
            } else if (currentHour < 18) {
                greeting = "Good afternoon";
            } else {
                greeting = "Good evening";
            }

            var cashierName = "<?php echo $cashierName; ?>";
            document.getElementById("greeting").innerHTML = greeting + ", " + cashierName;
        }
    </script>

</head>

<body onload="greetUser()">
    <?php include 'sidebar.php'; ?>
    <div class="history_all">
    <div class="welcome_base">
            <div class="profile">
               
            </div>
            <div class="greetings">
                <h1 id="greeting"><?php echo $cashierName; ?></h1>
            </div>
             <button id="darkModeToggle">
                    <i id="darkModeIcon" class="fas fa-moon"></i>
                </button>
        </div>
        <h1>Orders</h1>
        <?php if (empty($groupedOrders)) : ?>
            <p>No orders available.</p>
        <?php else : ?>
            <table id="order-table"> <!-- Added ID to the table -->
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Order ID</th>
                        <th>Food Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Date, Payment Method</th>
                        <th>Total</th>
                        <th>Action</th>
                        <th>Cashier</th>
                    </tr>
                </thead>
                <tbody id="order-body">
                    <?php foreach ($groupedOrders as $orderID => $orders) : ?>
                        <?php $totalPrice = 0; ?>
                        <?php foreach ($orders as $index => $order) : ?>
                            <?php $totalPrice += $order['price'] * $order['quantity']; ?>
                        <?php endforeach; ?>
                        <?php $firstOrder = reset($orders); ?>
                        <tr>
                            <td><?php echo $firstOrder['id']; ?></td>
                            <td rowspan="<?php echo count($orders); ?>"><?php echo $orderID; ?></td>
                            <td><?php echo isset($firstOrder['items']) ? $firstOrder['food_name'] . ', ' . $firstOrder['items'] : $firstOrder['food_name']; ?></td>
                            <td>GH₵ <?php echo number_format($firstOrder['price'], 2); ?></td>
                            <td><?php echo $firstOrder['quantity']; ?></td>
                            <td><?php echo $firstOrder['date'] . ', ' . $firstOrder['payment_method']; ?></td>
                            <td rowspan="<?php echo count($orders); ?>">GH₵ <?php echo number_format($totalPrice, 2); ?></td>
                            <td rowspan="<?php echo count($orders); ?>">
                                <a href="print_receipt.php?order_id=<?php echo $orderID; ?>" target="_blank">Print Receipt</a>
                            </td>
                            <td rowspan="<?php echo count($orders); ?>"><?php echo $cashierName; ?></td>
                        </tr>
                        <?php for ($i = 1; $i < count($orders); $i++) : ?>
                            <?php $order = $orders[$i]; ?>
                            <tr>
                                <td><?php echo $order['id']; ?></td>
                                <td><?php echo isset($order['items']) ? $order['food_name'] . ', ' . $order['items'] : $order['food_name']; ?></td>
                                <td>GH₵ <?php echo number_format($order['price'], 2); ?></td>
                                <td><?php echo $order['quantity']; ?></td>
                                <td><?php echo $order['date'] . ', ' . $order['payment_method']; ?></td>
                            </tr>
                        <?php endfor; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <script src="./js/toggle.js"></script>
</body>

</html>
