function toggleDropdown(event, el) {
  event.preventDefault();
  document.querySelectorAll('.dropdown-content').forEach(dd => {
    if (!dd.previousElementSibling.isSameNode(el)) {
      dd.style.display = 'none';
    }
  });
  const dropdown = el.nextElementSibling;
  dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

// Optional: Hide dropdowns when clicking outside
// document.addEventListener('click', function(e) {
//   if (!e.target.classList.contains('dropdown-toggle')) {
//     document.querySelectorAll('.dropdown-content').forEach(dd => dd.style.display = 'none');
//   }
// });

function updatePlaceholder() {
  const selected = document.getElementById("fieldSelect").value;
  const input = document.getElementById("searchInput");
  input.placeholder = selected ? `Enter ${selected}...` : "Select a field...";
}

document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("infoModal");
  const modalName = document.getElementById("modalName");
  const modalAmount = document.getElementById("modalAmount");
  const modalType = document.getElementById("modalType");
  const breakdownTable = document.querySelector("#breakdownTable tbody");
  let selectedRow = null;

  const sampleBreakdowns = [
    { description: "Registration Form", amount: "₱250.00", category: "Business Tax" },
    { description: "Quarterly Filing", amount: "₱3,000.00", category: "Business Tax" }
  ];

  function openModal(name, amount, type) {
    modalName.textContent = name;
    modalType.textContent = type;
    renderBreakdown();
    modal.style.display = "block";
  }

  document.querySelectorAll("#payerTable tbody tr").forEach(row => {
    row.addEventListener("click", () => {
      selectedRow = row;
      openModal(row.dataset.taxpayer, row.dataset.amount, row.dataset.type);
    });
  });

  document.querySelectorAll("#vendorTable tbody tr").forEach(row => {
    row.addEventListener("click", () => {
      selectedRow = row;
      openModal(row.dataset.vendor, row.dataset.amount, row.dataset.category);
    });
  });

  function renderBreakdown() {
    breakdownTable.innerHTML = "";
    let totalAmount = 0;

    sampleBreakdowns.forEach((item, index) => {
      // Convert amount string to float (strip peso sign and commas)
      const numericAmount = parseFloat(item.amount.replace(/[^0-9.]/g, '')) || 0;
      totalAmount += numericAmount;

      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${item.description}</td>
        <td>${item.amount}</td>
        <td>${item.category}</td>
        <td>
          <button class="btn edit-btn" onclick="editBreakdown(${index})">Edit</button>
          <button class="btn delete-btn" onclick="deleteBreakdown(${index})">Delete</button>
        </td>
      `;
      breakdownTable.appendChild(tr);
    });

    // Update modal amount with the total sum of breakdowns, formatted with commas and 2 decimals
    modalAmount.textContent = `₱${totalAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  }

  let currentEditIndex = null;

  window.editBreakdown = function(index) {
    currentEditIndex = index;
    const item = sampleBreakdowns[index];

    document.getElementById('editDesc').value = item.description;

    // Remove peso sign and commas for number input field
    const cleanAmount = item.amount.replace(/[^0-9.]/g, '');
    document.getElementById('editAmt').value = cleanAmount;

    document.getElementById('editCat').value = item.category;

    document.getElementById('editModal').style.display = 'flex';
  };

  function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
  }
  window.closeEditModal = closeEditModal;

  document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const newDesc = document.getElementById('editDesc').value;
    let newAmt = document.getElementById('editAmt').value;
    const newCat = document.getElementById('editCat').value;

    // Make sure peso sign is included correctly
    newAmt = `₱${newAmt.replace(/^₱+/, '')}`;

    if (currentEditIndex !== null) {
      sampleBreakdowns[currentEditIndex] = {
        description: newDesc,
        amount: newAmt,
        category: newCat
      };
      renderBreakdown();
    }

    closeEditModal();
  });

  window.deleteBreakdown = function (index) {
    if (confirm("Delete this breakdown item?")) {
      sampleBreakdowns.splice(index, 1);
      renderBreakdown();
    }
  };

  window.onclick = function (event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  };

  // Manually trigger click on the active daily_collection link to show dropdown
  const dailyCollectionLink = document.querySelector(".dropdown-toggle.active");
  if (dailyCollectionLink) dailyCollectionLink.click();
});

// Add active class to clicked nav-link and remove from others
// document.querySelectorAll('.nav-link').forEach(link => {
//     link.addEventListener('click', function() {
//         // Remove active from all nav-links
//         document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
//         // Add active to the clicked link
//         this.classList.add('active');
//     });
// });

// Sample data for demonstration
let manualTrackers = [];
let currentTrackerIndex = null;
let editing = false;
// let activityPending = false; // Track if activity input is required

function getStatusClass(status) {
    switch (status) {
        case "On Progress": return "status-orange";
        case "For Review": return "status-orange";
        case "Approved": return "status-green";
        case "Released": return "status-green";
        case "Completed": return "status-green";
        case "Returned": return "status-red";
        case "Cancelled": return "status-red";
        default: return "";
    }
}

