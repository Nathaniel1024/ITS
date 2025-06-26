<?php
require_once '../config/db.php';

$filter = $_GET['filterType'] ?? '';
$column = $_GET['column'] ?? '';
$keyword = $_GET['keyword'] ?? '';

$where = "1=1";

// Date filtering
switch ($filter) {
    case 'daily':
        $where .= " AND DATE(date_made) = CURDATE()";
        break;
    case 'weekly':
        $where .= " AND YEARWEEK(date_made, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'monthly':
        $where .= " AND MONTH(date_made) = MONTH(CURDATE()) AND YEAR(date_made) = YEAR(CURDATE())";
        break;
    case 'annually':
        $where .= " AND YEAR(date_made) = YEAR(CURDATE())";
        break;
}

// Column filtering
if ($column && $keyword) {
    $column = $db->real_escape_string($column);
    $keyword = $db->real_escape_string($keyword);
    $where .= " AND `$column` LIKE '%$keyword%'";
}

$query = "SELECT * FROM tracking_details WHERE $where ORDER BY date_made DESC";
$result = $db->query($query);

if ($result->num_rows > 0) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=tracking_details.csv');

    $output = fopen('php://output', 'w');

    // Column headers
    $fields = $result->fetch_fields();
    $headers = [];
    foreach ($fields as $field) {
        $headers[] = $field->name;
    }
    fputcsv($output, $headers);

    // Rows
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
} else {
    echo "No data to export.";
}

$db->close();
exit;
?>
