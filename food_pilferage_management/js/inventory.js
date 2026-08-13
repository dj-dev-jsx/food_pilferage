let currentPage = 1;
const itemsPerPage = 10;

const ajaxConfig = {
    dataType: 'json',
    error: function(xhr, status, error) {
        console.error('AJAX Error:', {xhr, status, error});
        Swal.fire({
            title: 'Error!',
            text: 'An error occurred. Please try again.',
            icon: 'error',
            confirmButtonColor: '#28a745'
        });
    }
};

// Start Day Inventory Handler
function startDayInventory(itemId, startingQuantity) {
    $.ajax({
        url: 'processes/start_day_inventory.php',
        method: 'POST',
        dataType: 'json',
        data: {
            item_id: itemId,
            starting_quantity: startingQuantity
        },
        success: function(response) {
            if(response.status === 'success') {
                Swal.fire({
                    title: 'Success!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonColor: '#28a745'
                });
                fetchData(currentPage);
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: response.message,
                    icon: 'error',
                    confirmButtonColor: '#28a745'
                });
            }
        }
    });
}

function updateInventoryAction(itemId, actionType, quantity, expirationDate = null) {
    const data = {
        item_id: itemId,
        action_type: actionType,
        quantity: quantity
    };

    if (expirationDate) {
        data.expiration_date = expirationDate;
    }

    $.ajax({
        url: 'processes/update_inventory_action.php',
        method: 'POST',
        dataType: 'json',
        data: data,
        success: function(response) {
            if(response.status === 'success') {
                Swal.fire({
                    title: 'Success!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonColor: '#28a745'
                });
                fetchData(currentPage);
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: response.message,
                    icon: 'error',
                    confirmButtonColor: '#28a745'
                });
            }
        }
    });
}
function endDayCount(itemId, actualQuantity) {
    $.ajax({
        url: 'processes/end_day_count.php',
        method: 'POST',
        dataType: 'json',
        data: {
            item_id: itemId,
            actual_quantity: actualQuantity
        },
        success: function(response) {
            if(response.status === 'success') {
                const discrepancy = Math.abs(response.discrepancy);
                const discrepancyType = response.discrepancy > 0 ? 'Missing' : 'Excess';
                
                if (discrepancyType === 'Missing') {
                    Swal.fire({
                        title: 'Missing Items Detected',
                        html: `Missing quantity: ${discrepancy} ${response.unit}<br><br>
                            Please file a pilferage report for the missing items.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Report Pilferage',
                        confirmButtonColor: '#ff2929',
                        cancelButtonText: 'Later'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'processes/add_report_process.php',
                                method: 'POST',
                                dataType: 'json',
                                data: {
                                    submit_report: true,
                                    item_id: itemId,
                                    reported_quantity: discrepancy,
                                    report_status_id: 1,
                                    description: `End day count detected missing items: ${discrepancy} ${response.unit}`
                                },
                                success: function(reportResponse) {
                                    if(reportResponse.status === 200) {
                                        Swal.fire({
                                            title: 'Report Submitted',
                                            text: 'Pilferage report has been successfully submitted',
                                            icon: 'success',
                                            confirmButtonColor: '#28a745'
                                        });
                                    }
                                }
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Excess Quantity Detected',
                        html: `Excess quantity: ${discrepancy} ${response.unit}<br><br>
                              Please recount to verify or update the stock if your count is correct.`,
                        icon: 'info',
                        confirmButtonText: 'Got it',
                        confirmButtonColor: '#28a745'
                    });
                }
                fetchData(currentPage);
            }
        }
    });
}






// Inventory Action Handlers
$(document).on('click', '.inventory-dropdown-item', function() {
    const itemId = $(this).data('id');
    const startingStock = $(this).data('stock');
    const categoryBadge = $(this).closest('tr').find('td:eq(3)').text().trim(); // Changed to eq(3) for direct text
    const isPerishable = categoryBadge.includes('Meat') || categoryBadge.includes('Vegetable');
    console.log('Category:', categoryBadge, 'Is Perishable:', isPerishable);

    if ($(this).find('i').hasClass('bi-sunrise')) {
        Swal.fire({
            title: 'Start Day Count',
            input: 'number',
            inputValue: startingStock,
            inputLabel: 'Enter starting quantity',
            showCancelButton: true,
            confirmButtonText: 'Start Day',
            confirmButtonColor: '#28a745',
            inputValidator: (value) => {
                if (!value || value < 0) return 'Please enter a valid quantity!';
            }
        }).then((result) => {
            if (result.isConfirmed) {
                startDayInventory(itemId, result.value);
            }
        });
    } else if ($(this).find('i').hasClass('bi-arrow-repeat')) {
        Swal.fire({
            title: 'Update Stock',
            html: `
                <select id="actionType" class="form-select mb-3">
                    <option value="addition">Add Stock</option>
                    <option value="subtraction">Remove Stock</option>
                </select>
                <input type="number" id="quantityInput" class="form-control mb-3" placeholder="Enter quantity" min="0">
                ${isPerishable ? `
                    <input type="date" id="expirationDate" class="form-control"
                           placeholder="Expiration Date"
                           min="${new Date().toISOString().split('T')[0]}"
                           required>
                ` : ''}
            `,
            showCancelButton: true,
            confirmButtonText: 'Update',
            confirmButtonColor: '#28a745',
            preConfirm: () => {
                const actionType = document.getElementById('actionType').value;
                const quantity = document.getElementById('quantityInput').value;
                const expirationDate = isPerishable ? document.getElementById('expirationDate').value : null;

                if (!quantity || quantity <= 0) {
                    Swal.showValidationMessage('Please enter a valid quantity');
                    return false;
                }

                if (isPerishable && actionType === 'addition' && !expirationDate) {
                    Swal.showValidationMessage('Please select an expiration date');
                    return false;
                }

                return { actionType, quantity, expirationDate };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                updateInventoryAction(
                    itemId,
                    result.value.actionType,
                    result.value.quantity,
                    result.value.expirationDate
                );
            }
        });
    }
    else if ($(this).find('i').hasClass('bi-sunset')) {
        Swal.fire({
            title: 'End Day Count',
            input: 'number',
            inputLabel: 'Enter final quantity count',
            showCancelButton: true,
            confirmButtonText: 'End Day',
            confirmButtonColor: '#28a745',
            inputValidator: (value) => {
                if (!value || value < 0) return 'Please enter a valid quantity!';
            }
        }).then((result) => {
            if (result.isConfirmed) {
                endDayCount(itemId, result.value);
            }
        });
    }
});

// Edit button handler
$(document).on('click', '.edit-btn', function() {
    const itemId = $(this).data('id');
    $.ajax({
        ...ajaxConfig,
        url: 'processes/edit_item_process.php',
        method: 'POST',
        data: { action: 'fetch', itemId: itemId },
        success: function(response) {
            if (response.error) {
                Swal.fire({
                    title: 'Error!',
                    text: response.error,
                    icon: 'error',
                    confirmButtonColor: '#28a745'
                });
                return;
            }
            
            $('#editItemId').val(response.item_id);
            $('#editItemName').val(response.item_name);
            $('#editUnitPrice').val(response.unit_price);
            $('#editCategory').val(response.category_id);
            $('#editStockQuantity').val(response.stock_quantity);
            $('#editUnitOfMeasure').val(response.unit_of_measure);
            $('#editExpiryDate').val(response.expiration_date);
            toggleExpiryForm(document.getElementById('editCategory'));
            $('#edit-modal').modal('show');
        }
    });
});

// Edit form submission
$(document).on('submit', '#editForm', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'update');

    $.ajax({
        ...ajaxConfig,
        url: 'processes/edit_item_process.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.status === 'success') {
                $('#edit-modal').modal('hide');
                fetchData();
                Swal.fire({
                    title: 'Success!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonColor: '#28a745',
                    timer: 3000,
                    timerProgressBar: true
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: response.message,
                    icon: 'error',
                    confirmButtonColor: '#28a745'
                });
            }
        }
    });
});

// Delete button handler
$(document).on('click', '.delete-btn', function() {
    const itemId = $(this).data('id');
    
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                ...ajaxConfig,
                url: 'processes/delete_item_process.php',
                method: 'POST',
                data: {action: 'delete', itemId: itemId},
                success: function(response) {
                    if (response.status === 'success') {
                        fetchData();
                        Swal.fire({
                            title: 'Deleted!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonColor: '#28a745',
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                }
            });
        }
    });
});

// Add item form submission
$(document).on('submit', '#addItemForm', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('save_item', true);

    Swal.fire({
        title: 'Processing...',
        text: 'Adding new item',
        didOpen: () => Swal.showLoading(),
        allowOutsideClick: false,
        showConfirmButton: false
    });

    $.ajax({
        ...ajaxConfig,
        url: 'processes/add_item_process.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if(response.status === 200) {
                $('#addItemForm')[0].reset();
                $('#add-modal').modal('hide');
                fetchData();
                Swal.fire({
                    title: 'Success!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonColor: '#28a745',
                    timer: 2000,
                    timerProgressBar: true
                });
            }
        }
    });
});

// Fetch and update functions
function fetchData(page = 1) {
    $.ajax({
        ...ajaxConfig,
        url: 'processes/inventory_table.php',
        method: 'POST',
        data: {
            action: 'fetchData',
            page: page,
            items_per_page: itemsPerPage
        },
        success: function(response) {
            $('#records').html(response.records);
            updatePagination(response.total_records, page);
        }
    });
}

function updatePagination(totalRecords, currentPage) {
    const totalPages = Math.ceil(totalRecords / itemsPerPage);
    const start = ((currentPage - 1) * itemsPerPage) + 1;
    const end = Math.min(start + itemsPerPage - 1, totalRecords);
    
    $('#entries-start').text(start);
    $('#entries-end').text(end);
    $('#total-entries').text(totalRecords);
    $('#prev-page').prop('disabled', currentPage === 1);
    $('#next-page').prop('disabled', currentPage === totalPages);
    
    let pageButtons = '';
    for(let i = 1; i <= totalPages; i++) {
        pageButtons += `<button class="btn btn-outline-success ${i === currentPage ? 'active' : ''}" 
                        data-page="${i}">${i}</button>`;
    }
    $('#page-numbers').html(pageButtons);
}

// Initialize and event handlers
$(document).ready(function() {
    fetchData(1);
    
    // Search functionality
    $('#search_inp').keyup(function() {
        handleFilter('searchTable', {
            search_inp: $(this).val()
        });
    });
    
    // Status and Category filters
    $('#stockStatus, #category').on('change', function() {
        handleFilter('combinedFilter', {
            status_id: $('#stockStatus').val(),
            category_id: $('#category').val()
        });
    });
});

// Filter handler function
function handleFilter(action, data) {
    $.ajax({
        ...ajaxConfig,
        url: 'processes/search_process.php',
        method: 'POST',
        data: {
            action: action,
            ...data,
            page: currentPage,
            items_per_page: itemsPerPage
        },
        success: function(response) {
            $('#records').html(response.records);
            updatePagination(response.total_records, currentPage);
        }
    });
}



// Handle form submission
$('#confirm-use').click(function() {
    if (!$('#use-item-form')[0].checkValidity()) {
        $('#use-item-form')[0].reportValidity();
        return;
    }

    const formData = new FormData($('#use-item-form')[0]);
    
    $.ajax({
        url: 'processes/use_item_process.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    $('#use-modal').modal('hide');
                    $('#use-item-form')[0].reset();
                    fetchData();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: response.message
                });
            }
        }
    });
    
});

// Enhanced Use Item Modal functionality
$(document).on('click', '.use-item-btn', function() {
    const itemId = $(this).data('id');
    const unitPrice = $(this).data('price'); // Make sure to add data-price attribute to your button
    
    $.ajax({
        url: 'processes/get_item_details.php',
        type: 'POST',
        data: {item_id: itemId},
        success: function(response) {
            const item = JSON.parse(response);
            $('#use-item-id').val(item.item_id);
            $('#use-item-name').val(item.item_name);
            $('#use-unit').val(item.unit_of_measure);
            $('#available-qty').val(item.stock_quantity);
            $('#current-stock').val(item.stock_quantity);
            $('#unit-price').val(item.unit_price);
            $('#use-qty').val('');
            $('#total-cost').val('0.00');
        }
    });
});