function renderManualTrackerList(trackers = manualTrackers) {
    const tbody = document.getElementById('manualTrackerTableBody');
    tbody.innerHTML = "";
    trackers.forEach((tracker, idx) => {
        const statusClass = getStatusClass(tracker.status);
        tbody.innerHTML += `
            <tr>
                <td>${tracker.trackingId}</td>
                <td>${tracker.fundName}</td>
                <td>${tracker.department}</td>
                <td>${tracker.amount}</td>
                <td><span class="status-label ${statusClass}">${tracker.status}</span></td>
                <td>${tracker.lastUpdate}</td>
                <td>
                    <button class="action-btn" onclick="viewTracker(${idx})">View</button>
                </td>
            </tr>
        `;
    });
}

function searchManualTrackers() {
  const searchValue = document.getElementById('searchBox').value.trim().toLowerCase();
  const statusValue = document.getElementById('statusDropdown').value;

  const tbody = document.getElementById('manualTrackerTableBody');
  tbody.innerHTML = '';

  const filtered = manualTrackers.filter(tracker => {
    const matchesSearch = !searchValue ||
      tracker.fundName.toLowerCase().includes(searchValue) ||
      tracker.trackingId.toLowerCase().includes(searchValue);
    const matchesStatus = !statusValue || tracker.status === statusValue;
    return matchesSearch && matchesStatus;
  });

  if (filtered.length === 0) {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td colspan="7" style="text-align:center;color:#888;">No matching entries found.</td>`;
    tbody.appendChild(tr);
    return;
  }

  filtered.forEach((tracker, idx) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${tracker.trackingId}</td>
      <td>${tracker.fundName}</td>
      <td>${tracker.department}</td>
      <td>₱${Number(tracker.amount).toLocaleString()}</td>
      <td><span class="status-label ${getStatusClass(tracker.status)}">${tracker.status}</span></td>
      <td>${tracker.lastUpdate}</td>
      <td><button class="action-btn" onclick="viewTracker(${manualTrackers.indexOf(tracker)})">View Details</button></td>
    `;
    tbody.appendChild(tr);
  });
}

// --- Modal logic ---
function openNewTrackerModal() {
  editing = true;
  currentTrackerIndex = null;
  activityPending = false;
  document.getElementById('trackerForm').reset();
  document.getElementById('modalTrackingId').value = generateTrackingId();
  document.getElementById('modalLastUpdate').value = '';
  document.getElementById('modalStatus').value = 'Pending';
  setModalFieldsEditable(true);
  document.getElementById('editBtn').style.display = 'none';
  document.getElementById('saveBtn').style.display = '';
  document.getElementById('cancelEditBtn').style.display = '';
  renderActivityHistory([]);
  clearActivityForm();
  document.getElementById('trackerModal').style.display = 'flex';
}

function viewTracker(idx) {
  editing = false;
  currentTrackerIndex = idx;
  activityPending = false;
  const tracker = manualTrackers[idx];
  document.getElementById('modalTrackingId').value = tracker.trackingId;
  document.getElementById('modalFundName').value = tracker.fundName;
  document.getElementById('modalDepartment').value = tracker.department;
  document.getElementById('modalAmount').value = tracker.amount;
  document.getElementById('modalPurpose').value = tracker.purpose;
  document.getElementById('modalStatus').value = tracker.status;
  document.getElementById('modalLastUpdate').value = tracker.lastUpdate;
  setModalFieldsEditable(false);
  document.getElementById('editBtn').style.display = '';
  document.getElementById('saveBtn').style.display = 'none';
  document.getElementById('cancelEditBtn').style.display = 'none';
  renderActivityHistory(tracker.history || []);
  clearActivityForm();
  document.getElementById('trackerModal').style.display = 'flex';
}

function enableEditMode() {
  editing = true;
  setModalFieldsEditable(true);
  document.getElementById('editBtn').style.display = 'none';
  document.getElementById('saveBtn').style.display = '';
  document.getElementById('cancelEditBtn').style.display = '';
  // Require activity input when editing
  activityPending = true;
  showActivityForm(true);
}

function saveTrackerDetails() {
  // Check all required fields in the details section
  const requiredFields = [
    'modalFundName',
    'modalDepartment',
    'modalAmount',
    'modalStatus'
  ];
  for (const id of requiredFields) {
    const el = document.getElementById(id);
    if (!el.value.trim()) {
      alert('Please fill out all required fields.');
      el.focus();
      return false;
    }
  }

  // --- Add activity if all activity fields are filled ---
  const datetime = document.getElementById('activityDateTime').value;
  const action = document.getElementById('activityAction').value.trim();
  const details = document.getElementById('activityDetails').value.trim();
  const by = document.getElementById('activityBy').value.trim();

  let tracker = {
    trackingId: document.getElementById('modalTrackingId').value,
    fundName: document.getElementById('modalFundName').value,
    department: document.getElementById('modalDepartment').value,
    amount: document.getElementById('modalAmount').value,
    purpose: document.getElementById('modalPurpose').value,
    status: document.getElementById('modalStatus').value,
    lastUpdate: getCurrentDateTime(),
    history: []
  };

  // Use temp activity history for new tracker
  if (currentTrackerIndex === null && window._newTrackerTemp && window._newTrackerTemp.history) {
    tracker.history = window._newTrackerTemp.history;
  } else if (currentTrackerIndex !== null) {
    tracker.history = manualTrackers[currentTrackerIndex].history;
  }

  // Add activity if all fields are filled
  if (action && details && by) {
    tracker.history = tracker.history || [];
    tracker.history.push({ datetime, action, details, by });
    clearActivityForm();
  }

  if (currentTrackerIndex === null) {
    manualTrackers.push(tracker);
    window._newTrackerTemp = null; // clear temp
  } else {
    Object.assign(manualTrackers[currentTrackerIndex], tracker);
  }
  renderManualTrackerList();
  closeTrackerModal();
  alert('Details saved successfully.');
}

