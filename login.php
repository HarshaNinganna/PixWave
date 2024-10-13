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

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $identifier = $_POST['identifier']; // This will be username, phone number, or email
    $password = $_POST['password'];

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT user_id, password FROM Users WHERE username = ? OR phone_number = ? OR email = ?");
    $stmt->bind_param("sss", $identifier, $identifier, $identifier);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $identifier; // Changed to identifier for better clarity
            header("Location: index.php"); // Redirect to the main page
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "No user found with that identifier.";
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
    <title>Login - Social Media App</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
    background-color: #fafafa;
    font-family: 'Bahnschrift SemiCondensed', sans-serif; /* Set the font */
}
.login-container {
            max-width: 400px; /* Adjusted container width */
            margin: 100px auto;
            padding: 20px;
            background-color: white;
            border: 2px solid #dbdbdb;
            border-radius: 0px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-container .error-message {
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
        .forgot-password {
            text-align: center;
            margin-top: 10px;
        }
        .forgot-password a {
            color: #0095f6;
            text-decoration: none;
        }
        .forgot-password a:hover {
            text-decoration: underline;
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
    height: auto; /* Maintain the aspect ratio */
    margin: 0 0px; /* Adjust the margin to reduce space between buttons */
    cursor: pointer;
    object-fit: contain; /* Ensure images fit within their container */
}

        .download-links img {
            width: 240px; /* Adjust size as needed */
            margin: 0 10px;
        }
        /* Floating label styles */
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
        .show-password {
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 35px; /* Adjust position */
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="login-container">
            <div class="logo">
                <img src="logo.png" alt="Logo"> <!-- Replace with your logo path -->
            </div>
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <input type="text" class="form-control" name="identifier" id="identifier" placeholder=" " required>
                    <label class="floating-label" for="identifier">Username, Phone Number, or Email</label>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="password" id="password" placeholder=" " required>
                    <label class="floating-label" for="password">Password</label>
                    <span class="show-password" id="togglePassword">Show</span>
                </div>
                <button type="submit" class="btn btn-primary btn-block" name="login">Login</button>
            </form>

            <div class="text-center mt-3">_____________   OR   _____________</div><br>
            <div class="social-login text-center mt-2">
                <a href="your_google_auth_url"><img src="google.png" alt="Google Sign In"></a> <br><!-- Replace with actual Google sign-in URL -->
                <a href="your_facebook_auth_url"><img src="facebook.png" alt="Facebook Sign In"></a><!-- Replace with actual Facebook sign-in URL -->
            </div>

            <div class="forgot-password">
                <a href="forgot_password.php">Forgot password?</a>
            </div>
        </div>

        <div class="text-center mt-3">
            <p>Don't have an account? <a href="signin.php">Sign In here</a></p>
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
        // Show password toggle
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', () => {
            // Toggle the type attribute
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            // Toggle the eye slash icon
            togglePassword.textContent = togglePassword.textContent === 'Show' ? 'Hide' : 'Show';
        });
    </script>
 
</body>
</html>
