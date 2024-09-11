<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <?php include 'cdn.php' ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/login.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
   <div class="all">
   <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
    <p>This is the cashier's dashboard.</p>
    <a href="logout.php">Logout</a>
   </div>
</body>
</html>
