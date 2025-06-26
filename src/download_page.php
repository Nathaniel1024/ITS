<?php
require_once 'config/db.php'; // Include your database connection file
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: index.php");
  exit();
}

// Handle AJAX requests for table data
if (isset($_GET['ajax']) && $_GET['ajax'] === 'fetch_data') {
    
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    $filter = $_GET['filterType'] ?? '';
    $column = $_GET['column'] ?? '';
    $keyword = $_GET['keyword'] ?? '';

    $where = "1=1";

    // Date filtering
    switch ($filter) {
        case 'daily':
            $where .= " AND DATE(date_made) = CURDATE()";
            break;
        case 'weekly':
            $where .= " AND YEARWEEK(date_made, 1) = YEARWEEK(CURDATE(), 1)";
            break;
        case 'monthly':
            $where .= " AND MONTH(date_made) = MONTH(CURDATE()) AND YEAR(date_made) = YEAR(CURDATE())";
            break;
        case 'annually':
            $where .= " AND YEAR(date_made) = YEAR(CURDATE())";
            break;
    }

    // Column filtering
    if ($column && $keyword) {
        $column = $db->real_escape_string($column);
        $keyword = $db->real_escape_string($keyword);
        $where .= " AND `$column` LIKE '%$keyword%'";
    }

    $query = "SELECT * FROM tracking_details WHERE $where ORDER BY date_made DESC";
    $result = $db->query($query);

    if ($result && $result->num_rows > 0) {
        echo "<div class='table-responsive'><table><tr>";

        // Get field names for headers
        $fields = $result->fetch_fields();
        foreach ($fields as $field) {
            $header_name = ucwords(str_replace('_', ' ', $field->name));
            echo "<th>{$header_name}</th>";
        }
        echo "</tr>";

        // Reset result pointer to beginning
        $result->data_seek(0);
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $key => $cell) {
                if ($key === 'status') {
                    $status_class = 'status-' . strtolower($cell);
                    echo "<td><span class='status-badge {$status_class}'>" . htmlspecialchars($cell) . "</span></td>";
                } elseif ($key === 'date_made') {
                    echo "<td>" . date('M j, Y g:i A', strtotime($cell)) . "</td>";
                } else {
                    echo "<td>" . htmlspecialchars($cell ?? '') . "</td>";
                }
            }
            echo "</tr>";
        }

        echo "</table></div>";
    } else {
        echo '<div class="no-data">No data found matching your criteria</div>';
    }

    $db->close();
    exit; // Stop execution for AJAX requests
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Download Center</title>
  <link rel="stylesheet" href="css/download.css">
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
      <div class="item"><a href="dashboard.php"><i class="fas fa-desktop"></i> Dashboard</a></div>
      <div class="item"><a href="transaction_track.php"><i class="fas fa-folder"></i> Tracking</a></div>
      <div class="item"><a href="transaction.php"><i class="fas fa-money-check"></i> Transaction</a></div>
      <div class="item"><a href="online_billing.php"><i class="fas fa-money-bill"></i>Online Billing</a></div>
      <div class="item active"><a href="download_page.php"><i class="fas fa-download"></i> Download Center</a></div>
      <div class="form-item">
        <form action="logout.php" method="POST">
          <button type="submit" class="logout-btn"><i class="fas fa-info-circle"></i> Logout</button>
        </form>
      </div>
    </nav>
  </aside>

  <main>
    <section class="main">
      <h1>Download Center</h1>
    </section>

    <div class="container">

    <div class="card">
      <h2 class="card-title">Filter & Download Data</h2>
      <form id="filterForm" method="GET" action="./download/download.php">
        <div class="form-grid">
          <div class="form-group">
            <label for="filterType">Filter By Date</label>
            <select name="filterType" id="filterType">
              <option value="" disabled selected>Select time period</option>
              <option value="daily" <?= (isset($_GET['filterType']) && $_GET['filterType'] === 'daily') ? 'selected' : '' ?>>Daily</option>
              <option value="weekly" <?= (isset($_GET['filterType']) && $_GET['filterType'] === 'weekly') ? 'selected' : '' ?>>Weekly</option>
              <option value="monthly" <?= (isset($_GET['filterType']) && $_GET['filterType'] === 'monthly') ? 'selected' : '' ?>>Monthly</option>
              <option value="annually" <?= (isset($_GET['filterType']) && $_GET['filterType'] === 'annually') ? 'selected' : '' ?>>Annually</option>
            </select>
          </div>

          <div class="form-group">
            <label for="column">Search Column</label>
            <select name="column" id="column">
              <option value="" disabled selected>Select column</option>
              <option value="tracking_id" <?= (isset($_GET['column']) && $_GET['column'] === 'tracking_id') ? 'selected' : '' ?>>Tracking ID</option>
              <option value="department" <?= (isset($_GET['column']) && $_GET['column'] === 'department') ? 'selected' : '' ?>>Department</option>
              <option value="payment_method" <?= (isset($_GET['column']) && $_GET['column'] === 'payment_method') ? 'selected' : '' ?>>Payment Method</option>
              <option value="status" <?= (isset($_GET['column']) && $_GET['column'] === 'status') ? 'selected' : '' ?>>Status</option>
            </select>
          </div>

          <div class="form-group">
            <label for="keyword">Search Value</label>
            <input type="text" name="keyword" id="keyword" placeholder="Enter search value..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
          </div>

          <div class="form-group">
            <button type="submit">Download CSV</button>
          </div>
        </div>
      </form>
    </div>

    <div class="card">
      <h2 class="card-title">Print Receipt</h2>
      <form method="GET" action="./download/receipt.php" target="_blank">
        <div class="receipt-form">
          <div class="form-group" style="flex: 1;">
            <label for="tracking_id">Tracking ID</label>
            <input type="text" name="tracking_id" id="tracking_id" placeholder="Enter Tracking ID" required>
          </div>
          <button type="submit" class="btn-secondary">Print Receipt</button>
        </div>
      </form>
    </div>

    <div id="table-container">
      <div class="no-data">
        Select filters above to view tracking data
      </div>
    </div>
  </div>
  </main>

  <!-- <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Enhanced filter form functionality
      document.getElementById('filterForm').addEventListener('change', async function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('ajax', 'fetch_data');
        const params = new URLSearchParams(formData).toString();

        // Show loading state
        document.getElementById('table-container').innerHTML = 
          '<div class="loading"><div class="loading-text">Loading data...</div></div>';

        try {
          const response = await fetch('?' + params);
          const html = await response.text();
          document.getElementById('table-container').innerHTML = html;
        } catch (error) {
          console.error('Error fetching data:', error);
          document.getElementById('table-container').innerHTML = 
            '<div class="no-data">Error loading data. Please try again.</div>';
        }
      });

      // Form submission handlers with loading states
      const forms = document.querySelectorAll('form');
      forms.forEach(form => {
        form.addEventListener('submit', function(e) {
          // Skip AJAX handling for filter form
          if (this.id === 'filterForm' && e.target.type !== 'submit') {
            return;
          }

          const button = this.querySelector('button[type="submit"]');
          const originalText = button.textContent;
          
          if (this.id !== 'filterForm') {
            button.textContent = 'Processing...';
            button.disabled = true;
            
            // Re-enable after delay
            setTimeout(() => {
              button.textContent = originalText;
              button.disabled = false;
            }, 3000);
          }
        });
      });

      // Enhanced form validation and UX
      const trackingInput = document.getElementById('tracking_id');
      if (trackingInput) {
        trackingInput.addEventListener('input', function() {
          this.value = this.value.toUpperCase();
        });
      }

      // Auto-load data if filters are set
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('filterType') || urlParams.get('column') || urlParams.get('keyword')) {
        document.getElementById('filterForm').dispatchEvent(new Event('change'));
      }

      // Keyboard shortcuts
      document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + F to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
          e.preventDefault();
          document.getElementById('keyword').focus();
        }
      });
    });
  </script> -->
  <script src="download/script.js"></script>
  <script src="js/download.js"></script>


</body>

</html>