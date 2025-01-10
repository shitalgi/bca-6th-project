<?php
session_start();
include '../../Include/database.php'; // Include the database connection file

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch all service providers
$pending_providers = mysqli_query($conn, "SELECT * FROM serviceProviders WHERE status = 0");
$approved_providers = mysqli_query($conn, "SELECT * FROM serviceProviders WHERE status = 1");

// Handle approval
if (isset($_POST['approve'])) {
    $provider_id = $_POST['provider_id'];
    $update_query = "UPDATE serviceProviders SET status = 1 WHERE provider_id = $provider_id";
    mysqli_query($conn, $update_query);
    header("Location: manage_service_providers.php");
}

// Handle decline with confirmation
if (isset($_POST['decline'])) {
    $provider_id = $_POST['provider_id'];
    $delete_query = "DELETE FROM serviceProviders WHERE provider_id = $provider_id";
    mysqli_query($conn, $delete_query);
    echo "<script>alert('Service provider declined and removed from the list.');</script>";
    header("Refresh:0; url=manage_service_providers.php");
}

// Handle delete with confirmation
if (isset($_POST['delete'])) {
    $provider_id = $_POST['provider_id'];
    $delete_query = "DELETE FROM serviceProviders WHERE provider_id = $provider_id";
    mysqli_query($conn, $delete_query);
    echo "<script>alert('Service provider deleted.');</script>";
    header("Refresh:0; url=manage_service_providers.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Service Providers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../Include/CSS/slide.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #343a40;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 15px;
            text-decoration: none;
            font-size: 16px;
            margin-bottom: 10px;
            transition: background-color 0.3s;
        }

        .sidebar a:hover {
            background-color: #007bff;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            width: 100%;
        }

        .container {
            margin-top: 30px;
        }

        h2 {
            color: #007bff;
            margin-bottom: 20px;
        }

        table {
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th {
            background-color: #007bff;
            color: white;
            text-align: center;
        }

        td {
            vertical-align: middle;
            text-align: center;
        }

        .btn {
            margin-right: 5px;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-success {
            background-color: #28a745;
        }

        .btn-delete {
            background-color: #6c757d;
            color: white;
        }

        .btn-delete:hover {
            background-color: #5a6268;
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

    <div class="content">
        <div class="container">
            <h2>Pending Service Providers</h2>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Citizenship No</th>
                            <th>Address</th>
                            <th>Service Type</th>
                            <th>Profile Description</th>
                            <th>Registration Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($provider = mysqli_fetch_assoc($pending_providers)) { ?>
                        <tr>
                            <td><?php echo $provider['name']; ?></td>
                            <td><?php echo $provider['email']; ?></td>
                            <td><?php echo $provider['phone']; ?></td>
                            <td><?php echo $provider['citizenship_no']; ?></td>
                            <td><?php echo $provider['address']; ?></td>
                            <td><?php echo $provider['service_type']; ?></td>
                            <td><?php echo $provider['profile_description']; ?></td>
                            <td><?php echo $provider['registration_date']; ?></td>
                            <td>
                                <form method="post" style="display:inline-block;">
                                    <input type="hidden" name="provider_id"
                                        value="<?php echo $provider['provider_id']; ?>">
                                    <button type="submit" name="approve" class="btn btn-success btn-sm">Approve</button>
                                </form>
                                <form method="post" style="display:inline-block;"
                                    onsubmit="return confirm('Are you sure you want to decline this service provider?');">
                                    <input type="hidden" name="provider_id"
                                        value="<?php echo $provider['provider_id']; ?>">
                                    <button type="submit" name="decline" class="btn btn-danger btn-sm">Decline</button>
                                </form>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <h2>Approved Service Providers</h2>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Citizenship No</th>
                            <th>Address</th>
                            <th>Service Type</th>
                            <th>Profile Description</th>
                            <th>Registration Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($provider = mysqli_fetch_assoc($approved_providers)) { ?>
                        <tr>
                            <td><?php echo $provider['name']; ?></td>
                            <td><?php echo $provider['email']; ?></td>
                            <td><?php echo $provider['phone']; ?></td>
                            <td><?php echo $provider['citizenship_no']; ?></td>
                            <td><?php echo $provider['address']; ?></td>
                            <td><?php echo $provider['service_type']; ?></td>
                            <td><?php echo $provider['profile_description']; ?></td>
                            <td><?php echo $provider['registration_date']; ?></td>
                            <td>
                                <form method="post" style="display:inline-block;"
                                    onsubmit="return confirm('Are you sure you want to delete this service provider?');">
                                    <input type="hidden" name="provider_id"
                                        value="<?php echo $provider['provider_id']; ?>">
                                    <button type="submit" name="delete" class="btn btn-delete btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
