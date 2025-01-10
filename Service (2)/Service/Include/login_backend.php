<?php
session_start();
require_once 'database.php'; // Assuming this is the file with your connection code

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare statement to check in users table
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Store user info in session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['image'] = $user['image'];
        header("Location: ../FrontEnd/User/user_dashboard.php"); // Redirect to homepage or dashboard
        exit;
    } else {
        // Prepare statement to check in serviceproviders table
        $stmt = $conn->prepare("SELECT * FROM serviceproviders WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $provider = $result->fetch_assoc();

        if ($provider) {
            // Debug: Log or print the provider's data to check
            error_log("Provider found: " . print_r($provider, true));

            if (password_verify($password, $provider['password'])) {
                // Store service provider info in session
                $_SESSION['provider_id'] = $provider['provider_id'];
                $_SESSION['name'] = $provider['name'];
                $_SESSION['email'] = $provider['email'];
                $_SESSION['service_type'] = $provider['service_type'];
                $_SESSION['image'] = $provider['image'];
                header("Location: ../FrontEnd/ServiceProvider/provider_dashboard.php"); // Redirect to homepage or dashboard
                exit;
            } else {
                // Debug: Log or print to check the password mismatch
                error_log("Password mismatch for provider email: $email");
                header("Location: login.php?error=Invalid email or password");
                exit;
            }
        } else {
            // Debug: Log or print to check if the provider email was not found
            error_log("No provider found with email: $email");
            header("Location: login.php?error=Invalid email or password");
            exit;
        }
    }
} else {
    header("Location: login.php");
    exit;
}
?>
