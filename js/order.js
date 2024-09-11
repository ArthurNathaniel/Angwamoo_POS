document.addEventListener('DOMContentLoaded', function() {
    const addToOrderButtons = document.querySelectorAll('.add-to-order');
    const orderItemsContainer = document.getElementById('order-items');
    const paymentMethodSelect = document.getElementById('payment-method');
    const subtotalElement = document.getElementById('subtotal');
    const totalElement = document.getElementById('total');
    const amountGivenInput = document.getElementById('amount-given');
    const balanceElement = document.getElementById('balance');
    const cashPaymentSection = document.getElementById('cash-payment-section');
    const balanceSection = document.getElementById('balance-section');
    const orderDateInput = document.getElementById('order-date');
    let subtotal = 0;
    let orderId = null;

    addToOrderButtons.forEach(button => {
        button.addEventListener('click', function() {
            const foodId = this.dataset.id;
            const foodName = this.dataset.name;
            const foodPrice = parseFloat(this.dataset.price);

            // Prompt the cashier to enter the quantity
            let foodQuantity = prompt(`Enter the quantity for ${foodName}:`, 1);
            foodQuantity = parseInt(foodQuantity);

            // Ensure quantity is a valid number
            if (isNaN(foodQuantity) || foodQuantity <= 0) {
                alert('Please enter a valid quantity.');
                return;
            }

            if (!orderId) {
                orderId = generateOrderId(); // Generate Order ID if not already generated
            }

            // Add the selected food item to the order table
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>${orderId}</td>
                <td>${foodName}</td>
                <td><input type="number" class="price-input" value="${foodPrice}" required></td>
                <td><input type="number" class="quantity-input" value="${foodQuantity}" required></td>
                <td><button class="remove-item">Remove</button></td> <!-- Add remove button -->
            `;
            orderItemsContainer.appendChild(newRow);

            // Update subtotal
            updateSubtotal();
        });
    });

    // Listen for remove button clicks
    orderItemsContainer.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-item')) {
            const row = event.target.closest('tr');

            // Remove the row from the table
            row.remove();

            // Update subtotal and total
            updateSubtotal();
        }
    });

    // Listen for changes in the price or quantity input fields
    orderItemsContainer.addEventListener('input', function(event) {
        if (event.target.classList.contains('price-input') || event.target.classList.contains('quantity-input')) {
            updateSubtotal();
        }
    });

    // Listen for changes in the amount given field
    amountGivenInput.addEventListener('input', function() {
        updateBalance();
    });

    // Function to update subtotal and total
    function updateSubtotal() {
        subtotal = 0;
        const rows = orderItemsContainer.querySelectorAll('tr');
        rows.forEach(row => {
            const price = parseFloat(row.querySelector('.price-input').value);
            const quantity = parseFloat(row.querySelector('.quantity-input').value);
            subtotal += price * quantity;
        });
        subtotalElement.textContent = subtotal.toFixed(2);

        // Update total
        updateTotal();
    }

    // Function to update the total
    function updateTotal() {
        const total = subtotal;
        totalElement.textContent = total.toFixed(2);

        // Update balance
        updateBalance();
    }

    // Function to update the balance
    function updateBalance() {
        const amountGiven = parseFloat(amountGivenInput.value) || 0;
        const total = parseFloat(totalElement.textContent);
        const balance = amountGiven - total;
        balanceElement.textContent = balance.toFixed(2);
    }

    // Function to generate a unique order ID consisting of numbers only
    function generateOrderId() {
        return Math.floor(Math.random() * 10000); // Generate a random 6-digit number
    }

    // Listen for changes in the payment method
    paymentMethodSelect.addEventListener('change', function() {
        const selectedPaymentMethod = this.value;
        if (selectedPaymentMethod === 'cash') {
            cashPaymentSection.style.display = 'block';
            balanceSection.style.display = 'block';
        } else {
            cashPaymentSection.style.display = 'none';
            balanceSection.style.display = 'none';
        }
        updateTotal();
    });

    // Checkout button event listener
    document.getElementById('checkout').addEventListener('click', function() {
        if (!validatePaymentMethod()) {
            return; // Stop further execution if payment method is not selected
        }

        const selectedPaymentMethod = paymentMethodSelect.value;
        const total = parseFloat(totalElement.textContent);
        const orderDate = orderDateInput.value;
        const rows = orderItemsContainer.querySelectorAll('tr'); // Get all rows

        if (!orderDate) {
            alert('Please enter the order date.');
            return;
        }

        if (rows.length === 0) { // Check if there are any food items in the order
            alert('Please add at least one food item to the order before checking out.');
            return;
        }

        // Change checkout button text to "Please wait"
        const checkoutButton = this;
        checkoutButton.textContent = 'Please wait...';
        checkoutButton.disabled = true; // Disable the button to prevent multiple clicks

        // Prepare order data
        const orderItems = [];
        rows.forEach(row => {
            const orderId = parseInt(row.querySelector('td').textContent); // Parse orderId to integer
            const foodName = row.querySelectorAll('td')[1].textContent;
            const price = parseFloat(row.querySelectorAll('td input')[0].value);
            const quantity = parseFloat(row.querySelectorAll('td input')[1].value);
            orderItems.push({
                orderId,
                foodName,
                price,
                quantity
            });
        });

        // Send order data to the server using AJAX
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'process_order.php');
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Server response handling
                alert(xhr.responseText);
                window.location.href = 'order_history.php'; // Redirect to home.php
            } else {
                alert('Error processing order');
                checkoutButton.textContent = 'Checkout'; // Revert button text on error
                checkoutButton.disabled = false; // Re-enable the button
            }
        };
        xhr.send(JSON.stringify({
            cashierName: "<?php echo $cashierName; ?>",
            orderDate,
            selectedPaymentMethod,
            total,
            orderItems
        }));
    });

    // Function to validate payment method selection
    function validatePaymentMethod() {
        const paymentMethodSelect = document.getElementById('payment-method');
        if (paymentMethodSelect.value === '') {
            alert('Please select a payment method before checking out.');
            return false; // Prevent form submission
        }
        return true; // Allow form submission
    }
});
