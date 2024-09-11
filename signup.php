<?php
include 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the username already exists
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $error = "Username already exists";
    } else {
        // Check if the password is already used
        $sql = "SELECT * FROM users";
        $result = $conn->query($sql);

        $passwordExists = false;
        while ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $passwordExists = true;
                break;
            }
        }

        if ($passwordExists) {
            $error = "Password is already used by another user";
        } else {
            // Hash the password and insert the new user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, password) VALUES ('$username', '$hashedPassword')";

            if ($conn->query($sql) === TRUE) {
                header("Location: login.php");
                exit();
            } else {
                $error = "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <?php include 'cdn.php' ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/login.css">
</head>
<body>
    <div class="page_all">
        <div class="page_login">
            <div class="forms">
                <h2>Sign Up to your Account</h2>
                <p>You are welcome back, signup a new Cashier</p>
            </div>
            <form method="POST" action="">
                <?php if ($error): ?>
                    <div class="error">
                        <p><?php echo $error; ?></p>
                        <span class="close-error"><i class="fa-solid fa-xmark"></i></span>
                    </div>
                <?php endif; ?>
                <div class="forms">
                    <label>Username:</label>
                    <input type="text" placeholder="Enter your username" name="username" required>
                </div>
                <div class="forms">
                    <label>Password:</label>
                    <span class="toggle-password"><i class="fa-regular fa-eye-slash"></i></span>
                    <input type="password" placeholder="Enter your password" name="password" id="password" required>
                </div>
                <div class="forms">
                    <button type="submit">Sign Up</button>
                </div>
            </form>
            <div class="forms">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
        <div class="page_swiper">
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img src="./images/1.png" alt="">
                        <div class="swiper_text">
                            <p>Enhance customer service and boost efficiency with our user-friendly restaurant POS system</p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <img src="./images/2.png" alt="">
                        <div class="swiper_text">
                            <p>Simplify your order management and elevate dining experiences with our advanced POS solution.</p>
                        </div>
                    </div>
                </div>
                <div class="swipper_arrow">
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>
    <script src="./js/swiper.js"></script>
    <script>
        // Toggle password visibility
        document.querySelector('.toggle-password').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        });

        // Close error message
        document.querySelector('.close-error')?.addEventListener('click', function() {
            const errorDiv = this.parentElement;
            errorDiv.style.display = 'none';
        });
    </script>
</body>
</html>
