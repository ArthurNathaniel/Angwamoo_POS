<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <style>
        .all {
            text-align: center;
            margin-top: 100px;
        }

        .all h1 {
            font-size: 2.5em;
            color: #ff0000;
        }

        .all p {
            font-size: 1.2em;
        }

        .all a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        .all a:hover {
            background-color: #0056b3;
        }
    </style><!-- Add your custom error styles here -->
</head>

<body>
    <div class="all">
        <h1>Access Denied</h1>
        <p>Sorry, you have entered an incorrect password and are not authorized to access this page.</p>
        <a href="login.php">Return to Login</a> <!-- Link back to login or homepage -->
    </div>
</body>

</html>