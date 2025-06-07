<?php
require_once 'db.php';

if (isset($_GET['tracking_id'])) {
    $tracking_id = $_GET['tracking_id'];
    $stmt = $db->prepare("SELECT * FROM tracking_details WHERE tracking_id = ?");
    $stmt->bind_param('s', $tracking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $details = $result->fetch_assoc();
    echo json_encode($details);
} else {
    echo json_encode(['error' => 'No tracking_id provided']);
}
?>