<?php
// Set PHP default timezone to Philippine Time
date_default_timezone_set('Asia/Manila');

// Include the database connection
require_once 'db.php';

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form data
    $payerName = $_POST['payerName'];
    $transactionType = $_POST['transactionType'];
    $amount = $_POST['amount'];
    $guarantorName = $_POST['guarantorName'];
    $updateStatus = $_POST['updateStatus'];

    // Get current date and time
    $date = date("Y-m-d");
    $time = date("H:i:s");

    // Insert into `transactions`
    $stmt = $db->prepare("INSERT INTO transactions (payer_name, transaction_type, amount, transaction_date, transaction_time, guarantor, update_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdssss", $payerName, $transactionType, $amount, $date, $time, $guarantorName, $updateStatus);

    if ($stmt->execute()) {
        $lastId = $stmt->insert_id;

        // Also insert into `transaction_history`
        $historyStmt = $db->prepare("INSERT INTO transaction_history (transaction_id, payer_name, transaction_type, amount, transaction_date, transaction_time, guarantor, update_status, action) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Insert')");
        $historyStmt->bind_param("issdssss", $lastId, $payerName, $transactionType, $amount, $date, $time, $guarantorName, $updateStatus);
        $historyStmt->execute();
        $historyStmt->close();

        // Redirect back to the main page
        header("Location: ../transaction.php");
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
