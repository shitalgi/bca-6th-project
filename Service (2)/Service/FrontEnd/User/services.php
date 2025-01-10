<?php
// services.php

// Include database configuration file and distance utility functions
include_once '../../Include/database.php';
// include_once '../../Include/distance_utils.php';
include '../../Include/algorithmImplement.php';


// Start session
session_start();

// Initialize variables
$message = null;
$serviceProviders = [];
$showModal = false;
$providerId = null;
$providerName = '';
$serviceType = '';
$serviceId = '';

// Check if there is a message query parameter
if (isset($_GET['message']) && $_GET['message'] === 'success') {
    $message = [
        'type' => 'success',
        'text' => 'Booking confirmed successfully!'
    ];
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../../index.php");
    exit();
}

// Get user details from session
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'];
$user_email = $_SESSION['email'];

// // Fetch user latitude and longitude from the database
$userQuery = "SELECT latitude, longitude FROM users WHERE user_id = ?";
$stmtUser = $conn->prepare($userQuery);
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$userResult = $stmtUser->get_result();
$userLocation = $userResult->fetch_assoc();
$stmtUser->close();

$userLat = $userLocation['latitude'] ?? null;
$userLon = $userLocation['longitude'] ?? null;

// Check for POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['service_id']) && filter_var($_POST['service_id'], FILTER_VALIDATE_INT)) {
        // Fetch service providers based on the selected service
        $service_id = intval($_POST['service_id']);

        // Fetch service type name
        $serviceTypeQuery = "SELECT service_name FROM services WHERE service_id = ?";
        $stmtType = $conn->prepare($serviceTypeQuery);
        $stmtType->bind_param("i", $service_id);
        $stmtType->execute();
        $resultType = $stmtType->get_result();

        if ($resultType->num_rows > 0) {
            $serviceType = $resultType->fetch_assoc()['service_name'];
        }
        $stmtType->close();

        // Fetch service providers based on the service type
        if ($serviceType) {
            $providerQuery = "SELECT * FROM serviceproviders WHERE service_type = ? AND status = 1";
            $stmt = $conn->prepare($providerQuery);
            $stmt->bind_param("s", $serviceType);
            $stmt->execute();
            $providerResult = $stmt->get_result();
            if ($providerResult->num_rows > 0) {
                $serviceProviders = $providerResult->fetch_all(MYSQLI_ASSOC);

                // Check if we have user's location to sort providers
                if ($userLat && $userLon) {
                    // Sort providers by distance
                    $serviceProviders = sort_providers_by_distance($serviceProviders, $userLat, $userLon);
                }

                $_SESSION['serviceProviders'] = $serviceProviders;
                $_SESSION['serviceType'] = $serviceType;
                $_SESSION['service_id'] = $service_id; // Set service_id in the session
                $showModal = true;
            }
            $stmt->close();
        }
    } elseif (isset($_POST['provider_id']) && filter_var($_POST['provider_id'], FILTER_VALIDATE_INT)) {
        // Fetch provider name
        $providerId = intval($_POST['provider_id']);
        $providerNameQuery = "SELECT name FROM serviceproviders WHERE provider_id = ?";
        $stmt = $conn->prepare($providerNameQuery);
        $stmt->bind_param("i", $providerId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $providerName = $result->fetch_assoc()['name'];
        }
        $stmt->close();

        $_SESSION['providerId'] = $providerId;
        $_SESSION['providerName'] = $providerName;
        $showModal = true;
    } else {
        // Handle invalid POST data
        $message = [
            'type' => 'danger',
            'text' => 'Invalid request data.'
        ];
    }
}

// Check for message in session
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Clear message from session
}

// Fetch available services from the database
$query = "SELECT * FROM services";
$result = mysqli_query($conn, $query);

