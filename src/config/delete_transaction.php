<?php
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Step 1: Fetch the transaction details first
    $selectStmt = $db->prepare("SELECT * FROM transactions WHERE id = ?");
    $selectStmt->bind_param("i", $id);
    $selectStmt->execute();
    $result = $selectStmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Step 2: Log it in transaction_history before deletion
        $historyStmt = $db->prepare("INSERT INTO transaction_history (transaction_id, payer_name, transaction_type, amount, transaction_date, transaction_time, guarantor, update_status, action) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Delete')");
        $historyStmt->bind_param(
            "issdssss",
            $row['id'],
            $row['payer_name'],
            $row['transaction_type'],
            $row['amount'],
            $row['transaction_date'],
            $row['transaction_time'],
            $row['guarantor'],
            $row['update_status']
        );
        $historyStmt->execute();
        $historyStmt->close();

        // Step 3: Delete the record from transactions
        $deleteStmt = $db->prepare("DELETE FROM transactions WHERE id = ?");
        $deleteStmt->bind_param("i", $id);

        if ($deleteStmt->execute()) {
            echo "Transaction deleted successfully.";
        } else {
            echo "Error deleting transaction: " . $deleteStmt->error;
        }

        $deleteStmt->close();
    } else {
        echo "Transaction not found.";
    }

    $selectStmt->close();
    $db->close();
} else {
    echo "Invalid request.";
}
?>
