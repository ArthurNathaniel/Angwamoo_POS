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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $date = htmlspecialchars($_POST['date']);
    $description = htmlspecialchars($_POST['description']);
    $amount = floatval($_POST['amount']); // Ensuring amount is a float

    $sql = "INSERT INTO expenses (name, date, description, amount) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssd", $name, $date, $description, $amount);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Expense added successfully!";
    } else {
        $_SESSION['error_message'] = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    header("Location: expenses_history.php");

    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/food.css">
    <link rel="stylesheet" href="./css/expenses.css">

    <script>
        function changeButtonText() {
            var button = document.getElementById("submit-button");
            button.textContent = "Please Wait...";
            button.disabled = true;
        }

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
    <div class="expense-form">
        <div class="welcome_base">
            <div class="greetings">
                <h1 id="greeting"></h1>
                <!-- <p>Welcome to Olu's Kitchen, </p> -->
            </div>
            <div class="profile"></div>
        </div>
        <h2>Add Expense</h2>
        <?php if (isset($_SESSION['success_message'])): ?>
            <script>
                alert('<?php echo $_SESSION['success_message']; ?>');
                <?php unset($_SESSION['success_message']); ?>
            </script>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_message'])): ?>
            <script>
                alert('<?php echo $_SESSION['error_message']; ?>');
                <?php unset($_SESSION['error_message']); ?>
            </script>
        <?php endif; ?>
        <form method="POST" action="add_expense.php" onsubmit="changeButtonText()">
            <div class="forms">
                <label for="name">Name</label>
                <input type="text" placeholder="Enetr your name" id="name" name="name" required>
            </div>

            <div class="forms">
                <label for="date">Date</label>
                <input type="text" id="date" placeholder="Pick a date"  name="date" required>
            </div>

            <div class="forms">
                <label for="description">Description</label>
                <textarea id="description" placeholder="Enter your description" name="description" rows="4" required></textarea>
            </div>

            <div class="forms">
                <label for="amount">Amount</label>
                <input type="number" min="0" placeholder="Enter the amount" id="amount" name="amount" step="0.01" required>
            </div>

            <div class="forms">
                <button type="submit" id="submit-button">Add Expense</button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
            // Initialize Flatpickr for the date input field
            flatpickr("#date", {
            dateFormat: "Y-m-d",
            minDate: "today",
            maxDate: "today",
            disableMobile: true // Enable Flatpickr on mobile devices
        });
    </script>
</body>
</html>
