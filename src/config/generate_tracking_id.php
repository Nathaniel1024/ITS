<?php
require_once 'db.php';

function generateUniqueTrackingID($db) {
    do {
        $timestamp = time();
        $random = mt_rand(100, 999);
        $trackingID = "TRK-" . $timestamp . "-" . $random;

        // Check if it already exists
        $stmt = $db->prepare("SELECT COUNT(*) FROM tracking_details WHERE tracking_id = ?");
        $stmt->bind_param("s", $trackingID);
        $stmt->execute();
        $count = 0;
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    } while ($count > 0);

    return $trackingID;
}

try {
    $uniqueTrackingID = generateUniqueTrackingID($db);
    echo json_encode(["success" => true, "tracking_id" => $uniqueTrackingID]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
