<?php
session_start();
include '../../Include/database.php'; 
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}
// Process user deletion
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['user_id']); // Ensure it's an integer
    $delete_query = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_users.php"); // Redirect after deletion
    exit();
}

// Fetch users for display
$users_query = "SELECT * FROM users";
$users_result = $conn->query($users_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../Include/CSS/slide.css">
    <style>
        .container-fluid {
            margin-left: 250px;
            padding: 20px;
        }
        .card {
            border-radius: 8px;
            overflow: hidden;
        }
        .card-header {
            color: brown;
            text-align: center;
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .table {
            border-collapse: collapse;
            width: 100%;
        }
        .table thead th {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 15px;
            font-weight: bold;
        }
        .table tbody td {
            vertical-align: middle;
            text-align: center;
            padding: 10px;
        }
        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .table tbody tr:hover {
            background-color: #ddd;
        }
        .btn {
            font-size: 0.875rem;
        }
        @media (max-width: 768px) {
            .container-fluid {
                margin-left: 0;
            }
            .sidebar {
                width: 100%;
                position: relative;
                height: auto;
                padding: 10px;
            }
            .sidebar a {
                margin-bottom: 10px;
            }
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

    <div class="container-fluid">
        <h2 class="mb-4">Manage Users</h2>
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">User List</h2>
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $users_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td>
                                <a href="manage_users.php?delete=true&user_id=<?php echo $row['user_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>
                        <?php } ?>                     
                    </tbody>
                </table>
            </div>
        </div>
    </div>  

    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/custom.js"></script>
</body>
</html>
