<?php
session_start();

// Page Title
$pageTitle = 'Service Categories';

// Includes
include 'connect.php';
include 'Includes/functions/functions.php'; 
include 'Includes/templates/resecptionest-header.php';

// Check If user is already logged in
if (isset($_SESSION['username_barbershop_Xw211qAAsq4']) && isset($_SESSION['password_barbershop_Xw211qAAsq4'])) {
    // Fetch Service Categories Data
    try {
        $stmt = $con->prepare("SELECT * FROM service_categories");
        $stmt->execute();
        $rows_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
?>
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Service Categories</h1>
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i>
                Generate Report
            </a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Service Categories</h6>
            </div>
            <div class="card-body">

                <!-- Categories Table -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Category ID</th>
                                <th>Category Name</th>
                                <th>Manage</th>
                            </tr>
                        </thead> 
                        <tbody>
                            <?php
                            foreach ($rows_categories as $category) {
                                echo "<tr>";
                                    echo "<td>" . htmlspecialchars($category['category_id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($category['category_name']) . "</td>";
                                    echo "<td>";
                                        // Manage buttons for Edit and Delete are removed
                                        if (strtolower($category["category_name"]) != "uncategorized") {
                                            echo "<ul>";
                                            echo "<li class='list-inline-item' data-toggle='tooltip' title='No Actions Available'>";
                                            echo "<button class='btn btn-secondary btn-sm rounded-0' disabled><i class='fa fa-ban'></i></button>";
                                            echo "</li>";
                                            echo "</ul>";
                                        }
                                    echo "</td>";
                                echo "</tr>";
                            }
                            ?>
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
