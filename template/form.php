<?php
include 'db.php';
$id = $_GET['id'] ?? null;
$editing = false;

if ($id) {
    $editing = true;
    $res = $conn->query("SELECT * FROM collections WHERE id=$id");
    $data = $res->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $receipt = $_POST['receipt_no'];
    $payer = $_POST['payer_name'];
    $revenue = $_POST['revenue_type'];
    $amount = $_POST['amount'];
    $date = $_POST['collection_date'];
    $time = $_POST['time_collected'];
    $collector = $_POST['collector'];
    $remarks = $_POST['remarks'];

    if ($editing) {
        $conn->query("UPDATE collections SET receipt_no='$receipt', payer_name='$payer', revenue_type='$revenue', amount='$amount', collection_date='$date', time_collected='$time', collector='$collector', remarks='$remarks' WHERE id=$id");
    } else {
        $conn->query("INSERT INTO collections (receipt_no, payer_name, revenue_type, amount, collection_date, time_collected, collector, remarks) VALUES ('$receipt', '$payer', '$revenue', '$amount', '$date', '$time', '$collector', '$remarks')");
    }
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head><title><?= $editing ? 'Edit' : 'Add' ?> Collection</title></head>
<body>
<h2><?= $editing ? 'Edit' : 'Add New' ?> Collection</h2>
<form method="POST">
    <input name="receipt_no" placeholder="Receipt #" value="<?= $data['receipt_no'] ?? '' ?>" required><br>
    <input name="payer_name" placeholder="Payer Name" value="<?= $data['payer_name'] ?? '' ?>" required><br>
    <input name="revenue_type" placeholder="Revenue Type" value="<?= $data['revenue_type'] ?? '' ?>" required><br>
    <input type="number" step="0.01" name="amount" placeholder="Amount" value="<?= $data['amount'] ?? '' ?>" required><br>
    <input type="date" name="collection_date" value="<?= $data['collection_date'] ?? '' ?>" required><br>
    <input type="time" name="time_collected" value="<?= $data['time_collected'] ?? '' ?>" required><br>
    <input name="collector" placeholder="Collector" value="<?= $data['collector'] ?? '' ?>" required><br>
    <input name="remarks" placeholder="Remarks" value="<?= $data['remarks'] ?? '' ?>"><br>
    <button type="submit"><?= $editing ? 'Update' : 'Save' ?></button>
</form>
<a href="index.php">Back</a>
</body>
</html>
