<?php
include_once '../../Include/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../../index.php");
    exit();
}

function getUserDetails($conn, $userId)
{
    $sql = "SELECT name, email, phone, address, image FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $userDetails = $result->fetch_assoc();
    $stmt->close();
    return $userDetails;
}

function updateUserDetails($conn, $name, $email, $phone, $address, $image, $userId)
{
    $sql = "UPDATE users SET name = ?, email = ?, phone = ?, address = ?, image = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssi', $name, $email, $phone, $address, $image, $userId);
    $stmt->execute();
    $stmt->close();
}

$userId = $_SESSION['user_id'] ?? 0;
$userDetails = getUserDetails($conn, $userId);

// Set session variables with user details, ensuring they are not null
$_SESSION['user_name'] = $userDetails['name'] ?? '';
$_SESSION['user_email'] = $userDetails['email'] ?? '';
$_SESSION['user_phone'] = $userDetails['phone'] ?? '';
$_SESSION['user_address'] = $userDetails['address'] ?? '';
$_SESSION['user_image'] = $userDetails['image'] ?? '';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userName = $_POST['name'] ?? '';
    $userEmail = $_POST['email'] ?? '';
    $userPhone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $image = $_FILES['image']['name'] ?? '';

    // Upload profile image
    if ($image) {
        $targetDir = "../../uploads/";
        $targetFile = $targetDir . basename($image);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $image = htmlspecialchars($image);
        } else {
            $message = "Failed to move uploaded file.";
            $image = $_SESSION['user_image'];
        }
    } else {
        $image = $_SESSION['user_image'];
    }

    updateUserDetails($conn, $userName, $userEmail, $userPhone, $address, $image, $userId);

    // Update session details
    $_SESSION['user_name'] = $userName;
    $_SESSION['user_email'] = $userEmail;
    $_SESSION['user_phone'] = $userPhone;
    $_SESSION['user_address'] = $address;
    $_SESSION['user_image'] = $image;

    $message = "Profile updated successfully!";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../Include/CSS/index.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .navbar { background-color: #007bff; }
        .nav-link, .btn-light { font-weight: 600; }
        .profile-form { max-width: 700px; margin: 0 auto; background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        .profile-form h2 { margin-bottom: 20px; color: #333; font-size: 28px; font-weight: 600; }
        .profile-form label { font-weight: 600; color: #555; }
        .profile-form input, .profile-form textarea { width: 100%; padding: 12px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ddd; }
        .profile-form button { width: 100%; padding: 14px; background-color: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: 600; }
        .profile-form button:hover { background-color: #0056b3; }
        .profile-image { text-align: center; margin-bottom: 20px; }
        .profile-image img { border-radius: 50%; width: 150px; height: 150px; object-fit: cover; }
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
                    <a href="contact.php" class="nav-link text-white">Contact</a>
                    <a href="user_booking.php   " class="nav-link text-white">Your Booking</a>
                    <a href="profile.php" class="nav-link text-white"><?php echo htmlspecialchars($_SESSION['user_name']); ?></a>
                    <a href="../../index.php" class="btn btn-light">Logout</a>
                </nav>
            </div>
        </div>
    </header>

    <div class="container py-5">
        <div class="profile-form">
            <h2>Edit Profile</h2>

            <!-- Display messages -->
            <?php if (!empty($message)): ?>
                <div class="alert alert-info">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="profile-image">
                <img src="<?php echo htmlspecialchars(!empty($_SESSION['user_image']) ? '../../uploads/' . $_SESSION['user_image'] : '../../uploads/default.png'); ?>" alt="Profile Image">
            </div>

            <form method="POST" action="profile.php" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user_phone'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" class="form-control" required><?php echo htmlspecialchars($_SESSION['user_address'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="image">Profile Image</label>
                    <input type="file" id="image" name="image" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>

    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p>&copy; 2024 Household Service Providing System. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
