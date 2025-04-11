<?php
session_start();

// Page Title
$pageTitle = 'Clients';

// Includes
include 'connect.php';
include 'Includes/functions/functions.php'; 
include 'Includes/templates/resecptionest-header.php';

// Check if user is already logged in
if(isset($_SESSION['username_barbershop_Xw211qAAsq4']) && isset($_SESSION['password_barbershop_Xw211qAAsq4'])) {

    // Process form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // ADD NEW CLIENT
        if (isset($_POST['add_new_client'])) {
            $client_fname = test_input($_POST['client_fname']);
            $client_lname = test_input($_POST['client_lname']);
            $client_phone = test_input($_POST['client_phone']);
            $client_email = test_input($_POST['client_email']);

            $flag_add_client_form = 0;

            // Validate inputs
            if (empty($client_fname)) {
                echo '<div class="alert alert-danger" role="alert">First name is required.</div>';
                $flag_add_client_form = 1;
            }

            if (empty($client_lname)) {
                echo '<div class="alert alert-danger" role="alert">Last name is required.</div>';
                $flag_add_client_form = 1;
            }

            if (empty($client_phone)) {
                echo '<div class="alert alert-danger" role="alert">Phone number is required.</div>';
                $flag_add_client_form = 1;
            }

            if (empty($client_email)) {
                echo '<div class="alert alert-danger" role="alert">Email is required.</div>';
                $flag_add_client_form = 1;
            }

            if ($flag_add_client_form == 0) {
                try {
                    $stmt = $con->prepare("INSERT INTO clients(first_name, last_name, phone_number, client_email) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$client_fname, $client_lname, $client_phone, $client_email]);
                    
                    echo '<script>
                            swal("New Client", "The new client has been added successfully.", "success").then(() => {
                                window.location.href = "clients.php";
                            });
                          </script>';
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger" role="alert">Error occurred: ' . $e->getMessage() . '</div>';
                }
            }
        }

        // DELETE CLIENT
        if (isset($_POST['delete_client'])) {
            $client_id = test_input($_POST['client_id']);

            try {
                $stmt = $con->prepare("DELETE FROM clients WHERE client_id = ?");
                $stmt->execute([$client_id]);
                
                echo '<script>
                        swal("Client Deleted", "The client has been deleted successfully.", "success").then(() => {
                            window.location.href = "clients.php";
                        });
                      </script>';
            } catch (Exception $e) {
                echo '<div class="alert alert-danger" role="alert">Error occurred: ' . $e->getMessage() . '</div>';
            }
        }
    }

    // Display clients management interface
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Clients</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i>
            Generate Report
        </a>
    </div>

    <!-- Clients Table -->
    <?php
    $stmt = $con->prepare("SELECT * FROM clients");
    $stmt->execute();
    $rows_clients = $stmt->fetchAll(); 
    ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Clients</h6>
        </div>
        <div class="card-body">
            <!-- Clients Table -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">ID#</th>
                            <th scope="col">First Name</th>
                            <th scope="col">Last Name</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col">E-mail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($rows_clients as $client) : ?>
                            <tr>
                                <td><?= $client['client_id']; ?></td>
                                <td><?= $client['first_name']; ?></td>
                                <td><?= $client['last_name']; ?></td>
                                <td><?= $client['phone_number']; ?></td>
                                <td><?= $client['client_email']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
    // Include Footer
    include 'Includes/templates/footer.php';
} else {
    header('Location: login.php');
    exit();
}
?>
