<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Fetch only ready orders from the database
$sql = "SELECT * FROM orders WHERE status='ready' ORDER BY id DESC";
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
    <title>Ready Orders</title>
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
            <div class="greetings">
                <h1 id="greeting"> <?php echo $cashierName; ?></h1>
                <p>Welcome to Olu's Kitchen, </p>
            </div>
            <div class="profile"></div>
        </div>
        <h1>Ready Orders</h1>
        <table id="order-table"> <!-- Added ID to the table -->
            <thead>
                <tr>
                    <th>ID</th> <!-- Added ID column -->
                    <th>Order ID</th>
                    <th>Food Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Date, Payment Method</th> <!-- Combined as one -->
                    <th>Total</th>
                    <th>Action</th> <!-- Added Action column -->
                </tr>
            </thead>
            <tbody id="order-body"> <!-- Added ID to the tbody -->
                <?php foreach ($groupedOrders as $orderID => $orders) : ?>
                    <?php $totalPrice = 0; ?>
                    <?php foreach ($orders as $index => $order) : ?>
                        <?php $totalPrice += $order['price'] * $order['quantity']; ?>
                    <?php endforeach; ?>
                    <?php $firstOrder = reset($orders); ?>
                    <tr>
                        <td><?php echo $firstOrder['id']; ?></td> <!-- Display the ID of the first order -->
                        <td rowspan="<?php echo count($orders); ?>"><?php echo $orderID; ?></td>
                        <td><?php echo isset($firstOrder['items']) ? $firstOrder['food_name'] . ', ' . $firstOrder['items'] : $firstOrder['food_name']; ?></td>
                        <td>$<?php echo number_format($firstOrder['price'], 2); ?></td>
                        <td><?php echo $firstOrder['quantity']; ?></td>
                        <td><?php echo $firstOrder['date'] . ', ' . $firstOrder['payment_method']; ?></td>
                        <td rowspan="<?php echo count($orders); ?>">$<?php echo number_format($totalPrice, 2); ?></td>
                        <td rowspan="<?php echo count($orders); ?>">
                            <form method="post" action="clear_order.php">
                                <input type="hidden" name="order_id" value="<?php echo $orderID; ?>">
                                <button type="submit">Clear Order</button>
                            </form>
                        </td>
                    </tr>
                    <?php for ($i = 1; $i < count($orders); $i++) : ?>
                        <?php $order = $orders[$i]; ?>
                        <tr>
                            <td><?php echo $order['id']; ?></td> <!-- Display the ID for subsequent orders -->
                            <td><?php echo isset($order['items']) ? $order['food_name'] . ', ' . $order['items'] : $order['food_name']; ?></td>
                            <td>$<?php echo number_format($order['price'], 2); ?></td>
                            <td><?php echo $order['quantity']; ?></td>
                            <td><?php echo $order['date'] . ', ' . $order['payment_method']; ?></td>
                        </tr>
                    <?php endfor; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
