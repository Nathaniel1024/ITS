<?php
require_once 'config/db.php';

// Ensure the response is JSON
header('Content-Type: application/json');

// Handle connection errors
if ($db->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => $db->connect_error]);
    exit;
}

// Query only the needed columns
$sql = "SELECT sector, budget FROM budget";
$result = $db->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
    $row['budget'] = (float)$row['budget']; // Ensure numeric value
    $data[] = $row;
}

echo json_encode($data);
?>