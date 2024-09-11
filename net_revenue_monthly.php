<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect to unauthorized page or login page
    header("Location: unauthorized.php");
    exit();
}

include 'db.php';

// Default to the current year
$selectedYear = date('Y');

// If form is submitted, update the selected year
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedYear = $_POST['year'];
}

// Initialize arrays to store data
$months = [
    'January', 'February', 'March', 'April', 'May', 'June', 
    'July', 'August', 'September', 'October', 'November', 'December'
];
$revenueData = array_fill(0, 12, 0);
$expenseData = array_fill(0, 12, 0);
$netRevenueData = array_fill(0, 12, 0);

// Fetch total revenue, expenses, and calculate net revenue for each month of the selected year
for ($month = 1; $month <= 12; $month++) {
    $startDate = "$selectedYear-$month-01";
    $endDate = date("Y-m-t", strtotime($startDate)); // Get last day of the month

    // Fetch total revenue for the month
    $sqlRevenue = "SELECT SUM(price * quantity) as total_amount 
                   FROM orders 
                   WHERE DATE(date) BETWEEN '$startDate' AND '$endDate'";
    $resultRevenue = $conn->query($sqlRevenue);
    $totalRevenue = ($resultRevenue->num_rows > 0) ? $resultRevenue->fetch_assoc()['total_amount'] : 0;
    $revenueData[$month - 1] = $totalRevenue;

    // Fetch total expenses for the month
    $sqlExpenses = "SELECT SUM(amount) as total_expenses 
                    FROM expenses 
                    WHERE DATE(date) BETWEEN '$startDate' AND '$endDate'";
    $resultExpenses = $conn->query($sqlExpenses);
    $totalExpenses = ($resultExpenses->num_rows > 0) ? $resultExpenses->fetch_assoc()['total_expenses'] : 0;
    $expenseData[$month - 1] = $totalExpenses;

    // Calculate net revenue for the month
    $netRevenueData[$month - 1] = $totalRevenue - $totalExpenses;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Net Revenue by Month</title>
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
    <div class="all">
        <div class="welcome_base">
            <div class="greetings">
                <h1 id="greeting"></h1>
            </div>
            <div class="profile"></div>
        </div>
        <h2>Net Revenue by Month</h2>
        <form method="POST" action="">
            <div class="forms">
                <label for="year">Year</label>
                <select id="year" name="year" required>
                    <?php
                    for ($year = 2024; $year <= 2094; $year++) {
                        $selected = ($year == $selectedYear) ? "selected" : "";
                        echo "<option value='$year' $selected>$year</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="forms">
                <button type="submit">Query</button>
            </div>
        </form>
        <h3>Year: <?php echo $selectedYear; ?></h3>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Total Revenue</th>
                    <th>Total Expenses</th>
                    <th>Net Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($months as $index => $month) : ?>
                    <tr>
                        <td><?php echo $month; ?></td>
                        <td>GH₵ <?php echo number_format($revenueData[$index], 2); ?></td>
                        <td>GH₵ <?php echo number_format($expenseData[$index], 2); ?></td>
                        <td>GH₵ <?php echo number_format($netRevenueData[$index], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
