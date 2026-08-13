<?php
include "include/connect_db.php";

// Fetch items for the dropdown
$query = "SELECT item_id, item_name FROM items";
$result = mysqli_query($conn, $query);

// Fetch report statuses for the status dropdown
$statusQuery = "SELECT report_status_id, report_status FROM report_status";
$status_result = mysqli_query($conn, $statusQuery);
?>

<div class="modal fade" id="edit-report-modal" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title text-dark" id="modal-title">
                    <i class="bi bi-pencil-square me-2"></i>Edit Report
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editReportForm">
                <div class="modal-body p-4">
                    <div id="errorMessageEdit" class="alert alert-danger d-none"></div>
                    <input type="hidden" name="reportId" id="editReportId">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['role_id']; ?>">

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control bg-light" id="editItemName" readonly>
                        <label>Item Name</label>
                        <input type="hidden" name="item_id" id="editItemId">
                    </div>

                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="editReportedQuantity" name="reported_quantity" placeholder="Quantity" required>
                        <label for="editReportedQuantity">Reported Quantity</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select name="report_status_id" id="editReportStatus" class="form-select" required>
                            <option value="" disabled selected>Select Status</option>
                            <?php
                            $statusQuery = "SELECT report_status_id, report_status FROM report_status";
                            $status_result = mysqli_query($conn, $statusQuery);
                            while ($rowstatus = mysqli_fetch_assoc($status_result)) { ?>
                                <option value="<?php echo $rowstatus['report_status_id']; ?>"><?php echo $rowstatus['report_status']; ?></option>
                            <?php } ?>
                        </select>
                        <label for="editReportStatus">Status</label>
                    </div>

                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="editDescription" name="description" placeholder="Description" style="height: 100px" required></textarea>
                        <label for="editDescription">Description</label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" id="editReportCancel">
                        <i class="bi bi-x-lg me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-lg me-2"></i>Update Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.modal-header {
    border-radius: 15px 15px 0 0;
}

.form-floating > .form-control,
.form-floating > .form-select {
    border-radius: 10px;
    border: 1px solid #ced4da;
}

.form-floating > .form-control:focus,
.form-floating > .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.btn {
    border-radius: 10px;
    padding: 10px 20px;
}

textarea.form-control {
    resize: none;
}

.bg-light {
    background-color: #f8f9fa !important;
}
</style>


