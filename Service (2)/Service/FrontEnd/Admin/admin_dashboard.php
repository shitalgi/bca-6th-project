<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../Include/CSS/slide.css">
    <style>       

        .main-content {
            margin-left: 250px; /* Same width as the sidebar */
            padding: 20px;
        }

        .navbar {
            background-color: #007bff;
            color: white;
        }

        .navbar .navbar-brand {
            color: white;
        }

        .navbar .navbar-brand:hover {
            color: white;
        }

        .header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .card {
            border: none;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="manage_service_providers.php">Manage Service Providers</a>
        <a href="manage_services.php">Manage Services</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="../../index.php">Logout</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Admin Dashboard</h1>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Manage Service Providers</h5>
                            <p class="card-text">View, approve, or decline service providers.</p>
                            <a href="manage_service_providers.php" class="btn btn-primary">Show</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Manage Services</h5>
                            <p class="card-text">Add, edit, or delete available services.</p>
                            <a href="manage_services.php" class="btn btn-primary">Show</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Manage Users</h5>
                            <p class="card-text">View and manage registered users.</p>
                            <a href="manage_users.php" class="btn btn-primary">Show</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
