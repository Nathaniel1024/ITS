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

  // Manually trigger click on the active taxpayer link to show dropdown
  const taxpayerLink = document.querySelector(".dropdown-toggle.active");
  taxpayerLink.click();
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