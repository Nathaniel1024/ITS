<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Municipal Collection Reports</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Municipal Treasury - Collection Report</h1>

<form method="GET" style="text-align:center;">
    <input type="text" name="search" placeholder="Search by payer, receipt, or type" value="<?= $_GET['search'] ?? '' ?>">
    <button type="submit">Search</button>
    <button name="report" value="daily">Daily</button>
    <button name="report" value="weekly">Weekly</button>
    <button name="report" value="monthly">Monthly</button>
    <button name="report" value="quarterly">Quarterly</button>
    <button name="report" value="annually">Annual</button>
</form>

<a href="form.php">+ Add New Collection</a>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Receipt #</th>
                <th>Payer Name</th>
                <th>Revenue Type</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Time</th>
                <th>Collector</th>
                <th>Remarks</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
<?php
$search = $_GET['search'] ?? '';
$report = $_GET['report'] ?? 'daily';

$where = "1"; // always true by default
$search = $conn->real_escape_string($search);
if ($search != '') {
    $where .= " AND (payer_name LIKE '%$search%' OR receipt_no LIKE '%$search%' OR revenue_type LIKE '%$search%')";
}

// Add report filter
switch ($report) {
    case 'weekly':
        $where .= " AND YEARWEEK(collection_date, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'monthly':
        $where .= " AND YEAR(collection_date) = YEAR(CURDATE()) AND MONTH(collection_date) = MONTH(CURDATE())";
        break;
    case 'quarterly':
        $where .= " AND QUARTER(collection_date) = QUARTER(CURDATE()) AND YEAR(collection_date) = YEAR(CURDATE())";
        break;
    case 'annually':
        $where .= " AND YEAR(collection_date) = YEAR(CURDATE())";
        break;
    default:
        $where .= " AND DATE(collection_date) = CURDATE()";
}

// Pagination
$limit = 10;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;

$countQuery = "SELECT COUNT(*) as total FROM collections WHERE $where";
$countResult = $conn->query($countQuery);
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Main query
$query = "SELECT * FROM collections WHERE $where ORDER BY collection_date DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['receipt_no']}</td>
            <td>{$row['payer_name']}</td>
            <td>{$row['revenue_type']}</td>
            <td>₱" . number_format($row['amount'], 2) . "</td>
            <td>{$row['collection_date']}</td>
            <td>{$row['time_collected']}</td>
            <td>{$row['collector']}</td>
            <td>{$row['remarks']}</td>
            <td>
                <a href='form.php?id={$row['id']}'>Edit</a> |
                <a href='delete.php?id={$row['id']}' onclick=\"return confirm('Are you sure?')\">Delete</a>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='9'>No records found.</td></tr>";
}
?>
        </tbody>
    </table>

    <div class="pagination">
        <?php if ($totalPages > 1): ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
