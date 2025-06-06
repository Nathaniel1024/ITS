window.onload = function () {
  document.getElementById("editModal").style.display = "none";
};

function closeEditModal() {
  document.getElementById("editModal").style.display = "none";
}

 document.getElementById("filterType").addEventListener("change", function () {
    const selectedType = this.value;
    const rows = document.querySelectorAll("#transactionList tr");

    rows.forEach(row => {
      const rowType = row.getAttribute("data-type");

      if (selectedType === "All" || rowType === selectedType) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  });

  function deleteTransaction(id) {
  if (confirm("Are you sure you want to delete this transaction?")) {
    fetch('config/delete_transaction.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: 'id=' + encodeURIComponent(id)
    })
    .then(response => response.text())
    .then(result => {
      alert(result);
      location.reload(); // Reload page to reflect the deletion
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Something went wrong while deleting the transaction.');
    });
  }
}

let currentEditId = null;

function editTransaction(id) {
  currentEditId = id;

  // Fetch the transaction data
  fetch('config/get_transaction.php?id=' + id)
    .then(response => response.json())
    .then(data => {
      // Populate the form
      document.getElementById('editPayerName').value = data.payer_name;
      document.getElementById('editAmount').value = data.amount;
      document.getElementById('edittransactionType').value = data.transaction_type;
      document.getElementById('editStatusType').value = data.update_status;

      // Show the modal
      document.getElementById('editModal').style.display = 'block';
    })
    .catch(error => {
      console.error('Error fetching transaction:', error);
      alert('Failed to load transaction details.');
    });
}

function closeEditModal() {
  document.getElementById('editModal').style.display = 'none';
}

// Handle form submission
document.getElementById('editTransactionForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const data = new URLSearchParams();
  data.append('id', currentEditId);
  data.append('payerName', document.getElementById('editPayerName').value);
  data.append('amount', document.getElementById('editAmount').value);
  data.append('transactionType', document.getElementById('edittransactionType').value);
  data.append('updateStatus', document.getElementById('editStatusType').value);

  fetch('config/edit_transaction.php', {
    method: 'POST',
    body: data,
  })
    .then(response => response.text())
    .then(result => {
      alert(result);
      closeEditModal();
      location.reload();
    })
    .catch(error => {
      console.error('Update error:', error);
      alert('Failed to update transaction.');
    });
});

function viewHistory(transactionId) {
    window.location.href = 'config/transaction_history.php?id=' + transactionId;
}

