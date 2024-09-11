<?php
session_start();

// Redirect to unauthorized page if user is not an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: unauthorized.php");
    exit();
}

include 'db.php';

$error = '';
$success = '';

// Handle deletion of food item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    // Delete the food item from the database
    $delete_sql = "DELETE FROM foods WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $delete_id);

    if ($delete_stmt->execute()) {
        $success = "Food item deleted successfully!";
    } else {
        $error = "Error deleting food item: " . $conn->error;
    }

    $delete_stmt->close();
}

// Fetch all food items from the database
$sql = "SELECT id, name, image FROM foods";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Food Items</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/food.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px dashed black;
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        img {
            width: 100px;
            height: 100px;
            object-fit: contain;
        }
        form button {
            width: 100%;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="all">
        <div class="page_login">
            <div class="forms">
                <h2>Food Items</h2>
                <p>List of all food items</p>
            </div>
            <?php if (!empty($error)): ?>
                <div class="forms error">
                    <p><?php echo $error; ?></p>
                    <span class="close-error"><i class="fa-solid fa-xmark"></i></span>
                </div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="forms success">
                    <p><?php echo $success; ?></p>
                    <span class="close-success"><i class="fa-solid fa-xmark"></i></span>
                </div>
            <?php endif; ?>
            <div class="forms">
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row["id"]; ?></td>
                                <td><?php echo $row["name"]; ?></td>
                                <td><img src="<?php echo $row["image"]; ?>" alt="<?php echo $row["name"]; ?>"></td>
                                <td>
                                    <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                        <input type="hidden" name="delete_id" value="<?php echo $row["id"]; ?>">
                                        <button type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4">No food items found</td></tr>
                    <?php endif; ?>
                </table>
            </div>
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
