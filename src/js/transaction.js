let transactions = [];

let editId = null; // Ensuring no previous transaction is selected
window.onload = () => {
    document.getElementById("editModal").style.display = "none"; // Hide modal on page refresh
};

transactionForm.addEventListener("submit", (e) => {
    e.preventDefault();

    const payerName = document.getElementById("payerName").value;
    const amount = parseFloat(document.getElementById("amount").value);
    const transactionType = document.getElementById("transactionType").value;
    const date = new Date().toLocaleDateString();
    const updateStatus = document.getElementById("updateStatus").value;

    if (!payerName || isNaN(amount)) {
        alert("Please enter valid transaction details.");
        return;
    }

    const newTransaction = {
        id: transactions.length + 1,
        payerName,
        type: transactionType,
        amount,
        date,
        status : updateStatus
    };

    transactions.push(newTransaction);
    updateTransactionTable();
    updateFinancialSummary();
    transactionForm.reset();
});

function openEditModal(id, event) {
    if (!event || !event.target) return; // Ensure event exists

    const transaction = transactions.find(tx => tx.id === id);
    if (!transaction) return;

    console.log("Manual Modal Trigger:", id); // Debugging step

    document.getElementById("editPayerName").value = transaction.payerName;
    document.getElementById("editAmount").value = transaction.amount;
    document.getElementById("editTransactionType").value = transaction.type;
    document.getElementById("editStatusType").value = transaction.status;

    editId = id;
    document.getElementById("editModal").style.display = "block";
}

    document.querySelector(".side-bar .item.active a").addEventListener("click", (event) => {
    event.preventDefault(); // Prevent default navigation if needed
    closeEditModal(); // Hide the modal
});


function closeEditModal() {
    document.getElementById("editModal").style.display = "none"; // Hide modal
}

document.getElementById("editTransactionForm").addEventListener("submit", (e) => {
    e.preventDefault();
    
    const transaction = transactions.find(tx => tx.id === editId);
    if (!transaction) return;

    // Update transaction with new values
    transaction.payerName = document.getElementById("editPayerName").value;
    transaction.amount = parseFloat(document.getElementById("editAmount").value);
    transaction.type = document.getElementById("editTransactionType").value;
    transaction.status = document.getElementById("editStatusType").value;

    updateTransactionTable();
    updateFinancialSummary();
    closeEditModal(); // Close modal after updating
});

function updateTransactionTable() {
    transactionList.innerHTML = "";
    transactions.forEach(tx => {
        const row = document.createElement("tr");
        row.setAttribute("data-type", tx.type); // Set transaction type for filtering

        // Assign appropriate class for the status
        let statusClass = tx.status.toLowerCase().replace(" ", "-"); // Converts "Awaiting Approval" -> "awaiting-approval"

        row.innerHTML = `
            <td>${tx.id}</td>
            <td>${tx.payerName}</td>
            <td>${tx.type}</td>
            <td>₱${tx.amount.toFixed(2)}</td>
            <td>${tx.date}</td>
            <td><span class="status ${statusClass}">${tx.status}</span></td>  <!-- Styled status -->
            <td>
                <button class="edit-btn" onclick="openEditModal(${tx.id}, event)">Edit & Update</button>
                <button class="delete-btn" onclick="deleteTransaction(${tx.id})">Delete</button>
            </td>
        `;

        transactionList.appendChild(row);
    });

    applyFilter(); // Call filtering function after updating table
}

window.deleteTransaction = function(id) {
        transactions = transactions.filter(tx => tx.id !== id);
        updateTransactionTable();
        updateFinancialSummary();
    };

function updateFinancialSummary() {
    const totalCollected = transactions.filter(tx => tx.type === "Collection")
                                      .reduce((sum, tx) => sum + tx.amount, 0);
    const totalSpent = transactions.filter(tx => tx.type === "Disbursement")
                                   .reduce((sum, tx) => sum + tx.amount, 0);

    totalCollections.textContent = totalCollected.toFixed(2);
    totalDisbursement.textContent = totalSpent.toFixed(2);
    remainingBalance.textContent = (totalCollected - totalSpent).toFixed(2);
}

// **Filtering Logic**
function applyFilter() {
    const selectedType = filterType.value;
    const rows = document.querySelectorAll("#transactionList tr");

    rows.forEach(row => {
        const type = row.getAttribute("data-type");
        row.style.display = (selectedType === "All" || type === selectedType) ? "" : "none";
    });
}

// Apply filter when dropdown changes
filterType.addEventListener("change", applyFilter);

