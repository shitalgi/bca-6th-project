<?php
// Start the session at the top of the file
session_start();
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../../index.php");
    exit();
}

// Retrieve user's name from the session
$userName = $_SESSION['user_name'] ?? 'Guest'; // Default to 'Guest' if user_name is not set
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Home Needs</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../Include/CSS/index.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            /* background-color: #f8f9fa;
            color: #333; */
        }

        .navbar {
            background-color: #007bff;
        }

        .nav-link,
        .btn-light {
            font-weight: 600;
        }

        .contact-section {
            padding: 60px 0;
            background: linear-gradient(to right, #6c757d, #adb5bd);
            color: white;
        }

        .contact-section h2 {
            font-size: 36px;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            animation: fadeInDown 1s;
        }

        .contact-section p {
            font-size: 18px;
            margin-bottom: 40px;
            animation: fadeIn 1.5s;
        }

        .contact-form {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 1s;
        }

        .contact-form h3 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #007bff;
            font-weight: 600;
        }

        .contact-form .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-bottom: 15px;
            padding: 15px;
        }

        .contact-form button {
            background-color: #007bff;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
        }

        .contact-form button:hover {
            background-color: #0056b3;
        }

        .contact-details {
            display: flex;
            justify-content: space-around;
            margin-top: 40px;
            text-align: center;
            animation: fadeIn 2s;
        }

        .contact-details div {
            flex: 1;
            margin: 20px;
            padding: 20px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
        }

        .contact-details h4 {
            font-size: 22px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .contact-details p {
            font-size: 16px;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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
                    <a href="contact.php" class="nav-link text-white">Contact</a>
                    <a href="user_booking.php   " class="nav-link text-white">Your Booking</a>
                    <a href="profile.php" class="nav-link text-white"><?php echo htmlspecialchars($userName); ?></a>
                    <a href="../../index.php" class="btn btn-light">Logout</a>
                </nav>
            </div>
        </div>
    </header>

    <section class="contact-section">
        <div class="container">
            <h2>Contact Us</h2>
            <p>We would love to hear from you! Please fill out the form below to get in touch with us.</p>

            <div class="contact-form">
                <h3>Get in Touch</h3>
                <form method="POST" action="process_contact.php">
                    <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                    <input type="email" name="email" class="form-control" placeholder="Your Email" required>
                    <textarea name="message" class="form-control" placeholder="Your Message" rows="5" required></textarea>
                    <button type="submit">Send Message</button>
                </form>
            </div>

            <div class="contact-details">
                <div>
                    <h4>Call Us</h4>
                    <p>+123 456 7890</p>
                </div>
                <div>
                    <h4>Email Us</h4>
                    <p>info@homeneeds.com</p>
                </div>
                <div>
                    <h4>Visit Us</h4>
                    <p>123 Main Street, Anytown, USA</p>
                </div>
            </div>
        </div>
    </section>

</body>

</html>
