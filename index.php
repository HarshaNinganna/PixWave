<?php
// Start session to handle login/logout
session_start();

// Assuming user is logged in with session
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : 'Guest';

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

// Ensure the 'uploads' directory exists
if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
}

// Handle form submission for creating a post
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_post'])) {
    if ($isLoggedIn) {
        $user_id = $_SESSION['user_id']; // Use session for logged-in user
        $content = $_POST['content'];
        $image = '';

        // Handle image upload if provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image = 'uploads/' . basename($_FILES['image']['name']);
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $image)) {
                echo "Error uploading image.";
            }
        }

        // Insert the post into the database
        if (!empty($content)) {
            $sql = "INSERT INTO Posts (user_id, content" . (!empty($image) ? ", post_image" : "") . ") VALUES ('$user_id', '$content'" . (!empty($image) ? ", '$image'" : "") . ")";
            
            if ($conn->query($sql) === TRUE) {
                echo "New post created successfully";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Content cannot be empty.";
        }
    } else {
        echo "You need to be logged in to create a post.";
    }
}

// Fetch posts from the database
$sql = "SELECT p.content, p.created_at, p.post_image, u.username
        FROM Posts p
        JOIN Users u ON p.user_id = u.user_id
        ORDER BY p.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Media App</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">SocialApp</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Notifications</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Messages</a>
                    </li>
                </ul>
                <form class="d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                </form>
                <!-- Dropdown Button -->
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="profileMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <?= $username ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileMenu">
                        <li><a class="dropdown-item" href="#">Update Profile</a></li>
                        <li><a class="dropdown-item" href="#">Settings</a></li>
                        <li><a class="dropdown-item" href="#">About</a></li>
                        <li><a class="dropdown-item" href="#">Notification Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="card">
                    <img src="https://via.placeholder.com/100" class="card-img-top" alt="Profile Picture">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?= $username ?></h5>
                        <p class="card-text">Bio or short description here.</p>
                    </div>
                </div>
                <div class="list-group mt-4">
                    <a href="#" class="list-group-item list-group-item-action active">Friends</a>
                    <a href="#" class="list-group-item list-group-item-action">Friend 1</a>
                    <a href="#" class="list-group-item list-group-item-action">Friend 2</a>
                    <a href="#" class="list-group-item list-group-item-action">Friend 3</a>
                </div>
            </div>

            <!-- Feed Section -->
            <div class="col-lg-9">
                <!-- Create Post -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Create a Post</h5>
                        <form action="" method="POST" enctype="multipart/form-data">
                            <textarea class="form-control mb-3" name="content" rows="3" placeholder="What's on your mind?"></textarea>
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-primary" name="create_post" type="submit">Post</button>
                                <input type="file" class="form-control-file" name="image">
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Display Posts -->
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title"><?= $row['username'] ?></h5>
                                <p><?= $row['content'] ?></p>
                                <?php if (!empty($row['post_image'])): ?>
                                    <img src="<?= $row['post_image'] ?>" class="img-fluid rounded mb-2" alt="Post Image">
                                <?php endif; ?>
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-outline-primary">Like</button>
                                    <button class="btn btn-outline-secondary">Comment</button>
                                    <button class="btn btn-outline-success">Share</button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No posts to display</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Popper -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="app.js"></script>
</body>
</html>
