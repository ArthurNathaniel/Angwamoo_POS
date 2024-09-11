<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect to unauthorized page or login page
    header("Location: unauthorized.php");
    exit();
}

include 'db.php';

// Initialize total earnings variable
$totalEarnings = 0;

// Set the default selected year to the current year
$selectedYear = date('Y');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the selected year from the form
    $selectedYear = $_POST['selected_year'];

    // Prepare the start and end dates for the selected year
    $startDate = date('Y-m-d', strtotime("first day of January $selectedYear"));
    $endDate = date('Y-m-d', strtotime("last day of December $selectedYear"));

    // Prepare SQL query to fetch earnings for each food item in the selected year
    $sql = "SELECT MONTH(date) AS month, SUM(price * quantity) AS total_earnings 
            FROM orders 
            WHERE YEAR(date) = '$selectedYear'
            GROUP BY month";
    $result = $conn->query($sql);

    $earnings = [];
    if ($result->num_rows > 0) {
        // Fetch the results into an associative array
        while ($row = $result->fetch_assoc()) {
            $earnings[] = $row;
            $totalEarnings += $row['total_earnings']; // Increment total earnings
        }
    } else {
        // If no results found, display a message
        $errorMessage = "No earnings found for $selectedYear.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yearly Earnings</title>
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
    <div class="all">
        <div class="welcome_base">
            <div class="greetings">
                <h1 id="greeting"></h1>
                <!-- <p>Welcome to Olu's Kitchen, </p> -->
            </div>
            <div class="profile"></div>
        </div>
        <h1>Yearly Earnings</h1>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="forms">
                <label for="selected_year">Select Year:</label>
                <select id="selected_year" name="selected_year" required>
                    <?php for ($year = 2024; $year <= 2094; $year++) : ?>
                        <option value="<?php echo $year; ?>" <?php if ($year == $selectedYear) echo 'selected'; ?>><?php echo $year; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
           <div class="forms">
           <button type="submit">Submit</button>
           </div>
        </form>

        <?php if (isset($errorMessage)) : ?>
            <p><?php echo $errorMessage; ?></p>
        <?php elseif (isset($earnings)) : ?>
            <h2>Year: <?php echo $selectedYear; ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Total Earnings</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($earnings as $earning) : ?>
                        <tr>
                            <td><?php echo date('F', mktime(0, 0, 0, $earning['month'], 1)); ?></td>
                            <td>GH₵ <?php echo number_format($earning['total_earnings'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td><strong>Total Earnings</strong></td>
                        <td><strong>GH₵ <?php echo number_format($totalEarnings, 2); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
