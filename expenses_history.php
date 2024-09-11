<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect to unauthorized page or login page
    header("Location: unauthorized.php");
    exit();
}

// Assuming $cashierName is set in session or should be retrieved from database
$cashierName = $_SESSION['username']; // or fetch from database if stored there

include 'db.php';

// Fetch expenses from the database
$sql = "SELECT * FROM expenses ORDER BY date DESC";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses History</title>
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

            var cashierName = "<?php echo $cashierName; ?>";
            document.getElementById("greeting").innerHTML = greeting + ", " + cashierName;
        }
    </script>

</head>

<body onload="greetUser()">
    <?php include 'sidebar.php'; ?>
    <div class="all">
        <div class="welcome_base">
            <div class="greetings">
                <h1 id="greeting"> <?php echo $cashierName; ?></h1>
                <!-- <p>Welcome to Olu's Kitchen, </p> -->
            </div>
            <div class="profile"></div>
        </div>
        <h2>Expenses History</h2>
        <?php
        if (isset($_SESSION['success_message'])) {
            echo "<p class='success-message'>" . $_SESSION['success_message'] . "</p>";
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['error_message'])) {
            echo "<p class='error-message'>" . $_SESSION['error_message'] . "</p>";
            unset($_SESSION['error_message']);
        }
        ?>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0) : ?>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td>GH₵<?php echo number_format($row['amount'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="4">No expenses found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
