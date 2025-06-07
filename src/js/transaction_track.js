$(document).ready(function () {
  // Hide modal on page load
  $("#trackingModal").hide();
  // Hide edit modal on load
  $("#editModal").hide();

  let currentEditId = null;

  // Filter rows based on transaction type
  $("#filterType").on("change", function () {
    const selectedType = this.value;
    $("#transactionList tr").each(function () {
      const rowType = $(this).data("type");
      $(this).toggle(selectedType === "All" || rowType === selectedType);
    });
  });

  // Delete transaction
  $(document).on("click", ".delete-btn", function () {
    console.log("Delete button clicked");
    const id = $(this).data("tracking-id");
    if (confirm("Are you sure you want to delete this tracking details?")) {
      $.post("config/delete_tracking_details.php", { tracking_id: id }, function (response) {
        if (response.success) {
          location.reload();
        } else {
          alert("Error: " + (response.message || "Failed to delete."));
        }
      }, "json");
    }
  });

  // Open modal for Add, View, Edit
  $(".add-btn, .view-btn, .edit-btn").on("click", function () {
    const mode = $(this).hasClass("add-btn") ? "add" :
                 $(this).hasClass("view-btn") ? "view" : "edit";
    const id = $(this).data("transaction-id") || $(this).data("tracking-id");
    openTrackingModal(mode, id);
  });

  // Edit button enables fields
  $("#editBtn").on("click", function () {
    $("#trackingForm input, #trackingForm select, #trackingForm textarea").prop("disabled", false);
    $("#saveBtn").show();
    $(this).hide();
  });

  // Cancel closes modal
  $("#cancelBtn").on("click", function () {
    closeTrackingModal();
  });

  // Save form data
  $("#trackingForm").on("submit", function (e) {
    e.preventDefault();
    $.post("config/save_tracking_details.php", $(this).serialize(), function (response) {
      if (response.success) {
        location.reload();
      } else {
        alert("Error: " + response.message);
      }
    }, "json");
  });

  // Utility: open modal with data
  function openTrackingModal(mode, id) {
    $("#trackingForm")[0].reset();
    $("#trackingForm input, #trackingForm select, #trackingForm textarea").prop("disabled", mode === "view");
    $("#saveBtn").toggle(mode !== "view");
    $("#editBtn").toggle(mode === "view");
    $("#trackingModal").css("display", "flex");

    if (mode === "add") {
  $("#tracking_id").val("");
  $("#transaction_id").val(id);

  // Fetch tracking ID from backend
  $.get("config/generate_tracking_id.php", function (response) {
    if (response.success) {
      $("#modalTrackingID").val(response.tracking_id);
    } else {
      alert("Failed to generate Tracking ID: " + response.message);
    }
  }, "json");

  $.get("config/get_transaction.php", { id }, function (data) {
    $("#modalTransactionName").val(data.payer_name);
    $("#modalDateMade").val(data.transaction_date + " " + data.transaction_time);
  }, "json");

  $("#activityHistory").html("");
} {
      $.get("config/get_tracking_details.php", { tracking_id: id }, function (data) {
        $("#tracking_id").val(data.tracking_id);
        $("#transaction_id").val(data.transaction_id);
        $("#modalTransactionName").val(data.transaction_name);
        $("#modalDateMade").val(data.date_made);
        $("#modalTrackingID").val(data.tracking_id);
        $("#modalDepartment").val(data.department);
        $("#modalPaymentMethod").val(data.payment_method);
        $("#modalStatus").val(data.status);
        $("#modalTreasurerRemarks").val(data.treasurer_remarks);

        $.get("config/get_tracking_history.php", { tracking_id: id }, function (history) {
          let html = "<strong>Activity History:</strong><ul>";
          if (history?.length) {
            history.forEach(item => {
              html += `<li>${item.changed_at} - ${item.changed_by}: ${item.change_description}</li>`;
            });
          } else {
            html += "<li>No history found.</li>";
          }
          html += "</ul>";
          $("#activityHistory").html(html);
        }, "json");
      }, "json");
    }
  }

  function closeTrackingModal() {
    $("#trackingModal").hide();
  }
});


