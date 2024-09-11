<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Initialize total earnings variable
$totalEarnings = 0;
$earnings = [];
$errorMessage = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the selected date from the form and sanitize it
    $selectedDate = $conn->real_escape_string($_POST['selected_date']);
    $selectedDateInWords = date("F j, Y", strtotime($selectedDate)); // Convert to words

    // Prepare SQL query to fetch earnings for each food item on the selected date
    $sql = "SELECT food_name, SUM(price * quantity) AS total_earnings 
            FROM orders 
            WHERE DATE(date) = '$selectedDate'
            GROUP BY food_name";
    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            // Fetch the results into an associative array
            while ($row = $result->fetch_assoc()) {
                $earnings[] = $row;
                $totalEarnings += $row['total_earnings']; // Increment total earnings
            }
        } else {
            // If no results found, display a message
            $errorMessage = "No earnings found for $selectedDateInWords.";
        }
    } else {
        $errorMessage = "Error: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Earnings</title>
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
        <h1>Daily Earnings</h1>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="forms">
                <label for="selected_date">Select Date:</label>
                <input type="date" id="selected_date" name="selected_date" required>
            </div>
            <div class="forms">
                <button type="submit">Submit</button>
            </div>
        </form>
        <?php if (!empty($errorMessage)) : ?>
            <p><?php echo $errorMessage; ?></p>
        <?php elseif (!empty($earnings)) : ?>
            <h2>Date: <?php echo $selectedDateInWords; ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>Food Name</th>
                        <th>Total Earnings</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($earnings as $earning) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($earning['food_name']); ?></td>
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
