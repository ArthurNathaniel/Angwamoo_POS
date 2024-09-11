<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect to unauthorized page or login page
    header("Location: unauthorized.php");
    exit();
}
include 'db.php';

$year = '';
$paymentTotals = [];
$totalEarnings = 0;

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $year = $_POST['year'];

    // Fetch total money gained for each payment method in the selected year
    $paymentQuery = "SELECT payment_method, SUM(price * quantity) as total_amount 
                     FROM orders 
                     WHERE YEAR(date) = ? 
                     GROUP BY payment_method";
    $stmt = $conn->prepare($paymentQuery);
    $stmt->bind_param("s", $year);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $paymentTotals[$row['payment_method']] = $row['total_amount'];
            $totalEarnings += $row['total_amount'];
        }
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Money Gained by Payment Method</title>
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

            var cashierName = "<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>";
            document.getElementById("greeting").innerHTML = greeting + ", " + cashierName;
        }
    </script>
</head>

<body onload="greetUser()">
    <?php include 'sidebar.php'; ?>
    <div class="history_all">
        <div class="welcome_base">
            <div class="greetings">
                <h1 id="greeting"></h1>
                <!-- <p>Welcome to Olu's Kitchen, </p> -->
            </div>
            <div class="profile"></div>
        </div>
        <div class="forms">
            <h1>Total Money Gained by Payment Method</h1>
            <!-- Form to select a year -->
            <form method="POST" action="">
                <div class="forms">
                    <label for="year">Select Year:</label>
                    <select id="year" name="year" required>
                        <?php
                            $currentYear = date('Y');
                            for ($i = 2024; $i <= 2090; $i++) {
                                echo "<option value='$i'" . ($i == $year ? " selected" : "") . ">$i</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="forms">
                    <button type="submit">Query</button>
                </div>
            </form>
        </div>

        <!-- Display results if any -->
        <?php if (!empty($paymentTotals)) : ?>
            <h2>Results for <?php echo htmlspecialchars($year); ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>Payment Method</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($paymentTotals as $method => $total) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($method); ?></td>
                            <td>GH₵ <?php echo number_format($total, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td><strong>Total Earnings</strong></td>
                        <td><strong>GH₵ <?php echo number_format($totalEarnings, 2); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST") : ?>
            <p>No records found for the selected year.</p>
        <?php endif; ?>
    </div>
</body>
</html>
