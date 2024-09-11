<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$selectedDate = date('Y-m-d');
$totalEarnings = 0;
$totalExpenses = 0;

// If form is submitted, update the selected date and fetch data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedDate = $_POST['date'];

    // Fetch total revenue (earnings) for the selected date
    $paymentQuery = "SELECT SUM(price * quantity) as total_amount 
                     FROM orders 
                     WHERE DATE(date) = ?";
    $stmt = $conn->prepare($paymentQuery);
    $stmt->bind_param("s", $selectedDate);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $totalEarnings = $row['total_amount'] ?? 0;
    }
    $stmt->close();

    // Fetch total expenses for the selected date
    $sqlExpenses = "SELECT SUM(amount) as total_expenses FROM expenses WHERE date = ?";
    $stmtExpenses = $conn->prepare($sqlExpenses);
    $stmtExpenses->bind_param("s", $selectedDate);
    $stmtExpenses->execute();
    $resultExpenses = $stmtExpenses->get_result();
    $totalExpenses = ($resultExpenses->num_rows > 0) ? $resultExpenses->fetch_assoc()['total_expenses'] : 0;
    $stmtExpenses->close();
}

$conn->close();

// Calculate net revenue
$netRevenue = $totalEarnings - $totalExpenses;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Net Revenue</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/food.css">
    <link rel="stylesheet" href="./css/expenses.css">
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
            </div>
            <div class="profile"></div>
        </div>

        <h2>Net Revenue</h2>
        <form method="POST" action="">
            <div class="forms">
                <label for="date">Date</label>
                <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($selectedDate); ?>" required>
            </div>
            <div class="forms">
                <button type="submit">Query</button>
            </div>
        </form>

        <h3>Date: <?php echo htmlspecialchars($selectedDate); ?></h3>

        <h2>Net Revenue</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Total Revenue</th>
                    <th>Total Expenses</th>
                    <th>Net Revenue</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo htmlspecialchars($selectedDate); ?></td>
                    <td>GH₵ <?php echo number_format($totalEarnings, 2); ?></td>
                    <td>GH₵ <?php echo number_format($totalExpenses, 2); ?></td>
                    <td>GH₵ <?php echo number_format($netRevenue, 2); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
