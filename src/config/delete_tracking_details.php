<?php
require_once 'db.php';

// Set content type to JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tracking_id = $_POST['tracking_id'] ?? null;
    
    if (empty($tracking_id)) {
        echo json_encode(['success' => false, 'message' => 'No tracking ID provided']);
        exit;
    }

    try {
        // Start transaction for data integrity
        $db->begin_transaction();
        
        // Delete from tracking_history first (child table)
        $historyStmt = $db->prepare("DELETE FROM tracking_history WHERE tracking_id = ?");
        $historyStmt->bind_param('s', $tracking_id);
        $historyStmt->execute();
        $historyStmt->close();
        
        // Delete from tracking_details (parent table)
        $detailsStmt = $db->prepare("DELETE FROM tracking_details WHERE tracking_id = ?");
        $detailsStmt->bind_param('s', $tracking_id);
        
        if ($detailsStmt->execute()) {
            if ($detailsStmt->affected_rows > 0) {
                $db->commit();
                echo json_encode(['success' => true, 'message' => 'Tracking details deleted successfully']);
            } else {
                $db->rollback();
                echo json_encode(['success' => false, 'message' => 'No record found with that tracking ID']);
            }
        } else {
            $db->rollback();
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $db->error]);
        }
        
        $detailsStmt->close();
        
    } catch (Exception $e) {
        $db->rollback();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    
    $db->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>