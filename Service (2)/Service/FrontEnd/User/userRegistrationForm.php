<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../Include/CSS/style.css">
    <style>
        /* Style for error messages */
        .alert-danger {
            color: #dc3545; /* Bootstrap's default red color for error messages */
            border-color: #dc3545;
            background-color: #f8d7da;
            padding: 15px;
            margin-bottom: 15px;
        }

        .alert-success {
            color: #155724;
            border-color: #c3e6cb;
            background-color: #d4edda;
            padding: 15px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="form-box register">
            <h2>User Registration Form</h2>
            <?php
            if (isset($_GET['error'])) {
                echo "<div class='alert alert-danger'>" . htmlspecialchars($_GET['error']) . "</div>";
            }
            if (isset($_GET['success'])) {
                echo "<div class='alert alert-success'>Registration successful!</div>";
            }
            ?>
            <form action="userRegister_backend.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
                <div class="input-box">
                    <span class="icon"><ion-icon name="person-outline"></ion-icon></span>
                    <input type="text" name="name" id="name">
                    <label>Full Name</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="mail-outline"></ion-icon></span>
                    <input type="email" name="email" id="email">
                    <label>Email</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span>
                    <input type="password" name="password" id="password">
                    <label>Password</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="call-outline"></ion-icon></span>
                    <input type="text" name="phone" id="phone" >
                    <label>Phone</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="location-outline"></ion-icon></span>
                    <input type="text" name="address">
                    <label>Address</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="image-outline"></ion-icon></span>
                    <input type="file" name="image" id="image" accept="image/*">
                    <label>Profile</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="location-outline"></ion-icon></span>
                    <input type="text" class="form-control" id="latitude" name="latitude"  readonly placeholder=" ">
                    <label>Latitude</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="location-outline"></ion-icon></span>
                    <input type="text" class="form-control" id="longitude" name="longitude"  readonly placeholder=" ">
                    <label>Longitude</label>
                </div>
                <button type="button" class="btn btn-secondary" onclick="getLocation()">Get Location</button>
                <button type="submit" class="btn">Register</button>
                <div class="login-register">
                    <p>Already have an account? <a href="../../Include/login.php" class="login-link">Login</a></p>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script src="../../Include/JS/script.js"></script>
    
    <script>
    function validateForm() {
    var name = document.getElementById('name').value.trim();
    var email = document.getElementById('email').value.trim();
    var password = document.getElementById('password').value.trim();
    var phone = document.getElementById('phone').value.trim();
    var address = document.querySelector('input[name="address"]').value.trim();
    var image = document.getElementById('image').files[0];
    var latitude = document.getElementById('latitude').value.trim();
    var longitude = document.getElementById('longitude').value.trim();
    var valid = true;

    // Clear previous error messages
    document.querySelectorAll('.error-message').forEach(function(el) {
        el.remove();
    });

    // Validate Name
    if (name === '' || !/^[A-Za-z\s]+$/.test(name)) {
        showError(document.getElementById('name'), 'Full Name can only contain alphabetic characters and spaces.');
        valid = false;
    }

    // Validate Email
    if (email === '' || !validateEmail(email)) {
        showError(document.getElementById('email'), 'Please enter a valid email address.');
        valid = false;
    }

    // Validate Password
    if (password === '' || !validatePassword(password)) {
        showError(document.getElementById('password'), 'Password must contain at least one uppercase letter, one lowercase letter, one number, and be at least 8 characters long.');
        valid = false;
    }

    // Validate Phone
    if (phone === '' || !/^[0-9]{10}$/.test(phone)) {
        showError(document.getElementById('phone'), 'Phone number must be 10 digits long.');
        valid = false;
    }

    // Validate Address
    if (address === '') {
        showError(document.querySelector('input[name="address"]'), 'Please enter your address.');
        valid = false;
    }

    // Validate Image Size
    if (image && image.size > 2097152) { // 2MB in bytes
        showError(document.getElementById('image'), 'Image size must be less than 2MB.');
        valid = false;
    }

    // Validate Latitude and Longitude
    if (latitude === '' || longitude === '') {
        showError(document.getElementById('latitude'), 'Please retrieve your location.');
        valid = false;
    }

    return valid;
}

function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@(([^<>()[\]\\.,;:\s@"]+\.)+[^<>()[\]\\.,;:\s@"]{2,})$/i;
    return re.test(String(email).toLowerCase());
}

function validatePassword(password) {
    return /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/.test(password);
}

function showError(input, message) {
    var error = document.createElement('div');
    error.className = 'error-message text-danger';
    error.innerText = message;
    input.parentElement.appendChild(error);
}

// Function to get location (already defined in your code)
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showErrorLocation);
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}

function showPosition(position) {
    document.getElementById('latitude').value = position.coords.latitude;
    document.getElementById('longitude').value = position.coords.longitude;
}

function showErrorLocation(error) {
    alert("Unable to retrieve your location. Please try again.");
}

// Attach the validateForm function to the form's onsubmit event
document.querySelector('form').onsubmit = validateForm;

    
    </script>
</body>
</html>
