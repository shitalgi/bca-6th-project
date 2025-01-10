<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "service";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve and sanitize form data
$name = mysqli_real_escape_string($conn, $_POST['name']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = password_hash(mysqli_real_escape_string($conn, $_POST['password']), PASSWORD_DEFAULT);
$phone = mysqli_real_escape_string($conn, $_POST['phone']);
$address = mysqli_real_escape_string($conn, $_POST['address']);
$latitude = !empty($_POST['latitude']) ? mysqli_real_escape_string($conn, $_POST['latitude']) : null;
$longitude = !empty($_POST['longitude']) ? mysqli_real_escape_string($conn, $_POST['longitude']) : null;

// Validate name
if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
    header("Location: userRegistrationForm.php?error=Invalid name format. Only letters and spaces are allowed.");
    exit();
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: userRegistrationForm.php?error=Invalid email format.");
    exit();
}

// Validate phone number (numeric and 10 digits)
if (!preg_match("/^[0-9]{10}$/", $phone)) {
    header("Location: userRegistrationForm.php?error=Phone number must be 10 digits long.");
    exit();
}

// Image upload handling
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $image = basename($_FILES['image']['name']);
    $target_dir = "../../uploads/";
    $target_file = $target_dir . $image;

    // Validate image size (max 2MB)
    if ($_FILES['image']['size'] > 2097152) {
        header("Location: userRegistrationForm.php?error=Image size must be less than 2MB.");
        exit();
    }

    // Validate image file extension
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $valid_extensions = array("jpg", "jpeg", "png");
    if (!in_array($imageFileType, $valid_extensions)) {
        header("Location: userRegistrationForm.php?error=Invalid file type. Only JPG, JPEG, and PNG allowed.");
        exit();
    }

    // Validate if file is an actual image
    $check = getimagesize($_FILES['image']['tmp_name']);
    if ($check === false) {
        header("Location: userRegistrationForm.php?error=File is not an image.");
        exit();
    }

    // Move the uploaded file to the server
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        header("Location: userRegistrationForm.php?error=Sorry, there was an error uploading your file.");
        exit();
    }
} else {
    header("Location: userRegistrationForm.php?error=No file uploaded or an error occurred during the upload.");
    exit();
}

// Validate latitude and longitude
if (($latitude && !is_numeric($latitude)) || ($longitude && !is_numeric($longitude))) {
    header("Location: userRegistrationForm.php?error=Invalid latitude or longitude format.");
    exit();
}

// Insert user data into the database
$sql = "INSERT INTO Users (name, email, password, phone, address, image, latitude, longitude) 
        VALUES ('$name', '$email', '$password', '$phone', '$address', '$image', '$latitude', '$longitude')";

if ($conn->query($sql) === TRUE) {
    header("Location: userRegistrationForm.php?success=Registration successful.");
} else {
    header("Location: userRegistrationForm.php?error=Error: " . $conn->error);
}

$conn->close();
?>
