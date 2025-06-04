<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Municipal Collection Reports</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Municipal Treasury - Collection Report</h1>

<form method="GET">
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
$report = $_GET['report'] ?? 'daily';
switch ($report) {
    case 'weekly':
        $query = "SELECT * FROM collections WHERE YEARWEEK(collection_date, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'monthly':
        $query = "SELECT * FROM collections WHERE YEAR(collection_date) = YEAR(CURDATE()) AND MONTH(collection_date) = MONTH(CURDATE())";
        break;
    case 'quarterly':
        $query = "SELECT * FROM collections WHERE QUARTER(collection_date) = QUARTER(CURDATE()) AND YEAR(collection_date) = YEAR(CURDATE())";
        break;
    case 'annually':
        $query = "SELECT * FROM collections WHERE YEAR(collection_date) = YEAR(CURDATE())";
        break;
    default:
        $query = "SELECT * FROM collections WHERE DATE(collection_date) = CURDATE()";
}
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
    echo "<tr><td colspan='9'>No records found for this period.</td></tr>";
}
?>
        </tbody>
    </table>
</div>
</body>
</html>
