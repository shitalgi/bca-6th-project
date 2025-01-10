<?php
include '../../Include/database.php';
session_start();

// Check if provider is logged in
if (!isset($_SESSION['provider_id'])) {
    header('Location: ../login.php');
    exit;
}

$provider_id = $_SESSION['provider_id'];

// Fetch provider's status from the database
$query = "SELECT name, status FROM serviceproviders WHERE provider_id = ?";
$stmt = $conn->prepare($query);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $provider_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the provider exists
if ($result->num_rows > 0) {
    $provider = $result->fetch_assoc();
    $_SESSION['name'] = $provider['name'];
    
    // Check if the provider's account is approved
    if ($provider['status'] != 1) { // Assuming '1' means approved
        echo "<script>alert('Your account is not approved yet. Please wait for admin approval.'); window.location.href='../../index.php';</script>";
        exit;
    }
} else {
    // Handle the case where provider_id is not found in the database
    echo "<script>alert('Invalid account. Please contact support.'); window.location.href='../../index.php';</script>";
    exit;
}

// Fetch latest bookings (excluding canceled ones)
$sql_latest_bookings = "
    SELECT * FROM bookings
    WHERE provider_id = ? AND status != 'canceled'
    ORDER BY created_at DESC
    LIMIT 3
";
$stmt_latest = $conn->prepare($sql_latest_bookings);

if ($stmt_latest === false) {
    die("Error preparing statement for latest bookings: " . $conn->error);
}

$stmt_latest->bind_param('i', $provider_id);
$stmt_latest->execute();
$result_latest = $stmt_latest->get_result();

$message = []; // Initialize message variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle profile update
    if (isset($_POST['update_profile'])) {
        $new_description = $_POST['profile_description'];

        $updateProfileSql = "UPDATE serviceproviders SET profile_description = ? WHERE provider_id = ?";
        $stmt_update_profile = $conn->prepare($updateProfileSql);

        if ($stmt_update_profile === false) {
            die("Error preparing statement for profile update: " . $conn->error);
        }

        $stmt_update_profile->bind_param('si', $new_description, $provider_id);

        if ($stmt_update_profile->execute()) {
            $message = ['type' => 'success', 'text' => 'Profile updated successfully!'];
        } else {
            $message = ['type' => 'danger', 'text' => 'Error updating profile. Please try again later.'];
        }

        $stmt_update_profile->close();
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
    <title>Provider Dashboard</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <!-- Header -->
    <header class="bg-gray-800 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-white text-2xl font-semibold">Home Needs - Provider Dashboard</div>
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
    </header>

    <!-- Main Content -->
    <main class="container mx-auto mt-10">
        <h2 class="text-3xl font-bold text-gray -800 mb-6">Dashboard Overview</h2>

        <!-- Dashboard Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- My Profile Card -->
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h3 class="text-xl font-semibold text-gray-700">My Profile</h3>
                <p class="mt-2 text-gray-600">Manage your personal information.</p>
                <a href="../../BackEnd/ServiceProvider/profile.php" class="mt-4 inline-block text-blue-500 hover:underline">Edit Profile</a>
            </div>

            <!-- Bookings Card -->
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h3 class="text-xl font-semibold text-gray-700">Bookings</h3>
                <p class="mt-2 text-gray-600">View and manage your bookings.</p>
                <a href="bookingManage.php" class="mt-4 inline-block text-blue-500 hover:underline">Manage Bookings</a>
            </div>

            <!-- Schedule Card -->
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h3 class="text-xl font-semibold text-gray-700">Schedule</h3>
                <p class="mt-2 text-gray-600">Set your availability.</p>
                <a href="set_schedule.php" class="mt-4 inline-block text-blue-500 hover:underline">Set Schedule</a>
            </div>           
        </div>

        <!-- Additional Content -->
        <div class="mt-10">
            <?php if (isset($message)): ?>
            <div class="mt-4 p-4 rounded-lg text-white <?php echo htmlspecialchars($message['type']) === 'success' ? 'bg-green-500' : 'bg-red-500'; ?>">
                <?php echo htmlspecialchars($message['text']); ?>
            </div>
            <?php endif; ?>

            <div class="mt-10">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Latest Bookings</h2>
                <div class="bg-white p-4 rounded-lg shadow-md">
                    <?php if ($result_latest->num_rows > 0): ?>
                        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="border-b px-4 py-2 text-center text-gray-700">Booking ID</th>
                                    <th class="border-b px-4 py-2 text-center text-gray-700">Service</th>
                                    <th class="border-b px-4 py-2 text-center text-gray-700">User  Name</th>
                                    <th class="border-b px-4 py-2 text-center text-gray-700">Date</th>
                                    <th class="border-b px-4 py-2 text-center text-gray-700">Time</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php while ($row = $result_latest->fetch_assoc()): ?>
                                    <tr>
                                        <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['booking_id']); ?></td>
                                        <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['service_name']); ?></td>
                                        <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['user_name']); ?></td>
                                        <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                                        <td class="border-b px-4 py-2 text-center"><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-gray-600">No recent bookings available.</p>
                    <?php endif; ?>
                    <p class="mt-4 text-gray-600"><a href="bookingManage.php" class="text-blue-500 hover:underline">View all bookings</a></p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 p-4 mt-10">
        <div class="container mx-auto text-center text-white">
            &copy; 2024 Home Needs. All rights reserved.
        </div>
    </footer>

</body>
 </html>