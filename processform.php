<?php
require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $contactNumber = $_POST["phone"];
    $message = $_POST["message"];
    $service = $_POST["service"]; // Get the selected service from the dropdown

    // Validate and sanitize the form data (perform necessary checks)

    // Database Configuration
    $servername = "localhost";
    $username = "gozoomte_instantform";
    $password = "Instant@2231";
    $dbname = "gozoomte_instant";

    // Create a new PDO instance
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        exit;
    }

    // Prepare and execute the database query
    try {
        $stmt = $conn->prepare("INSERT INTO customer_feedback (name, email, contactNumber, service, message) VALUES (:name, :email, :contactNumber, :service, :message)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contactNumber', $contactNumber);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':service', $service); // Bind the selected service
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }

    // Close the database connection
    $conn = null;

    // Send an email with the form data
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'mail.gozoomtech.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'info@instantdatarecovery.com';
        $mail->Password = 'Instant@2231';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Recipients
        $mail->setFrom($email, $name);
        $mail->addAddress('info@instantdatarecovery.com');

        // Email content
        $mail->isHTML(false);
        $mail->Subject = 'Form Submission';
        $mail->Body = "Name: $name<br>"
            . "Email: $email<br>"
            . "Contact Number: $contactNumber<br>"
            . "Service: $service<br>" // Include the service in the email
            . "Message: $message<br>";

        // Send the email
        $mail->send();

        // Email sent successfully
        header("Location: success.html");
        exit;
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        exit;
    }
}
?>