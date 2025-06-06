<?php

// Set PHP default timezone to Philippine Time
date_default_timezone_set('Asia/Manila');

require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $payerName = $_POST['payerName'];
    $amount = floatval(str_replace(',', '.', $_POST['amount']));
    $transactionType = $_POST['transactionType'];
    $updateStatus = $_POST['updateStatus'];

    // Current date and time
    $transactionDate = date('Y-m-d');
    $transactionTime = date('H:i:s');

    // Step 1: Get guarantor name from original record
    $getStmt = $db->prepare("SELECT guarantor FROM transactions WHERE id = ?");
    $getStmt->bind_param("i", $id);
    $getStmt->execute();
    $result = $getStmt->get_result();
    $row = $result->fetch_assoc();
    $guarantorName = $row ? $row['guarantor'] : '';
    $getStmt->close();

    // Step 2: Update the transaction
    $stmt = $db->prepare("UPDATE transactions SET payer_name=?, amount=?, transaction_type=?, update_status=?, transaction_date=?, transaction_time=? WHERE id=?");
    $stmt->bind_param("sdssssi", $payerName, $amount, $transactionType, $updateStatus, $transactionDate, $transactionTime, $id);

    if ($stmt->execute()) {
        // Step 3: Log updated record to transaction_history
        $logStmt = $db->prepare("INSERT INTO transaction_history (
            transaction_id, payer_name, transaction_type, amount,
            transaction_date, transaction_time, guarantor, update_status, action
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Update')");

        $logStmt->bind_param("issdssss", $id, $payerName, $transactionType, $amount, $transactionDate, $transactionTime, $guarantorName, $updateStatus);
        $logStmt->execute();
        $logStmt->close();

        echo "Transaction updated and history recorded.";
    } else {
        echo "Error updating transaction: " . $stmt->error;
    }

    $stmt->close();
    $db->close();
} else {
    echo "Invalid request.";
}
?>
