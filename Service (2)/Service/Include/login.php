<?php

if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="./CSS/lg.css">
    <style>
    .error-message {
    font-size: 0.875em;
    color: #dc3545; /* Bootstrap's red color for errors */
    margin-top: 5px;
}

    </style>
</head>

<body>
    <div class="wrapper">
        <a href="../index.php"><span class="icon-close"><ion-icon name="close-outline"></ion-icon></span></a>

        <!-- Login Form -->
        <div class="form-box login active">
            <h2>Login Form</h2>
            <!-- Error Message Display -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="login_backend.php" method="POST">
                <div class="input-box">
                    <span class="icon"><ion-icon name="mail-outline"></ion-icon></span>
                    <input type="email" name="email">
                    <label>Email</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span>
                    <input type="password" name="password" >
                    <label>Password</label>
                </div>
                <div class="remember-forgot">
                    <label><input type="checkbox" name="remember"> Remember me</label>
                    <a href="#">Forgot Password</a>
                </div>
                <button type="submit" class="btn">Login</button>
                <div class="login-register">
                    <p>Don't have an account? <a href="#register-options" class="register-link">Register</a></p>
                </div>
            </form>

        </div>

        <!-- Options for Register -->
        <div class="form-box register-options" id="register-options">
            <a href="../index.php"><span class="icon-close"><ion-icon name="close-outline"></ion-icon></span></a>
            <h2>Select Registration Type</h2>
            <div class="options">
                <button class="option register-user btn btn-primary">
                    <a href="../FrontEnd/User/userRegistrationForm.php" class="text-white">Register as User</a>
                </button>
                <button class="option register-service btn btn-secondary">
                    <a href="../FrontEnd/ServiceProvider/serviceProviderRegisterform.php" class="text-white">Register as Service Provider</a>
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        document.querySelector('.icon-close').addEventListener('click', function() {
            document.querySelector('.wrapper').style.display = 'none';
        });


        document.querySelector('form').addEventListener('submit', function(e) {
        let email = document.querySelector('input[name="email"]');
        let password = document.querySelector('input[name="password"]');
        let valid = true;

        // Clear previous error messages
        document.querySelectorAll('.error-message').forEach(function(el) {
            el.remove();
        });

        // Validate email
        if (!validateEmail(email.value)) {
            showError(email, 'Please enter a valid email address.');
            valid = false;
        }

        // Validate password
        if (password.value.trim() === '') {
            showError(password, 'Please enter your password.');
            valid = false;
        }

        // If not valid, prevent form submission
        if (!valid) {
            e.preventDefault();
        }
    });

    function validateEmail(email) {
        // Basic email pattern check
        var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@(([^<>()[\]\\.,;:\s@"]+\.)+[^<>()[\]\\.,;:\s@"]{2,})$/i;
        return re.test(String(email).toLowerCase());
    }

    function showError(input, message) {
        let error = document.createElement('div');
        error.className = 'error-message text-danger';
        error.innerText = message;
        input.parentElement.appendChild(error);
    }

    </script>
</body>

</html>