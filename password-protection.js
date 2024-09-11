const PASSWORD = "nath"; // Replace with your desired password

function checkPassword() {
    let password = prompt("Enter the password to access this page:");
    if (password !== PASSWORD) {
        alert("Incorrect password. Access denied.");
        window.location.href = "error.php"; // Redirect to an error page
    }
}

window.onload = function() {
    checkPassword();
    greetUser();
};

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
