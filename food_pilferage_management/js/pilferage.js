let currentPage = 1;
const itemsPerPage = 10;

$(document).ready(function() {
    console.log('Doc');
    $('#pilferage_record').html('<tr><td colspan="8" class="text-center"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
    fetchPilferageData(1);

    $('#searchInv, #report_status_id, #user_reported, #report_date').on('change keyup', function() {
        currentPage = 1;
    });
    $(document).on('click', '.status-btn', function() {
        const reportId = $(this).data('id');
        const currentStatus = $(this).data('current-status');
        
        Swal.fire({
            title: 'Update Report Status',
            html: `
                <select id="statusSelect" class="form-select">
                    <option value="1" ${currentStatus == 1 ? 'selected' : ''}>Pending</option>
                    <option value="2" ${currentStatus == 2 ? 'selected' : ''}>Under Investigation</option>
                    <option value="3" ${currentStatus == 3 ? 'selected' : ''}>Resolved</option>
                </select>
            `,
            showCancelButton: true,
            confirmButtonText: 'Update',
            confirmButtonColor: '#28a745',
            preConfirm: () => {
                return document.getElementById('statusSelect').value;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'processes/update_pilferage_status.php',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        report_id: reportId,
                        new_status: result.value
                    },
                    success: function(response) {
                        if(response.status === 'success') {
                            fetchPilferageData(currentPage);
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to update status',
                            icon: 'error',
                            confirmButtonColor: '#28a745'
                        });
                    }
                });
            }
        });
    });
    
});

function fetchPilferageData(page = 1) {
    $.ajax({
        url: 'processes/pilferage_table.php',
        method: 'POST',
        data: {
            action: 'fetchPilferageData',
            role_id: $('#pilferage_record').data('role'),
            page: page,
            items_per_page: itemsPerPage
        },
        dataType: 'json',
        success: function(response) {
            $('#pilferage_record').html(response.records);
            updatePagination(response.total_records, page);
        },
        error: function(xhr) {
            console.log('Response:', xhr.responseText);
            $('#pilferage_record').html('<tr><td colspan="8" class="text-center text-danger">Error loading data</td></tr>');
            Swal.fire({
                title: 'Error!',
                text: 'Failed to load pilferage data',
                icon: 'error',
                confirmButtonColor: '#28a745'
            });
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
        pageButtons += `<button class="btn btn-outline-success ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</button>`;
    }
    $('#page-numbers').html(pageButtons);
}

function makeAjaxCall(url, data, successCallback) {
    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        dataType: 'json',
        success: successCallback,
        error: function(xhr) {
            console.log('Response:', xhr.responseText);
            $('#pilferage_record').html('<tr><td colspan="8" class="text-center text-danger">Error loading data</td></tr>');
            Swal.fire({
                title: 'Error!',
                text: 'Failed to load data',
                icon: 'error',
                confirmButtonColor: '#28a745'
            });
        }
    });
}

// Event Handlers
$(document).on('click', '#page-numbers .btn', function() {
    currentPage = parseInt($(this).data('page'));
    fetchPilferageData(currentPage);
});

$('#prev-page').click(function() {
    if(currentPage > 1) {
        currentPage--;
        fetchPilferageData(currentPage);
    }
});

$('#next-page').click(function() {
    currentPage++;
    fetchPilferageData(currentPage);
});

$('#searchInv').keyup(function(e) {
    e.preventDefault();
    let data = {
        action: 'searchInvTable',
        search_inv_inp: $(this).val(),
        page: currentPage,
        items_per_page: itemsPerPage
    };
    makeAjaxCall('processes/search_pilf_table.php', data, function(response) {
        $('#pilferage_record').html(response.records);
        updatePagination(response.total_records, currentPage);
    });
});

$('#report_status_id').on('change', function(e) {
    e.preventDefault();
    let data = {
        action: $(this).val() === '' ? 'fetchPilferageData' : 'reportStatusFilter',
        report_status_id: $(this).val(),
        page: currentPage,
        items_per_page: itemsPerPage
    };
    makeAjaxCall('processes/search_pilf_table.php', data, function(response) {
        $('#pilferage_record').html(response.records);
        updatePagination(response.total_records, currentPage);
    });
});

