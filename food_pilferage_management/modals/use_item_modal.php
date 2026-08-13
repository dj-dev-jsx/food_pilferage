<div class="modal fade" id="use-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-box-arrow-right"></i> Use Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="use-item-form">
                    <input type="hidden" id="use-item-id" name="item_id">
                    <input type="hidden" id="current-stock" name="current_stock">
                    <input type="hidden" id="unit-price" name="unit_price">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Item Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-box-seam"></i></span>
                                <input type="text" class="form-control" id="use-item-name" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Unit of Measure</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-rulers"></i></span>
                                <input type="text" class="form-control" id="use-unit" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Available Stock</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-boxes"></i></span>
                                <input type="text" class="form-control" id="available-qty" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Quantity to Use</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-dash-circle"></i></span>
                                <input type="number" class="form-control" id="use-qty" name="quantity" required min="0.01" step="0.01">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Purpose/Description</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                <textarea class="form-control" id="use-description" name="description" rows="2" required placeholder="Enter the purpose of using this item"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-calculator"></i> Sales Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Total Cost</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="text" class="form-control" id="total-cost" name="total_cost" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i> Cancel
                </button>
                <button type="button" class="btn btn-success" id="confirm-use">
                    <i class="bi bi-check-lg"></i> Confirm Use
                </button>
            </div>
        </div>
    </div>
</div>
<script>
        document.getElementById('use-qty').addEventListener('input', function() {
            const quantity = parseFloat(this.value) || 0;
            const unitPrice = parseFloat(document.getElementById('unit-price').value) || 0;
            const availableQty = parseFloat(document.getElementById('available-qty').value) || 0;

            if (quantity > availableQty) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Quantity',
                    text: 'Quantity cannot exceed available stock'
                });
                this.value = '';
                document.getElementById('total-cost').value = '0.00';
                return;
            }

            const totalCost = quantity * unitPrice;
            document.getElementById('total-cost').value = totalCost.toFixed(2);
        });
    </script>