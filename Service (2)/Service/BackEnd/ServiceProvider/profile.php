<?php
// Include database configuration file
include '../../Include/database.php';
session_start();

// Check if the provider is logged in
if (!isset($_SESSION['provider_id'])) {
    header('Location: ../login.php');
    exit;
}

$provider_id = $_SESSION['provider_id'];

// Fetch provider details from the database
$query = "SELECT * FROM serviceproviders WHERE provider_id = ?";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$result = $stmt->get_result();
$provider = $result->fetch_assoc();

if ($provider === null) {
    die('Provider not found.');
}

// Fetch provider reviews
$query = "SELECT reviews.*, users.name AS user_name FROM reviews 
          JOIN users ON reviews.user_id = users.user_id 
          WHERE reviews.provider_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$reviews_result = $stmt->get_result();

// Calculate average rating
$query = "SELECT AVG(rating) AS average_rating FROM reviews WHERE provider_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$rating_result = $stmt->get_result();
$rating_row = $rating_result->fetch_assoc();
$average_rating = round($rating_row['average_rating'], 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update profile details in the database
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $service_type = $_POST['service_type'];
    $profile_description = $_POST['profile_description'];
    $image = $_FILES['image'];

    // Handle image upload
    if ($image['error'] == UPLOAD_ERR_OK) {
        // Get the filename only (basename)
        $imageName = basename($image['name']);
        
        // Define the upload directory and image path
        $imagePath = '../../uploads/' . $imageName;

        // Move the uploaded file to the desired location
        if (move_uploaded_file($image['tmp_name'], $imagePath)) {
            // Save only the image name to the database
            $uploadedImageName = $imageName;
        } else {
            // If the upload fails, fallback to the old image name
            $uploadedImageName = $provider['image'];
        }
    } else {
        // If no new image is uploaded, keep the existing image name
        $uploadedImageName = $provider['image'];
    }

    // Now, save `$uploadedImageName` to the database instead of the full path
    $query = "UPDATE serviceproviders SET name = ?, email = ?, phone = ?, address = ?, service_type = ?, profile_description = ?, image = ? WHERE provider_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("sssssssi", $name, $email, $phone, $address, $service_type, $profile_description, $uploadedImageName, $provider_id);

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!');</script>";
        echo "<script>window.location.href = 'profile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile. Please try again.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($provider['provider_name']); ?> - Profile</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .bg-profile {
            background: url('path/to/your/graphic-background.jpg') no-repeat center center;
            background-size: cover;
        }
        .profile-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
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
                <a href="../../FrontEnd/ServiceProvider/provider_dashboard.php" class="text-gray-300 hover:text-white">Home</a>
                <a href="profile.php" class="text-gray-300 hover:text-white">Profile</a>
                <a href="../../FrontEnd/ServiceProvider/bookingManage.php" class="text-gray-300 hover:text-white">Bookings</a>
                <a href="schedule.php" class="text-gray-300 hover:text-white">Schedule</a>
                <a href="../../index.php" class="text-gray-300 hover:text-white">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-10">
        <h2 class="text-3xl font-bold text-gray-800 mb-4">Profile</h2>

        <div class="bg-profile bg-white p-6 rounded-lg shadow-md mt-6">
            <div class="flex items-center justify-center mb-6">
                <?php 
                    // Correct the path to the image
                    $imagePath = '../../uploads/' . basename($provider['image']);
                    if (!empty($provider['image']) && file_exists($imagePath)): ?>
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Profile Image" class="profile-image">
                <?php else: ?>
                    <img src="path/to/default-image.jpg" alt="Default Profile Image" class="profile-image">
                <?php endif; ?>
            </div>
            <form id="profileForm" action="profile.php" method="POST" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Name</label>
                        <input type="text" id="name" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo htmlspecialchars($provider['name']); ?>" readonly>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                        <input type="email" id="email" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo htmlspecialchars($provider['email']); ?>" readonly>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">Phone</label>
                        <input type="text" id="phone" name="phone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo htmlspecialchars($provider['phone']); ?>" readonly>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="address">Address</label>
                        <input type="text" id="address" name="address" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo htmlspecialchars($provider['address']); ?>" readonly>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="service_type">Service Type</label>
                        <input type="text" id="service_type" name="service_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo htmlspecialchars($provider['service_type']); ?>" readonly>
                    </div>
                    <div class="mb-4 col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="profile_description">Profile Description</label>
                        <textarea id="profile_description" name="profile_description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly><?php echo htmlspecialchars($provider['profile_description']); ?></textarea>
                    </div>
                    <div class="mb-4 col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="image">Profile Image</label>
                        <input type="file" id="image" name="image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
                    </div>
                </div>

                <!-- Edit Button -->
                <button type="button" id="editBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Edit Profile
                </button>

                <!-- Save and Cancel Buttons (Initially Hidden) -->
                <div id="editActions" class="mt-4 hidden">
                    <button type="submit" id="saveBtn" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Save Changes
                    </button>
                    <button type="button" id="cancelBtn" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        <div class="container my-5">
            <h2 class="text-2xl font-bold text-gray-800 mb-4"><?php echo htmlspecialchars($provider['name']); ?></h2>
            <p class="text-lg">Average Rating: <?php echo $average_rating; ?>/5</p>

            <h3 class="text-xl font-semibold mt-4 mb-2">Reviews</h3>
            <?php if ($reviews_result->num_rows > 0): ?>
                <?php while ($review = $reviews_result->fetch_assoc()): ?>
                    <div class="review p-4 border rounded mb-4">
                        <h5 class="font-bold"><?php echo htmlspecialchars($review['user_name']); ?></h5>
                        <p class="text-sm">Rating: <?php echo htmlspecialchars($review['rating']); ?>/5</p>
                        <p><?php echo htmlspecialchars($review['review']); ?></p>
                        <small class="text-gray-500">Reviewed on <?php echo htmlspecialchars($review['review_date']); ?></small>
                        <hr class="my-2">
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No reviews yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const editBtn = document.getElementById('editBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const saveBtn = document.getElementById('saveBtn');
        const editActions = document.getElementById('editActions');
        const profileForm = document.getElementById('profileForm');
        const formFields = profileForm.querySelectorAll('input, textarea');

        editBtn.addEventListener('click', () => {
            formFields.forEach(field => field.removeAttribute('readonly'));
            editActions.classList.remove('hidden');
            editBtn.classList.add('hidden');
        });

        cancelBtn.addEventListener('click', () => {
            formFields.forEach(field => field.setAttribute('readonly', 'readonly'));
            editActions.classList.add('hidden');
            editBtn.classList.remove('hidden');
        });
    </script>
</body>
</html>
