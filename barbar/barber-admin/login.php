<?php
session_start();

// Check if the user is already logged in
if(isset($_SESSION['username_barbershop_Xw211qAAsq4']) && isset($_SESSION['password_barbershop_Xw211qAAsq4'])) {
    // Redirect to the appropriate dashboard based on role
    $role = $_SESSION['role_barbershop_Xw211qAAsq4']; // Assuming you store the role in the session

    switch($role) {
        case 'admin':
            header('Location: index.php');
            exit();
        case 'stylist':
            header('Location: stylist-dashboard.php');
            exit();
        case 'receptionist':
            header('Location: receptionist-dashboard.php');
            exit();
        default:
            // Redirect to a generic dashboard or handle accordingly
            header('Location: index.php'); // Adjust as per your application's flow
            exit();
    }
}

$pageTitle = 'Barber Admin Login';
include 'connect.php';
include 'Includes/functions/functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barber Admin Login</title>
    <!-- FONTS FILE -->
    <link href="Design/fonts/css/all.min.css" rel="stylesheet" type="text/css">

    <!-- Nunito FONT FAMILY FILE -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- CSS FILES -->
    <link href="Design/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="Design/css/main.css" rel="stylesheet">
</head>
<body>
<div class="login">
    <form class="login-container validate-form" name="login-form" method="POST" action="login.php" onsubmit="return validateLogInForm()">
        <span class="login100-form-title p-b-32">
            Barber Admin Login
        </span>

        <!-- PHP SCRIPT WHEN SUBMIT -->

        <?php
        if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signin-button'])) {
            $username = test_input($_POST['username']);
            $password = test_input($_POST['password']);
            $hashedPass = sha1($password); // You should consider using a stronger hashing algorithm like bcrypt

            // Check if User Exists in database
            $stmt = $con->prepare("SELECT admin_id, username, role FROM barber_admin WHERE username = ? AND password = ?");
            $stmt->execute([$username, $hashedPass]);
            $row = $stmt->fetch();
            $count = $stmt->rowCount();

            if($count > 0) {
                $_SESSION['username_barbershop_Xw211qAAsq4'] = $username;
                $_SESSION['password_barbershop_Xw211qAAsq4'] = $password;
                $_SESSION['admin_id_barbershop_Xw211qAAsq4'] = $row['admin_id'];
                $_SESSION['role_barbershop_Xw211qAAsq4'] = $row['role']; // Store the role in the session

                // Redirect based on role
                switch($row['role']) {
                    case 'admin':
                        header('Location: index.php');
                        die();
                    case 'stylist':
                        header('Location: stylist-dashboard.php');
                        die();
                    case 'receptionist':
                        header('Location: receptionist-dashboard.php');
                        die();
                    default:
                        // Redirect to a generic dashboard or handle accordingly
                        header('Location: index.php');
                        die();
                }
            } else {
                ?>

                <div class="alert alert-danger">
                    <button data-dismiss="alert" class="close close-sm" type="button">
                        <span aria-hidden="true">×</span>
                    </button>
                    <div class="messages">
                        <div>Username and/or password are incorrect!</div>
                    </div>
                </div>

                <?php
            }
        }
        ?>

        <!-- USERNAME INPUT -->

        <div class="form-input">
            <span class="txt1">Username</span>
            <input type="text" name="username" class = "form-control" oninput = "getElementById('required_username').style.display = 'none'" autocomplete="off">
            <span class="invalid-feedback" id="required_username">Username is required!</span>
        </div>

        <!-- PASSWORD INPUT -->

        <div class="form-input">
            <span class="txt1">Password</span>
            <input type="password" name="password" class="form-control" oninput = "getElementById('required_password').style.display = 'none'" autocomplete="new-password">
            <span class="invalid-feedback" id="required_password">Password is required!</span>
        </div>

        <!-- SIGN IN BUTTON -->

        <p>
            <button type="submit" name="signin-button">Sign In</button>
        </p>

        <!-- FORGOT YOUR PASSWORD LINK -->

        <span class="forgotPW">Forgot your password ? <a href="#">Reset it here.</a></span>
    </form>
</div>

<!-- Footer -->
<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>Copyright &copy; Barbershop Website by JAIRI IDRISS 2020</span>
        </div>
    </div>
</footer>
<!-- End of Footer -->

<!-- INCLUDE JS SCRIPTS -->
<script src="Design/js/jquery.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="Design/js/bootstrap.bundle.min.js"></script>
<script src="Design/js/sb-admin-2.min.js"></script>
<script src="Design/js/main.js"></script>
</body>
</html>
