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

let currentEditId = null;

function editTransaction(id) {
  currentEditId = id;

  // Fetch the transaction data
  fetch('config/get_billing.php?id=' + id)
    .then(response => response.json())
    .then(data => {
      // Populate the form
      document.getElementById('editPayerName').value = data.payer_name;
      document.getElementById('editAmount').value = data.amount;
      document.getElementById('editpaymentType').value = data.payment_type;
      document.getElementById('editpaymentOptions').value = data.payment_options;

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
  data.append('paymentType', document.getElementById('editpaymentType').value);
  data.append('paymentOptions', document.getElementById('editpaymentOptions').value);

  fetch('config/edit_billing.php', {
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

function printbill(rowId) {
  const row = document.querySelector(`tr[data-id="${rowId}"]`);
  if (!row) {
    alert('Transaction not found.');
    return;
  }

  const cells = row.querySelectorAll('td');
  const data = {
    invoice: cells[1].innerText,
    name: cells[2].innerText,
    type: cells[3].innerText,
    amount: cells[4].innerText,
    date: cells[5].innerText,
    time: cells[6].innerText,
    signatory: cells[7].innerText,
    option: cells[8].innerText
  };

  const receiptWindow = window.open('', '', 'width=380,height=650');
  receiptWindow.document.write(`
    <html>
    <head>
      <title>Receipt - ${data.invoice}</title>
      <style>
        body {
          font-family: 'Courier New', monospace;
          margin: 0;
          padding: 20px;
          background: #fff;
          font-size: 14px;
        }
        .receipt {
          max-width: 320px;
          margin: auto;
          border: 1px dashed #000;
          padding: 20px;
          position: relative;
        }
        .receipt h2 {
          text-align: center;
          margin-bottom: 10px;
        }
        .receipt .line {
          border-top: 1px dashed #000;
          margin: 10px 0;
        }
        .receipt .item {
          display: flex;
          justify-content: space-between;
        }
        .paid-stamp {
          position: absolute;
          top: 30px;
          right: -30px;
          transform: rotate(-25deg);
          font-size: 32px;
          font-weight: bold;
          color: red;
          opacity: 0.7;
          border: 2px solid red;
          padding: 5px 12px;
        }
        .footer {
          text-align: center;
          margin-top: 20px;
          font-style: italic;
        }
      </style>
    </head>
    <body>
      <div class="receipt">
        <div class="paid-stamp">PAID</div>
        <h2>Pennywise Billing</h2>
        <div class="line"></div>
        <div class="item"><span>Invoice ID:</span><span>${data.invoice}</span></div>
        <div class="item"><span>Payer:</span><span>${data.name}</span></div>
        <div class="item"><span>Type:</span><span>${data.type}</span></div>
        <div class="item"><span>Amount:</span><span>${data.amount}</span></div>
        <div class="item"><span>Date:</span><span>${data.date}</span></div>
        <div class="item"><span>Time:</span><span>${data.time}</span></div>
        <div class="item"><span>Signatory:</span><span>${data.signatory}</span></div>
        <div class="item"><span>Paid via:</span><span>${data.option}</span></div>
        <div class="line"></div>
        <div class="footer">Thank you for your payment!</div>
      </div>
      <script>window.onload = () => window.print();</script>
    </body>
    </html>
  `);
  receiptWindow.document.close();
}