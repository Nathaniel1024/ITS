<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaction_id = intval($_POST['transaction_id']);
    $department = $_POST['department'];
    $payment_method = $_POST['payment_method'];
    $status = $_POST['status'];
    $treasurer_remarks = $_POST['treasurer_remarks'];
    $tracking_id = $_POST['tracking_id'] ?? uniqid('TRK-');
    $date_made = date('Y-m-d H:i:s');

    // Check if tracking details already exist for this transaction
    $check = $db->prepare("SELECT id FROM tracking_details WHERE transaction_id = ?");
    $check->bind_param('i', $transaction_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        // Update existing
        $stmt = $db->prepare("UPDATE tracking_details SET department=?, payment_method=?, status=?, treasurer_remarks=?, date_made=? WHERE transaction_id=?");
        $stmt->bind_param('sssssi', $department, $payment_method, $status, $treasurer_remarks, $date_made, $transaction_id);
        $action = 'Updated';
    } else {
        // Insert new
        $stmt = $db->prepare("INSERT INTO tracking_details (transaction_id, tracking_id, department, payment_method, status, treasurer_remarks, date_made) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('issssss', $transaction_id, $tracking_id, $department, $payment_method, $status, $treasurer_remarks, $date_made);
        $action = 'Created';
    }

    if ($stmt->execute()) {
        // Log to history
        $desc = "$action tracking details (Status: $status, Dept: $department)";
        $history = $db->prepare("INSERT INTO tracking_history (tracking_id, changed_by, change_description) VALUES (?, ?, ?)");
        $user = $_SESSION['username'] ?? 'system';
        $history->bind_param('sss', $tracking_id, $user, $desc);
        $history->execute();

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>