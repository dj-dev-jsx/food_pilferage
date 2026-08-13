<?php
include 'include/connect_db.php';
$query = "SELECT category_id, category_name FROM categories";
$result = mysqli_query($conn, $query);
?>
<div class="modal fade" id="edit-modal" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-dark" id="modal-title">
                    <i class="bi bi-pencil-square me-2"></i>Edit Item
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm">
                <div class="modal-body p-4">
                    <div id="errorMessage" class="alert alert-danger d-none"></div>
                    <input type="hidden" id="editItemId" name="edit_item_id">
                    
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="edit_item_name" id="editItemName" placeholder="Item Name" required>
                        <label for="editItemName">Item Name</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" name="edit_unit_price" id="editUnitPrice" placeholder="Unit Price" required>
                        <label for="editUnitPrice">Unit Price</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-select" id="editCategory" name="edit_category_id" required onchange="toggleExpiryForm(this)">
                            <option value="" disabled selected>Select Category</option>
                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                <option value="<?php echo $row['category_id']; ?>"><?php echo $row['category_name']; ?></option>
                            <?php } ?>
                        </select>
                        <label for="editCategory">Category</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="editStockQuantity" name="edit_stock_quantity" placeholder="Stock Quantity" required>
                        <label for="editStockQuantity">Stock Quantity</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="editUnitOfMeasure" name="edit_unit_of_measure" placeholder="Unit of Measure" required>
                        <label for="editUnitOfMeasure">Unit of Measure</label>
                    </div>

                    <div class="form-floating mb-3 d-none" id="editExpiryDateDiv">
                        <input type="date" class="form-control" id="editExpiryDate" name="edit_expiry_date" placeholder="Expiry Date">
                        <label for="editExpiryDate">Expiry Date</label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" id="editCancelBtn" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-lg me-2"></i>Update
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
    border-color: #ffc107;
    box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
}

.btn {
    border-radius: 10px;
    padding: 10px 20px;
}
</style>

<script>
function toggleExpiryForm(selectElement) {
    const expiryDate = document.getElementById('editExpiryDateDiv');
    const isPerishable = selectElement.options[selectElement.selectedIndex].text === 'Perishable';
    
    expiryDate.classList.toggle('d-none', !isPerishable);
    document.getElementById('editExpiryDate').required = isPerishable;
}
</script>
