<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="css/online_billing.css">
  <link rel="shortcut icon" href="img/logo.jpg" type="image/x-icon">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" charset="utf-8"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

  <div class="menu-btn">
    <i class="fas fa-bars"></i>
  </div>

  <div class="side-bar">
    <header>
      <div class="close-btn">
        <i class="fas fa-times"></i>
      </div>
      <img src="img/pennywise-logo.jpg" alt="pennywise logo">
      <h1>Pennywise</h1>
    </header>
    <div class="menu">

      <div class="item"><a href="dashboard.php"><i class="fas fa-desktop"></i>Dashboard</a></div>
      <div class="item"><a href="transaction_track.php"><i class="fas fa-folder"></i>Document Tracking</a></div>
      <div class="item"><a href="transaction.php"><i class="fas fa-money-check"></i>Transaction</a></div>
      <div class="item active"><a href="online_billing.php"><i class="fas fa-money-bill"></i>Online Billing</a></div>
      <div class="item"><a href="download_page.php"><i class="fas fa-download"></i> Download Center</a></div>
      <div class="form-item">
        <form action="logout.php" method="POST">
          <button type="submit" class="logout-btn"><i class="fas fa-info-circle"></i>Logout</button>
        </form>
      </div>
    </div>

  </div>

  <section class="main">
    <h1>Online Billing</h1>

    <?php
    require_once 'config/db.php'; // Include your database connection file
    session_start();
    if (!isset($_SESSION['user'])) {
      header("Location: index.php");
      exit();
    }

    $resultBilling = $db->query("SELECT SUM(amount) AS total_payments FROM online_billing");
    $totalPayments = 0;
    if ($resultBilling && $row = $resultBilling->fetch_assoc()) {
      $totalPayments = $row['total_payments'] ?? 0;
    }
    ?>

    <!-- Online Billing Overview Cards -->
    <div class="finance-summary">
      <div class="card">
        <h3>Grand<br>Total</h3>
        <p>₱ <span id="totalCollections"><?= number_format($totalPayments, 2) ?></span></p>
      </div>
    </div>


    <!-- Transaction Form -->
    <div class="transaction-form">
      <h2>Online Billing Details</h2>
      <form id="transactionForm" method="POST" action="config/add_billing.php">
        <input type="text" name="payerName" placeholder="Payer Name" required>
        <label for="paymentType">Payment For:</label>
        <select id="paymentType" name="paymentType" required>
          <option value="Real Property Tax">Real Property Tax</option>
          <option value="Business Permit">Business Permit</option>
          <option value="Civil Registry Certificate">Civil Registry Certificate</option>
        </select>
        <input type="number" name="amount" min="1" step="0.01" placeholder="Amount" required>
        <input type="text" name="signatoryName" placeholder="Signatory Name" required>
        <label for="paymentOptions">Payment Options</label>
        <select name="paymentOptions" required>
          <option value="GCash">Gcash</option>
          <option value="PayMaya">PayMaya</option>
          <option value="Credit Card">Credit Card</option>
          <option value="Debit Card">Debit Card</option>
        </select>
        <button type="submit">Submit</button>
      </form>
    </div>


    <!-- Edit Mode -->
    <div id="editModal" class="modal">
      <div class="modal-content">
        <h2>Edit Transaction</h2>
        <div class="content">
          <form id="editTransactionForm">
            <label>Payer Name:</label>
            <div class=form-group>
              <input type="text" id="editPayerName" placeholder="Payer Name">
            </div>
            <label>Amount:</label>
            <div class=form-group>
              <input type="number" id="editAmount" min="1" step="0.01" placeholder="Amount" required>
            </div>
            <label for="editpaymentType">Payment For:</label>
            <div class=form-group>
              <select id="editpaymentType" name="editpaymentType" required>
                <option value="Real Property Tax">Real Property Tax</option>
                <option value="Business Permit">Business Permit</option>
                <option value="Civil Registry Certificate">Civil Registry Certificate</option>
              </select>
            </div>
            <label>Payment Options</label>
            <div class=form-group>
              <select id="editpaymentOptions">
                <option value="GCash">Gcash</option>
                <option value="PayMaya">PayMaya</option>
                <option value="Credit Card ">Credit Card</option>
                <option value="Debit Card">Debit Card</option>
              </select>
            </div>
            <div class="edit-button">
              <button type="submit">Update Transaction</button>
              <button type="button" onclick="closeEditModal()">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Transaction Records Table -->
    <div class="transaction-table">
      <h2>Recent Transactions</h2>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Invoice ID</th>
            <th>Payer Name</th>
            <th>
              Type
              <select class="type-options" id="filterType">
                <option value="All">All</option>
                <option value="Real Property Tax">Real Property Tax</option>
                <option value="Business Permit">Business Permit</option>
                <option value="Civil Registry Certificate">Civil Registry Certificate</option>
              </select>
            </th>
            <th>Amount</th>
            <th>Date</th>
            <th>Time</th>
            <th>Signatory Name</th>
            <th>Payment Options</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="transactionList">
          <?php
            require_once 'config/db.php';

            $result = $db->query("SELECT * FROM online_billing ORDER BY id DESC");

            if ($result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
              $rowId = $row['id'];
              $invoiceCode = htmlspecialchars($row['invoice_code']);
              $payer = htmlspecialchars($row['payer_name']);
              $type = htmlspecialchars($row['payment_type']);
              $amount = number_format($row['amount'], 2);
              $date = htmlspecialchars($row['payment_date']);
              $time = htmlspecialchars($row['payment_time']);
              $signatory = htmlspecialchars($row['signatory_name']);
              $option = htmlspecialchars($row['payment_options']);
          ?>
          <tr data-id="<?= $rowId ?>" data-type="<?= $type ?>">
            <td><?= $rowId ?></td>
            <td><?= $invoiceCode ?></td>
            <td><?= $payer ?></td>
            <td><?= $type ?></td>
            <td>₱ <?= $amount ?></td>
            <td><?= $date ?></td>
            <td><?= $time ?></td>
            <td><?= $signatory ?></td>
            <td><?= $option ?></td>
            <td>
              <button class="edit-btn" onclick="editTransaction(<?= $rowId ?>)">Edit</button>
              <button class="print-btn" onclick="printbill(<?= $rowId ?>)">Print</button>
            </td>
          </tr>
  <?php
    endwhile;
  else:
    echo '<tr><td colspan="10">No transactions found.</td></tr>';
  endif;

  $db->close();
  ?>
</tbody>
      </table>
    </div>
  </section>

  <script src="js/dashboard.js"></script>
  <script src="js/online_billing.js"></script>

</body>

</html>