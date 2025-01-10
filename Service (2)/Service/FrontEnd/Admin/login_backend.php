<?php
session_start();
include('../../Include/database.php'); // Make sure to include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // SQL query to fetch the admin details
    $query = "SELECT * FROM admin WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Login successful, redirect to admin dashboard
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_dashboard.php");
    } else {
        // Login failed, show error
        echo "<script>alert('Invalid email or password. Please try again.');</script>";
        echo "<script>window.location.href='admin_login.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
