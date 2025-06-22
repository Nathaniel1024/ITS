<?php

// Set PHP default timezone to Philippine Time
date_default_timezone_set('Asia/Manila');

require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $payerName = $_POST['payerName'];
    $amount = floatval(str_replace(',', '.', $_POST['amount']));
    $paymentType = $_POST['paymentType'];
    $paymentOptions = $_POST['paymentOptions'];

    // Current date and time
    $paymentDate = date('Y-m-d');
    $paymentTime = date('H:i:s');

    // Step 1: Get guarantor name from original record
    $getStmt = $db->prepare("SELECT signatory_name FROM online_billing WHERE id = ?");
    $getStmt->bind_param("i", $id);
    $getStmt->execute();
    $result = $getStmt->get_result();
    $row = $result->fetch_assoc();
    $signatoryName = $row ? $row['signatory_name'] : '';
    $getStmt->close();

    // Step 2: Update the transaction
    $stmt = $db->prepare("UPDATE online_billing SET payer_name=?, amount=?, payment_type=?, payment_options=?, payment_date=?, payment_time=? WHERE id=?");
    $stmt->bind_param("sdssssi", $payerName, $amount, $paymentType, $paymentOptions, $paymentDate, $paymentTime, $id);

    if ($stmt->execute()) {
        echo "Billing record updated successfully.";
    } else {
        echo "Error updating billing record: " . $stmt->error;
    }

    $stmt->close();
    $db->close();
} else {
    echo "Invalid request.";
}
?>
