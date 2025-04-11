<?php include '../connect.php'; ?>
<?php include '../Includes/functions/functions.php'; ?>


<?php
// Assuming you have retrieved $client_id from a form or another source
$client_id = $_POST['client_id'];

// Check if the client_id exists in clients table
$stmtCheckClient = $con->prepare("SELECT client_id FROM clients WHERE client_id = ?");
$stmtCheckClient->execute([$client_id]);
$clientExists = $stmtCheckClient->fetchColumn();

if (!$clientExists) {
    // Client does not exist, handle error or redirect accordingly
    echo "Error: Client with ID $client_id does not exist.";
    exit;
}

// Proceed with your appointment insertion/update logic
$stmtInsertAppointment = $con->prepare("INSERT INTO appointments (client_id, ...) VALUES (?, ...)");

// Redirect or display success message
header('Location: appointments.php');
exit;
?>
