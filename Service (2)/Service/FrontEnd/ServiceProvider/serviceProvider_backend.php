<?php
include '../../Include/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash(mysqli_real_escape_string($conn, $_POST['password']), PASSWORD_BCRYPT); // Hashing the password
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $citizenship_no = mysqli_real_escape_string($conn, $_POST['citizenship_no']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $profile_description = mysqli_real_escape_string($conn, $_POST['profile_description']);
    $latitude = mysqli_real_escape_string($conn, $_POST['latitude']);
    $longitude = mysqli_real_escape_string($conn, $_POST['longitude']);
    $service_type = mysqli_real_escape_string($conn, $_POST['service_type']);

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image = $_FILES['image'];
        $image_name = time() . '-' . basename($image['name']);
        $target_dir = '../../uploads/';
        $target_file = $target_dir . $image_name;
        move_uploaded_file($image['tmp_name'], $target_file);
    } else {
        $image_name = ''; // Set default if no image is uploaded
    }

    // Check for duplicate email
    $check_query = "SELECT * FROM serviceProviders WHERE email = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Insert new provider if no duplicate found
        $insert_query = "INSERT INTO serviceproviders (name, email, password, phone, citizenship_no, address, image, service_type, profile_description, latitude, longitude, registration_date, status)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 0)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sssssssssss", $name, $email, $password, $phone, $citizenship_no, $address, $image_name, $service_type, $profile_description, $latitude, $longitude);
        $stmt->execute();

        echo "<script>alert('Registration successful!'); window.location.href='../../Include/login.php';</script>";
    } else {
        echo "<script>alert('Email is already registered.'); window.location.href='serviceProviderRegisterform.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>