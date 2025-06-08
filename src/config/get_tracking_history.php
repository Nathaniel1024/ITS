<?php
require_once 'db.php';

if (isset($_GET['tracking_id'])) {
    $tracking_id = $_GET['tracking_id'];
    $stmt = $db->prepare("SELECT * FROM tracking_history WHERE tracking_id = ? ORDER BY changed_at DESC");
    $stmt->bind_param('s', $tracking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    echo json_encode($history);
} else {
    echo json_encode(['error' => 'No tracking_id provided']);
}
?>