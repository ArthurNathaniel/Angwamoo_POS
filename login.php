<?php
include 'db.php';

session_start();

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['user_role'] = $row['role']; // Store the user role in the session
            header("Location: chart.php");
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "No user found with that username";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <?php include 'cdn.php' ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/login.css">
    <style>
        .logos {
            height: 150px;
            width: 150px;
            margin-block: 30px;
            box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
            background-image: url(../images/logo.png);
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            border-radius: 50%;
        }
    </style>
</head>

<body>
    <div class="page_all">
        <div class="page_login">
            <div class="logos"></div>
            <div class="forms">
                <h2>Log in to your Account</h2>
                <p>You are welcome back, login as a Cashier</p>
            </div>
            <form method="POST" action="">
                <?php if ($error) : ?>
                    <div class="error">
                        <p><?php echo $error; ?></p>
                        <p class="close-error"><i class="fa-solid fa-xmark"></i></p>
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
                    <button type="submit">Login</button>
                </div>
            </form>
            <!-- <div class="forms">
                <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
            </div> -->
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