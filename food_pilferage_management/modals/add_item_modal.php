<?php
include 'include/connect_db.php';

// Fetch categories for food service operations
$query = "SELECT category_id, category_name FROM categories ORDER BY category_name";
$result = mysqli_query($conn, $query);
?>

<div class="modal fade" id="add-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>Add an Item
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
        
            <form id="addItemForm">
                <div class="modal-body p-4">
                    <div id="errorMessage" class="alert alert-danger d-none"></div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="itemId" name="item_id" placeholder="Item ID">
                                <label for="itemId">Item ID</label>
                            </div>
                            
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="itemName" name="item_name" placeholder="Item Name" required>
                                <label for="itemName">Item Name</label>
                            </div>
                            
                            <div class="form-floating mb-3">
                                <select class="form-select" id="category" name="category_id" required onchange="handleCategoryChange(this)">
                                    <option value="" disabled selected>Select Category</option>
                                    <option value="1">Meat Products</option>
                                    <option value="2">Vegetables</option>
                                    <option value="3">Seasonings</option>
                                    <option value="4">Dry Goods</option>
                                    <option value="5">Beverages</option>
                                </select>
                                <label for="category">Category</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="number" step="0.01" class="form-control" id="unitPrice" name="unit_price" placeholder="Unit Price" required>
                                <label for="unitPrice">Unit Price</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="stockQuantity" name="stock_quantity" placeholder="Stock Quantity" required>
                                <label for="stockQuantity">Stock Quantity</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="unitOfMeasure" name="unit_of_measure" placeholder="Unit of Measure" required>
                                <label for="unitOfMeasure">Unit of Measure</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="minimumStock" name="minimum_stock" placeholder="Minimum Stock Level" required>
                                <label for="minimumStock">Minimum Stock Level</label>
                            </div>
                            <div class="form-floating mb-3" id="expiryDateContainer">
                                <input type="date" class="form-control" id="expiryDateInput" name="expiry_date">
                                <label for="expiryDateInput">Expiry Date</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-plus-lg me-2"></i>Add Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function handleCategoryChange(selectElement) {
    const expiryDateContainer = document.getElementById('expiryDateContainer');
    const expiryDateInput = document.getElementById('expiryDateInput');
    const category = parseInt(selectElement.value);
    
    // Categories that typically need expiry dates
    const perishableCategories = [1, 2]; // Main Dishes, Meat Products, Vegetables
    
    if (perishableCategories.includes(category)) {
        expiryDateContainer.style.display = 'block';
        expiryDateInput.required = true;
    } else {
        expiryDateContainer.style.display = 'none';
        expiryDateInput.required = false;
    }
}
</script>
