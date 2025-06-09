<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="css/transactions.css">
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
      <div class="item active"><a href="transaction.php"><i class="fas fa-money-check"></i>Transaction</a></div>

      <div class="form-item">
        <form action="logout.php" method="POST">
          <button type="submit" class="logout-btn"><i class="fas fa-info-circle"></i>Logout</button>
        </form>
      </div>
    </div>

  </div>

  <section class="main">
    <h1>Transaction Management</h1>

    <?php
    require_once 'config/db.php'; // Include your database connection file
    session_start();
    if (!isset($_SESSION['user'])) {
      header("Location: index.php");
      exit();
    }

    $resultCollections = $db->query("SELECT SUM(amount) AS total_collections FROM transactions WHERE transaction_type = 'Collection'");
    $totalCollections = 0;
    if ($resultCollections && $row = $resultCollections->fetch_assoc()) {
      $totalCollections = $row['total_collections'] ?? 0;
    }

    $resultDisbursements = $db->query("SELECT SUM(amount) AS total_disbursements FROM transactions WHERE transaction_type = 'Disbursement'");
    $totalDisbursements = 0;
    if ($resultDisbursements && $row = $resultDisbursements->fetch_assoc()) {
      $totalDisbursements = $row['total_disbursements'] ?? 0;
    }

    $remainingBalance = $totalCollections - $totalDisbursements;
    ?>

    <!-- Financial Overview Cards -->
    <div class="finance-summary">
      <div class="card">
        <h3>Total<br>Collections</h3>
        <p>₱ <span id="totalCollections"><?= number_format($totalCollections, 2) ?></span></p>
      </div>
      <div class="card">
        <h3>Total Disbursements</h3>
        <p>₱ <span id="totalCollections"><?= number_format($totalDisbursements, 2) ?></span></p>
      </div>
      <div class="card">
        <h3>Remaining Balance</h3>
        <p>₱ <span id="totalCollections"><?= number_format($remainingBalance, 2) ?></span></p>
      </div>
    </div>


    <!-- Transaction Form -->
    <div class="transaction-form">
      <h2>Add New Transaction</h2>
      <form id="transactionForm" method="POST" action="config/add_transaction.php">
        <input type="text" name="payerName" placeholder="Payer Name" required>
        <label for="transactionType">Transaction Type</label>
        <select id="transactionType" name="transactionType" required>
          <option value="Collection">Collection</option>
          <option value="Disbursement">Disbursement</option>
        </select>
        <input type="number" name="amount" step="0.01" placeholder="Amount" required>
        <input type="text" name="guarantorName" placeholder="Guarantor Name" required>
        <label for="updateStatus">Status</label>
        <select name="updateStatus" required>
          <option value="Completed">Completed</option>
          <option value="Awaiting Approval">Awaiting Approval</option>
          <option value="Verified">Verified</option>
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
              <input type="number" id="editAmount" step="0.01" placeholder="Amount" required>
            </div>
            <label for="editransactionType">Transaction Type</label>
            <div class=form-group>
              <select id="edittransactionType" name="edittransactionType" required>
                <option value="Collection">Collection</option>
                <option value="Disbursement">Disbursement</option>
              </select>
            </div>
            <label>Status:</label>
            <div class=form-group>
              <select id="editStatusType">
                <option value="Completed">Completed</option>
                <option value="Awaiting Approval">Awaiting Approval</option>
                <option value="Verified">Verified</option>
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
            <th>Payer Name</th>
            <th>
              Type
              <select class="type-options" id="filterType">
                <option value="All">All</option>
                <option value="Collection">Collection</option>
                <option value="Disbursement">Disbursement</option>
              </select>
            </th>
            <th>Amount</th>
            <th>Date</th>
            <th>Time</th>
            <th>Guarantor</th>
            <th>Status</th>
            <th>Actions</th>
            <th>History</th>
          </tr>
        </thead>
        <tbody id="transactionList">
          <?php
          require_once 'config/db.php';

          $result = $db->query("SELECT * FROM transactions ORDER BY id DESC");

          if ($result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
          ?>
              <tr data-type="<?= htmlspecialchars($row['transaction_type']) ?>">
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['payer_name']) ?></td>
                <td><?= htmlspecialchars($row['transaction_type']) ?></td>
                <td>₱ <?= number_format($row['amount'], 2) ?></td>
                <td><?= htmlspecialchars($row['transaction_date']) ?></td>
                <td><?= htmlspecialchars($row['transaction_time']) ?></td>
                <td><?= htmlspecialchars($row['guarantor']) ?></td>
                <?php
                // Create a safe CSS class name from status (lowercase, no spaces)
                $statusClass = strtolower(str_replace(' ', '-', $row['update_status']));
                ?>
                <td><span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($row['update_status']) ?></span></td>
                <td>
                  <button class="edit-btn" onclick="editTransaction(<?= $row['id'] ?>)">Edit</button>
                  <button class="delete-btn" onclick="deleteTransaction(<?= $row['id'] ?>)">Delete</button>
                </td>
                <td><button class="history-btn" onclick="viewHistory(<?= $row['id'] ?>)">History</button></td>
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
  <script src="js/transaction.js"></script>

</body>

</html>