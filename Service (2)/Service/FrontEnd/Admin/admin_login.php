<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e0e7ff; /* Light Blue background */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        .login-container h3 {
            margin-bottom: 20px;
            color: #1e3a8a; /* Dark Blue Text */
            font-weight: 600;
            text-align: center;
        }
        .login-container .form-label {
            color: #555;
            font-weight: 500;
        }
        .login-container .form-control {
            border-radius: 8px;
            box-shadow: none;
            border-color: #ddd;
        }
        .login-container .form-control:focus {
            border-color: #3b82f6; /* Blue focus */
            box-shadow: 0 0 5px rgba(59, 130, 246, 0.5);
        }
        .btn-primary {
            background-color: #3b82f6; /* Blue button */
            border-color: #3b82f6;
            border-radius: 8px;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(59, 130, 246, 0.3);
        }
        .btn-primary:hover {
            background-color: #2563eb; /* Darker blue */
            border-color: #2563eb;
        }
        .btn-secondary {
            background-color: #ef4444; /* Red button */
            border-color: #ef4444;
            border-radius: 8px;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(239, 68, 68, 0.3);
        }
        .btn-secondary:hover {
            background-color: #dc2626; /* Darker red */
            border-color: #dc2626;
        }
        .text-muted {
            color: #888;
            text-align: center;
            margin-top: 15px;
        }
        .text-muted a {
            color: #3b82f6; /* Blue link */
            text-decoration: none;
        }
        .text-muted a:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        function closeTab() {
            window.location.href = '../../index.php';
        }
    </script>
</head>
<body>

<div class="login-container">
    <h3>Admin Login</h3>
    <form action="login_backend.php" method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 mb-2">Login</button>
        <button type="button" class="btn btn-secondary w-100" onclick="closeTab()">Close</button>
    </form>
</div>

</body>
</html>
