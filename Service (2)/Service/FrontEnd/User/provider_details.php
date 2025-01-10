<?php
// Include database configuration file
include_once '../../Include/database.php';

// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

// Initialize variables
$providerId = $_GET['provider_id'] ?? null;
$providerDetails = null;
$reviews = [];

// Fetch provider details from the database
if ($providerId) {
    $query = "SELECT * FROM serviceproviders WHERE provider_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $providerId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $providerDetails = $result->fetch_assoc();
    }
    $stmt->close();

    // Fetch reviews for the provider
    $query = "SELECT r.review, r.rating, r.review_date, u.name AS user_name 
              FROM reviews r 
              JOIN users u ON r.user_id = u.user_id 
              WHERE r.provider_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $providerId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Details</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../Include/CSS/index.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .card-img-top {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            margin: 20px auto;
        }
        .card-body {
            background-color: #f7f7f7;
            border-radius: 0 0 10px 10px;
        }
        .review {
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
            padding: 15px;
            background-color: #ffffff;
        }
        .review-rating {
            font-size: 1.2em;
            color: #f39c12;
        }
        .bg-primary-custom {
            background-color: #007bff;
        }
        .bg-dark-custom {
            background-color: #343a40;
        }
    </style>
</head>
<body>
<header class="bg-primary-custom text-white py-3">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="logo">Home Needs</h2>
            <nav class="navigation">
                <a href="user_dashboard.php" class="nav-link text-white">Home</a>
                <a href="#about" class="nav-link text-white">About</a>
                <a href="services.php" class="nav-link text-white">Services</a>
                <a href="user_booking.php" class="nav-link text-white">Your Booking</a>
                <a href="contact.php" class="nav-link text-white">Contact</a>
                <a href="profile.php" class="nav-link text-white"><?php echo htmlspecialchars($_SESSION['name']); ?></a>
                <a href="../../index.php" class="btn btn-light">Logout</a>
            </nav>
        </div>
    </div>
</header>

<div class="container py-5">
    <h2 class="mb-4">Provider Details</h2>

    <?php if ($providerDetails): ?>
        <div class="card text-center">
            <?php
            // Construct the image path
            $imagePath = '../../uploads/' . basename($providerDetails['image']);
            // Check if the image file exists
            if (!empty($providerDetails['image']) && file_exists($imagePath)): ?>
                <img src="<?php echo htmlspecialchars($imagePath); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($providerDetails['name']); ?>">
            <?php else: ?>
                <img src="../../uploads/default-provider.jpg" class="card-img-top" alt="Default Image">
            <?php endif; ?>
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($providerDetails['name']); ?></h5>
                <p class="card-text">Profile Description: <?php echo htmlspecialchars($providerDetails['profile_description']); ?></p>
                <p class="card-text">Phone: <?php echo htmlspecialchars($providerDetails['phone']); ?></p>
                <p class="card-text">Address: <?php echo htmlspecialchars($providerDetails['address']); ?></p>
                <p class="card-text">Service Type: <?php echo htmlspecialchars($providerDetails['service_type']); ?></p>
                <p class="card-text">Latitude: <?php echo htmlspecialchars($providerDetails['latitude']); ?></p>
                <p class="card-text">Longitude: <?php echo htmlspecialchars($providerDetails['longitude']); ?></p>
                <a href="services.php" class="btn btn-primary">Back to Services</a>
            </div>
        </div>

        <h3 class="mt-5 mb-4">Reviews</h3>
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review">
                    <div class="review-rating">Rating: <?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?></div>
                    <p><strong><?php echo htmlspecialchars($review['user_name']); ?></strong> - <?php echo htmlspecialchars($review['review_date']); ?></p>
                    <p><?php echo htmlspecialchars($review['review']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No reviews yet.</p>
        <?php endif; ?>
    <?php else: ?>
        <p>Provider details not found.</p>
    <?php endif; ?>
</div>

<footer class="bg-dark-custom text-white py-4">
    <div class="container text-center">
        <p>&copy; 2024 Household Service Providing System. All rights reserved.</p>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

