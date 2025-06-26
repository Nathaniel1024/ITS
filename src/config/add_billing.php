<?php
// Set PHP default timezone to Philippine Time
date_default_timezone_set('Asia/Manila');

// Include the database connection
require_once 'db.php';

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form data
    $payerName = $_POST['payerName'];
    $paymentType = $_POST['paymentType'];
    $amount = $_POST['amount'];
    $signatoryName = $_POST['signatoryName'];
    $paymentOptions = $_POST['paymentOptions'];

    // Get current date and time
    $date = date("Y-m-d");
    $time = date("H:i:s");

    // Insert into `online_billing`
    $stmt = $db->prepare("INSERT INTO online_billing (payer_name, payment_type, amount, payment_date, payment_time, signatory_name, payment_options) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdssss", $payerName, $paymentType, $amount, $date, $time, $signatoryName, $paymentOptions);

    if ($stmt->execute()) {
        $lastId = $stmt->insert_id;

        // Generate invoice code (e.g., INV-2024-000123)
        $year = date("Y");
        $invoiceCode = "INV-" . $year . "-" . str_pad($lastId, 6, "0", STR_PAD_LEFT);

        // Update row with generated invoice code
        $updateStmt = $db->prepare("UPDATE online_billing SET invoice_code = ? WHERE id = ?");
        $updateStmt->bind_param("si", $invoiceCode, $lastId);
        $updateStmt->execute();
        $updateStmt->close();

        // Redirect back to the billing page
        header("Location: ../online_billing.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $db->close();
} else {
    echo "Invalid request.";
}
?>