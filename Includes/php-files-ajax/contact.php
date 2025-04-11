<?php
    $dsn = 'mysql:host=localhost;dbname=barbershop';
    $user = 'root';
    $pass = '';
    $options = array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    );

    try {
        $con = new PDO($dsn, $user, $pass, $options);
    } catch(PDOException $ex) {
        echo "Failed to connect with database: " . $ex->getMessage();
        die();
    }
?>
<?php
    include "../functions/functions.php";

    if(isset($_POST['contact_name']) && isset($_POST['contact_email']) && isset($_POST['contact_subject']) && isset($_POST['contact_message'])) {
        
        $contact_name = test_input($_POST['contact_name']);
        $contact_email = test_input($_POST['contact_email']);
        $contact_subject = test_input($_POST['contact_subject']);
        $contact_message = test_input($_POST['contact_message']);        

        try {
            // Save to Database
            $stmt = $con->prepare("INSERT INTO contact_submissions (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$contact_name, $contact_email, $contact_subject, $contact_message]);

            // Display success message
            echo "<div class='alert alert-success'>";
            echo "The message has been sent successfully and saved to the database.";
            echo "</div>";
        } catch(PDOException $ex) {
            // Display error message
            echo "<div class='alert alert-warning'>";
            echo "A problem occurred while trying to send the message and save to the database, please try again!";
            echo "</div>";
        }
    }
?>