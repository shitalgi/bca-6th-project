<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Provider Registration</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../Include/CSS/style.css">
</head>
<body>
    <div class="wrapper">
        <!-- Register Form for Service Providers -->
        <div class="form-box register" id="register-provider">
            <h2>Register as Service Provider</h2>
            <form action="serviceProvider_backend.php" method="POST" enctype="multipart/form-data">
                <div class="input-box">
                    <span class="icon"><ion-icon name="person-outline"></ion-icon></span>
                    <input type="text" name="name" required placeholder=" ">
                    <label>Full Name</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="mail-outline"></ion-icon></span>
                    <input type="email" name="email" required placeholder=" ">
                    <label>Email</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span>
                    <input type="password" name="password" required placeholder=" ">
                    <label>Password</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="call-outline"></ion-icon></span>
                    <input type="text" name="phone" required placeholder=" ">
                    <label>Phone</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="id-card-outline"></ion-icon></span>
                    <input type="text" name="citizenship_no" required placeholder=" ">
                    <label>Citizenship Number</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="location-outline"></ion-icon></span>
                    <input type="text" name="address" required placeholder=" ">
                    <label>Address</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="image-outline"></ion-icon></span>
                    <input type="file" name="image" accept="image/*" required>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="hammer-outline"></ion-icon></span>
                    <select name="service_type" id="service_type" class="form-select" required>
                        <!-- Options will be populated by JavaScript -->
                    </select>
                    <label>Service Type</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="text-outline"></ion-icon></span>
                    <textarea name="profile_description" rows="4" required placeholder=" "></textarea>
                    <label>Profile Description</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="location-outline"></ion-icon></span>
                    <input type="text" class="form-control" id="latitude" name="latitude" required readonly placeholder=" ">
                    <label>Latitude</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="location-outline"></ion-icon></span>
                    <input type="text" class="form-control" id="longitude" name="longitude" required readonly placeholder=" ">
                    <label>Longitude</label>
                </div>
                <button type="button" class="btn btn-secondary" onclick="getLocation()">Get Location</button>
                <input type="submit" class="btn btn-primary" value="Register">
                <div class="login-register">
                    <p>Already have an account? <a href="../../Include/login.php" class="login-link">Login</a></p>
                </div>
            </form>
        </div>
    </div>

    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Custom JS -->
    <script src="../../Include/JS/script.js"></script>
    <script>
        
    </script>
</body>
</html>
