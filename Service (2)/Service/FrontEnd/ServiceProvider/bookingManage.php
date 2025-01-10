<?php
include '../../Include/database.php';
session_start();

// Check if the provider is logged in
if (!isset($_SESSION['provider_id']) || !isset($_SESSION['name'])) {
    header('Location: ../login.php');
    exit;
}

$provider_id = $_SESSION['provider_id'];
$provider_name = $_SESSION['name']; // Store provider name from session

// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch bookings related to the logged-in service provider with different statuses
$sql_pending = "
    SELECT b.*, u.name as user_name, u.email as user_email, u.phone as user_phone, u.address as user_address, u.image as user_image
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    WHERE b.status = 'Pending' AND b.provider_id = ?
";
$stmt_pending = $conn->prepare($sql_pending);
$stmt_pending->bind_param('s', $provider_id);
$stmt_pending->execute();
$result_pending = $stmt_pending->get_result();

$sql_confirmed = "
    SELECT b.*, u.name as user_name, u.email as user_email, u.phone as user_phone, u.address as user_address, u.image as user_image
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    WHERE b.status = 'Confirmed' AND b.provider_id = ?
";
$stmt_confirmed = $conn->prepare($sql_confirmed);
$stmt_confirmed->bind_param('s', $provider_id);
$stmt_confirmed->execute();
$result_confirmed = $stmt_confirmed->get_result();

