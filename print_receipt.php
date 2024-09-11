<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Check if the order_id parameter is set in the URL
if (!isset($_GET['order_id'])) {
    echo "Order ID not specified.";
    exit();
}

$orderID = $_GET['order_id'];

// Fetch order details from the database based on the order ID
$sql = "SELECT * FROM orders WHERE order_id = $orderID";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Order not found.";
    exit();
}

$order = $result->fetch_assoc();

// Fetch all food items related to the order ID
$sql = "SELECT * FROM orders WHERE order_id = $orderID";
$result = $conn->query($sql);

$subtotal = 0;
$items = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $subtotal += $row['price'] * $row['quantity'];
        $items[] = $row;
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
    <title>Receipt</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/food.css">
    <style>
       
        .receipt {
            width: 600px;
            margin: 0 auto;
            border: 2px dashed #ccc;
            padding: 20px;
        }

        .receipt p {
            margin: 5px 0;
        }

        .logo_details {
            text-align: center;
        }

        .dash {
            padding: 10px;
            display: flex;
            text-align: center;
            align-items: center;
            border-top: 2px dashed #000;
            border-bottom: 2px dashed #000;
            margin-top: 20px;
            justify-content: center;
            margin-top: 20px;
        }

        .receipt_flex {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .powered {
            text-align: center;
            padding-top: 10px;
        }

        /* th {
            background-color: #000;
        } */

        table {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="receipt">
        <div class="logo"></div>
        <div class="logo_details">
            <h2>Los Barbados Lounge</h2>
            <p><strong>Location:</strong> KNUST Mango Road </p>
            <p><strong>Contact:</strong>  +233 57 615 7127</p>

        </div>
        <div class="dash">
            <h2>RECEIPT</h2>
        </div>

        <div class="receipt_flex">
    <p><strong>Cashier:</strong> <?php echo $cashierName; ?></p>
    <p><strong>Time:</strong> <?php echo date('h:i A'); ?></p> <!-- Display current time in 12-hour format with AM/PM -->
</div>


        <div class="receipt_flex">
            <p><strong>Payment Method:</strong> <?php echo $order['payment_method']; ?></p>
            <p><strong>Date:</strong> <?php echo $order['date']; ?></p>
        </div>

        <!-- <p><strong>Description:</strong></p> -->
        <table>
            <thead>
                <tr>
                    <th>Food Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item) : ?>
                    <tr>
                        <td><?php echo $item['food_name']; ?></td>
                        <td>GH₵ <?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>GH₵ <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3"><strong>Subtotal:</strong></td>
                    <td>GH₵ <?php echo number_format($subtotal, 2); ?></td>
                </tr>
                <tr>
                    <td colspan="3"><strong>Tax:</strong></td>
                    <td>GH₵ 0.00</td>
                </tr>
                <tr>
                    <td colspan="3"><strong>Total:</strong></td>
                    <td>GH₵ <?php echo number_format($order['total'], 2); ?></td>
                </tr>
            </tbody>
        </table>


        <div class="dash">
            <h2>Order ID: <?php echo $order['order_id']; ?></h2>
        </div>
        <p class="powered">Powered by Nathstack Tech | Tel: +233 541 9874 78</p>
    </div>
    <script>
        // Automatically trigger print dialog when the page is loaded
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>