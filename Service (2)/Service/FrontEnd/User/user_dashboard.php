<?php
session_start(); // Call session_start() once at the very beginning

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

// Get user details from session
$userName = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'User';
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'];
$user_email = $_SESSION['email'];

include_once '../../Include/database.php';
include_once '../../Include/distance_utils.php';

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

// Fetch user latitude and longitude from the database
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
        $message = [
            'type' => 'danger',
            'text' => 'Invalid request data.'
        ];
    }
}

// Check for message in session
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
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
    <title>User Dashboard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../Include/CSS/index.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        header .logo {
            font-weight: 600;
            font-size: 1.5rem;
        }

        .nav-link {
            margin: 0 10px;
            font-weight: 500;
        }

        .hero-section {
            position: relative;
        }

        .carousel-item img {
            height: 500px;
            object-fit: cover;
            filter: brightness(70%);
            transition: filter 0.3s ease;
        }

        .carousel-item img:hover {
            filter: brightness(100%);
        }

        .carousel-caption {
            background: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 10px;
        }

        .carousel-caption h5 {
            font-size: 2rem;
            font-weight: 600;
            color: #fff;
        }

        .carousel-caption p {
            font-size: 1.2rem;
            color: #ddd;
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

        #about {
            background: linear-gradient(to right, #3b82f6, #9333ea, #14b8a6);
        }

        #about h2 {
            color: #ffffff;
        }

        #about h4 {
            color: #ffffff;
        }

        #about p {
            color: #ffffff;
        }

        #about .container {
            border-radius: 12px;
            padding: 2rem;
        }

        #about .transform:hover {
            transform: scale(1.05);
        }

        #about .transition-transform {
            transition: transform 0.3s ease;
        }

        #contact {
            background: linear-gradient(to right, #e0f2fe, #bbd3f3);
        }

        #contact h2 {
            color: #1f2937;
        }

        #contact h3 {
            color: #1f2937;
        }

        #contact p {
            color: #4b5563;
        }

        #contact .bg-white {
            background-color: #ffffff;
        }

        footer {
            background-color: #343a40;
            color: #adb5bd;
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
                    <a href="profile.php" class="nav-link text-white"><?php echo $userName; ?></a>
                    <a href="../../index.php" class="btn btn-light">Logout</a>
                </nav>
            </div>
        </div>
    </header>

    <section class="hero-section">
        <div id="heroCarousel" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="../../images/image4.jpg" class="d-block w-100" alt="Slide 1">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Professional and Reliable Services</h5>
                        <p>We offer a variety of household services to make your life easier.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="../../images/image2.jpg" class="d-block w-100" alt="Slide 3">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Customized Services to Meet Your Needs</h5>
                        <p>Grow Green save life.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="../../images/image1.jpg" class="d-block w-100" alt="Slide 2">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Experienced and Trustworthy Staff</h5>
                        <p>Your home is in good hands with our skilled professionals.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="../../images/image5.jpg" class="d-block w-100" alt="Slide 3">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Customized Services to Meet Your Needs</h5>
                        <p>We tailor our services to match your specific requirements.</p>
                    </div>
                </div>
            </div>
            <a class="carousel-control-prev" href="#heroCarousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#heroCarousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </section>

    <section class="services-section py-5">
    <div class="container my-5">
    <?php if ($message): ?>
        <div class="alert alert-<?php echo htmlspecialchars($message['type']); ?>">
            <?php echo htmlspecialchars($message['text']); ?>
        </div>
    <?php endif; ?>
    <h3 class="mb-4">Our Services</h3>
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
    </section>

    <section id="about" class="py-5 text-center">
        <div class="container">
            <h2 class="mb-4">About Us</h2>
            <p>We provide a range of household services tailored to meet your needs. Our experienced professionals ensure high-quality work and customer satisfaction. Whether you need cleaning, plumbing, painting, or any other service, we are here to help.</p>
            <div class="row">
                <div class="col-md-4">
                    <h4>Our Mission</h4>
                    <p>To deliver exceptional service with a focus on quality and customer satisfaction.</p>
                </div>
                <div class="col-md-4">
                    <h4>Our Vision</h4>
                    <p>To be the leading provider of household services, known for reliability and excellence.</p>
                </div>
                <div class="col-md-4">
                    <h4>Our Values</h4>
                    <p>Integrity, Professionalism, and Customer Focus.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="py-5 text-center">
        <div class="container">
            <h2 class="mb-4">Contact Us</h2>
            <p>If you have any questions or need further assistance, feel free to contact us. We are here to help!</p>
            <p>Email: contact@homeneeds.com</p>
            <p>Phone: 123-456-7890</p>
        </div>
    </section>

    <footer class="py-3 text-center">
        <p>&copy; <?php echo date("Y"); ?> Home Needs. All Rights Reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.min.js"></script>
    <script src="../../Include/JS/script.js"></script>
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
