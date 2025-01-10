<?php
// Start session
session_start();

// Include database configuration file
include '../../Include/database.php';

// Initialize variables for feedback
$success = '';
$error = '';

// Get provider and booking ID from GET request
if (isset($_GET['provider_id']) && isset($_GET['booking_id'])) {
    $provider_id = $_GET['provider_id'];
    $booking_id = $_GET['booking_id'];
} else {
    $error = "Invalid request. Provider ID or Booking ID is missing.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure booking ID and provider ID are set
    if (!isset($provider_id) || !isset($booking_id)) {
        $error = "Invalid provider or booking ID.";
    } else {
        $rating = $_POST['rating'];
        $review = $_POST['review'];

        // Validate input
        if (empty($rating) || empty($review)) {
            $error = "Rating and review are required.";
        } else {
            // Sanitize inputs
            $rating = (int)$rating;
            $review = htmlspecialchars(trim($review), ENT_QUOTES);

            // Check if booking_id exists in bookings table
            $query = "SELECT * FROM bookings WHERE booking_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $booking_id);
            $stmt->execute();
            $booking_result = $stmt->get_result();

            if ($booking_result->num_rows == 0) {
                $error = "Invalid booking ID.";
            } else {
                // Insert review into database
                $query = "INSERT INTO reviews (user_id, provider_id, booking_id, rating, review, review_date) VALUES (?, ?, ?, ?, ?, NOW())";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("iiids", $_SESSION['user_id'], $provider_id, $booking_id, $rating, $review);
                if ($stmt->execute()) {
                    // Update booking status
                    $query = "UPDATE bookings SET status = 'reviewed' WHERE booking_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $booking_id);
                    $stmt->execute();

                    $success = "Thank you for your review!";
                    // Redirect after successful review submission
                    header("Location: user_booking.php?review_success=1");
                    exit();
                } else {
                    $error = "Failed to submit review.";
                }
            }
        }
    }
}

// Fetch user name for the header (assuming it's stored in session)
$user_name = isset($_SESSION['name']) ? $_SESSION['name'] : 'User';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Leave a Review</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../Include/CSS/index.css">
</head>
<body>
<header class="bg-primary text-white py-3">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="logo">Home Needs</h2>
            <nav class="navigation">
                <a href="user_dashboard.php" class="nav-link text-white">Home</a>
                <a href="#about" class="nav-link text-white">About</a>
                <a href="services.php" class="nav-link text-white">Services</a>
                <a href="user_booking.php" class="nav-link text-white">Your Booking</a>
                <a href="contact.php" class="nav-link text-white">Contact</a>
                <a href="profile.php" class="nav-link text-white"><?php echo htmlspecialchars($user_name); ?></a>
                <a href="../../index.php" class="btn btn-light">Logout</a>
            </nav>
        </div>
    </div>
</header>

<div class="container my-5">
    <h2>Leave a Review</h2>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <input type="hidden" name="provider_id" value="<?php echo htmlspecialchars($provider_id); ?>">
        <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking_id); ?>">
        <div class="form-group">
            <label for="rating">Rating (1-5):</label>
            <input type="number" id="rating" name="rating" min="1" max="5" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="review">Review:</label>
            <textarea id="review" name="review" rows="4" cols="50" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Review</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
