<?php
session_start();

// Page Title
$pageTitle = 'Clients';

// Includes
include 'connect.php';
include 'Includes/functions/functions.php'; 
include 'Includes/templates/header.php';

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
            <a href="#" class="btn btn-success btn-sm mb-3" data-toggle="modal" data-target="#addClientModal"><i class="fa fa-plus"></i> Add New Client</a>

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
                            <th scope="col">Actions</th>
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
                                <td>
                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editClientModal<?= $client['client_id']; ?>"><i class="fa fa-edit"></i> Edit</button>
                                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteClientModal<?= $client['client_id']; ?>"><i class="fa fa-trash"></i> Delete</button>
                                </td>
                            </tr>

                            <!-- Edit Client Modal -->
                            <div class="modal fade" id="editClientModal<?= $client['client_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editClientModalLabel<?= $client['client_id']; ?>" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form method="post">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editClientModalLabel<?= $client['client_id']; ?>">Edit Client</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="client_id" value="<?= $client['client_id']; ?>">
                                                <div class="form-group">
                                                    <label for="edit_fname">First Name</label>
                                                    <input type="text" class="form-control" id="edit_fname" name="client_fname" value="<?= $client['first_name']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="edit_lname">Last Name</label>
                                                    <input type="text" class="form-control" id="edit_lname" name="client_lname" value="<?= $client['last_name']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="edit_phone">Phone Number</label>
                                                    <input type="text" class="form-control" id="edit_phone" name="client_phone" value="<?= $client['phone_number']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="edit_email">Email</label>
                                                    <input type="email" class="form-control" id="edit_email" name="client_email" value="<?= $client['client_email']; ?>" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="submit" name="edit_client" class="btn btn-primary">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Client Modal -->
                            <div class="modal fade" id="deleteClientModal<?= $client['client_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteClientModalLabel<?= $client['client_id']; ?>" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form method="post">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteClientModalLabel<?= $client['client_id']; ?>">Delete Client</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete <?= $client['first_name'] . ' ' . $client['last_name']; ?>?
                                            </div>
                                            <div class="modal-footer">
                                                <input type="hidden" name="client_id" value="<?= $client['client_id']; ?>">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <button type="submit" name="delete_client" class="btn btn-danger">Delete</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Client Modal -->
<div class="modal fade" id="addClientModal" tabindex="-1" role="dialog" aria-labelledby="addClientModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="addClientModalLabel">Add New Client</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add_fname">First Name</label>
                        <input type="text" class="form-control" id="add_fname" name="client_fname" required>
                    </div>
                    <div class="form-group">
                        <label for="add_lname">Last Name</label>
                        <input type="text" class="form-control" id="add_lname" name="client_lname" required>
                    </div>
                    <div class="form-group">
                        <label for="add_phone">Phone Number</label>
                        <input type="text" class="form-control" id="add_phone" name="client_phone" required>
                    </div>
                    <div class="form-group">
                        <label for="add_email">Email</label>
                        <input type="email" class="form-control" id="add_email" name="client_email" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="add_new_client" class="btn btn-primary">Add Client</button>
                </div>
            </form>
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
