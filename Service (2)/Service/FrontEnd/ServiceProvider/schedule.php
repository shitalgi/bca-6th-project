<?php
// Include database configuration file
include '../../Include/database.php';
session_start(); // Ensure the session is started

$provider_id = $_SESSION['provider_id'];  // Assuming provider is logged in

// Check if the form was submitted to add or update schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $available_date = $_POST['available_date'];
    $available_time = $_POST['set_time']; // Updated to match the database column name
    $schedule_id = isset($_POST['schedule_id']) ? $_POST['schedule_id'] : null;  // Capture schedule ID for editing

    // Validate input data (server-side)
    if (empty($available_date) || empty($available_time)) {
        echo "All fields are required!";
        exit;
    }

    // Validate that time is between 7 AM and 7 PM
    $available_time_int = strtotime($available_time);
    $start_time = strtotime("07:00");
    $end_time = strtotime("19:00");
    if ($available_time_int < $start_time || $available_time_int > $end_time) {
        echo "Time must be between 7 AM and 7 PM.";
        exit;
    }

    // Validate that the date is not in the past and within 1 month
    $today = date('Y-m-d');
    $one_month_later = date('Y-m-d', strtotime('+1 month'));
    if ($available_date < $today) {
        echo "The date cannot be in the past.";
        exit;
    }
    if ($available_date > $one_month_later) {
        echo "The date must be within 1 month.";
        exit;
    }

    // If editing an existing schedule
    if ($schedule_id) {
        // Update the schedule
        $sql = "UPDATE provider_schedule SET available_date = ?, available_time = ? WHERE provider_id = ? AND id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $available_date, $available_time, $provider_id, $schedule_id);
        if ($stmt->execute()) {
            $message = "Schedule updated successfully!";
        } else {
            $message = "Failed to update schedule.";
        }
    } else {
        // Check for duplicate entries (same provider, same date, and time)
        $sql = "SELECT * FROM provider_schedule WHERE provider_id = ? AND available_date = ? AND available_time = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $provider_id, $available_date, $available_time);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "You have already set availability for this date and time.";
            exit;
        }

        // Insert new schedule
        $sql = "INSERT INTO provider_schedule (provider_id, available_date, available_time) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $provider_id, $available_date, $available_time);
        if ($stmt->execute()) {
            $message = "Schedule saved successfully!";
        } else {
            $message = "Failed to save the schedule.";
        }
    }
}

// Handle schedule deletion
if (isset($_GET['delete_schedule_id'])) {
    $schedule_id = $_GET['delete_schedule_id'];
    $sql = "DELETE FROM provider_schedule WHERE provider_id = ? AND id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $provider_id, $schedule_id);
    if ($stmt->execute()) {
        $message = "Schedule deleted successfully!";
    } else {
        $message = "Failed to delete the schedule.";
    }
}

// Fetch existing schedules for the logged-in provider
$sql = "SELECT * FROM provider_schedule WHERE provider_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$schedule_result = $stmt->get_result();

// Check if editing schedule
$edit_schedule_id = isset($_GET['edit_schedule_id']) ? $_GET['edit_schedule_id'] : null;
$edit_schedule = null;

if ($edit_schedule_id) {
    $sql = "SELECT * FROM provider_schedule WHERE provider_id = ? AND id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $provider_id, $edit_schedule_id);
    $stmt->execute();
    $edit_result = $stmt->get_result();
    $edit_schedule = $edit_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Provider Schedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            setTimeout(function () {
                const messageDiv = document.getElementById("message");
                if (messageDiv) {
                    messageDiv.style.display = "none";
                }
            }, 1000);
        });
    </script>
</head>
<body>
<div class="container mt-5">
    <!-- Back button -->
    <div class="mb-3">
        <a href="provider_dashboard.php" class="btn btn-secondary">Back</a>
    </div>

    <h2 class="mb-3"><?php echo $edit_schedule ? 'Edit' : 'Set'; ?> Your Availability</h2>

    <!-- Display message if exists -->
    <?php if (!empty($message)): ?>
        <div id="message" class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" class="mb-5">
        <input type="hidden" name="schedule_id" value="<?php echo $edit_schedule['id'] ?? ''; ?>"> <!-- Hidden field for edit -->
        <div class="mb-3">
            <label for="available_date" class="form-label">Select Date</label>
            <input type="date" id="available_date" name="available_date" class="form-control" value="<?php echo $edit_schedule['available_date'] ?? ''; ?>" required>
        </div>
        <div class="mb-3">
            <label for="set_time" class="form-label">Set Time</label>
            <input type="time" id="set_time" name="set_time" class="form-control" value="<?php echo $edit_schedule['available_time'] ?? ''; ?>" required>
        </div>
        
        <button type="submit" class="btn btn-primary"><?php echo $edit_schedule ? 'Update' : 'Save'; ?></button>
    </form>

    <h3>Your Available Times</h3>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $schedule_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['available_date']); ?></td>
                <td><?php echo htmlspecialchars($row['available_time']); ?></td>
                <td>
                    <a href="?edit_schedule_id=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
                    <a href="?delete_schedule_id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this schedule?')">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
