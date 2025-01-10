<?php
// Include database configuration file
include_once '../../Include/database.php';

// Start session
session_start();

// Initialize variables
$serviceType = '';
$providerName = '';
$providerId = ''; // Variable for provider ID
$userName = $_SESSION['user_name'] ?? '';
$userEmail = $_SESSION['user_email'] ?? '';
$userPhone = $_SESSION['user_phone'] ?? '';
$address = $_SESSION['address'] ?? ''; // Default address, will be overwritten if fetched
$appointmentDate = '';
$appointmentTime = '';
$location = '';
$message = null; // Initialize the message variable

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Fetch user details from the database
$query = "SELECT name, email, phone, address FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($user = $result->fetch_assoc()) {
    $userName = $user['name'];
    $userEmail = $user['email'];
    $userPhone = $user['phone'];
    $address = $user['address'];
}
$stmt->close();

// Retrieve service_id and provider_id from POST request
$service_id = $_POST['service_id'] ?? '';
$provider_id = $_POST['provider_id'] ?? '';

// Fetch service name and provider details from database
if (!empty($service_id) && !empty($provider_id)) {
    // Fetch service name based on service_id
    $query = "SELECT service_name FROM services WHERE service_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($service = $result->fetch_assoc()) {
        $serviceType = $service['service_name'];
    }
    $stmt->close();

    // Fetch provider details based on provider_id
    $query = "SELECT name FROM serviceproviders WHERE provider_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $provider_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($provider = $result->fetch_assoc()) {
        $providerName = $provider['name'];
    }
    $stmt->close();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $appointmentDate = $_POST['appointment_date'] ?? '';
    $appointmentTime = $_POST['appointment_time'] ?? '';
    $location = $_POST['location'] ?? '';

    // Validate required fields
    if (empty($serviceType) || empty($providerName) || empty($appointmentDate) || empty($appointmentTime) || empty($provider_id)) {
        $message = [
            'type' => 'danger',
            'text' => 'Please fill in all required fields.'
        ];
    } else {
        // Check if the selected date is within 3 weeks and not in the past
        $current_date = date('Y-m-d');
        $max_date = date('Y-m-d', strtotime('+3 weeks'));

        if ($appointmentDate < $current_date || $appointmentDate > $max_date) {
            $message = [
                'type' => 'danger',
                'text' => 'Error: Appointment date must be within 3 weeks and cannot be in the past.'
            ];
        } else {
            // Check if the time slot is already booked
            $check_query = "SELECT * FROM provider_schedule WHERE provider_id = ? AND available_date = ? AND available_time = ? AND is_booked = 0";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param("iss", $provider_id, $appointmentDate, $appointmentTime);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                $message = [
                    'type' => 'danger',
                    'text' => 'Error: This time slot is not available.'
                ];
            } else {
                // Prepare SQL query to insert booking data
                $query = "INSERT INTO bookings (service_name, provider_name, provider_id, user_name, user_email, user_phone, address, appointment_date, appointment_time, location, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssisssssssi", $serviceType, $providerName, $provider_id, $userName, $userEmail, $userPhone, $address, $appointmentDate, $appointmentTime, $location, $user_id);

                if ($stmt->execute()) {
                    // Mark the time slot as booked
                    $update_query = "UPDATE provider_schedule SET is_booked = 1 WHERE provider_id = ? AND available_date = ? AND available_time = ?";
                    $stmt = $conn->prepare($update_query);
                    $stmt->bind_param("iss", $provider_id, $appointmentDate, $appointmentTime);
                    $stmt->execute();

                    echo "<script>
                    window.location.href = 'user_booking.php';
                  </script>";
                    exit();
                    echo "<script>
                            alert('Booking confirmed successfully.');
                            window.location.href = 'services.php';
                          </script>";
                    exit();
                } else {
                    $message = [
                        'type' => 'danger',
                        'text' => 'There was an error confirming your booking. Please try again.'
                    ];
                }
                $stmt->close();
            }
        }
    }
}
// Handle AJAX request to fetch available dates and times
if (isset($_GET['provider_id']) && isset($_GET['date'])) {
    $providerId = (int)$_GET['provider_id'];
    $selectedDate = $_GET['date'];

    // Fetch available times from database
    $query = "SELECT available_time FROM provider_schedule WHERE provider_id = ? AND available_date = ? AND is_booked = 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $providerId, $selectedDate);
    $stmt->execute();
    $result = $stmt->get_result();

    $availableTimes = [];
    while ($row = $result->fetch_assoc()) {
        $availableTimes[] = $row['available_time'];
    }

    // Return available times as JSON
    echo json_encode(['success' => true, 'schedule' => [$selectedDate => $availableTimes]]);
    exit();
}


// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
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
                    <a href="contact.php" class="nav-link text-white">Contact</a>
                    <a href="profile.php" class="nav-link text-white"><?php echo htmlspecialchars($userName); ?></a>
                    <a href="../../index.php" class="btn btn-light">Logout</a>
                </nav>
            </div>
        </div>
    </header>

    <div class="container py-5 header-spacing">
        <button class="btn btn-secondary back-button" onclick="window.location.href='services.php';">Back</button>
        <div class="booking-form">
            <h2>Booking Form</h2>

            <!-- Display messages -->
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo htmlspecialchars($message['type']); ?>">
                    <?php echo htmlspecialchars($message['text']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="booking.php" onsubmit="return validateForm()">
                <!-- Hidden fields to pass serviceType, providerName, and providerId -->
                <input type="hidden" name="service_id" value="<?php echo htmlspecialchars($service_id); ?>">
                <input type="hidden" name="provider_id" value="<?php echo htmlspecialchars($provider_id); ?>">

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="serviceName">Selected Service</label>
                        <input type="text" id="serviceName" class="form-control" value="<?php echo htmlspecialchars($serviceType); ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="providerName">Service Provider</label>
                        <input type="text" id="providerName" class="form-control" value="<?php echo htmlspecialchars($providerName); ?>" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="userName">Your Name</label>
                        <input type="text" id="userName" name="user_name" class="form-control" value="<?php echo htmlspecialchars($userName); ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="userEmail">Email</label>
                        <input type="email" id="userEmail" name="user_email" class="form-control" value="<?php echo htmlspecialchars($userEmail); ?>" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="userPhone">Phone</label>
                        <input type="tel" id="userPhone" name="user_phone" class="form-control" value="<?php echo htmlspecialchars($userPhone); ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" class="form-control" value="<?php echo htmlspecialchars($address); ?>" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="appointment_date">Appointment Date:</label>
                        <input type="date" name="appointment_date" id="appointment_date" class="form-control" required min="<?php echo $current_date; ?>" max="<?php echo $max_date; ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="appointment_time">Appointment Time:</label>
                        <select name="appointment_time" id="appointment_time" class="form-control" required>
                            <option value="" disabled selected>Select Appointment Time</option>
                            <!-- Dynamically populated options will go here -->
                        </select>
                        <div id="no_time_message" class="text-danger mt-2" style="display: none;"></div> <!-- Message placeholder -->
                    </div>
                   

                </div>

                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" class="form-control" readonly>
                </div>

                <button type="submit" class="btn btn-primary">Confirm Booking</button>
            </form>
        </div>
    </div>

    <footer class="bg-primary text-white text-center py-3">
        <div class="container">
            <p>© 2024 Home Needs. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function validateForm() {
            const appointmentDate = document.getElementById("appointment_date").value;
            const currentDate = new Date();
            const maxDate = new Date();
            maxDate.setDate(currentDate.getDate() + 21); // 3 weeks from today

            if (new Date(appointmentDate) < currentDate) {
                alert("Appointment date cannot be in the past.");
                return false;
            }
            if (new Date(appointmentDate) > maxDate) {
                alert("Appointment date must be within 3 weeks.");
                return false;
            }
            return true; // All checks passed
        }

        // Function to get current location and set it in the location input field
        function setLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var latitude = position.coords.latitude;
                    var longitude = position.coords.longitude;
                    var locationInput = document.getElementById('location');
                    locationInput.value = 'Latitude: ' + latitude + ', Longitude: ' + longitude;
                }, function(error) {
                    console.error('Error getting location: ' + error.message);
                });
            } else {
                console.error('Geolocation is not supported by this browser.');
            }
        }

        // Set location on page load
        document.addEventListener('DOMContentLoaded', function() {
            setLocation();
        });

        document.getElementById('appointment_date').addEventListener('change', function() {
            const selectedDate = this.value;
            const providerId = document.querySelector('input[name="provider_id"]').value;

            if (selectedDate && providerId) {
                fetch(`booking.php?provider_id=${providerId}&date=${selectedDate}`)
                    .then(response => response.json())
                    .then(data => {
                        const appointmentTimeSelect = document.getElementById('appointment_time');
                        const noTimeMessage = document.getElementById('no_time_message');

                        appointmentTimeSelect.innerHTML = ''; // Clear existing options
                        noTimeMessage.style.display = 'none'; // Hide the message initially

                        if (data.success) {
                            // Check if there are available times
                            if (data.schedule[selectedDate] && data.schedule[selectedDate].length > 0) {
                                // Populate available times
                                data.schedule[selectedDate].forEach(time => {
                                    const option = document.createElement('option');
                                    option.value = time;
                                    option.textContent = time;
                                    appointmentTimeSelect.appendChild(option);
                                });
                            } else {
                                // Display message if no available times
                                noTimeMessage.textContent = "The service provider is busy. Please choose a different date.";
                                noTimeMessage.style.display = 'block';
                            }
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => console.error('Error fetching available times:', error));
            }
        });



        // Set min and max dates for appointment date input
        document.addEventListener('DOMContentLoaded', function() {
            const appointmentDateInput = document.getElementById('appointment_date');
            const currentDate = new Date();
            const maxDate = new Date();
            maxDate.setDate(currentDate.getDate() + 21); // Set max date to 3 weeks from today

            // Format dates to yyyy-mm-dd for input fields
            const formattedCurrentDate = currentDate.toISOString().split('T')[0];
            const formattedMaxDate = maxDate.toISOString().split('T')[0];

            appointmentDateInput.min = formattedCurrentDate;
            appointmentDateInput.max = formattedMaxDate;
        });
    </script>
</body>

</html>