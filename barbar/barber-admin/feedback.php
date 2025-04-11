<?php
session_start();

// Page Title
$pageTitle = 'Admin Feedback';

// Includes
include 'connect.php'; // Adjust path as per your setup
include 'Includes/functions/functions.php'; // Adjust path as per your setup
include 'Includes/templates/header.php'; // Adjust path as per your setup

// Check If user is already logged in
if (isset($_SESSION['username_barbershop_Xw211qAAsq4']) && isset($_SESSION['password_barbershop_Xw211qAAsq4'])) {
    try {
        // Fetch Submissions from Database
        $stmt = $con->prepare("SELECT * FROM contact_submissions");
        $stmt->execute();
        $rows_submissions = $stmt->fetchAll();
        
        // Delete Submission if ID is provided
        if (isset($_POST['id'])) {
            $submissionId = $_POST['id'];
            $deleteStmt = $con->prepare("DELETE FROM contact_submissions WHERE id = ?");
            $deleteStmt->execute([$submissionId]);
            if ($deleteStmt->rowCount() > 0) {
                echo 'success'; // Signal success to AJAX
                exit;
            } else {
                echo 'error'; // Signal error to AJAX
                exit;
            }
        }
    } catch(PDOException $ex) {
        echo "Failed to fetch or delete submissions: " . $ex->getMessage();
        die();
    }
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Admin Feedback</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i>
            Generate Report
        </a>
    </div>

    <!-- Clients Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Contact Form Submissions</h6>
        </div>
        <div class="card-body">
            <!-- Submissions Table -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">ID#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Subject</th>
                            <th scope="col">Message</th>
                            <th scope="col">Submission Date</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows_submissions as $submission): ?>
                            <tr>
                                <td><?php echo $submission['id']; ?></td>
                                <td><?php echo htmlspecialchars($submission['name']); ?></td>
                                <td><?php echo htmlspecialchars($submission['email']); ?></td>
                                <td><?php echo htmlspecialchars($submission['subject']); ?></td>
                                <td><?php echo htmlspecialchars($submission['message']); ?></td>
                                <td><?php echo $submission['submission_date']; ?></td>
                                <td><button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $submission['id']; ?>">Delete</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<!-- JavaScript for Delete Operation -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('.delete-btn').click(function() {
            var submissionId = $(this).data('id');
            if (confirm('Are you sure you want to delete this submission?')) {
                $.ajax({
                    url: '<?php echo $_SERVER['PHP_SELF']; ?>',
                    type: 'POST',
                    data: {
                        id: submissionId
                   } }) 
            }
        });
    });
</script>

<?php
    // Include Footer
    include 'Includes/templates/footer.php'; // Adjust path as per your setup

} else {
    header('Location: login.php');
    exit();
}
?>
