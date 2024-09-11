<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$cashierName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown'; // Get the cashier's name from the session

include 'db.php';

// Fetch food items from the database
$sql = "SELECT * FROM foods";
$result = $conn->query($sql);

$foods = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $foods[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Food</title>
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

            var cashierName = "<?php echo $cashierName; ?>";
            document.getElementById("greeting").innerHTML = greeting + ", " + cashierName;
        }
    </script>

</head>

<body onload="greetUser()">
    <?php include 'sidebar.php'; ?>
    <div class="page_all">
        <div class="welcome_base">
            <div class="profile">
               
            </div>
            <div class="greetings">
                <h1 id="greeting"><?php echo $cashierName; ?></h1>
            </div>
             <button id="darkModeToggle">
                    <i id="darkModeIcon" class="fas fa-moon"></i>
                </button>
        </div>
        <div class="page_cards">
            <!-- Add search input field -->
            <div class="search-bar">
                <input type="text" id="search-input" placeholder="Search food...">
            </div>
            <div class="swiper-container">
                <div class="swiper mySwiper2">
                    <div class="swiper-wrapper" id="food-items">
                        <?php if (empty($foods)) : ?>
                            <p>No food items available.</p>
                        <?php else : ?>
                            <?php foreach ($foods as $food) : ?>
                                <div class="swiper-slide">
                                    <div class="card">
                                        <img src="<?php echo $food['image']; ?>" alt="<?php echo $food['name']; ?>">
                                        <div class="card_info">
                                            <h4><?php echo $food['name']; ?></h4>
                                            <p>GH₵<?php echo number_format($food['price'], 2); ?></p>
                                            <button class="add-to-order" data-id="<?php echo $food['id']; ?>" data-name="<?php echo $food['name']; ?>" data-price="<?php echo $food['price']; ?>">
                                                <i class="fa-solid fa-cart-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="arrows">
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                </div>
            </div>
            <div class="order-section">
                <div class="forms">
                    <h2>Order Details</h2>
                </div>
                <div class="forms">
                    <p>Cashier: <br>
                    <div class="circle"><?php echo $cashierName; ?></div>
                    </p>
                </div>
                <div class="forms">
                    <label for="order-date">Date:</label>
                    <input type="text" placeholder="Pick a date" id="order-date" required> <!-- Input for cashier to enter date -->
                </div>
                <table id="order-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Food Name</th>
                            <th>Enter Price</th> <!-- New column for entering price -->
                            <th>Quantity</th> <!-- New column for entering quantity -->
                            <th>Action</th> <!-- Added new column for the remove button -->
                        </tr>
                    </thead>
                    <tbody id="order-items">
                        <!-- Dynamically populated with JavaScript -->
                    </tbody>
                </table>
                <br><br>
                <div class="forms">
                    <label for="payment-method">Select Payment Method:</label>
                    <select id="payment-method" required>
                        <option value="" selected hidden>Select the payment method</option>
                        <option value="cash">Cash</option>
                        <option value="momo">Mobile Money</option>
                    </select>
                </div>

                <div class="forms">
                    <p class="subtotal">Subtotal: GH₵<span id="subtotal">0.00</span></p>
                </div>
                <div class="forms">
                    <p class="total">Total: GH₵<span id="total">0.00</span></p>
                </div>

                <div class="forms" id="cash-payment-section" style="display: none;">
                    <label for="amount-given">Amount Given:</label>
                    <input type="number" id="amount-given" placeholder="Enter amount given by client">
                </div>

                <div class="forms" id="balance-section" style="display: none;">
                    <p class="balance">Balance: GH₵<span id="balance">0.00</span></p>
                </div>

                <div class="forms">
                    <button id="checkout">Checkout</button>
                </div>
            </div>
        </div>
    </div>
    <script src="./js/toggle.js"></script>
    <script src="./js/swiper.js"></script>
    <script src="./js/order.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#order-date", {
            dateFormat: "Y-m-d",
            minDate: "today",
            maxDate: "today",
            disableMobile: true
        });

        // JavaScript to filter the food items based on the search input
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const foodItemsContainer = document.getElementById('food-items');
            const swiperSlides = foodItemsContainer.getElementsByClassName('swiper-slide');

            // Create a "No results found" message
            const noResultsMessage = document.createElement('div');
            noResultsMessage.id = 'no-results-message';
            noResultsMessage.textContent = 'No results found';
            noResultsMessage.style.display = 'none';
            noResultsMessage.style.textAlign = 'center';
            noResultsMessage.style.fontSize = '1em';
            noResultsMessage.style.marginTop = '20px';
            foodItemsContainer.appendChild(noResultsMessage);

            searchInput.addEventListener('keyup', function() {
                const filter = searchInput.value.toLowerCase();
                let hasResults = false;

                Array.from(swiperSlides).forEach(slide => {
                    const foodName = slide.querySelector('.card_info h4').textContent.toLowerCase();
                    if (foodName.includes(filter)) {
                        slide.style.display = '';
                        hasResults = true;
                    } else {
                        slide.style.display = 'none';
                    }
                });

                // Show or hide the "No results found" message
                noResultsMessage.style.display = hasResults ? 'none' : 'block';
            });

           
        });
    </script>
</body>


</html>