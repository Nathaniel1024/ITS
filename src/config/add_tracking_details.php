<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaction_id = intval($_POST['transaction_id']);
    $tracking_id = uniqid('TRK-');
    $department = $_POST['department'];
    $payment_method = $_POST['payment_method'];
    $status = $_POST['status'];
    $treasurer_remarks = $_POST['treasurer_remarks'];
    $date_made = date('Y-m-d H:i:s');

    $stmt = $db->prepare("INSERT INTO tracking_details (transaction_id, tracking_id, department, payment_method, status, treasurer_remarks, date_made) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('issssss', $transaction_id, $tracking_id, $department, $payment_method, $status, $treasurer_remarks, $date_made);

    if ($stmt->execute()) {
        // Optionally log to tracking_history
        $desc = "Created tracking details (Status: $status, Dept: $department)";
        $user = $_SESSION['username'] ?? 'system';
        $history = $db->prepare("INSERT INTO tracking_history (tracking_id, changed_by, change_description) VALUES (?, ?, ?)");
        $history->bind_param('sss', $tracking_id, $user, $desc);
        $history->execute();

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
    $stmt->close();
    $db->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>