<?php
session_start();
// Include database connection
include '../../Include/database.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle adding a new service
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $service_name = mysqli_real_escape_string($conn, $_POST['service_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Check for duplicate service name
    $check_query = "SELECT * FROM services WHERE service_name = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $service_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // No duplicate found, proceed to insert
        $insert_query = "INSERT INTO services (service_name, description) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ss", $service_name, $description);
        $stmt->execute();
        $stmt->close();
    } else {
        // Duplicate found
        echo "<script>alert('Service name already exists.');</script>";
    }
}

// Handle deleting a service
if (isset($_GET['delete'])) {
    $service_id = intval($_GET['service_id']); // Ensure it's an integer
    $delete_query = "DELETE FROM services WHERE service_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_services.php"); // Redirect after deletion
    exit();
}

// Handle editing a service
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_service'])) {
    $service_id = intval($_POST['service_id']);
    $service_name = mysqli_real_escape_string($conn, $_POST['service_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Check for duplicate service name
    $check_query = "SELECT * FROM services WHERE service_name = ? AND service_id != ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("si", $service_name, $service_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // No duplicate found, proceed to update
        $update_query = "UPDATE services SET service_name = ?, description = ? WHERE service_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssi", $service_name, $description, $service_id);
        $stmt->execute();
        $stmt->close();
        header("Location: manage_services.php"); // Redirect to prevent form resubmission
        exit();
    } else {
        // Duplicate found
        echo "<script>alert('Service name already exists.');</script>";
    }
}

// Fetch existing services
$services_query = "SELECT * FROM services";
$services_result = mysqli_query($conn, $services_query);

// Determine if an edit form should be displayed
$edit_mode = isset($_GET['edit']);
$edit_service = null;
if ($edit_mode && isset($_GET['service_id'])) {
    $service_id = intval($_GET['service_id']);
    $query = "SELECT * FROM services WHERE service_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $edit_service = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../Include/CSS/slide.css"> <!-- Check this path -->
</head>
<body class="bg-gray-100">
    <div class="sidebar fixed top-0 left-0 w-64 h-full bg-gray-800 text-white">
        <div class="p-4">
            <a href="admin_dashboard.php" class="block py-2 px-4 hover:bg-gray-700">Dashboard</a>
            <a href="manage_service_providers.php" class="block py-2 px-4 hover:bg-gray-700">Manage Service Providers</a>
            <a href="manage_services.php" class="block py-2 px-4 bg-gray-700">Manage Services</a>
            <a href="manage_users.php" class="block py-2 px-4 hover:bg-gray-700">Manage Users</a>
            <a href="../../index.php" class="block py-2 px-4 hover:bg-gray-700">Logout</a>
        </div>
    </div>
    
    <div class="ml-64 p-6">
    <h2 class="text-4xl font-bold text-gray-800 mb-8">Manage Services</h2>

    <!-- Add Service Form -->
    <div class="bg-gradient-to-r from-purple-500 to-blue-500 p-8 rounded-lg shadow-xl mb-8">
        <h4 class="text-2xl font-semibold text-white mb-6">Add New Service</h4>
        <form action="manage_services.php" method="post">
            <div class="mb-6">
                <label for="service_name" class="block text-white text-lg">Service Name</label>
                <input type="text" id="service_name" name="service_name" class="form-input mt-2 block w-full border-none rounded-md shadow-sm focus:ring-2 focus:ring-blue-300" required>
            </div>
            <div class="mb-6">
                <label for="description" class="block text-white text-lg">Description</label>
                <textarea id="description" name="description" class="form-input mt-2 block w-full border-none rounded-md shadow-sm focus:ring-2 focus:ring-blue-300" required></textarea>
            </div>
            <button type="submit" name="add_service" class="bg-white text-blue-500 px-6 py-3 rounded-lg font-semibold hover:bg-blue-600 hover:text-white transition duration-300 ease-in-out">Add Service</button>
        </form>
    </div>

    <!-- Edit Service Form -->
    <?php if ($edit_mode && $edit_service) { ?>
    <div class="bg-gradient-to-r from-green-400 to-blue-400 p-8 rounded-lg shadow-xl mb-8">
        <h4 class="text-2xl font-semibold text-white mb-6">Edit Service</h4>
        <form action="manage_services.php" method="post">
            <input type="hidden" name="service_id" value="<?php echo htmlspecialchars($edit_service['service_id']); ?>">
            <div class="mb-6">
                <label for="service_name" class="block text-white text-lg">Service Name</label>
                <input type="text" id="service_name" name="service_name" value="<?php echo htmlspecialchars($edit_service['service_name']); ?>" class="form-input mt-2 block w-full border-none rounded-md shadow-sm focus:ring-2 focus:ring-blue-300" required>
            </div>
            <div class="mb-6">
                <label for="description" class="block text-white text-lg">Description</label>
                <textarea id="description" name="description" class="form-input mt-2 block w-full border-none rounded-md shadow-sm focus:ring-2 focus:ring-blue-300" required><?php echo htmlspecialchars($edit_service['description']); ?></textarea>
            </div>
            <button type="submit" name="edit_service" class="bg-white text-green-500 px-6 py-3 rounded-lg font-semibold hover:bg-green-600 hover:text-white transition duration-300 ease-in-out">Update Service</button>
        </form>
    </div>
    <?php } ?>

 <!-- Existing Services Table -->
<div class="bg-white p-8 rounded-lg shadow-xl overflow-x-auto">
    <h4 class="text-3xl font-semibold text-gray-800 mb-6">Existing Services</h4>
    <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
        <thead class="bg-gradient-to-r from-pink-500 to-blue-500 text-white">
            <tr>
                <th class="px-6 py-4 text-left text-xs md:text-sm font-medium uppercase tracking-wider">Service ID</th>
                <th class="px-6 py-4 text-left text-xs md:text-sm font-medium uppercase tracking-wider">Service Name</th>
                <th class="px-6 py-4 text-left text-xs md:text-sm font-medium uppercase tracking-wider">Description</th>
                <th class="px-6 py-4 text-left text-xs md:text-sm font-medium uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php while ($row = mysqli_fetch_assoc($services_result)) { ?>
            <tr class="hover:bg-gray-100 transition duration-300 ease-in-out">
                <td class="px-6 py-4 whitespace-nowrap text-gray-800 text-xs md:text-sm font-semibold"><?php echo htmlspecialchars($row['service_id']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-800 text-xs md:text-sm font-semibold"><?php echo htmlspecialchars($row['service_name']); ?></td>
                <td class="px-6 py-4 whitespace-normal md:whitespace-nowrap text-gray-700 text-xs md:text-sm"><?php echo htmlspecialchars($row['description']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <a href="manage_services.php?edit=1&service_id=<?php echo htmlspecialchars($row['service_id']); ?>" class="text-blue-500 hover:text-blue-700 font-semibold transition duration-300 ease-in-out text-xs md:text-sm">Edit</a>
                    <a href="manage_services.php?delete=1&service_id=<?php echo htmlspecialchars($row['service_id']); ?>" class="text-red-500 hover:text-red-700 font-semibold ml-4 transition duration-300 ease-in-out text-xs md:text-sm" onclick="return confirm('Are you sure you want to delete this service?');">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>


</div>

</body>
</html>
