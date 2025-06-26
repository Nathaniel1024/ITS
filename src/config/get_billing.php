<?php
require_once 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $db->prepare("SELECT * FROM online_billing WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($transaction = $result->fetch_assoc()) {
        echo json_encode($transaction);
    } else {
        echo json_encode(['error' => 'Transaction not found']);
    }

    $stmt->close();
    $db->close();
}
?>
