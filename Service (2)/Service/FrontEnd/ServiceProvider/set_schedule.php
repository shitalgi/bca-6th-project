<?php
include '../../Include/database.php';
session_start();

// Check if the provider is logged in
if (!isset($_SESSION['provider_id'])) {
    header('Location: ../login.php');
    exit;
}

$provider_id = $_SESSION['provider_id'];
$provider_name = $_SESSION['name'];

// Initialize message variable to avoid warnings
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Adding a new time slot
    if (isset($_POST['available_date']) && isset($_POST['available_time'])) {
        $available_date = $_POST['available_date'];
        $available_time = $_POST['available_time'];

        // Check if the time slot already exists to avoid duplicates
        $check_query = "SELECT * FROM provider_schedule WHERE provider_id = ? AND available_date = ? AND available_time = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("iss", $provider_id, $available_date, $available_time);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "Error: Duplicate entry for this time slot.";
        } else {
            // Insert the new time slot
            $insert_query = "INSERT INTO provider_schedule (provider_id, available_date, available_time) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("iss", $provider_id, $available_date, $available_time);

            if ($stmt->execute()) {
                $message = "Time slot added successfully.";
            } else {
                $message = "Error: Could not add time slot.";
            }
        }
    }

    // Handle editing a time slot
    if (isset($_POST['edit_id'])) {
        $edit_id = $_POST['edit_id'];
        $edit_date = $_POST['edit_date'];
        $edit_time = $_POST['edit_time'];
        $update_query = "UPDATE provider_schedule SET available_date = ?, available_time = ? WHERE schedule_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssi", $edit_date, $edit_time, $edit_id);

        if ($stmt->execute()) {
            $message = "Time slot updated successfully.";
        } else {
            $message = "Error: Could not update time slot.";
        }
    }

    // Handle deleting a time slot
    if (isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];

        $delete_query = "DELETE FROM provider_schedule WHERE schedule_id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $delete_id);

        if ($stmt->execute()) {
            $message = "Time slot deleted successfully.";
        } else {
            $message = "Error: Could not delete time slot.";
        }
    }
}

// Fetch existing time slots for the provider
$fetch_query = "SELECT * FROM provider_schedule WHERE provider_id = ?";
$stmt = $conn->prepare($fetch_query);
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$schedule_result = $stmt->get_result();

// Get today's date in YYYY-MM-DD format
$today_date = date("Y-m-d");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Schedule</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById('available_date').addEventListener('input', function() {
                const today = new Date().toISOString().split('T')[0];
                if (this.value < today) {
                    alert("Please select a date that is today or in the future.");
                    this.value = today; // Reset to today's date if the selected date is invalid
                }
            });
        });
    </script>
</head>
<body class="bg-gray-100">

<header class="bg-gray-800 p-4">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-white text-2xl font-semibold">Home Needs - Provider Dashboard</h1>
        <div class="flex items-center space-x-4 text-white">
            <span>Welcome, <?php echo htmlspecialchars($provider_name); ?>!</span>
            <a href="provider_dashboard.php" class="hover:underline">Home</a>
            <a href="../../BackEnd/ServiceProvider/profile.php" class="hover:underline">Profile</a>
            <a href="bookingManage.php" class="hover:underline">Bookings</a>
            <a href="set_schedule.php" class="hover:underline">Schedule</a>
            <a href="../../index.php" class="hover:underline">Logout</a>
        </div>
    </div>
</header>

<main class="container mx-auto mt-10 p-5 bg-white shadow-lg rounded-lg">
    <h2 class="text-2xl font-bold mb-5">Set Your Available Time Slots</h2>

    <?php if ($message): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-5">
            <p><?php echo htmlspecialchars($message); ?></p>
        </div>
    <?php endif; ?>

    <!-- Add Time Slot Form -->
    <form action="set_schedule.php" method="POST" class="mb-10">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <label for="available_date" class="block text-sm font-medium text-gray-700">Select Date:</label>
                <input type="date" name="available_date" id="available_date" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required min="<?php echo $today_date; ?>">
            </div>

            <div>
                <label for="available_time" class="block text-sm font-medium text-gray-700">Select Time:</label>
                <input type="time" name="available_time" id="available_time" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
            </div>
        </div>
        <div class="mt-6">
            <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Add Time Slot</button>
        </div>
    </form>

    <!-- Time Slots Table -->
    <table class="min-w-full bg-white rounded-lg shadow-md overflow-hidden">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="py-3 px-4 text-left">Schedule ID</th>
                <th class="py-3 px-4 text-left">Available Date</th>
                <th class="py-3 px-4 text-left">Available Time</th>
                <th class="py-3 px-4 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($schedule_result->num_rows > 0): ?>
                <?php while ($row = $schedule_result->fetch_assoc()): ?>
                    <tr class="border-b">
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['schedule_id']); ?></td>
                        <td class="py-3 px-4"><?php echo date("m/d/Y", strtotime($row['available_date'])); ?></td>
                        <td class="py-3 px-4"><?php echo date("h:i A", strtotime($row['available_time'])); ?></td>
                        <td class="py-3 px-4">
                            <form class="inline-block" action="set_schedule.php" method="POST">
                                <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($row['schedule_id']); ?>">
                                <input type="date" name="edit_date" value="<?php echo htmlspecialchars($row['available_date']); ?>" required class="p-1 border rounded">
                                <input type="time" name="edit_time" value="<?php echo htmlspecialchars($row['available_time']); ?>" required class="p-1 border rounded">
                                <button type="submit" class="ml-2 text-blue-600 hover:underline">Edit</button>
                            </form>
                            <form class="inline-block ml-2" action="set_schedule.php" method="POST">
                                <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($row['schedule_id']); ?>">
                                <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Are you sure you want to delete this time slot?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="py-3 px-4 text-center text-gray-500">No time slots available.</ td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

</body>
</html>
