<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect to unauthorized page or login page
    header("Location: unauthorized.php");
    exit();
}
include 'db.php';

$error = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Verify password and confirm password match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if the username already exists
        $check_sql = "SELECT * FROM users WHERE username = '$username'";
        $check_result = $conn->query($check_sql);
        if ($check_result->num_rows > 0) {
            $error = "Username already exists";
        } else {
            // Insert new cashier into the database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$hashed_password', 'cashier')";
            if ($conn->query($insert_sql) === TRUE) {
                $success_message = "Cashier registered successfully";
                // Redirect to login page
                header("Location: login.php");
                exit();
            } else {
                $error = "Error: " . $conn->error;
            }
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Cashier</title>
    <?php include 'cdn.php' ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/login.css">
    <style>
        .success {
            color: green;
            margin-top: 10px;
        }
    </style>
</head>

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
    <div class="all">
        <div class="page_login">
            <div class="forms">
                <h2>Register a New Cashier</h2>
            </div>
            <form method="POST" action="">
                <?php if ($error) : ?>
                    <div class="error">
                        <p><?php echo $error; ?></p>
                    </div>
                <?php endif; ?>
                <?php if ($success_message) : ?>
                    <div class="success">
                        <p><?php echo $success_message; ?></p>
                    </div>
                <?php endif; ?>
                <div class="forms">
                    <label>Username:</label>
                    <input type="text" placeholder="Enter username" name="username" required>
                </div>
                <div class="forms">
                    <label>Password:</label>
                    <input type="password" placeholder="Enter password" name="password" required>
                </div>
                <div class="forms">
                    <label>Confirm Password:</label>
                    <input type="password" placeholder="Confirm password" name="confirm_password" required>
                </div>
                <div class="forms">
                    <button type="submit">Register Cashier</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
