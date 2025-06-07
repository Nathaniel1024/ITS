<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tracking_id = $_POST['tracking_id'];

    // Optionally, delete history first if you want to keep DB integrity
    $db->prepare("DELETE FROM tracking_history WHERE tracking_id=?")->bind_param('s', $tracking_id)->execute();

    $stmt = $db->prepare("DELETE FROM tracking_details WHERE tracking_id=?");
    $stmt->bind_param('s', $tracking_id);

    if ($stmt->execute()) {
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