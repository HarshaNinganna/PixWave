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
    $mobile = $_POST['mobile'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate OTP (backend logic for OTP validation should be implemented separately)
    $mobile_otp = $_POST['mobile_otp'];
    $email_otp = $_POST['email_otp'];

    // Here you should validate the OTPs (not implemented in this snippet)

    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match!";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO Users (username, email, mobile, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $mobile, $hashed_password);

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
            font-family: 'Bahnschrift SemiCondensed', sans-serif;
        }
        .sign-in-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: white;
            border: 2px solid #dbdbdb;
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
        .form-group {
            position: relative;
            margin-bottom: 20px;
        }
        .form-control {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
        }
        .form-control:focus {
            border-color: #007bff;
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
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            width: 175px; /* Adjust logo size as needed */
        }
        .social-login {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }
        .social-login img {
            max-height: 70px; /* Set the maximum height for both images */
            width: auto;
            height: auto;
            margin: 0 0px;
            cursor: pointer;
            object-fit: contain;
        }
        .download-links img {
            width: 240px; /* Adjust size as needed */
            margin: 0 10px;
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
                    <input type="text" class="form-control" name="mobile" id="mobile" placeholder=" " required>
                    <label class="floating-label" for="mobile">Mobile Number</label>
                    <button type="button" class="btn btn-secondary mt-2" onclick="sendMobileOtp()">Send OTP</button>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="mobile_otp" id="mobile_otp" placeholder=" " required>
                    <label class="floating-label" for="mobile_otp">Enter Mobile OTP</label>
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" name="email" id="email" placeholder=" " required>
                    <label class="floating-label" for="email">Email</label>
                    <button type="button" class="btn btn-secondary mt-2" onclick="sendEmailOtp()">Send OTP</button>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="email_otp" id="email_otp" placeholder=" " required>
                    <label class="floating-label" for="email_otp">Enter Email OTP</label>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="password" id="password" placeholder=" " required>
                    <label class="floating-label" for="password">Password</label>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder=" " required>
                    <label class="floating-label" for="confirm_password">Confirm Password</label>
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
    <script>
        function sendMobileOtp() {
            var mobile = document.getElementById("mobile").value;
            $.ajax({
                url: 'send_mobile_otp.php',
                type: 'POST',
                data: { mobile: mobile },
                success: function(response) {
                    alert(response);
                }
            });
        }

        function sendEmailOtp() {
            var email = document.getElementById("email").value;
            $.ajax({
                url: 'send_email_otp.php',
                type: 'POST',
                data: { email: email },
                success: function(response) {
                    alert(response);
                }
            });
        }
    </script>
</body>
</html>
