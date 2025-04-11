<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username_barbershop_Xw211qAAsq4'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit();
}

// Include necessary files
include 'connect.php'; // Assuming this file connects to your database
include 'Includes/functions/functions.php'; // Assuming this file contains your utility functions

// Fetch user details from session
$username = $_SESSION['username_barbershop_Xw211qAAsq4'];
$admin_id = $_SESSION['admin_id_barbershop_Xw211qAAsq4'];

// Fetch user details from database
$stmt = $con->prepare("SELECT * FROM barber_admin WHERE admin_id = ?");
$stmt->execute([$admin_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Define the redirect pages based on user roles
$redirectPages = [
    'admin' => 'index.php',
    'stylist' => 'stylist-dashboard.php',
    'receptionist' => 'receptionist-dashboard.php',
];

// Get the user role
$userRole = $user['role'];

// Determine the redirect page based on user role
$redirectPage = isset($redirectPages[$userRole]) ? $redirectPages[$userRole] : 'index.php';

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-profile'])) {
    $new_username = test_input($_POST['new-username']);
    $new_email = test_input($_POST['new-email']);
    $new_full_name = test_input($_POST['new-full-name']);
    $new_role = test_input($_POST['new-role']);
    
    // Check if a new password is provided
    if (!empty($_POST['new-password'])) {
        $new_password = sha1(test_input($_POST['new-password'])); // Save password in SHA1 format
        // Update user details in database with new password
        $update_stmt = $con->prepare("UPDATE barber_admin SET username = ?, email = ?, full_name = ?, password = ?, role = ? WHERE admin_id = ?");
        $update_stmt->execute([$new_username, $new_email, $new_full_name, $new_password, $new_role, $admin_id]);
    } else {
        // Update user details in database without changing password
        $update_stmt = $con->prepare("UPDATE barber_admin SET username = ?, email = ?, full_name = ?, role = ? WHERE admin_id = ?");
        $update_stmt->execute([$new_username, $new_email, $new_full_name, $new_role, $admin_id]);
    }

    // Update session with new username if changed
    if ($new_username !== $username) {
        $_SESSION['username_barbershop_Xw211qAAsq4'] = $new_username;
        $username = $new_username; // Update current session username
    }

    // Redirect to the appropriate dashboard page after successful update
    header("Location: $redirectPage");
    exit();
}

// Handle delete profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete-profile'])) {
    // Perform delete operation
    $delete_stmt = $con->prepare("DELETE FROM barber_admin WHERE admin_id = ?");
    $delete_stmt->execute([$admin_id]);

    // Clear session and redirect to login page
    session_destroy();
    header('Location: login.php');
    exit();
}

$pageTitle = 'Profile Page';
?>

<!-- Profile Page Content -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Your custom CSS -->
    <link rel="stylesheet" href="path/to/your/custom.css">
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .profile-container {
            position: relative;
        }
        .cancel-button {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #dc3545; /* Red color */
            text-decoration: none; /* Remove underline */
        }
        .form-group {
            margin-bottom: 1.5rem; /* Adjust spacing between form fields */
        }
        .btn-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2 class="text-center">Welcome, <?php echo $username; ?>!</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="profile.php">
                        <div class="form-group">
                            <label for="new-username">New Username:</label>
                            <input type="text" id="new-username" name="new-username" class="form-control" value="<?php echo $user['username']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="new-email">New Email:</label>
                            <input type="email" id="new-email" name="new-email" class="form-control" value="<?php echo $user['email']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="new-full-name">New Full Name:</label>
                            <input type="text" id="new-full-name" name="new-full-name" class="form-control" value="<?php echo $user['full_name']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="new-password">New Password:</label>
                            <input type="password" id="new-password" name="new-password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="new-role">New Role:</label>
                            <input type="text" id="new-role" name="new-role" class="form-control" value="<?php echo $user['role']; ?>" required>
                        </div>
                        <div class="btn-container">
                            <button type="submit" name="update-profile" class="btn btn-primary">Update Profile</button>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmDeleteModal">Delete Profile</button>
                        </div>
                    </form>
                    <a href="index.php" class="cancel-button"><i class="fas fa-times-circle"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Profile Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Delete Profile</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete your profile? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form method="POST" action="profile.php">
                    <button type="submit" name="delete-profile" class="btn btn-danger">Delete Profile</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
