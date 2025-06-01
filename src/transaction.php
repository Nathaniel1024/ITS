<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="css/transaction.css">
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
      <div class="item"><a href="document.php"><i class="fas fa-folder"></i>Document Tracking</a></div>
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

    <!-- Financial Overview Cards -->
    <div class="finance-summary">
        <div class="card">
            <h3>Total<br>Collections</h3>
            <p>₱ <span id="totalCollections">0</span></p>
        </div>
        <div class="card">
            <h3>Total Disbursements</h3>
            <p>₱ <span id="totalDisbursement">0</span></p>
        </div>
        <div class="card">
            <h3>Remaining Balance</h3>
            <p>₱ <span id="remainingBalance">0</span></p>
        </div>
    </div>

    <!-- Transaction Form -->
    <div class="transaction-form">
      <h2>Add New Transaction</h2>
      <form id="transactionForm">
        <input type="text" id="payerName" placeholder="Payer Name" required>
        <input type="number" id="amount" placeholder="Amount (₱)" required>
        <select id="transactionType">
          <option value="Collection">Collection</option>
          <option value="Disbursement">Disbursement</option>
        </select>
        <select id="updateStatus">
          <option value="Completed">Completed</option>
          <option value="Awaiting Approval">Awaiting Approval</option>
          <option value="Verified">Verified</option>
        </select>
        <button type="submit">Add Transaction</button>
      </form>
    </div>

    <!-- Edit Mode -->
    <div id="editModal" class="modal">
    <div class="modal-content">
        <h2>Edit Transaction</h2>
        <div class="content">
        <form id="editTransactionForm">
            <label>Payer Name:</label>
            <input type="text" id="editPayerName">
            
            <label>Amount:</label>
            <input type="number" id="editAmount">
            
            <label>Transaction Type:</label>
            <select id="editTransactionType">
                <option value="Collection">Collection</option>
                <option value="Disbursement">Disbursement</option>
            </select>
            <br>
            <div class="status-btn">
              <label>Status:</label>
              <select id="editStatusType">
                <option value="Completed">Completed</option>
                <option value="Awaiting Approval">Awaiting Approval</option>
                <option value="Verified">Verified</option>
              </select>
              <br>
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
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="transactionList">
          <!-- Dynamic Transactions Will Load Here -->
        </tbody>
      </table>
    </div>
</section>

  <script src="js/dashboard.js"></script>
  <script src="js/transaction.js"></script>

</body>

</html>