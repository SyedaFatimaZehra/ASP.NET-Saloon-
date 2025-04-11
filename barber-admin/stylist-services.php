<?php
    ob_start();
    session_start();

    // Page Title
    $pageTitle = 'Services';

    // Includes
    include 'connect.php';
    include 'Includes/functions/functions.php'; 
    include 'Includes/templates/stylist-header.php';

    // Extra JS FILES
    echo "<script src='https://unpkg.com/sweetalert/dist/sweetalert.min.js'></script>";

    // Check If user is already logged in
    if(isset($_SESSION['username_barbershop_Xw211qAAsq4']) && isset($_SESSION['password_barbershop_Xw211qAAsq4']))
    {
?>
        <!-- Begin Page Content -->
        <div class="container-fluid">
    
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Services</h1>
                <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-download fa-sm text-white-50"></i>
                    Generate Report
                </a>
            </div>
            
            <?php
                // Query to fetch services and their categories
                $stmt = $con->prepare("SELECT * FROM services s, service_categories sc WHERE s.category_id = sc.category_id");
                $stmt->execute();
                $rows_services = $stmt->fetchAll();
            ?>

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
                                foreach($rows_services as $service)
                                {
                                    echo "<tr>";
                                        echo "<td>";
                                            echo $service['service_name'];
                                        echo "</td>";
                                        echo "<td>";
                                            echo $service['category_name'];
                                        echo "</td>";
                                        echo "<td style='width:30%'>";
                                            echo $service['service_description'];
                                        echo "</td>";
                                        echo "<td>";
                                            echo $service['service_price'];
                                        echo "</td>";
                                        echo "<td>";
                                            echo $service['service_duration'];
                                        echo "</td>";
                                    echo "</tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->

<?php
    // Include Footer
    include 'Includes/templates/footer.php';
} else {
    header('Location: login.php');
    exit();
}
?>
