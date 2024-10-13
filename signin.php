<?php
// Start session
session_start();

// Database connection
$servername = "localhost";
$dbusername = "root";
$password = "";
$dbname = "social_media_app";

$conn = new mysqli($servername, $dbusername, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle sign-in form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sign_in'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO Users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        // User successfully created
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['username'] = $username;
        header("Location: index.php"); // Redirect to the main page
        exit();
    } else {
        $error_message = "Error creating account: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Social Media App</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-color: #fafafa;
            font-family: 'Bahnschrift SemiCondensed', sans-serif; /* Set the font */
        }
        .sign-in-container {
            max-width: 400px; /* Adjusted container width */
            margin: 100px auto;
            padding: 20px;
            background-color: white;
            border: 2px solid #dbdbdb;
            border-radius: 0px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }
        .sign-in-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .sign-in-container .error-message {
            color: red;
            text-align: center;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            width: 175px; /* Adjust logo size as needed */
        }
        .social-login {
            display: flex;
            justify-content: center; /* Center the images in the container */
            align-items: center; /* Align items vertically */
            flex-wrap: wrap; /* Allow wrapping if the container is too small */
        }
        .social-login img {
            max-height: 70px; /* Set the maximum height for both images */
            width: auto; /* Maintain the aspect ratio */
            margin: 0 5px; /* Adjust the margin for spacing */
            cursor: pointer;
            object-fit: contain; /* Ensure images fit within their container */
        }
        .form-group {
            position: relative;
            margin-bottom: 20px;
        }
        .form-control {
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s;
            font-size: 14px; /* Reduced font size */
            padding: 10px; /* Adjusted padding */
        }
        .form-control:focus {
            border-color: #007bff;
        }
        .form-control::placeholder {
            color: transparent; /* Hide placeholder text */
        }
        .form-control:hover {
            opacity: 0.8; /* Slightly increase opacity on hover */
        }
        .floating-label {
            position: absolute;
            left: 15px;
            top: 12px;
            transition: 0.2s;
            opacity: 0.5;
            font-size: 16px;
        }
        .form-control:focus + .floating-label,
        .form-control:not(:placeholder-shown) + .floating-label {
            top: -10px;
            left: 15px;
            font-size: 12px;
            opacity: 1;
        }
        footer {
            text-align: center;
            margin-top: 20px;
            padding: 10px 0;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="sign-in-container">
            <div class="logo">
                <img src="logo.png" alt="Logo"> <!-- Replace with your logo path -->
            </div>
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <form action="sign_in.php" method="POST">
                <div class="form-group">
                    <input type="text" class="form-control" name="username" id="username" placeholder=" " required>
                    <label class="floating-label" for="username">Username</label>
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" name="email" id="email" placeholder=" " required>
                    <label class="floating-label" for="email">Email</label>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="password" id="password" placeholder=" " required>
                    <label class="floating-label" for="password">Password</label>
                </div>
                <button type="submit" class="btn btn-primary btn-block" name="sign_in">Sign In</button>
            </form>

            <div class="text-center mt-3">_____________   OR   _____________</div><br>
            <div class="social-login text-center mt-2">
                <a href="your_google_auth_url"><img src="google.png" alt="Google Sign In"></a> <!-- Replace with actual Google sign-in URL -->
                <a href="your_facebook_auth_url"><img src="facebook.png" alt="Facebook Sign In"></a> <!-- Replace with actual Facebook sign-in URL -->
            </div>

            <div class="text-center mt-3">
                <p>Already have an account? <a href="login.php">Log In here</a></p>
            </div>
        </div>

        <!-- App Download Links -->
        <div class="download-links-container mt-4 text-center">
            <p>Get the app</p>
            <div class="download-links">
                <a href="https://play.google.com/store/apps/details?id=your_app_id" target="_blank">
                    <img src="playstore-logo.png" alt="Get it on Google Play">
                </a>
                <a href="https://apps.apple.com/us/app/your-app-id" target="_blank">
                    <img src="appstore-logo.png" alt="Download on the App Store">
                </a>
                <a href="https://www.microsoft.com/store/apps/your-app-id" target="_blank">
                    <img src="microsoftstore-logo.png" alt="Get it from Microsoft Store">
                </a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
