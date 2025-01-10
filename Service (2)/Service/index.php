<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Household Service Providing System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="Include/CSS/style.css">
    <style>
        body {
            background-color: #f8f9fa;
            opacity: 0.95;
            font-family: 'Arial', sans-serif;
        }
        .navbar {
            background-color: #343a40;
            padding: 20px;
        }
        .navbar-brand, .nav-link {
            color: #ffffff !important;
        }
        .navbar-nav .nav-link {
            margin-right: 15px;
        }
        .content-overlay {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }
        .card {
            border-radius: 12px;
            background: linear-gradient(135deg, #6b8aff, #86e3ce);
            color: white;
            padding: 20px;
        }
        .card-title {
            font-size: 2.8rem;
            font-weight: bold;
        }
        .card-text {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        .btn-primary, .btn-secondary {
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 1.1rem;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .btn-primary {
            background-color: #ff6b6b;
            border-color: #ff6b6b;
        }
        .btn-primary:hover {
            background-color: #ff4757;
            border-color: #ff4757;
        }
        .btn-secondary {
            background-color: #2ed573;
            border-color: #2ed573;
        }
        .btn-secondary:hover {
            background-color: #1dd1a1;
            border-color: #1dd1a1;
        }
        .info-section {
            margin-top: 50px;
        }
        .info-icon {
            font-size: 4rem;
            color: #6b8aff;
            margin-bottom: 20px;
        }
        .info-text {
            font-size: 1.2rem;
            color: #343a40;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Home Needs</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="./Include/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./Include/login.php#register-options">Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./FrontEnd/Admin/admin_login.php">Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>    

    <div class="row info-section text-center">
            <div class="col-lg-4">
                <i class="fas fa-tools info-icon"></i>
                <p class="info-text">Wide range of household services from plumbing to electrical repairs.</p>
            </div>
            <div class="col-lg-4">
                <i class="fas fa-user-shield info-icon"></i>
                <p class="info-text">Secure and trusted service providers verified by our team.</p>
            </div>
            <div class="col-lg-4">
                <i class="fas fa-headset info-icon"></i>
                <p class="info-text">24/7 customer support to assist you with your needs.</p>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="content-overlay">
                    <div class="card shadow-lg">
                        <div class="card-body">
                            <h1 class="card-title text-center">Welcome to Home Needs</h1>
                            <p class="card-text text-center">Please login or register to continue.<br>Feel Free to Use It.</p>
                            <div class="text-center">
                                <a href="./Include/login.php" class="btn btn-primary mb-3">Login</a>
                                <a href="./Include/login.php#register-options" class="btn btn-secondary">Register</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

       

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
