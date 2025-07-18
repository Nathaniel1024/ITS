<?php
// receipt.php

if (!isset($_GET['tracking_id']) || !preg_match('/^[A-Za-z0-9\-]+$/', $_GET['tracking_id'])) {
    die("Invalid or missing Tracking ID.");
}

$trackingId = $_GET['tracking_id'];

// DB connection
require_once '../config/db.php';
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Fetch tracking & transaction data
$sql = "
    SELECT td.*, t.payer_name, t.transaction_type, t.amount, t.transaction_date, t.transaction_time, t.guarantor, t.update_status
    FROM tracking_details td
    JOIN transactions t ON td.transaction_id = t.id
    WHERE td.tracking_id = ?
";

$stmt = $db->prepare($sql);
$stmt->bind_param("s", $trackingId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No receipt found for Tracking ID: " . htmlspecialchars($trackingId));
}

$data = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Receipt - <?= htmlspecialchars($data['tracking_id']) ?></title>
    <style>
        body { font-family: monospace; background: #f8f8f8; padding: 30px; }
        .receipt {
            width: 320px;
            background: white;
            padding: 20px;
            margin: auto;
            border: 1px solid #ccc;
        }
        .receipt-header {
            text-align: center;
            font-weight: bold;
        }
        .receipt-line {
            border-top: 1px dashed #aaa;
            margin: 10px 0;
        }
    </style>
</head>
<body onload="window.print()">

<div class="receipt">
    <div class="receipt-header">
        My Office Name<br>
        123 Office Street, City<br>
        ------------------------------
    </div>

    <div>
        <strong>Tracking ID:</strong> <?= htmlspecialchars($data['tracking_id']) ?><br>
        <strong>Department:</strong> <?= htmlspecialchars($data['department']) ?><br>
        <strong>Status:</strong> <?= htmlspecialchars($data['status']) ?><br>
        <strong>Date:</strong> <?= htmlspecialchars(date('Y-m-d', strtotime($data['date_made']))) ?><br>
        <strong>Time:</strong> <?= htmlspecialchars(date('H:i:s', strtotime($data['date_made']))) ?><br>
    </div>

    <div class="receipt-line"></div>

    <div>
        <strong>Payer:</strong> <?= htmlspecialchars($data['payer_name']) ?><br>
        <strong>Type:</strong> <?= htmlspecialchars($data['transaction_type']) ?><br>
        <strong>Amount:</strong> ₱<?= number_format($data['amount'], 2) ?><br>
        <strong>Guarantor:</strong> <?= htmlspecialchars($data['guarantor']) ?><br>
        <strong>Update Status:</strong> <?= htmlspecialchars($data['update_status']) ?><br>
    </div>

    <div class="receipt-line"></div>

    <div style="text-align:center;">
        Thank you for your transaction!<br>
        ------------------------------
    </div>
</div>

</body>
</html>