$('#user_reported').on('change', function(e) {
    e.preventDefault();
    let data = {
        action: $(this).val() === '' ? 'fetchPilferageData' : 'userReported',
        user_id: $(this).val(),
        page: currentPage,
        items_per_page: itemsPerPage
    };
    makeAjaxCall('processes/search_pilf_table.php', data, function(response) {
        $('#pilferage_record').html(response.records);
        updatePagination(response.total_records, currentPage);
    });
});

$('#report_date').on('change', function(e) {
    e.preventDefault();
    let data = {
        action: $(this).val() === '' ? 'fetchPilferageData' : 'dateFilter',
        report_date: $(this).val(),
        page: currentPage,
        items_per_page: itemsPerPage
    };
    makeAjaxCall('processes/search_pilf_table.php', data, function(response) {
        $('#pilferage_record').html(response.records);
        updatePagination(response.total_records, currentPage);
    });
});

$(document).on('submit', '#addReportForm', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append('submit_report', true);

    $.ajax({
        url: 'processes/add_report_process.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if(response.status === 200) {
                $('#addReportForm')[0].reset();
                $('#add-report-modal').modal('hide');
                fetchPilferageData(currentPage);
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
        },
        error: function(xhr) {
            console.log('Response:', xhr.responseText);
            Swal.fire({
                title: 'Error!',
                text: 'Failed to submit report',
                icon: 'error',
                confirmButtonColor: '#28a745'
            });
        }
    });
});

$(document).on('click', '.view-btn', function() {
    var reportId = $(this).data('id');
    $.ajax({
        url: 'processes/fetch_report_details.php',
        method: 'POST',
        data: {
            report_id: reportId,
            action: 'fetchSinglePilferage'
        },
        success: function(response) {
            $('#view-modal .modal-body').html(response);
            $('#view-modal').modal('show');
        },
        error: function() {
            Swal.fire({
                title: 'Error!',
                text: 'Error fetching report details',
                icon: 'error',
                confirmButtonColor: '#28a745'
            });
        }
    });
});

$(document).on('click', '.edit-btn', function() {
    let report_id = $(this).data('id');
    console.log("Report ID:", report_id);
    
    let formData = new FormData();
    formData.append('report_id', report_id);
    formData.append('action', 'fetch');
    
    $.ajax({
        type: "POST",
        url: "processes/edit_report_process.php",
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            console.log("Server response:", response);
            if (!response.error) {
                $('#editReportId').val(response.report_id);
                $('#editItemName').val(response.item_name);
                $('#editItemId').val(response.item_id);
                $('#editReportedQuantity').val(response.reported_quantity);
                $('#editReportStatus').val(response.report_status_id);
                $('#editDescription').val(response.description);
                $('#edit-report-modal').modal('show');
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: response.error,
                    icon: 'error',
                    confirmButtonColor: '#28a745'
                });
            }
        }
    });
});

$(document).on('submit', '#editReportForm', function(e){
    e.preventDefault();
    var formData = new FormData(this);
    formData.append('action', 'update');

    $.ajax({
        url: 'processes/edit_report_process.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            $('#edit-report-modal').modal('hide');
            $('.modal-backdrop').remove();
            $('#editReportForm')[0].reset();
            fetchPilferageData(currentPage);
            Swal.fire({
                title: 'Success!',
                text: response.message,
                icon: 'success',
                confirmButtonColor: '#28a745',
                timer: 3000,
                timerProgressBar: true
            });
        },
        error: function(xhr) {
            console.log('Update Response:', xhr.responseText);
            Swal.fire({
                title: 'Error!',
                text: 'Failed to update report',
                icon: 'error',
                confirmButtonColor: '#28a745'
            });
        }
    });
});

$(document).on('click', '#editReportCancel', function(e) {
    e.preventDefault();
    $('#edit-modal').modal('hide');
});

$('#exportCSV').click(function() {
    window.location.href = 'processes/export_pilferage.php';
});
