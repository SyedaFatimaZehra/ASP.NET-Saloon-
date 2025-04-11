<?php
ob_start();
session_start();

// Page Title
$pageTitle = 'Services';

// Includes
include 'connect.php';
include 'Includes/functions/functions.php'; 
include 'Includes/templates/resecptionest-header.php';

// Check If user is already logged in
if (isset($_SESSION['username_barbershop_Xw211qAAsq4']) && isset($_SESSION['password_barbershop_Xw211qAAsq4'])) {
    // Fetch Services Data
    try {
        // Assuming $con is your database connection from connect.php
        $stmt = $con->prepare("
            SELECT 
                services.service_name, 
                categories.category_name, 
                services.service_description, 
                services.service_price, 
                services.service_duration 
            FROM 
                services 
            INNER JOIN 
                service_categories AS categories 
            ON 
                services.category_id = categories.category_id
        ");
        $stmt->execute();
        $rows_services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
?>
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Services</h1>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Services</h6>
            </div>
            <div class="card-body">

                <!-- SERVICES TABLE -->

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Service Name</th>
                            <th scope="col">Service Category</th>
                            <th scope="col">Description</th>
                            <th scope="col">Price</th>
                            <th scope="col">Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($rows_services as $service) {
                            echo "<tr>";
                                echo "<td>" . htmlspecialchars($service['service_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($service['category_name']) . "</td>";
                                echo "<td style='width:30%'>" . htmlspecialchars($service['service_description']) . "</td>";
                                echo "<td>" . htmlspecialchars($service['service_price']) . "</td>";
                                echo "<td>" . htmlspecialchars($service['service_duration']) . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php
    include 'Includes/templates/footer.php';
} else {
    header('Location: login.php');
    exit();
}
?>
