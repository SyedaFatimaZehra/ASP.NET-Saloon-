<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username_barbershop_Xw211qAAsq4']) || !isset($_SESSION['password_barbershop_Xw211qAAsq4'])) {
    header('Location: login.php');
    exit();
}

// Page Title
$pageTitle = 'Dashboard';

// Includes
include 'connect.php';
include 'Includes/functions/functions.php'; 
include 'Includes/templates/header.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i>
            Generate Report
        </a>
    </div>

    <!-- Appointment Tables -->
    <div class="card shadow mb-4">
        <div class="card-header tab" style="padding: 0px !important;background: #36b9cc!important">
            <button class="tablinks active" onclick="openTab(event, 'Upcoming')">
                Upcoming Bookings
            </button>
            <button class="tablinks" onclick="openTab(event, 'All')">
                All Bookings
            </button>
            <button class="tablinks" onclick="openTab(event, 'Canceled')">
                Canceled Bookings
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <!-- Upcoming Bookings Table -->
                <table class="table table-bordered tabcontent" id="Upcoming" style="display:table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Start Time</th>
                            <th>Booked Services</th>
                            <th>End Time Expected</th>
                            <th>Client</th>
                            <th>Employee</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $con->prepare("SELECT * 
                                                FROM appointments a
                                                JOIN clients c ON a.client_id = c.client_id
                                                WHERE a.start_time >= :current_time
                                                AND a.canceled = 0
                                                ORDER BY a.start_time");
                        $stmt->execute(array(':current_time' => date('Y-m-d H:i:s')));
                        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $count = $stmt->rowCount();

                        if ($count == 0) {
                            echo "<tr><td colspan='6' style='text-align:center;'>List of your upcoming bookings will be presented here</td></tr>";
                        } else {
                            foreach ($rows as $row) {
                                echo "<tr>";
                                echo "<td>{$row['start_time']}</td>";
                                echo "<td>";
                                $stmtServices = $con->prepare("SELECT service_name
                                                              FROM services s
                                                              JOIN services_booked sb ON s.service_id = sb.service_id
                                                              WHERE sb.appointment_id = :appointment_id");
                                $stmtServices->execute(array(':appointment_id' => $row['appointment_id']));
                                $rowsServices = $stmtServices->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($rowsServices as $rowsService) {
                                    echo "- {$rowsService['service_name']}<br>";
                                }
                                echo "</td>";
                                echo "<td>{$row['end_time_expected']}</td>";
                                echo "<td><a href='#'>{$row['client_id']}</a></td>";
                                echo "<td>";
                                $stmtEmployees = $con->prepare("SELECT first_name, last_name
                                                              FROM employees e
                                                              WHERE e.employee_id = :employee_id");
                                $stmtEmployees->execute(array(':employee_id' => $row['employee_id']));
                                $rowsEmployees = $stmtEmployees->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($rowsEmployees as $rowsEmployee) {
                                    echo "{$rowsEmployee['first_name']} {$rowsEmployee['last_name']}";
                                }
                                echo "</td>";
                                echo "<td>";
                                $cancel_data = "cancel_appointment_{$row['appointment_id']}";
                                ?>
                                <ul class="list-inline m-0">
                                    <!-- CANCEL BUTTON -->
                                    <li class="list-inline-item" data-toggle="tooltip" title="Cancel Appointment">
                                        <button class="btn btn-danger btn-sm rounded-0" type="button" data-toggle="modal" data-target="#<?php echo $cancel_data; ?>" data-placement="top">
                                            <i class="fas fa-calendar-times"></i>
                                        </button>
                                        <!-- CANCEL MODAL -->
                                        <div class="modal fade" id="<?php echo $cancel_data; ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $cancel_data; ?>" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Cancel Appointment</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to cancel this appointment?</p>
                                                        <div class="form-group">
                                                            <label>Tell Us Why?</label>
                                                            <textarea class="form-control" id="appointment_cancellation_reason_<?php echo $row['appointment_id']; ?>"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                                                        <button type="button" data-id="<?php echo $row['appointment_id']; ?>" class="btn btn-danger cancel_appointment_button">Yes, Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <?php
                                echo "</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>

                <!-- All Bookings Table -->
                <table class="table table-bordered tabcontent" id="All" width="100%" cellspacing="0" style="display:none;">
                    <!-- Your code for displaying all bookings -->
                </table>

                <!-- Canceled Bookings Table -->
                <table class="table table-bordered tabcontent" id="Canceled" width="100%" cellspacing="0" style="display:none;">
                    <!-- Your code for displaying canceled bookings -->
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// Include Footer
include 'Includes/templates/footer.php';
?>
