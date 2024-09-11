<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Helper function to get total orders for a given time period
function getTotalOrders($conn, $startDate, $endDate) {
    $query = "SELECT food_name, SUM(quantity) as total_orders 
              FROM orders 
              WHERE date BETWEEN ? AND ? 
              GROUP BY food_name";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    $stmt->close();
    return $data;
}

// Get the current date
$today = date('Y-m-d');
$thisWeekStart = date('Y-m-d', strtotime('monday this week'));
$thisMonthStart = date('Y-m-01');
$thisYearStart = date('Y-01-01');

// Get total orders for each time period
$ordersToday = getTotalOrders($conn, $today, $today);
$ordersThisWeek = getTotalOrders($conn, $thisWeekStart, $today);
$ordersThisMonth = getTotalOrders($conn, $thisMonthStart, $today);
$ordersThisYear = getTotalOrders($conn, $thisYearStart, $today);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Orders for Food Items</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/food.css">
    <link rel="stylesheet" href="./css/chart.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include 'sidebar.php'; ?>
    <div class="history_all">
    <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
    <p>This is the orders dashboard.</p>
       

       <div class="card_all">
       <div class="card">
            <h2>Today</h2>
            <canvas id="todayChart"></canvas>
        </div>

        <div class="card">
            <h2>This Week</h2>
            <canvas id="weekChart"></canvas>
        </div>

        <div class="card">
            <h2>This Month</h2>
            <canvas id="monthChart"></canvas>
        </div>

        <div class="card">
            <h2>This Year</h2>
            <canvas id="yearChart"></canvas>
        </div>
       </div>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function renderChart(chartId, data, labels, type) {
                var ctx = document.getElementById(chartId).getContext('2d');
                new Chart(ctx, {
                    type: type,
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Orders',
                            data: data,
                            backgroundColor: [
                                'rgb(255, 99, 132)',
                                'rgb(54, 162, 235)',
                                'rgb(25, 00, 86)',
                                'rgb(75, 192, 192)',
                                'rgb(153, 102, 255)',
                                'rgb(255, 159, 64)',
                                'rgb(255, 19, 54)',
                            ],
                           
                        }]
                    },
                    options: {
                        scales: type === 'bar' ? {
                            y: {
                                beginAtZero: true
                            }
                        } : {}
                    }
                });
            }

            var ordersToday = <?php echo json_encode($ordersToday); ?>;
            var ordersThisWeek = <?php echo json_encode($ordersThisWeek); ?>;
            var ordersThisMonth = <?php echo json_encode($ordersThisMonth); ?>;
            var ordersThisYear = <?php echo json_encode($ordersThisYear); ?>;

            var todayLabels = ordersToday.map(item => item.food_name);
            var todayData = ordersToday.map(item => item.total_orders);

            var weekLabels = ordersThisWeek.map(item => item.food_name);
            var weekData = ordersThisWeek.map(item => item.total_orders);

            var monthLabels = ordersThisMonth.map(item => item.food_name);
            var monthData = ordersThisMonth.map(item => item.total_orders);

            var yearLabels = ordersThisYear.map(item => item.food_name);
            var yearData = ordersThisYear.map(item => item.total_orders);

            renderChart('todayChart', todayData, todayLabels, 'doughnut');
            renderChart('weekChart', weekData, weekLabels, 'pie');
            renderChart('monthChart', monthData, monthLabels, 'bar');
            renderChart('yearChart', yearData, yearLabels, 'bar');
        });
    </script>
</body>
</html>
