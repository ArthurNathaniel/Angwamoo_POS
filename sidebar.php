<style>
    .side-logo {
        text-align: center;
    }
    .logos {
    height: 150px;
    width: 150px;
    margin-block: 30px;
    margin-left: auto;
    margin-right: auto;
    background-image: url(../images/logo.png);
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
    border-radius: 50%;
    box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
}
    </style>
</style>
<div class="sidebar_all">
    <div class="logos">

    </div>
    <br>
    <br>
    <div class="links">
        <h3> <span class="icon"><i class="fa-solid fa-chart-simple"></i></span> Cashier</h3>
        <a href="order.php">Order Food</a>
        <a href="order_history.php">Order History</a>
        <a href="chart.php">Order Chart</a>

        <a href="daily_earnings.php">Daily Earning (Food)</a>
        <a href="daily_payment.php">Daily Payment Method</a>
        <a href="net_revenue.php">Daily Net Revenue</a>

        <?php if ($_SESSION['user_role'] === 'admin') : ?>
            <h3> <span class="icon"><i class="fa-solid fa-chart-simple"></i></span> Admin</h3>
            <a href="add_food.php">Add Food</a>
            <a href="view_food.php">View Added Food</a>
            <a href="register_cashier.php">Register Cashier</a>
            <a href="add_expense.php">Add Expenses</a>
            <a href="expenses_history.php">Expenses History</a>
            <a href="monthly_earnings.php">Monthly Earning(Food)</a>
            <a href="yearly_earnings.php">Yearly Earning (Food)</a>
            <a href="monthly_earnings.php">Monthly Payment Method</a>
            <a href="yearly_payment.php">Yearly Payment Method</a>
            <a href="net_revenue_monthly.php">Monthly Net Revenue</a>
            <a href="yearly_net_revenue.php">Yearly Net Revenue</a>
        <?php endif; ?>

        <a href="logout.php">
                <h3> <i class="fas fa-sign-out-alt"></i> LOGOUT
                </h3>
            </a>
    </div>
    <style>
        h3 a {
            background-color: transparent;
        }
    </style>
</div>
<button id="toggleButton">
    <i class="fa-solid fa-bars-staggered"></i>
</button>

<script>
    // Get the button and sidebar elements
    var toggleButton = document.getElementById("toggleButton");
    var sidebar = document.querySelector(".sidebar_all");
    var icon = toggleButton.querySelector("i");

    // Add click event listener to the button
    toggleButton.addEventListener("click", function() {
        // Toggle the visibility of the sidebar
        if (sidebar.style.display === "none" || sidebar.style.display === "") {
            sidebar.style.display = "block";
            icon.classList.remove("fa-bars-staggered");
            icon.classList.add("fa-xmark");
        } else {
            sidebar.style.display = "none";
            icon.classList.remove("fa-xmark");
            icon.classList.add("fa-bars-staggered");
        }
    });
</script>