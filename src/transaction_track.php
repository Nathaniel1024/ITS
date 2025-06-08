<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tracking Management</title>
  <link rel="stylesheet" href="css/transaction_track.css">
  <link rel="shortcut icon" href="img/logo.jpg" type="image/x-icon">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" charset="utf-8"></script>
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
      <div class="item active"><a href="transaction_track.php"><i class="fas fa-money-check"></i>Tracking</a></div>
      <div class="item"><a href="transaction.php"><i class="fas fa-money-check"></i>Transaction</a></div>
      <div class="form-item">
        <form action="logout.php" method="POST">
          <button type="submit" class="logout-btn"><i class="fas fa-info-circle"></i>Logout</button>
        </form>
      </div>
    </div>
  </div>

  <section class="main">
    <h1>Transaction Tracking Management</h1>

    <?php
    require_once 'config/db.php'; // Include your database connection file
    session_start();
    if (!isset($_SESSION['user'])) {
      header("Location: index.html");
      exit();
    }
    // Fetch transactions without tracking details
    $activityResult = $db->query("SELECT t.* FROM transactions t LEFT JOIN tracking_details td ON t.id = td.transaction_id WHERE td.id IS NULL");
    // Fetch transactions with tracking details
    $trackingResult = $db->query("SELECT t.*, td.tracking_id FROM transactions t JOIN tracking_details td ON t.id = td.transaction_id");
    ?>

    <!-- Transaction Activity Table -->
    <div class="activity-table">
      <h2>Transaction Activity Table</h2>
      <table class="table">
        <thead>
          <tr>
            <th>Date/Transaction made</th>
            <th>Transaction Name</th>
            <th>Track</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $activityResult->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['transaction_date']) ?> <?= htmlspecialchars($row['transaction_time']) ?></td>
              <td><?= htmlspecialchars($row['payer_name']) ?></td>
              <td>
                <button class="add-btn" data-transaction-id="<?= $row['id'] ?>">Add Tracking Details</button>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <!-- Tracking Details Table -->
    <div class="tracking-table">
      <h2>Tracking Details</h2>
      <table class="table">
        <thead>
          <tr>
            <th>Date/Transaction made</th>
            <th>Transaction Name</th>
            <th>Track</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $trackingResult->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['transaction_date']) ?> <?= htmlspecialchars($row['transaction_time']) ?></td>
              <td><?= htmlspecialchars($row['payer_name']) ?></td>
              <td>
                <button class="view-btn" data-tracking-id="<?= $row['tracking_id'] ?>">View Tracking Details</button>
                <button class="edit-btn" data-tracking-id="<?= $row['tracking_id'] ?>">Edit</button>
                <button class="delete-btn" data-tracking-id="<?= $row['tracking_id'] ?>">Delete</button>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <!-- Modal for Add/Edit/View Tracking Details -->
    <div id="trackingModal" class="modal">
      <div class="modal-content">
        <form id="trackingForm">
          <input type="hidden" name="tracking_id" id="tracking_id">
          <input type="hidden" name="transaction_id" id="transaction_id">
          <label>Transaction Name:</label>
          <input type="text" id="modalTransactionName" name="transaction_name" readonly>
          <label>Date Made:</label>
          <input type="text" id="modalDateMade" name="date_made" readonly>
          <label>Tracking ID:</label>
          <input type="text" id="modalTrackingID" name="tracking_id" readonly>
          <label>Department:</label>
          <select id="modalDepartment" name="department" required>
            <option value="Admin">Admin</option>
            <option value="Outreach">Outreach</option>
            <option value="Events">Events</option>
            <option value="Education">Education</option>
            <option value="Operations">Operations</option>
            <option value="Development">Development</option>
          </select>
          <label>Payment Method:</label>
          <select id="modalPaymentMethod" name="payment_method" required>
            <option value="Cash">Cash</option>
            <option value="Check">Check</option>
            <option value="Bank transfer">Bank transfer</option>
          </select>
          <label>Status:</label>
          <select id="modalStatus" name="status" required>
            <option value="Pending">Pending</option>
            <option value="Cleared">Cleared</option>
            <option value="Reconciled">Reconciled</option>
            <option value="Flagged">Flagged</option>
            <option value="Void">Void</option>
          </select>
          <label>Treasurer Remarks:</label>
          <textarea id="modalTreasurerRemarks" name="treasurer_remarks" placeholder="Treasurer’s instructions about the tracking details"></textarea>
          <div class="modal-buttons">
            <button type="submit" id="saveBtn">Save Details</button>
            <button type="button" id="editBtn">Edit</button>
            <button type="button" id="cancelBtn">Cancel</button>
          </div>
        </form>
        <div id="activityHistory"></div>
      </div>
    </div>

  </section>
  <script src="js/dashboard.js"></script>
  <script src="js/transaction_track.js"></script>
</body>

</html>