function validateTrackerForm() {
  // Check all required fields in the details section
const requiredFields = [
    'modalFundName',
    'modalDepartment',
    'modalAmount',
    'modalStatus'
  ];
  for (const id of requiredFields) {
    const el = document.getElementById(id);
    if (!el.value.trim()) {
      alert('Please fill out all required fields.');
      el.focus();
      return false;
    }
  }
   //If activityPending, require all activity fields
 if (activityPending && !activityFormFilled()) {
    alert('Please fill out all activity fields before saving.');
   return false;
   }
   return true;
 }

function activityFormFilled() {
  return (
    document.getElementById('activityAction').value.trim() &&
    document.getElementById('activityDetails').value.trim() &&
    document.getElementById('activityBy').value.trim()
  );
}

function pendingActivityAdded(tracker) {
  // Check if the last activity matches the current edit time
  if (!tracker.history || tracker.history.length === 0) return false;
  const last = tracker.history[tracker.history.length - 1];
  // Consider added if the last activity is within 2 minutes of now
  const now = getCurrentDateTime();
  return last && last.datetime && now.substring(0,16) === last.datetime.substring(0,16);
}

function cancelEditMode() {
  if (currentTrackerIndex !== null) {
    viewTracker(currentTrackerIndex);
  } else {
    closeTrackerModal();
  }
}

function setModalFieldsEditable(editable) {
  document.getElementById('modalFundName').readOnly = !editable;
  document.getElementById('modalDepartment').readOnly = !editable;
  document.getElementById('modalAmount').readOnly = !editable;
  document.getElementById('modalPurpose').readOnly = !editable;
  document.getElementById('modalStatus').disabled = !editable ? false : false;
}

function renderActivityHistory(history) {
  const tbody = document.getElementById('activityHistoryTableBody');
  tbody.innerHTML = '';
  if (!history || history.length === 0) {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td colspan="4" style="text-align:center;color:#888;">No activity yet.</td>`;
    tbody.appendChild(tr);
    return;
  }
  history.forEach(entry => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${entry.datetime || ''}</td>
      <td>${entry.action || ''}</td>
      <td>${entry.details || ''}</td>
      <td>${entry.by || ''}</td>
    `;
    tbody.appendChild(tr);
  });
}

function setActivityDateNow() {
  const now = new Date();
  const formatted = now.getFullYear() + '-' +
    String(now.getMonth() + 1).padStart(2, '0') + '-' +
    String(now.getDate()).padStart(2, '0') + ' ' +
    String(now.getHours()).padStart(2, '0') + ':' +
    String(now.getMinutes()).padStart(2, '0');
  document.getElementById('activityDateTime').value = formatted;
}

function clearActivityForm() {
  setActivityDateNow();
  document.getElementById('activityAction').value = '';
  document.getElementById('activityDetails').value = '';
  document.getElementById('activityBy').value = '';
}

function addActivityHistory() {
  const datetime = document.getElementById('activityDateTime').value;
  const action = document.getElementById('activityAction').value.trim();
  const details = document.getElementById('activityDetails').value.trim();
  const by = document.getElementById('activityBy').value.trim();

  if (!action || !details || !by) {
    alert('Please fill out all activity fields.');
    return;
  }

  // Allow adding activity even if the tracker is not yet saved (new tracker)
  let tracker;
  if (currentTrackerIndex !== null) {
    tracker = manualTrackers[currentTrackerIndex];
  } else {
    // Use a temp variable for new tracker activities before saving
    if (!window._newTrackerTemp) window._newTrackerTemp = { history: [] };
    tracker = window._newTrackerTemp;
  }

  if (!tracker.history) tracker.history = [];
  tracker.history.push({ datetime, action, details, by });
  renderActivityHistory(tracker.history);
  clearActivityForm();
}

// --- Close modal function ---
function closeTrackerModal() {
  document.getElementById('trackerModal').style.display = 'none';
}

function generateTrackingId() {
  return 'MT-' + String(manualTrackers.length + 1).padStart(3, '0');
}

function getCurrentDateTime() {
  const now = new Date();
  return now.getFullYear() + '-' +
    String(now.getMonth() + 1).padStart(2, '0') + '-' +
    String(now.getDate()).padStart(2, '0') + ' ' +
    String(now.getHours()).padStart(2, '0') + ':' +
    String(now.getMinutes()).padStart(2, '0');
}

// Initial render
window.onload = renderManualTrackerList;