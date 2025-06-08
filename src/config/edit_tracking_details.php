<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tracking_id = $_POST['tracking_id'];
    $department = $_POST['department'];
    $payment_method = $_POST['payment_method'];
    $status = $_POST['status'];
    $treasurer_remarks = $_POST['treasurer_remarks'];
    $date_made = date('Y-m-d H:i:s');

    $stmt = $db->prepare("UPDATE tracking_details SET department=?, payment_method=?, status=?, treasurer_remarks=?, date_made=? WHERE tracking_id=?");
    $stmt->bind_param('ssssss', $department, $payment_method, $status, $treasurer_remarks, $date_made, $tracking_id);

    if ($stmt->execute()) {
        // Log to tracking_history
        $desc = "Updated tracking details (Status: $status, Dept: $department)";
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