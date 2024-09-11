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
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_FILES['image'];

    // Validate inputs
    if (empty($name) || empty($price) || empty($image['name'])) {
        $error = 'All fields are required';
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = 'Please enter a valid price';
    } else {
        // Handle file upload
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($image["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if image file is an actual image or fake image
        $check = getimagesize($image["tmp_name"]);
        if ($check === false) {
            $error = "File is not an image.";
        }

        // Check if file already exists
        if (file_exists($targetFile)) {
            $error = "Sorry, file already exists.";
        }

        // Check file size (5MB limit)
        if ($image["size"] > 5000000) {
            $error = "Sorry, your file is too large.";
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }

        // Check if $error is empty to proceed with upload
        if (empty($error)) {
            if (move_uploaded_file($image["tmp_name"], $targetFile)) {
                // Insert food item into database
                $sql = "INSERT INTO foods (name, image, price) VALUES ('$name', '$targetFile', '$price')";
                if ($conn->query($sql) === TRUE) {
                    $success = "Food item added successfully!";
                } else {
                    $error = "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                $error = "Sorry, there was an error uploading your file.";
            }
        }
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Add Food Item</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/login.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
    <div class="all">
        <div class="page_login">
            <div class="forms">
                <h2>Add a New Food Item</h2>
                <p>Enter details of the new food item</p>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <?php if ($error): ?>
                    <div class="forms error">
                        <p><?php echo $error; ?></p>
                        <span class="close-error"><i class="fa-solid fa-xmark"></i></span>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="forms success">
                        <p><?php echo $success; ?></p>
                        <span class="close-success"><i class="fa-solid fa-xmark"></i></span>
                    </div>
                <?php endif; ?>
                <div class="forms">
                    <label>Food Name:</label>
                    <input type="text" placeholder="Enter food name" name="name" required>
                </div>
                <div class="forms">
                    <label>Food Price:</label>
                    <input type="text" placeholder="Enter food price" name="price" required>
                </div>
                <div class="forms">
                    <label>Food Image:</label>
                    <input type="file" name="image" required>
                </div>
                <div class="forms">
                    <button type="submit">Add Food</button>
                </div>
            </form>
        </div>
    </div>
    <script src="./js/swiper.js"></script>
    <script>
        // Close error message
        document.querySelectorAll('.close-error').forEach(el => {
            el.addEventListener('click', function() {
                const errorDiv = this.parentElement;
                errorDiv.style.display = 'none';
            });
        });

        // Close success message
        document.querySelectorAll('.close-success').forEach(el => {
            el.addEventListener('click', function() {
                const successDiv = this.parentElement;
                successDiv.style.display = 'none';
            });
        });
    </script>
</body>
</html>
