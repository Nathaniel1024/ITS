<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="css/document_tracking.css">
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
      <div class="item active"><a href="document.php"><i class="fas fa-folder"></i>Document Tracking</a></div>
      <div class="item"><a href="transaction.php"><i class="fas fa-money-check"></i>Transaction</a></div>

      <div class="form-item">
        <form action="logout.php" method="POST">
          <button type="submit" class="logout-btn"><i class="fas fa-info-circle"></i>Logout</button>
        </form>
      </div>
    </div>

  </div>

  <section class="main">
      <div class="main-header">
          <h1 class="Title">Manual Fund Tracking Log</h1>
          <button class="action-btn prominent" onclick="openNewTrackerModal()">Create New Manual Tracker Entry</button>
        </div>
        <div class="search-filter-section">
          <input type="text" id="searchBox" placeholder="Search by Fund/Doc Name or Tracking ID">
          <select id="statusDropdown">
            <option value="">All Statuses</option>
            <option value="On Progress">On Progress</option>
            <option value="For Review">For Review</option>
            <option value="Approved">Approved</option>
            <option value="Released">Released</option>
            <option value="Completed">Completed</option>
            <option value="Returned">Returned</option>
            <option value="Cancelled">Cancelled</option>
          </select>
          <button class="action-btn" onclick="searchManualTrackers()">Search</button>
        </div>
        <div class="manual-tracker-list">
          <table>
            <thead>
              <tr>
                <th>Tracking ID</th>
                <th>Fund/Doc Name</th>
                <th>Department/Project</th>
                <th>Allocated Amount</th>
                <th>Current Status</th>
                <th>Last Update Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="manualTrackerTableBody">
              <!-- Populated by JS -->
            </tbody>
          </table>
        </div>
        <!-- Modal for Fund/Document Details & Current Status -->
        <div id="trackerModal" class="modal">
          <div class="modal-form-container">
            <form id="trackerForm" class="flex-form" autocomplete="off" onsubmit="event.preventDefault(); saveTrackerDetails();">
              <span class="close" onclick="closeTrackerModal()">&times;</span>
              <h2>Fund/Document Details & Current Status</h2>
              <div class="flex-form-row">
                <div class="form-group">
                  <label>Tracking ID</label>
                  <input type="text" id="modalTrackingId" readonly>
                </div>
                <div class="form-group">
                  <label>Fund/Doc Name</label>
                  <input type="text" id="modalFundName" required>
                </div>
              </div>
              <div class="flex-form-row">
                <div class="form-group">
                  <label>Department/Project</label>
                  <input type="text" id="modalDepartment" required>
                </div>
                <div class="form-group">
                  <label>Allocated Amount</label>
                  <input type="number" id="modalAmount" required>
                </div>
              </div>
              <div class="flex-form-row">
                <div class="form-group">
                  <label>Purpose</label>
                  <input type="text" id="modalPurpose">
                </div>
                <div class="form-group">
                  <label>Current Status</label>
                  <select id="modalStatus" required>
                    <option value="On Progress">On Progress</option>
                    <option value="For Review">For Review</option>
                    <option value="Approved">Approved</option>
                    <option value="Released">Released</option>
                    <option value="Completed">Completed</option>
                    <option value="Returned">Returned</option>
                    <option value="Cancelled">Cancelled</option>
                  </select>
                </div>
              </div>
              <div class="flex-form-row">
                <div class="form-group" style="flex:1;">
                  <label>Last Status Update</label>
                  <input type="text" id="modalLastUpdate" readonly>
                </div>
              </div>
              <div class="modal-actions">
                <button type="button" id="editBtn" class="action-btn" onclick="enableEditMode()">Edit Details</button>
                <button type="submit" id="saveBtn" class="action-btn prominent" style="display:none;">Save Changes</button>
                <button type="button" id="cancelEditBtn" class="action-btn" onclick="cancelEditMode()" style="display:none;">Cancel Edit</button>
              </div>
              <h3>Fund/Document Activity History</h3>
              <div class="activity-history-container">
                <table class="activity-history-table">
                  <thead>
                    <tr>
                      <th>Date/Time</th>
                      <th>Status Change / Action</th>
                      <th>Details / Remarks</th>
                      <th>Performed By</th>
                    </tr>
                  </thead>
                  <tbody id="activityHistoryTableBody">
                    <!-- Activity history rows will appear here -->
                  </tbody>
                  <tfoot>
                    <tr>
                      <td>
                        <input type="text" id="activityDateTime" placeholder="YYYY-MM-DD HH:MM" readonly />
                      </td>
                      <td>
                        <input type="text" id="activityAction" placeholder="Action/Status" required />
                      </td>
                      <td>
                        <input type="text" id="activityDetails" placeholder="Remarks" required />
                      </td>
                      <td>
                        <input type="text" id="activityBy" placeholder="Performed By" required />
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </form>
          </div>
        </div>
  </section>
  <script src="js/document_tracking.js"></script>
  <script src="js/dashboard.js"></script>
  <script src="js/chart.js"></script>
</body>

</html>