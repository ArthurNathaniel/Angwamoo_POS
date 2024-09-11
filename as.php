<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Retrieve orders history from the database
$sql = "SELECT * FROM orders WHERE cashier_name = '{$_SESSION['username']}' ORDER BY date DESC";
$result = $conn->query($sql);

// Function to format currency
function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Orders History</title>
    <!-- Include any CSS styles for the table here -->
    <style>
        /* CSS styles for the table */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .receipt-link {
            text-decoration: none;
            color: blue;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Orders History</h1>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Cashier</th>
                <th>Total Amount</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['order_id']}</td>";
                    echo "<td>{$row['date']}</td>";
                    echo "<td>{$_SESSION['username']}</td>";
                    echo "<td>" . formatCurrency($row['total']) . "</td>";
                    echo "<td><a href='print_receipt.php?order_id={$row['order_id']}' class='receipt-link'>Print Receipt</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No orders found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