$sql_completed = "
    SELECT b.*, u.name as user_name, u.email as user_email, u.phone as user_phone, u.address as user_address, u.image as user_image
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    WHERE b.status = 'Completed' AND b.provider_id = ?
";
$stmt_completed = $conn->prepare($sql_completed);
$stmt_completed->bind_param('s', $provider_id);
$stmt_completed->execute();
$result_completed = $stmt_completed->get_result();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF token validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }

    $bookingId = $_POST['booking_id'];
    $action = $_POST['action'];

    if ($action === 'confirm' || $action === 'cancel') {
        $status = $action === 'confirm' ? 'Confirmed' : 'Canceled';
        $updateSql = "UPDATE bookings SET status = ? WHERE booking_id = ? AND provider_id = ?";
        $stmt_update = $conn->prepare($updateSql);
        $stmt_update->bind_param('sss', $status, $bookingId, $provider_id);

        if ($stmt_update->execute()) {
            $message = ['type' => $status === 'Confirmed' ? 'success' : 'danger', 'text' => 'Booking ' . $status . ' successfully!'];
        } else {
            $message = ['type' => 'danger', 'text' => 'Error updating booking status. Please try again later.'];
        }

        $stmt_update->close();
    }

    if ($action === 'complete') {
        $updateSql = "UPDATE bookings SET status = 'Completed' WHERE booking_id = ? AND provider_id = ?";
        $stmt_update = $conn->prepare($updateSql);
        $stmt_update->bind_param('is', $bookingId, $provider_id);

        if ($stmt_update->execute()) {
            $message = ['type' => 'success', 'text' => 'Booking marked as completed successfully!'];
        } else {
            $message = ['type' => 'danger', 'text' => 'Error updating booking status. Please try again later.'];
        }

        $stmt_update->close();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageDiv = document.getElementById('message');
            if (messageDiv) {
                setTimeout(() => {
                    messageDiv.style.display = 'none';
                }, 1500); 
            }
        });
    </script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <!-- Navigation Bar -->
    <nav class="bg-gray-800 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-white text-2xl font-semibold">Provider Dashboard</div>
            <div class="text-white text-lg">
                Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!
            </div>
            <div class="space-x-4">
                <a href="provider_dashboard.php" class="text-gray-300 hover:text-white">Home</a>
                <a href="../../BackEnd/ServiceProvider/profile.php" class="text-gray-300 hover:text-white">Profile</a>
                <a href="bookingManage.php" class="text-gray-300 hover:text-white">Bookings</a>
                <a href="set_schedule.php" class="text-gray-300 hover:text-white">Schedule</a>
                <a href="../../index.php" class="text-gray-300 hover:text-white">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-10 px-4">
        <h2 class="text-3xl font-bold text-gray-800">Manage Bookings</h2>

        <!-- Display messages -->
        <?php if (isset($message)): ?>
            <div id="message" class="mt-4 p-4 rounded-lg text-white <?php echo htmlspecialchars($message['type']) === 'success' ? 'bg-green-500' : 'bg-red-500'; ?>">
                <?php echo htmlspecialchars($message['text']); ?>
            </div>
        <?php endif; ?>

        <h3 class="text-2xl font-bold text-gray-800 mt-10">Pending Bookings</h3>
        <?php if ($result_pending->num_rows > 0): ?>
            <div class="overflow-x-auto mt-6">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Image</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Booking ID</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Service</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">User Name</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Email</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Phone</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Address</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Location</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Date</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Time</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php while ($row = $result_pending->fetch_assoc()): ?>
                            <tr>
                                <td class="border-b px-4 py-2 text-center">
                                    <img src="../../uploads/<?php echo htmlspecialchars($row['user_image']); ?>" alt="User Image" class="w-16 h-16 object-cover rounded-full border border-gray-300 mx-auto">
                                </td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['booking_id']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['service_name']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['user_name']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['user_email']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['user_phone']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['user_address']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['location']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                                <td class="border-b px-4 py-2 text-center">
                                    <form method="POST" action="bookingManage.php" class="flex justify-center space-x-2">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                        <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($row['booking_id']); ?>">
                                        <button type="submit" name="action" value="confirm" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Confirm</button>
                                        <button type="submit" name="action" value="cancel" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">Cancel</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="mt-4 text-gray-700 text-center">No pending bookings found.</p>
        <?php endif; ?>

        <h3 class="text-2xl font-bold text-gray-800 mt-10">Confirmed Bookings</h3>
        <?php if ($result_confirmed->num_rows > 0): ?>
            <div class="overflow-x-auto mt-6">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Image</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Booking ID</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Service</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">User Name</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Email</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Phone</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Address</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Location</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Date</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Time</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php while ($row = $result_confirmed->fetch_assoc()): ?>
                            <tr>
                                <td class="border-b px-4 py-2 text-center">
                                    <img src="../../uploads/<?php echo htmlspecialchars($row['user_image']); ?>" alt="User Image" class="w-16 h-16 object-cover rounded-full border border-gray-300 mx-auto">
                                </td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['booking_id']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['service_name']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['user_name']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['user_email']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['user_phone']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['user_address']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['location']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                                <td class="border-b px-4 py-2 text-center">
                                    <form method="POST" action="bookingManage.php" class="flex justify-center space-x-2">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                        <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($row['booking_id']); ?>">
                                        <button type="submit" name="action" value="complete" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">Complete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="mt-4 text-gray-700 text-center">No confirmed bookings found.</p>
        <?php endif; ?>

        <h3 class="text-2xl font-bold text-gray-800 mt-10">Completed Bookings</h3>
        <?php if ($result_completed->num_rows > 0): ?>
            <div class="overflow-x-auto mt-6">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Image</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Booking ID</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Service</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">User Name</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Email</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Phone</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Address</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Location</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Date</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Time</th>
                            <th class="border-b px-4 py-2 text-center text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php while ($row = $result_completed->fetch_assoc()): ?>
                            <tr>
                                <td class="border-b px-4 py-2 text-center">
                                    <img src="../../uploads/<?php echo htmlspecialchars($row['user_image']); ?>" alt="User Image" class="w-16 h-16 object-cover rounded-full border border-gray-300 mx-auto">
                                </td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['booking_id']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['service_name']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['user_name']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['user_email']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['user_phone']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['user_address']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['location']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                                <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                                <td class="border-b px-4 py-2 text-center">
                                    <!-- No actions needed for completed bookings -->
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="mt-4 text-gray-700 text-center">No completed bookings found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
