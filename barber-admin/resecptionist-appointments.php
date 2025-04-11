<?php
ob_start(); // Start output buffering

session_start();

// Check if user is logged in
if (!isset($_SESSION['username_barbershop_Xw211qAAsq4']) || !isset($_SESSION['password_barbershop_Xw211qAAsq4'])) {
    header('Location: login.php');
    exit();
}

// Page Title
$pageTitle = 'Appointments';

// Includes
include 'connect.php'; // Adjust path if necessary
include 'Includes/functions/functions.php'; // Adjust path if necessary
include 'Includes/templates/stylist-header.php'; // Adjust path if necessary

// Handle appointment cancellation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'cancel_appointment') {
    $appointment_id = $_POST['appointment_id'];

    // Update appointment status to canceled
    $stmt_cancel = $con->prepare("UPDATE appointments SET canceled = 1 WHERE appointment_id = :appointment_id");
    $stmt_cancel->execute(array(':appointment_id' => $appointment_id));

    // Redirect to refresh the page after cancellation
    header('Location: appointments.php');
    exit();
}
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Appointments</h1>
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
                <table class="table table-bordered tabcontent" id="Upcoming" style="display: table;" width="100%" cellspacing="0">
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
                            echo "<tr><td colspan='6' style='text-align:center;'>No upcoming bookings found.</td></tr>";
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
                                                        <form method="post" action="appointments.php">
                                                            <input type="hidden" name="appointment_id" value="<?php echo $row['appointment_id']; ?>">
                                                            <input type="hidden" name="action" value="cancel_appointment">
                                                            <button type="submit" class="btn btn-danger">Yes, Cancel</button>
                                                        </form>
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
                <table class="table table-bordered tabcontent" id="All" width="100%" cellspacing="0" style="display: none;">
                    <thead>
                        <tr>
                            <th>Start Time</th>
                            <th>Booked Services</th>
                            <th>End Time Expected</th>
                            <th>Client</th>
                            <th>Employee</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt_all = $con->prepare("SELECT * 
                                                FROM appointments a
                                                JOIN clients c ON a.client_id = c.client_id
                                                ORDER BY a.start_time");
                        $stmt_all->execute();
                        $rows_all = $stmt_all->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($rows_all as $row_all) {
                            echo "<tr>";
                            echo "<td>{$row_all['start_time']}</td>";
                            echo "<td>";
                            $stmtServices_all = $con->prepare("SELECT service_name
                                                          FROM services s
                                                          JOIN services_booked sb ON s.service_id = sb.service_id
                                                          WHERE sb.appointment_id = :appointment_id");
                            $stmtServices_all->execute(array(':appointment_id' => $row_all['appointment_id']));
                            $rowsServices_all = $stmtServices_all->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($rowsServices_all as $rowsService_all) {
                                echo "- {$rowsService_all['service_name']}<br>";
                            }
                            echo "</td>";
                            echo "<td>{$row_all['end_time_expected']}</td>";
                            echo "<td><a href='#'>{$row_all['client_id']}</a></td>";
                            echo "<td>";
                            $stmtEmployees_all = $con->prepare("SELECT first_name, last_name
                                                          FROM employees e
                                                          WHERE e.employee_id = :employee_id");
                            $stmtEmployees_all->execute(array(':employee_id' => $row_all['employee_id']));
                            $rowsEmployees_all = $stmtEmployees_all->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($rowsEmployees_all as $rowsEmployee_all) {
                                echo "{$rowsEmployee_all['first_name']} {$rowsEmployee_all['last_name']}";
                            }
                            echo "</td>";
                            echo "<td>";
                            echo ($row_all['canceled'] == 1) ? "Canceled" : "Scheduled";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Canceled Bookings Table -->
                <table class="table table-bordered tabcontent" id="Canceled" width="100%" cellspacing="0" style="display: none;">
                    <thead>
                        <tr>
                            <th>Start Time</th>
                            <th>Booked Services</th>
                            <th>End Time Expected</th>
                            <th>Client</th>
                            <th>Employee</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt_canceled = $con->prepare("SELECT * 
                                                    FROM appointments a
                                                    JOIN clients c ON a.client_id = c.client_id
                                                    WHERE a.canceled = 1
                                                    ORDER BY a.start_time");
                        $stmt_canceled->execute();
                        $rows_canceled = $stmt_canceled->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($rows_canceled as $row_canceled) {
                            echo "<tr>";
                            echo "<td>{$row_canceled['start_time']}</td>";
                            echo "<td>";
                            $stmtServices_canceled = $con->prepare("SELECT service_name
                                                                FROM services s
                                                                JOIN services_booked sb ON s.service_id = sb.service_id
                                                                WHERE sb.appointment_id = :appointment_id");
                            $stmtServices_canceled->execute(array(':appointment_id' => $row_canceled['appointment_id']));
                            $rowsServices_canceled = $stmtServices_canceled->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($rowsServices_canceled as $rowsService_canceled) {
                                echo "- {$rowsService_canceled['service_name']}<br>";
                            }
                            echo "</td>";
                            echo "<td>{$row_canceled['end_time_expected']}</td>";
                            echo "<td><a href='#'>{$row_canceled['client_id']}</a></td>";
                            echo "<td>";
                            $stmtEmployees_canceled = $con->prepare("SELECT first_name, last_name
                                                                FROM employees e
                                                                WHERE e.employee_id = :employee_id");
                            $stmtEmployees_canceled->execute(array(':employee_id' => $row_canceled['employee_id']));
                            $rowsEmployees_canceled = $stmtEmployees_canceled->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($rowsEmployees_canceled as $rowsEmployee_canceled) {
                                echo "{$rowsEmployee_canceled['first_name']} {$rowsEmployee_canceled['last_name']}";
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
<!-- /.container-fluid -->

<script>
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "table";
        evt.currentTarget.className += " active";
    }
</script>

<?php
include 'Includes/templates/footer.php';
?>
