<?php
require_once 'config/db.php'; // Include your database connection file
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: index.html");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="css/dashboard.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" charset="utf-8"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="shortcut icon" href="img/logo.jpg" type="image/x-icon">
</head>

<body>

  <div class="menu-btn">
    <i class="fas fa-bars"></i>
  </div>

  <aside class="side-bar">
    <header>
      <div class="close-btn"><i class="fas fa-times"></i></div>
      <img src="img/pennywise-logo.jpg" alt="Pennywise logo">
      <h1>Pennywise</h1>
    </header>
    <nav class="menu">
      <div class="item active"><a href="dashboard.php"><i class="fas fa-desktop"></i> Dashboard</a></div>
      <div class="item"><a href="transaction_track.php"><i class="fas fa-folder"></i> Tracking</a></div>
      <div class="item"><a href="transaction.php"><i class="fas fa-money-check"></i> Transaction</a></div>
      <div class="form-item">
        <form action="logout.php" method="POST">
          <button type="submit" class="logout-btn"><i class="fas fa-info-circle"></i> Logout</button>
        </form>
      </div>
    </nav>
  </aside>

  <main>
    <section class="main">
      <h1>Treasury Dashboard</h1>
    </section>
    <section class="main">
      <canvas id="myPieChart"></canvas>
    </section>
    <section class="main">
      <canvas id="budgetBarGraph"></canvas>
    </section>

    <section class="main">
      <h1>Report</h1>
    </section>
    <section class="main">
      <form method="GET" class="report-form">
        <input
          type="text"
          name="search"
          placeholder="Search by Tracking ID, Department, or Status"
          value="<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES); ?>">
        <button type="submit">Search</button>
        <select name="report" class="report-dropdown" onchange="this.form.submit()">
          <option value="" disabled <?= !isset($_GET['report']) ? 'selected' : '' ?>>Select Report Type</option>
          <option value="daily" <?= ($_GET['report'] ?? '') === 'daily' ? 'selected' : '' ?>>Daily</option>
          <option value="weekly" <?= ($_GET['report'] ?? '') === 'weekly' ? 'selected' : '' ?>>Weekly</option>
          <option value="monthly" <?= ($_GET['report'] ?? '') === 'monthly' ? 'selected' : '' ?>>Monthly</option>
          <option value="quarterly" <?= ($_GET['report'] ?? '') === 'quarterly' ? 'selected' : '' ?>>Quarterly</option>
          <option value="annually" <?= ($_GET['report'] ?? '') === 'annually' ? 'selected' : '' ?>>Annual</option>
        </select>
      </form>
    </section>

    <section class="main table-section">
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Tracking ID</th>
              <th>Department</th>
              <th>Payment Method</th>
              <th>Status</th>
              <th>Remarks</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $search = $_GET['search'] ?? '';
            $report = $_GET['report'] ?? 'daily';

            $search_safe = $db->real_escape_string($search);
            $where = "1";
            if ($search_safe !== '') {
              $where .= " AND (
        tracking_id LIKE '%$search_safe%' OR
        department LIKE '%$search_safe%' OR
        status LIKE '%$search_safe%'
    )";
            }

            switch ($report) {
              case 'weekly':
                $where .= " AND YEARWEEK(date_made, 1) = YEARWEEK(CURDATE(), 1)";
                break;
              case 'monthly':
                $where .= " AND YEAR(date_made) = YEAR(CURDATE()) AND MONTH(date_made) = MONTH(CURDATE())";
                break;
              case 'quarterly':
                $where .= " AND QUARTER(date_made) = QUARTER(CURDATE()) AND YEAR(date_made) = YEAR(CURDATE())";
                break;
              case 'annually':
                $where .= " AND YEAR(date_made) = YEAR(CURDATE())";
                break;
              default:
                $where .= " AND DATE(date_made) = CURDATE()";
            }

            $limit = 10;
            $page = max(1, intval($_GET['page'] ?? 1));
            $offset = ($page - 1) * $limit;

            $countQuery = "SELECT COUNT(*) as total FROM tracking_details WHERE $where";
            $countResult = $db->query($countQuery);
            $totalRows = intval($countResult->fetch_assoc()['total']);
            $totalPages = ceil($totalRows / $limit);

            $query = "SELECT * FROM tracking_details WHERE $where ORDER BY date_made DESC LIMIT $limit OFFSET $offset";
            $result = $db->query($query);

            if ($result && $result->num_rows > 0):
              while ($row = $result->fetch_assoc()):
            ?>
                <tr>
                  <td><?= htmlspecialchars($row['tracking_id'], ENT_QUOTES) ?></td>
                  <td><?= htmlspecialchars($row['department'], ENT_QUOTES) ?></td>
                  <td><?= htmlspecialchars($row['payment_method'], ENT_QUOTES) ?></td>
                  <td><?= htmlspecialchars($row['status'], ENT_QUOTES) ?></td>
                  <td><?= htmlspecialchars($row['treasurer_remarks'], ENT_QUOTES) ?></td>
                  <td><?= htmlspecialchars($row['date_made'], ENT_QUOTES) ?></td>
                </tr>
              <?php
              endwhile;
            else:
              ?>
              <tr>
                <td colspan="6">No records found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
          <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
                class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
          </div>
        <?php endif; ?>

      </div>
    </section>

  </main>

  <script src="js/dashboard.js"></script>
  <script src="js/chart.js"></script>

</body>

</html>