// Close the database connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Services</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../Include/CSS/index.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: #007bff;
        }

        .nav-link,
        .btn-light {
            font-weight: 600;
        }

        .service-card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .service-card:hover {
            transform: translateY(-5px);
        }

        .service-card img {
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }

        .service-card h5 {
            font-weight: 600;
            margin-top: 15px;
            color: #333;
        }

        .service-card p {
            color: #777;
        }

        .service-card button {
            margin-top: 10px;
        }

        .styled-button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .styled-button:hover {
            background-color: #0056b3;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-img-top {
            border-radius: 50%;
            height: 100px;
            width: 100px;
            object-fit: cover;
            margin: 0 auto;
        }

        .card-body {
            text-align: center;
            padding: 1rem;
        }

        .card-title {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .card-text {
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .styled-button {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }

        .btn-link {
            font-size: 0.875rem;
        }

        .error {
            color: red;
            font-size: 0.875rem;
        }
    </style>
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
                    <a href="user_booking.php   " class="nav-link text-white">Your Booking</a>
                    <a href="contact.php" class="nav-link text-white">Contact</a>
                    <a href="profile.php" class="nav-link text-white"><?php echo $user_name; ?></a>
                    <a href="../../index.php" class="btn btn-light">Logout</a>
                </nav>
            </div>
        </div>
    </header>
    <div class="container my-5">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo htmlspecialchars($message['type']); ?>">
                <?php echo htmlspecialchars($message['text']); ?>
            </div>
        <?php endif; ?>
        <h3 class="mb-4">Select a Service</h3>
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card service-card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['service_name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                            <form id="serviceForm" method="POST" action="">
                                <input type="hidden" name="service_id" value="<?php echo htmlspecialchars($row['service_id']); ?>">
                                <button type="submit" class="styled-button">View Providers</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Modal for Service Providers -->
    <div class="modal fade" id="serviceProvidersModal" tabindex="-1" aria-labelledby="serviceProvidersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serviceProvidersModalLabel">
                        Service Providers for <?php echo htmlspecialchars($_SESSION['serviceType'] ?? ''); ?>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <?php if (!empty($_SESSION['serviceProviders'])): ?>
                            <?php foreach ($_SESSION['serviceProviders'] as $provider): ?>
                                <?php if ($provider['distance'] < 20): ?> <!-- Only show providers within 20 km -->
                                    <div class="col-md-4 mb-4">
                                        <div class="card">
                                            <?php
                                            $imagePath = '../../uploads/' . $provider['image'];
                                            $defaultImage = 'default-provider.jpg';
                                            $imageSrc = file_exists($imagePath) && !empty($provider['image']) ? $imagePath : '../../uploads/' . $defaultImage;
                                            ?>
                                            <img src="<?php echo htmlspecialchars($imageSrc); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($provider['name']); ?>">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo htmlspecialchars($provider['name']); ?></h5>
                                                <p class="card-text"><?php echo htmlspecialchars($provider['profile_description']); ?></p>
                                                <p class="card-text">Phone: <?php echo htmlspecialchars($provider['phone']); ?></p>
                                                <p class="card-text">Address: <?php echo htmlspecialchars($provider['address']); ?></p>
                                                <p class="card-text">Distance: <?php echo number_format($provider['distance'], 2); ?> km</p>
                                                <form method="POST" action="booking.php">
                                                    <!-- Ensure session variables are correctly set -->
                                                    <input type="hidden" name="service_id" value="<?php echo htmlspecialchars($_SESSION['service_id'] ?? ''); ?>">
                                                    <input type="hidden" name="provider_id" value="<?php echo htmlspecialchars($provider['provider_id'] ?? ''); ?>">
                                                    <button type="submit" class="btn styled-button">Select</button>
                                                </form>
                                                <a href="provider_details.php?provider_id=<?php echo htmlspecialchars($provider['provider_id']); ?>" class="btn btn-link">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No service providers available for this service.</p>
                        <?php endif; ?>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Show modal if needed
        <?php if ($showModal): ?>
            $(document).ready(function() {
                $('#serviceProvidersModal').modal('show');
            });
        <?php endif; ?>

        // Client-side validation for service form
        document.getElementById('serviceForm').addEventListener('submit', function(event) {
            var serviceId = document.querySelector('input[name="service_id"]').value;
            if (!serviceId || isNaN(serviceId)) {
                alert('Please select a valid service.');
                event.preventDefault(); // Prevent form submission
            }
        });
    </script>
</body>

</html>