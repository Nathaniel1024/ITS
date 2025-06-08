<?php
require_once 'db.php';

// Get transaction_id from the query parameter
$transactionId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Prepare filtered query
$stmt = $db->prepare("SELECT * FROM transaction_history WHERE transaction_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $transactionId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Transaction History</title>
  <link rel="shortcut icon" href="img/logo.jpg" type="image/x-icon">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

  <style>
    /* General Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f6f8;
      color: #333;
      padding: 20px;
    }

    /* Main Section */
    .main {
      max-width: 1200px;
      margin: auto;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
      padding: 30px;
    }

    /* Heading */
    .main h2 {
      text-align: center;
      font-size: 28px;
      color: #2c3e50;
      margin-bottom: 30px;
    }

    /* Table Styling */
    .transaction-table table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      font-size: 14px;
    }

    .transaction-table th,
    .transaction-table td {
      padding: 12px 10px;
      border: 1px solid #ddd;
      text-align: center;
      vertical-align: middle;
    }

    .transaction-table th {
      background-color: #f1f1f1;
      font-weight: 700;
      color: #555;
    }

    .transaction-table tr:nth-child(even) {
      background-color: #fafafa;
    }

    .transaction-table tr:hover {
      background-color: #f0f9ff;
    }

    /* Amount Formatting - 5th column (Amount) */
    .transaction-table td:nth-child(5) {
      color: #27ae60;
      font-weight: 700;
    }

    /* Action Highlight - 10th column (Action) */
    .transaction-table td:nth-child(10) {
      font-weight: 700;
      color: #2980b9;
    }

    /* Timestamp Column Styling - 11th column */
    .transaction-table td:nth-child(11) {
      font-size: 12px;
      color: #888;
      font-style: italic;
    }

    /* Responsive Table */
    @media (max-width: 768px) {
      .transaction-table table,
      .transaction-table thead,
      .transaction-table tbody,
      .transaction-table th,
      .transaction-table td,
      .transaction-table tr {
        display: block;
      }

      .transaction-table thead tr {
        display: none;
      }

      .transaction-table tr {
        margin-bottom: 20px;
        padding: 15px 10px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
      }

      .transaction-table td {
        padding-left: 50%;
        position: relative;
        text-align: left;
        border: none;
        border-bottom: 1px solid #eee;
        font-size: 14px;
      }

      .transaction-table td::before {
        content: attr(data-label);
        position: absolute;
        left: 15px;
        width: 45%;
        font-weight: 700;
        text-align: left;
        color: #555;
      }

      .transaction-table td:last-child {
        border-bottom: none;
      }
    }
  </style>

</head>
<body>

  <section class="main">
    <h2>Transaction History (Transaction ID: <?= htmlspecialchars($transactionId) ?>)</h2>
    <div class="transaction-table">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Transaction ID</th>
            <th>Payer Name</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Time</th>
            <th>Guarantor</th>
            <th>Status</th>
            <th>Action</th>
            <th>Timestamp</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($result->num_rows > 0):
              while ($row = $result->fetch_assoc()):
          ?>
            <tr>
              <td data-label="ID"><?= htmlspecialchars($row['id']) ?></td>
              <td data-label="Transaction ID"><?= htmlspecialchars($row['transaction_id']) ?></td>
              <td data-label="Payer Name"><?= htmlspecialchars($row['payer_name']) ?></td>
              <td data-label="Type"><?= htmlspecialchars($row['transaction_type']) ?></td>
              <td data-label="Amount">₱ <?= number_format($row['amount'], 2) ?></td>
              <td data-label="Date"><?= htmlspecialchars($row['transaction_date']) ?></td>
              <td data-label="Time"><?= htmlspecialchars($row['transaction_time']) ?></td>
              <td data-label="Guarantor"><?= htmlspecialchars($row['guarantor']) ?></td>
              <td data-label="Status"><?= htmlspecialchars($row['update_status']) ?></td>
              <td data-label="Action"><strong><?= htmlspecialchars($row['action']) ?></strong></td>
              <td data-label="Timestamp"><?= htmlspecialchars($row['timestamp']) ?></td>
            </tr>
          <?php
              endwhile;
          else:
              echo '<tr><td colspan="11">No history found for this transaction.</td></tr>';
          endif;

          $stmt->close();
          $db->close();
          ?>
        </tbody>
      </table>
    </div>
  </section>

</body>
</html>
